<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

use Plenta\ContaoJobsBasic\EventListener\Contao\DCA\TlPlentaJobsBasicOffer;
use Symfony\Component\Intl\Currencies;

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer'] = [
    'config' => [
        'dataContainer' => 'Table',
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
    ],

    'list' => [
        'sorting' => [
            'mode' => 1,
            'fields' => ['title'],
            'flag' => 1,
            'panelLayout' => 'filter;search,sort,limit',
        ],
        'label' => [
            'fields' => ['title'],
            'showColumns' => false,
        ],
        'operations' => [
            'edit' => [
                'href' => 'table=tl_content',
                'icon' => 'edit.svg',
            ],
            'editheader' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
            ],
            'copy' => [
                'href' => 'act=copy',
                'icon' => 'copy.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\''.($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null).'\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'href' => null,
                'icon' => 'visible.svg',
                'attributes' => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => ['tl_plenta_jobs_basic_offer', 'toggleIcon'],
                'showInHeader' => true,
            ],
            'renewDatePosted' => [
                'attributes' => 'onclick="Backend.getScrollOffset();"',
                'haste_ajax_operation' => [
                    'field' => 'datePosted',
                    'options' => [
                        [
                            'value' => time(),
                            'icon' => 'sync.svg',
                        ],
                    ],
                ],
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],

    'palettes' => [
        '__selector__' => ['addImage', 'isRemote', 'hasLocationRequirements', 'addSalary'],
        'default' => '{title_legend},title,alias,description;{settings_legend},employmentType,validThrough;{location_legend},jobLocation,isRemote;{salary_legend},addSalary;{image_legend},addImage;{expert_legend:hide},cssClass;{publish_legend},published,start,stop',
    ],
    'subpalettes' => [
        'addImage' => 'singleSRC',
        'isRemote' => 'isOnlyRemote,hasLocationRequirements',
        'hasLocationRequirements' => 'applicantLocationRequirements',
        'addSalary' => 'salaryCurrency,salaryUnit,salaryValue,salaryMaxValue',
    ],

    'fields' => [
        'id' => [
        ],
        'tstamp' => [
        ],
        'title' => [
            'inputType' => 'text',
            'exclude' => true,
            'eval' => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50'
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
        ],
        'employmentType' => [
            'inputType' => 'select',
            'exclude' => true,
            'sorting' => true,
            'filter' => true,
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
        ],

        'validThrough' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
        ],

        'url' => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['url'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'rgxp' => 'url',
                'decodeEntities' => true,
                'maxlength' => 255,
                'dcaPicker' => true,
                'addWizardClass' => false,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(255) NOT NULL default ''",
        ],

        'cssClass' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'published' => [
            'exclude' => true,
            'filter' => true,
            'flag' => 1,
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
        ],
        'stop' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
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
        ],
        'isRemote' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 m12', 'isBoolean' => true],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'isOnlyRemote' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'clr w50'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'hasLocationRequirements' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ],
        'applicantLocationRequirements' => [
            'exclude' => true,
            'inputType' => 'group',
            'palette' => ['key', 'value'],
            'eval' => ['tl_class' => 'clr'],
            'fields' => [
                'key' => [
                    'inputType' => 'select',
                    'options' => ['Country', 'State', 'City', 'SchoolDistrict'],
                    'eval' => ['tl_class' => 'w50', 'mandatory' => true],
                    'reference' => &$GLOBALS['TL_LANG']['tl_plenta_jobs_basic_offer']['administrativeAreas'],
                ],
                'value' => [
                    'inputType' => 'text',
                    'eval' => ['mandatory' => true, 'tl_class' => 'w50'],
                ],
            ],
            'order' => false,
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
        ],
    ],
];

class tl_plenta_jobs_basic_offer extends \Contao\Backend
{
    /**
     * Import the back end user object.
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('Contao\BackendUser', 'User');
    }

    /**
     * Return the "toggle visibility" button.
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (Contao\Input::get('tid')) {
            $this->toggleVisibility(Contao\Input::get('tid'), (1 == Contao\Input::get('state')), (func_num_args() <= 12 ? null : func_get_arg(12)));
            $this->redirect($this->getReferer());
        }

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$this->User->hasAccess('tl_plenta_jobs_basic_offer::published', 'alexf')) {
            return '';
        }

        $href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

        if (!$row['published']) {
            $icon = 'invisible.svg';
        }

        return '<a href="'.$this->addToUrl($href).'" title="'.Contao\StringUtil::specialchars($title).'"'.$attributes.'>'.Contao\Image::getHtml($icon, $label, 'data-state="'.($row['published'] ? 1 : 0).'"').'</a> ';
    }

    /**
     * Disable/enable a job offer.
     *
     * @param int                  $intId
     * @param bool                 $blnVisible
     * @param Contao\DataContainer $dc
     *
     * @throws Contao\CoreBundle\Exception\AccessDeniedException
     */
    public function toggleVisibility($intId, $blnVisible, Contao\DataContainer $dc = null): void
    {
        // Set the ID and action
        Contao\Input::setGet('id', $intId);
        Contao\Input::setGet('act', 'toggle');

        if ($dc) {
            $dc->id = $intId; // see #8043
        }

        // Trigger the onload_callback
        if (isset($GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['config']['onload_callback']) && is_array($GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['config']['onload_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['config']['onload_callback'] as $callback) {
                if (is_array($callback)) {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($dc);
                } elseif (is_callable($callback)) {
                    $callback($dc);
                }
            }
        }

        // Check the field access
        if (!$this->User->hasAccess('tl_plenta_jobs_basic_offer::published', 'alexf')) {
            throw new Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to publish/unpublish job offer ID "'.$intId.'".');
        }

        $objRow = $this->Database->prepare('SELECT * FROM tl_plenta_jobs_basic_offer WHERE id=?')
            ->limit(1)
            ->execute($intId);

        if ($objRow->numRows < 1) {
            throw new Contao\CoreBundle\Exception\AccessDeniedException('Invalid job offer ID "'.$intId.'".');
        }

        // Set the current record
        if ($dc) {
            $dc->activeRecord = $objRow;
        }

        $objVersions = new Contao\Versions('tl_plenta_jobs_basic_offer', $intId);
        $objVersions->initialize();

        // Trigger the save_callback
        if (isset($GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['config']['save_callback']) && is_array($GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['fields']['published']['save_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['fields']['published']['save_callback'] as $callback) {
                if (is_array($callback)) {
                    $this->import($callback[0]);
                    $blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, $dc);
                } elseif (is_callable($callback)) {
                    $blnVisible = $callback($blnVisible, $dc);
                }
            }
        }

        $time = time();

        // Update the database
        $this->Database->prepare("UPDATE tl_plenta_jobs_basic_offer SET tstamp=$time, published='".($blnVisible ? '1' : '0')."' WHERE id=?")
            ->execute($intId);

        if ($dc) {
            $dc->activeRecord->tstamp = $time;
            $dc->activeRecord->published = ($blnVisible ? '1' : '');
        }

        // Trigger the onsubmit_callback
        if (isset($GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['config']['onsubmit_callback']) && is_array($GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['config']['onsubmit_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['config']['onsubmit_callback'] as $callback) {
                if (is_array($callback)) {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($dc);
                } elseif (is_callable($callback)) {
                    $callback($dc);
                }
            }
        }

        $objVersions->create();

        if ($dc) {
            $dc->invalidateCacheTags();
        }
    }
}
