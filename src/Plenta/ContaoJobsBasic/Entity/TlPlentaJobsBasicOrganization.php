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
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected string $name = '';

    /**
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected string $sameAs;

    /**
     * @ORM\Column(type="string", length=16, nullable=true,  options={"default": null})
     */
    protected string $logoUUID;

    /**
     * @var @ORM\OneToMany(targetEntity="Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicJobLocation", mappedBy="pid")
     */
    protected $jobLocation;
}
