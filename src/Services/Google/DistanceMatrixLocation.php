<?php
declare(strict_types=1);

namespace Frogg\Services\Google;

use Frogg\Services\Google\Exception\DistanceMatrixLocationInvalid;

class DistanceMatrixLocation
{
    /** @var string */
    private $zipCode;

    /** @var string */
    private $country;

    /** @var string */
    private $state;

    /** @var string */
    private $city;

    /** @var string */
    private $latitude;

    /** @var string */
    private $longitude;

    /** @var string */
    private $placeId;

    /** @var string */
    private $formattedLocation;

    public function __construct(
        ?string $country = null,
        ?string $state = null,
        ?string $city = null,
        ?string $zipCode = null,
        ?string $latitude = null,
        ?string $longitude = null,
        ?string $placeId = null
    ) {
        $this->country   = $country;
        $this->state     = $state;
        $this->city      = $city;
        $this->zipCode   = $zipCode;
        $this->latitude  = $latitude;
        $this->longitude = $longitude;
        $this->placeId   = $placeId;

        if (!$this->getFormattedLocation()) {
            throw new DistanceMatrixLocationInvalid();
        }
    }

    public function getZipCode() : string
    {
        return $this->zipCode;
    }

    public function getCountry() : string
    {
        return $this->country;
    }

    public function getState() : string
    {
        return $this->state;
    }

    public function getCity() : string
    {
        return $this->city;
    }

    public function getLatitude() : string
    {
        return $this->latitude;
    }

    public function getLongitude() : string
    {
        return $this->longitude;
    }

    public function getPlaceId() : string
    {
        return $this->placeId;
    }

    public function getFormattedLocation() : ?string
    {
        if ($this->formattedLocation) {
            return $this->formattedLocation;
        }

        if ($this->latitude && $this->longitude) {
            return $this->formattedLocation = "{$this->getLatitude()},{$this->getLongitude()}";
        }

        if ($this->zipCode) {
            return $this->formattedLocation = $this->replaceSpaces($this->getZipCode());
        }

        if ($this->placeId) {
            return $this->formattedLocation = "place_id:{$this->getPlaceId()}";
        }

        if ($this->city) {
            $this->formattedLocation = $this->replaceSpaces($this->getCity());
        }

        if ($this->state) {
            $this->concatLocation($this->getState());
        }

        if ($this->country) {
            $this->concatLocation($this->getCountry());
        }

        return $this->formattedLocation ?: null;
    }

    private function replaceSpaces(string $string) : string
    {
        return str_replace(" ", "+", $string);
    }

    private function concatLocation(string $string) : void
    {
        if ($this->formattedLocation) {
            $this->formattedLocation .= "+";
        }

        $this->formattedLocation .= $this->replaceSpaces($string);
    }
}
