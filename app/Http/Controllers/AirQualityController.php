<?php

namespace App\Http\Controllers;

use App\Services\AirVisualService;
use Illuminate\Http\Request;

class AirQualityController extends Controller
{
    protected $airVisualService;

    public function __construct(AirVisualService $airVisualService)
    {
        $this->airVisualService = $airVisualService;
    }

    /**
     * Get air quality data for a specific city.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCityData(Request $request)
    {
        // Get parameters from query string or use defaults for Bandung
        $city = $request->input('city', 'Bandung');
        $state = $request->input('state', 'West Java');
        $country = $request->input('country', 'Indonesia');
        
        // Get city data from AirVisualService
        $data = $this->airVisualService->getCityData($city, $state, $country);

        // Return data in JSON format
        return response()->json($data);
    }
}