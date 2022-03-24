<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FileUpload;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth()->user()->role == 1){
            
            $sortDirection = "ASC";
            $value = request('search');
            $users = User::withCount('files')->where('name', 'like', '%' . $value . '%')->orWhere('email', 'like', '%' . $value . '%')->with(['files'])->OrderBy('name', $sortDirection)->paginate(12);
            
            return view('files.index', compact('users'))->with('i', (request()->input('page', 1) - 1) * 5);
        }

        return view('files.upload');
    }

   
        
    public function getDistance($addressFrom = "C. de Pedro III el Grande 46005 Valencia", $addressTo = "Plaça Ollers 46970 Alaquàs València", $unit = "k"){
        //Change address format
        $formattedAddrFrom = str_replace(' ','+',$addressFrom);
        $formattedAddrTo = str_replace(' ','+',$addressTo);
        
        //Send request and receive json data
        $geocodeFrom = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$formattedAddrFrom.'&sensor=false');
        $outputFrom = json_decode($geocodeFrom);
        $geocodeTo = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$formattedAddrTo.'&sensor=false');
        $outputTo = json_decode($geocodeTo);
        
        //Get latitude and longitude from geo data
        $latitudeFrom = $outputFrom->results[0]->geometry->location->lat;
        $longitudeFrom = $outputFrom->results[0]->geometry->location->lng;
        $latitudeTo = $outputTo->results[0]->geometry->location->lat;
        $longitudeTo = $outputTo->results[0]->geometry->location->lng;
        
        //Calculate distance from latitude and longitude
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        if ($unit == "K") {
            return ($miles * 1.609344).' km';
        } else if ($unit == "N") {
            return ($miles * 0.8684).' nm';
        } else {
            return $miles.' mi';
        }

    }
}
