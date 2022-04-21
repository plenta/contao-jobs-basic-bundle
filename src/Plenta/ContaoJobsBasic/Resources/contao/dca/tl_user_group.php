<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$GLOBALS['TL_DCA']['tl_user_group']['fields']['plenta_jobs_basic_settings'] = [
    'inputType' => 'checkbox',
    'exclude' => true,
    'options' => [
        'settings',
    ],
    'reference' => &$GLOBALS['TL_LANG']['tl_user_group']['plenta_jobs_basic_settings'],
    'eval' => [
        'multiple' => true,
    ],
    'sql' => 'blob NULL',
];

PaletteManipulator::create()
    ->addField('plenta_jobs_basic_settings', 'modules', PaletteManipulator::POSITION_AFTER)
    ->applyToPalette('default', 'tl_user_group')
;
