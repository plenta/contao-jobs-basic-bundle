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

use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\StringUtil;

class DataTypeMapper
{
    public function __construct(private readonly ContaoFramework $framework)
    {
    }

    public function serializedToJson(string $serializedData): string
    {
        /** @var Adapter<StringUtil> $stringUtil */
        $stringUtil = $this->framework->getAdapter(StringUtil::class);

        $data = $stringUtil->deserialize($serializedData);

        return json_encode($data);
    }

    public function jsonToSerialized(string|null $jsonData): string|null
    {
        if (null === $jsonData) {
            return serialize([]);
        }

        return serialize(json_decode($jsonData, true));
    }
}
