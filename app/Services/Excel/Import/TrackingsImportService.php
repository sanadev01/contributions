<?php

namespace App\Services\Excel\Import;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Services\Excel\AbstractImportService;

class TrackingsImportService extends AbstractImportService
{
    protected $container;

    public function __construct(UploadedFile $file, $container)
    {
        $this->container = $container;
        $filename = $this->importFile($file);
        
        parent::__construct(
            $this->getStoragePath($filename)
        );
    }
    
    public function handle()
    {
        return $this->readTrackingsFromFile();
    }
    
    public function readTrackingsFromFile()
    {
        $limit = $this->noRows > 500? 500: $this->noRows;
        foreach (range(1, $limit) as $row) {
            $codes[] =  $this->workSheet->getCell('A'.$row)->getValue();
        }
        foreach($codes as $code) {
            $order = DB::table('orders')->where('corrios_tracking_code', $code)->value('id');
            if($order) {
                $this->container->orders()->attach($order);
            }
        }
        return $this->container;
    }
}
