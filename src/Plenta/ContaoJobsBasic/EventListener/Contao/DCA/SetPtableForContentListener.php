<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2021, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\EventListener\Contao\DCA;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Symfony\Component\HttpFoundation\RequestStack;

class SetPtableForContentListener
{
    private RequestStack $requestStack;
    private ScopeMatcher $scopeMatcher;

    public function __construct(RequestStack $requestStack, ScopeMatcher $scopeMatcher)
    {
        $this->requestStack = $requestStack;
        $this->scopeMatcher = $scopeMatcher;
    }

    public function setPtableForContentListener(string $table): void
    {
        // We only want to adjust the DCA of tl_content
        if ('tl_content' !== $table) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        // Check if this is a back end request
        if (null === $request || !$this->scopeMatcher->isBackendRequest($request)) {
            return;
        }

        if ('plenta_jobs_basic_offers' === $request->query->get('do')) {
            $GLOBALS['TL_DCA']['tl_content']['config']['ptable'] = 'tl_plenta_jobs_basic_offer';
        }
    }
}
