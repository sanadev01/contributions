<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Repositories\BrazilTrackingRepository;

class getBrazilTrackings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'brazil:trackings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Correios Brazil trackings from api';

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

        $brazil_tracking_repository = new BrazilTrackingRepository();

        $brazil_tracking_repository->handle();
        Log::info("Coreos Brazil Trackings is working fine!");
        
        return 0;
    }
}
