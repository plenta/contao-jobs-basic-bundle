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
     * @ORM\ManyToOne(targetEntity="Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOrganization", inversedBy="jobLocation")
     */
    protected int $pid;

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
}
