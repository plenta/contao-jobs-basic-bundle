<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao CMS
 *
 * @copyright     Copyright (c) 2020, Christian Barkowsky & Christoph Werner
 * @author        Christian Barkowsky <https://plenta.io>
 * @author        Christoph Werner <https://plenta.io>
 * @link          https://plenta.io
 * @license       proprietary
 */

namespace Plenta\ContaoJobsBasic\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Plenta\ContaoJobsBasic\PlentaContaoJobsBasicBundle;

class PlentaContaoJobsBasicBundleTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $bundle = new PlentaContaoJobsBasicBundle();

        $this->assertInstanceOf('Plenta\ContaoJobsBasic\PlentaContaoJobsBasicBundle', $bundle);
    }
}
