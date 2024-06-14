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

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Twig\Finder\FinderFactory;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicJobLocationModel;

class TlModule
{
    public function __construct(protected FinderFactory $finderFactory)
    {
    }

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

    #[AsCallback(table: 'tl_module', target: 'fields.plentaJobsBasicElementTpl.options')]
    public function onElementTplOptionsCallback()
    {
        return $this->finderFactory
            ->create()
            ->identifier('plenta_jobs_basic_offer_default')
            ->extension('html.twig')
            ->withVariants()
            ->asTemplateOptions();
    }
}
