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

use Symfony\Contracts\Translation\LocaleAwareInterface;

class EmploymentType
{
    protected LocaleAwareInterface $translator;

    public function __construct(
        LocaleAwareInterface $translator
    ) {
        $this->translator = $translator;
    }

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

    public function getEmploymentTypeName(string $employmentType): ?string
    {
        $translation = $this->translator->trans(
            'MSC.PLENTA_JOBS.'.$employmentType,
            [],
            'contao_default'
        );

        if ($translation === 'MSC.PLENTA_JOBS.'.$employmentType) {
            return null;
        }

        return $translation;
    }

    public function getEmploymentTypesFormatted(?array $employmentTypes): string
    {
        if (null === $employmentTypes) {
            return '';
        }

        $employmentTypesTemp = [];

        foreach ($employmentTypes as $employmentType) {
            $employmentTypesTemp[] = $this->getEmploymentTypeName($employmentType);
        }

        return implode(', ', $employmentTypesTemp);
    }
}
