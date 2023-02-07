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

use Contao\Config;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\Environment;
use Contao\FrontendTemplate;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Template;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicJobLocationModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Events\JobOfferListAfterFormBuildEvent;
use Plenta\ContaoJobsBasic\Events\JobOfferListBeforeParseTemplateEvent;
use Plenta\ContaoJobsBasic\Form\Type\JobSortingType;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;

/**
 * @FrontendModule("plenta_jobs_basic_offer_list",
 *   category="plentaJobsBasic",
 *   template="mod_plenta_jobs_basic_offer_list",
 *   renderer="forward"
 * )
 */
class JobOfferListController extends AbstractFrontendModuleController
{
    protected MetaFieldsHelper $metaFieldsHelper;

    protected TranslatorInterface $translator;

    protected EventDispatcherInterface $eventDispatcher;

    protected TwigEnvironment $twig;

    public function __construct(
        MetaFieldsHelper $metaFieldsHelper,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        TwigEnvironment $twig
    ) {
        $this->metaFieldsHelper = $metaFieldsHelper;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
        $this->twig = $twig;
    }

    public function generateJobOfferUrl(PlentaJobsBasicOfferModel $jobOffer, ModuleModel $model): string
    {
        $objPage = $model->getRelated('jumpTo');

        if (!$objPage instanceof PageModel) {
            $url = ampersand(Environment::get('request'));
        } else {
            $params = (Config::get('useAutoItem') ? '/' : '/items/').($this->metaFieldsHelper->getMetaFields($jobOffer)['alias'] ?: $jobOffer->id);

            $url = ampersand($objPage->getFrontendUrl($params));
        }

        return $url;
    }

    /**
     * @param Template    $template
     * @param ModuleModel $model
     * @param Request     $request
     *
     * @return Response|null
     */
    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $moduleLocations = StringUtil::deserialize($model->plentaJobsBasicLocations);
        if (!\is_array($moduleLocations)) {
            $moduleLocations = [];
        }
        $moduleJobTypes = StringUtil::deserialize($model->plentaJobsBasicEmploymentTypes);
        if (!\is_array($moduleJobTypes)) {
            $moduleJobTypes = [];
        }

        $types = \is_array($request->get('types')) && !$model->plentaJobsBasicNoFilter ? $request->get('types') : [];
        $locations = \is_array($request->get('location')) && !$model->plentaJobsBasicNoFilter ? $request->get('location') : $moduleLocations;

        if (!empty($moduleLocations)) {
            $locations = array_filter($locations, function ($element) use ($moduleLocations) {
                $els = explode('|', $element);
                foreach ($els as $el) {
                    if (\in_array($el, $moduleLocations, true)) {
                        return true;
                    }
                }

                return false;
            });
            if (empty($locations)) {
                $locations = $moduleLocations;
            }
        }

        if (!empty($moduleJobTypes)) {
            $types = array_filter($types, fn ($element) => \in_array($element, $moduleJobTypes, true));
            if (empty($types)) {
                $types = $moduleJobTypes;
            }
        }

        $sortByLocation = null;
        $sortBy = $request->get('sortBy') ?? $model->plentaJobsBasicSortingDefaultField;
        $order = $request->get('order') ?? $model->plentaJobsBasicSortingDefaultDirection;

        if ($model->plentaJobsBasicShowSorting) {
            System::loadLanguageFile('tl_module');

            $formId = 'plenta_jobs_basic_sorting_'.$model->id;
            $default = $sortBy.'__'.$order;

            $fields = StringUtil::deserialize($model->plentaJobsBasicSortingFields);
            $options = [];

            foreach ($fields as $field) {
                $options[] = $field.'__ASC';
                $options[] = $field.'__DESC';
            }

            $form = $this->createForm(JobSortingType::class, null, [
                'sortingOptions' => $options,
                'attr' => [
                    'class' => 'form_'.$formId,
                ],
            ]);

            $event = new JobOfferListAfterFormBuildEvent();
            $event->setForm($form);

            $this->eventDispatcher->dispatch($event, $event::NAME);

            $form = $event->getForm();

            $template->sortingForm = $this->twig->render('@PlentaContaoJobsBasic/form.html.twig', ['form' => $form->createView()]);

            $template->showSorting = true;
            $template->formId = $formId;

            if ('jobLocation' === $sortBy) {
                $sortByLocation = $order;
                $sortBy = null;
                $order = null;
            }
        }

        $jobOffers = PlentaJobsBasicOfferModel::findAllPublishedByTypesAndLocation($types, $locations, $sortBy, $order);

        if (null !== $sortByLocation) {
            $itemParts = [];
            if (empty($locations)) {
                $locations[] = 'remote';
                foreach (PlentaJobsBasicJobLocationModel::findAll() as $location) {
                    $locations[] = (string) $location->id;
                }
            }
            $locationArr = 'DESC' === $sortByLocation ? array_reverse($locations) : $locations;
            foreach ($locationArr as $location) {
                $joinedLocations = explode('|', $location);
                foreach ($joinedLocations as $joinedLocation) {
                    $itemParts[(string) $joinedLocation] = [];
                }
            }
        }

        $items = [];

        if ($jobOffers) {
            foreach ($jobOffers as $jobOffer) {
                $itemTemplate = new FrontendTemplate('plenta_jobs_basic_offer_default');
                $itemTemplate->jobOffer = $jobOffer;
                $itemTemplate->jobOfferMeta = $this->metaFieldsHelper->getMetaFields($jobOffer, $model->imgSize);
                $itemTemplate->headlineUnit = $model->plentaJobsBasicHeadlineTag;
                $parts = StringUtil::deserialize($model->plentaJobsBasicListParts);
                if (!\is_array($parts)) {
                    $parts = [];
                }
                $itemTemplate->parts = $parts;

                $itemTemplate->link = $this->generateJobOfferUrl($jobOffer, $model);

                if (null !== $sortByLocation) {
                    $jobLocations = StringUtil::deserialize($jobOffer->jobLocation);

                    foreach ($locationArr as $location) {
                        $joinedLocations = explode('|', $location);
                        foreach ($joinedLocations as $joinedLocation) {
                            if (\in_array((string) $joinedLocation, $jobLocations, true)) {
                                $itemParts[$location][] = $itemTemplate->parse();
                                break 2;
                            }
                        }
                    }
                } else {
                    $items[] = $itemTemplate->parse();
                }
            }
        }

        if (null !== $sortByLocation) {
            foreach ($itemParts as $part) {
                $items = array_merge($items, $part);
            }
        }

        $template->attributes = 'data-id="'.$model->id.'"';

        $template->empty = $this->translator->trans('MSC.PLENTA_JOBS.emptyList', [], 'contao_default');

        $template->items = $items;

        $event = new JobOfferListBeforeParseTemplateEvent($jobOffers, $template, $model, $this);

        $this->eventDispatcher->dispatch($event, $event::NAME);

        $template = $event->getTemplate();
        $model = $event->getModel();

        return $template->getResponse();
    }
}
