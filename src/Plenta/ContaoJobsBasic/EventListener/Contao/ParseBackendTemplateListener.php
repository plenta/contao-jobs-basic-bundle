<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\EventListener\Contao;

use Contao\CoreBundle\Util\PackageUtil;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Hook("parseBackendTemplate")
 */
class ParseBackendTemplateListener
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function __invoke(string $buffer, string $template): string
    {
        if ('be_welcome' === $template) {
            $GLOBALS['TL_CSS'][] = 'bundles/plentacontaojobsbasic/dashboard.css';

            $info = '<div id="plentaJobsInfoPanel" style="padding: 15px; border-bottom: 1px solid #e6e6e8;">';
            $info .= '<div class="tl_info plenta-wrapper">';
            $info .= '<div class="plenta-box">';
            $info .= 'Plenta Jobs (Basic) '.PackageUtil::getVersion('plenta/contao-jobs-basic-bundle');
            $info .= '</div>';
            $info .= '<div class="plenta-box last">';
            $info .= '<a href="https://github.com/plenta/contao-jobs-basic-bundle/issues" title="'.
                $this->translator->trans('MSC.PLENTA_JOBS.DASHBOARD.report_error', [], 'contao_default').'
                " target="_blank">'.
                $this->translator->trans('MSC.PLENTA_JOBS.DASHBOARD.report_error', [], 'contao_default').
                '</a> - <a href="https://plenta.io" title="'.
                $this->translator->trans('MSC.PLENTA_JOBS.DASHBOARD.documentation', [], 'contao_default').'
                " target="_blank">'.
                $this->translator->trans('MSC.PLENTA_JOBS.DASHBOARD.documentation', [], 'contao_default').
                '</a> - <a href="https://github.com/sponsors/plenta" title="'.
                $this->translator->trans('MSC.PLENTA_JOBS.DASHBOARD.sponsor', [], 'contao_default').'
                " target="_blank">'.
                $this->translator->trans('MSC.PLENTA_JOBS.DASHBOARD.sponsor', [], 'contao_default').'</a>';
            $info .= '</div>';
            $info .= '</div>';
            $info .= '</div>';

            $buffer = $info.$buffer;
        }

        return $buffer;
    }
}
