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

class JobLocationCountryUppercaseMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function getName(): string
    {
        return 'Contao Jobs Basic - migrate countries to uppercase';
    }

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_plenta_jobs_basic_job_location'])) {
            return false;
        }

        if (!isset($schemaManager->listTableColumns('tl_plenta_jobs_basic_job_location')['addresscountry'])) {
            return false;
        }

        $test = $this->connection->fetchOne('SELECT TRUE FROM tl_plenta_jobs_basic_job_location WHERE BINARY addressCountry!=BINARY UPPER(addressCountry) LIMIT 1');

        return false !== $test;
    }

    public function run(): MigrationResult
    {
        $this->connection->executeStatement('UPDATE tl_plenta_jobs_basic_job_location SET addressCountry=UPPER(addressCountry) WHERE BINARY addressCountry!=BINARY UPPER(addressCountry)');

        return $this->createResult(true);
    }
}
