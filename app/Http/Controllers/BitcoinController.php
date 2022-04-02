<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class BitcoinController extends Controller
{

    private $listed_currencies = [];
    private $mandatory_request_params = ["currency_code",];

    function __construct()
    {

        if (!empty(env("LISTED_CURRENCIES"))) {
            $this->listed_currencies = explode(",", env("LISTED_CURRENCIES"));
        }
    }

    //  this function fetches data from 
    //  https://api.coindesk.com/v1/bpi/historical/close.json?start=2013-09-01&end=2013-09-05&currency=eur
    //  and 
    //  https://api.coindesk.com/v1/bpi/currentprice/eur.json
    //  and returns modified data as per user request    
    public function getBitcoinInfo()
    {
        $request_data = request()->all();


        $validator_response  = $this->validator_response($request_data);

        if ($validator_response->getData()->result == false) {
            return $validator_response;
        }

        $current_data = $this->getCurrentData($request_data);
        $historical_data = $this->getHistoricalData($request_data);
        
        return response()->json([
            'result'        =>  true,
            'message'       => "data fetched",
            'current'       => $current_data,
            'historical'    => $historical_data
        ]);
    }

    //validating the user request
    function validator_response($request_data)
    {
        // 1. Check for missing parameters
        $params =  array_keys($request_data);
        $missing_params =  array_diff($this->mandatory_request_params, $params);

        if (!empty($missing_params)) {
            $stringified_missing_params = implode(",", $missing_params);
            return response()->json([
                'result'    => false,
                'message'   => "These params are mandatory : $stringified_missing_params"
            ]);
        }

        //2. Check if currecy code is ok 
        if (!in_array(strtoupper($request_data['currency_code']), $this->listed_currencies)) {
            $stringified_listed_currencies = implode(",", $this->listed_currencies);
            return response()->json([
                'result'    => false,
                'message'   => "Only these currecies are available : $stringified_listed_currencies"
            ]);
        }

        // Passes !
        return response()->json([
            'result'    => true,
            'message'   => "Everything is ok"
        ]);
    }

    function current_url($currency_code)
    {
        return "https://api.coindesk.com/v1/bpi/currentprice/$currency_code.json";
    }

    function historical_url()
    {
        return "https://api.coindesk.com/v1/bpi/historical/close.json";
    }

    function getCurrentData($request_data)
    {
        $current_response = Http::get($this->current_url(strtolower($request_data['currency_code'])));


        try {
            $current_response = Http::get($this->current_url(strtolower($request_data['currency_code'])));
        } catch (\Exception $e) {
            return [
                'data'   => null,
                'message'       => "could not fetch current data"
            ];
        }

        $currency_code_upper_case = strtoupper($request_data['currency_code']);

        if (!$current_response->ok()) {
            return [
                'data'   => null,
                'message'       => "could not fetch current data"
            ];
        }

        $currency_code_upper_case = strtoupper($request_data['currency_code']);

        return [
            'data'   => $current_response->object()->bpi->$currency_code_upper_case,
            'message'       => "current data is found"
        ];
    }

    function getHistoricalData($request_data)
    {

        $parameters = [
            "start"         => date("Y-m-d", strtotime('-30 days')),
            "end"           => date("Y-m-d"),
            "currency"      => strtolower($request_data['currency_code'])
        ];

        try {
            $historical_response = Http::get($this->historical_url(), $parameters);
        } catch (\Exception $e) {
            return [
                'data'   => null,
                'message'       => "could not fetch historical data"
            ];
        }

        if (!$historical_response->ok()) {
            return [
                'data'   => null,
                'message'       => "could not fetch historical data"
            ];
        }


        // generating max min from values
        $values = array_values((array) $historical_response->object()->bpi);
        $max = max($values);
        $min = min($values);

        return  [
            'data'   => [
                "max" => $max,
                "min" => $min
            ],
            'message'       => "historical data is found"
        ];
    }
}
