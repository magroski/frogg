<?php

namespace Frogg\Sms;

use Twilio\Rest\Client;

class Twilio
{

    private $client;

    /**
     * Constructor
     *
     * @param array $credentials Twilio credentials in the following format ['TWILIO_ACCOUNT_ID'=>x,'TWILIO_AUTH_TOKEN'=>y]
     */
    public function __construct(array $credentials)
    {
        $this->client = new Client($credentials['TWILIO_ACCOUNT_ID'], $credentials['TWILIO_AUTH_TOKEN']);
    }

    /**
     * Send a sms using Twilio Rest API
     *
     * @param array $data ['text', 'to', 'from'] Key-value array
     *                    * 'text' - message that will be sent
     *                    * 'to'     - number without country code,
     *                    * 'from' - number that will send the message,
     *
     * @return boolean
     */
    public function send(array $data)
    {
        $text = self::sanitizeText($data['text']);
        $to   = preg_replace("/[(,),\-,\s]/", "", $data['to']);
        $from = preg_replace("/[(,),\-,\s]/", "", $data['from']);

        try {
            $message = $this->client->messages->create($to, ['from' => $from, 'body' => $text]);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Swap incompatible characters with compatible ones.
     * Ex: Swaps [ã | à | á] with [a]
     *
     * @param string $text Text to be sanitized
     *
     * @return string Sanitized text.
     */
    public static function sanitizeText(string $text)
    {
        $unwanted_array = ['Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A',
                           'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O',
                           'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a',
                           'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i',
                           'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
                           'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y'];

        return strtr($text, $unwanted_array);
    }
}