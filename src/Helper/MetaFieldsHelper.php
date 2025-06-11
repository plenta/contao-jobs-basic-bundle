<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Helper;

use Contao\CoreBundle\File\Metadata;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\Date;
use Contao\FilesModel;
use Contao\Frontend;
use Contao\FrontendTemplate;
use Contao\StringUtil;
use Contao\System;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicJobLocationModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOrganizationModel;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Intl\Countries;

class MetaFieldsHelper
{
    public function __construct(
        protected EmploymentType $employmentTypeHelper,
        protected RequestStack $requestStack,
        protected InsertTagParser $insertTagParser
    ) {
    }

    public function getMetaFields(PlentaJobsBasicOfferModel $jobOffer, $imageSize = null): array
    {
        $metaFields = [];

        $translation = $jobOffer->getTranslation($this->requestStack->getCurrentRequest()->getLocale());

        $metaFields['publicationDateFormatted'] = Date::parse(Date::getNumericDateFormat(), $jobOffer->datePosted);
        $metaFields['employmentTypeFormatted'] = $this->employmentTypeHelper->getEmploymentTypesFormatted(json_decode($jobOffer->employmentType, true));
        $metaFields['locationFormatted'] = $this->formatLocation($jobOffer);
        $metaFields['addressLocalityFormatted'] = $this->formatAddressLocality($jobOffer);
        $metaFields['addressCountryFormatted'] = $this->formatAddressCountry($jobOffer);
        $metaFields['title'] = $this->insertTagParser->replace($translation['title'] ?? $jobOffer->title);
        $metaFields['description'] = $this->insertTagParser->replace(StringUtil::restoreBasicEntities($translation['description'] ?? $jobOffer->description));
        $metaFields['alias'] = $translation['alias'] ?? $jobOffer->alias;
        $metaFields['company'] = $this->formatCompany($jobOffer);
        $metaFields['teaser'] = $this->insertTagParser->replace($translation['teaser'] ?? $jobOffer->teaser ?? '');

        if ($jobOffer->entryDate) {
            $metaFields['entryDateFormatted'] = Date::parse(Date::getNumericDateFormat(), $jobOffer->entryDate);
        }

        if ($imageSize && $jobOffer->addImage) {
            $file = FilesModel::findByUuid(StringUtil::binToUuid($jobOffer->singleSRC));
            if ($file) {
                $tpl = new FrontendTemplate('jobs_basic_reader_parts/plenta_jobs_basic_reader_image');
                $meta = [];
                if ($jobOffer->overwriteMeta) {
                    $request = System::getContainer()->get('request_stack')->getCurrentRequest();
                    if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
                        $language = $request->getLocale();
                    } else {
                        global $objPage;
                        $language = $objPage->language;
                    }
                    $arrMeta = Frontend::getMetaData($file->meta, $language);
                    $meta = [
                        'alt' => $jobOffer->alt ?: ($arrMeta['alt'] ?? ''),
                        'imageTitle' => $jobOffer->imageTitle ?: ($arrMeta['title'] ?? ''),
                        'imageUrl' => $jobOffer->imageUrl ?: ($arrMeta['link'] ?? ''),
                        'caption' => $jobOffer->caption ?: ($arrMeta['caption'] ?? ''),
                    ];
                }
                $data = [
                    'id' => null,
                    'singleSRC' => $jobOffer->singleSRC,
                    'sortBy' => 'custom',
                    'fullsize' => false,
                    'size' => $imageSize,
                ];

                if (!empty($meta)) {
                    $data['overwriteMeta'] = true;
                    $data['alt'] = $meta['alt'] ?? '';
                    $data['imageTitle'] = $meta['imageTitle'] ?? '';
                    $data['imageUrl'] = $meta['imageUrl'] ?? '';
                    $data['caption'] = $meta['caption'] ?? '';
                }

                $tpl->data = $data;

                $metaFields['image'] = $tpl->parse();
            }
        }

        if (!isset($metaFields['image'])) {
            $metaFields['image'] = '';
        }

        return $metaFields;
    }

    public function formatLocation(PlentaJobsBasicOfferModel $jobOffer): string
    {
        return $this->formatAddressLocality($jobOffer);
    }

    public function formatAddressLocality(PlentaJobsBasicOfferModel $jobOffer): string
    {
        $locationsTemp = [];

        $locations = StringUtil::deserialize($jobOffer->jobLocation);

        foreach ($locations as $location) {
            $objLocation = PlentaJobsBasicJobLocationModel::findByPk($location);
            $name = 'onPremise' === $objLocation->jobTypeLocation ? $objLocation->addressLocality : ('Country' === $objLocation->requirementType ? $objLocation->requirementValue : null);
            if (!\in_array($name, $locationsTemp, true)) {
                $locationsTemp[] = $name;
            }
        }

        return implode(', ', $locationsTemp);
    }

    public function formatAddressCountry(PlentaJobsBasicOfferModel $jobOffer): string
    {
        $countriesTemp = [];
        $locations = StringUtil::deserialize($jobOffer->jobLocation);

        System::loadLanguageFile('countries');

        foreach ($locations as $location) {
            $objLocation = PlentaJobsBasicJobLocationModel::findByPk($location);
            $name = 'onPremise' === $objLocation->jobTypeLocation ? Countries::getName($objLocation->addressCountry) : ('Country' === $objLocation->requirementType ? $objLocation->requirementValue : null);
            if ($name && !\in_array($name, $countriesTemp, true)) {
                $countriesTemp[] = $name;
            }
        }

        return implode(', ', $countriesTemp);
    }

    public function formatCompany(PlentaJobsBasicOfferModel $jobOffer): string
    {
        $company = [];
        $locations = StringUtil::deserialize($jobOffer->jobLocation);
        foreach ($locations as $location) {
            $objLocation = PlentaJobsBasicJobLocationModel::findByPk($location);
            if (!\in_array($objLocation->pid, $company, true)) {
                $company[$objLocation->pid] = PlentaJobsBasicOrganizationModel::findByPk($objLocation->pid)->name;
            }
        }

        return implode(', ', $company);
    }
}
