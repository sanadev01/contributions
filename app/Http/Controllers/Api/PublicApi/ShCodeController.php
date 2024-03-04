<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\ShCode;
use Illuminate\Http\Request;

class ShCodeController extends Controller
{
    public function __invoke($search = null)
    {
        $type = request()->has('type') ? strtolower(request()->type) : null;
        $type = $type == "courier" ? 'Courier' : ($type == "postal" ? 'Postal (Correios)' : null);

        $shCode = ShCode::query();

        if ($type !== null) {
            $shCode->where('type', $type);
        } elseif ($search == null) {
            $shCode->where('type', 'Postal (Correios)');
        }

        if ($search != null) {
            $shCode->where(function ($query) use ($search) {
                $query->where('code', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $shCodes = $shCode->orderBy('description', 'ASC')->get(['code', 'description', 'type']);

        if ($shCodes->isEmpty()) {
            return apiResponse(false, 'No SH Code Found');
        }

        return $shCodes->map(function ($sh) {
            return [
                'code' => $sh->code,
                'type' => $sh->type,
                'description' => $sh->description,
            ];
        });

    }

}
