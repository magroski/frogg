<?php

declare(strict_types=1);


namespace Frogg\Services\Google\ValueObject;


class DistanceMatrixOrigin
{
    /** @var mixed[] */
    private $destinations;

    /** @var int */
    private $index;

    /** @var string */
    private $address;

    /** @var mixed[] */
    private $destinationNames;

    public function __construct(int $index, string $address, array $destinations, $destinationNames)
    {
        $this->index            = $index;
        $this->address          = $address;
        $this->destinations     = $destinations;
        $this->destinationNames = $destinationNames;
    }

    public function getAddress() : string
    {
        return $this->address;
    }

    public function getDestination(int $index) : DistanceMatrixDestination
    {
        if (!isset($this->destinations[$index])) {
            return new DistanceMatrixDestination($this, $this->destinationNames[$index], []);
        }

        return new DistanceMatrixDestination($this, $this->destinationNames[$index], $this->destinations[$index]);
    }

    public function getDestinationByName(string $name) : DistanceMatrixDestination
    {
        $idx = array_search($name, $this->destinationNames);

        return $this->getDestination($idx);
    }

    /**
     * @return DistanceMatrixDestination[]
     */
    public function getDestinations() : array
    {
        $result = [];
        foreach ($this->destinations as $idx => $destination) {
            $result[] = $this->getDestination($idx);
        }

        return $result;
    }
}
