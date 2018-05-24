<?php
/**
 * Created by PhpStorm.
 * User: magroski
 * Date: 06/12/17
 * Time: 15:13
 */

use Frogg\Services\Google\DistanceMatrixAPI;
use Frogg\Services\Google\ValueObject\DistanceMatrixLocation;
use PHPUnit\Framework\TestCase;

class DistanceMatrixAPITest extends TestCase
{

    /**
     * @throws \Frogg\Exceptions\ServiceProviderException
     */
    public function testCalculateDistanceMatrixMetric()
    {
        $origins      = [
            new DistanceMatrixLocation('Canada', 'BC', 'Vancouver'),
            new DistanceMatrixLocation('USA', 'WA', 'Seattle'),
        ];
        $destinations = [
            new DistanceMatrixLocation('USA', 'CA', 'San Francisco'),
            new DistanceMatrixLocation('Canada', 'BC', 'San Victoria'),
        ];

        $api    = new DistanceMatrixAPI('');
        $matrix = $api->calculateDistanceMatrix($origins, $destinations);

        /**
         * Search by idx
         */
        //Vancouver -> San Francisco
        $milesToKm = 1527125 * DistanceMatrixAPI::METER_TO_MILE;
        $this->assertEquals($milesToKm, $matrix->getOrigin(0)
                                               ->getDestination(0)
                                               ->getDistanceValue());
        //Vancouver -> Victoria
        $milesToKm = 114204 * DistanceMatrixAPI::METER_TO_MILE;
        $this->assertEquals($milesToKm, $matrix->getOrigin(0)
                                               ->getDestination(1)
                                               ->getDistanceValue());

        /**
         * Search by names
         */
        //Seattle -> San Francisco
        $milesToKm   = 1298738 * DistanceMatrixAPI::METER_TO_MILE;
        $destination = $matrix->getOriginByName($origins[1]->getFormattedLocation())
                              ->getDestinationByName($destinations[0]->getFormattedLocation());

        $this->assertEquals($milesToKm, $destination->getDistanceValue());

        //Seattle -> Victoria
        $milesToKm   = 171860 * DistanceMatrixAPI::METER_TO_MILE;
        $destination = $matrix->getDestinationByName($origins[1]->getFormattedLocation(), $destinations[1]->getFormattedLocation());

        $this->assertEquals($milesToKm, $destination->getDistanceValue());
    }

    /**
     * @throws \Frogg\Exceptions\ServiceProviderException
     */
    public function testCalculateDistanceMatrixImperial()
    {
        $origins      = [
            new DistanceMatrixLocation('Canada', 'BC', 'Vancouver'),
            new DistanceMatrixLocation('USA', 'WA', 'Seattle'),
        ];
        $destinations = [
            new DistanceMatrixLocation('USA', 'CA', 'San Francisco'),
            new DistanceMatrixLocation('Canada', 'BC', 'San Victoria'),
        ];

        $api    = new DistanceMatrixAPI('');
        $matrix = $api->calculateDistanceMatrix($origins, $destinations);

        //Vancouver -> San Francisco
        $this->assertEquals(1527125, $matrix->getOrigin(0)->getDestination(0)->getDistanceValue(false));
        //Vancouver -> Victoria
        $this->assertEquals(114204, $matrix->getOrigin(0)->getDestination(1)->getDistanceValue(false));
        //Seattle -> San Francisco
        $this->assertEquals(1298738, $matrix->getOrigin(1)->getDestination(0)->getDistanceValue(false));
        //Seattle -> Victoria
        $this->assertEquals(171860, $matrix->getOrigin(1)->getDestination(1)->getDistanceValue(false));
    }

}
