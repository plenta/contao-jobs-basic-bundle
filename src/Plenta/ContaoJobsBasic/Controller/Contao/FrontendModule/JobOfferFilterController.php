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

use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\Input;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\Template;
use Doctrine\Persistence\ManagerRegistry;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer;
use Plenta\ContaoJobsBasic\GoogleForJobs\GoogleForJobs;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule("plenta_jobs_basic_filter_reader",
 *   category="plentaJobsBasic",
 *   template="mod_plenta_jobs_basic_filter_reader",
 *   renderer="forward"
 * )
 */
class JobOfferFilterController extends AbstractFrontendModuleController
{

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        return $template->getResponse();
    }
}
