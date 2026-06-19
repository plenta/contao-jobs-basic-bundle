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

use Plenta\ContaoJobsBasic\Form\Type\JobOfferFilterType;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

class JobOfferFilterAfterFormBuildEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.job_offer_filter.after_form_build';

    /**
     * @var FormInterface<JobOfferFilterType>
     */
    protected FormInterface $form;

    protected string $route;

    /**
     * @return FormInterface<JobOfferFilterType>
     */
    public function getForm(): FormInterface
    {
        return $this->form;
    }

    /**
     * @param FormInterface<JobOfferFilterType> $form
     */
    public function setForm(FormInterface $form): self
    {
        $this->form = $form;

        return $this;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }
}
