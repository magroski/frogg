<?php
declare(strict_types=1);

namespace Frogg\Services\Google\ValueObject;

use Frogg\Services\Google\DistanceMatrixAPI;

class DistanceMatrixDestination
{
    /** @var DistanceMatrixOrigin */
    private $origin;

    /** @var array */
    private $data;

    public function __construct(DistanceMatrixOrigin $origin, array $data)
    {
        $this->origin = $origin;
        $this->data   = $data;
    }

    public function getDistanceValue(bool $convertToMiles = true) : ?int
    {
        if (!isset($this->data['distance']['value'])) {
            return null;
        }

        if ($convertToMiles) {
            return $this->data['distance']['value'] * DistanceMatrixAPI::KM_TO_MILE;
        }

        return $this->data['distance']['value'];
    }

    public function getDistanceText() : ?string
    {
        if (!isset($this->data['distance']['text'])) {
            return null;
        }

        return $this->data['distance']['text'];
    }

    public function getDurationValue() : ?int
    {
        if (!isset($this->data['duration']['value'])) {
            return null;
        }

        return $this->data['duration']['value'];
    }

    public function getDurationText() : ?string
    {
        if (!isset($this->data['duration']['text'])) {
            return null;
        }

        return $this->data['duration']['text'];
    }

    public function getOrigin() : DistanceMatrixOrigin
    {
        return $this->origin;
    }
}
