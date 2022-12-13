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

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;

/**
 * @Hook("getSearchablePages")
 */
class GetSearchablePagesListener
{
    public function __invoke(array $pages, $rootId = null, bool $isSitemap = false, string $language = null): array
    {
        $time = time();
        $jobs = PlentaJobsBasicOfferModel::findBy(['published = ?', '(start = ? OR start > ?)', '(stop = ? or stop < ?)'], [1, '', $time, '', $time]);
        if ($jobs) {
            foreach ($jobs as $job) {
                if ($job->robots === 'noindex,nofollow') {
                    continue;
                }
                
                if ($page = $job->getAbsoluteUrl($language)) {
                    $pages[] = $page;
                }
            }
        }

        return $pages;
    }
}
