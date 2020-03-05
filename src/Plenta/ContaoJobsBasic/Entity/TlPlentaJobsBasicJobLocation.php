<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao CMS
 *
 * @copyright     Copyright (c) 2020, Christian Barkowsky & Christoph Werner
 * @author        Christian Barkowsky <https://plenta.io>
 * @author        Christoph Werner <https://plenta.io>
 * @link          https://plenta.io
 * @license       proprietary
 */

namespace Plenta\ContaoJobsBasic\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
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
