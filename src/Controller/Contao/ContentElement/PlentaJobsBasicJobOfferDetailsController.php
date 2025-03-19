<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022-2025, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Controller\Contao\ContentElement;

use Contao\Config;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Input;
use Contao\StringUtil;
use Contao\Template;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(category: 'plentaJobsBasic')]
class PlentaJobsBasicJobOfferDetailsController extends AbstractContentElementController
{

    protected ?PlentaJobsBasicOfferModel $jobOffer = null;

    protected ?array $metaFields = null;

    public function __construct(
        protected MetaFieldsHelper $metaFieldsHelper,
        protected RequestStack $requestStack,
        protected ScopeMatcher $matcher
    ) {
    }

    public function getMetaFields(ContentModel $model): array
    {
        if (null !== $this->metaFields) {
            return $this->metaFields;
        }

        $this->metaFields = $this->metaFieldsHelper->getMetaFields($this->getJobOffer(), $model->size);

        return $this->metaFields;
    }

    public function getJobOffer($language = null): ?PlentaJobsBasicOfferModel
    {
        if (null !== $this->jobOffer) {
            return $this->jobOffer;
        }

        $alias = Input::get('auto_item');

        if (null === $alias) {
            return null;
        }

        $this->jobOffer = PlentaJobsBasicOfferModel::findPublishedByIdOrAlias($alias);

        return $this->jobOffer;
    }

    public function renderDetails(string $data, string $class): string
    {
        if (empty($data)) {
            return '';
        }

        return '<div class="'.$class.'">'.$data.'</div>';
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        if ($this->matcher->isFrontendRequest($this->requestStack->getCurrentRequest())) {
            if (null === $this->getJobOffer($request->getLocale())) {
                return new Response('');
            }

            $metaFields = $this->getMetaFields($model);
            $template->content = '';

            if (!empty($model->plenta_jobs_basic_job_offer_details)) {
                $detailsSelected = StringUtil::deserialize($model->plenta_jobs_basic_job_offer_details);

                foreach ($detailsSelected as $details) {
                    $cssClass = $details;

                    if ('description' === $details) {
                        $cssClass .= ' ce_text';
                    }

                    $template->content .= $this->renderDetails($metaFields[$details], $cssClass);
                }
            }

            $template->jobOfferMeta = $this->getMetaFields($model);
            $template->jobOffer = $this->getJobOffer();

            $this->tagResponse('contao.db.tl_plenta_jobs_basic_offer.'.$this->jobOffer->id);
        }

        return $template->getResponse();
    }
}
