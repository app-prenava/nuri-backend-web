<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AirVisualService
{
    /**
     * Get city air quality data.
     *
     * @param string $city
     * @param string $state
     * @param string $country
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCityData($city, $state, $country)
    {
        $apiKey = config('services.airvisual.key');

        \Log::info('Making request to AirVisual API to get city data', [
            'city' => $city,
            'state' => $state,
            'country' => $country,
        ]);
    
        $response = Http::get("http://api.airvisual.com/v2/city", [
            'city' => $city,
            'state' => $state,
            'country' => $country,
            'key' => $apiKey,
        ]);
        
        if ($response->successful()) {
            return $response->json();
        } else {
            return [
                'error' => 'Unable to fetch data', 
                'status' => $response->status(),
                'message' => $response->body()
            ];
        }        
    }
}