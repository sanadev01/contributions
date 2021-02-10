<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\AffiliateSale;
use App\Models\CommissionSetting;
use App\Services\Calculators\CommissionCalculator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CalculateCommission
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderPaid  $event
     * @return void
     */
    public function handle(OrderPaid $orderPaid)
    {

        foreach ($orderPaid->orders as  $order){

            if ( $orderPaid->isPaid ){
                $this->addCommision($order);
            }

            if ( !$orderPaid->isPaid && $order->affiliateSale ){
                $this->removeCommision($order);
            }
            
        }

    }

    private function addCommision($order)
    {
        $commissionCalculator = new CommissionCalculator($order);
        $commission = $commissionCalculator->getCommission();

        

        if($commissionCalculator->hasReferrer()){
            
            $referrer = $commissionCalculator->hasReferrer();
            
            $order->addAffiliateCommissionSale($referrer, $commissionCalculator, true);

            $this->updateCommisionBalance($referrer);
        }

        $total = $order->total + $commission; 
        
        $order->update([
            'comission' => $commission,
            'total' => $total,
        ]);
        
        
    }

    private function removeCommision($order)
    {
        $commission = $order->affiliateSale->commission;
        $user = $order->affiliateSale->user;
        $order->affiliateSale()->delete();
        
        $this->updateCommisionBalance($user);
        
        $total = $order->total - $commission; 
        
        $order->update([
            'comission' => 0,
            'total' => $total,
        ]);
        
        
    }

    private function updateCommisionBalance($referrer)
    {
        $commissionSetting = $referrer->commissionSetting;
       
        if(!$commissionSetting){
            return $this->addCommisionSetting($referrer);
        }

        return $commissionSetting->update([
            'commission_balance' => AffiliateSale::query()->where('user_id', $referrer->id)->sum('commission')
        ]);
    }

    private function addCommisionSetting($referrer)
    {
        return CommissionSetting::create([
            'user_id' => $referrer->id,
            'value' => setting('VALUE',$default = null, $userId = null, $admin = true),
            'type' => setting('TYPE',$default = null, $userId = null, $admin = true),
            'commission_balance' => AffiliateSale::query()->where('user_id', $referrer->id)->sum('commission')
        ]);
    }


}
