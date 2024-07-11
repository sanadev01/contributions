<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\State;
use App\Models\Country;
use Illuminate\Support\Facades\Cache;
use App\Services\Converters\UnitsConverter;
use App\Http\Requests\Calculator\USCalculatorRequest;
use App\Repositories\Calculator\USCalculatorRepository;

class TaxCalculatorController extends Controller
{
    public function index()
    {
        return view('tax-calculator.index');
    }
 
}
