<?php

declare(strict_types=1);

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
     * Returns authorised google client.
     * @return Google_Client
     * @throws \Google_Exception
     */
    public function getClient(): Google_Client
    {
        if ($this->_client === null) {
            $this->_client = new Google_Client();
            $this->_client->setAuthConfig(Yii::getAlias($this->_authConfig));
            $this->_client->setApplicationName($this->_applicationName);
            $this->_client->setScopes([Google_Service_Calendar::CALENDAR_READONLY]);
            $this->_client->setAccessType('offline');
            $this->_client->setAccessToken(Json::decode(file_get_contents(Yii::getAlias($this->_accessToken))));
        }

        return $this->getRefreshedClient();
    }
    
    /**
     * Returns refresh token.
     * @return string
     */
    protected function getRefreshToken(): string
    {
        $token = $this->_client->getRefreshToken() ?: null;

        if ($token === null) {
            $token = Json::decode(file_get_contents(Yii::getAlias($this->_refreshToken)));
        }

        return $token;
    }

    /**
     * @var Google_Client|null authorised Google client
     */
    private $_client;

    /**
     * Returns authorised Google client with refreshed token.
     * @return Google_Client
     */
    protected function getRefreshedClient(): Google_Client
    {
        if ($this->_client === null) {
            return null;
        }

        if ($this->_client->isAccessTokenExpired()) {
            $this->_client->refreshToken($this->getRefreshToken());

            file_put_contents(
                Yii::getAlias($this->_accessToken),
                Json::encode($this->_client->getAccessToken())
            );
        }

        return $this->_client;
    }

    /**
     * @var Google_Service_Calendar|null authorised calendar service
     */
    private $_service;

    /**
     * Returns authorised calendar service.
     * @return Google_Service_Calendar
     * @throws \Google_Exception
     */
    public function getService(): Google_Service_Calendar
    {
        if ($this->_service === null) {
            $this->_service = new Google_Service_Calendar($this->getClient());
        }

        return $this->_service;
    }

    /**
     * Return calendars list.
     * @return Google_Service_Calendar_CalendarList
     * @throws \Google_Exception
     */
    public function getCalendarsList(): Google_Service_Calendar_CalendarList
    {
        return $this->getService()->calendarList->listCalendarList();
    }

    /**
     * Returns calendar events.
     * @param string $calendarId
     * @param int $limit
     * @return Google_Service_Calendar_Events
     * @throws \Google_Exception
     */
    public function getEvents(string $calendarId = 'primary', int $limit = 10): Google_Service_Calendar_Events
    {
        return $this->getService()->events->listEvents($calendarId, [
            'maxResults' => $limit,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
        ]);
    }
}
