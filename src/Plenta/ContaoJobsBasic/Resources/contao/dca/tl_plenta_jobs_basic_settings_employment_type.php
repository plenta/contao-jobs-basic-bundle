<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

use Plenta\ContaoJobsBasic\EventListener\Contao\DCA\TlPlentaJobsBasicSettingsEmploymentType;

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_settings_employment_type'] = [
    'config' => [
        'dataContainer' => 'Table',
        'switchToEdit' => true,
        'markAsCopy' => 'title',
        'enableVersioning' => true,
    ],

    // Palettes
    'palettes' => [
        'default' => '{settings_legend},title,google_for_jobs_mapping,translation',
    ],

    // Fields
    'fields' => [
        'id' => [
        ],
        'tstamp' => [
        ],
        'title' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
        ],
        'google_for_jobs_mapping' => [
            'exclude' => true,
            'inputType' => 'select',
            'options' => ['ascending', 'descending'],
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => ['tl_class' => 'w50'],
        ],
        'translation' => [
            'inputType' => 'metaWizard',
            'eval' => [
                'allowHtml' => true,
                'multiple' => true,
                'metaFields' => [
                    'title' => 'maxlength="255"',
                ],
            ],
            'load_callback' => [[
                TlPlentaJobsBasicSettingsEmploymentType::class,
                'etranslationLoadCallback',
            ]],
            'save_callback' => [[
                TlPlentaJobsBasicSettingsEmploymentType::class,
                'translationSaveCallback',
            ]],
        ],
    ],
];
