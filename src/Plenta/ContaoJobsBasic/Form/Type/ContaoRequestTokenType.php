<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Form\Type;

use Plenta\ContaoJobsBasic\Csrf\JobsBasicCsrfTokenManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContaoRequestTokenType extends AbstractType
{
    private JobsBasicCsrfTokenManager $csrfTokenManager;

    public function __construct(
        JobsBasicCsrfTokenManager $csrfTokenManager
    ) {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data' => $this->csrfTokenManager->generateToken(),
            'empty_data' => $this->csrfTokenManager->generateToken(),
            'mapped' => false,
        ]);
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['full_name'] = 'REQUEST_TOKEN';
    }
}
