<?php

namespace App\Http\Controllers;

use App\Helpers\FoursquareAPI;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function places(Request $request)
    {
        $query = $request->input('q');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        $latlng = $latitude != null && $longitude != null ? $latitude.','.$longitude : null ;

        $foursquareApi = new FoursquareAPI();
        $source = $foursquareApi
            ->endpoint('venues/suggestcompletion')
            ->query($query)
            ->coordinates($latlng)
            ->get();

        return response()->json($source);
    }
}
