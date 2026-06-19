<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic;

use Plenta\ContaoJobsBasic\DependencyInjection\PlentaContaoJobsBasicExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Configures the bundle.
 */
class PlentaContaoJobsBasicBundle extends AbstractBundle
{
    public function getContainerExtension(): ExtensionInterface|null
    {
        return new PlentaContaoJobsBasicExtension();
    }
}
