<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\AffiliateSale;
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
        }

        $total = $order->total + $commission; 
        
        $order->update([
            'comission' => $commission,
            'total' => $total,
        ]);
        
        $this->updateCommisionBalance($referrer);
    }

    private function removeCommision($order)
    {
        $commission = $order->affiliateSale->commission;
        $order->affiliateSale()->delete();

        $total = $order->total - $commission; 
        
        $order->update([
            'comission' => 0,
            'total' => $total,
        ]);
        
        $this->updateCommisionBalance($order->affiliateSale->user);
    }

    private function updateCommisionBalance($referrer)
    {
        $commissionSetting = $referrer->commissionSetting;
        
        $commissionSetting->update([
            'commission_balance' => AffiliateSale::query()->where('user_id', $referrer->id)->sum('commission')
        ]);
    }
}
