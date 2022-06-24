<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ZPLConverter
{
    private $appUrl;

    public function __construct()
    {
        $this->appUrl = 'http://api.labelary.com/v1/printers/8dpmm/labels/4x6/0/';
    }

    public function convertToPdf($data)
    {
        try {
            
            $response = Http::withHeaders([
                'Accept' => 'application/pdf',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'X-Rotation' => '180',
            ])->post($this->appUrl,[
                $data => null
            ]);
            
            if ($response->status() == 200) {
                return [
                    'success' => true,
                    'label' => $response->body(),
                ];
            }

            return [
                'success' => false,
                'label' => null,
                'message' => 'error while converting to pdf',
            ];

        } catch (\Exception $ex) {
            return [
                'success' => false,
                'label' => null,
                'message' => $ex->getMessage(),
            ];
        }
    }
}
