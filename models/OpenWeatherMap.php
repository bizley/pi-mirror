<?php

declare(strict_types=1);

namespace app\models;

use Exception;
use Yii;
use yii\base\Component;
use yii\caching\Cache;
use yii\httpclient\Client;

/**
 * OpenWeatherMap
 * @author Bizley
 * 
 * @property Client $client
 */
class OpenWeatherMap extends Component
{
    /**
     * @var string API current weather URL
     */
    public $current_url = 'https://api.openweathermap.org/data/2.5/weather';
    
    /**
     * @var string API forecast weather URL
     */
    public $forecast_url = 'https://api.openweathermap.org/data/2.5/forecast';
    
    /**
     * @var string API key
     */
    public $key;
    
    /**
     * @var string units type
     */
    public $units = 'metric';
    
    /**
     * @var string language code
     */
    public $lang = 'pl';
    
    /**
     * @var int city ID
     */
    public $city = 3081368;
    
    /**
     * @var int cache duration for current weather
     */
    public $current_duration = 600;
    
    /**
     * @var int cache duration for forecast weather
     */
    public $forecast_duration = 1800;
    
    /**
     * @var Client HTTP client
     */
    private $_client;
    
    /**
     * @var array current weather conditions
     */
    private $_weather;
    
    /**
     * @var array forecast weather conditions
     */
    private $_forecast;
    
    /**
     * Returns HTTP client object.
     * @return Client
     */
    public function getClient(): Client
    {
        if ($this->_client === null) {
            $this->_client = new Client();
        }

        return $this->_client;
    }
    
    /**
     * Returns cache component.
     * @return Cache
     */
    public function getCache(): Cache
    {
        return Yii::$app->cache;
    }
    
    /**
     * Checks current weather.
     * @return array|bool
     */
    public function checkWeather()
    {
        try {
            $response = $this->client->createRequest()
                ->setMethod('get')
                ->setUrl($this->current_url)
                ->setData([
                    'id' => $this->city,
                    'units' => $this->units,
                    'lang' => $this->lang,
                    'APPID' => $this->key
                ])
                ->send();

            if (!$response->isOk) {
                throw new Exception($response->data);
            }

            return $response->data;

        } catch (Exception $e) {
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
        $this->_weather = $this->cache->get('weather');

        if ($this->_weather === false) {
            $this->_weather = $this->checkWeather();

            if ($this->_weather !== false) {
                $this->cache->set('weather', $this->_weather, $this->current_duration);
            }
        }

        return $this->_weather;
    }
    
    /**
     * Returns city latitude.
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        $weather = $this->getWeather();

        return !empty($weather['coord']['lat']) ? $weather['coord']['lat'] : null;
    }
    
    /**
     * Returns city longitude.
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        $weather = $this->getWeather();

        return !empty($weather['coord']['lon']) ? $weather['coord']['lon'] : null;
    }
    
    /**
     * Returns weather ID.
     * @return int|null
     */
    public function getWeatherId(): ?int
    {
        $weather = $this->getWeather();

        return !empty($weather['weather'][0]['id']) ? $weather['weather'][0]['id'] : null;
    }
    
    /**
     * Returns weather main.
     * @return string|null
     */
    public function getWeatherMain(): ?string
    {
        $weather = $this->getWeather();

        return !empty($weather['weather'][0]['main']) ? $weather['weather'][0]['main'] : null;
    }
    
    /**
     * Returns weather description.
     * @return string|null
     */
    public function getWeatherDescription(): ?string
    {
        $weather = $this->getWeather();

        return !empty($weather['weather'][0]['description']) ? $weather['weather'][0]['description'] : null;
    }
    
    /**
     * Returns weather icon.
     * @return string|null
     */
    public function getWeatherIcon(): ?string
    {
        $weather = $this->getWeather();

        return !empty($weather['weather'][0]['icon']) ? $weather['weather'][0]['icon'] : null;
    }
    
    /**
     * Returns temperature.
     * @return int|null
     */
    public function getTemp(): ?int
    {
        $weather = $this->getWeather();

        return !empty($weather['main']['temp']) ? (int) round($weather['main']['temp']) : null;
    }
    
    /**
     * Returns pressure.
     * @return int|null
     */
    public function getPressure(): ?int
    {
        $weather = $this->getWeather();

        return !empty($weather['main']['pressure']) ? (int) round($weather['main']['pressure']) : null;
    }
    
    /**
     * Returns humidity.
     * @return int|null
     */
    public function getHumidity(): ?int
    {
        $weather = $this->getWeather();

        return !empty($weather['main']['humidity']) ? $weather['main']['humidity'] : null;
    }
    
    /**
     * Returns minimal temperature.
     * @return int|null
     */
    public function getTempMin(): ?int
    {
        $weather = $this->getWeather();

        return !empty($weather['main']['temp_min']) ? (int) round($weather['main']['temp_min']) : null;
    }
    
    /**
     * Returns maximal temperature.
     * @return int|null
     */
    public function getTempMax(): ?int
    {
        $weather = $this->getWeather();

        return !empty($weather['main']['temp_max']) ? (int) round($weather['main']['temp_max']) : null;
    }
    
    /**
     * Returns wind speed.
     * @return float|null
     */
    public function getWindSpeed(): ?float
    {
        $weather = $this->getWeather();

        return !empty($weather['wind']['speed']) ? $weather['wind']['speed'] : null;
    }
    
    /**
     * Returns wind deg.
     * @return int|null
     */
    public function getWindDeg(): ?int
    {
        $weather = $this->getWeather();

        return !empty($weather['wind']['deg']) ? $weather['wind']['deg'] : null;
    }
    
    /**
     * Returns sunrise.
     * @return int|null
     */
    public function getSunrise(): ?int
    {
        $weather = $this->getWeather();

        return !empty($weather['sys']['sunrise']) ? $weather['sys']['sunrise'] : null;
    }
    
    /**
     * Returns sunset.
     * @return int|null
     */
    public function getSunset(): ?int
    {
        $weather = $this->getWeather();

        return !empty($weather['sys']['sunset']) ? $weather['sys']['sunset'] : null;
    }
    
    /**
     * Returns clouds.
     * @return int|null
     */
    public function getClouds(): ?int
    {
        $weather = $this->getWeather();

        return !empty($weather['clouds']['all']) ? $weather['clouds']['all'] : null;
    }
    
    /**
     * Checks forecast weather.
     * @return bool|array
     */
    public function checkForecast()
    {
        try {
            $response = $this->client->createRequest()
                ->setMethod('get')
                ->setUrl($this->forecast_url)
                ->setData([
                    'id'    => $this->city,
                    'units' => $this->units,
                    'lang'  => $this->lang,
                    'APPID' => $this->key,
                ])
                ->send();

            if (!$response->isOk) {
                throw new Exception($response->data);
            }

            return $response->data;

        } catch (Exception $e) {
            Yii::error($e->getMessage());
        }

        return false;
    }
    
    /**
     * Returns cached forecast weather conditions.
     * @return bool|array
     */
    public function getForecast()
    {
        $this->_forecast = $this->cache->get('forecast');

        if ($this->_forecast === false) {
            $this->_forecast = $this->checkForecast();

            if ($this->_forecast !== false) {
                $this->cache->set('forecast', $this->_forecast, $this->forecast_duration);
            }
        }

        return $this->_forecast;
    }
    
    /**
     * Returns forecast weather for the rest of the day (6:00, 12:00, 18:00).
     * Skips current time > hour - 30 minutes.
     * @return array
     */
    public function getToday(): array
    {
        $list = [];
        $forecast = $this->getForecast();

        if (!empty($forecast['list'])) {
            $hour = date('Gm');

            if ($hour < 1730) {
                $start = '06';

                while ($hour >= ((int) $start - 1) . '30') {
                    $start = (int) $start + 6;
                }

                $date = date('Y-m-d');

                foreach ($forecast['list'] as $weather) {
                    if (strpos($weather['dt_txt'], $date) !== 0) {
                        break;
                    }

                    if (strpos($weather['dt_txt'], $date . ' ' . $start) === 0) {
                        $list[$start . ':00'] = [
                            'weatherId' => !empty($weather['weather'][0]['id']) ? $weather['weather'][0]['id'] : null,
                            'temp' => !empty($weather['main']['temp']) ? round($weather['main']['temp']) : null,
                        ];

                        $start = (int) $start + 6;
                    }
                }
            }
        }

        return $list;
    }
    
    /**
     * Returns forecast weather for the next day (6:00, 12:00, 18:00).
     * @return array
     */
    public function getTomorrow(): array
    {
        $list = [];
        $forecast = $this->getForecast();

        if (!empty($forecast['list'])) {
            $start = '06';
            $startDate = date('Y-m-d', strtotime('+1 day'));
            $dayParsed = false;

            foreach ($forecast['list'] as $weather) {
                if ($dayParsed && strpos($weather['dt_txt'], $startDate) !== 0) {
                    break;
                }

                if (strpos($weather['dt_txt'], $startDate . ' ' . $start) === 0) {
                    $dayParsed = true;

                    $list[$start . ':00'] = [
                        'weatherId' => !empty($weather['weather'][0]['id']) ? $weather['weather'][0]['id'] : null,
                        'temp'      => !empty($weather['main']['temp']) ? round($weather['main']['temp']) : null,
                    ];

                    $start = (int) $start + 6;
                    if ($start > 18) {
                        break;
                    }
                }
            }
        }

        return $list;
    }
    
    /**
     * Returns forecast weather for the day after tomorrow (6:00, 12:00, 18:00).
     * @return array
     */
    public function getOvermorrow(): array
    {
        $list = [];
        $forecast = $this->getForecast();

        if (!empty($forecast['list'])) {
            $start = '06';
            $startDate = date('Y-m-d', strtotime('+2 days'));
            $dayParsed = false;

            foreach ($forecast['list'] as $weather) {
                if ($dayParsed && strpos($weather['dt_txt'], $startDate) !== 0) {
                    break;
                }

                if (strpos($weather['dt_txt'], $startDate . ' ' . $start) === 0) {
                    $dayParsed    = true;

                    $list[$start . ':00'] = [
                        'weatherId' => !empty($weather['weather'][0]['id']) ? $weather['weather'][0]['id'] : null,
                        'temp'      => !empty($weather['main']['temp']) ? round($weather['main']['temp']) : null,
                    ];

                    $start = (int) $start + 6;
                    if ($start > 18) {
                        break;
                    }
                }
            }
        }

        return $list;
    }
}
