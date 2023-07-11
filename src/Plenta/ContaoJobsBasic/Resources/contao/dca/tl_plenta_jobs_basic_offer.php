<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
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
                'attributes' => 'onclick="Backend.getScrollOffset()"',
                'href' => 'action=renewDatePosted',
                'icon' => 'sync.svg',
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],

    'palettes' => [
        '__selector__' => ['addImage', 'addSalary'],
        'default' => '{title_legend},title,alias,author,teaser,description;{meta_legend},pageTitle,robots,pageDescription,serpPreview;{translations_legend:hide},translations;{settings_legend},employmentType,validThrough,directApply;{location_legend},jobLocation;{salary_legend},addSalary;{image_legend},addImage;{expert_legend:hide},cssClass;{publish_legend},published,start,stop',
    ],
    'subpalettes' => [
        'addImage' => 'singleSRC',
        'addSalary' => 'salaryCurrency,salaryUnit,salaryValue,salaryMaxValue',
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
            'filter' => true,
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

        if ('renewDatePosted' === Input::get('action')) {
            $this->renewDatePosted(Input::get('id'));
        }
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
            $this->toggleVisibility(Contao\Input::get('tid'), 1 == Contao\Input::get('state'), func_num_args() <= 12 ? null : func_get_arg(12));
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

    public function renewDatePosted($intId): void
    {
        $objJobOffer = PlentaJobsBasicOfferModel::findByPk($intId);
        $objJobOffer->datePosted = time();
        $objJobOffer->save();
        $this->redirect($this->getReferer());
    }
}
