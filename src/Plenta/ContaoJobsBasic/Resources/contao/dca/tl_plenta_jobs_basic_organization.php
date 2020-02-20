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

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_organization'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'enableVersioning' => true,
        'closed' => true,
    ],

    'list' => [],

    // Palettes
    'palettes' => [
        'default' => '{settings_legend},name,sameAs;',
    ],

    // Fields
    'fields' => [
        'id' => [
        ],
        'tstamp' => [
        ],
        'name' => [
            'label' => &$GLOBALS['TL_LANG']['tl_plenta_jobs_basic_organization']['name'],
            'exclude' => true,
            'inputType' => 'text',
            'default' => '',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50 clr'],
        ],
        'sameAs' => [
            'label' => &$GLOBALS['TL_LANG']['tl_plenta_jobs_basic_organization']['sameAs'],
            'exclude' => true,
            'inputType' => 'text',
            'default' => '',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50 clr'],
        ]
    ],
];
