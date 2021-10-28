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

array_insert($GLOBALS['BE_MOD'], 1, [
    'plenta_jobs_basic' => [
        'plenta_jobs_offers' => [
            'tables' => ['tl_plenta_jobs_basic_offer'],
        ],
        'plenta_jobs_organizations' => [
            'tables' => ['tl_plenta_jobs_basic_organization'],
        ],
        'plenta_jobs_settings' => [
        ]
    ],
]);
