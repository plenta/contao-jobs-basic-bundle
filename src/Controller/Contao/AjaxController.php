<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2025, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Controller\Contao;

use Contao\CoreBundle\Controller\AbstractController;
use Contao\CoreBundle\String\SimpleTokenParser;
use Contao\ModuleModel;
use Plenta\ContaoJobsBasic\Helper\CountJobsHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('_plenta-jobs-basic/offer', defaults: ['_scope' => 'frontend'])]
class AjaxController extends AbstractController
{
    #[Route('/count', methods: ['GET'])]
    public function count(Request $request, CountJobsHelper $countJobsHelper, SimpleTokenParser $tokenParser)
    {
        $module = ModuleModel::findByPk($request->get('module'));

        if (!$module) {
            throw new \Exception('Module not found');
        }

        $request->attributes->set('moduleModel', $module);

        return new Response($tokenParser->parse($module->plentaJobsBasicSubmit, ['count' => $countJobsHelper->countJobs()]));
    }
}
