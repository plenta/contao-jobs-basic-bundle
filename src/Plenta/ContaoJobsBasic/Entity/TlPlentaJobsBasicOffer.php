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
 * Class TlPlentaJobsBasicOffer.
 *
 * @ORM\Entity
 * @ORM\Table(name="tl_plenta_jobs_basic_offer")
 */
class TlPlentaJobsBasicOffer extends DCADefault
{
    /**
     * @ORM\JoinColumn(name="jobLocation", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicJobLocation", inversedBy="jobOffer")
     */
    protected TlPlentaJobsBasicJobLocation $jobLocation;

    /**
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected string $name = '';

    /**
     * @return TlPlentaJobsBasicJobLocation
     */
    public function getJobLocation(): TlPlentaJobsBasicJobLocation
    {
        return $this->jobLocation;
    }

    /**
     * @param TlPlentaJobsBasicJobLocation $jobLocation
     * @return TlPlentaJobsBasicOffer
     */
    public function setJobLocation(TlPlentaJobsBasicJobLocation $jobLocation): self
    {
        $this->jobLocation = $jobLocation;
        return $this;
    }
}
