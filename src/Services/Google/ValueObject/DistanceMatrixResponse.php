<?php

declare(strict_types=1);

namespace Frogg\Services\Google\ValueObject;

class DistanceMatrixResponse
{
    /** @var mixed[] */
    private $rows;

    /** @var mixed[] */
    private $origins;

    /** @var mixed[] */
    private $destinations;

    /** @var mixed[] */
    private $googleOrigins;

    /** @var mixed[] */
    private $googleDestinations;

    /**
     * DistanceMatrixResponse constructor.
     * @param mixed[] $rows
     * @param mixed[] $origins
     * @param mixed[] $destinations
     * @param mixed[] $googleOrigins
     * @param mixed[] $googleDestinations
     */
    public function __construct(
        array $rows,
        array $origins,
        array $destinations,
        array $googleOrigins,
        array $googleDestinations
    ) {
        $this->rows               = $rows;
        $this->origins            = $origins;
        $this->destinations       = $destinations;
        $this->googleOrigins      = $googleOrigins;
        $this->googleDestinations = $googleDestinations;
    }

    /**
     * @return mixed[]
     */
    public function getResponseData() : array
    {
        return $this->rows;
    }

    /**
     * @return mixed[]
     */
    public function getOrigins() : array
    {
        return $this->origins;
    }

    /**
     * @return mixed[]
     */
    public function getDestinations() : array
    {
        return $this->destinations;
    }

    /**
     * @return mixed[]
     */
    public function getGoogleOrigins() : array
    {
        return $this->googleOrigins;
    }

    /**
     * @return mixed[]
     */
    public function getGoogleDestinations() : array
    {
        return $this->googleDestinations;
    }

    public function hasOrigin(int $index) : bool
    {
        return isset($this->rows[$index]['elements']);
    }

    public function hasDestination(int $originIndex, int $destinationIndex) : bool
    {
        return isset($this->rows[$originIndex]['elements'][$destinationIndex]);
    }

    public function getOrigin(int $index) : DistanceMatrixOrigin
    {
        if (!$this->hasOrigin($index)) {
            return new DistanceMatrixOrigin($index, $this->origins[$index], [], $this->destinations);
        }

        return new DistanceMatrixOrigin($index, $this->origins[$index], $this->rows[$index]['elements'], $this->destinations);
    }

    public function getOriginByName(string $name) : DistanceMatrixOrigin
    {
        $idx = array_search($name, $this->origins);

        return $this->getOrigin($idx);
    }

    public function getDestinationByName(string $originName, string $destinationName) : DistanceMatrixDestination
    {
        $origin         = $this->getOriginByName($originName);
        $destinationIdx = array_search($destinationName, $this->destinations);

        return $origin->getDestination($destinationIdx);
    }

    public function getDestination(int $originIndex, int $destinationIndex) : DistanceMatrixDestination
    {
        return $this->getOrigin($originIndex)->getDestination($destinationIndex);
    }
}
