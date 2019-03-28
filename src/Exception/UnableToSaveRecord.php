<?php

declare(strict_types=1);

namespace Frogg\Exception;

/**
 * This exception is for situations where one record, of any type, can not be saved at somewhere.
 */
class UnableToSaveRecord extends \InvalidArgumentException
{
}
