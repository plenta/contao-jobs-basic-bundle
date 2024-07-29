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
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Routing\ResponseContext\HtmlHeadBag\HtmlHeadBag;
use Contao\CoreBundle\Routing\ResponseContext\ResponseContextAccessor;
use Contao\CoreBundle\String\HtmlDecoder;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Date;
use Contao\Environment;
use Contao\FilesModel;
use Contao\Frontend;
use Contao\FrontendTemplate;
use Contao\Input;
use Contao\ModuleModel;
use Contao\StringUtil;
use Contao\System;
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

#[AsFrontendModule(type: 'plenta_jobs_basic_offer_reader', category: 'plentaJobsBasic')]
class JobOfferReaderController extends AbstractFrontendModuleController
{

    public function __construct(
        protected MetaFieldsHelper $metaFieldsHelper,
        protected GoogleForJobs $googleForJobs,
        protected RequestStack $requestStack,
        protected EventDispatcherInterface $eventDispatcher,
        protected HtmlDecoder $htmlDecoder,
        protected ResponseContextAccessor $responseContextAccessor
    ) {
    }

    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $parts = StringUtil::deserialize($model->plentaJobsBasicTemplateParts);

        System::loadLanguageFile('tl_plenta_jobs_basic_offer');

        if (!\is_array($parts)) {
            $parts = [];
        }

        if (\in_array('backlink', $parts, true)) {
            $template->referer = 'javascript:history.go(-1)';
            $template->back = $GLOBALS['TL_LANG']['MSC']['goBack'];
        }

        $alias = Input::get('auto_item');

        $jobOffer = PlentaJobsBasicOfferModel::findPublishedByIdOrAlias($alias);

        if (null === $jobOffer) {
            throw new PageNotFoundException('Page not found: '.Environment::get('uri'));
        }

        $parentId = $jobOffer->id;

        // Fill the template with data from the parent record
        $template->jobOffer = $jobOffer;
        $template->jobOfferMeta = $metaFields = $this->metaFieldsHelper->getMetaFields($jobOffer, $model->imgSize);

        $responseContext = $this->responseContextAccessor
            ->getResponseContext();

        if ($responseContext && $responseContext->has(HtmlHeadBag::class)) {
            $htmlHeadBag = $responseContext->get(HtmlHeadBag::class);
            $translation = $jobOffer->getTranslation($request->getLocale());

            $this->setMetaTitle($jobOffer, $htmlHeadBag, $translation);
            $this->setMetaDescription($jobOffer, $htmlHeadBag, $translation);

            if ($jobOffer->robots) {
                $htmlHeadBag->setMetaRobots($jobOffer->robots);
            }
        }

        $this->buildCanonical($request, $jobOffer);

        $content = '';

        if (\in_array('title', $parts, true)) {
            $template->headline = ['text' => StringUtil::stripInsertTags($metaFields['title']), 'tag_name' => $model->plentaJobsBasicHeadlineTag];
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
                ->setContentResponse($tempContent ?? '')
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
                $template->image = $image;
                $template->imgSize = $model->imgSize;
                $options = [];

                if ($jobOffer->overwriteMeta) {
                    global $objPage;

                    $arrMeta = Frontend::getMetaData($image->meta, $objPage->language);
                    $meta = [
                        'alt' => $jobOffer->alt ?: $arrMeta['alt'],
                        'imageTitle' => $jobOffer->imageTitle ?: $arrMeta['title'],
                        'imageUrl' => $jobOffer->imageUrl ?: $arrMeta['link'],
                        'caption' => $jobOffer->caption ?: $arrMeta['caption'],
                    ];
                    $options['metadata'] = $meta;
                    $template->overwriteMeta = true;
                }

                $template->options = $options;

                return $template->parse();
            }
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
                        $image = FilesModel::findByUuid($organization->logo);
                        if ($image) {
                            $imgTpl = new FrontendTemplate('plenta_jobs_basic_reader_image');
                            $imgSize = [200, 200, 'proportional'];
                            $imgTpl->image = $image;
                            $imgTpl->imgSize = $imgSize;
                            $imgs[$organization->id] = $imgTpl->parse();
                        }
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
        ?array $translation
    ): void {
        if ($jobOffer->pageTitle || (null !== $translation)) {
            if ($jobOffer->pageTitle) {
                $htmlHeadBag->setTitle($this->htmlDecoder->inputEncodedToPlainText($jobOffer->pageTitle));
            }

            if (null !== $translation) {
                if ($translation['title']) {
                    $htmlHeadBag->setTitle($this->htmlDecoder->inputEncodedToPlainText($translation['title']));
                }

                if ($translation['pageTitle']) {
                    $htmlHeadBag->setTitle($this->htmlDecoder->inputEncodedToPlainText($translation['pageTitle']));
                }
            }
        } elseif ($jobOffer->title) {
            $htmlHeadBag->setTitle($this->htmlDecoder->inputEncodedToPlainText($jobOffer->title));
        }
    }

    private function setMetaDescription(
        PlentaJobsBasicOfferModel $jobOffer,
        HtmlHeadBag $htmlHeadBag,
        ?array $translation
    ): void {
        if ($jobOffer->pageDescription || (null !== $translation)) {
            if ($jobOffer->pageDescription) {
                $htmlHeadBag->setMetaDescription($this->htmlDecoder->inputEncodedToPlainText($jobOffer->pageDescription));
            }

            if (null !== $translation) {
                if ($translation['description']) {
                    $htmlHeadBag->setMetaDescription($this->htmlDecoder->inputEncodedToPlainText($translation['description']));
                }

                if ($translation['teaser']) {
                    $htmlHeadBag->setMetaDescription($this->htmlDecoder->inputEncodedToPlainText($translation['teaser']));
                }
                if ($translation['pageDescription']) {
                    $htmlHeadBag->setMetaDescription(
                        $this->htmlDecoder->inputEncodedToPlainText(
                            $translation['pageDescription']
                        )
                    );
                }
            }
        } elseif ($jobOffer->teaser || $jobOffer->description) {
            if ($jobOffer->description) {
                $htmlHeadBag->setMetaDescription($this->htmlDecoder->htmlToPlainText($jobOffer->description));
            }

            if ($jobOffer->teaser) {
                $htmlHeadBag->setMetaDescription($this->htmlDecoder->inputEncodedToPlainText($jobOffer->teaser));
            }
        }
    }
}
