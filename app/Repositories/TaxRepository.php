<?php

namespace App\Repositories;
use App\Events\AutoChargeAmountEvent;
use App\Http\Requests\Tax\TaxRequest;
use App\Http\Requests\Tax\TaxUpdateRequest;
use Exception;
use App\Models\Tax;
use App\Models\User;
use App\Models\Order;
use App\Models\Deposit;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Models\PaymentInvoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaxRepository
{

    protected $fileName;

    public function get(Request $request, $paginate = true, $pageSize = 50 )
    {
        $query = Tax::has('user')->has('order');

        if ( $request->search ){
            $query->whereHas('user',function($query) use($request) {
                return $query->where('name', 'LIKE', "%{$request->search}%");
            });
            $query->orWhereHas('order',function($query) use($request) {
                return $query->where('warehouse_number', 'LIKE', "%{$request->search}%")
                ->orWhere('corrios_tracking_code', 'LIKE', "%{$request->search}%");
            });
        }
        
        $startDate  = $request->start_date.' 00:00:00';
        $endDate    = $request->end_date.' 23:59:59';
        if ( $request->start_date ){
            $query->where('created_at' , '>=',$startDate);
        }
        if ( $request->end_date ){
            $query->where('created_at' , '<=',$endDate);
        }

        $taxes = $query->orderBy('id','desc');

        return $paginate ? $taxes->paginate($pageSize) : $taxes->get();
    }

    public function getOrders(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);
        $trackingNumber = explode(',', preg_replace('/\s+/', '', $request->trackingNumbers));
       
        return Order::where('user_id',$request->user_id)->whereIn('corrios_tracking_code', $trackingNumber)->get();
    }

    public function store(TaxRequest $request)
    {
        $insufficientBalanceMessages=[];
        $depositedMessages=[];
        $user = null; 
            foreach ($request->order_id as $orderId){
                    DB::beginTransaction();
                    try{
                        $order = Order::find($orderId); 
                        $user = $order->user;
                        $balance = Deposit::getCurrentBalance($user);

                        $sellingUSD =   round($request->tax_payment[$order->id]/$request->selling_br[$order->id],2);
                        $buyingUSD  =  round($request->tax_payment[$order->id]/$request->buying_br[$order->id],2);
                         
                        if ($balance >= $sellingUSD) 
                        {
                            if($order->tax){
                                 DB::rollBack();  
                                 return  $depositedMessages+$insufficientBalanceMessages;
                            }
                            //save tax information.
                            Tax::create([
                                'user_id' => $order->user_id,
                                'order_id' => $order->id,
                                'tax_payment' => $request->tax_payment[$order->id], 
                                'buying_br' => $request->buying_br[$order->id],
                                'selling_br' => $request->selling_br[$order->id], 
                                'selling_usd' => $sellingUSD,
                                'buying_usd' => $buyingUSD,
                            ]);
                            //deposite balance.
                            $deposit = Deposit::create([
                                'uuid' => PaymentInvoice::generateUUID('DP-'),
                                'amount' => $sellingUSD ,
                                'user_id' => $order->user_id,
                                'order_id' => $order->id,
                                'balance' => $balance - $sellingUSD,
                                'is_credit' => false,
                                'attachment' => $this->fileName,
                                'last_four_digits' => 'Pay Tax',
                                'description' => 'Pay Tax',
                            ]); 
                            //upload files 
                            $attachs = optional($request->file('attachment'))[$order->id]; 
                            if ($attachs) {
                                foreach($request->attachment[$order->id] as $attach){
                                    $document = Document::saveDocument($attach);
                                    $deposit->depositAttchs()->create([
                                        'name' => $document->getClientOriginalName(),
                                        'size' => $document->getSize(),
                                        'type' => $document->getMimeType(),
                                        'path' => $document->filename
                                    ]);
                                }
                            }
                            // associate deposite with tax.  
                            Tax::where('order_id',$order->id)->update(['deposit_id' => $deposit->id]);
                            $depositedMessages['deposit'.$orderId] = $order->warehouse_number." : Balance deposited.";
                          
                        }else
                        {
                            $insufficientBalanceMessages['balance'.$orderId] = $order->warehouse_number." :Low Balance";
                       }
                    }
                    catch(Exception $e){
                        DB::rollBack(); 
                        return [ 'error' => $e->getMessage()]; 
                    } 
                     DB::commit();
            }
            if( count($insufficientBalanceMessages) > 0 )
                return $depositedMessages+$insufficientBalanceMessages; 
            else{
                if($user){
                  AutoChargeAmountEvent::dispatch($user);
                } 
                return true; 
            }
           
    }

    public function update(TaxUpdateRequest $request,Tax $tax)
    {
        try{
            $deposit = $tax->deposit;
            $balance = Deposit::getCurrentBalance($tax->user);
            $sellingUSD =  round($request->tax_payment/$request->selling_br,2);
            $diffAmount = $sellingUSD - $tax->selling_usd;
            
            $buyingUSD  =  round($request->tax_payment/$request->buying_br,2);
             
            if($balance >= $diffAmount ) {
                if($sellingUSD > $tax->selling_usd || $sellingUSD < $tax->selling_usd ) {
                    $deposit->decrement('balance', $diffAmount);
                    $deposit->increment('amount', $diffAmount);            
                }
                //FILE UPLOAD
                if ($request->hasFile('attachment')) {
                    foreach ($deposit->depositAttchs as $attachedFile ) {
                        Storage::delete($attachedFile->getStoragePath());
                    }
                    $deposit->depositAttchs()->delete();
                    $attachs = $request->file('attachment');
                    if($attachs){
                        foreach($attachs as $attach){
                                $document = Document::saveDocument($attach);
                                $deposit->depositAttchs()->create([
                                    'name' => $document->getClientOriginalName(),
                                    'size' => $document->getSize(),
                                    'type' => $document->getMimeType(),
                                    'path' => $document->filename
                                ]);
                        }

                    }
                }
                $tax->update([
                    'tax_payment' => $request->tax_payment,
                    'buying_usd' => $buyingUSD,
                    'selling_usd' => $sellingUSD,
                    'buying_br' => $request->buying_br,
                    'selling_br' => $request->selling_br,
                ]);

                return true;
            }
            return false;
        }catch(Exception $exception){
            session()->flash('alert-danger','Error'.$exception->getMessage());
            return null;
        }
    }

    public function delete()
    {
        //
    }

}
