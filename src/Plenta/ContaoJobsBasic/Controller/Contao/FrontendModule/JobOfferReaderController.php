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
use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Routing\ResponseContext\HtmlHeadBag\HtmlHeadBag;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\CoreBundle\String\HtmlDecoder;
use Contao\Date;
use Contao\Environment;
use Contao\FilesModel;
use Contao\Frontend;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Template;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicJobLocationModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Events\JobOfferReaderBeforeParseTemplateEvent;
use Plenta\ContaoJobsBasic\Events\JobOfferReaderContentPartEvent;
use Plenta\ContaoJobsBasic\GoogleForJobs\GoogleForJobs;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Plenta\ContaoJobsBasic\Helper\NumberHelper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule("plenta_jobs_basic_offer_reader",
 *   category="plentaJobsBasic",
 *   template="mod_plenta_jobs_basic_offer_reader",
 *   renderer="forward"
 * )
 */
class JobOfferReaderController extends AbstractFrontendModuleController
{
    protected MetaFieldsHelper $metaFieldsHelper;

    protected GoogleForJobs $googleForJobs;

    protected RequestStack $requestStack;

    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        MetaFieldsHelper $metaFieldsHelper,
        GoogleForJobs $googleForJobs,
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->metaFieldsHelper = $metaFieldsHelper;
        $this->googleForJobs = $googleForJobs;
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        /* @var PageModel $objPage */
        global $objPage;

        $parts = StringUtil::deserialize($model->plentaJobsBasicTemplateParts);

        System::loadLanguageFile('tl_plenta_jobs_basic_offer');

        if (!\is_array($parts)) {
            $parts = [];
        }

        if (\in_array('backlink', $parts, true)) {
            $template->referer = 'javascript:history.go(-1)';
            $template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];
        }

        if (!isset($_GET['items']) && isset($_GET['auto_item']) && Config::get('useAutoItem')) {
            Input::setGet('items', Input::get('auto_item'));
        }
        $alias = Input::get('items');

        $jobOffer = PlentaJobsBasicOfferModel::findPublishedByIdOrAlias($alias);

        if (null === $jobOffer) {
            throw new PageNotFoundException('Page not found: '.Environment::get('uri'));
        }

        $parentId = $jobOffer->id;

        // Fill the template with data from the parent record
        $template->jobOffer = $jobOffer;
        $template->jobOfferMeta = $metaFields = $this->metaFieldsHelper->getMetaFields($jobOffer, $model->imgSize);

        $responseContext = System::getContainer()
            ->get('contao.routing.response_context_accessor')
            ->getResponseContext();

        if ($responseContext && $responseContext->has(HtmlHeadBag::class)) {
            $htmlHeadBag = $responseContext->get(HtmlHeadBag::class);
            $htmlDecoder = System::getContainer()->get('contao.string.html_decoder');
            $translation = $jobOffer->getTranslation($request->getLocale());

            $this->setMetaTitle($jobOffer, $htmlHeadBag, $htmlDecoder, $translation);
            $this->setMetaDescription($jobOffer, $htmlHeadBag, $htmlDecoder, $translation);

            if ($jobOffer->robots) {
                $htmlHeadBag->setMetaRobots($jobOffer->robots);
            }
        }

        $this->buildCanonical($request, $jobOffer);

        $content = '';

        if (\in_array('title', $parts, true)) {
            $template->headline = StringUtil::stripInsertTags($metaFields['title']);
            $template->hl = $model->plentaJobsBasicHeadlineTag;
        }

        foreach ($parts as $part) {
            $tempContent = '';
            switch ($part) {
                case 'image':
                    $tempContent = $this->getImage($jobOffer, $model);
                    break;
                case 'elements':
                    $tempContent = $this->getContentElements($request, $parentId);
                    break;
                case 'description':
                    $tempContent = $this->getDescription($jobOffer);
                    break;
                case 'employmentType':
                    $tempContent = $this->getEmploymentType($jobOffer);
                    break;
                case 'validThrough':
                    $tempContent = $this->getValidThrough($jobOffer);
                    break;
                case 'salary':
                    $tempContent = $this->getSalary($jobOffer);
                    break;
                case 'jobLocation':
                    $tempContent = $this->getJobLocation($jobOffer, $model);
                    break;
            }
            $event = new JobOfferReaderContentPartEvent();
            $event
                ->setJobOffer($jobOffer)
                ->setModel($model)
                ->setRequest($request)
                ->setPart($part)
                ->setContentResponse($tempContent)
            ;

            $this->eventDispatcher->dispatch($event, $event::NAME);

            $content .= $event->getContentResponse();
        }

        $template->content = $content;

        $StructuredData = $this->googleForJobs->generatestructuredData($jobOffer);

        if ($jobOffer->cssClass) {
            $template->class .= ('' != $template->class ? ' ' : '').$jobOffer->cssClass;
        }

        $event = new JobOfferReaderBeforeParseTemplateEvent($jobOffer, $template, $model, $this, $StructuredData);

        $this->eventDispatcher->dispatch($event, $event::NAME);

        $template = $event->getTemplate();
        $model = $event->getModel();
        $StructuredData = $event->getStructuredData();

        if (null !== $StructuredData) {
            $GLOBALS['TL_BODY'][] = $StructuredData;
        }

        return $template->getResponse();
    }

    private function buildCanonical(Request $request, PlentaJobsBasicOfferModel $jobOffer): void
    {
        if (($page = $jobOffer->getReaderPage($request->getLocale())) && $page->id !== $this->getPageModel()->id) {
            $GLOBALS['TL_HEAD'][] = '<link rel="canonical" href="'.$jobOffer->getAbsoluteUrl($request->getLocale()).'" />';
        }
    }

    private function getContentElements($request, $parentId): ?string
    {
        $elements = ContentModel::findPublishedByPidAndTable($parentId, 'tl_plenta_jobs_basic_offer');

        if (null === $elements) {
            return null;
        }

        // The layout section is stored in a request attribute
        $section = $request->attributes->get('section', 'main');

        // Get the rendered content elements
        $content = '';

        foreach ($elements as $element) {
            $content .= Controller::getContentElement($element->id, $section);
        }

        return $content;
    }

    private function getImage(PlentaJobsBasicOfferModel $jobOffer, $model): ?string
    {
        if ($jobOffer->addImage) {
            $template = new FrontendTemplate('plenta_jobs_basic_reader_image');
            $template->class = 'ce_image';
            $image = FilesModel::findByUuid(StringUtil::binToUuid($jobOffer->singleSRC));

            if ($image) {
                $rowData = [
                    'singleSRC' => $image->path,
                    'size' => $model->imgSize
                ];

                if (true === (bool) $jobOffer->overwriteMeta) {
                    global $objPage;

                    $arrMeta = Frontend::getMetaData($image->meta, $objPage->language);
                    $rowData['overwriteMeta'] = true;
                    $rowData['alt'] = $jobOffer->alt ?: $arrMeta['alt'];
                    $rowData['imageTitle'] = $jobOffer->imageTitle ?: $arrMeta['title'];
                    $rowData['imageUrl'] = $jobOffer->imageUrl ?: $arrMeta['link'];
                    $rowData['caption'] = $jobOffer->caption ?: $arrMeta['caption'];
                }

                Controller::addImageToTemplate($template, $rowData, null, null, $image);
            }

            return $template->parse();
        }

        return '';
    }

    private function getDescription(PlentaJobsBasicOfferModel $jobOffer): ?string
    {
        $template = new FrontendTemplate('plenta_jobs_basic_reader_description');
        $template->text = $this->metaFieldsHelper->getMetaFields($jobOffer)['description'];
        $template->class = 'ce_text job_description';

        return $template->parse();
    }

    private function getEmploymentType(PlentaJobsBasicOfferModel $jobOffer): ?string
    {
        $template = new FrontendTemplate('plenta_jobs_basic_reader_attribute');
        $metaFields = $this->metaFieldsHelper->getMetaFields($jobOffer);
        $template->label = $GLOBALS['TL_LANG']['tl_plenta_jobs_basic_offer']['employmentType'][0];
        $template->value = $metaFields['employmentTypeFormatted'];
        $template->class = 'job_employment_type';

        return $template->parse();
    }

    private function getValidThrough(PlentaJobsBasicOfferModel $jobOffer): ?string
    {
        if ($jobOffer->validThrough) {
            $template = new FrontendTemplate('plenta_jobs_basic_reader_attribute');
            $template->label = $GLOBALS['TL_LANG']['tl_plenta_jobs_basic_offer']['validThrough'][0];
            $template->value = Date::parse(Date::getNumericDatimFormat(), $jobOffer->validThrough);
            $template->class = 'job_valid_through';

            return $template->parse();
        }

        return '';
    }

    private function getJobLocation(PlentaJobsBasicOfferModel $jobOffer, $model): ?string
    {
        $template = new FrontendTemplate('plenta_jobs_basic_reader_job_location');

        $locationsArr = StringUtil::deserialize($jobOffer->jobLocation);

        $organizations = [];
        $locationsTpl = [];
        $imgs = [];

        if (\is_array($locationsArr)) {
            $locations = PlentaJobsBasicJobLocationModel::findMultipleByIds($locationsArr);
            foreach ($locations as $location) {
                $organization = $location->getRelated('pid');
                if (!\array_key_exists($organization->id, $organizations)) {
                    if ($model->plentaJobsBasicShowLogo && $organization->logo) {
                        $imgTpl = new FrontendTemplate('ce_image');
                        $image = FilesModel::findByUuid($organization->logo);
                        Controller::addImageToTemplate($imgTpl, [
                            'singleSRC' => $image->path,
                            'size' => [200, 200, 'proportional'],
                        ]);
                        $imgs[$organization->id] = $imgTpl->parse();
                    }
                    $organizations[$organization->id] = $organization;
                    $locationsTpl[$organization->id] = [];
                }
                $locationsTpl[$organization->id][] = $location;
            }
        }

        $template->showCompanyName = $model->plentaJobsBasicShowCompany;
        $template->organizations = $organizations;
        $template->locations = $locationsTpl;
        $template->imgs = $imgs;
        $template->plentaJobsBasicHideRemoteRequirements = $model->plentaJobsBasicHideRemoteRequirements;

        return $template->parse();
    }

    private function getSalary(PlentaJobsBasicOfferModel $jobOffer)
    {
        if ($jobOffer->addSalary) {
            $numberHelper = new NumberHelper($jobOffer->salaryCurrency, $this->requestStack->getCurrentRequest()->getLocale());
            $template = new FrontendTemplate('plenta_jobs_basic_reader_salary');
            $salary = [];

            if ($jobOffer->salaryValue > 0) {
                $salary[] = $numberHelper->formatCurrency($jobOffer->salaryValue);
            }

            if ($jobOffer->salaryMaxValue > 0) {
                $salary[] = $numberHelper->formatCurrency($jobOffer->salaryMaxValue);
            }

            if (empty($salary)) {
                return '';
            }

            $template->salary = implode(' - ', $salary);
            $template->unit = $GLOBALS['TL_LANG']['tl_plenta_jobs_basic_offer']['salaryUnits'][$jobOffer->salaryUnit];

            return $template->parse();
        }

        return '';
    }

    private function setMetaTitle(
        PlentaJobsBasicOfferModel $jobOffer,
        HtmlHeadBag $htmlHeadBag,
        HtmlDecoder $htmlDecoder,
        ?array $translation
    ): void {
        if ($jobOffer->pageTitle || (null !== $translation)) {
            if ($jobOffer->pageTitle) {
                $htmlHeadBag->setTitle($htmlDecoder->inputEncodedToPlainText($jobOffer->pageTitle));
            }

            if (null !== $translation) {
                if ($translation['title']) {
                    $htmlHeadBag->setTitle($htmlDecoder->inputEncodedToPlainText($translation['title']));
                }

                if ($translation['pageTitle']) {
                    $htmlHeadBag->setTitle($htmlDecoder->inputEncodedToPlainText($translation['pageTitle']));
                }
            }
        } elseif ($jobOffer->title) {
            $htmlHeadBag->setTitle($htmlDecoder->inputEncodedToPlainText($jobOffer->title));
        }
    }

    private function setMetaDescription(
        PlentaJobsBasicOfferModel $jobOffer,
        HtmlHeadBag $htmlHeadBag,
        HtmlDecoder $htmlDecoder,
        ?array $translation
    ): void {
        if ($jobOffer->pageDescription || (null !== $translation)) {
            if ($jobOffer->pageDescription) {
                $htmlHeadBag->setMetaDescription($htmlDecoder->inputEncodedToPlainText($jobOffer->pageDescription));
            }

            if (null !== $translation) {
                if ($translation['description']) {
                    $htmlHeadBag->setMetaDescription($htmlDecoder->inputEncodedToPlainText($translation['description']));
                }

                if ($translation['teaser']) {
                    $htmlHeadBag->setMetaDescription($htmlDecoder->inputEncodedToPlainText($translation['teaser']));
                }
                if ($translation['pageDescription']) {
                    $htmlHeadBag->setMetaDescription(
                        $htmlDecoder->inputEncodedToPlainText(
                            $translation['pageDescription']
                        )
                    );
                }
            }
        } elseif ($jobOffer->teaser || $jobOffer->description) {
            if ($jobOffer->description) {
                $htmlHeadBag->setMetaDescription($htmlDecoder->htmlToPlainText($jobOffer->description));
            }

            if ($jobOffer->teaser) {
                $htmlHeadBag->setMetaDescription($htmlDecoder->inputEncodedToPlainText($jobOffer->teaser));
            }
        }
    }
}
