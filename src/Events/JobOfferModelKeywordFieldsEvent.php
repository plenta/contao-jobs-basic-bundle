<?php

namespace Plenta\ContaoJobsBasic\Events;

use Symfony\Contracts\EventDispatcher\Event;

class JobOfferModelKeywordFieldsEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.job_offer.model.keyword_fields';

    public function __construct(protected array $fields)
    {
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): JobOfferModelKeywordFieldsEvent
    {
        $this->fields = $fields;
        return $this;
    }
}