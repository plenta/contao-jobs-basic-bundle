<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\EventListener\Contao\DCA;

class TlPlentaJobsBasicJobLocation
{
    public function listLocations(array $arrRow): string
    {
        return '<div class="tl_content_left">'.$arrRow['addressLocality'].', '.$arrRow['postalCode'].'</div>';
    }
}
