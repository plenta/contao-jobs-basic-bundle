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

use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicJobLocationModel;

class TlModule
{
    public function jobLocationOptionsCallback(): array
    {
        $jobLocations = PlentaJobsBasicJobLocationModel::findAll();

        $return = [];
        foreach ($jobLocations as $jobLocation) {
            $return[$jobLocation->id] = $jobLocation->getRelated('pid')->name.': ';
            if ('onPremise' === $jobLocation->jobTypeLocation) {
                $return[$jobLocation->id] .= $jobLocation->streetAddress;

                if ('' !== $jobLocation->addressLocality) {
                    $return[$jobLocation->id] .= ', '.$jobLocation->addressLocality;
                }
            } else {
                $return[$jobLocation->id] .= $GLOBALS['TL_LANG']['MSC']['PLENTA_JOBS']['remote'].' ['.$jobLocation->requirementValue.']';
            }
        }

        return $return;
    }
}
