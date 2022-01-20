<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Controller\Contao\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\Input;
use Contao\ModuleModel;
use Contao\Template;
use Doctrine\Persistence\ManagerRegistry;
use Haste\Form\Form as HasteForm;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicJobLocation;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer;
use Plenta\ContaoJobsBasic\Helper\EmploymentType;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
                    if (!\array_key_exists($k, $this->counterEmploymentType)) {
                        continue;
                    }
                }

                $options[$k] = $v.$this->addItemCounter($model, $k);
            }
        }

        return $options;
    }

    public function addItemCounter(ModuleModel $model, string $key): string
    {
        if (true === (bool) $model->plentaJobsShowQuantity &&
            \array_key_exists($key, $this->counterEmploymentType)
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
        if (\is_array($employmentTypes)) {
            foreach ($employmentTypes as $employmentType) {
                if (\array_key_exists($employmentType, $this->counterEmploymentType)) {
                    $this->counterEmploymentType[$employmentType] = ++$this->counterEmploymentType[$employmentType];
                } else {
                    $this->counterEmploymentType[$employmentType] = 1;
                }
            }
        }
    }

    public function getLocations(ModuleModel $model): ?array
    {
        $options = [];

        foreach ($this->getAllLocations() as $k) {
            if (array_key_exists($k->getAddressLocality(), $options)) {
                $options[$k->getAddressLocality()] = $options[$k->getAddressLocality()].'|'.$k->getId();
            } else {
                $options[$k->getAddressLocality()] = $k->getId();
            }
        }

        $options = array_flip($options);

        return $options;
    }

    public function getAllLocations(): array
    {
        $items = [];

        $locationsRepository = $this->registry->getRepository(TlPlentaJobsBasicJobLocation::class);
        $locations = $locationsRepository->findAll();

        foreach ($locations as $location) {
            $items[] = $location;
        }

        return $items;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $form = new HasteForm('someid', 'GET', fn ($objHaste) => Input::post('FORM_SUBMIT') === $objHaste->getFormId());

        if (0 !== (int) $model->jumpTo) {
            $form->setFormActionFromPageId($model->jumpTo);
        }

        if ($model->plentaJobsShowTypes) {
            $form->addFormField('typesHeadline', [
                'inputType' => 'html',
                'eval' => ['html' => $model->plentaJobsTypesHeadline],
            ]);

            $form->addFormField('types', [
                'inputType' => 'checkbox',
                'default' => $request->query->get('types'),
                'options' => $this->getTypes($model),
                'eval' => ['multiple' => true],
            ]);
        }

        if ($model->plentaJobsShowLocations) {
            $form->addFormField('locationHeadline', [
                'inputType' => 'html',
                'eval' => ['html' => $model->plentaJobsLocationsHeadline],
            ]);

            $form->addFormField('location', [
                'inputType' => 'checkbox',
                'default' => $request->query->get('location'),
                'options' => $this->getLocations($model),
                'eval' => ['multiple' => true],
            ]);
        }

        if ($model->plentaJobsShowButton) {
            $form->addFormField('submit', [
                'label' => $model->plentaJobsSubmit,
                'inputType' => 'submit',
            ]);
        }

        $template->form = $form->generate();

        return $template->getResponse();
    }
}
