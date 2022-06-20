<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderService;
use App\Models\Recipient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Clearing Orders and related Table Table");
        DB::table('orders')->truncate();
        DB::table('order_items')->truncate();
        DB::table('order_services')->truncate();
        DB::table('recipients')->truncate();

        $this->command->info("Importing Orders Table");
        $orders = $this->loadOrdersFromFile();
        $this->command->getOutput()->progressStart(count($orders));
        foreach ($orders as $key => $order) {
            DB::beginTransaction();
            try {
                
                $this->command->getOutput()->progressAdvance();

                Order::create(
                    collect($order)->except(['order_value','items','services','recipient'])->all()
                );

                if ( $order['items'] ){
                    OrderItem::insert($order['items']);
                }
                if ( $order['services'] ){
                    OrderService::insert($order['services']);
                }

                if ( $order['recipient'] ){
                    Recipient::create($order['recipient']);
                }

                DB::commit();

            } catch (\Exception $ex) {
                DB::rollback();
                $this->command->error($ex->getMessage());
            }
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info("Import Success Full");

    }

    public function loadOrdersFromFile()
    {
        $contents = Storage::get('upgrade/orders.json');
        return json_decode($contents,true);
    }
}
