<?php

declare(strict_types=1);

namespace Plenta\ContaoJobsBasic\EventListener\Contao\DCA;

use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsCallback(table: 'tl_content', target: 'config.onload')]
class TlContent
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function __invoke(DataContainer|null $dc = null): void
    {
        if (null === $dc || !$dc->id || 'edit' !== $this->requestStack->getCurrentRequest()->query->get('act')) {
            return;
        }

        $element = ContentModel::findById($dc->id);

        if (null === $element || 'plenta_jobs_basic_job_offer_teaser' !== $element->type) {
            return;
        }

        $GLOBALS['TL_DCA']['tl_content']['fields']['text']['eval']['mandatory'] = false;
    }
}
