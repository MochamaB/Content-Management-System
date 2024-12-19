<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Slider;
use App\Models\Unit;
use App\Models\UnitDetail;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $slider = Slider::all();
        $units = Unit::with('unitdetails')->has('unitdetails')->get();
        $properties = Property::with('sliders','propertyType')->get();
       // dd($units);
       // $featuredListings = UnitDetail::all();

        return view('client.home', [
            'slider' => $slider,
            'units' => $units,
            'properties' =>$properties
        ]);
    }


   
    
}
