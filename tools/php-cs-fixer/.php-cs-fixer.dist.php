<?php

declare(strict_types=1);

use PhpCsFixer\Finder;

/**
 * @example https://github.com/symfony/demo/blob/main/.php_cs.dist
 * @doc https://cs.symfony.com/doc/rules/index.html
 * @doc https://cs.symfony.com/doc/usage.html
 */
$fileHeaderComment = <<<COMMENT
    This file is part of the Ranky Media Bundle package.
    (c) Jose Carlos Campos <nerjacarloscampos@gmail.com>
    For the full copyright and license information, please view the LICENSE
    file that was distributed with this source code.
    COMMENT;

$rules = [
    '@Symfony:risky'                              => true,
    '@PHPUnit84Migration:risky'                   => true,
    '@PHP80Migration:risky'                       => true,
    '@PHP81Migration'                             => true,
    'header_comment'                              => ['header' => '', 'separate' => 'both'],
    'linebreak_after_opening_tag'                 => true,
    'combine_consecutive_issets'                  => true,
    'combine_consecutive_unsets'                  => true,
    'declare_strict_types'                        => true,
    'single_quote'                                => true,
    'declare_parentheses'                         => true,
    'mb_str_functions'                            => false,
    'modernize_strpos'                            => true,
    'no_php4_constructor'                         => true,
    'no_unused_imports'                           => true,
    'no_unreachable_default_argument_value'       => true,
    'no_useless_else'                             => true,
    'no_useless_return'                           => true,
    'native_function_invocation'                  => false,
    'native_constant_invocation'                  => false,
    'date_time_create_from_format_call'           => true,
    'php_unit_strict'                             => false,
    'no_alias_functions'                          => true,
    'no_multiline_whitespace_around_double_arrow' => true,
    'phpdoc_order'                                => true,
    'strict_comparison'                           => true,
    'strict_param'                                => true,
    'line_ending'                                 => true,
];

$config         = new PhpCsFixer\Config();
$rootDir        = \dirname(__DIR__, 2);

return $config
    ->setRules($rules)
    ->setUsingCache(true)
    ->setCacheFile(__DIR__.'/php-cs-fixer.cache')
    ->setRiskyAllowed(true)
    ->setFinder(
        Finder::create()->in([
            $rootDir.'/src',
            $rootDir.'/tests',
        ])->append([
            __FILE__,
        ])
    );
