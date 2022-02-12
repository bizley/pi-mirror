<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\OpenWeatherMap;
use yii\base\InvalidConfigException;
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
     * @return Response
     * @throws InvalidConfigException
     */
    public function actionData(): Response
    {
        $data = new OpenWeatherMap();

        return $this->asJson(
            [
                'current' => $data->getCurrentWeather(),
                'hourly' => [
                    $data->getHourlyWeather(0),
                    $data->getHourlyWeather(1),
                    $data->getHourlyWeather(2),
                    $data->getHourlyWeather(4),
                    $data->getHourlyWeather(6),
                    $data->getHourlyWeather(8),
                ],
                'daily' => [
                    $data->getDailyWeather(0),
                    $data->getDailyWeather(1),
                    $data->getDailyWeather(2),
                    $data->getDailyWeather(3),
                    $data->getDailyWeather(4),
                    $data->getDailyWeather(5),
                ],
                'lastDate' => $data->getLastFetchDate(),
            ]
        );
    }
}
