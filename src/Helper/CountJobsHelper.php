<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2025, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Helper;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\ModuleModel;
use Contao\StringUtil;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicJobLocationModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Symfony\Component\HttpFoundation\RequestStack;

class CountJobsHelper
{
    public function __construct(protected RequestStack $requestStack)
    {
    }

    public function countJobs(bool $filtered = true)
    {
        $request = $this->requestStack->getCurrentRequest();

        $model = $request->attributes->get('moduleModel');

        if ($model && !$model instanceof ModuleModel) {
            $model = ModuleModel::findByPk($model);
        }

        if ('plenta_jobs_basic_offer_list' !== $model?->type && 'plenta_jobs_basic_filter' !== $model?->type) {
            $model = null;
        }

        if (!$model) {
            $page = $request->attributes->get('pageModel');

            $articles = ArticleModel::findByPid($page->id);

            foreach ($articles as $article) {
                $contents = ContentModel::findPublishedByPidAndTable($article->id, 'tl_article');

                foreach ($contents as $content) {
                    if ('module' === $content->type) {
                        $module = ModuleModel::findByPk($content->module);

                        if ('plenta_jobs_basic_offer_list' === $module->type) {
                            $model = $module;
                            break 2;
                        }
                    }
                }
            }
        }

        $types = [];
        $locations = [];

        if ($model) {
            $types = StringUtil::deserialize($model->plentaJobsBasicEmploymentTypes) ?? [];
            $locations = StringUtil::deserialize($model->plentaJobsBasicLocations) ?? [];

            if (empty($locations) && !empty($model->plentaJobsBasicCompanies)) {
                $locationObjs = PlentaJobsBasicJobLocationModel::findByMultiplePids(StringUtil::deserialize($model->plentaJobsBasicCompanies, true));

                foreach ($locationObjs as $locationObj) {
                    $locations[] = $locationObj->id;
                }
            }
        }

        if ($filtered) {
            $queryTypes = $request->query->all('types');
            $queryLocations = $request->query->all('location');

            $types = array_merge($types, is_array($queryTypes) ? $queryTypes : [$queryTypes]);
            $locations = array_merge($locations, is_array($queryLocations) ? $queryLocations : [$queryLocations]);

            return PlentaJobsBasicOfferModel::countAllPublishedByTypesAndLocation($types, $locations, true, $model);
        }

        return PlentaJobsBasicOfferModel::countAllPublishedByTypesAndLocation($types, $locations, true, $model, false);
    }
}
