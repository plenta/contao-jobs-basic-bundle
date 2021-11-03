<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Helper;

use Contao\Date;
use Contao\StringUtil;
use Doctrine\Persistence\ManagerRegistry;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicJobLocation;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer;

class MetaFieldsHelper
{
    protected EmploymentType $employmentTypeHelper;

    protected ManagerRegistry $registry;

    public function __construct(
        EmploymentType $employmentTypeHelper,
        ManagerRegistry $registry
    ) {
        $this->employmentTypeHelper = $employmentTypeHelper;
        $this->registry = $registry;
    }

    public function getMetaFields(TlPlentaJobsBasicOffer $jobOffer): array
    {
        global $objPage;

        $metaFields = [];

        $metaFields['publicationDateFormatted'] = Date::parse($objPage->dateFormat, $jobOffer->getDatePosted());
        $metaFields['employmentTypeFormatted'] = $this->employmentTypeHelper->getEmploymentTypesFormatted($jobOffer->getEmploymentType());
        $metaFields['locationFormatted'] = $this->formatLocation($jobOffer);
        $metaFields['addressLocalityFormatted'] = $this->formatAddressLocality($jobOffer);

        return $metaFields;
    }

    public function formatLocation(TlPlentaJobsBasicOffer $jobOffer): string
    {
        return $this->formatAddressLocality($jobOffer);
    }

    public function formatAddressLocality(TlPlentaJobsBasicOffer $jobOffer): string
    {
        $locations = StringUtil::deserialize($jobOffer->getJobLocation());
        $locationRepository = $this->registry->getRepository(TlPlentaJobsBasicJobLocation::class);

        $locationsTemp = [];

        foreach ($locations as $location) {
            $locationEntity = $locationRepository->find($location);
            $locationsTemp[] = $locationEntity->getAddressLocality();
        }

        return implode(', ', $locationsTemp);
    }
}
