<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyFetchController extends Controller
{

    public function index()
    {
        try {
            $url = config('app.api_currency');
            $response = Http::get($url);

            // Check request
            if ($response->successful()) {
                // Decode JSON response
                $data = $response->json();

                foreach ($data as $currencyCode => $currencyName) {
                    // Check if the country with the same currency code already exists
                    $existingCountry = Country::where('currency', $currencyCode)->first();
                    if (!$existingCountry) {
                        $country = new Country();
                        $country->name = $currencyName;
                        $country->currency = $currencyCode;
                        $country->save();
                    }
                }

                return response()->json(['message' => 'Countries and currencies saved successfully', "data" => $data], 200);
            } else {
                return response()->json(['message' => 'Failed to fetch data from API'], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
