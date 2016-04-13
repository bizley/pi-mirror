<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css?family=Dosis:400,700&subset=latin,latin-ext',
        'css/style.css',
        'css/weather-icons.min.css',
    ];
    public $js = [
        'js/mirror.js',
    ];
    public $depends = [
//        'yii\web\YiiAsset',
    ];
}
