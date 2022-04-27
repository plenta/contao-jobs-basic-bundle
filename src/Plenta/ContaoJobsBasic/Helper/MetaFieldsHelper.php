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

use Contao\Controller;
use Contao\Date;
use Contao\StringUtil;
use Contao\System;
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
        $metaFields = [];

        $metaFields['publicationDateFormatted'] = Date::parse(Date::getNumericDateFormat(), $jobOffer->getDatePosted());
        $metaFields['employmentTypeFormatted'] = $this->employmentTypeHelper->getEmploymentTypesFormatted($jobOffer->getEmploymentType());
        $metaFields['locationFormatted'] = $this->formatLocation($jobOffer);
        $metaFields['addressLocalityFormatted'] = $this->formatAddressLocality($jobOffer);
        $metaFields['title'] = Controller::replaceInsertTags($jobOffer->getTitle());
        $metaFields['description'] = Controller::replaceInsertTags($jobOffer->getDescription());

        return $metaFields;
    }

    public function formatLocation(TlPlentaJobsBasicOffer $jobOffer): string
    {
        return $this->formatAddressLocality($jobOffer);
    }

    public function formatAddressLocality(TlPlentaJobsBasicOffer $jobOffer): string
    {
        $locationsTemp = [];

        if ($jobOffer->isRemote()) {
            $locationsTemp[] = $GLOBALS['TL_LANG']['MSC']['PLENTA_JOBS']['remote'];
        }

        if (!$jobOffer->isRemote() || !$jobOffer->isOnlyRemote()) {
            $locations = StringUtil::deserialize($jobOffer->getJobLocation());
            $locationRepository = $this->registry->getRepository(TlPlentaJobsBasicJobLocation::class);


            foreach ($locations as $location) {
                $locationEntity = $locationRepository->find($location);
                $locationsTemp[] = $locationEntity->getAddressLocality();
            }
        }

        return implode(', ', $locationsTemp);
    }
}
