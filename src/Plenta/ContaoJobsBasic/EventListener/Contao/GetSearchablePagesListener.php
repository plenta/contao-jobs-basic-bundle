<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\EventListener\Contao;

use Contao\Config;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\ModuleModel;
use Contao\PageModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer;

/**
 * @Hook("getSearchablePages")
 */
class GetSearchablePagesListener
{
    public function __invoke(array $pages, $rootId = null, bool $isSitemap = false, string $language = null): array
    {
        $processed = [];

        $modules = ModuleModel::findByType('plenta_jobs_basic_offer_list');
        if ($modules) {
            foreach ($modules as $module) {
                $jobs = PlentaJobsBasicOfferModel::findBy('published', 1);
                foreach ($jobs as $job) {
                    if (!\in_array($job->id, $processed, true)) {
                        if ($page = $this->generateJobOfferUrl($job, $module)) {
                            $pages[] = $page;
                            $processed[] = $job->id;
                        }
                    }
                }
            }
        }

        return $pages;
    }

    public function generateJobOfferUrl(PlentaJobsBasicOfferModel $jobOffer, ModuleModel $model): ?string
    {
        $objPage = $model->getRelated('jumpTo');

        if (!$objPage instanceof PageModel) {
            return null;
        }

        $params = (Config::get('useAutoItem') ? '/' : '/items/').($jobOffer->alias ?: $jobOffer->id);

        return ampersand($objPage->getAbsoluteUrl($params));
    }
}
