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
        'enableVersioning' => true,
    ],

    // Palettes
    'palettes' => [
        'default' => '{settings_legend},jobLocation,employmentType;',
    ],

    // Fields
    'fields' => [
        'id' => [
        ],
        'tstamp' => [
        ],
        'jobLocation' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'foreignKey' => 'tl_plenta_jobs_basic_job_location.streetAddress',
            'eval' => [
                'includeBlankOption' => false,
                'tl_class' => 'w50',
                'mandatory' => true,
            ],
        ],
        'title' => [
            'exclude' => true,
            'inputType' => 'text',
            'default' => '',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50 clr'],
        ],
        'employmentType' => [
            'exclude' => true,
            'inputType' => 'select',
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
            'sorting' => true,
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
        ],
    ],
];
