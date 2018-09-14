<?php

namespace Frogg\Exception;

class InvalidAttributeException extends \Exception
{
    /**
     * InvalidAttributeException constructor.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct('Attribute \''.$message.'\' does not exist on the current object');
    }
}
