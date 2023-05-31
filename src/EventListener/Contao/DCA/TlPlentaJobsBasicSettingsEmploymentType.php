<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\EventListener\Contao\DCA;

use Contao\DataContainer;
use Plenta\ContaoJobsBasic\Helper\DataTypeMapper;
use Plenta\ContaoJobsBasic\Helper\EmploymentType;

class TlPlentaJobsBasicSettingsEmploymentType
{
    protected DataTypeMapper $dataTypeMapper;
    protected EmploymentType $employmentTypeHelper;

    public function __construct(
        DataTypeMapper $dataTypeMapper,
        EmploymentType $employmentTypeHelper
    ) {
        $this->dataTypeMapper = $dataTypeMapper;
        $this->employmentTypeHelper = $employmentTypeHelper;
    }

    public function translationSaveCallback($value, DataContainer $dc): string
    {
        return $this->dataTypeMapper->serializedToJson($value);
    }

    public function translationLoadCallback($value, DataContainer $dc): string
    {
        return $this->dataTypeMapper->jsonToSerialized($value);
    }

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
