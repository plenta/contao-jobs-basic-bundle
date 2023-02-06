<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2023, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\Csrf;

use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;

class JobsBasicCsrfTokenManager
{
    private ContaoCsrfTokenManager $csrfTokenManager;
    private string $csrfTokenName;

    public function __construct(
        ContaoCsrfTokenManager $csrfTokenManager,
        string $csrfTokenName
    ) {
        $this->csrfTokenName = $csrfTokenName;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function generateToken(): string
    {
        return $this->csrfTokenManager->getToken($this->csrfTokenName)->getValue();
    }
}
