<?php

namespace App\Services\Excel\Export;
use App\Models\Order;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Repositories\Reports\AuditReportsRepository;

class AuditReportExport extends AbstractExportService
{
    private $orders;

    private $currentRow = 1;

    public function __construct(Collection $orders)
    {
        $this->orders = $orders;

        parent::__construct();
    }

    public function handle()
    {
        $this->prepareExcelSheet();

        return $this->download();
    }

    private function prepareExcelSheet()
    {
        $this->setExcelHeaderRow();

        $row = $this->currentRow;
        $auditReportsRepository = new AuditReportsRepository;
        foreach ($this->orders as $order) {
            $user = $order->user;
            $rates = $auditReportsRepository->getRates($order);
            $this->setCellValue('A'.$row, $order->order_date);
            $this->setCellValue('B'.$row, $user->name);
            $this->setCellValue('C'.$row, $order->warehouse_number);
            $this->setCellValue('D'.$row, (string)$order->corrios_tracking_code);
            $this->setCellValue('E'.$row, $order->getWeight('kg'));
            $this->setCellValue('F'.$row, $order->getWeight('lbs'));
            $this->setCellValue('G'.$row, number_format($order->dangrous_goods,2));
            $this->setCellValue('H'.$row, number_format($order->services->sum('price'),2));
            $this->setCellValue('I'.$row, number_format($order->shipping_value,2));
            $this->setCellValue('J'.$row, number_format($order->gross_total,2));
            $this->setCellValue('K'.$row, $rates['accrualRate']);
            // $this->setCellValue('K'.$row, $rates['profitPackageRate']);
            $row++;
        }
        $this->currentRow = $row;
        $this->setCellValue('G'.$row, "=SUM(G1:G{$row})");
        $this->setCellValue('H'.$row, "=SUM(H1:H{$row})");
        $this->setCellValue('I'.$row, "=SUM(I1:I{$row})");
        $this->setCellValue('J'.$row, "=SUM(J1:J{$row})");
        // $this->setCellValue('K'.$row, "=SUM(K1:K{$row})");
        $this->mergeCells("A{$row}:F{$row}");
        $this->setBackgroundColor("A{$row}:L{$row}", 'adfb84');
        $this->setAlignment('A'.$row, Alignment::VERTICAL_CENTER);
        $this->setCellValue('A'.$row, 'Total Order: '.$this->orders->count());
    }


    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'Date');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'Name');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'Order ID#');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'Tracking Code');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'Weight (Kg)');

        $this->setColumnWidth('F', 25);
        $this->setCellValue('F1', 'Weight (Lbs)');

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'Battery/Perfume/Flameable');
        
        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'Additional Charges');

        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1', 'Shipping Value');

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', 'Total Charges');

        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1', 'Corrieos Charges');
        
        // $this->setColumnWidth('L', 20);
        // $this->setCellValue('L1', 'Profit');

        $this->setBackgroundColor('A1:K1', '2b5cab');
        $this->setColor('A1:K1', 'FFFFFF');

        $this->currentRow++;
    }

    private function checkValue($value)
    {
        if($value == 0){
            return 0.00;
        }
        return $value;
    }
}
