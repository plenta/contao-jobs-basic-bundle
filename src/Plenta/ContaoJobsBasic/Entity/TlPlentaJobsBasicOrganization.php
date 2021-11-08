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
 * Class TlPlentaJobsBasicOrganization.
 *
 * @ORM\Entity
 * @ORM\Table(name="tl_plenta_jobs_basic_organization")
 */
class TlPlentaJobsBasicOrganization extends DCADefault
{
    /**
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected string $name = '';

    /**
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected string $sameAs;

    /**
     * @ORM\Column (type="binary_string", nullable=true, options={"default": NULL})
     */
    protected string $logoUUID;

    /**
     * @var Collection|TlPlentaJobsBasicJobLocation[]
     * @ORM\OneToMany(targetEntity="Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicJobLocation", mappedBy="organization")
     */
    protected Collection $jobLocation;

    public function __construct()
    {
        $this->jobLocation = new ArrayCollection();
    }

    /**
     * @return Collection|TlPlentaJobsBasicJobLocation[]
     */
    public function getJobLocation(): Collection
    {
        return $this->jobLocation;
    }

    /**
     * @param TlPlentaJobsBasicJobLocation $jobLocation
     *
     * @return $this
     */
    public function addJobLocation(TlPlentaJobsBasicJobLocation $jobLocation): self
    {
        $this->jobLocation->add($jobLocation);

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return TlPlentaJobsBasicOrganization
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSameAs(): string
    {
        return $this->sameAs;
    }

    /**
     * @param string $sameAs
     * @return TlPlentaJobsBasicOrganization
     */
    public function setSameAs(string $sameAs): TlPlentaJobsBasicOrganization
    {
        $this->sameAs = $sameAs;
        return $this;
    }
}
