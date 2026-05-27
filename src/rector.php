<?php

//declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/api',
        __DIR__ . '/backend',
        __DIR__ . '/common',
        __DIR__ . '/console',
        __DIR__ . '/environments',
        __DIR__ . '/frontend',
    ])
//    ->withSkip([
//        // Excludes the entire vendor directory from all rector processes
//        __DIR__ . '/vendor/*',
//    ])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(0);
