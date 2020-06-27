<?php

declare(strict_types=1);

namespace app\models;

use RuntimeException;
use Throwable;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;

/**
 * OpenWeatherMap
 * @author Bizley
 */
class OpenWeatherMap extends Component
{
    private const UNITS = 'metric';
    private const LANG = 'pl';
    private const CACHE_DURATION = 1800;

    /**
     * @var string API weather URL
     */
    private string $url = 'https://api.openweathermap.org/data/2.5/onecall';

    /**
     * @var Client|null HTTP client
     */
    private ?Client $client = null;

    /**
     * Returns HTTP client object.
     * @return Client
     */
    public function getClient(): Client
    {
        if ($this->client === null) {
            $this->client = new Client();
        }

        return $this->client;
    }

    /**
     * Checks current weather.
     * @return array|bool
     */
    public function checkWeather()
    {
        try {
            $response = $this->getClient()->createRequest()
                ->setMethod('get')
                ->setUrl($this->url)
                ->setData(
                    [
                        'lat' => Yii::$app->params['latitude'] ?? null,
                        'lon' => Yii::$app->params['longitude'] ?? null,
                        'units' => Yii::$app->params['units'] ?? self::UNITS,
                        'lang' => Yii::$app->params['lang'] ?? self::LANG,
                        'APPID' => Yii::$app->params['apiKey'] ?? null,
                        'exclude' => 'minutely',
                    ]
                )
                ->send();

            if (!$response->isOk) {
                throw new RuntimeException($response->data);
            }

            return $response->data;
        } catch (Throwable $e) {
            Yii::error($e->getMessage());
        }

        return false;
    }

    /**
     * Returns cached weather conditions.
     * @return array|bool
     */
    public function getWeather()
    {
        $weather = Yii::$app->cache->get('OpenWeatherMap');

        if ($weather === false) {
            $weather = $this->checkWeather();

            if ($weather !== false) {
                Yii::$app->cache->set(
                    'OpenWeatherMap',
                    $weather,
                    Yii::$app->params['cacheDuration'] ?? self::CACHE_DURATION
                );
            }
        }

        return $weather;
    }

    /**
     * Returns cached current weather object.
     * @return array|null
     * @throws InvalidConfigException
     */
    public function getCurrentWeather(): ?array
    {
        $weather = $this->getWeather();

        if ($weather === false || !array_key_exists('current', $weather)) {
            return null;
        }

        return Weather::format($weather['current']);
    }

    /**
     * Returns cached hourly weather object for given hour offset.
     * @param int $offset
     * @return array|null
     * @throws InvalidConfigException
     */
    public function getHourlyWeather(int $offset): ?array
    {
        $weather = $this->getWeather();

        if (
            $weather === false
            || !array_key_exists('hourly', $weather)
            || !is_array($weather['hourly'])
            || count($weather['hourly']) === 0
        ) {
            return null;
        }

        $currentOffset = 0;
        foreach ($weather['hourly'] as $hourlyWeather) {
            if (
                array_key_exists('dt', $hourlyWeather)
                && $hourlyWeather['dt'] > time()
                && $currentOffset++ === $offset
            ) {
                return Weather::format($hourlyWeather);
            }
        }

        return null;
    }

    /**
     * Returns cached daily weather object for given day offset.
     * @param int $offset
     * @return array|null
     * @throws InvalidConfigException
     */
    public function getDailyWeather(int $offset): ?array
    {
        $weather = $this->getWeather();

        if (
            $weather === false
            || !array_key_exists('daily', $weather)
            || !is_array($weather['daily'])
            || count($weather['daily']) === 0
        ) {
            return null;
        }

        $currentOffset = 0;
        foreach ($weather['daily'] as $dailyWeather) {
            if (
                array_key_exists('dt', $dailyWeather)
                && $dailyWeather['dt'] > time()
                && $currentOffset++ === $offset
            ) {
                return Weather::format($dailyWeather);
            }
        }

        return null;
    }
}
