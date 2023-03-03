<?php

namespace App\Http\Controllers\Admin\Tax;

use App\Models\Tax;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Repositories\TaxRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tax\TaxRequest;
use App\Http\Requests\Tax\TaxUpdateRequest;
use App\Models\Deposit;
use App\Models\PaymentInvoice;
use Exception;
use Illuminate\Support\Facades\DB;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TaxRepository $repository, Request $request)
    {
        $this->authorize('view', Tax::class);
        $taxes = $repository->get($request);
        return view('admin.tax.index', compact('taxes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(TaxRepository $repository, Request $request)
    {
        $this->authorize('create', Tax::class);
        $orders = null;
        if ($request->trackingNumbers) {
            $orders = $repository->getOrders($request);
        }
        return view('admin.tax.create', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaxRepository $repository, TaxRequest $request)
    {
        $this->authorize('create', Tax::class);
        $response = $repository->store($request);
        if (is_bool($response) && $response) {
            session()->flash('alert-success', 'Tax has been added successfully');
            return redirect()->route('admin.tax.index');
        } else {
            return back()->withInput()->withErrors($response);
        };
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Tax $tax)
    {
        $this->authorize('update', $tax);
        return view('admin.tax.edit', compact('tax'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TaxUpdateRequest $request, Tax $tax, TaxRepository $repository)
    {
        $this->authorize('update', $tax);
        if ($repository->update($request, $tax)) {
            session()->flash('alert-success', 'Tax Transaction Updated');
            return redirect()->route('admin.tax.index');
        }
        session()->flash('alert-danger', 'Error While Update Tax! Check Your Account Balance');
        return back()->withInput();
    }

    public function refund(Request $request)
    {

        $taxesIds = json_decode($request->taxes);
        $count=0;
        $error='';
        foreach ($taxesIds as $id) {
            $tax = Tax::find($id);
          DB::beginTransaction();
          try{
            $balance = Deposit::getCurrentBalance($tax->user); 
            $deposit = Deposit::create([
                'uuid' => PaymentInvoice::generateUUID('DP-'),
                'amount' =>  $tax->selling_usd,
                'user_id' => $tax->user_id,
                'balance' => $balance +  $tax->selling_usd,
                'is_credit' => true,
                'attachment' => '',
                'last_four_digits' => 'Tax refunded',
            ]);

            $tax->update([
                  'deposit_id'=>$deposit->id,
            ]); 
            DB::commit();
            $count++;
          }
          catch(Exception $e) {  
            $error = $e->getMessage();
            DB::rollBack();
          }
        }

        if($count)
        session()->flash('alert-success', ($count>1?$count:'').' tax refunded.'.$error);
        else
        session()->flash('alert-danger', 'no tax refunded.Error '.$error);

        return back();
        
    }
}
