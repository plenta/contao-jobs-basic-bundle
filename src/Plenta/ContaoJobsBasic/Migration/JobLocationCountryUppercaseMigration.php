<?php

declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace Plenta\ContaoJobsBasic\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;


class JobLocationCountryUppercaseMigration extends AbstractMigration
{
    public function getName(): string
    {
        return "Contao Jobs Basic - migrate countries to uppercase";
    }

    public function __construct(private Connection $connection)
    {
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
