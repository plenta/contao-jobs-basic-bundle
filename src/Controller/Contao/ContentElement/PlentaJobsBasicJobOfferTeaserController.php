<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022-2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Controller\Contao\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\StringUtil;
use Contao\Template;
use Contao\FrontendTemplate;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsContentElement(category: 'plentaJobsBasic')]
class PlentaJobsBasicJobOfferTeaserController extends AbstractContentElementController
{
    protected $metaFields;

    public function __construct(
        protected MetaFieldsHelper $metaFieldsHelper,
        protected TranslatorInterface $translator
    ) {
    }

    public function getMetaFields(ContentModel $model, PlentaJobsBasicOfferModel $jobOffer): array
    {
        if (null !== $this->metaFields) {
            return $this->metaFields;
        }

        $this->metaFields = $this->metaFieldsHelper->getMetaFields($jobOffer, $model->size);

        return $this->metaFields;
    }

    public function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $jobOffer = PlentaJobsBasicOfferModel::findPublishedByIdOrAlias($model->plentaJobsBasicJobOffer);

        if (!$jobOffer) {
            $headlineData = StringUtil::deserialize($model->headline ?? [] ?: '', true);
            $attributesData = StringUtil::deserialize($model->cssID ?? [] ?: '', true);

            $template = new FrontendTemplate('plenta_jobs_basic_job_offer_empty');
            $template->setData([
                'type' => $this->getType(),
                'template' => $template->getName(),
                'data' => $model,
                'element_html_id' => $attributesData[0] ?? null,
                'element_css_classes' => trim($attributesData[1] ?? ''),
                'empty' => $model->plentaJobsBasicNotice ?:
                    $this->translator->trans('MSC.PLENTA_JOBS.emptyList', [], 'contao_default'),
                'headline' => [
                    'text' => $headlineData['value'] ?? '',
                    'tag_name' => $headlineData['unit'] ?? 'h1',
                ],
            ]);

            return $template->getResponse();
        }

        $template->jobOffer = $jobOffer;
        $parts = StringUtil::deserialize($model->plentaJobsBasicJobOfferTeaserParts);

        if (!\is_array($parts)) {
            $parts = [];
        }

        $template->parts = $parts;
        $template->jobOfferMeta = $this->getMetaFields($model, $jobOffer);
        $template->link = $jobOffer->getFrontendUrl($request->getLocale());

        return $template->getResponse();
    }
}
