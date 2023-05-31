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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobSortingType extends AbstractType
{
    protected RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $request = $this->requestStack->getMainRequest();
        $sortBy = $request->get('sortBy', 'title');
        $order = $request->get('order', 'ASC');

        $builder->add('REQUEST_TOKEN', ContaoRequestTokenType::class);
        $builder->add('sort', ChoiceType::class, [
            'choices' => $options['sortingOptions'],
            'choice_label' => fn ($item) => 'tl_module.plentaJobsBasicSortingFields.fields.'.$item,
            'translation_domain' => 'contao_tl_module',
            'label' => false,
            'data' => $sortBy.'__'.$order,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'sortingOptions' => null,
        ]);
    }
}
