<?php

$date = date('Y');

$header = <<<EOF
Plenta Jobs Basic Bundle for Contao Open Source CMS

@copyright     Copyright (c) $date, Plenta.io
@author        Plenta.io <https://plenta.io>
@link          https://github.com/plenta/
EOF;

$finder = PhpCsFixer\Finder::create()
    ->exclude('Resources')
    ->exclude('Fixtures')
    ->in([__DIR__.'/src'])
;

$config = new PhpCsFixer\Config();

return $config->setRules(
    [
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP71Migration' => true,
        '@PHP71Migration:risky' => true,
        '@PHP73Migration' => true,
        '@PHP74Migration' => true,
        '@PHP74Migration:risky' => true,
        '@PHPUnit60Migration:risky' => true,
        '@PHPUnit75Migration:risky' => true,
        '@PHPUnit84Migration:risky' => true,
        'align_multiline_comment' => true,
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'comment_to_phpdoc' => true,
        'compact_nullable_typehint' => true,
        'general_phpdoc_annotation_remove' => [
            'annotations' => [
                'author',
                'expectedException',
                'expectedExceptionMessage',
            ],
        ],
        'fully_qualified_strict_types' => true,
        'header_comment' => ['header' => $header, 'comment_type' => 'PHPDoc'],
        'heredoc_to_nowdoc' => true,
        'linebreak_after_opening_tag' => true,
        'list_syntax' => ['syntax' => 'short'],
        'no_null_property_initialization' => true,
        'no_superfluous_elseif' => true,
        'no_superfluous_phpdoc_tags' => false,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'php_unit_strict' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
            'sort_algorithm' => 'none',
        ],
        'strict_comparison' => false,
        'strict_param' => true,
        'void_return' => true,
    ]
)
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(false)
    ;
