<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\CorrieosBrazilLabelRepository;
use App\Models\Order;

class UpdateAnjunOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:anjun-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Anjun orders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $orders = [
            'NA305265475BR',
            'NA303453405BR',
            'NA303456101BR',
            'NA303450381BR',
            'NA303474723BR',
            'NA303482146BR',
            'NA303535555BR',
            'NA303485479BR',
            'NA299712512BR',
            'NA303569973BR',
            'NA299643975BR',
            'NA299644035BR',
            'NA303541241BR',
            'NA303487090BR',
            'NA303571795BR',
            'NA303503637BR',
            'NA303507355BR',
            'NA299620759BR',
            'NA299642357BR',
            'NA299910186BR',
            'NA299543938BR',
            'NA299919517BR',
            'NA299614930BR',
            'NA299919429BR',
            'NA299919123BR',
            'NA299910124BR',
            'NA299648108BR',
            'NA299643953BR',
            'NA299544403BR',
            'NA299646226BR',
            'NA299642286BR',
            'NA299544235BR',
            'NA299539978BR',
            'NA299620630BR',
            'NA299539417BR',
            'NA299643882BR',
            'NA299910257BR',
            'NA299643936BR',
            'NA299919548BR',
            'NA299605500BR',
            'NA299646597BR',
            'NA299543805BR',
            'NA299648125BR',
            'NA299648200BR',
            'NA299648111BR',
            'NA299910583BR',
            'NA299643905BR',
            'NA299646190BR',
            'NA299642595BR',
            'NA299606010BR',
            'NA299648227BR',
            'NA299644110BR',
            'NA299644083BR',
            'NA299539893BR',
            'NA299910455BR',
            'NA299642309BR',
            'NA299539244BR',
            'NA299644097BR',
            'NA299605535BR',
            'NA299648235BR',
            'NA299620970BR',
            'NA299644106BR',
            'NA299910359BR',
            'NA299544624BR',
            'NA299644018BR',
            'NA299644049BR',
            'NA299614400BR',
            'NA299919463BR',
            'NA299910521BR',
            'NA299605575BR',
            'NA299643922BR',
            'NA299544451BR',
            'NA303543106BR',
            'NA303537803BR',
            'NA303553749BR',
            'NA303581033BR',
            'NA303541745BR',
            'NA303569678BR',
            'NA303563030BR',
            'NA303556303BR',
            'NA303562710BR',
            'NA303564517BR',
            'NA303565203BR',
            'NA303555957BR',
            'NA303564874BR',
            'NA303565530BR',
            'NA306096257BR',
            'NA303602515BR',
            'NA303602317BR',
            'NA303535997BR',
            'NA303568730BR',
            'NA303580744BR',
            'NA303580130BR',
            'NA303473294BR',
            'NA303461163BR',
            'NA303471775BR',
            'NA303568187BR',
            'NA303472299BR',
            'NA303569409BR',
            'NA303472002BR',
            'NA306204331BR',
            'NA306205045BR',
            'NA303555135BR',
            'NA303504178BR',
            'NA303531553BR',
            'NA303486647BR',
            'NA303530235BR',
            'NA303487404BR',
            'NA303502322BR',
            'NA303488285BR',
            'NA303488679BR',
            'NA303502027BR',
            'NA303458385BR',
            'NA303489728BR',
            'NA303484751BR',
            'NA303531955BR',
            'NA303531584BR',
            'NA303579030BR',
            'NA303577802BR',
            'NA303532307BR',
            'NA303501517BR',
            'NA303532602BR',
            'NA303578391BR',
            'NA303602175BR',
            'NA303570384BR',
            'NA303486134BR',
            'NA303483331BR',
            'NA303471510BR',
            'NA303567867BR',
            'NA303571549BR',
            'NA303508532BR',
            'NA303540918BR',
            'NA303485880BR',
            'NA303581696BR',
            'NA303509250BR',
            'NA303541737BR',
            'NA303557153BR',
            'NA303565509BR',
            'NA303553390BR',
            'NA303540294BR',
            'NA303565217BR',
            'NA303540665BR',
            'NA303571005BR',
            'NA305333051BR',
            'NA303554660BR',
            'NA303562944BR',
            'NA303542750BR',
            'NA303565906BR',
            'NA303533660BR',
            'NA303601740BR',
            'NA303539954BR',
            'NA303532837BR',
            'NA305332997BR',
            'NA305344275BR',
            'NA305333167BR',
            'NA305344139BR',
            'NA303601197BR',
            'NA303532465BR',
            'NA303570486BR',
            'NA305333105BR',
            'NA303553823BR',
            'NA303556572BR',
            'NA303563573BR',
            'NA303570733BR',
            'NA303567080BR',
            'NA303474935BR',
            'NA303474017BR',
            'NA303475511BR',
            'NA303482937BR',
            'NA303473776BR',
            'NA303482163BR',
            'NA303481208BR',
            'NA303485522BR',
            'NA303483535BR'
        ];
    
        $anjunOrders = Order::whereIn('corrios_tracking_code', $orders)->get();
        $corriosBrazilRepository = new CorrieosBrazilLabelRepository();
    
        foreach ($anjunOrders as $order) {
           $order->update([
            'shipping_service_id' => 1,
            'shipping_service_name' => 'Packet Standard'
           ]);
    
           $corriosBrazilRepository->update($order);
        }

        \Log::info('Anjun Orders Updated');
    }
}
