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
       $user = User::findOrFail(Auth::id(),['id','name','last_name','pobox_number','email','phone','street_no','address','address2','city','zipcode','state_id','country_id']);
       if ($user){
           return apiResponse(true,'User detail',$user);
       }
       return apiResponse(false,'User not found',null);
   }
}
