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
    private $name;

    /** @var mixed[] */
    private $destinationNames;

    public function __construct(int $index, string $name, array $destinations, $destinationNames)
    {
        $this->index            = $index;
        $this->name             = $name;
        $this->destinations     = $destinations;
        $this->destinationNames = $destinationNames;
    }

    public function getDestination(int $index) : DistanceMatrixDestination
    {
        if (!isset($this->destinations[$index])) {
            return new DistanceMatrixDestination($this, []);
        }

        return new DistanceMatrixDestination($this, $this->destinations[$index]);
    }

    public function getDestinationByName(string $name) : DistanceMatrixDestination
    {
        $idx = array_search($name, $this->destinationNames);

        return $this->getDestination($idx);
    }
}
