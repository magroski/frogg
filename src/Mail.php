<?php

namespace Frogg;

use Aws\Credentials\Credentials;
use Aws\Result;
use Aws\Ses\SesClient;

/**
 * @deprecated Use magroski/simple-ses
 */
class Mail
{

    protected string $fromName;
    protected string $fromEmail;
    protected string $bounceAddress;
    protected SesClient $ses;
    /**
     * @var mixed
     */
    protected $t;

    /**
     * @param array<string> $credentials
     */
    public function __construct(array $credentials)
    {
        $this->fromName      = $credentials['fromName'];
        $this->fromEmail     = $credentials['fromEmail'];
        $this->bounceAddress = $credentials['bounceAddress'];

        $credentialsInst = new Credentials(
            $credentials['AWS_ACCESS_KEY'],
            $credentials['AWS_SECRET_KEY']
        );

        $this->ses = new SesClient([
            'credentials' => $credentialsInst,
            'region'      => $credentials['AWS_SES_REGION'],
            'version'     => 'latest',
        ]);
    }

    public function setBounceAddress(string $bounceAddress) : void
    {
        $this->bounceAddress = $bounceAddress;
    }

    /**
     * @param  array<string>|string      $to
     * @param  string|false      $text
     */
    public function send(string $subject, string $body, $to, $text = false) : Result
    {
        if (!is_array($to)) {
            $to = [$to];
        }
        $body = preg_replace('/[\s\t\n]+/', ' ', $body);

        foreach ($to as $key => $value) {
            $to[$key] = trim($value);
        }

        $fromName   = $this->fromName;
        $fromEmail  = $this->fromEmail;
        $email_data = [
            'Source'      => "$fromName <$fromEmail>",
            'Destination' => [
                'ToAddresses'  => $to,
                'CcAddresses'  => [],
                'BccAddresses' => [],
            ],
            'Message'     => [
                'Subject' => [
                    'Data'    => $subject,
                    'Charset' => 'utf-8',
                ],
                'Body'    => [
                    'Html' => [
                        'Data'    => $body,
                        'Charset' => 'utf-8',
                    ],
                ],
            ],
        ];

        if (!empty($this->bounceAddress)) {
            $email_data['ReturnPath'] = $this->bounceAddress;
        }

        if ($text) {
            $email_data['Message']['Body']['Text'] = [
                'Data'    => $text,
                'Charset' => 'utf-8',
            ];
        }

        return $this->ses->sendEmail($email_data);
    }

}
