<?php

declare(strict_types=1);

/*
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2026, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\EventListener\Contao\DCA;

use Contao\DataContainer;
use Plenta\ContaoJobsBasic\Helper\DataTypeMapper;
use Plenta\ContaoJobsBasic\Helper\EmploymentType;

class TlPlentaJobsBasicSettingsEmploymentType
{
    public function __construct(
        protected DataTypeMapper $dataTypeMapper,
        protected EmploymentType $employmentTypeHelper,
    ) {
    }

    public function translationSaveCallback(mixed $value, DataContainer $dc): string
    {
        return $this->dataTypeMapper->serializedToJson($value);
    }

    public function translationLoadCallback(mixed $value, DataContainer $dc): string
    {
        return $this->dataTypeMapper->jsonToSerialized($value);
    }

    /**
     * @return array<string, string>
     */
    public function googleForJobsMappingOptionsCallback(): array
    {
        $employmentTypes = $this->employmentTypeHelper->getGoogleForJobsEmploymentTypes();

        $return = [];

        foreach ($employmentTypes as $employmentType) {
            $return[$employmentType] = $this->employmentTypeHelper->getEmploymentTypeName($employmentType);
        }

        return $return;
    }
}
