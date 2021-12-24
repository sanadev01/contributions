<?php

namespace App\Http\Controllers\Admin\Modals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserSuspendController extends Controller
{
    public function __invoke()
    {
        return view('admin.modals.users.suspended');
    }
}
