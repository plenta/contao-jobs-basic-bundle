<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['plenta_jobs_basic_offer_list'] =
    '{title_legend},name,type;{config_legend},plentaJobsBasicHeadlineTag;{redirect_legend},jumpTo;{expert_legend:hide},cssID'
;

$GLOBALS['TL_DCA']['tl_module']['palettes']['plenta_jobs_basic_offer_reader'] =
    '{title_legend},name,type;{config_legend},plentaJobsBasicHeadlineTag;{expert_legend:hide},cssID'
;

$GLOBALS['TL_DCA']['tl_module']['palettes']['plenta_jobs_basic_filter_reader'] =
    '{title_legend},name,type;{config_legend},plentaJobsShowQuantity;{redirect_legend},jumpTo;{expert_legend:hide},cssID'
;

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsBasicHeadlineTag'] = [
    'exclude' => true,
    'search' => true,
    'inputType' => 'select',
    'options' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div'],
    'eval' => ['maxlength' => 8, 'tl_class' => 'w50 clr'],
    'sql' => "varchar(8) NOT NULL default 'h2'",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['plentaJobsShowQuantity'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'sql' => "char(1) NOT NULL default ''"
];
