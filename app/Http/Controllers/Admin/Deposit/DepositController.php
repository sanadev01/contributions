<?php


namespace App\Http\Controllers\Admin\Deposit;


use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DepositRepository;
use Illuminate\Support\Facades\Response;
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
        $stripeKey = null;

        $paymentGateway = setting('PAYMENT_GATEWAY', null, null, true);
        if($paymentGateway == 'STRIPE')
        {
            $stripeKey = setting('STRIPE_KEY', null, null, true);
        }
        
        return view('admin.deposit.create', compact('paymentGateway', 'stripeKey'));
    }

    public function store(Request $request, DepositRepository $depositRepository)
    {
        
        $request->validate([
            'amount' => 'required|numeric',
        ]);
        $request->merge(['payment_gateway' => 'authorize']);
        
        if(Auth::user()->isAdmin()){
            
            if($request->adminpay){
                $user = Deposit::query()->where('user_id',$request->user_id)->latest('id')->first();
                $request->validate([
                    'user_id'     => 'required',
                    'description' => 'required',
                    'is_credit'=>'required',
                    'amount'      => 'required|numeric',

                ]);
                if($user){
                if((float)($request->amount) > $user->balance && $request->is_credit=="false"){
                    $request->validate([
                    'amount'      => 'numeric|max:'.$user->balance,
                    ], [
                        'amount.max' => 'Your Current Account Balance is '.$user->balance.' and debit amount should be less than '.$user->balance.'.!',
                    ]);
                }
            }
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

    public function showDescription(Deposit $deposit)
    {
        return view('admin.modals.deposits.description',compact('deposit'));
    }
    
    public function updateDescription(Request $request, Deposit $deposit)
    {
        if($deposit){
            $deposit->update([
                'description' => $request->description
            ]);
        }
        session()->flash('alert-success', 'Description Updated');
        return redirect()->route('admin.deposit.index');
    }
}
