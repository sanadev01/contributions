<?php

use App\Models\Order;
use App\Models\Deposit;
use App\Models\PaymentInvoice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class BalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orders = [
            [
                'order_id' => '68254',
                'amount' => '50.00',
            
            ],
            [
                'order_id' => '68277',
                'amount' => '37.50',
            
            ],
            [
                'order_id' => '68313',
                'amount' => '37.50',
            
            ],
            [
                'order_id' => '68635',
                'amount' => '64.50',
            
            ],
            [
                'order_id' => '68243',
                'amount' => '10.38',
            
            ],
            [
                'order_id' => '68244',
                'amount' => '32.50',
            
            ],
            [
                'order_id' => '68245',
                'amount' => '13.00',
            
            ],
            [
                'order_id' => '68246',
                'amount' => '37.50',
            
            ],
            [
                'order_id' => '68247',
                'amount' => '16.07',
            
            ],
            [
                'order_id' => '68248',
                'amount' => '10.72',
            
            ],
            [
                'order_id' => '68249',
                'amount' => '43.25',
            
            ],
            [
                'order_id' => '68251',
                'amount' => '15.72',
            
            ],
            [
                'order_id' => '68252',
                'amount' => '14.25',
            
            ],
            [
                'order_id' => '68253',
                'amount' => '50.00',
            
            ],
            [
                'order_id' => '68255',
                'amount' => '11.41',
            
            ],
            [
                'order_id' => '68256',
                'amount' => '16.32',
            
            ],
            [
                'order_id' => '68257',
                'amount' => '16.41',
            
            ],
            [
                'order_id' => '68258',
                'amount' => '31.25',
            
            ],
            [
                'order_id' => '68259',
                'amount' => '16.75',
            
            ],
            [
                'order_id' => '68260',
                'amount' => '25.00',
            
            ],
            [
                'order_id' => '68261',
                'amount' => '10.38',
            
            ],
            [
                'order_id' => '68262',
                'amount' => '21.75',
            
            ],
            [
                'order_id' => '68263',
                'amount' => '31.25',
            
            ],
            [
                'order_id' => '68264',
                'amount' => '21.75',
            
            ],
            [
                'order_id' => '68265',
                'amount' => '64.50',
            
            ],
            [
                'order_id' => '68266',
                'amount' => '31.25',
            
            ],
            [
                'order_id' => '68267',
                'amount' => '21.75',
            
            ],
            [
                'order_id' => '68268',
                'amount' => '38.75',
            
            ],
            [
                'order_id' => '68269',
                'amount' => '14.25',
            
            ],
            [
                'order_id' => '68270',
                'amount' => '31.25',
            
            ],
            [
                'order_id' => '68271',
                'amount' => '30.97',
            
            ],
            [
                'order_id' => '68272',
                'amount' => '16.75',
            
            ],
            [
                'order_id' => '68273',
                'amount' => '16.07',
            
            ],
            [
                'order_id' => '68274',
                'amount' => '15.50',
            
            ],
            [
                'order_id' => '68275',
                'amount' => '16.41',
            
            ],
            [
                'order_id' => '68276',
                'amount' => '10.38',
            
            ],
            [
                'order_id' => '68278',
                'amount' => '31.25',
            
            ],
            [
                'order_id' => '68279',
                'amount' => '45.00',
            
            ],
            [
                'order_id' => '68280',
                'amount' => '79.25',
            
            ],
            [
                'order_id' => '68281',
                'amount' => '16.07',
            
            ],
            [
                'order_id' => '68282',
                'amount' => '21.75',
            
            ],
            [
                'order_id' => '68283',
                'amount' => '38.75',
            
            ],
            [
                'order_id' => '68284',
                'amount' => '26.25',
            
            ],
            [
                'order_id' => '68285',
                'amount' => '16.41',
            
            ],
            [
                'order_id' => '68286',
                'amount' => '11.07',
            
            ],
            [
                'order_id' => '68287',
                'amount' => '16.07',
            
            ],
            [
                'order_id' => '68288',
                'amount' => '16.75',
            
            ],
            [
                'order_id' => '68289',
                'amount' => '15.38',
            
            ],
            [
                'order_id' => '68290',
                'amount' => '16.07',
            
            ],
            [
                'order_id' => '68291',
                'amount' => '11.41',
            
            ],
            [
                'order_id' => '68292',
                'amount' => '16.07',
            
            ],
            [
                'order_id' => '68293',
                'amount' => '16.41',
            
            ],
            [
                'order_id' => '68294',
                'amount' => '26.25',
            
            ],
            [
                'order_id' => '68295',
                'amount' => '19.25',
            
            ],
            [
                'order_id' => '68296',
                'amount' => '50.00',
            
            ],
            [
                'order_id' => '68297',
                'amount' => '11.41',
            
            ],
            [
                'order_id' => '68298',
                'amount' => '16.75',
            
            ],
            [
                'order_id' => '68299',
                'amount' => '16.07',
            
            ],
            [
                'order_id' => '68300',
                'amount' => '16.41',
            
            ],
            [
                'order_id' => '68301',
                'amount' => '13.00',
            
            ],
            [
                'order_id' => '68302',
                'amount' => '31.25',
            
            ],
            [
                'order_id' => '68303',
                'amount' => '31.25',
            
            ],
            [
                'order_id' => '68305',
                'amount' => '43.75',
            
            ],
            [
                'order_id' => '68306',
                'amount' => '15.50',
            
            ],
            [
                'order_id' => '68307',
                'amount' => '48.25',
            
            ],
            [
                'order_id' => '68308',
                'amount' => '11.75',
            
            ],
            [
                'order_id' => '68309',
                'amount' => '38.75',
            
            ],
            [
                'order_id' => '68310',
                'amount' => '38.75',
            
            ],
            [
                'order_id' => '68311',
                'amount' => '43.75',
            
            ],
            [
                'order_id' => '68312',
                'amount' => '31.25',
            
            ],
            [
                'order_id' => '68314',
                'amount' => '16.07',
            
            ],
            [
                'order_id' => '68315',
                'amount' => '31.75',
            
            ],
            [
                'order_id' => '68316',
                'amount' => '20.50',
            
            ],
            [
                'order_id' => '68317',
                'amount' => '20.50',
            
            ],
            [
                'order_id' => '68318',
                'amount' => '37.50',
            
            ],
            [
                'order_id' => '68319',
                'amount' => '18.00',
            
            ],
            [
                'order_id' => '68320',
                'amount' => '43.75',
            
            ],
            [
                'order_id' => '68321',
                'amount' => '45.00',
            
            ],
            [
                'order_id' => '68322',
                'amount' => '171.40',
            
            ],
            [
                'order_id' => '68323',
                'amount' => '84.25',
            
            ],
            [
                'order_id' => '68324',
                'amount' => '79.25',
            
            ],
            [
                'order_id' => '68325',
                'amount' => '10.38',
            
            ],
            [
                'order_id' => '68326',
                'amount' => '10.38',
            
            ],
            [
                'order_id' => '68327',
                'amount' => '15.38',
            
            ],
            [
                'order_id' => '68328',
                'amount' => '10.72',
            
            ],
            [
                'order_id' => '68329',
                'amount' => '16.41',
            
            ],
            [
                'order_id' => '68330',
                'amount' => '16.41',
            
            ],
            [
                'order_id' => '68331',
                'amount' => '15.72',
            
            ],
            [
                'order_id' => '68332',
                'amount' => '22.07',
            
            ],
            [
                'order_id' => '68333',
                'amount' => '16.75',
            
            ],
            [
                'order_id' => '68334',
                'amount' => '15.72',
            
            ],
            [
                'order_id' => '68335',
                'amount' => '16.41',
            
            ],
            [
                'order_id' => '68336',
                'amount' => '16.07',
            
            ],
            [
                'order_id' => '68337',
                'amount' => '21.75',
            
            ],
            [
                'order_id' => '68338',
                'amount' => '31.25',
            
            ],
            [
                'order_id' => '68339',
                'amount' => '18.00',
            
            ],
            [
                'order_id' => '68340',
                'amount' => '15.72',
            
            ],
            [
                'order_id' => '68341',
                'amount' => '16.41',
            
            ],
            [
                'order_id' => '68342',
                'amount' => '31.25',
            
            ],
            [
                'order_id' => '68343',
                'amount' => '17.78',
            
            ],
            [
                'order_id' => '68344',
                'amount' => '50.00',
            
            ],
            [
                'order_id' => '68345',
                'amount' => '20.50',
            
            ],
            [
                'order_id' => '68346',
                'amount' => '31.25',
            
            ],
            [
                'order_id' => '68347',
                'amount' => '31.25',
            
            ],
            [
                'order_id' => '68348',
                'amount' => '16.07',
            
            ],
            [
                'order_id' => '68349',
                'amount' => '16.41',
            
            ],
            [
                'order_id' => '68350',
                'amount' => '16.41',
            
            ],
            [
                'order_id' => '68351',
                'amount' => '18.25',
            
            ],
            [
                'order_id' => '68352',
                'amount' => '146.10',
            
            ],
            [
                'order_id' => '68353',
                'amount' => '34.82',
            
            ],
            [
                'order_id' => '68628',
                'amount' => '25.50',
            
            ],
            [
                'order_id' => '68629',
                'amount' => '18.00',
            
            ],
            [
                'order_id' => '68630',
                'amount' => '20.72',
            
            ],
            [
                'order_id' => '68631',
                'amount' => '15.72',
            
            ],
            [
                'order_id' => '68632',
                'amount' => '21.07',
            
            ],
            [
                'order_id' => '68633',
                'amount' => '23.00',
            
            ],
            [
                'order_id' => '68634',
                'amount' => '30.00',
            
            ],
            [
                'order_id' => '68667',
                'amount' => '26.25',
            
            ],
            [
                'order_id' => '68668',
                'amount' => '13.00',
            
            ],
            [
                'order_id' => '68669',
                'amount' => '13.00',
            
            ],
            [
                'order_id' => '68670',
                'amount' => '26.25',
            
            ],
            [
                'order_id' => '68671',
                'amount' => '16.75',
            
            ],
            [
                'order_id' => '68672',
                'amount' => '11.75',
            
            ],
            [
                'order_id' => '68673',
                'amount' => '10.72',
            
            ],
            [
                'order_id' => '68674',
                'amount' => '14.25',
            
            ],
            [
                'order_id' => '68675',
                'amount' => '11.41',
            
            ],
            [
                'order_id' => '68676',
                'amount' => '11.75',
            
            ],
            [
                'order_id' => '68677',
                'amount' => '32.50',
            
            ],
            [
                'order_id' => '68678',
                'amount' => '45.00',
            
            ],
            [
                'order_id' => '68679',
                'amount' => '13.00',
            
            ],
            [
                'order_id' => '68680',
                'amount' => '26.25',
            
            ],
            [
                'order_id' => '68681',
                'amount' => '10.72',
            
            ],
            [
                'order_id' => '68750',
                'amount' => '32.50',
            
            ],
            
        ];

        foreach($orders as $order ){
            if(optional($order)['order_id']){

                $findOrder = Order::find(optional($order)['order_id']);
                if($findOrder){

                
                    $lastTransaction = Deposit::query()->where('user_id',$findOrder->user_id)->latest('id')->first();
                    if ( !$lastTransaction ){
                        $getCurrentBalance = 0;
                    }
                    $getCurrentBalance = $lastTransaction->balance;
                    
                    if($findOrder){
                        $deposit = Deposit::create([
                            'uuid' => PaymentInvoice::generateUUID('DP-'),
                            'amount' => $order['amount'],
                            'user_id' => $findOrder->user_id,
                            'balance' => $getCurrentBalance - $order['amount'],
                            'is_credit' => false,
                        ]);
                
                        if ( $findOrder ){
                            $findOrder->deposits()->sync($deposit->id);
                        }
                    }
                }
            }

        }
    }
    
}
