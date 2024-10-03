<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\CustomResponse;

class CustomsResponseController extends Controller
{
    public function handle(Request $request)
    {
        // Log the data received
        Log::info('Webhook received:', $request->all());


        $customResponse = new CustomResponse();
        $customResponse->batch_id = $request->input('batch_id') ?? null;
        $customResponse->response = json_encode($request->all());
        $customResponse->save();

        return response()->json(['status' => 'success']);
    }
}

