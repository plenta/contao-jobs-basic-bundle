<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Controller;

use Contao\CoreBundle\Controller\AbstractController;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Module;
use Contao\PageModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JobsOfferFilterRequestController extends AbstractController
{
    public function filterOffersAction(Request $request): Response
    {
        $this->initializeContaoFramework();

        $page = $request->query->get('page');
        $pageModel = PageModel::findByPk($page);
        $request->attributes->set('pageModel', $pageModel);

        $module = Module::getFrontendModule($request->get('id'));

        return new Response($module);
    }
}
