<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

use Contao\System;

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_job_location'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'ptable' => 'tl_plenta_jobs_basic_organization',
        'enableVersioning' => true,
    ],

    'list' => [
        'sorting' => [
            'mode' => 2,
            'fields' => ['pid'],
            'flag' => 1,
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'label' => [
            'fields' => [
                'streetAddress',
                'postalCode',
                'addressLocality',
            ],
            'format' => '%s, %s %s',
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
                'label' => &$GLOBALS['TL_LANG']['tl_jobs_product_config']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.svg',
            ],
        ],
    ],

    // Palettes
    'palettes' => [
        'default' => '{settings_legend},organization,streetAddress,postalCode,addressLocality,addressRegion,addressCountry;',
    ],

    // Fields
    'fields' => [
        'id' => [
            'search' => true,
        ],
        'pid' => [
            'foreignKey' => 'tl_plenta_jobs_basic_organization.name',
            'relation' => [
                'type' => 'belongsTo',
                'load' => 'lazy',
            ],
        ],
        'tstamp' => [
            'sorting' => true,
            'flag' => 6,
        ],
        'streetAddress' => [
            'exclude' => true,
            'inputType' => 'text',
            'default' => '',
            'eval' => [
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
        ],
        'postalCode' => [
            'exclude' => true,
            'inputType' => 'text',
            'default' => '',
            'eval' => [
                'maxlength' => 255,
                'tl_class' => 'w50 clr',
            ],
        ],
        'addressLocality' => [
            'exclude' => true,
            'inputType' => 'text',
            'default' => '',
            'eval' => [
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
        ],
        'addressRegion' => [
            'exclude' => true,
            'inputType' => 'text',
            'default' => '',
            'eval' => [
                'maxlength' => 255,
                'tl_class' => 'w50 clr',
            ],
        ],
        'addressCountry' => [
            'exclude' => true,
            'inputType' => 'select',
            'options' => System::getCountries(),
            'eval' => [
                'includeBlankOption' => true,
                'chosen' => true,
                'tl_class' => 'w50',
            ],
        ],
    ],
];
