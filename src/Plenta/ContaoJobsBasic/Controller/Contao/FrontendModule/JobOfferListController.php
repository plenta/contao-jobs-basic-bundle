<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao CMS
 *
 * @copyright     Copyright (c) 2020, Christian Barkowsky & Christoph Werner
 * @author        Christian Barkowsky <https://plenta.io>
 * @author        Christoph Werner <https://plenta.io>
 * @link          https://plenta.io
 * @license       proprietary
 */

namespace Plenta\ContaoJobsBasic\Controller\Contao\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\ModuleModel;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule("plenta_jobs_basic_offer_list",
 *   category="plentaJobsBasic",
 *   template="mod_plenta_jobs_basic_offer_list",
 *   renderer="esi"
 * )
 */
class JobOfferListController extends AbstractFrontendModuleController
{
    /**
     * @param Template $template
     * @param ModuleModel $model
     * @param Request $request
     * @return Response|null
     */
    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        return $template->getResponse();
    }
}
