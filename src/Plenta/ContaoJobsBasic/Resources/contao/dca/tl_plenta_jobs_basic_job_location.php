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
use Plenta\ContaoJobsBasic\EventListener\Contao\DCA\TlPlentaJobsBasicJobLocation;
use Plenta\ContaoJobsBasic\GoogleForJobs\GoogleForJobs;

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_job_location'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'ptable' => 'tl_plenta_jobs_basic_organization',
        'switchToEdit' => true,
        'enableVersioning' => true,
    ],

    'list' => [
        'sorting' => [
            'mode' => 4,
            'flag' => 1,
            'fields' => ['pid'],
            'headerFields' => ['name'],
            'child_record_callback' => [TlPlentaJobsBasicJobLocation::class, 'listLocations'],
            'child_record_class' => 'no_padding',
            'panelLayout' => 'filter;sort,search,limit',
            'disableGrouping' => true,
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
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).'\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],

    // Palettes
    'palettes' => [
        '__selector__' => ['jobTypeLocation'],
        'default' => '{type_legend},jobTypeLocation',
        'onPremise' => '{type_legend},jobTypeLocation;{address_legend},streetAddress,postalCode,addressLocality,addressRegion,addressCountry',
        'Telecommute' => '{type_legend},jobTypeLocation;{location_legend},requirementType,requirementValue',
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
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50 clr',
            ],
        ],
        'addressLocality' => [
            'exclude' => true,
            'sorting' => true,
            'flag' => 5,
            'inputType' => 'text',
            'default' => '',
            'eval' => [
                'mandatory' => true,
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
                'mandatory' => true,
                'includeBlankOption' => true,
                'chosen' => true,
                'tl_class' => 'w50',
            ],
        ],
        'jobTypeLocation' => [
            'exclude' => true,
            'inputType' => 'select',
            'options' => ['onPremise', 'Telecommute'],
            'reference' => &$GLOBALS['TL_LANG']['tl_plenta_jobs_basic_job_location']['jobTypeLocationOptions'],
            'eval' => [
                'submitOnChange' => true,
            ],
        ],
        'requirementType' => [
            'exclude' => true,
            'inputType' => 'select',
            'options' => GoogleForJobs::ALLOWED_TYPES,
            'eval' => ['tl_class' => 'w50', 'mandatory' => true],
            'reference' => &$GLOBALS['TL_LANG']['tl_plenta_jobs_basic_job_location']['administrativeAreas'],
        ],
        'requirementValue' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'tl_class' => 'w50'],
        ],
    ],
];
