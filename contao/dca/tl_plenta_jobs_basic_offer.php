<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace contao\dca;

use Contao;
use Contao\Backend;
use Contao\BackendUser;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\Input;
use Contao\Versions;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\EventListener\Contao\DCA\TlPlentaJobsBasicOffer;
use Plenta\ContaoJobsBasic\Contao\Backend\OfferPanel;
use Symfony\Component\Intl\Currencies;

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => ['tl_content'],
        'switchToEdit' => true,
        'markAsCopy' => 'title',
        'enableVersioning' => true,
        'onload_callback' => [[
            TlPlentaJobsBasicOffer::class,
            'onShowInfoCallback',
        ]],
        'onsubmit_callback' => [[
            TlPlentaJobsBasicOffer::class,
            'saveCallbackGlobal',
        ]],
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    'list' => [
        'sorting' => [
            'mode' => 2,
            'fields' => ['title'],
            'flag' => 1,
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'label' => [
            'fields' => ['title'],
            'showColumns' => false,
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            ],
        ],
        'operations' => [
            'edit',
            'children',
            'copy',
            'delete',
            'toggle',
            'renewDatePosted' => [
                'attributes' => 'onclick="Backend.getScrollOffset()"',
                'route' => 'jobsBasic_renewDatePosted',
                'icon' => 'sync.svg',
            ],
            'show',
        ],
    ],

    'palettes' => [
        '__selector__' => ['addImage', 'overwriteMeta', 'addSalary'],
        'default' => '{title_legend},title,alias,author,teaser,description;{meta_legend},pageTitle,robots,pageDescription,serpPreview;{translations_legend:hide},translations;{settings_legend},employmentType,validThrough,entryDate,directApply;{location_legend},jobLocation;{salary_legend},addSalary;{image_legend},addImage;{expert_legend:hide},cssClass;{publish_legend},published,start,stop',
    ],
    'subpalettes' => [
        'addImage' => 'singleSRC,overwriteMeta',
        'addSalary' => 'salaryCurrency,salaryUnit,salaryValue,salaryMaxValue',
        'overwriteMeta' => 'alt,imageTitle,imageUrl,caption',
    ],

    'fields' => [
        'id' => [
            'sql' => [
                'type' => 'integer',
                'unsigned' => true,
                'autoincrement' => true,
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
        'title' => [
            'inputType' => 'text',
            'exclude' => true,
            'sorting' => true,
            'eval' => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'long',
            ],
            'sql' => [
                'type' => 'string',
                'length' => 255,
                'default' => '',
            ],
        ],
        'alias' => [
            'inputType' => 'text',
            'exclude' => true,
            'eval' => ['tl_class' => 'w50', 'doNotCopy' => true],
            'save_callback' => [[
                TlPlentaJobsBasicOffer::class,
                'aliasSaveCallback',
            ]],
            'sql' => [
                'type' => 'text',
                'notnull' => false,
                'default' => null,
            ],
        ],
        'teaser' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'textarea',
            'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
            'sql' => 'text NULL',
        ],
        'description' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'textarea',
            'eval' => [
                'mandatory' => true,
                'rte' => 'tinyMCE',
                'tl_class' => 'clr',
            ],
            'sql' => [
                'type' => 'text',
                'notnull' => false,
                'default' => null,
            ],
        ],
        'pageTitle' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'robots' => [
            'exclude' => true,
            'inputType' => 'select',
            'options' => ['index,follow', 'index,nofollow', 'noindex,follow', 'noindex,nofollow'],
            'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true],
            'sql' => "varchar(32) NOT NULL default ''",
        ],
        'pageDescription' => [
            'exclude' => true,
            'inputType' => 'textarea',
            'eval' => ['style' => 'height:60px', 'decodeEntities' => true, 'tl_class' => 'clr'],
            'sql' => 'text NULL',
        ],
        'serpPreview' => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['serpPreview'],
            'exclude' => true,
            'inputType' => 'serpPreview',
            'eval' => [
                'url_callback' => [TlPlentaJobsBasicOffer::class, 'getSerpUrl'],
                'titleFields' => ['pageTitle', 'title'],
                'descriptionFields' => ['pageDescription', 'teaser'],
            ],
            'sql' => null,
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
                'mandatory' => true,
                'multiple' => true,
                'chosen' => true,
            ],
            'sql' => [
                'type' => 'text',
                'notnull' => false,
                'default' => null,
            ],
        ],
        'employmentType' => [
            'inputType' => 'select',
            'exclude' => true,
            'reference' => &$GLOBALS['TL_LANG']['MSC']['PLENTA_JOBS'],
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
            'sql' => 'json NULL default NULL',
        ],
        'validThrough' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => [
                'type' => 'string',
                'length' => 10,
                'default' => '',
            ],
        ],
        'directApply' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12', 'isBoolean' => true],
            'sql' => ['type' => 'boolean', 'default' => true],
        ],
        'cssClass' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
            'sql' => [
                'type' => 'string',
                'length' => 255,
                'default' => '',
            ],
        ],
        'published' => [
            'exclude' => true,
            'filter' => true,
            'flag' => 1,
            'toggle' => true,
            'inputType' => 'checkbox',
            'eval' => [
                'isBoolean' => true,
            ],
            'sql' => [
                'type' => 'boolean',
                'default' => false,
            ],
        ],
        'start' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => [
                'type' => 'string',
                'length' => 10,
                'default' => '',
            ],
        ],
        'stop' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => [
                'type' => 'string',
                'length' => 10,
                'default' => '',
            ],
        ],
        'addImage' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'singleSRC' => [
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => ['fieldType' => 'radio', 'filesOnly' => true, 'mandatory' => true, 'tl_class' => 'clr'],
            'sql' => [
                'type' => 'binary_string',
                'notnull' => false,
            ],
        ],
        'addSalary' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'salaryCurrency' => [
            'exclude' => true,
            'inputType' => 'select',
            'default' => 'EUR',
            'options' => Currencies::getNames(),
            'eval' => [
                'chosen' => true,
                'mandatory' => true,
                'tl_class' => 'w50',
            ],
            'sql' => [
                'type' => 'string',
                'length' => 5,
                'default' => 'EUR',
            ],
        ],
        'salaryValue' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'rgxp' => 'digit',
                'tl_class' => 'w50',
            ],
            'load_callback' => [
                [TlPlentaJobsBasicOffer::class, 'salaryOnLoad'],
            ],
            'save_callback' => [
                [TlPlentaJobsBasicOffer::class, 'salaryOnSave'],
            ],
            'sql' => [
                'type' => 'integer',
                'default' => 0,
            ],
        ],
        'salaryMaxValue' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'digit',
                'tl_class' => 'w50',
            ],
            'load_callback' => [
                [TlPlentaJobsBasicOffer::class, 'salaryOnLoad'],
            ],
            'save_callback' => [
                [TlPlentaJobsBasicOffer::class, 'salaryOnSave'],
            ],
            'sql' => [
                'type' => 'integer',
                'default' => 0,
            ],
        ],
        'salaryUnit' => [
            'exclude' => true,
            'inputType' => 'select',
            'default' => 'MONTH',
            'options' => ['HOUR', 'DAY', 'WEEK', 'MONTH', 'YEAR'],
            'eval' => [
                'mandatory' => true,
                'tl_class' => 'w50',
            ],
            'reference' => &$GLOBALS['TL_LANG']['tl_plenta_jobs_basic_offer']['salaryUnits'],
            'sql' => [
                'type' => 'string',
                'length' => 5,
                'default' => '',
            ],
        ],
        'translations' => [
            'inputType' => 'group',
            'palette' => ['language', 'title', 'alias', 'teaser', 'description', 'pageTitle', 'pageDescription'],
            'fields' => [
                'language' => [
                    'inputType' => 'select',
                    'options_callback' => [TlPlentaJobsBasicOffer::class, 'getLanguages'],
                    'eval' => [
                        'chosen' => true,
                        'mandatory' => true,
                    ],
                ],
            ],
            'sql' => [
                'type' => 'text',
                'notnull' => false,
                'default' => null,
            ],
        ],
        'datePosted' => [
            'sorting' => true,
            'sql' => [
                'type' => 'integer',
                'unsigned' => true,
                'notnull' => false,
                'default' => null,
            ],
        ],
        'author' => [
            'default' => BackendUser::getInstance()->id,
            'exclude' => true,
            'search' => true,
            'filter' => true,
            'sorting' => true,
            'flag' => DataContainer::SORT_ASC,
            'inputType' => 'select',
            'foreignKey' => 'tl_user.name',
            'eval' => ['doNotCopy' => true, 'chosen' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql' => 'int(10) unsigned NOT NULL default 0',
            'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
        ],
        'overwriteMeta' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql' => "char(1) COLLATE ascii_bin NOT NULL default ''"
        ],
        'alt' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''"
        ],
        'imageTitle' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength'=>255, 'tl_class'=>'w50'],
            'sql' => "varchar(255) NOT NULL default ''"
        ],
        'imageUrl' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => [
                'rgxp' => 'url',
                'decodeEntities' => true,
                'maxlength' => 2048,
                'dcaPicker' => true,
                'tl_class' => 'w50'
            ],
            'sql' => "text NULL"
        ],
        'caption' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'allowHtml' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''"
        ],
        'entryDate' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'date', 'datepicker' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
    ],
];
