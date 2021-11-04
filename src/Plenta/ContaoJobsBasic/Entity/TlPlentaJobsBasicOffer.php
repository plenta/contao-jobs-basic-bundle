<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class TlPlentaJobsBasicOffer.
 *
 * @ORM\Entity(repositoryClass="Plenta\ContaoJobsBasic\Repository\TlPlentaJobsBasicOfferRepository")
 * @ORM\Table(name="tl_plenta_jobs_basic_offer")
 */
class TlPlentaJobsBasicOffer extends DCADefault
{
    /**
     * @ORM\Column(type="text", nullable=true, options={"default": NULL})
     */
    protected ?string $jobLocation;

    /**
     * @ORM\Column(type="text", nullable=true, options={"default": NULL})
     */
    protected ?string $description;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"unsigned": true, "default": NULL})
     */
    protected ?int $datePosted;

    /**
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected string $title = '';

    /**
     * @ORM\Column (type="json", nullable=true, options={"default": NULL})
     */
    protected ?array $employmentType;

    /**
     * @ORM\Column(type="text", nullable=true, options={"default": NULL})
     */
    protected ?string $alias;

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDatePosted(): ?int
    {
        return $this->datePosted;
    }

    /**
     * @param int|null $datePosted
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setDatePosted(?int $datePosted): self
    {
        $this->datePosted = $datePosted;

        return $this;
    }

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
     * @return TlPlentaJobsBasicOffer
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getEmploymentType(): ?array
    {
        return $this->employmentType;
    }

    public function setEmploymentType(?array $employmentType): self
    {
        $this->employmentType = $employmentType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getJobLocation(): ?string
    {
        return $this->jobLocation;
    }

    /**
     * @param string|null $jobLocation
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setJobLocation(?string $jobLocation): self
    {
        $this->jobLocation = $jobLocation;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * @param string|null $alias
     * @return TlPlentaJobsBasicOffer
     */
    public function setAlias(?string $alias): TlPlentaJobsBasicOffer
    {
        $this->alias = $alias;
        return $this;
    }
}
