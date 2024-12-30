<?php

namespace App\Services\Excel\Export;

use App\Models\ShippingService;
use App\Models\ZoneCountry;
use App\Models\ZoneRate;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PasarExExportUserZoneRate extends AbstractExportService
{
    private $rates;
    private $user;

    private $currentRow = 3;
    private $currentColumn = 0;


    public function __construct()
    {
        $this->user = Auth::user();
        $serviceIds = ShippingService::where('service_sub_class', ShippingService::PasarEx)->first('id');
        $rates = ZoneRate::orderBy('id')->select('selling_rates', 'id', 'user_id', 'shipping_service_id')->where('shipping_service_id',$serviceIds->id)->where('user_id', $this->user->id)->first();
        if (!$rates) {
            $rates = ZoneRate::orderBy('id')->select('selling_rates', 'id', 'user_id', 'shipping_service_id')->where('shipping_service_id',$serviceIds->id)->where('user_id', null)->first();
        }
        if (!$rates) {
            abort(404, 'rate not found');
        }
        $this->rates = json_decode($rates->selling_rates, true);
        parent::__construct();
    }

    public function handle()
    {
        $this->prepareExcelSheet();

        return $this->download();
    }

    private function prepareExcelSheet()
    {
        $this->setUsernameHeader();
        foreach ($this->rates as $zone => $zoneData) {
            $this->currentRow = 3;
            $this->setZoneHeader($zone);
            $this->setZoneData($zoneData, $zone);
            $this->currentColumn = $this->currentColumn + 3;
        } 
    }

    private function setUsernameHeader()
    {
        $this->mergeCells("A1:C1");
        $this->setAlignment('A1', 'center');
        $this->setCellValue('A1', $this->user->name . " " . $this->user->pobox_number);
        $this->setBackgroundColor('A1:C1', '2b5cab');
        $this->setColor('A1:C1', 'FFFFFF');
    }

    private function setZoneHeader($zone)
    {
        $col = $this->getColumnForZone($this->currentColumn);
        $colPlus =$this->getColumnForZone($this->currentColumn+1);
        \Log::info($col);
        $this->mergeCells("{$col}{$this->currentRow}:{$colPlus}{$this->currentRow}");
        $this->setAlignment("{$col}{$this->currentRow}", 'center');
        $this->setCellValue("{$col}{$this->currentRow}", $zone);
        $this->setBackgroundColor("{$col}{$this->currentRow}", '2b5cab');
        $this->setColor("{$col}{$this->currentRow}", 'FFFFFF');
    }
    private function setZoneData($zoneData, $zone)
    {
        $startRow = $this->currentRow + 1;
        $col = $this->getColumnForZone($this->currentColumn);
        $colPlus =$this->getColumnForZone($this->currentColumn+1);
        
        $this->setColumnWidth($col, 15);
        $this->setColumnWidth($colPlus, 15);
        //set start and end zipcode  header
        $this->setCellValue("{$col}{$startRow}", "Zipcode Start at");
        $this->setCellValue($colPlus . "$startRow", "Zipcode End at");
        $this->setBackgroundColor("{$col}{$startRow}", '2b5cab');
        $this->setBackgroundColor($colPlus . "$startRow", '2b5cab');
        $this->setColor("{$col}{$startRow}", 'FFFFFF');
        $this->setColor($colPlus . "$startRow", 'FFFFFF');
        $startRow++;
        //set start and end zipcode 
        $groupRange = getGroupRange($zone);
        $this->setCellValue("{$col}{$startRow}", $groupRange['start']);
        $this->setCellValue($colPlus . "$startRow", $groupRange['end']);
        $this->setBackgroundColor("{$col}{$startRow}", '2b5cab');
        $this->setBackgroundColor($colPlus . "$startRow", '2b5cab');
        $this->setColor("{$col}{$startRow}", 'FFFFFF');
        $this->setColor($colPlus . "$startRow", 'FFFFFF');
        $startRow++; 
        //set weidht and rate header
        $this->setCellValue("{$col}{$startRow}", "Weight");
        $this->setCellValue($colPlus . "$startRow", "Rate");
        $this->setBackgroundColor("{$col}{$startRow}", '2b5cab');
        $this->setBackgroundColor($colPlus . "$startRow", '2b5cab');
        $this->setColor("{$col}{$startRow}", 'FFFFFF');
        $this->setColor($colPlus . "$startRow", 'FFFFFF');
        $startRow++;
        foreach ($zoneData['data'] as $weight => $rate) {
            $this->setCellValue("{$col}{$startRow}", $weight);
            $this->setCellValue($colPlus . "$startRow", $rate);
            $startRow++;
        }
    }
    private function getColumnForZone($columnIndex)
    {
        $columnName = '';
    
        while ($columnIndex >= 0) {
            $columnName = chr(($columnIndex % 26) + 65) . $columnName;
            $columnIndex = intdiv($columnIndex, 26) - 1;
        }
    
        return $columnName;
    }
    
}
