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

use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\ModuleModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobOfferFilterType extends AbstractType
{
    protected InsertTagParser $insertTagParser;
    protected RequestStack $requestStack;

    public function __construct(InsertTagParser $insertTagParser, RequestStack $requestStack)
    {
        $this->insertTagParser = $insertTagParser;
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ModuleModel $model */
        $model = $options['fmd'];
        $builder->add('REQUEST_TOKEN', ContaoRequestTokenType::class);
        if ($model->plentaJobsBasicShowTypes) {
            $builder->add('typesHeadline', HtmlType::class, [
                'html' => $this->getHeadlineHtml($model->plentaJobsBasicTypesHeadline, 'jobTypes'),
                'priority' => 100,
            ]);
            $builder->add('types', ChoiceType::class, [
                'choices' => array_flip($options['types']),
                'multiple' => true,
                'expanded' => true,
                'label' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'widget-checkbox',
                ],
                'data' => $this->requestStack->getMainRequest()->get('types', []),
                'priority' => 99,
            ]);
        }

        if ($model->plentaJobsBasicShowLocations) {
            $builder->add('locationsHeadline', HtmlType::class, [
                'html' => $this->getHeadlineHtml($model->plentaJobsBasicLocationsHeadline, 'jobLocation'),
                'priority' => 90,
            ]);
            $builder->add('location', ChoiceType::class, [
                'choices' => array_flip($options['locations']),
                'multiple' => !$model->plentaJobsBasicDisableMultipleLocations,
                'expanded' => true,
                'label' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'widget-checkbox',
                ],
                'placeholder' => 'MSC.PLENTA_JOBS.filterForm.locationPlaceholder',
                'translation_domain' => 'contao_default',
                'data' => $this->requestStack->getMainRequest()->get('location', $model->plentaJobsBasicDisableMultipleLocations ? '' : []),
                'priority' => 89,
            ]);
        }

        if ($model->plentaJobsBasicShowButton) {
            $builder->add('submit', SubmitType::class, [
                'label' => $model->plentaJobsBasicSubmit,
                'priority' => 0,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'fmd' => null,
            'types' => [],
            'locations' => [],
        ]);
    }

    public function getHeadlineHtml(?string $content, string $type): string
    {
        if (empty($content)) {
            return '';
        }

        $return = '<div class="plenta_jobs_basic_filter_widget_headline '.$type.'">';
        $return .= $this->insertTagParser->replace($content);
        $return .= '</div>';

        return $return;
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
