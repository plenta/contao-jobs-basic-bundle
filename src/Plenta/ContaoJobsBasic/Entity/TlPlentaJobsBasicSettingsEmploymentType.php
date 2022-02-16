<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Plenta\ContaoJobsBasic\Repository\TlPlentaJobsBasicSettingsEmploymentTypeRepository")
 * @ORM\Table(name="tl_plenta_jobs_basic_settings_employment_type")
 */
class TlPlentaJobsBasicSettingsEmploymentType extends DCADefault
{
    /**
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected string $title = '';

    /**
     * @ORM\Column(type="string", name="google_for_jobs_mapping", length=32, options={"default": "OTHER"})
     */
    protected string $googleForJobsMapping = '';

    /**
     * @ORM\Column (type="json", nullable=true, options={"default": NULL})
     */
    protected ?array $translation;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return TlPlentaJobsBasicSettingsEmploymentType
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getGoogleForJobsMapping(): string
    {
        return $this->googleForJobsMapping;
    }

    /**
     * @param string $googleForJobsMapping
     *
     * @return TlPlentaJobsBasicSettingsEmploymentType
     */
    public function setGoogleForJobsMapping(string $googleForJobsMapping): self
    {
        $this->googleForJobsMapping = $googleForJobsMapping;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getTranslation(): ?array
    {
        return $this->translation;
    }

    /**
     * @param array|null $translation
     *
     * @return TlPlentaJobsBasicSettingsEmploymentType
     */
    public function setTranslation(?array $translation): self
    {
        $this->translation = $translation;

        return $this;
    }
}
