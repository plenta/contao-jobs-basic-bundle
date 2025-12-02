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
        $arrRoot = $event->getRootPageIds();

        if (empty($arrRoot)) {
            return;
        }

        $arrPages = [];
        $jobs = PlentaJobsBasicOfferModel::findAllPublished();

        foreach ($arrRoot as $pageId) {
            $objPage = $this->framework->getAdapter(PageModel::class)->findPublishedById($pageId)?->loadDetails();

            if (null === $objPage) {
                continue;
            }

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

        foreach ($arrPages as $strUrl) {
            $event->addUrlToDefaultUrlSet($strUrl);
        }
    }
}
