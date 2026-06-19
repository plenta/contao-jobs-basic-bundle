<?php

declare(strict_types=1);

/*
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2026, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\EventListener\Contao;

use Contao\CoreBundle\Event\ContaoCoreEvents;
use Contao\CoreBundle\Event\PreviewUrlConvertEvent;
use Contao\CoreBundle\Security\Authentication\FrontendPreviewAuthenticator;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PreviewConvertListener
{
    public function __construct(protected TokenStorageInterface $tokenStorage)
    {
    }

    #[AsEventListener(ContaoCoreEvents::PREVIEW_URL_CONVERT, priority: 100)]
    public function onConvert(PreviewUrlConvertEvent $event): void
    {
        $request = $event->getRequest();

        if ($jobId = $request->query->get('jobsBasicOffer')) {
            $job = PlentaJobsBasicOfferModel::findById($jobId);

            if (!$job->published || ($job->start && $job->start > time()) || ($job->stop && $job->stop < time())) {
                $preview = $request->getSession()->get(FrontendPreviewAuthenticator::SESSION_NAME);
                $preview['showUnpublished'] = true;
                $request->getSession()->set(FrontendPreviewAuthenticator::SESSION_NAME, $preview);
            }

            $event->setUrl($job->getFrontendUrl($request->getLocale()));
        }
    }
}
