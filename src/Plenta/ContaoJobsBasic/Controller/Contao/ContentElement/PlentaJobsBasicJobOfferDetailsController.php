<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Controller\Contao\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\Input;
use Contao\ModuleModel;
use Contao\StringUtil;
use Contao\Template;
use Doctrine\Persistence\ManagerRegistry;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOfferTranslation;
use Plenta\ContaoJobsBasic\Helper\MetaFieldsHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement(category="plentaJobsBasic",template="ce_plenta_jobs_basic_job_offer_details")
 */
class PlentaJobsBasicJobOfferDetailsController extends AbstractContentElementController
{
    protected ManagerRegistry $registry;

    protected MetaFieldsHelper $metaFieldsHelper;

    protected ?TlPlentaJobsBasicOffer $jobOffer = null;

    protected ?array $metaFields = null;

    public function __construct(
        ManagerRegistry $registry,
        MetaFieldsHelper $metaFieldsHelper
    ) {
        $this->registry = $registry;
        $this->metaFieldsHelper = $metaFieldsHelper;
    }

    public function getMetaFields(ContentModel $model): array
    {
        if (null !== $this->metaFields) {
            return $this->metaFields;
        }

        $this->metaFields = $this->metaFieldsHelper->getMetaFields($this->getJobOffer(), $model->size);

        return $this->metaFields;
    }

    public function getJobOffer($language = null): ?TlPlentaJobsBasicOffer
    {
        if (null !== $this->jobOffer) {
            return $this->jobOffer;
        }

        $jobOfferRepository = $this->registry->getRepository(TlPlentaJobsBasicOffer::class);
        $jobOfferTransRepository = $this->registry->getRepository(TlPlentaJobsBasicOfferTranslation::class);

        $alias = Input::get('auto_item');

        if (null === $alias) {
            return null;
        }

        if (!preg_match('/^[1-9]\d*$/', $alias)) {
            $this->jobOffer = $jobOfferRepository->findOneBy(['alias' => $alias]);
        } else {
            $this->jobOffer = $jobOfferRepository->find($alias);
        }

        if (!$this->jobOffer && $language) {
            $translation = $jobOfferTransRepository->findByAliasAndLanguage($alias, $language);
            if ($translation) {
                $this->jobOffer = $translation->getOffer();
            }
        }

        return $this->jobOffer;
    }

    public function renderDetails(string $data, string $class): string
    {
        if (empty($data)) {
            return '';
        }

        return '<div class="'.$class.'">'.$data.'</div>';
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        if ('FE' === TL_MODE) {
            if (null === $this->getJobOffer($request->getLocale())) {
                return new Response('');
            }
            $metaFields = $this->getMetaFields($model);
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
        }

        return $template->getResponse();
    }
}
