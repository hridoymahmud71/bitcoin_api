<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BitcoinController extends Controller
{
 
    //  this function fetches data from 
    //  https://api.coindesk.com/v1/bpi/historical/close.json?start=2013-09-01&end=2013-09-05&currency=eur
    //  and 
    //  https://api.coindesk.com/v1/bpi/currentprice/eur.json 
    //  and returns modified data as per user request
    public function getBitcoinInfo(){

    }

}
