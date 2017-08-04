<?php

namespace Frogg\Calendar;

class Google extends Base
{

    /**
     *
     * @var \Google_Client
     */
    protected $googleApi;
    protected $calendarService = null;

    /**
     *
     * @param array $configs - ['key' => '', 'secret' => '', 'callback' => 'CALLBACK URL']
     *
     * @throws \Exception
     */
    public function __construct(array $configs)
    {
        if (!isset($configs['key']) || !isset($configs['secret'])) {
            throw new \Exception('The configuration array does not contain the element(s) "key" and/or "secret"');
        }

        $this->callbackUrl = $configs['callback'];

        $this->googleApi = new \Google_Client([
            'client_id'     => $configs['key'],
            'client_secret' => $configs['secret'],
        ]);
        $this->googleApi->setRedirectUri($this->callbackUrl);
        $this->googleApi->setIncludeGrantedScopes(true);
        $this->googleApi->setAccessType('offline');
    }

    public function getAuthUrl()
    {
        return $this->googleApi->createAuthUrl([\Google_Service_Calendar::CALENDAR]);
    }

    public function login()
    {
        $accessToken = $this->googleApi->fetchAccessTokenWithAuthCode($_GET['code']);

        return $this->setAccessToken($accessToken);
    }

    public function setAccessToken($accessToken)
    {
        if (!isset($accessToken['created']) && isset($accessToken['expires'])) {
            $accessToken['created']    = time();
            $accessToken['expires_in'] = $accessToken['expires'] - $accessToken['created'];
        }

        $this->googleApi->setAccessToken($accessToken);
        if ($this->googleApi->isAccessTokenExpired()) {
            $accessToken = $this->googleApi->fetchAccessTokenWithRefreshToken($this->googleApi->getRefreshToken());
        }

        $tokenArray = [
            'access_token'  => $accessToken['access_token'],
            'expires'       => $accessToken['created'] + $accessToken['expires_in'],
            'refresh_token' => false,
        ];

        if (isset($accessToken['refresh_token'])) {
            $tokenArray['refresh_token'] = $accessToken['refresh_token'];
        }

        return $tokenArray;
    }

    public function getBusySlots($params)
    {
        $service = $this->getCalendarService();

        $calendarList = $service->calendarList->listCalendarList();

        // Make our Freebusy request
        $freebusy = new \Google_Service_Calendar_FreeBusyRequest();
        $freebusy->setTimeMin(gmdate(\DateTime::ISO8601, $params['from']));
        $freebusy->setTimeMax(gmdate(\DateTime::ISO8601, $params['to']));
        $freebusy->setTimeZone('Etc/UTC');
        $freebusy->setItems([['id' => 'primary']]);
        $createdReq = $service->freebusy->query($freebusy);

        $busyArray = [];
        foreach ($createdReq->getCalendars() as $calendar) {
            foreach ($calendar->getBusy() as $busy) {
                $busyArray[] = [
                    'start' => strtotime($busy->start),
                    'end'   => strtotime($busy->end),
                ];
            }
        }

        return $busyArray;
    }

    public function addEvent($params)
    {
        $service = $this->getCalendarService();

        $event = new \Google_Service_Calendar_Event([
            'summary'     => $params['summary'],
            'description' => $params['description'],
            'start'       => [
                'dateTime' => gmdate(\DateTime::ISO8601, $params['start']),
                'timeZone' => 'Etc/UTC',
            ],
            'end'         => [
                'dateTime' => gmdate(\DateTime::ISO8601, $params['end']),
                'timeZone' => 'Etc/UTC',
            ],
        ]);

        $event = $service->events->insert('primary', $event);

        return $event->getId();
    }

    public function updateEvent($params)
    {
        $service = $this->getCalendarService();

        $event = new \Google_Service_Calendar_Event([
            'summary'     => $params['summary'],
            'description' => $params['description'],
            'start'       => [
                'dateTime' => gmdate(\DateTime::ISO8601, $params['start']),
                'timeZone' => 'Etc/UTC',
            ],
            'end'         => [
                'dateTime' => gmdate(\DateTime::ISO8601, $params['end']),
                'timeZone' => 'Etc/UTC',
            ],
        ]);

        $event = $service->events->update('primary', $params['eventId'], $event);

        return $event->getId();
    }

    public function getCalendarService()
    {
        if ($this->calendarService === null) {
            $this->calendarService = new \Google_Service_Calendar($this->googleApi);
        }

        return $this->calendarService;
    }

}