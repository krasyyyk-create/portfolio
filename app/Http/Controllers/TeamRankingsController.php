<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeamRankingsController extends Controller
{
    public function index()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://v1.formula-1.api-sports.io/rankings/teams', //season team
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'x-apisports-key: 5f49d9933181ba8dca31b21434c71130'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

    }

       

}
