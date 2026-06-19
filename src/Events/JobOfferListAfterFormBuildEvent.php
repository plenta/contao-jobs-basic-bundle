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

use Plenta\ContaoJobsBasic\Form\Type\JobSortingType;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

class JobOfferListAfterFormBuildEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.job_offer_list.after_form_build';

    /**
     * @var FormInterface<JobSortingType>
     */
    protected FormInterface $form;

    /**
     * @return FormInterface<JobSortingType>
     */
    public function getForm(): FormInterface
    {
        return $this->form;
    }

    /**
     * @param FormInterface<JobSortingType> $form
     */
    public function setForm(FormInterface $form): self
    {
        $this->form = $form;

        return $this;
    }
}
