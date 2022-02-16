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

class TlPlentaJobsBasicSettingsEmploymentType
{
    protected DataTypeMapper $dataTypeMapper;

    public function __construct(DataTypeMapper $dataTypeMapper)
    {
        $this->dataTypeMapper = $dataTypeMapper;
    }

    public function translationSaveCallback($value, DataContainer $dc): string
    {
        return $this->dataTypeMapper->serializedToJson($value);
    }

    public function translationLoadCallback($value, DataContainer $dc): string
    {
        return $this->dataTypeMapper->jsonToSerialized($value);
    }
}
