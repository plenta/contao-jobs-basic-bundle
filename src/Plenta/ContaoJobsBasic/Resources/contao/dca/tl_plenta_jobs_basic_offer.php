<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

use Plenta\ContaoJobsBasic\EventListener\Contao\DCA\TlPlentaJobsBasicOffer;

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'ctable' => ['tl_content'],
        'switchToEdit' => true,
        'markAsCopy' => 'title',
        'enableVersioning' => true,
    ],

    'list' => [
        'sorting' => [
            'mode' => 1,
            'fields' => ['title'],
            'flag' => 1,
            'panelLayout' => 'filter;search,sort,limit',
        ],
        'label' => [
            'fields' => ['title'],
            'showColumns' => false,
        ],
        'operations' => [
            'edit' => [
                'href' => 'table=tl_content',
                'icon' => 'edit.svg',
            ],
            'editheader' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
            ],
        ],
    ],

    // Palettes
    'palettes' => [
        'default' => '{title_legend},title,alias,description;{settings_legend},jobLocation,employmentType;',
    ],

    // Fields
    'fields' => [
        'id' => [
        ],
        'tstamp' => [
        ],
        'alias' => [
            'inputType' => 'text',
            'exclude' => true,
            'eval' => ['tl_class' => 'w50 clr'],
            'save_callback' => [[
                TlPlentaJobsBasicOffer::class,
                'aliasSaveCallback',
            ]],
        ],
        'title' => [
            'inputType' => 'text',
            'exclude' => true,
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50 clr'],
        ],
        'jobLocation' => [
            'inputType' => 'select',
            'exclude' => true,
            'filter' => true,
            'options_callback' => [
                TlPlentaJobsBasicOffer::class,
                'jobLocationOptionsCallback',
            ],
            'eval' => [
                'includeBlankOption' => true,
                'tl_class' => 'w50',
                'mandatory' => false,
                'multiple' => true,
                'chosen' => true,
            ],
        ],
        'employmentType' => [
            'inputType' => 'select',
            'exclude' => true,
            'sorting' => true,
            'filter' => true,
            'options_callback' => [
                TlPlentaJobsBasicOffer::class,
                'employmentTypeOptionsCallback',
            ],
            'load_callback' => [[
                TlPlentaJobsBasicOffer::class,
                'employmentTypeLoadCallback',
            ]],
            'save_callback' => [[
                TlPlentaJobsBasicOffer::class,
                'employmentTypeSaveCallback',
            ]],
            'eval' => [
                'includeBlankOption' => true,
                'tl_class' => 'w50',
                'mandatory' => true,
                'multiple' => true,
                'chosen' => true,
            ],
        ],
        'description' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'textarea',
            'eval' => [
                'rte' => 'tinyMCE',
                'tl_class' => 'clr',
            ],
        ], /*
        'serpPreview' => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['serpPreview'],
            'exclude' => true,
            'inputType' => 'serpPreview',
            'eval' => [
                'titleFields' => ['pageTitle', 'title'],
                'descriptionFields' => ['description', 'teaser'],
                //'title_tag_callback' => ['tl_page', 'getTitleTag'],
                //'url_callback' => ['tl_page', 'getSerpUrl']
            ],
            'sql' => null,
        ],
        */
    ],
];
