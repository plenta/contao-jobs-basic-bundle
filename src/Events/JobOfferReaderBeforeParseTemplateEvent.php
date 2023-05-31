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

    private Template $template;

    private PlentaJobsBasicOfferModel $jobOffer;

    private JobOfferReaderController $objModule;

    private ModuleModel $model;

    private ?string $structuredData;

    public function __construct(PlentaJobsBasicOfferModel $jobOffer, Template $template, ModuleModel $model, JobOfferReaderController $objModule, ?string $structuredData)
    {
        $this->jobOffer = $jobOffer;
        $this->template = $template;
        $this->model = $model;
        $this->objModule = $objModule;
        $this->structuredData = $structuredData;
    }

    /**
     * @return Template
     */
    public function getTemplate(): Template
    {
        return $this->template;
    }

    /**
     * @param Template $template
     *
     * @return JobOfferReaderBeforeParseTemplateEvent
     */
    public function setTemplate(Template $template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return PlentaJobsBasicOfferModel
     */
    public function getJobOffer(): PlentaJobsBasicOfferModel
    {
        return $this->jobOffer;
    }

    /**
     * @param PlentaJobsBasicOfferModel $jobOffer
     *
     * @return JobOfferReaderBeforeParseTemplateEvent
     */
    public function setJobOffer(PlentaJobsBasicOfferModel $jobOffer): self
    {
        $this->jobOffer = $jobOffer;

        return $this;
    }

    /**
     * @return JobOfferReaderController
     */
    public function getObjModule(): JobOfferReaderController
    {
        return $this->objModule;
    }

    /**
     * @param JobOfferReaderController $objModule
     *
     * @return JobOfferReaderBeforeParseTemplateEvent
     */
    public function setObjModule(JobOfferReaderController $objModule): self
    {
        $this->objModule = $objModule;

        return $this;
    }

    /**
     * @return ModuleModel
     */
    public function getModel(): ModuleModel
    {
        return $this->model;
    }

    /**
     * @param ModuleModel $model
     *
     * @return JobOfferReaderBeforeParseTemplateEvent
     */
    public function setModel(ModuleModel $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStructuredData(): ?string
    {
        return $this->structuredData;
    }

    /**
     * @param string|null $structuredData
     *
     * @return JobOfferReaderBeforeParseTemplateEvent
     */
    public function setStructuredData(?string $structuredData): self
    {
        $this->structuredData = $structuredData;

        return $this;
    }
}
