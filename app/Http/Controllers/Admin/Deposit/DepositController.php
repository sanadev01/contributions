<?php


namespace App\Http\Controllers\Admin\Deposit;


use App\Http\Controllers\Controller;
use App\Repositories\DepositRepository;
use App\Repositories\OrderRepository;
use App\Services\Excel\Export\ExportDepositReport;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index(Request $request, DepositRepository $depositRepository)
    {
        if ( $request->dl ==1 ){
            $deposits = $depositRepository->get($request,false,0,$request->sortBy,$request->sortOrder);
            $depositReport = new ExportDepositReport($deposits);
            return $depositReport->handle();
        }

        return view('admin.deposit.index');
    }

    public function create()
    {
        return view('admin.deposit.create');
    }

    public function store(Request $request, DepositRepository $depositRepository)
    {
        if ( $depositRepository->store($request) ){
            session()->flash('alert-success', __('orders.payment.alert-success'));
            return redirect()->route('admin.deposit.index');
        }

        session()->flash('alert-danger',$depositRepository->getError());
        return \back()->withInput();

    }
}
