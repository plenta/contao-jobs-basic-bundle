<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Controller\Contao\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\FrontendTemplate;
use Contao\ModuleModel;
use Contao\Template;
use Doctrine\Persistence\ManagerRegistry;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule("plenta_jobs_basic_offer_list",
 *   category="plentaJobsBasic",
 *   template="mod_plenta_jobs_basic_offer_list",
 *   renderer="forward"
 * )
 */
class JobOfferListController extends AbstractFrontendModuleController
{
    protected ManagerRegistry $registry;

    protected MetaFieldsHelper $metaFieldsHelper;

    public function __construct(
        ManagerRegistry $registry,
        MetaFieldsHelper $metaFieldsHelper
    ) {
        $this->registry = $registry;
        $this->metaFieldsHelper = $metaFieldsHelper;
    }

    /**
     * @param Template    $template
     * @param ModuleModel $model
     * @param Request     $request
     *
     * @return Response|null
     */
    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $jobOfferRepository = $this->registry->getRepository(TlPlentaJobsBasicOffer::class);

        $jobOffers = $jobOfferRepository->findAllPublished();

        $items = [];

        foreach ($jobOffers as $jobOffer) {
            $itemTemplate = new FrontendTemplate('plenta_jobs_basic_offer_default');
            $itemTemplate->jobOffer = $jobOffer;
            $itemTemplate->jobOfferMeta = $this->metaFieldsHelper->getMetaFields($jobOffer);

            $items[] = $itemTemplate->parse();
        }

        $template->empty = 'Es sind momentan keine Stellenanzeigen vorhanden.';

        $template->items = $items;

        return $template->getResponse();
    }
}
