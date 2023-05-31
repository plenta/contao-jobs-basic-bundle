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

use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

class JobOfferFilterAfterFormBuildEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.job_offer_filter.after_form_build';

    protected FormInterface $form;

    /**
     * @return FormInterface
     */
    public function getForm(): FormInterface
    {
        return $this->form;
    }

    /**
     * @param FormInterface $form
     *
     * @return JobOfferListAfterFormBuildEvent
     */
    public function setForm(FormInterface $form): self
    {
        $this->form = $form;

        return $this;
    }
}
