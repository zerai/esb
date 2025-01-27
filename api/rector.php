<?php declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
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
        __DIR__ . '/tools',
        __DIR__ . '/src/Kernel.php',
        __DIR__ . '/tests/bootstrap.php',
    ])

    // uncomment to reach your current PHP version
    ->withPhpSets(php82: true)
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(0);
