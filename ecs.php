<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return ECSConfig::configure()
    ->withSets([
        __DIR__ . '/vendor/lmc/coding-standard/ecs.php',
    ])
    ->withConfiguredRule(
        ArraySyntaxFixer::class,
        ['syntax' => 'short'],
    )
    ->withCache(
        directory: sys_get_temp_dir() . '/ecs_cached_files',
        namespace: getcwd(),
    )
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSets([
        SetList::SPACES,
        SetList::ARRAY,
        SetList::DOCBLOCK,
        SetList::PSR_12,
    ]);
