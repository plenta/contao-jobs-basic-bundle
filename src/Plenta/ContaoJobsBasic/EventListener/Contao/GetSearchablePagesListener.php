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
use Contao\Database;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer;

/**
 * @Hook("getSearchablePages")
 */
class GetSearchablePagesListener
{
    /**
     * @var EntityManagerInterface
     */
    protected $registry;
    private Connection $connection;

    public function __construct(Connection $connection, EntityManagerInterface $registry)
    {
        $this->connection = $connection;
        $this->registry = $registry;
    }

    public function __invoke(array $pages, int $rootId = null, bool $isSitemap = false, string $language = null): array
    {
        $processed = [];

        $jobOfferRepo = $this->registry->getRepository(TlPlentaJobsBasicOffer::class);

        // Alle Listingmodule
        // Module >  plenta_jobs_basic_offer_list
        $modules = ModuleModel::findByType('plenta_jobs_basic_offer_list');
        if ($modules) {
            foreach ($modules as $module) {
                $locations = StringUtil::deserialize($module->plentaJobsBasicLocations);
                $jobs = $jobOfferRepo->findAllPublishedByTypesAndLocation([], $locations);
                foreach ($jobs as $job) {
                    if (!\in_array($job->getId(), $processed, true)) {
                        if ($page = $this->generateJobOfferUrl($job, $module)) {
                            $pages[] = $page;
                            $processed[] = $job->getId();
                        }
                    }
                }
            }
        }

        return $pages;
    }

    public function generateJobOfferUrl(TlPlentaJobsBasicOffer $jobOffer, ModuleModel $model): ?string
    {
        $objPage = $model->getRelated('jumpTo');

        if (!$objPage instanceof PageModel) {
            return null;
        }

        $params = (Config::get('useAutoItem') ? '/' : '/items/').($jobOffer->getAlias() ?: $jobOffer->getId());

        return ampersand($objPage->getAbsoluteUrl($params));
    }
}
