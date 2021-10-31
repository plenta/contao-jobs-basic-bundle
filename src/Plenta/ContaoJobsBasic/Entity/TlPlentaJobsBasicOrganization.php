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
 * Class TlPlentaJobsBasicOrganization.
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
     * @var @ORM\OneToMany(targetEntity="Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicJobLocation", mappedBy="pid")
     */
    protected $jobLocation;
}
