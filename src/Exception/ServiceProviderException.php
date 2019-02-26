<?php

namespace Frogg\Exception;

class ServiceProviderException extends \Exception
{
    /**
     * InvalidAttributeException constructor.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
