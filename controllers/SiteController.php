<?php

namespace app\controllers;

use app\models\OpenWeatherMap;
use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * PI Mirror site controller.
 */
class SiteController extends Controller
{

    /**
     * Error action just in case.
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Main screen.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    /**
     * AJAX weather response.
     */
    public function actionWeather()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $weather = new OpenWeatherMap(['key' => Yii::$app->params['open-weather-map-api-id']]);
        
        $period  = 'day';
        $sunrise = $weather->sunrise;
        $sunset  = $weather->sunset;
        if (!empty($sunrise) && !empty($sunset)) {
            if (time() < $sunrise || time() > $sunset) {
                $period = 'night';
            }
        } else {
            if (date('G') < 6 || date('G') >= 18) {
                $period = 'night';
            }
        }
        
        return [
            'period'    => $period,
            'weatherId' => $weather->weatherId,
            'temp'      => $weather->temp,
        ];
    }
}
