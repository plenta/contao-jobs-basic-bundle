<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

$GLOBALS['TL_DCA']['tl_content']['palettes']['plenta_jobs_basic_job_offer_details'] = '{type_legend},type,plenta_jobs_basic_job_offer_details;{image_legend},size;{expert_legend},guests,cssID;{invisible_legend:hide},invisible,start,stop';

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
