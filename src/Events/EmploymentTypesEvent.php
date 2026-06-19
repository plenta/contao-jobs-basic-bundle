<?php

declare(strict_types=1);

/*
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2026, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Events;

use Symfony\Contracts\EventDispatcher\Event;

class EmploymentTypesEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.employment_types';

    /**
     * @var array<string, string>
     */
    private array $employmentTypes = [];

    /**
     * @param array<string, string> $employmentTypes
     */
    public function setEmploymentTypes(array $employmentTypes): self
    {
        $this->employmentTypes = $employmentTypes;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getEmploymentTypes(): array
    {
        return $this->employmentTypes;
    }
}
