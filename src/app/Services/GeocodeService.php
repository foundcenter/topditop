<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;

class GeocodeService
{
    public $http;

    protected $key;

    protected $apiUrl;

    /**
     * GeocodeService constructor.
     * @param $http
     */
    public function __construct(Client $http)
    {
        $this->apiUrl = "https://maps.googleapis.com/maps/api/geocode/json";
        $this->http = $http;
        $this->key = config('services.google_maps.api_key');
    }

    public function extractCityLongName(\stdClass $result)
    {
        $address_components = $result->address_components;

        foreach ($address_components as $component) {
            if (in_array("locality", $component->types)) {
                return $component->long_name;
            }
        }

        throw new Exception("Google could not determine the City");
    }

    public function extractLatitude(\stdClass $result)
    {
        return $result->geometry->location->lat;
    }

    public function extractLongitude(\stdClass $result)
    {
        return $result->geometry->location->lng;
    }

    public function geocode(string $address, string $language = 'de')
    {
        $result = $this->http->get($this->buildUrl($address, $language));

        if ($result->getStatusCode() != 200) {
            return null;
        }

        $body = json_decode($result->getBody());

        if (count($body->results) == 0) {
            return null;
        }

        return $body->results[0];
    }

    /**
     * @param string $address
     * @param string $language
     * @return string
     */
    private function buildUrl(string $address, string $language)
    {
        $encodedAddress = urlencode($address);

        $params = "address=$encodedAddress&key=$this->key&language=$language";

        return "$this->apiUrl?$params";
    }

}