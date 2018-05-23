<?php
/**
 * Created by PhpStorm.
 * User: magroski
 * Date: 02/10/17
 * Time: 12:25
 */

namespace Frogg\Exceptions;

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
