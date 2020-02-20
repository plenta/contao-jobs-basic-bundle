<?php


namespace Plenta\Products\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class TlPlentaJobsBasicOrganization
 * @package Plenta\Products\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="tl_plenta_jobs_basic_organization")
 */
class TlPlentaJobsBasicOrganization extends DCADefault
{
    /**
     * @var string
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected $name = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected $sameAs;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=true,  options={"default": null})
     */
    protected $logoUUID;

    /**
     * @var @ORM\OneToMany(targetEntity="Plenta\Products\Entity\TlPlentaJobsBasicJobLocation", mappedBy="pid")
     */
    protected $jobLocation;
}