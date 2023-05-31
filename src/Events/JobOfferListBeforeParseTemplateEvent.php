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
use Plenta\ContaoJobsBasic\Controller\Contao\FrontendModule\JobOfferListController;
use Symfony\Contracts\EventDispatcher\Event;

class JobOfferListBeforeParseTemplateEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.job_offer_list.before_parse_template';

    private Template $template;

    private $jobOffers;

    private JobOfferListController $objModule;

    private ModuleModel $model;

    public function __construct($jobOffers, Template $template, ModuleModel $model, JobOfferListController $objModule)
    {
        $this->jobOffers = $jobOffers;
        $this->template = $template;
        $this->model = $model;
        $this->objModule = $objModule;
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
     * @return JobOfferListBeforeParseTemplateEvent
     */
    public function setTemplate(Template $template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getJobOffers()
    {
        return $this->jobOffers;
    }

    /**
     * @param mixed $jobOffers
     *
     * @return JobOfferListBeforeParseTemplateEvent
     */
    public function setJobOffers($jobOffers)
    {
        $this->jobOffers = $jobOffers;

        return $this;
    }

    /**
     * @return JobOfferListController
     */
    public function getObjModule(): JobOfferListController
    {
        return $this->objModule;
    }

    /**
     * @param JobOfferListController $objModule
     *
     * @return JobOfferListBeforeParseTemplateEvent
     */
    public function setObjModule(JobOfferListController $objModule): self
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
     * @return JobOfferListBeforeParseTemplateEvent
     */
    public function setModel(ModuleModel $model): self
    {
        $this->model = $model;

        return $this;
    }
}
