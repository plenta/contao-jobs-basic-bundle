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
        'ctable' => ['tl_plenta_jobs_basic_job_location'],
        'switchToEdit' => true,
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
                'href' => 'table=tl_plenta_jobs_basic_job_location',
                'icon' => 'edit.svg',
            ],
            'editheader' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
                //'button_callback'     => array('tl_news_archive', 'editHeader')
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
