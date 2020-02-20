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

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_job_location'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'enableVersioning' => true,
        'closed' => true,
    ],

    'list' => [],

    // Palettes
    'palettes' => [
        'default' => '{settings_legend},streetAddress;',
    ],

    // Fields
    'fields' => [
        'id' => [
        ],
        'tstamp' => [
        ],
        'streetAddress' => [
            'label' => &$GLOBALS['TL_LANG']['tl_jobs_product_config']['streetAddress'],
            'exclude' => true,
            'inputType' => 'text',
            'default' => '',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50 clr']
        ]
    ],
];
