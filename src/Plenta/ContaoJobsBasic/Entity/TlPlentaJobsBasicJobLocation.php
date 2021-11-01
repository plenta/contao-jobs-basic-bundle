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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
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
     * @var Collection|TlPlentaJobsBasicOffer[]
     * @ORM\OneToMany(targetEntity="Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer", mappedBy="jobLocation")
     */
    protected Collection $jobOffer;

    /**
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected string $streetAddress;

    /**
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected string $addressLocality;

    /**
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected string $addressRegion;

    /**
     * @ORM\Column(type="string", length=32, options={"default": ""})
     */
    protected string $postalCode;

    /**
     * @ORM\Column(type="string", length=2, options={"default": ""})
     */
    protected string $addressCountry;

    public function __construct()
    {
        $this->jobOffer = new ArrayCollection();
    }

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
     * @return Collection|TlPlentaJobsBasicOffer[]
     */
    public function getJobOffer(): Collection
    {
        return $this->jobOffer;
    }

    public function addJobOffer(TlPlentaJobsBasicOffer $jobOffer): self
    {
        $this->jobOffer->add($jobOffer);

        return $this;
    }
}
