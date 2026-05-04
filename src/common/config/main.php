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
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=docker_db_name;dbname=autofix',
            'username' => '1111',
            'password' => '1111',
            'charset' => 'utf8',
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
        ],
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => 'db', // Database connection component
            'tableName' => '{{%queue}}', // Table storing the queue
            'channel' => 'default', // Queue channel
            'mutex' => \yii\mutex\MysqlMutex::class, // Avoid race conditions
            'as log' => \yii\queue\LogBehavior::class,
            'ttr' => 2 * 60, // Максимальное время выполнения задания
            'attempts' => 3, // Максимальное кол-во попыток
        ],
    ],
];
