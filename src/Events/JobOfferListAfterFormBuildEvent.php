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

use Plenta\ContaoJobsBasic\Form\Type\JobOfferFilterType;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

class JobOfferListAfterFormBuildEvent extends Event
{
    public const NAME = 'plenta_jobs_basic.job_offer_list.after_form_build';

    protected FormInterface $form;

    /**
     * @return JobOfferFilterType
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
