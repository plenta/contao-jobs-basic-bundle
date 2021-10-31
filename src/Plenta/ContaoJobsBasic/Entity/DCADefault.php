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
 * @ORM\MappedSuperclass()
 * @ORM\HasLifecycleCallbacks()
 */
abstract class DCADefault
{
    /**
     * @ORM\Column(name="id", type="integer", options={"unsigned": true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", options={"unsigned": true, "default": 0})
     */
    protected $tstamp;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Update the entry's timestamp.
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function touch(): void
    {
        $this->timestamp = time();
    }
}
