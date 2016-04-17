<?php

namespace app\controllers;

use app\models\GoogleCalendar;
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
            'pressure'  => $weather->pressure,
            'humidity'  => $weather->humidity,
            'windDeg'   => $weather->windDeg,
            'windSpeed' => $weather->windSpeed,
            'clouds'    => $weather->clouds,
        ];
    }
    
    /**
     * AJAX forecast weather response.
     */
    public function actionForecast()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $forecast = new OpenWeatherMap(['key' => Yii::$app->params['open-weather-map-api-id']]);
        
        return [
            'today'      => $forecast->today,
            'tomorrow'   => $forecast->tomorrow,
            'overmorrow' => $forecast->overmorrow,
        ];
    }
    
    /**
     * AJAX calendar events response.
     */
    public function actionEvents()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $upcoming = [];
        $calendar = new GoogleCalendar;
        
        $specificCals = ['primary', Yii::$app->params['calendars']['birthdays'], Yii::$app->params['calendars']['holidays']];
        foreach ($specificCals as $cal) {
            $events = $calendar->getEvents($cal);
            foreach ($events->getItems() as $event) {
                $start = $event->start->dateTime;
                if (empty($start)) {
                    $start = $event->start->date;
                    $date  = date('Y/m/d', strtotime($start));
                } else {
                    $date  = date('H:i Y/m/d', strtotime($start));
                }
                $upcoming[] = [
                    'stamp' => strtotime($start),
                    'event' => $event->getSummary(),
                    'date'  => $date,
                ];
            }
        }

        if (!empty($upcoming)) {
            uasort($upcoming, function ($a, $b) {
                if ($a['stamp'] == $b['stamp']) {
                    return 0;
                }
                return ($a['stamp'] < $b['stamp']) ? -1 : 1;
            });
        }
        
        return array_slice($upcoming, 0, 10);
    }
}
