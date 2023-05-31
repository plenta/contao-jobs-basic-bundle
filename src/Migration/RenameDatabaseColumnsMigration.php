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

class RenameDatabaseColumnsMigration extends AbstractMigration
{
    private Connection $database;
    private array $oldColumnNames = [
        'plentaJobsMethod' => "varchar(12) NOT NULL default 'POST'",
        'plentaJobsShowButton' => "char(1) NOT NULL default ''",
        'plentaJobsSubmit' => "varchar(255) NOT NULL default ''",
        'plentaJobsShowTypes' => "char(1) NOT NULL default ''",
        'plentaJobsTypesHeadline' => "varchar(255) NOT NULL default ''",
        'plentaJobsShowAllTypes' => "char(1) NOT NULL default ''",
        'plentaJobsShowQuantity' => "char(1) NOT NULL default ''",
        'plentaJobsShowLocations' => "char(1) NOT NULL default ''",
        'plentaJobsLocationsHeadline' => "varchar(255) NOT NULL default ''",
        'plentaJobsShowAllLocations' => "char(1) NOT NULL default ''",
        'plentaJobsShowLocationQuantity' => "char(1) NOT NULL default ''",
    ];

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->database->getSchemaManager();

        if (!$schemaManager->tablesExist(['tl_module'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_module');
        $shouldRun = false;

        foreach ($columns as $currentColumn) {
            $currentColumnName = $currentColumn->getName();

            if (true === \array_key_exists($currentColumnName, $this->oldColumnNames)) {
                $shouldRun = true;
                break;
            }
        }

        return $shouldRun;
    }

    public function run(): MigrationResult
    {
        $schemaManager = $this->database->getSchemaManager();
        $columns = $schemaManager->listTableColumns('tl_module');

        foreach ($columns as $currentColumn) {
            $currentColumnName = $currentColumn->getName();

            if (true === \array_key_exists($currentColumnName, $this->oldColumnNames)) {
                $newColumnName = str_replace('plentaJobs', 'plentaJobsBasic', $currentColumnName);

                $this->database
                    ->executeQuery(
                        'ALTER TABLE tl_module
                        CHANGE '.$currentColumnName.' '.$newColumnName.' '.$this->oldColumnNames[$currentColumnName]
                    )
                ;
            }
        }

        return $this->createResult(
            true,
            'All Plenta Jobs Basic Columns have been renamed.'
        );
    }
}
