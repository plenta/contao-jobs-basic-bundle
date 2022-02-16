<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Helper;

use Doctrine\Persistence\ManagerRegistry;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicSettingsEmploymentType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\LocaleAwareInterface;

class EmploymentType
{
    protected LocaleAwareInterface $translator;
    protected ManagerRegistry $registry;
    protected RequestStack $requestStack;
    protected string $customEmploymentTypePrefix = 'CUSTOM_';
    private ?array $customEmploymentTypes = null;

    public function __construct(
        LocaleAwareInterface $translator,
        ManagerRegistry $registry,
        RequestStack $requestStack
    ) {
        $this->translator = $translator;
        $this->registry = $registry;
        $this->requestStack = $requestStack;
    }

    public function getGoogleForJobsEmploymentTypes(): array
    {
        return [
            'FULL_TIME',
            'PART_TIME',
            'CONTRACTOR',
            'TEMPORARY',
            'INTERN',
            'VOLUNTEER',
            'PER_DIEM',
            'OTHER',
        ];
    }

    public function getCustomEmploymentTypes(): array
    {
        $customEmploymentTypes = $this->getAndSetCustomEmploymentTypes();
        $return = [];

        if (empty($customEmploymentTypes)) {
            return $return;
        }

        foreach ($customEmploymentTypes as $customEmploymentType) {
            $return[] = $this->customEmploymentTypePrefix.$customEmploymentType->getId();
        }

        return $return;
    }

    /**
     * @return TlPlentaJobsBasicSettingsEmploymentType[]
     */
    public function getAndSetCustomEmploymentTypes(): array
    {
        if (null === $this->customEmploymentTypes) {
            $employmentTypeRepository = $this->registry->getRepository(TlPlentaJobsBasicSettingsEmploymentType::class);

            $this->customEmploymentTypes = $employmentTypeRepository->findAllIndexed();
        }

        return $this->customEmploymentTypes;
    }

    /**
     * @return string[]
     */
    public function getEmploymentTypes(): array
    {
        return array_merge(
            $this->getGoogleForJobsEmploymentTypes(),
            $this->getCustomEmploymentTypes()
        );
    }

    public function getEmploymentTypeName(string $employmentType): ?string
    {
        if (0 === strpos($employmentType, $this->customEmploymentTypePrefix)) {
            $translation = $this->getEmploymentTypeNameFromDatabase($employmentType);
        } else {
            $translation = $this->getEmploymentTypeNameFromTranslator($employmentType);
        }

        return $translation;
    }

    public function getEmploymentTypeNameFromDatabase(string $identifier): ?string
    {
        /** @var TlPlentaJobsBasicSettingsEmploymentType[] $employmentTypes */
        $employmentTypes = $this->getAndSetCustomEmploymentTypes();

        if (empty($employmentTypes)) {
            return null;
        }

        $employmentTypeId = str_replace($this->customEmploymentTypePrefix, '', $identifier);

        if (false === isset($employmentTypes[$employmentTypeId])) {
            return null;
        }

        $language = substr(
            $this->requestStack->getMasterRequest()->getLocale(),
            0,
            2
        );

        if (false === empty($employmentTypes[$employmentTypeId]->getTranslation()[$language]['title'])) {
            $translation = $employmentTypes[$employmentTypeId]->getTranslation()[$language]['title'];
        } else {
            $translation = $employmentTypes[$employmentTypeId]->getTitle();
        }

        return $translation;
    }

    public function getEmploymentTypeNameFromTranslator(string $employmentType): ?string
    {
        $translation = $this->translator->trans(
            'MSC.PLENTA_JOBS.'.$employmentType,
            [],
            'contao_default'
        );

        if ($translation === 'MSC.PLENTA_JOBS.'.$employmentType) {
            return null;
        }

        return $translation;
    }

    public function getEmploymentTypesFormatted(?array $employmentTypes): string
    {
        if (null === $employmentTypes) {
            return '';
        }

        $employmentTypesTemp = [];

        foreach ($employmentTypes as $employmentType) {
            $employmentTypesTemp[] = $this->getEmploymentTypeName($employmentType);
        }

        return implode(', ', $employmentTypesTemp);
    }

    public function getCustomEmploymentTypePrefix(): string
    {
        return $this->customEmploymentTypePrefix;
    }

    public function getMappedEmploymentTypesForGoogleForJobs(array $employmentTypesUnmapped): array
    {
        $employmentTypes = $this->getAndSetCustomEmploymentTypes();
        $return = [];

        foreach ($employmentTypesUnmapped as $employmentTypeUnmapped) {
            if (0 === strpos($employmentTypeUnmapped, $this->customEmploymentTypePrefix)) {
                $employmentTypeId = str_replace($this->customEmploymentTypePrefix, '', $employmentTypeUnmapped);

                if (false === isset($employmentTypes[$employmentTypeId])) {
                    continue;
                }

                $return[] = $employmentTypes[$employmentTypeId]->getGoogleForJobsMapping();
            } else {
                $return[] = $employmentTypeUnmapped;
            }
        }

        return array_unique($return);
    }
}
