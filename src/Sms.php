<?php

namespace Frogg;

use Frogg\Sms\Twilio;
use Frogg\Sms\Tww;

/** @deprecated  */
class Sms
{

    protected $gateway;

    /**
     * Get the appropriate SMS gateway object
     *
     * @param array  $credentials Gateway client credentials
     * @param string $cc          Country code ('us', 'br', 'jp', ...)
     */
    public function __construct(array $credentials, string $cc)
    {
        $cc = strtolower($cc);
        switch ($cc) {
            case 'us':
                $this->gateway = new Twilio($credentials);
                break;
            case 'br':
                $this->gateway = new Tww($credentials);
                break;
            default:
                $this->gateway = new Twilio($credentials);
                break;
        }
    }

    /**
     * Send a sms
     *
     * @param array $data ['id', 'text', 'to', 'from'] Key-value array
     *                    * 'id'     - recipient unique identifier
     *                    * 'text' - message that will be sent
     *                    * 'to'     - number without country code,
     *                    * 'from' - number that will send the message,
     *
     * @return bool
     * @throws \Exception
     */
    public function send(array $data)
    {
        return $this->gateway->send($data);
    }

}
