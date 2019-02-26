<?php

namespace Frogg;

use Aws\Credentials\Credentials;
use Aws\Ses\SesClient;

class Mail
{

    protected $fromName;
    protected $fromEmail;
    protected $bounceAddress;
    protected $ses;
    protected $t;

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

    public function send($subject, $body, $to, $text = false)
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

        $status = $this->ses->sendEmail($email_data);

        return $status;
    }

}
