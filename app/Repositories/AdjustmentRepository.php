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

class AdjustmentRepository
{
    public function store(Request $request)
    {
        $user = User::find($request->user_id);
        $balance = Deposit::getCurrentBalance($user);

        DB::beginTransaction();
        try {
               $deposit = Deposit::create([
                    'uuid' => PaymentInvoice::generateUUID('DP-'),
                    'amount' =>  $request->adjustment, 
                    'user_id' =>$request->user_id,
                    'balance' => $balance +  $request->adjustment,
                    'is_credit' => true,
                    'attachment' => '',
                    'last_four_digits' => 'Tax Adjustment',
                    'description' =>  $request->reasone,
                ]);
            // upload files 
            if ($request->hasFile('attachment')) {
                foreach ($request->attachment as $attach) {
                    $document = Document::saveDocument($attach);
                    $deposit->depositAttchs()->create([
                        'name' => $document->getClientOriginalName(),
                        'size' => $document->getSize(),
                        'type' => $document->getMimeType(),
                        'path' => $document->filename
                    ]);
                }
            }
            Tax::create([
                'user_id' => $request->user_id,
                'adjustment' => $request->adjustment,
                'deposit_id' => $deposit->id,
            ]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('alert-success', $e);
            return false;
        }
    }


    public function update(Request $request, Tax $tax)
    {
        DB::beginTransaction();
        try {
            $balance = Deposit::getCurrentBalance($tax->user);

            $deposit = $tax->deposit;
            if ($deposit)
                $diffAmount = $request->adjustment - $tax->adjustment;
            else
                $diffAmount = $request->adjustment;
            if ($diffAmount < 0 && $balance < -$diffAmount){
                DB::rollBack();
                return false;
            }

            
                $amount =  $diffAmount > 0 ? $diffAmount : -$diffAmount;
                if ($diffAmount != 0 && $deposit) {

                    $deposit = Deposit::create([
                        'uuid' => PaymentInvoice::generateUUID('DP-'),
                        'amount' =>  $amount,
                        'user_id' => $tax->user_id,
                        'balance' => $balance +  $diffAmount,
                        'is_credit' => $diffAmount > 0 ? true : false,
                        'attachment' => '',
                        'last_four_digits' => 'Tax Adjustment Edited',
                        'description' => $request->reasone,
                    ]);
                    $tax->update([
                        'user_id' => $tax->user_id,
                        'adjustment' => $request->adjustment,
                        'deposit_id' => $deposit->id,
                    ]);

                } elseif ($diffAmount != 0 && $deposit==null) {
                    $deposit = Deposit::create([
                        'uuid' => PaymentInvoice::generateUUID('DP-'),
                        'amount' =>  $amount,
                        'user_id' => $tax->user_id,
                        'balance' => $balance + $amount,
                        'is_credit' =>   true,
                        'attachment' => '',
                        'last_four_digits' => 'Tax Adjustment Edited',
                        'description' => $request->reasone,
                    ]);

                    $tax->update([
                        'user_id' => $tax->user_id,
                        'adjustment' => $request->adjustment,
                        'deposit_id' => $deposit->id,
                    ]);
                }

            if ($request->hasFile('attachment')  && $deposit) {

                $deposit->update([
                    'description' => $request->reasone,
                ]);

                foreach ($request->attachment as $attach) {
                    $document = Document::saveDocument($attach);
                    $deposit->depositAttchs()->create([
                        'name' => $document->getClientOriginalName(),
                        'size' => $document->getSize(),
                        'type' => $document->getMimeType(),
                        'path' => $document->filename
                    ]);
                }
            }
            else{
                session()->flash('alert-danger', 'No change made.');                
                return false;
            }
            DB::commit();
            return true;
        } catch (Exception $exception) {
            DB::rollBack();
            session()->flash('alert-danger', 'Error' . $exception->getMessage());
            return false;
        }
    }
}
