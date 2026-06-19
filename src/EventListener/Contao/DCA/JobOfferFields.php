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

class JobOfferFields
{
    /**
     * @return array<string>
     */
    public static function getFields(): array
    {
        return ['title', 'tstamp', 'datePosted', 'jobLocation'];
    }

    /**
     * @return array<string>
     */
    public static function getParts(): array
    {
        return ['image', 'teaser', 'company', 'jobLocation', 'publicationDate', 'employmentType'];
    }
}
