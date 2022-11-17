<?php

namespace Frogg\Services;

class GMaps
{

    private string $apiKey = '';

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
     * @param bool  $metric    (default = false) flag to indicate if the result should be returned in metric or
     *                         imperial
     *
     * @return int Distance in meters (if the flag is true) or its "equivalent" imperial value (calculated by
     *             multiplying meters by 0.621371)
     *
     * Obs: 1 Km = 0.62 miles but 1 meter != 0.62 yards.
     * So, the returned value can only be in meters (if flag is active) or an approximation in miles, not yards.
     * Thanks, America.
     *
     * @throws \Exception When one of the given addresses is not found
     */
    public function calculateDistance($locationA, $locationB, $metric = false) : int
    {
        $locationA = is_array($locationA) ? implode(',', $locationA) : $locationA;
        $locationB = is_array($locationB) ? implode(',', $locationB) : $locationB;

        if ($locationA == $locationB) {
            return 0;
        }

        $response = file_get_contents(
            'https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . urlencode(
                $locationA
            ) . '&destinations=' . urlencode($locationB) . '&key=' . $this->apiKey
        );
        if (!$response) {
            throw new \Exception('Could not get result');
        }
        $data     = json_decode($response);

        if ($data->rows[0]->elements[0]->status == 'NOT_FOUND') {
            throw new \Exception(
                'GoogleMaps exception: ADDRESS NOT FOUND. [' . $locationA . ' | ' . $locationB . ']', 1
            );
        }
        if ($data->rows[0]->elements[0]->status == 'ZERO_RESULTS') {
            return 9999999;
        }

        $distance = $data->rows[0]->elements[0]->distance->value;

        return $metric ? $distance : $distance * self::KM_TO_MILE;
    }

    /**
     * Search the most relevant place based on a keyword and return a link to its location
     *
     * @param string $keyword An address or place. Ex: '5th Avenue, New York' or 'Eiffel Tower'
     *
     * @return string A GoogleMaps link
     *
     * @throws \Exception When the given address was not found
     */
    public function generateLink(string $keyword) : string
    {
        $response = file_get_contents(
            'https://maps.googleapis.com/maps/api/place/textsearch/json?query=' . urlencode(
                $keyword
            ) . '&key=' . $this->apiKey
        );
        if (!$response) {
            throw new \Exception('Could not get result');
        }
        $data = json_decode($response);

        if (empty($data->results) || (property_exists($data, 'status') && $data->status === 'ZERO_RESULTS')) {
            throw new \Exception('GoogleMaps exception: ADDRESS NOT FOUND. [' . $keyword . ']', 1);
        }

        $lat = $data->results[0]->geometry->location->lat;
        $lng = $data->results[0]->geometry->location->lng;

        return 'http://maps.google.com/maps?q=' . $lat . ',' . $lng . '&z=17';
    }

}
