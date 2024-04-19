<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyFetchController extends Controller
{

    public function index()
    {
        $response = Http::get('https://openexchangerates.org/api/currencies.json?prettyprint=false&show_alternative=false&show_inactive=false&app_id=1');

        // Decode JSON response
        $data = $response->json();

        // Iterate through the data and save to database
        foreach ($data as $currencyCode => $currencyName) {
            $country = new Country();
            $country->name = $currencyName;
            $country->currency = $currencyCode;
            $country->save();
        }

        return response()->json(['message' => 'Countries and currencies saved successfully', "data" => $data], 200);
    }
}
