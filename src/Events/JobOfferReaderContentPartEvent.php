<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Events;

use Contao\ModuleModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class JobOfferReaderContentPartEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.job_offer_reader.content_part';

    private PlentaJobsBasicOfferModel $jobOffer;

    private ModuleModel $model;

    private Request $request;

    private string $contentResponse = '';

    private string $part = '';

    public function getJobOffer(): PlentaJobsBasicOfferModel
    {
        return $this->jobOffer;
    }

    public function setJobOffer(PlentaJobsBasicOfferModel $jobOffer): self
    {
        $this->jobOffer = $jobOffer;

        return $this;
    }

    public function getModel(): ModuleModel
    {
        return $this->model;
    }

    public function setModel(ModuleModel $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getContentResponse(): string
    {
        return $this->contentResponse;
    }

    public function setContentResponse(string $contentResponse): self
    {
        $this->contentResponse = $contentResponse;

        return $this;
    }

    public function getPart(): string
    {
        return $this->part;
    }

    public function setPart(string $part): self
    {
        $this->part = $part;

        return $this;
    }
}
