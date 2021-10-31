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
 * @ORM\Entity
 * @ORM\Table(name="tl_plenta_jobs_basic_job_location")
 */
class TlPlentaJobsBasicJobLocation extends DCADefault
{
    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOrganization", inversedBy="jobLocation")
     */
    protected $pid;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected $streetAddress;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected $addressLocality;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected $addressRegion;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, options={"default": ""})
     */
    protected $postalCode;

    /**
     * @var string
     * @ORM\Column(type="string", length=2, options={"default": ""})
     */
    protected $addressCountry;
}
