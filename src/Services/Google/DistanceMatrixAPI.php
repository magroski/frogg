<?php

namespace Frogg\Services\Google;

use Frogg\Exceptions\ServiceProviderException;
use Frogg\Services\Google\ValueObject\DistanceMatrixLocation;
use Frogg\Services\Google\ValueObject\DistanceMatrixResponse;

class DistanceMatrixAPI
{

    private $apiKey   = '';
    private $endpoint = 'https://maps.googleapis.com/maps/api/distancematrix/';
    private $format   = 'json';

    const METER_TO_MILE = 0.000621371;

    public function __construct(string $apiKey, string $format = 'json')
    {
        $this->apiKey = $apiKey;
        $this->format = 'json';
    }

    /**
     * Calculates the distance between multiple origins and destinations
     *
     * @param DistanceMatrixLocation[] $origins
     * @param DistanceMatrixLocation[] $destinations
     *
     * As Google always return the distance value in meters (not km), the function multiplies
     * the result by 0.62 (km:mile) to calculate an approximation in imperial.
     * Obs: Be aware that 1 Km = 0.62 miles but 1 meter != 0.62 yards.
     *
     * @return DistanceMatrixResponse
     * @throws ServiceProviderException
     */
    public function calculateDistanceMatrix(array $origins, array $destinations) : DistanceMatrixResponse
    {
        $formattedOrigins      = $this->formatEntities($origins);
        $formattedDestinations = $this->formatEntities($destinations);

        $query = http_build_query([
            'origins'      => $formattedOrigins,
            'destinations' => $formattedDestinations,
            'key'          => $this->apiKey,
        ]);

        $url  = $this->generateBaseUrl() . '?' . $query;
        $data = $this->processRequest($url);

        if (!$this->checkResponseStatus($data)) {
            $data = $this->processRequest($url);
            if (!$this->checkResponseStatus($data)) {
                throw new ServiceProviderException('GoogleDistanceMatrix returned an unknown_error after retry');
            }
        }

        return new DistanceMatrixResponse(
            $data['rows'],
            explode('|', $formattedOrigins),
            explode('|', $formattedDestinations),
            $data['origin_addresses'],
            $data['destination_addresses']
        );
    }

    private function generateBaseUrl() : string
    {
        return $this->endpoint . $this->format;
    }

    /**
     * @param DistanceMatrixLocation[] $entities
     *
     * @return string
     */
    private function formatEntities(array $entities) : string
    {
        $formattedEntities = array_map(function (DistanceMatrixLocation $entry) {
            return $entry->getFormattedLocation();
        }, $entities);
        $formattedEntities = implode('|', $formattedEntities);

        return $formattedEntities;
    }

    /**
     * @throws \Frogg\Exceptions\ServiceProviderException
     */
    private function checkResponseStatus($data) : bool
    {
        $details = isset($data['error_message']) ? $data['error_message'] : '';

        switch ($data['status']) {
            case 'OK':
                return true;
            case 'INVALID_REQUEST':
                throw new ServiceProviderException('Invalid Request. ' . $details);
            case 'MAX_ELEMENTS_EXCEEDED':
                throw new ServiceProviderException('Request contains too many elements. ' . $details);
            case 'OVER_QUERY_LIMIT':
                throw new ServiceProviderException('Too many requests. ' . $details);
            case 'REQUEST_DENIED':
                throw new ServiceProviderException('Google denied API usage. Check GoogleDistanceMatrix console.' . $details);
            //From the docs:
            //UNKNOWN_ERROR indicates a Distance Matrix request could not be processed due to a server error.
            //The request may succeed if you try again.
            case 'UNKNOWN_ERROR';
                return false;
        }
    	return true;
    }

    /**
     * @return mixed
     */
    private function processRequest(string $url)
    {
        $response = file_get_contents($url);
        $data     = json_decode($response, true);

        return $data;
    }

}
