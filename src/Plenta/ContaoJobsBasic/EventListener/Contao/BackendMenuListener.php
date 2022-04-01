<?php

namespace Plenta\ContaoJobsBasic\EventListener\Contao;

use Contao\CoreBundle\Event\MenuEvent;
use Contao\System;
use Plenta\ContaoJobsBasic\Controller\Contao\BackendModule\SettingsController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class BackendMenuListener
{
    protected $router;
    protected $requestStack;

    public function __construct(RouterInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    public function __invoke(MenuEvent $event): void
    {
        $factory = $event->getFactory();
        $tree = $event->getTree();

        if ('mainMenu' !== $tree->getName()) {
            return;
        }

        $contentNode = $tree->getChild('plenta_jobs_basic');

        $node = $factory
            ->createItem('plenta-jobs-basic-settings')
            ->setUri($this->router->generate(SettingsController::class))
            ->setLabel($GLOBALS['TL_LANG']['MOD']['plenta_jobs_basic_settings'][0])
            ->setLinkAttribute('title', $GLOBALS['TL_LANG']['MOD']['plenta_jobs_basic_settings'][1])
            ->setLinkAttribute('class', 'plenta-jobs-basic-settings')
            ->setCurrent($this->requestStack->getCurrentRequest()->get('_controller') === SettingsController::class.'::showSettings' || SettingsController::isActive($this->requestStack))
        ;

        $contentNode->addChild($node);
    }
}