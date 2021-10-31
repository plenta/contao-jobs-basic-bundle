<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_job_location'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'enableVersioning' => true,
    ],

    'list' => [
        'sorting' => [
            'mode' => 1,
            'fields' => [''],
            'flag' => 1,
            'disableGrouping' => true,
        ],
        'label' => [
            'fields' => [''],
            'format' => '%s',
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations' => [
            'edit' => [
                //'label' => &$GLOBALS['TL_LANG']['tl_jobs_product_config']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.svg',
            ],
        ],
    ],

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
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50 clr'],
        ],
    ],
];
