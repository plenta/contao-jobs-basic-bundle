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

use Contao\Database;
use Contao\Config;
use Contao\PageModel;
use Contao\Environment;
use Contao\ModuleModel;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer;

/**
 * @Hook("getSearchablePagesXX")
 */
class GetSearchablePagesListener
{
    private Connection $connection;

    /**
     * @var EntityManagerInterface
     */
    protected $registry;

    public function __construct(Connection $connection, EntityManagerInterface $registry)
    {
        $this->connection = $connection;
        $this->registry = $registry;
    }

    public function __invoke(array $pages, int $rootId = null, bool $isSitemap = false, string $language = null): array
    {
        $time = time();
        $rootPages = [];
        $processed = [];

        if (null !== $rootId) {
            $database = Database::getInstance();
            $rootPages = $database->getChildRecords([$rootId], 'tl_page');
        }

        // Alle Listingmodule
        // Module >  plenta_jobs_basic_offer_list


        $jobOfferRepo = $this->registry->getRepository(TlPlentaJobsBasicOffer::class);

        $jobs = $jobOfferRepo->findAllPublished();

        if (!is_countable($jobs)) {
            return $pages;
        }

        foreach ($jobs as $job) {
            //$pages[] = $job->getAlias();
            $params = (Config::get('useAutoItem') ? '/' : '/items/').($job->getAlias() ?: $job->getId());
            $pages[] = $params; //ampersand($objPage->getFrontendUrl($params));
        }

        return $pages;
    }

    public function generateJobOfferUrl(TlPlentaJobsBasicOffer $jobOffer, ModuleModel $model): string
    {
        $objPage = $model->getRelated('jumpTo');

        if (!$objPage instanceof PageModel) {
            $url = ampersand(Environment::get('request'));
        } else {
            $params = (Config::get('useAutoItem') ? '/' : '/items/').($jobOffer->getAlias() ?: $jobOffer->getId());

            $url = ampersand($objPage->getFrontendUrl($params));
        }

        return $url;
    }
}