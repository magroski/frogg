<?php
declare(strict_types=1);

namespace Frogg\Services\Google\Exception;

use RuntimeException;
use Throwable;

class DistanceMatrixLocationInvalid extends RuntimeException implements Throwable
{
}
