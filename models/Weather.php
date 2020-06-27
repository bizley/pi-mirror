<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\InvalidConfigException;

class Weather
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string|null
     * @throws InvalidConfigException
     */
    public function getTime(): ?string
    {
        return array_key_exists('dt', $this->data)
            ? Yii::$app->formatter->asTime($this->data['dt'], 'HH:mm')
            : null;
    }

    /**
     * @return string|null
     * @throws InvalidConfigException
     */
    public function getDate(): ?string
    {
        return array_key_exists('dt', $this->data)
            ? Yii::$app->formatter->asDate($this->data['dt'], 'yyyy-MM-dd')
            : null;
    }

    /**
     * @return string|null
     * @throws InvalidConfigException
     */
    public function getSunrise(): ?string
    {
        return array_key_exists('sunrise', $this->data)
            ? Yii::$app->formatter->asTime($this->data['sunrise'], 'HH:mm')
            : null;
    }

    /**
     * @return string|null
     * @throws InvalidConfigException
     */
    public function getSunset(): ?string
    {
        return array_key_exists('sunset', $this->data)
            ? Yii::$app->formatter->asTime($this->data['sunset'], 'HH:mm')
            : null;
    }

    public function getTemperature(): ?string
    {
        return array_key_exists('temp', $this->data) && !is_array($this->data['temp'])
            ? (string)round($this->data['temp'])
            : null;
    }

    public function getMorningTemperature(): ?string
    {
        return array_key_exists('temp', $this->data)
        && is_array($this->data['temp'])
        && array_key_exists('morn', $this->data['temp'])
            ? (string)round($this->data['temp']['morn'])
            : null;
    }

    public function getDayTemperature(): ?string
    {
        return array_key_exists('temp', $this->data)
        && is_array($this->data['temp'])
        && array_key_exists('day', $this->data['temp'])
            ? (string)round($this->data['temp']['day'])
            : null;
    }

    public function getEveningTemperature(): ?string
    {
        return array_key_exists('temp', $this->data)
        && is_array($this->data['temp'])
        && array_key_exists('eve', $this->data['temp'])
            ? (string)round($this->data['temp']['eve'])
            : null;
    }

    public function getNightTemperature(): ?string
    {
        return array_key_exists('temp', $this->data)
        && is_array($this->data['temp'])
        && array_key_exists('night', $this->data['temp'])
            ? (string)round($this->data['temp']['night'])
            : null;
    }

    public function getMinTemperature(): ?string
    {
        return array_key_exists('temp', $this->data)
        && is_array($this->data['temp'])
        && array_key_exists('min', $this->data['temp'])
            ? (string)round($this->data['temp']['min'])
            : null;
    }

    public function getMaxTemperature(): ?string
    {
        return array_key_exists('temp', $this->data)
        && is_array($this->data['temp'])
        && array_key_exists('max', $this->data['temp'])
            ? (string)round($this->data['temp']['max'])
            : null;
    }

    public function getFeelsLike(): ?string
    {
        return array_key_exists('feels_like', $this->data) && !is_array($this->data['feels_like'])
            ? (string)round($this->data['feels_like'])
            : null;
    }

    public function getMorningFeelsLike(): ?string
    {
        return array_key_exists('feels_like', $this->data)
        && is_array($this->data['feels_like'])
        && array_key_exists('morn', $this->data['feels_like'])
            ? (string)round($this->data['feels_like']['morn'])
            : null;
    }

    public function getDayFeelsLike(): ?string
    {
        return array_key_exists('feels_like', $this->data)
        && is_array($this->data['feels_like'])
        && array_key_exists('day', $this->data['feels_like'])
            ? (string)round($this->data['feels_like']['day'])
            : null;
    }

    public function getEveningFeelsLike(): ?string
    {
        return array_key_exists('feels_like', $this->data)
        && is_array($this->data['feels_like'])
        && array_key_exists('eve', $this->data['feels_like'])
            ? (string)round($this->data['feels_like']['eve'])
            : null;
    }

    public function getNightFeelsLike(): ?string
    {
        return array_key_exists('feels_like', $this->data)
        && is_array($this->data['feels_like'])
        && array_key_exists('night', $this->data['feels_like'])
            ? (string)round($this->data['feels_like']['night'])
            : null;
    }

    public function getPressure(): ?string
    {
        return array_key_exists('pressure', $this->data)
            ? (string)$this->data['pressure']
            : null;
    }

    public function getHumidity(): ?string
    {
        return array_key_exists('humidity', $this->data)
            ? (string)$this->data['humidity']
            : null;
    }

    public function getDewPoint(): ?string
    {
        return array_key_exists('dew_point', $this->data)
            ? (string)$this->data['dew_point']
            : null;
    }

    public function getCloudiness(): ?string
    {
        return array_key_exists('clouds', $this->data)
            ? (string)$this->data['clouds']
            : null;
    }

    public function getUvIndex(): ?string
    {
        return array_key_exists('uvi', $this->data)
            ? (string)$this->data['uvi']
            : null;
    }

    public function getVisibility(): ?string
    {
        return array_key_exists('visibility', $this->data)
            ? (string)$this->data['visibility']
            : null;
    }

    public function getWindSpeed(): ?string
    {
        return array_key_exists('wind_speed', $this->data)
            ? (string)$this->data['wind_speed']
            : null;
    }

    public function getWindGust(): ?string
    {
        return array_key_exists('wind_gust', $this->data)
            ? (string)$this->data['wind_gust']
            : null;
    }

    public function getWindDirection(): ?string
    {
        return array_key_exists('wind_deg', $this->data)
            ? (string)$this->data['wind_deg']
            : null;
    }

    public function getRain(): ?string
    {
        if (!array_key_exists('rain', $this->data)) {
            return null;
        }
        if (!is_array($this->data['rain'])) {
            return (string)$this->data['rain'];
        }
        return array_key_exists('1h', $this->data['rain']) ? (string)$this->data['rain']['1h'] : null;
    }

    public function getSnow(): ?string
    {
        if (!array_key_exists('snow', $this->data)) {
            return null;
        }
        if (!is_array($this->data['snow'])) {
            return (string)$this->data['snow'];
        }
        return array_key_exists('1h', $this->data['snow']) ? (string)$this->data['snow']['1h'] : null;
    }

    public function getWeatherId(): ?string
    {
        return array_key_exists('weather', $this->data)
        && is_array($this->data['weather'])
        && array_key_exists(0, $this->data['weather'])
        && is_array($this->data['weather'][0])
        && array_key_exists('id', $this->data['weather'][0])
            ? (string)$this->data['weather'][0]['id']
            : null;
    }

    public function getWeatherGroup(): ?string
    {
        return array_key_exists('weather', $this->data)
        && is_array($this->data['weather'])
        && array_key_exists(0, $this->data['weather'])
        && is_array($this->data['weather'][0])
        && array_key_exists('main', $this->data['weather'][0])
            ? (string)$this->data['weather'][0]['main']
            : null;
    }

    public function getWeatherDescription(): ?string
    {
        return array_key_exists('weather', $this->data)
        && is_array($this->data['weather'])
        && array_key_exists(0, $this->data['weather'])
        && is_array($this->data['weather'][0])
        && array_key_exists('description', $this->data['weather'][0])
            ? (string)$this->data['weather'][0]['description']
            : null;
    }

    public function getWeatherIcon(): ?string
    {
        return array_key_exists('weather', $this->data)
        && is_array($this->data['weather'])
        && array_key_exists(0, $this->data['weather'])
        && is_array($this->data['weather'][0])
        && array_key_exists('icon', $this->data['weather'][0])
            ? (string)$this->data['weather'][0]['icon']
            : null;
    }

    public function getPeriod(): string
    {
        $period = 'day';

        $sunrise = (int)str_replace(':', '', $this->getSunrise() ?? '06:00');
        $sunset = (int)str_replace(':', '', $this->getSunset() ?? '20:00');

        $now = date('Gi');

        if ($now < $sunrise || $now > $sunset) {
            $period = 'night';
        }

        return $period;
    }

    /**
     * @param array $data
     * @return array
     * @throws InvalidConfigException
     */
    public static function format(array $data): array
    {
        $weather = new static($data);

        return [
            'date' => $weather->getDate(),
            'time' => $weather->getTime(),
            'period' => $weather->getPeriod(),
            'sunrise' => $weather->getSunrise(),
            'sunset' => $weather->getSunset(),
            'temperature' => $weather->getTemperature(),
            'morningTemperature' => $weather->getMorningTemperature(),
            'dayTemperature' => $weather->getDayTemperature(),
            'eveningTemperature' => $weather->getEveningTemperature(),
            'nightTemperature' => $weather->getNightTemperature(),
            'minTemperature' => $weather->getMinTemperature(),
            'maxTemperature' => $weather->getMaxTemperature(),
            'feelsLike' => $weather->getFeelsLike(),
            'morningFeelsLike' => $weather->getMorningFeelsLike(),
            'dayFeelsLike' => $weather->getDayFeelsLike(),
            'eveningFeelsLike' => $weather->getEveningFeelsLike(),
            'nightFeelsLike' => $weather->getNightFeelsLike(),
            'pressure' => $weather->getPressure(),
            'humidity' => $weather->getHumidity(),
            'dewPoint' => $weather->getDewPoint(),
            'cloudiness' => $weather->getCloudiness(),
            'uvIndex' => $weather->getUvIndex(),
            'visibility' => $weather->getVisibility(),
            'windSpeed' => $weather->getWindSpeed(),
            'windGust' => $weather->getWindGust(),
            'windDirection' => $weather->getWindDirection(),
            'rain' => $weather->getRain(),
            'snow' => $weather->getSnow(),
            'weatherId' => $weather->getWeatherId(),
            'weatherGroup' => $weather->getWeatherGroup(),
            'weatherDescription' => $weather->getWeatherDescription(),
            'weatherIcon' => $weather->getWeatherIcon(),
        ];
    }
}
