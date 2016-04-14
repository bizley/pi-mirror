<?php

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
     * @var string API url
     */
    public $api = 'http://api.openweathermap.org/data/2.5/weather';
    
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
     * @var integer city ID
     */
    public $city = 3081368;
    
    /**
     * @var integer cache duration
     */
    public $duration = 600;
    
    /**
     * @var Client HTTP client
     */
    private $_client;
    
    /**
     * @var array current weather conditions
     */
    private $_weather = false;
    
    /**
     * Returns HTTP client object.
     * @return Client
     */
    public function getClient()
    {
        if (empty($this->_client)) {
            $this->_client = new Client;
        }
        return $this->_client;
    }
    
    /**
     * Returns cache component.
     * @return Cache
     */
    public function getCache()
    {
        return Yii::$app->cache;
    }
    
    /**
     * Checks current weather.
     * @return mixed
     */
    public function checkWeather()
    {
        try {
            $response = $this->client->createRequest()
                    ->setMethod('get')
                    ->setUrl($this->api)
                    ->setData([
                        'id'    => $this->city,
                        'units' => $this->units,
                        'lang'  => $this->lang,
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
     */
    public function getWeather()
    {
        $this->_weather = $this->cache->get('weather');
        if ($this->_weather === false) {
            $this->_weather = $this->checkWeather();
            if ($this->_weather !== false) {
                $this->cache->set('weather', $this->_weather, $this->duration);
            }
        }
        return $this->_weather;
    }
    
    /**
     * Returns city latitude.
     * @return float
     */
    public function getLatitude()
    {
        $weather = $this->weather;
        return !empty($weather['coord']['lat']) ? $weather['coord']['lat'] : null;
    }
    
    /**
     * Returns city longitude.
     * @return float
     */
    public function getLongitude()
    {
        $weather = $this->weather;
        return !empty($weather['coord']['lon']) ? $weather['coord']['lon'] : null;
    }
    
    /**
     * Returns weather ID.
     * @return integer
     */
    public function getWeatherId()
    {
        $weather = $this->weather;
        return !empty($weather['weather'][0]['id']) ? $weather['weather'][0]['id'] : null;
    }
    
    /**
     * Returns weather main.
     * @return string
     */
    public function getWeatherMain()
    {
        $weather = $this->weather;
        return !empty($weather['weather'][0]['main']) ? $weather['weather'][0]['main'] : null;
    }
    
    /**
     * Returns weather description.
     * @return string
     */
    public function getWeatherDescription()
    {
        $weather = $this->weather;
        return !empty($weather['weather'][0]['description']) ? $weather['weather'][0]['description'] : null;
    }
    
    /**
     * Returns weather icon.
     * @return string
     */
    public function getWeatherIcon()
    {
        $weather = $this->weather;
        return !empty($weather['weather'][0]['icon']) ? $weather['weather'][0]['icon'] : null;
    }
    
    /**
     * Returns temperature.
     * @return integer
     */
    public function getTemp()
    {
        $weather = $this->weather;
        return !empty($weather['main']['temp']) ? $weather['main']['temp'] : null;
    }
    
    /**
     * Returns pressure.
     * @return integer
     */
    public function getPressure()
    {
        $weather = $this->weather;
        return !empty($weather['main']['pressure']) ? $weather['main']['pressure'] : null;
    }
    
    /**
     * Returns humidity.
     * @return integer
     */
    public function getHumidity()
    {
        $weather = $this->weather;
        return !empty($weather['main']['humidity']) ? $weather['main']['humidity'] : null;
    }
    
    /**
     * Returns temp_min.
     * @return integer
     */
    public function getTempMin()
    {
        $weather = $this->weather;
        return !empty($weather['main']['temp_min']) ? $weather['main']['temp_min'] : null;
    }
    
    /**
     * Returns temp_max.
     * @return integer
     */
    public function getTempMax()
    {
        $weather = $this->weather;
        return !empty($weather['main']['temp_max']) ? $weather['main']['temp_max'] : null;
    }
    
    /**
     * Returns wind speed.
     * @return float
     */
    public function getWindSpeed()
    {
        $weather = $this->weather;
        return !empty($weather['wind']['speed']) ? $weather['wind']['speed'] : null;
    }
    
    /**
     * Returns wind deg.
     * @return integer
     */
    public function getWindDeg()
    {
        $weather = $this->weather;
        return !empty($weather['wind']['deg']) ? $weather['wind']['deg'] : null;
    }
    
    /**
     * Returns sunrise.
     * @return integer
     */
    public function getSunrise()
    {
        $weather = $this->weather;
        return !empty($weather['sys']['sunrise']) ? $weather['sys']['sunrise'] : null;
    }
    
    /**
     * Returns sunset.
     * @return integer
     */
    public function getSunset()
    {
        $weather = $this->weather;
        return !empty($weather['sys']['sunset']) ? $weather['sys']['sunset'] : null;
    }
}