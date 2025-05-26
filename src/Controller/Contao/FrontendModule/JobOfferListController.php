<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2023-2025, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Controller\Contao\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Environment;
use Contao\Input;
use Contao\LayoutModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Pagination;
use Contao\StringUtil;
use Contao\System;
use Contao\Template;
use Contao\ThemeModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicJobLocationModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Events\JobOfferListAfterFormBuildEvent;
use Plenta\ContaoJobsBasic\Events\JobOfferListBeforeParseTemplateEvent;
use Plenta\ContaoJobsBasic\Events\JobOfferDataManipulatorEvent;
use Plenta\ContaoJobsBasic\Form\Type\JobSortingType;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;

#[AsFrontendModule(type: 'plenta_jobs_basic_offer_list', category: 'plentaJobsBasic')]
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
        $objPage = PageModel::findPublishedById($model->jumpTo);

        if (!$objPage instanceof PageModel) {
            $url = StringUtil::ampersand(Environment::get('request'));
        } else {
            $params = '/'.($this->metaFieldsHelper->getMetaFields($jobOffer)['alias'] ?: $jobOffer->id);

            $url = StringUtil::ampersand($objPage->getFrontendUrl($params));
        }

        return $url;
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        global $objPage;

        if (!$objPage) {
            $objPage = PageModel::findWithDetails(Input::get('page'));
            if ($layout = LayoutModel::findByPk($objPage->layout)) {
                $theme = ThemeModel::findByPk($layout->pid);
                $objPage->templateGroup = ($theme ? $theme->templates : null);
            }
        }

        $moduleLocations = StringUtil::deserialize($model->plentaJobsBasicLocations);
        if (!\is_array($moduleLocations)) {
            $moduleLocations = [];
        }

        if (empty($moduleLocations) && !empty($model->plentaJobsBasicCompanies)) {
            $locations = PlentaJobsBasicJobLocationModel::findByMultiplePids(StringUtil::deserialize($model->plentaJobsBasicCompanies, true));

            foreach ($locations as $location) {
                $moduleLocations[] = $location->id;
            }
        }

        $moduleJobTypes = StringUtil::deserialize($model->plentaJobsBasicEmploymentTypes);
        if (!\is_array($moduleJobTypes)) {
            $moduleJobTypes = [];
        }

        $types = \is_array($request->get('types')) && !$model->plentaJobsBasicNoFilter ? $request->get('types') : [];
        $locations = \is_array($request->get('location')) && !$model->plentaJobsBasicNoFilter ? $request->get('location') : (!empty($request->get('location')) && !$model->plentaJobsBasicNoFilter ? [$request->get('location')] : $moduleLocations);

        if (!empty($moduleLocations)) {
            $locations = array_filter($locations, function ($element) use ($moduleLocations) {
                $els = explode('|', (string) $element);
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

            $template->sortingForm = $this->twig->render('@Contao/jobs_basic/plenta_jobs_basic_form.html.twig', ['form' => $form->createView()]);

            $template->showSorting = true;
            $template->formId = $formId;

            if ('jobLocation' === $sortBy) {
                $sortByLocation = $order;
                $sortBy = null;
                $order = null;
            }
        }

        $limit = 0;
        $offset = 0;

        $intTotal = PlentaJobsBasicOfferModel::countAllPublishedByTypesAndLocation($types, $locations, $model->plentaJobsBasicHideOffersWithoutTranslation, $model);

        if ($model->numberOfItems > 0) {
            $limit = $model->numberOfItems;
        }

        if ($model->perPage > 0 && (empty($limit) || $model->numberOfItems > $model->perPage)) {
            $limit = $model->perPage;
            $pageParameter = 'page_n'.$model->id;
            $page = Input::get($pageParameter) ?? 1;
            $pages = ceil($intTotal / $model->perPage);
            if ($pages && ($page > $pages || $page < 1)) {
                throw new PageNotFoundException('Page not found: '.$request->getUri());
            }
            $offset = ($page - 1) * $model->perPage;
            $pagination = new Pagination($intTotal, $model->perPage, 7, $pageParameter);
            $template->pagination = $pagination->generate();
        }

        $jobOffers = PlentaJobsBasicOfferModel::findAllPublishedByTypesAndLocation($types, $locations, (int) $limit, $offset, $sortBy, $order, $model->plentaJobsBasicHideOffersWithoutTranslation, $model);

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
                $parts = StringUtil::deserialize($model->plentaJobsBasicListParts);

                if (!\is_array($parts)) {
                    $parts = [];
                }

                $data = [
                    'jobOffer' => $jobOffer,
                    'jobOfferMeta' => $this->metaFieldsHelper->getMetaFields($jobOffer, $model->imgSize),
                    'headlineUnit' => $model->plentaJobsBasicHeadlineTag,
                    'parts' => $parts,
                    'link' => $this->generateJobOfferUrl($jobOffer, $model),
                ];

                $event = new JobOfferDataManipulatorEvent();
                $event
                    ->setJob($jobOffer)
                    ->setData($data)
                ;

                $this->eventDispatcher->dispatch($event, $event::NAME);

                $tpl = $model->plentaJobsBasicElementTpl ?: 'jobs_basic/plenta_jobs_basic_offer_default';

                $stream = $this->twig->render('@Contao/'.$tpl.'.html.twig', $event->getData());

                if (null !== $sortByLocation) {
                    $jobLocations = StringUtil::deserialize($jobOffer->jobLocation);

                    foreach ($locationArr as $location) {
                        $joinedLocations = explode('|', $location);
                        foreach ($joinedLocations as $joinedLocation) {
                            if (\in_array((string) $joinedLocation, $jobLocations, true)) {
                                $itemParts[$location][] = $stream;
                                break 2;
                            }
                        }
                    }
                } else {
                    $items[] = $stream;
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

        $this->tagResponse('contao.db.tl_plenta_jobs_basic_offer');

        return $template->getResponse();
    }
}
