<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao CMS
 *
 * @copyright     Copyright (c) 2020, Plenta. Digital solutions
 * @author        Plenta. Digital solutions <https://plenta.io>
 * @author        Christian Barkowsky <https://plenta.io>
 * @author        Christoph Werner <https://plenta.io>
 * @link          https://plenta.io
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
