<?php

declare(strict_types=1);

use Frogg\Services\Google\DistanceMatrixLocation;
use PHPUnit\Framework\TestCase;

class DistanceMatrixLocationTest extends TestCase
{
    /**
     * @expectedException \Frogg\Services\Google\Exception\DistanceMatrixLocationInvalid
     */
    public function testNoCreateInvalidLocation()
    {
        new DistanceMatrixLocation();
    }

    public function testFormatCountryLocation()
    {
        $location = new DistanceMatrixLocation('Canada');

        $this->assertEquals('Canada', $location->getFormattedLocation());
    }

    public function testFormatStateLocation()
    {
        $location = new DistanceMatrixLocation('Canada', 'BC');

        $this->assertEquals('BC+Canada', $location->getFormattedLocation());
    }

    public function testFormatCityLocation()
    {
        $location = new DistanceMatrixLocation('Canada', 'BC', 'Vancouver');

        $this->assertEquals('Vancouver+BC+Canada', $location->getFormattedLocation());
    }

    public function testFormatCoordinatesLocation()
    {
        $location = new DistanceMatrixLocation(
            null,
            null,
            null,
            null,
            '49.252784',
            '-123.108113'
        );

        $this->assertEquals('49.252784,-123.108113', $location->getFormattedLocation());
    }

    public function testFormatZipCodeLocation()
    {
        $location = new DistanceMatrixLocation(
            null,
            null,
            null,
            null,
            null,
            null,
            'ChIJpekq4O9zhlQRUNNS_bu6dEM'
        );

        $this->assertEquals('place_id:ChIJpekq4O9zhlQRUNNS_bu6dEM', $location->getFormattedLocation());
    }
}
