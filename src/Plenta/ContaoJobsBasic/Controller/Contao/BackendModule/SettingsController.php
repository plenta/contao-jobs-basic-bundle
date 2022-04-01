<?php

namespace Plenta\ContaoJobsBasic\Controller\Contao\BackendModule;

use Contao\CoreBundle\Controller\AbstractController;
use Contao\System;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;

class SettingsController extends AbstractController
{
    private $twig;

    public function __construct(TwigEnvironment $twig)
    {
        $this->twig = $twig;
    }

    public function showSettings(): Response
    {
        System::loadLanguageFile('modules');

        $mods = [];

        foreach ($GLOBALS['BE_MOD']['plenta_jobs_basic'] as $key => $mod) {
            if (isset($mod['hideInNavigation']) && $mod['hideInNavigation']) {
                $mod['title'] = $GLOBALS['TL_LANG']['MOD'][$key];
                $mods[$key] = $mod;
            }
        }

        return new Response($this->twig->render(
            '@PlentaContaoJobsBasic/be_plenta_jobs_basic_settings.html.twig',
            [
                'title' => $GLOBALS['TL_LANG']['MOD']['plenta_jobs_basic_settings'][0],
                'mods' => $mods
            ]
        ));
    }

    public static function isActive(RequestStack $requestStack)
    {
        $do = $requestStack->getCurrentRequest()->get('do');
        if (isset($GLOBALS['BE_MOD']['plenta_jobs_basic'][$do]) && $GLOBALS['BE_MOD']['plenta_jobs_basic'][$do]['hideInNavigation']) {
            return true;
        }
        return false;
    }
}