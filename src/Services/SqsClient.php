<?php

namespace Frogg\Services;

use Aws\Credentials\Credentials;
use Aws\Sqs\SqsClient as AmazonSqsClient;

class SqsClient
{
    protected $sqsClient;
    protected $queueUrl;

    /**
     * SqsClient constructor.
     *
     * @param array $config Config array expect the following keys:
     *                      AWS_ACCESS_KEY
     *                      AWS_SECRET_KEY
     *                      AWS_SQS_REGION
     *                      AWS_SQS_QUEUE_URL
     */
    public function __construct(array $config)
    {
        $credentialsInst = new Credentials(
            $config['AWS_ACCESS_KEY'],
            $config['AWS_SECRET_KEY']
        );

        $this->sqsClient = new AmazonSqsClient([
            'credentials' => $credentialsInst,
            'region'      => $config['AWS_SQS_REGION'],
            'version'     => 'latest',
        ]);
        $this->queueUrl  = $config['AWS_SQS_QUEUE_URL'];
    }

    /**
     * Sends a message to AWS SQS service
     *
     * @param string $message The content of the message, must be text or a json_encoded array
     */
    public function sendMessage(string $message)
    {
        $this->sqsClient->sendMessage([
            'MessageBody' => $message,
            'QueueUrl'    => $this->queueUrl,
        ]);
    }

    /**
     * Sends a message to AWS SQS service
     *
     * @param string $message The content of the message, must be text or a json_encoded array
     * @param int    $delay   Delay in seconds. Min: 0 Max: 900 (15 minutes)
     */
    public function sendDelayedMessage(string $message, int $delay = 0)
    {
        $delay = max(0, $delay);
        $delay = min(900, $delay);
        $this->sqsClient->sendMessage([
            'DelaySeconds' => $delay,
            'MessageBody'  => $message,
            'QueueUrl'     => $this->queueUrl,
        ]);
    }
}
