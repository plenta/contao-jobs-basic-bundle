<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2023-2025, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Events;

use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Symfony\Contracts\EventDispatcher\Event;

class JobOfferDataManipulatorEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.job_offer_list.offer_data_manipulator';

    protected array $data;
    protected PlentaJobsBasicOfferModel $jobOffer;

    public function __construct()
    {
    }

    public function getJob(): PlentaJobsBasicOfferModel
    {
        return $this->jobOffer;
    }

    public function setJob(PlentaJobsBasicOfferModel $jobOffer): self
    {
        $this->jobOffer = $jobOffer;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}