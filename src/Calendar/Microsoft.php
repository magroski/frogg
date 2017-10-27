<?php

namespace Frogg\Calendar;

use League\OAuth2\Client\Provider\GenericProvider;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\Event;

class Microsoft
{

    /**
     *
     * @var GenericProvider
     */
    protected $oauth2Client;
    /**
     *
     * @var Graph
     */
    protected $graph;

    protected $key;
    protected $secret;
    protected $callbackUrl;
    protected $token;
    protected $scopes;

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

        $this->key         = $configs['key'];
        $this->secret      = $configs['secret'];
        $this->callbackUrl = $configs['callback'];
        $this->scopes      = 'offline_access Calendars.ReadWrite User.Read';

        $this->oauth2Client = new GenericProvider([
            'clientId'                => $this->key,
            'clientSecret'            => $this->secret,
            'redirectUri'             => $this->callbackUrl,
            'urlAuthorize'            => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'urlAccessToken'          => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'urlResourceOwnerDetails' => '',
            'scopes'                  => $this->scopes,
        ]);

        $this->graph = new Graph();
    }

    public function getAuthUrl()
    {
        $data = [
            'client_id'     => $this->key,
            'redirect_uri'  => $this->callbackUrl,
            'response_type' => 'code',
            'scope'         => $this->scopes,
        ];

        return 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?'.http_build_query($data);
    }

    public function login()
    {
        if (!isset($_GET['code'])) return false;

        if ($_GET['code']) {
            $accessToken = $this->oauth2Client->getAccessToken('authorization_code', [
                'code' => $_GET['code'],
            ]);

            return $this->setAccessToken([
                'access_token'  => $accessToken->getToken(),
                'expires'       => $accessToken->getExpires(),
                'refresh_token' => $accessToken->getRefreshToken(),
            ]);
        }

        return false;
    }

    public function setAccessToken($token)
    {
        $this->token = $token;

        if ($this->token['expires'] - time() < 30) {
            $token = $this->oauth2Client->getAccessToken('refresh_token', [
                'refresh_token' => $this->token['refresh_token'],
            ]);

            $this->token = [
                'access_token'  => $token->getToken(),
                'expires'       => $token->getExpires(),
                'refresh_token' => $token->getRefreshToken(),
            ];
        }

        $this->graph->setAccessToken($this->token['access_token']);

        return $this->token;
    }

    public function getBusySlots($params)
    {
        $start = date('Y-m-d\TH:i:s', $params['from']);
        $end   = date('Y-m-d\TH:i:s', $params['to']);

        $response = $this->graph->createRequest('GET', '/me/calendarview'."?startDateTime={$start}&endDateTime={$end}")->execute();
        $events   = $response->getBody()['value'];

        $slots = [];
        foreach ($events as $event) {
            $slots[] = [
                'start' => strtotime($event['start']['dateTime']),
                'end'   => strtotime($event['end']['dateTime']),
            ];
        }

        return $slots;
    }

    /*
    'summary'		=> 'event summary 3',
    'description'	=> 'event description',
    'start'			=> $selectedSlot['start'],
    'end'			=> $selectedSlot['end'],
    */
    public function addEvent($params)
    {
        $data  = [
            'Subject' => $params['summary'],
            'Body'    => [
                'ContentType' => 'HTML',
                'Content'     => $params['description'],
            ],
            'Start'   => [
                'DateTime' => date('Y-m-d\TH:i:s', $params['start']),
                'TimeZone' => 'Etc/GMT',
            ],
            'End'     => [
                'DateTime' => date('Y-m-d\TH:i:s', $params['end']),
                'TimeZone' => 'Etc/GMT',
            ],
        ];
        $event = $this->graph->createRequest("POST", '/me/events')
            ->attachBody($data)
            ->setReturnType(Event::class)
            ->execute();

        return $event->getId();
    }

    /*
    'summary'		=> 'event summary 3',
    'description'	=> 'event description',
    'start'			=> $selectedSlot['start'],
    'end'			=> $selectedSlot['end'],
    */
    public function updateEvent($params)
    {
        $data  = [
            'Subject' => $params['summary'],
            'Body'    => [
                'ContentType' => 'HTML',
                'Content'     => $params['description'],
            ],
            'Start'   => [
                'DateTime' => date('Y-m-d\TH:i:s', $params['start']),
                'TimeZone' => 'Etc/GMT',
            ],
            'End'     => [
                'DateTime' => date('Y-m-d\TH:i:s', $params['end']),
                'TimeZone' => 'Etc/GMT',
            ],
        ];
        $event = $this->graph->createRequest("PATCH", '/me/events/'.$params['eventId'])
            ->attachBody($data)
            ->setReturnType(Event::class)
            ->execute();

        return $event->getId();
    }

}