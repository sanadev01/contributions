<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;

class CalculatorController extends Controller
{
    public function index()
    {   
        $countries = countries();
        $states = states();
        return view('calculator.index', compact('countries', 'states'));
    }

}
