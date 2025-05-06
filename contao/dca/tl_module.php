<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022-2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

use Plenta\ContaoJobsBasic\EventListener\Contao\DCA\JobOfferFields;
use Plenta\ContaoJobsBasic\EventListener\Contao\DCA\TlModule;
use Plenta\ContaoJobsBasic\EventListener\Contao\DCA\TlPlentaJobsBasicOffer;

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'plentaJobsBasicShowTypes';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'plentaJobsBasicShowLocations';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'plentaJobsBasicShowButton';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'plentaJobsBasicShowSorting';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'plentaJobsBasicShowKeyword';

$GLOBALS['TL_DCA']['tl_module']['palettes']['plenta_jobs_basic_offer_list'] =
    '{title_legend},name,type,headline;
    {config_legend},numberOfItems,perPage,plentaJobsBasicHeadlineTag,plentaJobsBasicSortingDefaultField,plentaJobsBasicSortingDefaultDirection,plentaJobsBasicShowSorting,plentaJobsBasicCompanies,plentaJobsBasicLocations,plentaJobsBasicEmploymentTypes,plentaJobsBasicNoFilter,plentaJobsBasicListParts,imgSize,plentaJobsBasicHideOffersWithoutTranslation;
    {redirect_legend},jumpTo;
    {template_legend:hide},plentaJobsBasicElementTpl,customTpl;
    {expert_legend:hide},cssID'
;

$GLOBALS['TL_DCA']['tl_module']['palettes']['plenta_jobs_basic_offer_reader'] =
    '{title_legend},name,type;
    {config_legend},plentaJobsBasicHeadlineTag,imgSize,plentaJobsBasicTemplateParts,plentaJobsBasicShowCompany,plentaJobsBasicShowLogo,plentaJobsBasicHideRemoteRequirements;
    {template_legend:hide},customTpl;
    {expert_legend:hide},cssID'
;

$GLOBALS['TL_DCA']['tl_module']['palettes']['plenta_jobs_basic_filter'] =
    '{title_legend},name,type;
    {config_legend},plentaJobsBasicShowButton,plentaJobsBasicCompanies,plentaJobsBasicShowKeyword,plentaJobsBasicShowTypes,plentaJobsBasicShowLocations,plentaJobsBasicHideOffersWithoutTranslation;
    {template_legend:hide},customTpl;
    {redirect_legend},jumpTo;
    {expert_legend:hide},cssID'
;

$GLOBALS['TL_DCA']['tl_module']['subpalettes']['plentaJobsBasicShowButton'] = 'plentaJobsBasicSubmit,plentaJobsBasicDynamicButton';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['plentaJobsBasicShowTypes'] = 'plentaJobsBasicTypesHeadline,plentaJobsBasicEmploymentTypes,plentaJobsBasicShowAllTypes,plentaJobsBasicShowQuantity';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['plentaJobsBasicShowLocations'] = 'plentaJobsBasicLocationsHeadline,plentaJobsBasicLocations,plentaJobsBasicShowAllLocations,plentaJobsBasicShowLocationQuantity,plentaJobsBasicDisableMultipleLocations';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['plentaJobsBasicShowSorting'] = 'plentaJobsBasicSortingFields';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['plentaJobsBasicShowKeyword'] = 'plentaJobsBasicKeywordHeadline';

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicHeadlineTag'] = [
    'exclude' => true,
    'search' => true,
    'inputType' => 'select',
    'options' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div'],
    'eval' => ['maxlength' => 8, 'tl_class' => 'w50 clr'],
    'sql' => "varchar(3) NOT NULL default 'h2' COLLATE ascii_bin",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicShowButton'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => "char(1) NOT NULL default '' COLLATE ascii_bin",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicSubmit'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['mandatory' => true, 'maxlength' => 64, 'tl_class' => 'w50 clr', 'decodeEntities' => true],
    'sql' => "varchar(64) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicShowTypes'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => "char(1) NOT NULL default '' COLLATE ascii_bin",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicTypesHeadline'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['allowHtml' => true, 'tl_class' => 'w50'],
    'sql' => 'tinytext NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicShowAllTypes'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr w50'],
    'sql' => "char(1) NOT NULL default '' COLLATE ascii_bin",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicShowQuantity'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50'],
    'sql' => "char(1) NOT NULL default '' COLLATE ascii_bin",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicShowLocations'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => "char(1) NOT NULL default '' COLLATE ascii_bin",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicLocationsHeadline'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['allowHtml' => true, 'tl_class' => 'w50 clr'],
    'sql' => 'tinytext NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicShowAllLocations'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr w50'],
    'sql' => "char(1) NOT NULL default '' COLLATE ascii_bin",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicShowLocationQuantity'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50'],
    'sql' => "char(1) NOT NULL default '' COLLATE ascii_bin",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicShowSorting'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => [
        'submitOnChange' => true,
        'tl_class' => 'clr',
    ],
    'sql' => "char(1) NOT NULL default '' COLLATE ascii_bin",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicSortingFields'] = [
    'exclude' => true,
    'inputType' => 'checkboxWizard',
    'options' => JobOfferFields::getFields(),
    'eval' => [
        'multiple' => true,
    ],
    'reference' => &$GLOBALS['TL_LANG']['tl_module']['plentaJobsBasicSortingFields']['fields'],
    'sql' => 'mediumtext NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicSortingDefaultField'] = [
    'exclude' => true,
    'inputType' => 'select',
    'options' => JobOfferFields::getFields(),
    'eval' => [
        'tl_class' => 'w50 clr',
    ],
    'reference' => &$GLOBALS['TL_LANG']['tl_module']['plentaJobsBasicSortingFields']['fields'],
    'sql' => "varchar(32) NOT NULL default '' COLLATE ascii_bin",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicSortingDefaultDirection'] = [
    'exclude' => true,
    'inputType' => 'select',
    'options' => ['ASC', 'DESC'],
    'eval' => [
        'tl_class' => 'w50',
    ],
    'sql' => "varchar(4) NOT NULL default '' COLLATE ascii_bin",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicTemplateParts'] = [
    'exclude' => true,
    'inputType' => 'checkboxWizard',
    'options' => [
        'title',
        'image',
        'elements',
        'description',
        'employmentType',
        'validThrough',
        'salary',
        'jobLocation',
        'backlink',
        'teaser',
        'publicationDate',
    ],
    'eval' => ['multiple' => true, 'tl_class' => 'clr'],
    'reference' => &$GLOBALS['TL_LANG']['MSC']['PLENTA_JOBS']['offerParts'],
    'sql' => 'mediumtext null',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicLocations'] = [
    'exclude' => true,
    'inputType' => 'checkboxWizard',
    'options_callback' => [TlModule::class, 'jobLocationOptionsCallback'],
    'eval' => ['multiple' => true, 'tl_class' => 'clr'],
    'sql' => 'mediumtext null',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicShowCompany'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'sql' => "char(1) NOT NULL default '' COLLATE ascii_bin",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicShowLogo'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'sql' => "char(1) NOT NULL default '' COLLATE ascii_bin",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicNoFilter'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'sql' => "char(1) NOT NULL default '' COLLATE ascii_bin",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicHideRemoteRequirements'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'sql' => "char(1) NOT NULL default '' COLLATE ascii_bin",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicListParts'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'options' => JobOfferFields::getParts(),
    'eval' => [
        'multiple' => true,
    ],
    'reference' => &$GLOBALS['TL_LANG']['MSC']['PLENTA_JOBS']['offerParts'],
    'sql' => "varchar(255) COLLATE ascii_bin NOT NULL default '".serialize(['jobLocation', 'publicationDate', 'employmentType'])."'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicEmploymentTypes'] = [
    'exclude' => true,
    'inputType' => 'checkboxWizard',
    'options_callback' => [TlPlentaJobsBasicOffer::class, 'employmentTypeOptionsCallback'],
    'eval' => [
        'multiple' => true,
        'tl_class' => 'clr',
    ],
    'sql' => 'mediumtext NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicHideOffersWithoutTranslation'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr w50'],
    'sql' => "char(1) COLLATE ascii_bin NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicDisableMultipleLocations'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr w50'],
    'sql' => "char(1) COLLATE ascii_bin NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicElementTpl'] = [
    'exclude' => true,
    'inputType' => 'select',
    'eval' => ['tl_class' => 'w50'],
    'sql' => "varchar(255) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicShowKeyword'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr w50', 'submitOnChange' => true],
    'sql' => "char(1) COLLATE ascii_bin NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicKeywordHeadline'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => ['allowHtml' => true, 'tl_class' => 'w50'],
    'sql' => 'tinytext NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicCompanies'] = [
    'inputType' => 'checkbox',
    'foreignKey' => 'tl_plenta_jobs_basic_organization.name',
    'eval' => ['multiple' => true, 'submitOnChange' => true],
    'sql' => 'mediumtext NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicDynamicButton'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => "char(1) COLLATE ascii_bin NOT NULL default ''",
];
