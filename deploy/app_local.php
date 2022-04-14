<?php

use BEdita\AWS\Filesystem\Adapter\S3Adapter;
use BEdita\Core\Cache\Engine\LayeredEngine;
use Cake\Cache\Engine\FileEngine;
use Cake\Cache\Engine\RedisEngine;

return [
    'debug' => filter_var(env('DEBUG', false), FILTER_VALIDATE_BOOLEAN),

    'Security' => [
        'salt' => env('SECURITY_SALT', null),
    ],

    'Datasources' => [
        'default' => [
            'url' => env('DATABASE_URL', null),
            'log' => false,
        ],
    ],

    'Cache' => [
        'default' => [
            'className' => RedisEngine::class,
            'host' => env('CACHE_REDIS_HOST', null),
            'port' => env('CACHE_REDIS_PORT', 6379),
            'database' => 0,
        ],

        'session' => [
            'className' => RedisEngine::class,
            'host' => env('CACHE_REDIS_HOST', null),
            'port' => env('CACHE_REDIS_PORT', 6379),
            'database' => 3,
            'duration' => 'tomorrow 4:00',
        ],

        '_bedita_core_' => [
            'className' => LayeredEngine::class,
            'persistent' => [
                'className' => RedisEngine::class,
                'host' => env('CACHE_REDIS_HOST', null),
                'port' => env('CACHE_REDIS_PORT', 6379),
                'database' => 0,
                'prefix' => 'bedita_core_',
            ],
            'prefix' => 'bedita_core_',
            'serialize' => true,
            'duration' => '+1 year',
            'url' => env('CACHE_BEDITACORE_URL', null),
        ],

        '_bedita_object_types_' => [
            'className' => LayeredEngine::class,
            'persistent' => [
                'className' => RedisEngine::class,
                'host' => env('CACHE_REDIS_HOST', null),
                'port' => env('CACHE_REDIS_PORT', 6379),
                'database' => 1,
                'prefix' => 'bedita_object_types_',
            ],
            'prefix' => 'bedita_object_types_',
            'serialize' => true,
            'duration' => '+1 year',
            'url' => env('CACHE_BEDITAOBJECTTYPES_URL', null),
        ],

        '_cake_core_' => [
            'className' => FileEngine::class,
            'prefix' => 'cake_core_',
            'path' => CACHE . 'persistent/',
            'serialize' => true,
            'duration' => '+1 years',
            'url' => env('CACHE_CAKECORE_URL', null),
        ],

        '_cake_model_' => [
            'className' => RedisEngine::class,
            'host' => env('CACHE_REDIS_HOST', null),
            'port' => env('CACHE_REDIS_PORT', 6379),
            'database' => 2,
            'prefix' => 'cake_model_',
            'serialize' => true,
            'duration' => '+1 years',
        ],

        '_cake_routes_' => [
            'className' => FileEngine::class,
            'prefix' => 'cake_routes_',
            'path' => CACHE,
            'serialize' => true,
            'duration' => '+1 years',
            'url' => env('CACHE_CAKEROUTES_URL', null),
        ],

        '_twig_views_' => [
            'className' => FileEngine::class,
            'path' => CACHE . 'twigView/',
            'serialize' => true,
            'duration' => '+1 year',
        ],
    ],

    'Session' => [
        'defaults' => 'cache',
        'timeout' => 1440, // 1 day, in minutes
        'handler' => [
            'config' => 'session',
        ],
    ],

    'Filesystem' => [
        'default' => [
            'className' => S3Adapter::class,
            'host' => env('S3_BUCKET_NAME', null),
            'path' => '',
            'region' => env('S3_BUCKET_REGION', null),
            'distributionId' => env('CDN_DISTRIBUTION_ID', null),
            'baseUrl' => env('CDN_DISTRIBUTION_URL', null),
        ],
        'thumbnails' => [
            'className' => S3Adapter::class,
            'host' => env('S3_BUCKET_NAME', null),
            'path' => '',
            'region' => env('S3_BUCKET_REGION', null),
            'distributionId' => env('CDN_DISTRIBUTION_ID', null),
            'baseUrl' => env('CDN_DISTRIBUTION_URL', null),
        ],
    ],

    'FrontendPlugin' => env('FRONTEND_PLUGIN', 'BEdita/API'),

    'StagingSite' => filter_var(env('STAGING', false), FILTER_VALIDATE_BOOLEAN),

    'Manage' => [
        'manager' => [
            'url' => env('MANAGER_URL', ''),
        ],
    ],
];
