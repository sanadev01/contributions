<?php

namespace App\Http\Controllers\Admin\Activity;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Activity::class);
    }

    public function index()
    {
        return view('admin.activity.index');
    }
}
