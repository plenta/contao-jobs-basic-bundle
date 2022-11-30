<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

use Plenta\ContaoJobsBasic\EventListener\Contao\DCA\JobOfferFields;

$GLOBALS['TL_DCA']['tl_content']['palettes']['plenta_jobs_basic_job_offer_details'] = '{type_legend},type,plenta_jobs_basic_job_offer_details;{image_legend},size;{expert_legend},guests,cssID;{invisible_legend:hide},invisible,start,stop';
$GLOBALS['TL_DCA']['tl_content']['palettes']['plenta_jobs_basic_job_offer_teaser'] = '{type_legend},type,headline;{plenta_jobs_basic_legend},text,plentaJobsBasicHeadlineTag,plentaJobsBasicJobOffer,plentaJobsBasicJobOfferTeaserParts;{image_legend},size;{expert_legend},guests,cssID;{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA']['tl_content']['fields']['plenta_jobs_basic_job_offer_details'] = [
    'exclude' => true,
    'inputType' => 'checkboxWizard',
    'options' => [
        'employmentTypeFormatted' => 'Stellenarten',
        'description' => 'Beschreibung',
        'publicationDateFormatted' => 'Veröffentlichungsdatum',
        'title' => 'Titel',
        'addressLocalityFormatted' => 'Arbeitsort',
        'image' => 'Bild',
    ],
    'eval' => ['multiple' => true, 'tl_class' => 'clr'],
    'sql' => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['plentaJobsBasicJobOffer'] = [
    'exclude' => true,
    'inputType' => 'select',
    'foreignKey' => 'tl_plenta_jobs_basic_offer.title',
    'eval' => [
        'chosen' => true,
        'tl_class' => 'clr',
    ],
    'sql' => 'int(10) unsigned NOT NULL default 0',
];

$GLOBALS['TL_DCA']['tl_content']['fields']['plentaJobsBasicJobOfferTeaserParts'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'options' => JobOfferFields::getParts(),
    'eval' => [
        'multiple' => true,
    ],
    'reference' => &$GLOBALS['TL_LANG']['MSC']['PLENTA_JOBS']['offerParts'],
    'sql' => "varchar(255) COLLATE ascii_bin NOT NULL default '".serialize(['jobLocation', 'publicationDate', 'employmentType'])."'",
];

$GLOBALS['TL_DCA']['tl_content']['fields']['plentaJobsBasicHeadlineTag'] = [
    'exclude' => true,
    'search' => true,
    'inputType' => 'select',
    'options' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div'],
    'eval' => ['maxlength' => 8, 'tl_class' => 'w50 clr'],
    'sql' => "varchar(3) NOT NULL default 'h2' COLLATE ascii_bin",
];
