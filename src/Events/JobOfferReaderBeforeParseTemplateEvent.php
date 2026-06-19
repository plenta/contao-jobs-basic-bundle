<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Events;

use Contao\ModuleModel;
use Contao\Template;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Controller\Contao\FrontendModule\JobOfferReaderController;
use Symfony\Contracts\EventDispatcher\Event;

class JobOfferReaderBeforeParseTemplateEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.job_offer_reader.before_parse_template';

    private JobOfferReaderController $objModule;

    public function __construct(
        private PlentaJobsBasicOfferModel $jobOffer,
        private Template $template,
        private ModuleModel $model,
        JobOfferReaderController $objModule,
        private string|null $structuredData,
    ) {
        $this->objModule = $objModule;
    }

    public function getTemplate(): Template
    {
        return $this->template;
    }

    public function setTemplate(Template $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getJobOffer(): PlentaJobsBasicOfferModel
    {
        return $this->jobOffer;
    }

    public function setJobOffer(PlentaJobsBasicOfferModel $jobOffer): self
    {
        $this->jobOffer = $jobOffer;

        return $this;
    }

    public function getObjModule(): JobOfferReaderController
    {
        return $this->objModule;
    }

    public function setObjModule(JobOfferReaderController $objModule): self
    {
        $this->objModule = $objModule;

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

    public function getStructuredData(): string|null
    {
        return $this->structuredData;
    }

    public function setStructuredData(string|null $structuredData): self
    {
        $this->structuredData = $structuredData;

        return $this;
    }
}
