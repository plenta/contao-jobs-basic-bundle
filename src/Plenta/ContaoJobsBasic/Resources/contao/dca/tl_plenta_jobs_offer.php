<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao CMS
 *
 * @copyright     Copyright (c) 2020, Plenta. Digital solutions
 * @author        Plenta. Digital solutions <https://plenta.io>
 * @author        Christian Barkowsky <https://plenta.io>
 * @author        Christoph Werner <https://plenta.io>
 * @link          https://plenta.io
 */

$GLOBALS['TL_DCA']['tl_plenta_jobs_offer'] = [
    // Config
    'config' => [
        'dataContainer' => 'Table',
        'enableVersioning' => true
    ],

    // Fields
    'fields' => [
        'id' => [
        ],
        'tstamp' => [
        ],
    ],
];