<?php


namespace App\Http\Controllers\Admin\Deposit;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\OrderRepository;
use App\Repositories\DepositRepository;
use App\Services\Excel\Export\ExportDepositReport;

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
        if(Auth::user()->isAdmin()){
            
            if($request->adminpay){
                $request->validate([
                    'user_id'     => 'required',
                    'description' => 'required',
                    'amount'      => 'required',
                ]);
                $depositRepository->adminAdd($request);
                session()->flash('alert-success', __('orders.payment.alert-success'));
                return redirect()->route('admin.deposit.index');
            }
        }
        if ( $depositRepository->store($request) ){
            session()->flash('alert-success', __('orders.payment.alert-success'));
            return redirect()->route('admin.deposit.index');
        }

        session()->flash('alert-danger',$depositRepository->getError());
        return \back()->withInput();

    }

    public function downloadAttachment($attachment)
    {
        $file_path = storage_path().'/app/deposits/'. $attachment;

        if (file_exists($file_path))
        {
            return Response::download($file_path, $attachment, [
                'Content-Length: '. filesize($file_path)
            ]);
        }
        else
        {
            abort(404);
        }   
    }

    public function showDescription($description)
    {
        return view('admin.modals.deposits.description',compact('description'));
    }
}
