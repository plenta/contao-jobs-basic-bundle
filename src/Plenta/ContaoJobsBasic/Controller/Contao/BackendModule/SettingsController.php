<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Controller\Contao\BackendModule;

use Contao\BackendUser;
use Contao\CoreBundle\Controller\AbstractController;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Util\PackageUtil;
use Contao\System;
use Plenta\ContaoJobsBasic\Helper\PermissionsHelper;
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
        if (!PermissionsHelper::canAccessModule('settings')) {
            throw new AccessDeniedException('The settings module of the Plenta Jobs Basic Bundle is not allowed for user "'.BackendUser::getInstance()->username.'".');
        }

        System::loadLanguageFile('modules');

        $GLOBALS['TL_CSS'][] = 'bundles/plentacontaojobsbasic/dashboard.css';

        $mods = [];

        foreach ($GLOBALS['BE_MOD']['plenta_jobs_basic'] as $key => $mod) {
            if (isset($mod['hideInNavigation']) && $mod['hideInNavigation']) {
                if (!PermissionsHelper::canAccessModule($key, 'modules')) {
                    continue;
                }
                $mod['title'] = $GLOBALS['TL_LANG']['MOD'][$key];
                $mods[$key] = $mod;
            }
        }

        return new Response($this->twig->render(
            '@PlentaContaoJobsBasic/be_plenta_jobs_basic_settings.html.twig',
            [
                'title' => $GLOBALS['TL_LANG']['MOD']['plenta_jobs_basic_settings'][0],
                'mods' => $mods,
                'version' => PackageUtil::getVersion('plenta/contao-jobs-basic-bundle'),
            ]
        ));
    }

    public static function isActive(RequestStack $requestStack)
    {
        $do = $requestStack->getCurrentRequest()->get('do');
        if (isset($GLOBALS['BE_MOD']['plenta_jobs_basic'][$do], $GLOBALS['BE_MOD']['plenta_jobs_basic'][$do]['hideInNavigation']) && $GLOBALS['BE_MOD']['plenta_jobs_basic'][$do]['hideInNavigation']) {
            return true;
        }

        return false;
    }
}
