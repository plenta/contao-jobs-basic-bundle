<?php

declare(strict_types=1);

/**
 * Plenta Jobs Basic Bundle for Contao Open Source CMS
 *
 * @copyright     Copyright (c) 2022, Plenta.io
 * @author        Plenta.io <https://plenta.io>
 * @link          https://github.com/plenta/
 */

namespace Plenta\ContaoJobsBasic\EventListener\Contao\Hooks;

use Composer\InstalledVersions;
use Contao\Config;
use Contao\Input;
use Doctrine\ORM\EntityManagerInterface;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOffer;
use Plenta\ContaoJobsBasic\Entity\TlPlentaJobsBasicOfferTranslation;
use Symfony\Component\HttpFoundation\RequestStack;
use Terminal42\ChangeLanguage\Event\ChangelanguageNavigationEvent;

class ChangelanguageNavigationListener
{
    protected EntityManagerInterface $entityManager;
    protected RequestStack $requestStack;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    public function onChangelanguageNavigation(ChangelanguageNavigationEvent $event): void
    {
        $targetRoot = $event->getNavigationItem()->getRootPage();
        $language = $targetRoot->language;
        if (!isset($_GET['items']) && isset($_GET['auto_item']) && Config::get('useAutoItem')) {
            Input::setGet('items', Input::get('auto_item'));
        }
        $alias = Input::get('items');

        if ($alias) {
            if (version_compare(InstalledVersions::getVersion('contao/core-bundle'), '4.13', '>=')) {
                $mainRequest = $this->requestStack->getMainRequest();
            } else {
                $mainRequest = $this->requestStack->getMasterRequest();
            }

            $offerRepo = $this->entityManager->getRepository(TlPlentaJobsBasicOffer::class);
            $jobOffer = $offerRepo->findPublishedByIdOrAlias($alias);

            if (null === $jobOffer) {
                $offerTransRepo = $this->entityManager->getRepository(TlPlentaJobsBasicOfferTranslation::class);
                $jobOfferTrans = $offerTransRepo->findByAliasAndLanguage($alias, $mainRequest->getLocale());
                if ($jobOfferTrans) {
                    $jobOffer = $jobOfferTrans->getOffer();
                }
            }

            if (null !== $jobOffer) {
                if ($targetRoot->rootIsFallback) {
                    $newAlias = $jobOffer->getAlias();
                } else {
                    $translation = $jobOffer->getTranslation($language);
                    if (!$translation) {
                        $event->skipInNavigation();

                        return;
                    }
                    $newAlias = $translation->getAlias();
                }

                $event->getUrlParameterBag()->setUrlAttribute('items', $newAlias);
            }
        }
    }
}
