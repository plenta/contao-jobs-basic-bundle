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
     * @ORM\Column (type="boolean", nullable=false, options={"default": false})
     */
    protected bool $addImage;

    /**
     * @ORM\Column (type="binary", nullable=true)
     */
    protected $singleSRC;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    protected bool $isRemote;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    protected bool $isOnlyRemote;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    protected bool $hasLocationRequirements;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected string $applicantLocationRequirements;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    protected bool $addSalary;

    /**
     * @ORM\Column(type="string", length=5, nullable=false, options={"default": "EUR"})
     */
    protected string $salaryCurrency;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default": 0})
     */
    protected int $salaryValue;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"default": 0})
     */
    protected int $salaryMaxValue;

    /**
     * @ORM\Column(type="string", length=5, nullable=false, options={"default": ""})
     */
    protected string $salaryUnit;

    /**
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected string $metaTitle = '';

    /**
     * @ORM\Column(type="text", nullable=true, options={"default": NULL})
     */
    protected ?string $metaDescription;

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

    /**
     * @return bool
     */
    public function isAddImage(): bool
    {
        return $this->addImage;
    }

    /**
     * @param bool $addImage
     */
    public function setAddImage(bool $addImage): void
    {
        $this->addImage = $addImage;
    }

    /**
     * @return mixed
     */
    public function getSingleSRC()
    {
        return $this->singleSRC;
    }

    /**
     * @param mixed $singleSRC
     */
    public function setSingleSRC($singleSRC): void
    {
        $this->singleSRC = $singleSRC;
    }

    /**
     * @return bool
     */
    public function isRemote(): bool
    {
        return $this->isRemote;
    }

    /**
     * @param bool $isRemote
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setIsRemote(bool $isRemote): self
    {
        $this->isRemote = $isRemote;

        return $this;
    }

    /**
     * @return bool
     */
    public function isOnlyRemote(): bool
    {
        return $this->isOnlyRemote;
    }

    /**
     * @param bool $isOnlyRemote
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setIsOnlyRemote(bool $isOnlyRemote): self
    {
        $this->isOnlyRemote = $isOnlyRemote;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHasLocationRequirements(): bool
    {
        return $this->hasLocationRequirements;
    }

    /**
     * @param bool $hasLocationRequirements
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setHasLocationRequirements(bool $hasLocationRequirements): self
    {
        $this->hasLocationRequirements = $hasLocationRequirements;

        return $this;
    }

    /**
     * @return string
     */
    public function getApplicantLocationRequirements(): string
    {
        return $this->applicantLocationRequirements;
    }

    /**
     * @param string $applicantLocationRequirements
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setApplicantLocationRequirements(string $applicantLocationRequirements): self
    {
        $this->applicantLocationRequirements = $applicantLocationRequirements;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAddSalary(): bool
    {
        return $this->addSalary;
    }

    /**
     * @param bool $addSalary
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setAddSalary(bool $addSalary): self
    {
        $this->addSalary = $addSalary;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalaryCurrency(): string
    {
        return $this->salaryCurrency;
    }

    /**
     * @param string $salaryCurrency
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setSalaryCurrency(string $salaryCurrency): self
    {
        $this->salaryCurrency = $salaryCurrency;

        return $this;
    }

    /**
     * @return int
     */
    public function getSalaryValue(): int
    {
        return $this->salaryValue;
    }

    /**
     * @param int $salaryValue
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setSalaryValue(int $salaryValue): self
    {
        $this->salaryValue = $salaryValue;

        return $this;
    }

    /**
     * @return int
     */
    public function getSalaryMaxValue(): int
    {
        return $this->salaryMaxValue;
    }

    /**
     * @param int $salaryMaxValue
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setSalaryMaxValue(int $salaryMaxValue): self
    {
        $this->salaryMaxValue = $salaryMaxValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalaryUnit(): string
    {
        return $this->salaryUnit;
    }

    /**
     * @param string $salaryUnit
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setSalaryUnit(string $salaryUnit): self
    {
        $this->salaryUnit = $salaryUnit;

        return $this;
    }

    /**
     * @return string
     */
    public function getMetaTitle(): string
    {
        return $this->metaTitle;
    }

    /**
     * @param string $metaTitle
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setMetaTitle(string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    /**
     * @param string|null $metaDescription
     *
     * @return TlPlentaJobsBasicOffer
     */
    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }
}
