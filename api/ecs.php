<?php declare(strict_types=1);

use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\Import\FullyQualifiedStrictTypesFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withCache(__DIR__ . '/var/cache_tools/ecs')
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/context/cart/src',
        __DIR__ . '/context/cart/tests',
        __DIR__ . '/context/inventory/src',
        __DIR__ . '/context/inventory/tests',
        __DIR__ . '/context/pricing/src',
        __DIR__ . '/context/pricing/tests',
    ])

    ->withSkip([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src/Kernel.php',
        __DIR__ . '/tests/bootstrap.php',
        BlankLineAfterOpeningTagFixer::class,
    ])

    // add a single rule
    ->withRules([
        BlankLineAfterNamespaceFixer::class,
        DeclareStrictTypesFixer::class,
        FullyQualifiedStrictTypesFixer::class,
        NativeFunctionInvocationFixer::class,
        NoUnusedImportsFixer::class,
        OrderedImportsFixer::class,
        StrictComparisonFixer::class,
    ])

    // add sets - group of rules
    ->withPreparedSets(
        arrays: true,
        comments: true,
        docblocks: true,
        namespaces: true,
        spaces: true,
        psr12: true
     )
     
     ;
