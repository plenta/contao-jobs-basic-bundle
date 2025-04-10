<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2024, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\EventListener\Contao;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\CoreBundle\Event\SitemapEvent;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\ModuleModel;
use Contao\PageModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

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

            if (empty($objArticles)) {
                continue;
            }

            foreach ($objArticles as $article) {
                if (true === $article->published) {
                    $objContents = $this->framework->getAdapter(ContentModel::class)->findByPid($article->id);

                    if (empty($objContents)) {
                        continue;
                    }

                    foreach ($objContents as $objContent) {
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
                                        if (!\in_array($page, $arrPages, true)) {
                                            $arrPages[] = $page;
                                        }
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
