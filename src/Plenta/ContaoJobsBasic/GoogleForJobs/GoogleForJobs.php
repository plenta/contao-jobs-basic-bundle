<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\GoogleForJobs;

use Contao\CoreBundle\Asset\ContaoContext;
use Contao\CoreBundle\Image\PictureFactory;
use Contao\Environment;
use Contao\FilesModel;
use Contao\Image\PictureConfiguration;
use Contao\Image\PictureConfigurationItem;
use Contao\Image\ResizeConfiguration;
use Contao\StringUtil;
use Contao\System;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicJobLocationModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOrganizationModel;
use Plenta\ContaoJobsBasic\Helper\EmploymentType;
use Plenta\ContaoJobsBasic\Helper\NumberHelper;

class GoogleForJobs
{
    public const ALLOWED_TYPES = ['Country'/*, 'State', 'City', 'SchoolDistrict'*/]; // Google Search Console Validator allows only Country atm

    protected PictureFactory $pictureFactory;
    protected ContaoContext $contaoFileContext;
    protected EmploymentType $employmentTypeHelper;

    protected string $projectDir;

    public function __construct(
        PictureFactory $pictureFactory,
        ContaoContext $contaoFileContext,
        EmploymentType $employmentTypeHelper,
        string $projectDir
    ) {
        $this->pictureFactory = $pictureFactory;
        $this->contaoFileContext = $contaoFileContext;
        $this->employmentTypeHelper = $employmentTypeHelper;
        $this->projectDir = $projectDir;

        // contao.string.html_decoder
    }

    public function generateStructuredData(?PlentaJobsBasicOfferModel $jobOffer): ?string
    {
        if (false === $this->checkPrerequisites($jobOffer)) {
            return null;
        }

        $arrStructuredData['@context'] = 'https://schema.org';
        $arrStructuredData['@type'] = 'JobPosting';
        $arrStructuredData['title'] = strip_tags(StringUtil::restoreBasicEntities($jobOffer->title));
        $arrStructuredData['datePosted'] = date('c', (int) $jobOffer->datePosted);

        if (!empty($jobOffer->validThrough)) {
            $arrStructuredData['validThrough'] = date('Y-m-d\TH:i:sP', (int) $jobOffer->validThrough);
        }

        if (null !== ($description = $this->sanitizeDescription($jobOffer->description))) {
            $arrStructuredData['description'] = $description;
        }

        $employmentType = $this->employmentTypeHelper->getMappedEmploymentTypesForGoogleForJobs(json_decode($jobOffer->employmentType, true));

        if (null !== $employmentType) {
            if (1 === \count($employmentType)) {
                $arrStructuredData['employmentType'] = $employmentType[0];
            } else {
                $arrStructuredData['employmentType'] = $employmentType;
            }
        }

        $arrStructuredData['directApply'] = (bool) $jobOffer->directApply;

        $arrStructuredData = $this->generateJobLocation($jobOffer, $arrStructuredData);
        $arrStructuredData = $this->generateHiringOrganization($jobOffer, $arrStructuredData);
        $arrStructuredData = $this->generateSalary($jobOffer, $arrStructuredData);

        $json = json_encode($arrStructuredData);

        if (false === $json) {
            return null;
        }

        return '<script type="application/ld+json">'.$json.'</script>';
    }

    public function generateHiringOrganization(PlentaJobsBasicOfferModel $jobOffer, array $structuredData): array
    {
        $jobLocationIds = $jobOffer->jobLocation;

        if (null !== $jobLocationIds) {
            $jobLocations = StringUtil::deserialize($jobLocationIds);

            $jobLocation = PlentaJobsBasicJobLocationModel::findByPk($jobLocations[0]);
            $hiringOrganization = $jobLocation->getRelated('pid');

            $structuredData['hiringOrganization'] = [];
            $structuredData['hiringOrganization']['@type'] = 'Organization';

            $structuredData['hiringOrganization']['name'] = StringUtil::restoreBasicEntities($hiringOrganization->name);

            if ('' !== $hiringOrganization->sameAs) {
                $sameAs = $hiringOrganization->sameAs;

                if ('http' != substr($sameAs, 0, 4)) {
                    $sameAs = 'https://'.$sameAs;
                }

                $structuredData['hiringOrganization']['sameAs'] = $sameAs;
            }

            $structuredData = $this->generateLogo($hiringOrganization, $structuredData);
        }

        return $structuredData;
    }

    public function generateLogo(PlentaJobsBasicOrganizationModel $hiringOrganization, array $structuredData): array
    {
        $uuid = $hiringOrganization->logo;

        if (null !== $uuid && '' !== $uuid) {
            $image = FilesModel::findByUuid($uuid);
            $staticUrl = $this->contaoFileContext->getStaticUrl();

            $imageConfigItem = new PictureConfigurationItem();
            $resizeConfig = new ResizeConfiguration();
            $pictureConfiguration = new PictureConfiguration();

            // Set sizes
            $resizeConfig->setWidth(700);
            $resizeConfig->setHeight(700);
            $resizeConfig->setZoomLevel(100);
            $resizeConfig->setMode(ResizeConfiguration::MODE_PROPORTIONAL);
            $pictureConfiguration->setSize($imageConfigItem->setResizeConfig($resizeConfig));

            // Create Contao picture factory object
            $picture = $this->pictureFactory->create(
                $this->projectDir.'/'.$image->path,
                $pictureConfiguration
            );

            $imgSrc = $picture->getImg($this->projectDir, $staticUrl)['src'];

            $structuredData['hiringOrganization']['logo'] = Environment::get('url').'/'.$imgSrc;
        }

        return $structuredData;
    }

    public function generateJobLocation(PlentaJobsBasicOfferModel $jobOffer, array $structuredData): array
    {
        $jobLocationIds = $jobOffer->jobLocation;

        $structuredDataTemp = [];

        if (null !== $jobLocationIds) {
            $jobLocations = StringUtil::deserialize($jobLocationIds);

            foreach ($jobLocations as $jobLocationId) {
                $jobLocation = PlentaJobsBasicJobLocationModel::findByPk($jobLocationId);
                if ('onPremise' === $jobLocation->jobTypeLocation) {
                    $jobLocationTemp = [];
                    $jobLocationTemp['@type'] = 'Place';
                    $jobLocationTemp['address'] = [];
                    $jobLocationTemp['address']['@type'] = 'PostalAddress';

                    if ('' !== $jobLocation->streetAddress) {
                        $jobLocationTemp['address']['streetAddress'] = $jobLocation->streetAddress;
                    }

                    if ('' !== $jobLocation->addressLocality) {
                        $jobLocationTemp['address']['addressLocality'] = $jobLocation->addressLocality;
                    }

                    if ('' !== $jobLocation->postalCode) {
                        $jobLocationTemp['address']['postalCode'] = $jobLocation->postalCode;
                    }

                    if ('' !== $jobLocation->addressRegion) {
                        $jobLocationTemp['address']['addressRegion'] = $jobLocation->addressRegion;
                    }

                    if ('' !== $jobLocation->addressCountry) {
                        $jobLocationTemp['address']['addressCountry'] = $jobLocation->addressCountry;
                    }

                    $structuredDataTemp[] = $jobLocationTemp;
                } else {
                    $structuredData['jobLocationType'] = 'TELECOMMUTE';

                    $structuredDataTempRequirements = $structuredData['applicantLocationRequirements'] ?? [];

                    $structuredDataTempRequirements[] = [
                        '@type' => $jobLocation->requirementType,
                        'name' => $jobLocation->requirementValue,
                    ];

                    $structuredData['applicantLocationRequirements'] = $structuredDataTempRequirements;
                }
            }
        }

        if (1 === \count($structuredDataTemp)) {
            $structuredData['jobLocation'] = $structuredDataTemp[0];
        } elseif (1 < \count($structuredDataTemp)) {
            $structuredData['jobLocation'] = $structuredDataTemp;
        }

        return $structuredData;
    }

    public function checkPrerequisites(?PlentaJobsBasicOfferModel $jobsOffer): bool
    {
        if (null === $jobsOffer) {
            return false;
        }

        return true;
    }

    public function sanitizeDescription(?string $description): ?string
    {
        if (null === $description) {
            return null;
        }

        $description = strip_tags($description, '<br><p><ol><ul><li><h1><h2><h3><h4><h5><strong><em>');
        $description = StringUtil::stripInsertTags($description);

        return StringUtil::restoreBasicEntities($description);
    }

    public function generateSalary(PlentaJobsBasicOfferModel $jobOffer, array $structuredData)
    {
        $numberHelper = new NumberHelper($jobOffer->salaryCurrency, 'en');

        if ($jobOffer->addSalary) {
            $structuredDataTemp = [
                '@type' => 'MonetaryAmount',
                'currency' => $jobOffer->salaryCurrency,
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'unitText' => $jobOffer->salaryUnit,
                ],
            ];

            if ($jobOffer->salaryMaxValue > 0 && $jobOffer->salaryValue > 0) {
                $structuredDataTemp['value']['minValue'] = $numberHelper->formatNumberFromDbForDCAField((string) $jobOffer->salaryValue);
                $structuredDataTemp['value']['maxValue'] = $numberHelper->formatNumberFromDbForDCAField((string) $jobOffer->salaryMaxValue);
            } else {
                $structuredDataTemp['value']['value'] = $numberHelper->formatNumberFromDbForDCAField((string) max($jobOffer->salaryMaxValue, $jobOffer->salaryValue));
            }

            $structuredData['baseSalary'] = $structuredDataTemp;
        }

        return $structuredData;
    }
}
