<?php

namespace Plenta\ContaoJobsBasic\Events;

use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Symfony\Contracts\EventDispatcher\Event;

class JobOfferObjectEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.job_offer.object';
    
    public function __construct(protected PlentaJobsBasicOfferModel|null $jobOffer)
    {
    }

    public function getJobOffer(): ?PlentaJobsBasicOfferModel
    {
        return $this->jobOffer;
    }

    public function setJobOffer(?PlentaJobsBasicOfferModel $jobOffer): JobOfferObjectEvent
    {
        $this->jobOffer = $jobOffer;
        return $this;
    }
}