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

use Contao\Template;
use Contao\StringUtil;
use Contao\ContentModel;
use Contao\FrontendTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Symfony\Contracts\Translation\TranslatorInterface;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;

/**
 * @ContentElement(category="plentaJobsBasic")
 */
class PlentaJobsBasicJobOfferTeaserController extends AbstractContentElementController
{
    protected const EMPTY_TEMPLATE = 'ce_plenta_jobs_basic_job_offer_teaser_empty';
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

    public function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        $jobOffer = PlentaJobsBasicOfferModel::findPublishedByIdOrAlias($model->plentaJobsBasicJobOffer);

        if (!$jobOffer) {
            $template = new FrontendTemplate(self::EMPTY_TEMPLATE);
            $template->class = $template->getName();
            $template->empty = $model->plentaJobsBasicNotice ?:
                $this->translator->trans('MSC.PLENTA_JOBS.emptyList', [], 'contao_default')
            ;

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
