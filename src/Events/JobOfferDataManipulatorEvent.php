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

use Contao\Model;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Symfony\Contracts\EventDispatcher\Event;

class JobOfferDataManipulatorEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.job_offer_list.offer_data_manipulator';

    /**
     * @var array<string, mixed>
     */
    protected array $data;

    protected PlentaJobsBasicOfferModel $jobOffer;

    protected Model $model;

    public function getJob(): PlentaJobsBasicOfferModel
    {
        return $this->jobOffer;
    }

    public function setJob(PlentaJobsBasicOfferModel $jobOffer): self
    {
        $this->jobOffer = $jobOffer;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function setModel(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getModel(): Model
    {
        return $this->model;
    }
}
