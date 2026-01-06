<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2023-2025, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Events;

use Symfony\Contracts\EventDispatcher\Event;

class EmploymentTypesEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.employment_types';

    private array $employmentTypes = [];

    public function setEmploymentTypes(array $employmentTypes): self
    {
        $this->employmentTypes = $employmentTypes;

        return $this;
    }

    public function getEmploymentTypes(): array
    {
        return $this->employmentTypes;
    }
}
