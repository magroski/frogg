<?php
/**
 * Created by PhpStorm.
 * User: magroski
 * Date: 06/12/17
 * Time: 15:13
 */

use Frogg\Services\Google\DistanceMatrixAPI;
use PHPUnit\Framework\TestCase;

class DistanceMatrixAPITest extends TestCase
{

    /**
     * @throws \Frogg\Exceptions\ServiceProviderException
     */
    public function testCalculateDistanceMatrixMetric()
    {
        $origins      = ["Vancouver, BC, Canada", "Seattle, WA, USA"];
        $destinations = ["San Francisco, CA, USA", "Victoria, BC, Canada"];

        $api    = new DistanceMatrixAPI('');
        $matrix = $api->calculateDistanceMatrix($origins, $destinations, true);

        //Vancouver -> San Francisco
        $this->assertEquals(1527125, $matrix[0][0][2]);
        //Vancouver -> Victoria
        $this->assertEquals(114179, $matrix[0][1][2]);
        //Seattle -> San Francisco
        $this->assertEquals(1298738, $matrix[1][0][2]);
        //Seattle -> Victoria
        $this->assertEquals(172265, $matrix[1][1][2]);
    }

    /**
     * @throws \Frogg\Exceptions\ServiceProviderException
     */
    public function testCalculateDistanceMatrixImperial()
    {
        $origins      = ["Vancouver, BC, Canada", "Seattle, WA, USA"];
        $destinations = ["San Francisco, CA, USA", "Victoria, BC, Canada"];

        $api    = new DistanceMatrixAPI('');
        $matrix = $api->calculateDistanceMatrix($origins, $destinations);

        //Vancouver -> San Francisco
        $this->assertEquals(1527125 * DistanceMatrixAPI::KM_TO_MILE, $matrix[0][0][2]);
        //Vancouver -> Victoria
        $this->assertEquals(114179 * DistanceMatrixAPI::KM_TO_MILE, $matrix[0][1][2]);
        //Seattle -> San Francisco
        $this->assertEquals(1298738 * DistanceMatrixAPI::KM_TO_MILE, $matrix[1][0][2]);
        //Seattle -> Victoria
        $this->assertEquals(172265 * DistanceMatrixAPI::KM_TO_MILE, $matrix[1][1][2]);
    }

}
