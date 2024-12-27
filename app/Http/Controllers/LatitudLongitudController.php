<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LatitudLongitudController extends Controller
{
    public function index(Request $r){
    Log::info(__FUNCTION__.'/'.__FILE__); Log::info($r); $rta['cod']=500; $rta['msg']='Error'; 
       return $this->getDistanceBetweenPointsNew($r->latitude1, $r->longitude1, $r->latitude2, $r->longitude2);
    }
    function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'kilometers') {
        Log::info($latitude1);
        $theta = $longitude1 - $longitude2; 
        Log::info($theta);
        $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta))); 
        $distance = acos($distance); 
        $distance = rad2deg($distance); 
        $distance = $distance * 60 * 1.1515; 
        switch($unit) { 
          case 'miles': 
            break; 
          case 'kilometers' : 
            $distance = $distance * 1.609344; 
        } 
        return (round($distance,2)); 
      }
}
