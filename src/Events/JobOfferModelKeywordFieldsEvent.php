<?php

declare(strict_types=1);

/*
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2026, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Events;

use Symfony\Contracts\EventDispatcher\Event;

class JobOfferModelKeywordFieldsEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.job_offer.model.keyword_fields';

    /**
     * @param array<string> $fields
     */
    public function __construct(protected array $fields)
    {
    }

    /**
     * @return array<string>
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array<string> $fields
     */
    public function setFields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }
}
