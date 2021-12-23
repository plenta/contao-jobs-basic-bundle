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

use Contao\Input;
use Haste\Form\Form as HasteForm;
use Contao\Template;
use Contao\ModuleModel;
use Contao\FormCheckBox;
use Doctrine\Persistence\ManagerRegistry;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer;
use Plenta\ContaoJobsBasic\Helper\EmploymentType;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;

/**
 * @FrontendModule("plenta_jobs_basic_filter",
 *   category="plentaJobsBasic",
 *   template="mod_plenta_jobs_basic_filter",
 *   renderer="forward"
 * )
 */
class JobOfferFilterController extends AbstractFrontendModuleController
{
    protected ManagerRegistry $registry;

    protected MetaFieldsHelper $metaFieldsHelper;

    protected EmploymentType $employmentTypeHelper;

    protected array $counterEmploymentType = [];

    public function __construct(
        ManagerRegistry $registry,
        MetaFieldsHelper $metaFieldsHelper,
        EmploymentType $employmentTypeHelper
    ) {
        $this->registry = $registry;
        $this->metaFieldsHelper = $metaFieldsHelper;
        $this->employmentTypeHelper = $employmentTypeHelper;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $form = new HasteForm('someid', 'GET', function($objHaste) {
            return Input::post('FORM_SUBMIT') === $objHaste->getFormId();
        });

        if (0 !== (int) $model->jumpTo) {
            $form->setFormActionFromPageId($model->jumpTo);
        }

        $form->addFormField('types', [
            'inputType' => 'checkbox',
            'options' => $this->getTypes($model),
            'eval' => ['multiple' => true]
        ]);

        $form->addFormField('submit', [
            'label' => $model->plentaJobsSubmit,
            'inputType' => 'submit'
        ]);

        $template->form = $form->generate();

        return $template->getResponse();
    }

    public function getTypes(ModuleModel $model): ?array
    {
        $options = [];
        $employmentTypes = [];
        $employmentTypeHelper = $this->employmentTypeHelper;
        $this->getAllOffers();

        foreach ($employmentTypeHelper->getEmploymentTypes() as $employmentType) {
            $employmentTypes[$employmentType] = $employmentTypeHelper->getEmploymentTypeName($employmentType);
        }

        if (array_is_assoc($employmentTypes)) {
            foreach ($employmentTypes as $k => $v) {
                if (true !== (bool) $model->plentaJobsShowAllTypes) {
                    if (!array_key_exists($k, $this->counterEmploymentType)) {
                        continue;
                    }
                }

                $options[$k] = $v.$this->addItemCounter($model, $k);
            }
        }

        return $options;
    }

    public function getTypesOld(ModuleModel $model): ?array
    {
        $options = [];
        $employmentTypes = [];
        $employmentTypeHelper = $this->employmentTypeHelper;
        $this->getAllOffers();

        foreach ($employmentTypeHelper->getEmploymentTypes() as $employmentType) {
            $employmentTypes[$employmentType] = $employmentTypeHelper->getEmploymentTypeName($employmentType);
        }

        if (array_is_assoc($employmentTypes)) {
            foreach ($employmentTypes as $k => $v) {
                if (true !== (bool) $model->plentaJobsShowAllTypes) {
                    if (!array_key_exists($k, $this->counterEmploymentType)) {
                        continue;
                    }
                }

                $options[] = [
                    'label' => $v.$this->addItemCounter($model, $k),
                    'value' => $k,
                    'group' => '1',
                ];
            }
        }

        return $options;
    }

    public function addItemCounter(ModuleModel $model, string $key): string
    {
        if (true === (bool) $model->plentaJobsShowQuantity &&
            array_key_exists($key, $this->counterEmploymentType)
        ) {
            return '<span class="item-counter">['.$this->counterEmploymentType[$key].']</span>';
        }

        return '';
    }

    public function getAllOffers(): array
    {
        $items = [];

        $jobOfferRepository = $this->registry->getRepository(TlPlentaJobsBasicOffer::class);
        $jobOffers = $jobOfferRepository->findAllPublished();

        foreach ($jobOffers as $jobOffer) {
            $this->collectEmploymenttypes($jobOffer->getEmploymentType());
            $items[] = $jobOffer;
        }

        return $items;
    }

    public function collectEmploymenttypes(?array $employmentTypes): void
    {
        if (is_array($employmentTypes)) {
            foreach ($employmentTypes as $employmentType) {
                if (array_key_exists($employmentType, $this->counterEmploymentType)) {
                    $this->counterEmploymentType[$employmentType] = ++$this->counterEmploymentType[$employmentType];
                } else {
                    $this->counterEmploymentType[$employmentType] = 1;
                }
            }
        }
    }
}
