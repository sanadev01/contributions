<?php

namespace App\Repositories;
use Exception;
use App\Models\Tax;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Models\PaymentInvoice;
use Illuminate\Support\Facades\DB;
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
                'user_id' => $request->user_id,
                'balance' => $balance +  $request->adjustment,
                'is_credit' => true,
                'attachment' => '',
                'last_four_digits' => 'Tax Adjustment',
                'description' =>  $request->reason,
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
            if ($tax->adjustment < 0) {
                session()->flash('alert-danger', 'The adjustment must be nagative');
                DB::rollBack();
                return false;
            } 
            if ($balance < $diffAmount) {
                session()->flash('alert-danger', 'Error While Update Tax! Check Your Account Balance');
                DB::rollBack();
                return false;
            }
            $amount =  $diffAmount > 0 ? $diffAmount : -$diffAmount;
            $isCredit =$diffAmount > 0 ? true : false;

            if ($diffAmount != 0 && $deposit) {
                $deposit = $this->createTaxDeposit($tax,$amount,$diffAmount,$isCredit,$request->reason);
                $this->updateTax($tax, $request->adjustment, $deposit->id);

            } elseif ($diffAmount != 0 && $deposit == null) {
                $deposit = $this->createTaxDeposit($tax,$amount,$amount,true,$request->reason);
                $this->updateTax($tax, $request->adjustment, $deposit->id);

            }
            if ($deposit) {
                $deposit->update([
                    'description' => $request->reason,
                ]);
            }
            if ($request->hasFile('attachment') && $deposit) {
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
            DB::commit();
            return true;
        } catch (Exception $exception) {
            DB::rollBack();
            session()->flash('alert-danger', 'Error' . $exception->getMessage());
            return false;
        }
    }
    public function createTaxDeposit($tax,$amount,$diffrence,$isCredit,$description)
    {
        $balance = Deposit::getCurrentBalance($tax->user);
        return Deposit::create([
            'uuid' => PaymentInvoice::generateUUID('DP-'),
            'amount' =>  $amount,
            'user_id' => $tax->user_id,
            'balance' => $balance + $diffrence,
            'is_credit' =>   $isCredit,
            'attachment' => '',
            'last_four_digits' => 'Tax Adjustment Edited',
            'description' => $description ,

        ]);
    }
    public function updateTax($tax,$adjustment,$deposit_id)
    {
        $tax->update([ 
            'adjustment' => $adjustment,
            'deposit_id' => $deposit_id,
        ]);
    }
}
