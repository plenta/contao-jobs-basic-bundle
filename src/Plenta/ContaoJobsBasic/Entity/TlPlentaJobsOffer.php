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
 * Class TlPlentaJobsOffer.
 *
 * @ORM\Entity
 * @ORM\Table(name="tl_plenta_jobs_offer")
 */
class TlPlentaJobsOffer extends DCADefault
{
    /**
     * @var string
     * @ORM\Column(type="string", length=255, options={"default": ""})
     */
    protected $name = '';
}
