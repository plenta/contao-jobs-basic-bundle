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
use Contao\StringUtil;
use Contao\System;
use Doctrine\DBAL\Connection;
use Plenta\ContaoJobsBasic\GoogleForJobs\GoogleForJobs;

class MoveRemoteJobsMigration extends \Contao\CoreBundle\Migration\AbstractMigration
{
    protected Connection $database;

    public function __construct(Connection $connection)
    {
        $this->database = $connection;
    }

    public function getName(): string
    {
        return 'Plenta Jobs Basic Bundle 2.0 Update - Remote jobs';
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->database->getSchemaManager();

        if (!$schemaManager->tablesExist(['tl_plenta_jobs_basic_offer', 'tl_plenta_jobs_basic_job_location'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_plenta_jobs_basic_offer');
        $columnsLocation = $schemaManager->listTableColumns('tl_plenta_jobs_basic_job_location');

        if (!isset($columns['isremote']) || !isset($columnsLocation['requirementtype'])) {
            return false;
        }

        if (true === (bool) $this->database
                ->executeQuery("
                    SELECT EXISTS(
                        SELECT id
                        FROM tl_plenta_jobs_basic_offer
                        WHERE
                            isRemote = '1'
                    )
                ")
                ->fetchOne()
        ) {
            return true;
        }

        return false;
    }

    public function run(): MigrationResult
    {
        $offers = $this->database->executeQuery("SELECT * FROM tl_plenta_jobs_basic_offer WHERE isRemote = '1'")->fetchAllAssociative();
        $rootPage = $this->database->executeQuery("SELECT * FROM tl_page WHERE type = 'root' AND fallback = '1' LIMIT 1")->fetchAssociative();
        System::loadLanguageFile('countries', $rootPage['language'] ?? 'en');

        foreach ($offers as $offer) {
            $locations = StringUtil::deserialize($offer['jobLocation']);
            $newLocation = null;
            foreach ($locations as $location) {
                $locationArr = $this->database->prepare('SELECT * FROM tl_plenta_jobs_basic_job_location WHERE id = ?')->executeQuery([$location])->fetchAssociative();
                if ('Telecommute' === $locationArr['jobTypeLocation']) {
                    $newLocation = null;
                    break;
                }
                if (null !== $newLocation) {
                    continue;
                }

                $requirements = StringUtil::deserialize($offer['applicantLocationRequirements']);
                if (!\is_array($requirements)) {
                    $requirements = [];
                }

                $requirements = array_filter($requirements, fn ($item) => \in_array($item['key'], GoogleForJobs::ALLOWED_TYPES, true));

                if (empty($requirements)) {
                    $requirements = [
                        [
                            'key' => 'Country',
                            'value' => $GLOBALS['TL_LANG']['CNT'][$locationArr['addressCountry']],
                        ],
                    ];
                }

                foreach ($requirements as $requirement) {
                    $remoteLocation = $this->database->prepare('SELECT * FROM tl_plenta_jobs_basic_job_location WHERE jobTypeLocation = ? AND pid = ? AND requirementType = ? AND requirementValue = ?')->executeQuery(['Telecommute', $locationArr['pid'], $requirement['key'], $requirement['value']])->fetchAssociative();
                    if ($remoteLocation) {
                        $newLocation = $remoteLocation['id'];
                    } else {
                        $this->database->insert('tl_plenta_jobs_basic_job_location', [
                            'pid' => $locationArr['pid'],
                            'requirementType' => $requirement['key'],
                            'requirementValue' => $requirement['value'],
                            'tstamp' => time(),
                            'jobTypeLocation' => 'Telecommute',
                        ]);
                        $newLocation = $this->database->lastInsertId();
                    }
                    $locations[] = $newLocation;
                }
            }

            if (null !== $newLocation) {
                $this->database->update(
                    'tl_plenta_jobs_basic_offer',
                    [
                        'isRemote' => 0,
                        'applicantLocationRequirements' => null,
                        'jobLocation' => serialize($locations),
                    ],
                    [
                        'id' => $offer['id'],
                    ]
                );
            }
        }

        return new MigrationResult(true, 'Remote jobs have successfully been migrated to having designated remote job locations.');
    }
}
