<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
   public function __invoke()
   {
       $user = User::findOrFail(Auth::id());
       if ($user){
           return apiResponse(true,'User detail',$user);
       }
       return apiResponse(false,'User not found',null);
   }
}
