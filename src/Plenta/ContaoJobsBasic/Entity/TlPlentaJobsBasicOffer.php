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
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    protected bool $published;

    /**
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected string $cssClass = '';

    /**
     * @ORM\Column(type="string", length=10, nullable=false, options={"default": ""})
     */
    protected string $validThrough;

    /**
     * @ORM\Column(type="string", length=10, nullable=false, options={"default": ""})
     */
    protected string $start;

    /**
     * @ORM\Column(type="string", length=10, nullable=false, options={"default": ""})
     */
    protected string $stop;



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
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string
     */
    public function getValidThrough(): string
    {
        return $this->validThrough;
    }

    /**
     * @param string $start
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setValidThrough(string $validThrough): self
    {
        $this->validThrough = $validThrough;

        return $this;
    }

    /**
     * @return string
     */
    public function getStart(): string
    {
        return $this->start;
    }

    /**
     * @param string $start
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setStart(string $start): self
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return string
     */
    public function getStop(): string
    {
        return $this->stop;
    }

    /**
     * @param string $stop
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setStop(string $stop): self
    {
        $this->stop = $stop;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->published;
    }

    /**
     * @param bool $published
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return string
     */
    public function getCssClass(): string
    {
        return $this->cssClass;
    }

    /**
     * @param string $cssClass
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setCssClass(string $cssClass): self
    {
        $this->cssClass = $cssClass;

        return $this;
    }
}
