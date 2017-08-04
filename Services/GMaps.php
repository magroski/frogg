<?php

namespace Frogg\Services;

class GMaps
{

    private $apiKey = '';

    const KM_TO_MILE = 0.621371;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Calculate the route distance between two locations
     *
     * @param mixed $locationA 'City,State' string or [city,state] array
     * @param mixed $locationB 'City,State' string or [city,state] array
     * @param bool  $metric    (default = false) flag to indicate if the result should be returned in metric or imperial
     *
     * @return int Distance in meters (if the flag is true) or its "equivalent" imperial value (calculated by multiplying meters by
     *             0.621371)
     *
     * Obs: 1 Km = 0.62 miles but 1 meter != 0.62 yards.
     *      So, the returned value can only be in meters (if flag is active) or an approximation in miles, not yards. Thanks, America.
     */
    public function calculateDistance($locationA, $locationB, $metric = false)
    {
        $locationA = is_array($locationA) ? implode(',', $locationA) : $locationA;
        $locationB = is_array($locationB) ? implode(',', $locationB) : $locationB;

        if ($locationA == $locationB) {
            return 0;
        }

        $response = file_get_contents('https://maps.googleapis.com/maps/api/distancematrix/json?origins='.urlencode($locationA).'&destinations='.urlencode($locationB).'&key='.$this->apiKey);
        $data     = json_decode($response);

        if ($data->rows[0]->elements[0]->status == 'NOT_FOUND') {
            throw new \Exception("GoogleMaps returned: ADDRESS NOT FOUND", 1);
        }
        if ($data->rows[0]->elements[0]->status == 'ZERO_RESULTS') {
            return 9999999;
        }

        $distance = $data->rows[0]->elements[0]->distance->value;

        return $metric ? $distance : $distance * self::KM_TO_MILE;
    }

}