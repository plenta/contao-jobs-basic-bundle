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

use Contao\Controller;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\ModuleModel;
use Contao\StringUtil;
use Contao\Template;
use Doctrine\Persistence\ManagerRegistry;
use Haste\Form\Form as HasteForm;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicJobLocation;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer;
use Plenta\ContaoJobsBasic\Helper\EmploymentType;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

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
    protected RouterInterface $router;
    protected array $counterEmploymentType = [];
    protected array $counterLocation = [];
    protected array $locations = [];
    protected array $offers = [];

    public function __construct(
        ManagerRegistry $registry,
        MetaFieldsHelper $metaFieldsHelper,
        EmploymentType $employmentTypeHelper,
        RouterInterface $router
    ) {
        $this->registry = $registry;
        $this->metaFieldsHelper = $metaFieldsHelper;
        $this->employmentTypeHelper = $employmentTypeHelper;
        $this->router = $router;
    }

    public function getTypes(ModuleModel $model): ?array
    {
        $options = [];
        $employmentTypes = [];
        $employmentTypeHelper = $this->employmentTypeHelper;
        $this->getAllOffers($model);

        foreach ($employmentTypeHelper->getEmploymentTypes() as $employmentType) {
            $employmentTypes[$employmentType] = $employmentTypeHelper->getEmploymentTypeName($employmentType);
        }

        if (array_is_assoc($employmentTypes)) {
            foreach ($employmentTypes as $k => $v) {
                if (true !== (bool) $model->plentaJobsBasicShowAllTypes) {
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
        if (true === (bool) $model->plentaJobsBasicShowQuantity &&
            \array_key_exists($key, $this->counterEmploymentType)
        ) {
            return '<span class="item-counter">['.$this->counterEmploymentType[$key].']</span>';
        }

        return '';
    }

    public function addLocationCounter(ModuleModel $model, string $key): string
    {
        if (true === (bool) $model->plentaJobsBasicShowLocationQuantity && \array_key_exists($key, $this->counterLocation)) {
            return '<span class="item-counter">['.$this->counterLocation[$key].']</span>';
        }

        return '';
    }

    public function getAllOffers($model): array
    {
        if (empty($this->offers)) {
            $jobOfferRepository = $this->registry->getRepository(TlPlentaJobsBasicOffer::class);
            $moduleLocations = StringUtil::deserialize($model->plentaJobsBasicLocations);
            if (!\is_array($moduleLocations)) {
                $moduleLocations = [];
            }
            $jobOffers = $jobOfferRepository->findAllPublishedByTypesAndLocation([], $moduleLocations);

            foreach ($jobOffers as $jobOffer) {
                $this->collectEmploymenttypes($jobOffer->getEmploymentType());
                $this->collectLocations($jobOffer, $model);
                $this->offers[] = $jobOffer;
            }
        }

        return $this->offers;
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

    public function collectLocations(?TlPlentaJobsBasicOffer $jobOffer, $model): void
    {
        $locations = StringUtil::deserialize($jobOffer->getJobLocation());
        $addedLocations = [];
        if (\is_array($locations)) {
            foreach ($locations as $locationId) {
                /** @var TlPlentaJobsBasicJobLocation $location */
                $location = $this->getAllLocations($model)[(int) $locationId] ?? null;

                if (null === $location) {
                    continue;
                }

                $name = 'onPremise' === $location->getJobTypeLocation() ? $location->getAddressLocality() : $GLOBALS['TL_LANG']['MSC']['PLENTA_JOBS']['remote'];

                if (\in_array($name, $addedLocations, true)) {
                    continue;
                }

                if (\array_key_exists($name, $this->counterLocation)) {
                    ++$this->counterLocation[$name];
                } else {
                    $this->counterLocation[$name] = 1;
                }

                $addedLocations[] = $name;
            }
        }
    }

    public function getLocations(ModuleModel $model): ?array
    {
        $this->getAllOffers($model);

        $options = [];

        foreach ($this->getAllLocations($model) as $k) {
            $name = 'onPremise' === $k->getJobTypeLocation() ? $k->getAddressLocality() : $GLOBALS['TL_LANG']['MSC']['PLENTA_JOBS']['remote'];
            if (!$model->plentaJobsBasicShowAllLocations && !\array_key_exists($name, $this->counterLocation)) {
                continue;
            }
            if (\array_key_exists($name, $options)) {
                $options[$name] = $options[$name].'|'.$k->getId();
            } else {
                $options[$name] = $k->getId();
            }
        }

        $options = array_flip($options);

        foreach ($options as $key => $option) {
            $options[$key] = $option.$this->addLocationCounter($model, $option);
        }

        return $options;
    }

    public function getAllLocations($model): array
    {
        if (empty($this->locations)) {
            $locationsRepository = $this->registry->getRepository(TlPlentaJobsBasicJobLocation::class);
            $moduleLocations = StringUtil::deserialize($model->plentaJobsBasicLocations);
            if (!\is_array($moduleLocations) || empty($moduleLocations)) {
                $locations = $locationsRepository->findAll();
            } else {
                $locations = $locationsRepository->findBy(['id' => $moduleLocations]);
            }

            foreach ($locations as $location) {
                $this->locations[$location->getId()] = $location;
            }
        }

        return $this->locations;
    }

    public function getHeadlineHtml(string $content, string $type): string
    {
        if (empty($content)) {
            return '';
        }

        $return = '<div class="plenta_jobs_basic_filter_widget_headline '.$type.'">';
        $return .= Controller::replaceInsertTags($content);
        $return .= '</div>';

        return $return;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $form = new HasteForm('plenta_jobs_basic_filter_'.$model->id, 'GET', fn ($objHaste) => false);

        if (0 !== (int) $model->jumpTo) {
            $form->setFormActionFromPageId($model->jumpTo);
        }

        if ($model->plentaJobsBasicShowTypes) {
            $form->addFormField('typesHeadline', [
                'inputType' => 'html',
                'eval' => [
                    'html' => $this->getHeadlineHtml($model->plentaJobsBasicTypesHeadline, 'jobTypes'),
                ],
            ]);

            $form->addFormField('types', [
                'inputType' => 'checkbox',
                'default' => $request->get('types'),
                'options' => $this->getTypes($model),
                'eval' => ['multiple' => true],
            ]);
        }

        if ($model->plentaJobsBasicShowLocations) {
            $form->addFormField('locationHeadline', [
                'inputType' => 'html',
                'eval' => [
                    'html' => $this->getHeadlineHtml($model->plentaJobsBasicLocationsHeadline, 'jobLocation'),
                ],
            ]);

            $form->addFormField('location', [
                'inputType' => 'checkbox',
                'default' => $request->get('location'),
                'options' => $this->getLocations($model),
                'eval' => ['multiple' => true],
            ]);
        }

        if ($model->plentaJobsBasicShowButton) {
            $form->addFormField('submit', [
                'label' => $model->plentaJobsBasicSubmit,
                'inputType' => 'submit',
            ]);
        }

        $template->form = $form->generate();
        $template->local = $request->getLocale();
        $template->ajaxRoute = $this->router->getRouteCollection()->get('plenta_jobs_basic.offer_filter')->getPath();

        return $template->getResponse();
    }
}
