<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\EventListener\Contao\DCA;

use Contao\CoreBundle\Slug\Slug;
use Contao\DataContainer;
use Contao\Input;
use Contao\StringUtil;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicJobLocation;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer as TlPlentaJobsBasicOfferEntity;
use Plenta\ContaoJobsBasic\Helper\EmploymentType;
use Plenta\ContaoJobsBasic\Helper\NumberHelper;
use Symfony\Component\HttpFoundation\RequestStack;

class TlPlentaJobsBasicOffer
{
    protected EmploymentType $employmentTypeHelper;

    protected ManagerRegistry $registry;

    protected Slug $slugGenerator;

    protected RequestStack $requestStack;

    public function __construct(
        EmploymentType $employmentTypeHelper,
        ManagerRegistry $registry,
        Slug $slugGenerator,
        RequestStack $requestStack
    ) {
        $this->employmentTypeHelper = $employmentTypeHelper;
        $this->registry = $registry;
        $this->slugGenerator = $slugGenerator;
        $this->requestStack = $requestStack;
    }

    /**
     * @param mixed $varValue
     *
     * @throws Exception
     */
    public function aliasSaveCallback($varValue, DataContainer $dc): string
    {
        $jobOfferRepository = $this->registry->getRepository(TlPlentaJobsBasicOfferEntity::class);

        $aliasExists = fn (string $alias): bool => $jobOfferRepository->doesAliasExist($alias, (int) $dc->activeRecord->id);

        if (empty($varValue)) {
            $varValue = $this->slugGenerator->generate(
                $dc->activeRecord->title,
                [],
                $aliasExists
            );
        } elseif (preg_match('/^[1-9]\d*$/', $varValue)) {
            throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasNumeric'], $varValue));
        } elseif ($aliasExists($varValue)) {
            throw new Exception(sprintf($GLOBALS['TL_LANG']['ERR']['aliasExists'], $varValue));
        }

        return $varValue;
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

    public function saveCallbackGlobal(DataContainer $dc): void
    {
        // Front end call
        if (!$dc instanceof DataContainer) {
            return;
        }

        if (!$dc->activeRecord) {
            return;
        }

        if (null === $dc->activeRecord->datePosted && !empty(Input::post('published'))) {
            $offerRepository = $this->registry->getRepository(TlPlentaJobsBasicOfferEntity::class);
            $offer = $offerRepository->find($dc->activeRecord->id);
            $offer->setDatePosted(time());
            $this->registry->getManager()->persist($offer);
            $this->registry->getManager()->flush();
        }
    }

    public function salaryOnLoad($value, DataContainer $dc): string
    {
        $numberHelper = new NumberHelper($dc->activeRecord->salaryCurrency, $this->requestStack->getCurrentRequest()->getLocale());
        $value = $numberHelper->formatNumberFromDbForDCAField((string) $value);

        return $value;
    }

    public function salaryOnSave($value, DataContainer $dc): int
    {
        $numberHelper = new NumberHelper($dc->activeRecord->salaryCurrency, $this->requestStack->getCurrentRequest()->getLocale());
        return $numberHelper->reformatDecimalForDb($value);
    }
}
