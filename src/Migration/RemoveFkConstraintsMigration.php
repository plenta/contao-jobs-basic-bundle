<?php

declare(strict_types=1);

/*
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2026, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

class RemoveFkConstraintsMigration extends AbstractMigration
{
    /**
     * @var array<string>
     */
    protected array $tables = ['tl_plenta_jobs_basic_offer_translation', 'tl_plenta_jobs_basic_job_location'];

    public function __construct(protected Connection $database)
    {
    }

    public function getName(): string
    {
        return 'Plenta Jobs Basic Bundle 2.0 Update - Foreign Key Constraints';
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->database->createSchemaManager();

        foreach ($this->tables as $table) {
            if ($schemaManager->tablesExist([$table])) {
                $foreignKeys = $schemaManager->listTableForeignKeys($table);
                if (!empty($foreignKeys)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function run(): MigrationResult
    {
        $schemaManager = $this->database->createSchemaManager();

        foreach ($this->tables as $table) {
            if ($schemaManager->tablesExist([$table])) {
                $foreignKeys = $schemaManager->listTableForeignKeys($table);
                if (!empty($foreignKeys)) {
                    foreach ($foreignKeys as $foreignKey) {
                        $schemaManager->dropForeignKey($foreignKey, $table);
                    }
                }
            }
        }

        return new MigrationResult(true, 'Foreign key constraints have successfully been dropped.');
    }
}
