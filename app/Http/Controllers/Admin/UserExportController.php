<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Excel\Export\ExportUsers;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Models\User;

class UserExportController extends Controller
{
    public function __invoke(Request $request, UserRepository $userRepository)
    {
        $exportUsers = new ExportUsers(
            $userRepository->get($request, false)
        );
        return $exportUsers->handle();
    }
}
