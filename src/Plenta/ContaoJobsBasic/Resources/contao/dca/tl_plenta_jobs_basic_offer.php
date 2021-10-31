<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'enableVersioning' => true,
    ],

    // Palettes
    'palettes' => [
        'default' => '{settings_legend},jobLocation;',
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
    ],
];
