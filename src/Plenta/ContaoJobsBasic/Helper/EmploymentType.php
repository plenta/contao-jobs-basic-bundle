<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Helper;

class EmploymentType
{
    /**
     * @return string[]
     */
    public function getEmploymentTypes(): array
    {
        return [
            'FULL_TIME',
            'PART_TIME',
            'CONTRACTOR',
            'TEMPORARY',
            'INTERN',
            'VOLUNTEER',
            'PER_DIEM',
            'OTHER',
        ];
    }

    /**
     * @TODO Get employment type name from language files
     */
    public function getEmploymentTypeName(string $employmentType): ?string
    {
        return $employmentType;
    }
}
