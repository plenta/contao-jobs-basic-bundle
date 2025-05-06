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
use Plenta\ContaoJobsBasic\Helper\CountJobsHelper;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsInsertTag('jobs')]
class JobsCountInsertTag implements InsertTagResolverNestedResolvedInterface
{
    public function __construct(protected CountJobsHelper $countJobsHelper)
    {
    }

    public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
    {
        if ('count' === $insertTag->getParameters()->get(0)) {
            $filtered = 'filtered' === $insertTag->getParameters()->get(1);

            return new InsertTagResult((string) $this->countJobsHelper->countJobs($filtered));
        }

        return new InsertTagResult('');
    }
}
