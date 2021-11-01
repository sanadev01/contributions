<?php

namespace App\Repositories\Reports;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\CommissionSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CommissionReportsRepository
{
    protected $error;

    public function getCommissionReportOfUsers(Request $request, $paginate = true, $pageSize = 50, $orderBy = 'id', $orderType = 'asc')
    {

        if (auth()->user()->isUser()) {
            // $query = User::where(Auth::id());
            $query = CommissionSetting::where('user_id', Auth::id());
            $query->with(['affiliateSales']);
            
            // $commissionSettings = CommissionSetting::where('user_id', Auth::id())->get();
            dd(($query->get())->toArray());
        } else {
            $query = User::query();

            $query->with(['affiliateSales']);

            if ($request->name) {
                $query->where('name', 'LIKE', "%{$request->name}%")
                    ->orWhere('last_name', 'LIKE', "%{$request->name}%");
            } elseif ($request->pobox_number) {
                $query->where('pobox_number', 'LIKE', "%{$request->pobox_number}%");
            } elseif ($request->email) {
                $query->where('email', 'LIKE', "%{$request->email}%");
            }

            $query->withCount(['affiliateSales as sale_count' => function ($query) use ($request) {

                if ($request->start_date) {
                    $query->where('created_at', '>', $request->start_date);
                }

                if ($request->end_date) {
                    $query->where('created_at', '<=', $request->end_date);
                }
            }, 'affiliateSales as commission' => function ($query) use ($request) {
                if ($request->start_date) {
                    $query->where('created_at', '>', $request->start_date);
                }

                if ($request->end_date) {
                    $query->where('created_at', '<=', $request->end_date);
                }

                $query->select(DB::raw('sum(commission) as commission'));
            }])
                ->orderBy($orderBy, $orderType);

            return $paginate ? $query->paginate($pageSize) : $query->get();
        }
    }
}
