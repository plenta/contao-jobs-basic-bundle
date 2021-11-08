<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\GoogleForJobs;

use Contao\StringUtil;
use Doctrine\Persistence\ManagerRegistry;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicJobLocation;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer;

class GoogleForJobs
{
    protected ManagerRegistry $registry;

    public function __construct(
        ManagerRegistry $registry
    ) {
        $this->registry = $registry;
    }

    public function generateStructuredData(?TlPlentaJobsBasicOffer $jobOffer): ?string
    {
        if (false === $this->checkPrerequisites($jobOffer)) {
            return null;
        }

        $arrStructuredData['@context'] = 'https://schema.org';
        $arrStructuredData['@type'] = 'JobPosting';
        $arrStructuredData['title'] = StringUtil::restoreBasicEntities($jobOffer->getTitle());
        $arrStructuredData['datePosted'] = date('c', (int) $jobOffer->getDatePosted());

        if ($description = null !== $this->sanitizeDescription($jobOffer->getDescription())) {
            $arrStructuredData['description'] = $description;
        }

        $employmentType = $jobOffer->getEmploymentType();

        if (null !== $employmentType) {
            if (1 === \count($employmentType)) {
                $arrStructuredData['employmentType'] = $employmentType[0];
            } else {
                $arrStructuredData['employmentType'] = $employmentType;
            }
        }

        $arrStructuredData = $this->generateJobLocation($jobOffer, $arrStructuredData);
        $arrStructuredData = $this->generateHiringOrganization($jobOffer, $arrStructuredData);

        $json = json_encode($arrStructuredData);

        if (false === $json) {
            return null;
        }

        return $json;
    }

    public function generateHiringOrganization(TlPlentaJobsBasicOffer $jobOffer, array $structuredData): array
    {
        $jobLocationIds = $jobOffer->getJobLocation();
        $jobLocationRepository = $this->registry->getRepository(TlPlentaJobsBasicJobLocation::class);

        $structuredDataTemp = [];

        if (null !== $jobLocationIds) {
            $jobLocations = StringUtil::deserialize($jobLocationIds);

            $jobLocation = $jobLocationRepository->find($jobLocations[0]);
            $hiringOrganization = $jobLocation->getOrganization();

            $arrStructuredData['hiringOrganization'] = [];
            $arrStructuredData['hiringOrganization']['@type'] = 'Organization';

            $arrStructuredData['hiringOrganization']['name'] = StringUtil::restoreBasicEntities($hiringOrganization->getName());

            if ('' !== $hiringOrganization->getSameAs()) {
                $sameAs = $hiringOrganization->getSameAs();

                if ('http' != substr($sameAs, 0, 4)) {
                    $sameAs = 'https://'.$sameAs;
                }

                $arrStructuredData['hiringOrganization']['sameAs'] = $sameAs;
            }
        }

        return $structuredData;
    }

    public function generateJobLocation(TlPlentaJobsBasicOffer $jobOffer, array $structuredData): array
    {
        $jobLocationIds = $jobOffer->getJobLocation();
        $jobLocationRepository = $this->registry->getRepository(TlPlentaJobsBasicJobLocation::class);

        $structuredDataTemp = [];

        if (null !== $jobLocationIds) {
            $jobLocations = StringUtil::deserialize($jobLocationIds);

            foreach ($jobLocations as $jobLocationId) {
                $jobLocation = $jobLocationRepository->find($jobLocationId);

                $jobLocationTemp = [];
                $jobLocationTemp['@type'] = 'Place';
                $jobLocationTemp['address'] = [];
                $jobLocationTemp['address']['@type'] = 'PostalAddress';

                if ('' !== $jobLocation->getStreetAddress()) {
                    $jobLocationTemp['address']['streetAddress'] = $jobLocation->getStreetAddress();
                }

                if ('' !== $jobLocation->getAddressLocality()) {
                    $jobLocationTemp['address']['addressLocality'] = $jobLocation->getAddressLocality();
                }

                if ('' !== $jobLocation->getPostalCode()) {
                    $jobLocationTemp['address']['postalCode'] = $jobLocation->getPostalCode();
                }

                if ('' !== $jobLocation->getAddressRegion()) {
                    $jobLocationTemp['address']['addressRegion'] = $jobLocation->getAddressRegion();
                }

                if ('' !== $jobLocation->getAddressCountry()) {
                    $jobLocationTemp['address']['addressCountry'] = $jobLocation->getAddressCountry();
                }

                $structuredDataTemp[] = $jobLocationTemp;
            }
        }

        if (1 === \count($structuredDataTemp)) {
            $structuredData['jobLocation'] = $structuredDataTemp[0];
        } elseif (1 < \count($structuredDataTemp)) {
            $structuredData['jobLocation'] = $structuredDataTemp;
        }

        return $structuredData;
    }

    public function checkPrerequisites(?TlPlentaJobsBasicOffer $jobsOffer): bool
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
        $description = StringUtil::restoreBasicEntities($description);

        return $description;
    }
}
