<?php


namespace App\Services\Excel\Export;

use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class AbstractCsvExportService
{
    protected $useHeaders = true;

    public function __construct()
    {

    }

    public abstract function handle();

    protected abstract function prepareHeaders(): array;

    protected abstract function prepareData(): array;


    protected function download()
    {
        $filename = md5(microtime()).".csv";
        $headers = array(
            'Content-Type'        => 'text/csv',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Disposition' => 'attachment; filename='.$filename,
            'Expires'             => '0',
            'Pragma'              => 'public',
        );
    
        $response = new StreamedResponse(function(){
            // Open output stream
            $handle = fopen('php://output', 'w');
    
            // // Add CSV headers
            if ( $this->useHeaders ){
                fputcsv($handle, $this->prepareHeaders());
            }
    
            foreach($this->prepareData() as $line){
                fputcsv($handle, $line);
            }
    
            // // Close the output stream
            fclose($handle);
        }, 200, $headers);

        return $response->send();
    }
}