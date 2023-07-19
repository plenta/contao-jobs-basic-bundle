<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022-2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\EventListener\Contao;

use Contao\ArticleModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\CoreBundle\Event\SitemapEvent;
use Contao\ContentModel;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;

#[AsEventListener(event: SitemapEvent::class)]
class SitemapListener
{
    public function __construct(private ContaoFramework $framework)
    {
    }

    public function __invoke(SitemapEvent $event): void
    {
        $arrRoot = $this->framework->createInstance(Database::class)->getChildRecords($event->getRootPageIds(), 'tl_page');

        if (empty($arrRoot)) {
            return;
        }

        $arrPages = [];

        foreach ($arrRoot as $pageId) {
            $objPage = $this->framework->getAdapter(PageModel::class)->findWithDetails($pageId);

            if (null === $objPage) {
                continue;
            }

            $objArticles = $this->framework->getAdapter(ArticleModel::class)->findByPid($objPage->id);

            foreach ($objArticles as $article) {
                if (true === $article->published) {
                    $objContent = $this->framework->getAdapter(ContentModel::class)->findByPid($article->id);

                    if ('module' === $objContent->type && false === $objContent->invisible) {
                        $module = $this->framework->getAdapter(ModuleModel::class)->findByPk($objContent->module);

                        if (null === $module) {
                            continue;
                        }

                        if ('plenta_jobs_basic_offer_reader' !== $module->type) {
                            continue;
                        }

                        $jobs = PlentaJobsBasicOfferModel::findAllPublished();
                        if ($jobs) {
                            foreach ($jobs as $job) {
                                if ('noindex,nofollow' === $job->robots) {
                                    continue;
                                }

                                if ($page = $job->getAbsoluteUrl($objPage->language)) {
                                    if (!in_array($page, $arrPages)) {
                                        $arrPages[] = $page;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($arrPages as $strUrl) {
            $event->addUrlToDefaultUrlSet($strUrl);
        }
    }
}
