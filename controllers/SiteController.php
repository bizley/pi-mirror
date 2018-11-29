<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\GoogleCalendar;
use app\models\OpenWeatherMap;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ErrorAction;

/**
 * PI Mirror site controller.
 * @package app\controllers
 */
class SiteController extends Controller
{
    /**
     * Error action just in case.
     * @return array
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    /**
     * Main screen.
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }
    
    /**
     * AJAX weather response.
     * @return array
     */
    public function actionWeather(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $weather = new OpenWeatherMap(['key' => Yii::$app->params['open-weather-map-api-id']]);
        
        $period  = 'day';

        $sunrise = $weather->getSunrise();
        $sunset  = $weather->getSunset();

        $now = time();
        $currentHour = date('G');

        if ($sunrise !== null && $sunset !== null) {
            if ($now < $sunrise || $now > $sunset) {
                $period = 'night';
            }
        } elseif ($currentHour < 6 || $currentHour >= 18) {
            $period = 'night';
        }
        
        return [
            'period' => $period,
            'weatherId' => $weather->getWeatherId(),
            'temp' => $weather->getTemp(),
            'pressure' => $weather->getPressure(),
            'humidity' => $weather->getHumidity(),
            'windDeg' => $weather->getWindDeg(),
            'windSpeed' => $weather->getWindSpeed(),
            'clouds' => $weather->getClouds(),
        ];
    }
    
    /**
     * AJAX forecast weather response.
     * @return array
     */
    public function actionForecast(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $forecast = new OpenWeatherMap(['key' => Yii::$app->params['open-weather-map-api-id']]);
        
        return [
            'today' => $forecast->getToday(),
            'tomorrow' => $forecast->getTomorrow(),
            'overmorrow' => $forecast->getOvermorrow(),
        ];
    }
    
    /**
     * AJAX calendar events response.
     * @return array
     */
    public function actionEvents(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $upcoming = [];
        $calendar = new GoogleCalendar();
        
        $specificCals = [
            'primary',
            Yii::$app->params['calendars']['birthdays'],
            Yii::$app->params['calendars']['holidays'],
        ];

        foreach ($specificCals as $cal) {
            $events = $calendar->getEvents($cal);

            /* @var $event \Google_Service_Calendar_Events */
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
                    'date' => $date,
                ];
            }
        }

        if (!empty($upcoming)) {
            uasort($upcoming, function ($a, $b) {
                return $a['stamp'] <=> $b['stamp'];
            });
        }
        
        return \array_slice($upcoming, 0, 10);
    }
}
