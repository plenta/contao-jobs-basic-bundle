<?php

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
