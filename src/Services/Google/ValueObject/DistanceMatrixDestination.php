<?php
declare(strict_types=1);

namespace Frogg\Services\Google\ValueObject;

use Frogg\Services\Google\DistanceMatrixAPI;

class DistanceMatrixDestination
{
    /** @var DistanceMatrixOrigin */
    private $origin;

    /** @var string */
    private $address;

    /** @var array */
    private $data;

    public function __construct(DistanceMatrixOrigin $origin, string $address, array $data)
    {
        $this->origin  = $origin;
        $this->address = $address;
        $this->data    = $data;
    }

    public function getAddress() : string
    {
        return $this->address;
    }

    public function getDistanceValue(bool $convertToMiles = true) : ?float
    {
        if (!isset($this->data['distance']['value'])) {
            return null;
        }

        if ($convertToMiles) {
            return $this->data['distance']['value'] * DistanceMatrixAPI::METER_TO_MILE;
        }

        return $this->data['distance']['value'];
    }

    public function getDistanceText(bool $convertToMiles = true) : ?string
    {
        if (!isset($this->data['distance']['text'])) {
            return null;
        }

        if ($convertToMiles) {
            return round($this->data['distance']['value'] * DistanceMatrixAPI::METER_TO_MILE, 2) . " miles";
        }

        return $this->data['distance']['text'];
    }

    public function getDurationValue() : ?float
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
