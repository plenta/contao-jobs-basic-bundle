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
use Doctrine\Persistence\ManagerRegistry;
use Haste\Form\Form;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @FrontendModule("plenta_jobs_basic_offer_list",
 *   category="plentaJobsBasic",
 *   template="mod_plenta_jobs_basic_offer_list",
 *   renderer="forward"
 * )
 */
class JobOfferListController extends AbstractFrontendModuleController
{
    protected ManagerRegistry $registry;

    protected MetaFieldsHelper $metaFieldsHelper;

    protected TranslatorInterface $translator;

    public function __construct(
        ManagerRegistry $registry,
        MetaFieldsHelper $metaFieldsHelper,
        TranslatorInterface $translator
    ) {
        $this->registry = $registry;
        $this->metaFieldsHelper = $metaFieldsHelper;
        $this->translator = $translator;
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
        $jobOfferRepository = $this->registry->getRepository(TlPlentaJobsBasicOffer::class);

        $moduleLocations = StringUtil::deserialize($model->plentaJobsBasicLocations);
        if (!\is_array($moduleLocations)) {
            $moduleLocations = [];
        }

        $types = \is_array($request->get('types')) ? $request->get('types') : [];
        $locations = \is_array($request->get('location')) ? $request->get('location') : $moduleLocations;

        if (!empty($moduleLocations)) {
            $locations = array_filter($locations, static fn ($element) => \in_array($element, $moduleLocations, true));
            if (empty($locations)) {
                $locations = $moduleLocations;
            }
        }

        if ($model->plentaJobsBasicShowSorting) {
            System::loadLanguageFile('tl_module');
            $sortBy = $request->get('sortBy') ?? $model->plentaJobsBasicSortingDefaultField;
            $order = $request->get('order') ?? $model->plentaJobsBasicSortingDefaultDirection;

            $formId = 'plenta_jobs_basic_sorting_'.$model->id;
            $default = $sortBy.'__'.$order;

            $fields = StringUtil::deserialize($model->plentaJobsBasicSortingFields);
            $options = [];

            foreach ($fields as $field) {
                $options[] = $field.'__ASC';
                $options[] = $field.'__DESC';
            }

            $form = new Form($formId, 'POST', fn ($objHaste) => false);
            $form->addFormField('sort', [
                'inputType' => 'select',
                'default' => $default,
                'options' => $options,
                'reference' => &$GLOBALS['TL_LANG']['tl_module']['plentaJobsBasicSortingFields']['fields'],
            ]);

            $template->sortingForm = $form->generate();
            $template->showSorting = true;
            $template->formId = $formId;
        } else {
            $sortBy = null;
            $order = null;
        }

        $jobOffers = $jobOfferRepository->findAllPublishedByTypesAndLocation($types, $locations, $sortBy, $order);

        $items = [];

        foreach ($jobOffers as $jobOffer) {
            $itemTemplate = new FrontendTemplate('plenta_jobs_basic_offer_default');
            $itemTemplate->jobOffer = $jobOffer;
            $itemTemplate->jobOfferMeta = $this->metaFieldsHelper->getMetaFields($jobOffer);
            $itemTemplate->headlineUnit = $model->plentaJobsBasicHeadlineTag;

            $itemTemplate->link = $this->generateJobOfferUrl($jobOffer, $model);

            $items[] = $itemTemplate->parse();
        }

        $template->attributes = 'data-id="'.$model->id.'"';

        $template->empty = $this->translator->trans('MSC.PLENTA_JOBS.emptyList', [], 'contao_default');

        $template->items = $items;

        return $template->getResponse();
    }

    public function generateJobOfferUrl(TlPlentaJobsBasicOffer $jobOffer, ModuleModel $model): string
    {
        $objPage = $model->getRelated('jumpTo');

        if (!$objPage instanceof PageModel) {
            $url = ampersand(Environment::get('request'));
        } else {
            $params = (Config::get('useAutoItem') ? '/' : '/items/').($jobOffer->getAlias() ?: $jobOffer->getId());

            $url = ampersand($objPage->getFrontendUrl($params));
        }

        return $url;
    }
}
