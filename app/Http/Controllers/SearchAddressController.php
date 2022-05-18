<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Resources\AddressResource;

class SearchAddressController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $query = Address::query();

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }
        
        $query->where('phone', 'LIKE', "%{$request->phone}%");

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $addresses = $query->take(5)->get();
        
        if ($addresses) {
            return response()->json([
                'success' => true,
                'message' => 'Address found',
                'addresses' => AddressResource::collection($addresses),
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'No address found',
            'addresses' => null,
        ], 404);

    }
}
