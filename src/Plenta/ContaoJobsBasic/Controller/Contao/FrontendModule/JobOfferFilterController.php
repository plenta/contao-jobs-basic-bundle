<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Controller\Contao\FrontendModule;

use Contao\Template;
use Contao\ModuleModel;
use Contao\FormCheckBox;
use Plenta\ContaoJobsBasic\Helper\EmploymentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;

/**
 * @FrontendModule("plenta_jobs_basic_filter_reader",
 *   category="plentaJobsBasic",
 *   template="mod_plenta_jobs_basic_filter",
 *   renderer="forward"
 * )
 */
class JobOfferFilterController extends AbstractFrontendModuleController
{
    protected EmploymentType $employmentTypeHelper;

    public function __construct(
        EmploymentType $employmentTypeHelper
    ) {
        $this->employmentTypeHelper = $employmentTypeHelper;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $model = new ModuleModel();
        $model->tstamp = time();

        $checkbox = new FormCheckBox($model);
        $checkbox->options = $this->getTypes();
        $checkbox->eval = ['multiple'=>true];

        $template->types = $checkbox->generate();

        return $template->getResponse();
    }

    public function getTypes(): ?array
    {
        $options = [];
        $employmentTypes = [];
        $employmentTypeHelper = $this->employmentTypeHelper;

        foreach ($employmentTypeHelper->getEmploymentTypes() as $employmentType) {
            $employmentTypes[$employmentType] = $employmentTypeHelper->getEmploymentTypeName($employmentType);
        }

        if (array_is_assoc($employmentTypes)) {
            foreach ($employmentTypes as $k => $v) {
                if (isset($v['label'])) {
                    $options[] = $v;
                } else {
                    $options[] = [
                        'label' => $v,
                        'value' => $k,
                        'group' => '1',
                    ];
                }
            }
        }

        return $options;
    }
}
