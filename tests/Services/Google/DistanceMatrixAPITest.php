<?php
/**
 * Created by PhpStorm.
 * User: magroski
 * Date: 06/12/17
 * Time: 15:13
 */

use Frogg\Services\Google\DistanceMatrixAPI;
use Frogg\Services\Google\DistanceMatrixLocation;
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
        $matrix = $api->calculateDistanceMatrix($origins, $destinations, true);

        //Vancouver -> San Francisco
        $this->assertEquals(1527125, $matrix[0][0][2]);
        //Vancouver -> Victoria
        $this->assertEquals(114204, $matrix[0][1][2]);
        //Seattle -> San Francisco
        $this->assertEquals(1298738, $matrix[1][0][2]);
        //Seattle -> Victoria
        $this->assertEquals(171860, $matrix[1][1][2]);
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
        $this->assertEquals(1527125 * DistanceMatrixAPI::KM_TO_MILE, $matrix[0][0][2]);
        //Vancouver -> Victoria
        $this->assertEquals(114204 * DistanceMatrixAPI::KM_TO_MILE, $matrix[0][1][2]);
        //Seattle -> San Francisco
        $this->assertEquals(1298738 * DistanceMatrixAPI::KM_TO_MILE, $matrix[1][0][2]);
        //Seattle -> Victoria
        $this->assertEquals(171860 * DistanceMatrixAPI::KM_TO_MILE, $matrix[1][1][2]);
    }

}
