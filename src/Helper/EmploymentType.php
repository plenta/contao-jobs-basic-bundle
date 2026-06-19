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

use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicSettingsEmploymentTypeModel;
use Plenta\ContaoJobsBasic\Events\EmploymentTypesEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmploymentType
{
    protected string $customEmploymentTypePrefix = 'CUSTOM_';

    /**
     * @var array<int, PlentaJobsBasicSettingsEmploymentTypeModel>|null
     */
    private array|null $customEmploymentTypes = null;

    public function __construct(
        protected TranslatorInterface $translator,
        protected RequestStack $requestStack,
        protected EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @return array<string>
     */
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

    /**
     * @return array<string>
     */
    public function getCustomEmploymentTypes(): array
    {
        $customEmploymentTypes = $this->getAndSetCustomEmploymentTypes();
        $return = [];

        if (empty($customEmploymentTypes)) {
            return $return;
        }

        foreach ($customEmploymentTypes as $customEmploymentType) {
            $return[] = $this->customEmploymentTypePrefix.$customEmploymentType->id;
        }

        return $return;
    }

    /**
     * @return array<PlentaJobsBasicSettingsEmploymentTypeModel>
     */
    public function getAndSetCustomEmploymentTypes()
    {
        if (null === $this->customEmploymentTypes) {
            $employmentTypes = [];
            $objEmploymentTypes = PlentaJobsBasicSettingsEmploymentTypeModel::findAll();
            if ($objEmploymentTypes) {
                foreach ($objEmploymentTypes as $employmentType) {
                    $employmentTypes[$employmentType->id] = $employmentType;
                }
            }
            $this->customEmploymentTypes = $employmentTypes;
        }

        return $this->customEmploymentTypes;
    }

    /**
     * @return array<string>
     */
    public function getEmploymentTypes(): array
    {
        $employmentTypes = array_merge(
            $this->getGoogleForJobsEmploymentTypes(),
            $this->getCustomEmploymentTypes(),
        );

        $customEmploymentTypesEvent = new EmploymentTypesEvent();
        $customEmploymentTypesEvent->setEmploymentTypes($employmentTypes);
        $this->eventDispatcher->dispatch($customEmploymentTypesEvent, $customEmploymentTypesEvent::NAME);

        return $customEmploymentTypesEvent->getEmploymentTypes();
    }

    public function getEmploymentTypeName(string $employmentType): string|null
    {
        if (str_starts_with($employmentType, $this->customEmploymentTypePrefix)) {
            $translation = $this->getEmploymentTypeNameFromDatabase($employmentType);
        } else {
            $translation = $this->getEmploymentTypeNameFromTranslator($employmentType);
        }

        return $translation;
    }

    public function getEmploymentTypeNameFromDatabase(string $identifier): string|null
    {
        /** @var array<PlentaJobsBasicSettingsEmploymentTypeModel> $employmentTypes */
        $employmentTypes = $this->getAndSetCustomEmploymentTypes();

        if (empty($employmentTypes)) {
            return null;
        }

        $employmentTypeId = str_replace($this->customEmploymentTypePrefix, '', $identifier);

        if (false === isset($employmentTypes[$employmentTypeId])) {
            return null;
        }

        $request = $this->requestStack->getCurrentRequest();

        $language = substr(
            $request->getLocale(),
            0,
            2,
        );

        if (
            null !== $employmentTypes[$employmentTypeId]->translation
            && false === empty($translatedTitle = json_decode($employmentTypes[$employmentTypeId]->translation, true)[$language]['title'] ?? null)
        ) {
            $translation = $translatedTitle;
        } else {
            $translation = $employmentTypes[$employmentTypeId]->title;
        }

        return $translation;
    }

    public function getEmploymentTypeNameFromTranslator(string $employmentType): string|null
    {
        $translation = $this->translator->trans(
            'MSC.PLENTA_JOBS.'.$employmentType,
            [],
            'contao_default',
        );

        if ($translation === 'MSC.PLENTA_JOBS.'.$employmentType) {
            return null;
        }

        return $translation;
    }

    /**
     * @param array<string>|null $employmentTypes
     */
    public function getEmploymentTypesFormatted(array|null $employmentTypes): string
    {
        if (null === $employmentTypes) {
            return '';
        }

        $employmentTypesTemp = [];

        foreach ($employmentTypes as $employmentType) {
            $employmentTypesTemp[] = $this->getEmploymentTypeName($employmentType);
        }

        return implode(', ', array_filter($employmentTypesTemp));
    }

    public function getCustomEmploymentTypePrefix(): string
    {
        return $this->customEmploymentTypePrefix;
    }

    /**
     * @param  array<string> $employmentTypesUnmapped
     * @return array<string>
     */
    public function getMappedEmploymentTypesForGoogleForJobs(array $employmentTypesUnmapped): array
    {
        $employmentTypes = $this->getAndSetCustomEmploymentTypes();
        $return = [];

        foreach ($employmentTypesUnmapped as $employmentTypeUnmapped) {
            if (str_starts_with($employmentTypeUnmapped, $this->customEmploymentTypePrefix)) {
                $employmentTypeId = str_replace($this->customEmploymentTypePrefix, '', $employmentTypeUnmapped);

                if (false === isset($employmentTypes[$employmentTypeId])) {
                    continue;
                }

                $return[] = $employmentTypes[$employmentTypeId]->google_for_jobs_mapping;
            } else {
                $return[] = $employmentTypeUnmapped;
            }
        }

        return array_unique($return);
    }
}
