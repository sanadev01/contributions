<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AmazonApiTokenController extends Controller
{
    public function __invoke(Request $request)
    {
        $validate = \Validator::make($request->all(),[
            'token' => 'required',
        ]);

        if ( $validate->fails() ){
            return response()->json([
                'success' => false,
                'message' => "Validation Errors",
                'data' => [
                    'errors' => $validate->errors()->toArray()
                ]
            ],422); 
        }

        try{
            $user = User::find(Auth::user()->id);
            if($user){
                $user->update([
                    'amazon_api_key' => $request->token
                ]);
                return apiResponse(true,'Api Key Updated');
            }
        } catch (\Exception $ex) {
           return apiResponse(false,$ex->getMessage());
        }
    }
}
