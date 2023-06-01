<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Controller\Contao\FrontendModule;

use Contao\ArrayUtil;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use Contao\StringUtil;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicJobLocationModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Events\JobOfferFilterAfterFormBuildEvent;
use Plenta\ContaoJobsBasic\Form\Type\JobOfferFilterType;
use Plenta\ContaoJobsBasic\Helper\EmploymentType;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

#[AsFrontendModule(type: 'plenta_jobs_basic_filter', category: 'plentaJobsBasic')]
class JobOfferFilterController extends AbstractFrontendModuleController
{
    protected MetaFieldsHelper $metaFieldsHelper;
    protected EmploymentType $employmentTypeHelper;
    protected RouterInterface $router;
    protected array $counterEmploymentType = [];
    protected array $counterLocation = [];
    protected array $locations = [];
    protected array $offers = [];
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        MetaFieldsHelper $metaFieldsHelper,
        EmploymentType $employmentTypeHelper,
        RouterInterface $router,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->metaFieldsHelper = $metaFieldsHelper;
        $this->employmentTypeHelper = $employmentTypeHelper;
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
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

        if (ArrayUtil::isAssoc($employmentTypes)) {
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
        if (true === (bool) $model->plentaJobsBasicShowQuantity
            && \array_key_exists($key, $this->counterEmploymentType)
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
            $moduleLocations = StringUtil::deserialize($model->plentaJobsBasicLocations);
            $moduleEmploymentTypes = StringUtil::deserialize($model->plentaJobsBasicEmploymentTypes);
            if (!\is_array($moduleLocations)) {
                $moduleLocations = [];
            }
            if (!\is_array($moduleEmploymentTypes)) {
                $moduleEmploymentTypes = [];
            }
            $jobOffers = PlentaJobsBasicOfferModel::findAllPublishedByTypesAndLocation($moduleEmploymentTypes, $moduleLocations, 0, 0, null, null, $model->plentaJobsBasicHideOffersWithoutTranslation);

            if ($jobOffers) {
                foreach ($jobOffers as $jobOffer) {
                    $this->collectEmploymenttypes(json_decode($jobOffer->employmentType, true));
                    $this->collectLocations($jobOffer, $model);
                    $this->offers[] = $jobOffer;
                }
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

    public function collectLocations(?PlentaJobsBasicOfferModel $jobOffer, $model): void
    {
        $locations = StringUtil::deserialize($jobOffer->jobLocation);
        $addedLocations = [];
        if (\is_array($locations)) {
            foreach ($locations as $locationId) {
                /** @var PlentaJobsBasicJobLocationModel $location */
                $location = $this->getAllLocations($model)[(int) $locationId] ?? null;

                if (null === $location) {
                    continue;
                }

                $name = 'onPremise' === $location->jobTypeLocation ? $location->addressLocality : $GLOBALS['TL_LANG']['MSC']['PLENTA_JOBS']['remote'];

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
            $name = 'onPremise' === $k->jobTypeLocation ? $k->addressLocality : $GLOBALS['TL_LANG']['MSC']['PLENTA_JOBS']['remote'];
            if (!$model->plentaJobsBasicShowAllLocations && !\array_key_exists($name, $this->counterLocation)) {
                continue;
            }
            if (\array_key_exists($name, $options)) {
                $options[$name] = $options[$name].'|'.$k->id;
            } else {
                $options[$name] = $k->id;
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
            $moduleLocations = StringUtil::deserialize($model->plentaJobsBasicLocations);
            if (!\is_array($moduleLocations) || empty($moduleLocations)) {
                $locations = PlentaJobsBasicJobLocationModel::findAll();
            } else {
                $locations = PlentaJobsBasicJobLocationModel::findMultipleByIds($moduleLocations);
            }

            foreach ($locations as $location) {
                $this->locations[$location->id] = $location;
            }
        }

        return $this->locations;
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $form = $this->createForm(JobOfferFilterType::class, null, [
            'types' => $this->getTypes($model),
            'locations' => $this->getLocations($model),
            'fmd' => $model,
        ]);

        $event = new JobOfferFilterAfterFormBuildEvent();
        $event->setForm($form);

        $this->eventDispatcher->dispatch($event, $event::NAME);

        $form = $event->getForm();
        $template->form = $form;
        $template->ajaxRoute = $this->router->getRouteCollection()->get('plenta_jobs_basic.offer_filter')->getPath();
        $template->locale = $request->getLocale();

        global $objPage;
        $template->page = $objPage->id;

        return $template->getResponse();
    }
}
