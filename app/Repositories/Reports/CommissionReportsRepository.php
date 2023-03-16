<?php

namespace App\Repositories\Reports;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\AffiliateSale;
use App\Models\CommissionSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CommissionReportsRepository
{
    protected $error;

    public function getCommissionReportOfUsers(Request $request,$paginate = true,$pageSize=50,$orderBy = 'id',$orderType='asc')
    {
        $query = User::query();
            $query->with(['affiliateSales']);

        if ( $request->name ){
            $query->where('name','LIKE',"%{$request->name}%")
                    ->orWhere('last_name','LIKE',"%{$request->name}%");
        } elseif ( $request->pobox_number ) 
        {
            $query->where('pobox_number','LIKE',"%{$request->pobox_number}%");
        } elseif ( $request->email)
        {
            $query->where('email','LIKE',"%{$request->email}%");
        } else if( $request->search )
        {
            $query->where('name','LIKE',"%{$request->search}%")
            ->orWhere('last_name','LIKE',"%{$request->search}%")
            ->orWhere('pobox_number','LIKE',"%{$request->search}%")
            ->orWhere('email','LIKE',"%{$request->search}%");
        }

        $query->withCount(['affiliateSales as sale_count'=> function($query) use ($request){
            
            if ( $request->start_date ){
                $query->where('created_at','>',$request->start_date);
            }

            if ( $request->end_date ){
                $query->where('created_at','<=',$request->end_date);
            }

        },'affiliateSales as commission' => function($query) use ($request) {
            if ( $request->start_date ){
                $query->where('created_at','>',$request->start_date);
            }

            if ( $request->end_date ){
                $query->where('created_at','<=',$request->end_date);
            }

            $query->select(DB::raw('sum(commission) as commission'));
        }])
        ->orderBy($orderBy,$orderType);

        return $paginate ? $query->paginate($pageSize) : $query->get();
    }

    public function getCommissionReportOfLoggedInUser(Request $request, $paginate = true, $pageSize = 50, $orderBy = 'id', $orderType = 'asc')
    {
        $query = CommissionSetting::where('user_id', Auth::id());
        // $query->with(['affiliateSales']);
        
        $query->withCount(['affiliateSales as sale_count'=> function($query) use ($request){
            
            if ( $request->start_date ){
                $query->where('created_at','>',$request->start_date);
            }

            if ( $request->end_date ){
                $query->where('created_at','<=',$request->end_date);
            }

        },'affiliateSales as commission' => function($query) use ($request) {
            if ( $request->start_date ){
                $query->where('created_at','>',$request->start_date);
            }

            if ( $request->end_date ){
                $query->where('created_at','<=',$request->end_date);
            }

            $query->select(DB::raw('sum(commission) as commission'));
        }])->orderBy($orderBy,$orderType);

        return $paginate ? $query->paginate($pageSize) : $query->get();
    }

    public function getCommissionReportOfUserByMonth(User $user, Request $request)
    {
        $query = $user->affiliateSales();
        $startDate  = $request->start_date.' 00:00:00'; 
        $endDate    = $request->end_date.' 23:59:59';
        $affiliateSalesByMonth = $query->selectRaw(
            "count(*) as total, Month(created_at) as month"
        )->groupBy('month')->whereYear('created_at','LIKE','%'.$request->year.'%')->orderBy('month','asc')->get();
        // )->groupBy('month')->whereBetween('created_at',[$startDate,$endDate])->orderBy('month','asc')->get();
        return $affiliateSalesByMonth;
    }
}
