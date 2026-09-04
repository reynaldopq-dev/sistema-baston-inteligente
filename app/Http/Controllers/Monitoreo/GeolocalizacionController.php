<?php

namespace App\Http\Controllers\Monitoreo;

use App\Http\Controllers\Controller;

class GeolocalizacionController extends Controller
{
    public function index()
    {
        return view('monitoreo.geolocalizacion.index');
    }
}
