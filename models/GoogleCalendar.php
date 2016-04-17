<?php

namespace app\models;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_CalendarList;
use Google_Service_Calendar_Events;
use Yii;
use yii\base\Component;
use yii\helpers\Json;

/**
 * GoogleCalendar
 * @author Bizley
 * 
 * @property Google_Client $client
 * @property Google_Service_Calendar $service
 * @property Google_Service_Calendar_CalendarList $calendarsList
 * @property Google_Service_Calendar_Events $events
 */
class GoogleCalendar extends Component
{
    
    /**
     * @var string Yii alias for google.json file path
     */
    protected $_authConfig = '@app/config/google.json';
    
    /**
     * @var string Yii alias for token.json file path
     */
    protected $_accessToken = '@app/config/token.json';
    
    /**
     * @var string Yii alias for refresh.json file path
     */
    protected $_refreshToken = '@app/config/refresh.json';
    
    /**
     * @var string application name
     */
    protected $_applicationName = 'PI Mirror';
    
    /**
     * @var Google_Client authorised google client
     */
    private $_client;
    
    /**
     * @var Google_Service_Calendar authorised calendar service
     */
    private $_service;
    
    /**
     * Returns authorised google client.
     * @return Google_Client
     */
    public function getClient()
    {
        if (empty($this->_client)) {
            $this->_client = new Google_Client;
            $this->_client->setAuthConfig(Yii::getAlias($this->_authConfig));
            $this->_client->setApplicationName($this->_applicationName);
            $this->_client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
            $this->_client->setAccessType('offline');
            $this->_client->setAccessToken(Json::decode(file_get_contents(Yii::getAlias($this->_accessToken))));
        }
        return $this->_getRefreshedClient();
    }
    
    /**
     * Returns refresh token.
     * @return string
     */
    protected function _getRefreshToken()
    {
        $token = $this->_client->getRefreshToken() ?: null;
        if (empty($token)) {
            $token = Json::decode(file_get_contents(Yii::getAlias($this->_refreshToken)));
        }
        return $token;
    }
    
    /**
     * Returns authorised google client with refreshed token.
     * @return Google_Client
     */
    protected function _getRefreshedClient()
    {
        if (empty($this->_client)) {
            return null;
        }
        if ($this->_client->isAccessTokenExpired()) {
            $this->_client->refreshToken($this->_getRefreshToken());
            file_put_contents(Yii::getAlias($this->_accessToken), Json::encode($this->_client->getAccessToken()));
        }
        return $this->_client;
    }
    
    /**
     * Returns authorised calendar service.
     * @return Google_Service_Calendar
     */
    public function getService()
    {
        if (empty($this->_service)) {
            $this->_service = new Google_Service_Calendar($this->client);
        }
        return $this->_service;
    }
    
    /**
     * Return calendars list.
     * @return Google_Service_Calendar_CalendarList
     */
    public function getCalendarsList()
    {
        return $this->service->calendarList->listCalendarList();
    }
    
    /**
     * Returns calendar events.
     * @param string $calendarId
     * @param integer $limit
     * @return Google_Service_Calendar_Events
     */
    public function getEvents($calendarId = 'primary', $limit = 10)
    {
        return $this->service->events->listEvents($calendarId, [
            'maxResults'   => $limit,
            'orderBy'      => 'startTime',
            'singleEvents' => true,
            'timeMin'      => date('c'),
        ]);
    }
}