<?php

use App\Models\ZipCode;
use Illuminate\Database\Seeder;

class ZipCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ZipCode::truncate();
        foreach (states(30) as $state) {
            $this->command->info('Imporint Data For State: '.$state->code);
            $this->importZipCodes($state);
        }
    }

    public function importZipCodes($state)
    {
            $curl = curl_init();
                $where = urlencode('{"estado": "'.$state->code.'"}');
            curl_setopt($curl, CURLOPT_URL, 'https://parseapi.back4app.com/classes/CEP?limit=1000000000&where=' . $where);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'X-Parse-Application-Id: 0yGhkskBgC6LMtROXg0SoyHMyl6yYa4SStdCLBpX',
                'X-Parse-Master-Key: Dv9aEYXQtwEQRmeR4BMXX8YadeE9CyNy6PJFJPQe'
            ));
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        $data = curl_exec($curl);
        
        $zipcodes = json_decode($data)->results;

        $this->command->getOutput()->progressStart(count($zipcodes));
        foreach (collect($zipcodes)->chunk(100) as $codeChunks) {
            $insertChunk = [];
            foreach ($codeChunks as $code) {
                $insertChunk [] = [
                    'country_id' => 30,
                    'city' => $code->cidade,
                    'state' => $code->estado,
                    'neighborhood' => $code->bairro,
                    'address' => $code->logradouro,
                    'house_number' => $code->numero,
                    'zipcode' => $code->CEP,
                ];
                $this->command->getOutput()->progressAdvance();
            }

            ZipCode::insert($insertChunk);
        }

        $this->command->getOutput()->progressFinish();
    }
}
