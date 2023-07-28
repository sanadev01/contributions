<?php

use App\Models\Country;
use App\Models\ProfitPackage;
use App\Models\Rate;
use App\Models\ShippingService;
use Illuminate\Database\Seeder;

class ShippingServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->totalExpressSeeder();
    }
    public function totalExpressSeeder()
    {

        $this->command->info("Clearing ShippingService Table Table");
        //total express
        if (!ShippingService::where('service_sub_class', ShippingService::TOTAL_EXPRESS)->exists()) {
            $shippingService = ShippingService::create([
                'name' => 'Total Express',
                'service_sub_class' => ShippingService::TOTAL_EXPRESS,
                'max_length_allowed' => 108,
                'max_width_allowed' => 108,
                'min_width_allowed' => 10,
                'min_length_allowed' => 10,
                'max_sum_of_all_sides' =>  200,
                'max_weight_allowed' => 30,
                'contains_battery_charges' => 1,
                'contains_perfume_charges' => 1,
                'contains_flammable_liquid_charges' => 1,
                'active' => 1,
            ]);

            Rate::create([
                'shipping_service_id' => $shippingService->id,
                'country_id' => Country::Brazil,
                'data' => [['weight' => 100, 'leve' => 9.06], ['weight' => 200, 'leve' => 9.27], ['weight' => 300, 'leve' => 9.48], ['weight' => 400, 'leve' => 9.68], ['weight' => 500, 'leve' => 10.53], ['weight' => 600, 'leve' => 10.73], ['weight' => 700, 'leve' => 10.94], ['weight' => 800, 'leve' => 11.14], ['weight' => 900, 'leve' => 11.35], ['weight' => 1000, 'leve' => 12.21], ['weight' => 1500, 'leve' => 13.87], ['weight' => 2000, 'leve' => 15.54], ['weight' => 2500, 'leve' => 17.22], ['weight' => 3000, 'leve' => 19.1], ['weight' => 3500, 'leve' => 20.78], ['weight' => 4000, 'leve' => 22.46], ['weight' => 4500, 'leve' => 24.15], ['weight' => 5000, 'leve' => 26.01], ['weight' => 5500, 'leve' => 27.86], ['weight' => 6000, 'leve' => 29.72], ['weight' => 6500, 'leve' => 31.57], ['weight' => 7000, 'leve' => 33.42], ['weight' => 7500, 'leve' => 35.28], ['weight' => 8000, 'leve' => 37.13], ['weight' => 8500, 'leve' => 38.99], ['weight' => 9000, 'leve' => 40.84], ['weight' => 9500, 'leve' => 42.69], ['weight' => 10000, 'leve' => 44.55], ['weight' => 10500, 'leve' => 46.4], ['weight' => 11000, 'leve' => 48.26], ['weight' => 11500, 'leve' => 50.11], ['weight' => 12000, 'leve' => 51.96], ['weight' => 12500, 'leve' => 53.82], ['weight' => 13000, 'leve' => 56.7], ['weight' => 13500, 'leve' => 58.56], ['weight' => 14000, 'leve' => 60.41], ['weight' => 14500, 'leve' => 62.26], ['weight' => 15000, 'leve' => 64.12], ['weight' => 15500, 'leve' => 65.97], ['weight' => 16000, 'leve' => 67.83], ['weight' => 16500, 'leve' => 69.68], ['weight' => 17000, 'leve' => 71.53], ['weight' => 17500, 'leve' => 73.39], ['weight' => 18000, 'leve' => 75.24], ['weight' => 18500, 'leve' => 77.1], ['weight' => 19000, 'leve' => 78.95], ['weight' => 19500, 'leve' => 80.8], ['weight' => 20000, 'leve' => 82.66], ['weight' => 20500, 'leve' => 84.51], ['weight' => 21000, 'leve' => 86.37], ['weight' => 21500, 'leve' => 88.22], ['weight' => 22000, 'leve' => 90.07], ['weight' => 22500, 'leve' => 91.93], ['weight' => 23000, 'leve' => 93.78], ['weight' => 23500, 'leve' => 95.64], ['weight' => 24000, 'leve' => 97.49], ['weight' => 24500, 'leve' => 99.34], ['weight' => 25000, 'leve' => 101.2], ['weight' => 25500, 'leve' => 103.05], ['weight' => 26000, 'leve' => 104.91], ['weight' => 26500, 'leve' => 106.76], ['weight' => 27000, 'leve' => 108.61], ['weight' => 27500, 'leve' => 110.47], ['weight' => 28000, 'leve' => 112.32], ['weight' => 28500, 'leve' => 114.18], ['weight' => 29000, 'leve' => 116.03], ['weight' => 29500, 'leve' => 117.88], ['weight' => 30000, 'leve' => 119.74]],
            ]);

            ProfitPackage::create([
                'shipping_service_id' => $shippingService->id,
                'name' => 'Total Express Package',
                'type' => 'custom',
                'data' => [["min_weight" =>0,"max_weight" =>"100","value" =>"8.17"],["min_weight" =>101,"max_weight" =>"200","value" =>"12.14"],["min_weight" =>201,"max_weight" =>"300","value" =>"13.38"],["min_weight" =>301,"max_weight" =>"400","value" =>"14.66"],["min_weight" =>401,"max_weight" =>"500","value" =>"24.28"],["min_weight" =>501,"max_weight" =>"600","value" =>"28.3"],["min_weight" =>601,"max_weight" =>"700","value" =>"24.14"],["min_weight" =>701,"max_weight" =>"800","value" =>"25.14"],["min_weight" =>801,"max_weight" =>"900","value" =>"26.21"],["min_weight" =>901,"max_weight" =>"1000","value" =>"40.44"],["min_weight" =>1001,"max_weight" =>"1500","value" =>"59.95"],["min_weight" =>1501,"max_weight" =>"2000","value" =>"57.32"],["min_weight" =>2001,"max_weight" =>"2500","value" =>"61.33"],["min_weight" =>2501,"max_weight" =>"3000","value" =>"64.52"],["min_weight" =>3001,"max_weight" =>"3500","value" =>"65.39"],["min_weight" =>3501,"max_weight" =>"4000","value" =>"67.66"],["min_weight" =>4001,"max_weight" =>"4500","value" =>"69.63"],["min_weight" =>4501,"max_weight" =>"5000","value" =>"68.07"],["min_weight" =>5001,"max_weight" =>"5500","value" =>"68.28"],["min_weight" =>5501,"max_weight" =>"6000","value" =>"68.56"],["min_weight" =>6001,"max_weight" =>"6500","value" =>"79.44"],["min_weight" =>6501,"max_weight" =>"7000","value" =>"79.03"],["min_weight" =>7001,"max_weight" =>"7500","value" =>"78.64"],["min_weight" =>7501,"max_weight" =>"8000","value" =>"78.26"],["min_weight" =>8001,"max_weight" =>"8500","value" =>"77.97"],["min_weight" =>8501,"max_weight" =>"9000","value" =>"77.64"],["min_weight" =>9001,"max_weight" =>"9500","value" =>"77.4"],["min_weight" =>9501,"max_weight" =>"10000","value" =>"77.18"],["min_weight" =>10001,"max_weight" =>"10500","value" =>"76.92"],["min_weight" =>10501,"max_weight" =>"11000","value" =>"76.75"],["min_weight" =>11001,"max_weight" =>"11500","value" =>"76.54"],["min_weight" =>11501,"max_weight" =>"12000","value" =>"82.74"],["min_weight" =>12001,"max_weight" =>"12500","value" =>"82.37"],["min_weight" =>12501,"max_weight" =>"13000","value" =>"81.98"],["min_weight" =>13001,"max_weight" =>"13500","value" =>"78.36"],["min_weight" =>13501,"max_weight" =>"14000","value" =>"78.14"],["min_weight" =>14001,"max_weight" =>"14500","value" =>"77.95"],["min_weight" =>14501,"max_weight" =>"15000","value" =>"77.79"],["min_weight" =>15001,"max_weight" =>"15500","value" =>"77.6"],["min_weight" =>15501,"max_weight" =>"16000","value" =>"77.44"],["min_weight" =>16001,"max_weight" =>"16500","value" =>"77.28"],["min_weight" =>16501,"max_weight" =>"17000","value" =>"81.72"],["min_weight" =>17001,"max_weight" =>"17500","value" =>"81.48"],["min_weight" =>17501,"max_weight" =>"18000","value" =>"81.22"],["min_weight" =>18001,"max_weight" =>"18500","value" =>"80.99"],["min_weight" =>18501,"max_weight" =>"19000","value" =>"80.77"],["min_weight" =>19001,"max_weight" =>"19500","value" =>"80.57"],["min_weight" =>19501,"max_weight" =>"20000","value" =>"80.37"],["min_weight" =>20001,"max_weight" =>"20500","value" =>"80.17"],["min_weight" =>20501,"max_weight" =>"21000","value" =>"80"],["min_weight" =>21001,"max_weight" =>"21500","value" =>"79.81"],["min_weight" =>21501,"max_weight" =>"22000","value" =>"83.27"],["min_weight" =>22001,"max_weight" =>"22500","value" =>"83.04"],["min_weight" =>22501,"max_weight" =>"23000","value" =>"82.8"],["min_weight" =>23001,"max_weight" =>"23500","value" =>"82.6"],["min_weight" =>23501,"max_weight" =>"24000","value" =>"82.37"],["min_weight" =>24001,"max_weight" =>"24500","value" =>"82.18"],["min_weight" =>24501,"max_weight" =>"25000","value" =>"81.99"],["min_weight" =>25001,"max_weight" =>"25500","value" =>"81.8"],["min_weight" =>25501,"max_weight" =>"26000","value" =>"81.63"],["min_weight" =>26001,"max_weight" =>"26500","value" =>"81.44"],["min_weight" =>26501,"max_weight" =>"27000","value" =>"84.27"],["min_weight" =>27001,"max_weight" =>"27500","value" =>"84.06"],["min_weight" =>27501,"max_weight" =>"28000","value" =>"83.85"],["min_weight" =>28001,"max_weight" =>"28500","value" =>"83.66"],["min_weight" =>28501,"max_weight" =>"29000","value" =>"83.46"],["min_weight" =>29001,"max_weight" =>"29500","value" =>"83.28"],["min_weight" =>29501,"max_weight" =>"30000","value" =>"83.11"]], 
            ]);
        }
    }
}
