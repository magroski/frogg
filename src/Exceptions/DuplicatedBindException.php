<?php
/**
 * Created by PhpStorm.
 * User: dreanmer
 * Date: 29/12/17
 * Time: 11:53
 */

namespace Frogg\Exceptions;

class DuplicatedBindException extends \Exception
{
    /**
     * DuplicatedBindException constructor.
     *
     * @param string $bind
     */
    public function __construct($bind)
    {
        parent::__construct('`'.$bind.'` bind was already added to this query, to overwriting it (skiping this check), pass bindType [`skipBindCheck` => true]');
    }
}