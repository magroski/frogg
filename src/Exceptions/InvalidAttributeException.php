<?php
/**
 * Created by PhpStorm.
 * User: magroski
 * Date: 02/10/17
 * Time: 12:25
 */

namespace Frogg\Exceptions;

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