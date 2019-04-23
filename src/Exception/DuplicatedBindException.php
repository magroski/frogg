<?php

namespace Frogg\Exception;

class DuplicatedBindException extends \Exception
{
    /**
     * DuplicatedBindException constructor.
     *
     * @param string $bind
     */
    public function __construct($bind)
    {
        parent::__construct('`' . $bind . '` bind was already added to this query, to overwriting it (skiping this check), pass bindType [`skipBindCheck` => true]');
    }
}
