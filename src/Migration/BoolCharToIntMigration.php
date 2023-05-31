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

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\BooleanType;

class BoolCharToIntMigration extends AbstractMigration
{
    private Connection $database;

    private array $columns = ['addImage', 'addSalary'];

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    public function getName(): string
    {
        return 'Plenta Jobs Basic Bundle 1.4.5 Update';
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->database->getSchemaManager();

        if (!$schemaManager->tablesExist(['tl_plenta_jobs_basic_offer'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_plenta_jobs_basic_offer');

        $shouldRun = false;

        foreach ($columns as $currentColumn) {
            if (is_a($currentColumn->getType(), BooleanType::class)) {
                continue;
            }
            $currentColumnName = $currentColumn->getName();

            if (true === \in_array($currentColumnName, $this->columns, true)) {
                if (true === (bool) $this->database->executeQuery('SELECT EXISTS (SELECT id FROM tl_plenta_jobs_basic_offer WHERE '.$currentColumnName." = '')")->fetchOne()) {
                    $shouldRun = true;
                    break;
                }
            }
        }

        return $shouldRun;
    }

    public function run(): MigrationResult
    {
        $schemaManager = $this->database->getSchemaManager();
        $columns = $schemaManager->listTableColumns('tl_plenta_jobs_basic_offer');

        foreach ($columns as $currentColumn) {
            $currentColumnName = $currentColumn->getName();

            if (true === \in_array($currentColumnName, $this->columns, true)) {
                $this->database
                    ->executeQuery(
                        'UPDATE tl_plenta_jobs_basic_offer SET '.$currentColumnName.' = 0 WHERE '.$currentColumnName." = ''"
                    )
                ;
            }
        }

        return $this->createResult(
            true,
            'All empty boolean values have been changed to 0.'
        );
    }
}
