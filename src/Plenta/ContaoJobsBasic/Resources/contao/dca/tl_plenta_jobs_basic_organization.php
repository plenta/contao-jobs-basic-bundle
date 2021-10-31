<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_organization'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'enableVersioning' => true,
    ],

    'list' => [
        'sorting' => [
            'mode' => 2,
            'fields' => ['name'],
            'flag' => 1,
            'disableGrouping' => true,
        ],
        'label' => [
            'fields' => ['name'],
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
        ],
    ],
];
