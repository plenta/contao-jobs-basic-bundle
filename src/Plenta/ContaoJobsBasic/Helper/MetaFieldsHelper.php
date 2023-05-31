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

use Composer\InstalledVersions;
use Contao\Controller;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\Date;
use Contao\FilesModel;
use Contao\FrontendTemplate;
use Contao\Input;
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

        $mainRequest = $this->requestStack->getMainRequest();

        $translation = $jobOffer->getTranslation($mainRequest->getLocale());

        $metaFields['publicationDateFormatted'] = Date::parse(Date::getNumericDateFormat(), $jobOffer->datePosted);
        $metaFields['employmentTypeFormatted'] = $this->employmentTypeHelper->getEmploymentTypesFormatted(json_decode($jobOffer->employmentType, true));
        $metaFields['locationFormatted'] = $this->formatLocation($jobOffer);
        $metaFields['addressLocalityFormatted'] = $this->formatAddressLocality($jobOffer);
        $metaFields['addressCountryFormatted'] = $this->formatAddressCountry($jobOffer);
        $metaFields['title'] = $this->insertTagParser->replace($translation['title'] ?? $jobOffer->title);
        $metaFields['description'] = $this->insertTagParser->replace($translation['description'] ?? $jobOffer->description);
        $metaFields['alias'] = $translation['alias'] ?? $jobOffer->alias;
        $metaFields['company'] = $this->formatCompany($jobOffer);
        $metaFields['teaser'] = $this->insertTagParser->replace($translation['teaser'] ?? $jobOffer->teaser ?? '');
        if ($imageSize && $jobOffer->addImage) {
            $file = FilesModel::findByUuid(StringUtil::binToUuid($jobOffer->singleSRC));
            if ($file) {
                $tpl = new FrontendTemplate('ce_image');
                /** @var Studio $studio */
                $studio = System::getContainer()->get('contao.image.studio');
                $figure = $studio->createFigureBuilder()->fromUuid($jobOffer->singleSRC)->setSize($imageSize)->build();
                $figure->applyLegacyTemplateData($tpl);
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
