<?php

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Class AppAsset
 * @package app\assets
 */
class AppAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $basePath = '@webroot';

    /**
     * @var string
     */
    public $baseUrl = '@web';

    /**
     * @var array
     */
    public $css = [
        'https://fonts.googleapis.com/css?family=Dosis:400,700&subset=latin,latin-ext',
        'css/style.1.4.css',
        'css/weather-icons.min.css',
        'css/weather-icons-wind.min.css',
    ];

    /**
     * @var array
     */
    public $js = ['js/mirror.1.4.js'];
}
