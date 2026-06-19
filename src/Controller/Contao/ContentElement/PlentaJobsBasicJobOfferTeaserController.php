<?php

declare(strict_types=1);

/*
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2026, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Controller\Contao\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\FrontendTemplate;
use Contao\StringUtil;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsContentElement(category: 'plentaJobsBasic')]
class PlentaJobsBasicJobOfferTeaserController extends AbstractContentElementController
{
    /**
     * @var array<string, mixed>|null
     */
    protected array|null $metaFields = null;

    public function __construct(
        protected MetaFieldsHelper $metaFieldsHelper,
        protected TranslatorInterface $translator,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
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

            $template = new FrontendTemplate('jobs_basic/plenta_jobs_basic_job_offer_empty');
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

        $this->tagResponse('contao.db.tl_plenta_jobs_basic_offer.'.$jobOffer->id);

        return $template->getResponse();
    }
}
