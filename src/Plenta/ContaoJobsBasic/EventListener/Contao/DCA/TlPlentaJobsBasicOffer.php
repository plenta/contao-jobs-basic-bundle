<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\EventListener\Contao\DCA;

use Contao\DataContainer;
use Contao\StringUtil;
use Doctrine\Persistence\ManagerRegistry;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicJobLocation;
use Plenta\ContaoJobsBasic\Helper\EmploymentType;

class TlPlentaJobsBasicOffer
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

    public function jobLocationOptionsCallback(): array
    {
        $jobLocationRepository = $this->registry->getRepository(TlPlentaJobsBasicJobLocation::class);

        $jobLocations = $jobLocationRepository->findAll();

        $return = [];
        foreach ($jobLocations as $jobLocation) {
            $return[$jobLocation->getId()] = $jobLocation->getOrganization()->getName().': '.$jobLocation->getStreetAddress();

            if ('' !== $jobLocation->getAddressLocality()) {
                $return[$jobLocation->getId()] .= ', '.$jobLocation->getAddressLocality();
            }
        }

        return $return;
    }

    public function employmentTypeOptionsCallback(): array
    {
        $employmentTypes = $this->employmentTypeHelper->getEmploymentTypes();

        $return = [];
        foreach ($employmentTypes as $employmentType) {
            $return[$employmentType] = $this->employmentTypeHelper->getEmploymentTypeName($employmentType);
        }

        return $return;
    }

    public function employmentTypeSaveCallback($value, DataContainer $dc): string
    {
        $value = StringUtil::deserialize($value);

        return json_encode($value);
    }

    public function employmentTypeLoadCallback($value, DataContainer $dc): string
    {
        if (null === $value) {
            return serialize([]);
        }

        return serialize(json_decode($value));
    }
}
