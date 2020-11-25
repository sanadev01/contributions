<?php

namespace App\Http\Controllers\Admin\Connect;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    public function __invoke()
    {
        return view('admin.connects.quick-guide');
    }
}
