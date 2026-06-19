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

use Contao\Model\Collection;
use Contao\ModuleModel;
use Contao\Template;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Plenta\ContaoJobsBasic\Controller\Contao\FrontendModule\JobOfferListController;
use Symfony\Contracts\EventDispatcher\Event;

class JobOfferListBeforeParseTemplateEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.job_offer_list.before_parse_template';

    private JobOfferListController $objModule;

    /**
     * @param Collection<PlentaJobsBasicOfferModel>|null $jobOffers
     */
    public function __construct(
        private Collection|null $jobOffers,
        private Template $template,
        private ModuleModel $model,
        JobOfferListController $objModule,
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

    /**
     * @return Collection<PlentaJobsBasicOfferModel>|null
     */
    public function getJobOffers(): Collection|null
    {
        return $this->jobOffers;
    }

    /**
     * @param Collection<PlentaJobsBasicOfferModel>|null $jobOffers
     */
    public function setJobOffers(Collection|null $jobOffers): self
    {
        $this->jobOffers = $jobOffers;

        return $this;
    }

    public function getObjModule(): JobOfferListController
    {
        return $this->objModule;
    }

    public function setObjModule(JobOfferListController $objModule): self
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
}
