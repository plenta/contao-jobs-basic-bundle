<?php

namespace Plenta\ContaoJobsBasic\EventListener\Contao\DCA;

class TlPlentaJobsBasicJobLocation
{
    public function listLocations(array $arrRow): string
    {

        //'default' => '{settings_legend},organization,streetAddress,,,addressRegion,addressCountry;',


        return '<div class="tl_content_left">'.$arrRow['addressLocality'].', '.$arrRow['postalCode'].'</div>';
    }

}
