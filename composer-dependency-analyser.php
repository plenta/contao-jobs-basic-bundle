<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();
$config
    ->ignoreErrorsOnPath('src/EventListener/Contao/Hooks/ChangelanguageNavigationListener.php', [ErrorType::UNKNOWN_CLASS])
    ->ignoreErrorsOnPackage('contao/maker-bundle', [ErrorType::UNUSED_DEPENDENCY])
;

return $config;