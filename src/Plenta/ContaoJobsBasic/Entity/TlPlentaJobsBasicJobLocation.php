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
 * @ORM\Entity(repositoryClass="Plenta\ContaoJobsBasic\Repository\TlPlentaJobsBasicJobLocationRepository")
 * @ORM\Table(name="tl_plenta_jobs_basic_job_location")
 */
class TlPlentaJobsBasicJobLocation extends DCADefault
{
    /**
     * @ORM\JoinColumn(name="pid", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOrganization", inversedBy="jobLocation")
     */
    protected TlPlentaJobsBasicOrganization $organization;

    /**
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected string $streetAddress = '';

    /**
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected string $addressLocality = '';

    /**
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected string $addressRegion = '';

    /**
     * @ORM\Column(type="string", length=32, options={"default": ""})
     */
    protected string $postalCode = '';

    /**
     * @ORM\Column(type="string", length=2, options={"default": ""})
     */
    protected string $addressCountry = '';

    /**
     * Options => ['onPremise', 'Telecommute'].
     *
     * @ORM\Column(type="string", length=32, options={"default": "onPremise"})
     */
    protected string $jobTypeLocation = 'onPremise';

    /**
     * @ORM\Column (type="binary_string", nullable=true, options={"default": NULL})
     */
    protected ?string $logo;

    /**
     * @return TlPlentaJobsBasicOrganization
     */
    public function getOrganization(): TlPlentaJobsBasicOrganization
    {
        return $this->organization;
    }

    /**
     * @param TlPlentaJobsBasicOrganization $organization
     *
     * @return TlPlentaJobsBasicJobLocation
     */
    public function setOrganization(TlPlentaJobsBasicOrganization $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return string
     */
    public function getStreetAddress(): string
    {
        return $this->streetAddress;
    }

    /**
     * @param string $streetAddress
     *
     * @return TlPlentaJobsBasicJobLocation
     */
    public function setStreetAddress(string $streetAddress): self
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressLocality(): string
    {
        return $this->addressLocality;
    }

    /**
     * @param string $addressLocality
     *
     * @return TlPlentaJobsBasicJobLocation
     */
    public function setAddressLocality(string $addressLocality): self
    {
        $this->addressLocality = $addressLocality;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressRegion(): string
    {
        return $this->addressRegion;
    }

    /**
     * @param string $addressRegion
     *
     * @return TlPlentaJobsBasicJobLocation
     */
    public function setAddressRegion(string $addressRegion): self
    {
        $this->addressRegion = $addressRegion;

        return $this;
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     *
     * @return TlPlentaJobsBasicJobLocation
     */
    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressCountry(): string
    {
        return $this->addressCountry;
    }

    /**
     * @param string $addressCountry
     *
     * @return TlPlentaJobsBasicJobLocation
     */
    public function setAddressCountry(string $addressCountry): self
    {
        $this->addressCountry = $addressCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getJobTypeLocation(): string
    {
        return $this->jobTypeLocation;
    }

    /**
     * @param string $jobTypeLocation
     *
     * @return TlPlentaJobsBasicJobLocation
     */
    public function setJobTypeLocation(string $jobTypeLocation): self
    {
        $this->jobTypeLocation = $jobTypeLocation;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLogo(): ?string
    {
        return $this->logo;
    }

    /**
     * @param string|null $singleSRC
     *
     * @return TLWuerthEyeCatcher
     */
    public function setSingleSRC(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }
}
