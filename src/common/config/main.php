<?php

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@frontWeb' => '',
        '@backWeb' => '',
        '@apiWeb' => '',
        '@frontSSLWeb' => '',
        '@backSSLWeb' => '',
        '@apiSSLWeb' => '',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
        ],
    ],
];
