<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2024, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\InsertTag;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Contao\CoreBundle\InsertTag\Resolver\InsertTagResolverNestedResolvedInterface;
use Contao\CoreBundle\Routing\PageFinder;
use Contao\ModuleModel;
use Contao\StringUtil;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicJobLocationModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Controller\Contao\FrontendModule\JobOfferListController;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsInsertTag('jobs')]
class JobsCountInsertTag implements InsertTagResolverNestedResolvedInterface
{
    public function __construct(protected RequestStack $requestStack)
    {
    }

    public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
    {
        if ('count' === $insertTag->getParameters()->get(0)) {
            $filtered = 'filtered' === $insertTag->getParameters()->get(1);

            $request = $this->requestStack->getCurrentRequest();

            $model = $request->attributes->get('moduleModel');

            if ($model && !$model instanceof ModuleModel) {
                $model = ModuleModel::findByPk($model);

                if ($model?->type !== 'plenta_jobs_basic_offer_list') {
                    $model = null;
                }
            }

            if (!$model) {
                $page = $request->attributes->get('pageModel');

                $articles = ArticleModel::findByPid($page->id);

                foreach ($articles as $article) {
                    $contents = ContentModel::findPublishedByPidAndTable($article->id, 'tl_article');

                    foreach ($contents as $content) {
                        if ($content->type === 'module') {
                            $module = ModuleModel::findByPk($content->module);

                            if ($module->type === 'plenta_jobs_basic_offer_list') {
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
                return new InsertTagResult((string) PlentaJobsBasicOfferModel::countAllPublishedByTypesAndLocation($types, $locations, true, $model));
            }

            return new InsertTagResult((string) PlentaJobsBasicOfferModel::countAllPublishedByTypesAndLocation($types, $locations, true, $model, false));
        }

        return new InsertTagResult('');
    }
}
