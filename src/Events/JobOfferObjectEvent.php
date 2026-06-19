<?php

declare(strict_types=1);

namespace Plenta\ContaoJobsBasic\Events;

use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Symfony\Contracts\EventDispatcher\Event;

class JobOfferObjectEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.job_offer.object';

    public function __construct(protected PlentaJobsBasicOfferModel|null $jobOffer)
    {
    }

    public function getJobOffer(): PlentaJobsBasicOfferModel|null
    {
        return $this->jobOffer;
    }

    public function setJobOffer(PlentaJobsBasicOfferModel|null $jobOffer): self
    {
        $this->jobOffer = $jobOffer;

        return $this;
    }
}
