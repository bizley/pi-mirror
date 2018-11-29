<?php

declare(strict_types=1);

use yii\log\FileTarget;
use yii\caching\FileCache;

return [
    'id' => 'pi-mirror',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'G_D9zNoNXCFZmd0hcsRaqDLWkbc4id7C',
        ],
        'cache' => [
            'class' => FileCache::class,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => require __DIR__ . '/params-local.php',
];
