<?php

declare(strict_types=1);

use Contao\EasyCodingStandard\Set\SetList;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $date = date('Y');

    $header = <<<EOF
    Plenta Jobs Basic Bundle for Contao Open Source CMS
    
    @copyright     Copyright (c) $date, Plenta.io
    @author        Plenta.io <https://plenta.io>
    @link          https://github.com/plenta/
    EOF;

    $ecsConfig->ruleWithConfiguration(HeaderCommentFixer::class, [
        'header' => $header,
    ]);
};

/*return ECSConfig::configure()
    ->withSets([SetList::CONTAO])
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/contao',
        __DIR__.'/src',
        // __DIR__.'/tests',
        __DIR__.'/ecs.php',
    ])
    /*->withSkip([
        ReferenceUsedNamesOnlySniff::class => [
            'config/contao.php',
        ],
    ]) //end comment
    ->withConfiguredRule(HeaderCommentFixer::class, [
        'header' => $header,
    ])
    ->withParallel()
    ->withCache(sys_get_temp_dir().'/ecs/ecs')
;*/
