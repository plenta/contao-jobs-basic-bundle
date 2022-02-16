<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Helper;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\StringUtil;

class DataTypeMapper
{
    private ContaoFramework $framework;

    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    public function serializedToJson(string $serializedData): string
    {
        /** @var StringUtil $stringUtil */
        $stringUtil = $this->framework->getAdapter(StringUtil::class);

        $data = $stringUtil::deserialize($serializedData);

        return json_encode($data);
    }

    public function jsonToSerialized(?string $jsonData): ?string
    {
        if (null === $jsonData) {
            return serialize([]);
        }

        return serialize(json_decode($jsonData));
    }
}
