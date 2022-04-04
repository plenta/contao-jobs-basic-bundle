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

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\CoreBundle\Util\PackageUtil;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;

/**
 * @Hook("parseBackendTemplate")
 */
class ParseBackendTemplateListener
{
    private TwigEnvironment $twig;

    public function __construct(TwigEnvironment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(string $buffer, string $template): string
    {
        if ('be_welcome' === $template) {
            $GLOBALS['TL_CSS'][] = 'bundles/plentacontaojobsbasic/dashboard.css';
            $info = $this->twig->render('@PlentaContaoJobsBasic/be_plenta_info.html.twig', [
                'version' => PackageUtil::getVersion('plenta/contao-jobs-basic-bundle'),

            ]);

            $buffer = $info.$buffer;
        }

        return $buffer;
    }
}
