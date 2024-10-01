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

use Contao\CoreBundle\Event\MenuEvent;
use Plenta\ContaoJobsBasic\Controller\Contao\BackendModule\SettingsController;
use Plenta\ContaoJobsBasic\Helper\PermissionsHelper;
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

        if (!$contentNode) {
            return;
        }

        if (PermissionsHelper::canAccessModule('settings')) {
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
}
