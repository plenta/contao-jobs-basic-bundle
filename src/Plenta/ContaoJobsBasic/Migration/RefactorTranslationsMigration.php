<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Migration;

use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\TextType;

class RefactorTranslationsMigration extends \Contao\CoreBundle\Migration\AbstractMigration
{
    protected Connection $database;

    public function __construct(Connection $connection)
    {
        $this->database = $connection;
    }

    public function getName(): string
    {
        return 'Plenta Jobs Basic Bundle 2.0 Update - Job Offer Translations';
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->database->getSchemaManager();

        if (!$schemaManager->tablesExist(['tl_plenta_jobs_basic_offer_translation', 'contao\dca\tl_plenta_jobs_basic_offer'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('contao\dca\tl_plenta_jobs_basic_offer');

        if (!\array_key_exists('translations', $columns)) {
            return false;
        }

        foreach ($columns as $column) {
            if ('translations' !== $column->getName()) {
                continue;
            }

            if (!is_a($column->getType(), TextType::class)) {
                return false;
            }
        }

        if (true === (bool) $this->database
                ->executeQuery('
                    SELECT EXISTS(
                        SELECT id
                        FROM tl_plenta_jobs_basic_offer_translation
                    )
                ')
                ->fetchOne()
        ) {
            return true;
        }

        return false;
    }

    public function run(): MigrationResult
    {
        $offers = $this->database->executeQuery('SELECT * FROM tl_plenta_jobs_basic_offer')->fetchAllAssociative();

        foreach ($offers as $offer) {
            $translations = $this->database->prepare('SELECT * FROM tl_plenta_jobs_basic_offer_translation WHERE offer_id = ?')->executeQuery([$offer['id']])->fetchAllAssociative();
            if (!empty($translations)) {
                $translationsArr = [];
                foreach ($translations as $translation) {
                    $translationArr = [
                        'description' => $translation['description'],
                        'title' => $translation['title'],
                        'alias' => $translation['alias'],
                        'language' => $translation['language'],
                    ];
                    if (empty($translationsArr)) {
                        $translationsArr[1] = $translationArr;
                    } else {
                        $translationsArr[] = $translationArr;
                    }
                }

                $this->database->prepare('UPDATE tl_plenta_jobs_basic_offer SET translations = ? WHERE id = ?')->executeStatement([serialize($translationsArr), $offer['id']]);
                $this->database->prepare('DELETE FROM tl_plenta_jobs_basic_offer_translation WHERE offer_id = ?')->executeStatement([$offer['id']]);
            }
        }

        return new MigrationResult(true, 'Translations have successfully been moved from entities to serialized arrays.');
    }
}
