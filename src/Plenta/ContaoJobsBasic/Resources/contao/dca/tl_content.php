<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

$GLOBALS['TL_DCA']['tl_content']['fields']['plenta_jobs_basic_job_offer_details'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['mycheckboxwizard'],
    'exclude' => true,
    'inputType' => 'checkboxWizard',
    'options' => [
        'employmentTypeFormatted' => 'Stellenarten',
        'description' => 'Beschreibung',
        'publicationDateFormatted' => 'Veröffentlichungsdatum',
        'title' => 'Titel',
        'addressLocalityFormatted' => 'Arbeitsort',
    ],
    'eval' => ['multiple' => true, 'tl_class' => 'clr'],
    'sql' => 'blob NULL',
];
