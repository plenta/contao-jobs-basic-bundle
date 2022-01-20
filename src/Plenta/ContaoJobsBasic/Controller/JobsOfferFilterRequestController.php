<?php

declare(strict_types=1);

/**
 * @copyright     Copyright (c) 2009-2021, Christian Barkowsky & Christoph Werner
 * @author        Christian Barkowsky <https://brkwsky.de>
 * @author        Christoph Werner <https://brkwsky.de>
 * @license       proprietary
 */

namespace Plenta\ContaoJobsBasic\Controller;

use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\Module;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JobsOfferFilterRequestController extends AbstractController
{
    public function __construct() {
    }

    public function filterOffersAction(Request $request): Response
    {
        $fmd = Module::getFrontendModule($request->get('id'));
        return new Response($fmd);
    }
}
