<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Controller\Contao;

use Contao\CoreBundle\Controller\AbstractBackendController;
use Contao\System;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Attribute\Route;

#[Route('%contao.backend.route_prefix%/_jobs', defaults: ['_scope' => 'backend'])]
class BackendController extends AbstractBackendController
{
    #[Route('/renewDatePosted', name: 'jobsBasic_renewDatePosted')]
    public function renewDatePosted(Request $request, RequestStack $requestStack)
    {
        $id = $request->get('id');
        $objJobOffer = PlentaJobsBasicOfferModel::findByPk($id);
        $objJobOffer->datePosted = time();
        $objJobOffer->save();

        return $this->redirect(System::getReferer());
    }
}
