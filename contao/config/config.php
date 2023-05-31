<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

use Composer\InstalledVersions;
use Contao\ArrayUtil;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicJobLocationModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOrganizationModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicSettingsEmploymentTypeModel;
use Plenta\ContaoJobsBasic\EventListener\Contao\Hooks\ChangelanguageNavigationListener;

ArrayUtil::arrayInsert($GLOBALS['BE_MOD'], 1, [
    'plenta_jobs_basic' => [
        'plenta_jobs_basic_offers' => [
            'tables' => ['contao\dca\tl_plenta_jobs_basic_offer', 'tl_content'],
        ],
        'plenta_jobs_basic_organizations' => [
            'tables' => ['tl_plenta_jobs_basic_organization', 'tl_plenta_jobs_basic_job_location'],
            'hideInNavigation' => true,
        ],
        'plenta_jobs_basic_settings_employment_type' => [
            'tables' => ['tl_plenta_jobs_basic_settings_employment_type'],
            'hideInNavigation' => true,
        ],
    ],
]);

if (defined('TL_MODE') && TL_MODE == 'BE') {
    $GLOBALS['TL_CSS'][] = 'bundles/plentacontaojobsbasic/backend.css|static';
}

$GLOBALS['TL_MODELS'][PlentaJobsBasicOfferModel::getTable()] = PlentaJobsBasicOfferModel::class;
$GLOBALS['TL_MODELS'][PlentaJobsBasicJobLocationModel::getTable()] = PlentaJobsBasicJobLocationModel::class;
$GLOBALS['TL_MODELS'][PlentaJobsBasicOrganizationModel::getTable()] = PlentaJobsBasicOrganizationModel::class;
$GLOBALS['TL_MODELS'][PlentaJobsBasicSettingsEmploymentTypeModel::getTable()] = PlentaJobsBasicSettingsEmploymentTypeModel::class;

if (InstalledVersions::isInstalled('terminal42/contao-changelanguage')) {
    $GLOBALS['TL_HOOKS']['changelanguageNavigation'][] = [ChangelanguageNavigationListener::class, 'onChangelanguageNavigation'];
}
