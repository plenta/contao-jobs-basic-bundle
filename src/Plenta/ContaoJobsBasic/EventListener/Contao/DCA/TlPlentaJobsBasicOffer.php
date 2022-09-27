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

use Composer\InstalledVersions;
use Contao\CoreBundle\Slug\Slug;
use Contao\CoreBundle\Util\PackageUtil;
use Contao\DataContainer;
use Contao\Input;
use Contao\Message;
use Contao\StringUtil;
use Contao\System;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicJobLocation;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer as TlPlentaJobsBasicOfferEntity;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOfferTranslation;
use Plenta\ContaoJobsBasic\Helper\EmploymentType;
use Plenta\ContaoJobsBasic\Helper\NumberHelper;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment as TwigEnvironment;

class TlPlentaJobsBasicOffer
{
    protected EmploymentType $employmentTypeHelper;

    protected ManagerRegistry $registry;

    protected Slug $slugGenerator;

    protected RequestStack $requestStack;

    protected TwigEnvironment $twig;

    public function __construct(
        EmploymentType $employmentTypeHelper,
        ManagerRegistry $registry,
        Slug $slugGenerator,
        RequestStack $requestStack,
        TwigEnvironment $twig
    ) {
        $this->employmentTypeHelper = $employmentTypeHelper;
        $this->registry = $registry;
        $this->slugGenerator = $slugGenerator;
        $this->requestStack = $requestStack;
        $this->twig = $twig;
    }

    /**
     * @param mixed $varValue
     *
     * @throws Exception
     */
    public function aliasSaveCallback($varValue, DataContainer $dc): string
    {
        $jobOfferRepository = $this->registry->getRepository(TlPlentaJobsBasicOfferEntity::class);
        $jobOfferTranslationRepository = $this->registry->getRepository(TlPlentaJobsBasicOfferTranslation::class);
        if ($dc->inputName === 'alias') {
            $title = $dc->activeRecord->title;
            $aliasExists = fn (string $alias): bool => $jobOfferRepository->doesAliasExist($alias, (int) $dc->activeRecord->id);
        } else {
            $index = str_replace('translations__alias__', '', $dc->inputName);
            $title = Input::post('translations__title__'.$index);
            $lang = Input::post('translations__language__'.$index);
            $aliasExists = fn (string $alias): bool => $jobOfferRepository->doesAliasExist($alias) || $jobOfferTranslationRepository->doesAliasExist($alias, (int) $dc->activeRecord->id, $lang);
        }

        if (empty($varValue)) {
            $varValue = $this->slugGenerator->generate(
                $title,
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
                $return[$jobLocation->getId()] .= ($jobLocation->getStreetAddress() ? ', ' : '').$jobLocation->getAddressLocality();
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

    public function onShowInfoCallback(DataContainer $dc = null): void
    {
        $GLOBALS['TL_CSS'][] = 'bundles/plentacontaojobsbasic/dashboard.css';
        $info = $this->twig->render('@PlentaContaoJobsBasic/be_plenta_info.html.twig', [
            'version' => PackageUtil::getVersion('plenta/contao-jobs-basic-bundle'),
        ]);

        Message::addRaw($info);
    }

    public function getLanguages(): array
    {
        if (version_compare(InstalledVersions::getVersion('contao/core-bundle'), '4.13', '>=')) {
            return System::getContainer()->get('contao.intl.locales')->getLanguages();
        }

        return System::getLanguages();
    }

    public function labelCallback(array $row, string $label, DataContainer $dc, array $labels): string
    {
        $jobLocations = [];
        $locations = $this->jobLocationOptionsCallback();
        if ($row['isRemote']) {
            $jobLocations[] = $GLOBALS['TL_LANG']['MSC']['PLENTA_JOBS']['remote'];
        }
        $locationsArr = StringUtil::deserialize($row['jobLocation']);
        foreach ($locationsArr as $location) {
            $jobLocations[] = $locations[$location];
        }

        $jobEmploymentTypes = [];
        $employmentTypes = $this->employmentTypeOptionsCallback();
        $typesArr = StringUtil::deserialize($this->employmentTypeLoadCallback($row['employmentType'], $dc));
        foreach ($typesArr as $type) {
            $jobEmploymentTypes[] = $employmentTypes[$type];
        }

        $label = '<h2>'.$row['title'].'</h2>';
        $label .= implode(' | ', $jobLocations);
        $label .= ' | '.implode(', ', $jobEmploymentTypes);

        return $label;
    }
}
