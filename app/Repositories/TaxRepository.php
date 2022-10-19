<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Tax;
use App\Models\Deposit;
use App\Models\Document;
use App\Models\PaymentInvoice;
use App\Models\User;
use Exception;

class TaxRepository
{

    protected $fileName;

    public function get(Request $request, $paginate = true, $pageSize = 3 )
    {
        $query = Tax::has('user');
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
        return Order::where('user_id',$request->user_id)->whereDoesntHave('tax')->whereIn('corrios_tracking_code', $trackingNumber)->get();
    }

    public function store(Request $request)
    {
        $amount = 0;
        $trackingNos = [];
        try{
            $user = User::find($request->user_id);
            if($user) {
                foreach($request->order_id as $key=> $orderId) {
                    $order = Order::find($orderId);
                    if($order) {
                        Tax::create([
                            'user_id' => $request->user_id,
                            'order_id' => $orderId,
                            'tax_1' => $request->tax_1[$key],
                            'tax_2' => $request->tax_2[$key],
                        ]);
                        $amount += $request->tax_2[$key];
                        $trackingNos[] = array('Code' => $request->tracking_code[$key], );
                    }
                }
                $balance = Deposit::getCurrentBalance($user);
                $codes = json_encode($trackingNos);
                if(!empty($codes) && $balance >= $amount) {

                    $deposit = Deposit::create([
                        'uuid' => PaymentInvoice::generateUUID('DP-'),
                        'amount' => $amount,
                        'user_id' => $request->user_id,
                        'balance' => $balance - $amount,
                        'is_credit' => false,
                        'attachment' => $this->fileName,
                        'last_four_digits' => 'Pay Tax',
                        'description' => 'Pay Tax'.' '.$codes,
                    ]);
                    if ($request->hasFile('attachment')) {
                        foreach ($request->file('attachment') as $attach) {
                            $document = Document::saveDocument($attach);
                            $deposit->depositAttchs()->create([
                                'name' => $document->getClientOriginalName(),
                                'size' => $document->getSize(),
                                'type' => $document->getMimeType(),
                                'path' => $document->filename
                            ]);
                        }
                    }
                    $taxes = Tax::whereIn('order_id', $request->order_id)->update(['deposit_id' => $deposit->id]);
                    return true;
                }
                return false;
            }
        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Adding Tax'. $exception->getMessage());
            return null;
        }
    }

    public function update(Request $request)
    {
        //
    }


    public function delete()
    {
        //
    }

}
