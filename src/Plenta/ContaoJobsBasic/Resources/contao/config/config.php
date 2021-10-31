<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

array_insert($GLOBALS['BE_MOD'], 1, [
    'plenta_jobs_basic' => [
        'plenta_jobs_basic_offers' => [
            'tables' => ['tl_plenta_jobs_basic_offer'],
        ],
        'plenta_jobs_basic_organizations' => [
            'tables' => ['tl_plenta_jobs_basic_organization'],
        ],
        'plenta_jobs_basic_location' => [
            'tables' => ['tl_plenta_jobs_basic_location'],
        ],
        'plenta_jobs_basic_settings' => [
        ],
        'plenta_jobs_basic_support' => [
        ],
    ],
]);
