<?php


namespace App\Libs;

use Illuminate\Support\Facades\Http;

class Geocoder
{
    private $key;
    private $baseUrl;

    public function __construct()
    {
        $this->key = env('LOCATIONIQ_API_KEY', "");
        $this->baseUrl = env('LOCATIONIQ_API_BASE_URL', "https://eu1.locationiq.com");
    }

    public function reverse($latitude, $longitude)
    {
        $response = Http::get($this->baseUrl."/v1/reverse.php", [
            "key" => $this->key,
            "lat" => $latitude,
            "lon"  => $longitude,
            "format" => "json",
            "polygon_geojson" => 1
        ]);

        return $response->json();
    }

    public function forward($address)
    {
        $response = Http::get($this->baseUrl."/v1/search.php", [
            "key" => $this->key,
            "q" => $address,
            "format" => "json"
        ]);

        return $response->json();
    }
}
