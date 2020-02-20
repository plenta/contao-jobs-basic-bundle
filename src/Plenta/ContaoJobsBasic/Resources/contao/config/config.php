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

array_insert($GLOBALS['BE_MOD'], 1,
[
    'plenta_jobs_basic' => [
        'organization' => [
            'tables' => ['tl_plenta_jobs_basic_organization'],
        ],
        'jobOffer' => [
            'tables' => ['tl_plenta_jobs_basic_offer'],
        ],
    ],
]);
