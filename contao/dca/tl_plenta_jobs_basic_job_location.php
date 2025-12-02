<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

use Contao\DC_Table;
use Contao\System;
use Plenta\ContaoJobsBasic\EventListener\Contao\DCA\TlPlentaJobsBasicJobLocation;
use Plenta\ContaoJobsBasic\GoogleForJobs\GoogleForJobs;

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_job_location'] = [
    // Config
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_plenta_jobs_basic_organization',
        'switchToEdit' => true,
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
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
        'default' => '{type_legend},jobTypeLocation,title',
        'onPremise' => '{type_legend},jobTypeLocation,title;{address_legend},streetAddress,postalCode,addressLocality,addressRegion,addressCountry',
        'Telecommute' => '{type_legend},jobTypeLocation,title;{location_legend},requirementType,requirementValue',
    ],

    // Fields
    'fields' => [
        'id' => [
            'search' => true,
            'sql' => [
                'type' => 'integer',
                'unsigned' => true,
                'autoincrement' => true,
            ],
        ],
        'pid' => [
            'foreignKey' => 'tl_plenta_jobs_basic_organization.name',
            'relation' => [
                'type' => 'belongsTo',
                'load' => 'lazy',
            ],
            'sql' => [
                'type' => 'integer',
                'unsigned' => true,
                'notnull' => false,
            ],
        ],
        'tstamp' => [
            'sorting' => true,
            'flag' => 6,
            'sql' => [
                'type' => 'integer',
                'unsigned' => true,
                'default' => 0,
            ],
        ],
        'streetAddress' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'default' => '',
            'eval' => [
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => [
                'type' => 'string',
                'length' => 255,
                'default' => '',
            ],
        ],
        'postalCode' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'default' => '',
            'eval' => [
                'mandatory' => true,
                'maxlength' => 32,
                'tl_class' => 'w50 clr',
            ],
            'sql' => [
                'type' => 'string',
                'length' => 32,
                'default' => '',
            ],
        ],
        'addressLocality' => [
            'exclude' => true,
            'sorting' => true,
            'search' => true,
            'flag' => 5,
            'inputType' => 'text',
            'default' => '',
            'eval' => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50',
            ],
            'sql' => [
                'type' => 'string',
                'length' => 255,
                'default' => '',
            ],
        ],
        'addressRegion' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'default' => '',
            'eval' => [
                'maxlength' => 255,
                'tl_class' => 'w50 clr',
            ],
            'sql' => [
                'type' => 'string',
                'length' => 255,
                'default' => '',
            ],
        ],
        'addressCountry' => [
            'exclude' => true,
            'inputType' => 'select',
            'options' => System::getContainer()->get('contao.intl.countries')->getCountries(),
            'eval' => [
                'mandatory' => true,
                'includeBlankOption' => true,
                'chosen' => true,
                'tl_class' => 'w50',
            ],
            'sql' => [
                'type' => 'string',
                'length' => 2,
                'default' => '',
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
            'sql' => [
                'type' => 'string',
                'length' => 32,
                'default' => 'onPremise',
            ],
        ],
        'requirementType' => [
            'exclude' => true,
            'inputType' => 'select',
            'options' => GoogleForJobs::ALLOWED_TYPES,
            'eval' => ['tl_class' => 'w50', 'mandatory' => true],
            'reference' => &$GLOBALS['TL_LANG']['tl_plenta_jobs_basic_job_location']['administrativeAreas'],
            'sql' => [
                'type' => 'string',
                'length' => 32,
                'default' => '',
            ],
        ],
        'requirementValue' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql' => [
                'type' => 'string',
                'length' => 255,
                'default' => '',
            ],
        ],
        'title' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
            'sql' => [
                'type' => 'string',
                'length' => 255,
                'default' => '',
            ],
        ],
    ],
];
