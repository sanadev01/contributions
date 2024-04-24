<?php

namespace App\Services\Excel\Export;

use App\Services\Excel\Export\AbstractExportService;
use App\Models\Warehouse\DeliveryBill;
class ExportPasarExManfestService extends AbstractExportService
{ 

    private $currentRow = 1;
    private $date;

    public function __construct(DeliveryBill $deliveryBill)
    {
        $this->deliveryBill = $deliveryBill;
        $this->date = $deliveryBill->created_at->format('Y-m-d');
        
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
            foreach ($this->deliveryBill->containers as $container) {
                  foreach ($container->orders as $order) {
                    $this->setCellValue('A'. $row, $order->corrios_tracking_code);
                    $this->setCellValue('B'. $row, $this->date);
                    $this->setCellValue('C'. $row, $order->getSenderFullName());
                    $this->setCellValue('D'. $row, $order->sender_phone);
                    $this->setCellValue('E'. $row, $order->sender_email);
                    $this->setCellValue('F'. $row, $order->sender_address);
                    $this->setCellValue('G'. $row, $order->sender_phone);
                    $this->setCellValue('H'. $row, 'postal code'); // Sender postal code
                    $this->setCellValue('I'. $row, $order->name_city);
                    $this->setCellValue('J'. $row, $order->senderState->name);
                    $this->setCellValue('K'. $row, $order->senderCountry->name);  
                    
                    $this->setCellValue('L'. $row, ($order->recipient)->fullName());
                    $this->setCellValue('M'. $row, ($order->recipient)->phone);
                    $this->setCellValue('N'. $row, ($order->recipient)->email);
                    $this->setCellValue('O'. $row, ($order->recipient)->getAddress());
                    $this->setCellValue('P'. $row, ($order->recipient)->phone);
                    $this->setCellValue('Q'. $row, ($order->recipient)->zipcode);
                    
                    $this->setCellValue('R'. $row, ($order->recipient)->city);
                    $this->setCellValue('S'. $row, ($order->recipient)->state->name);
                    $this->setCellValue('T'. $row, ($order->recipient)->country->code);
                    $this->setCellValue('U'. $row, 'CONTENIDO'); // Content
                    $this->setCellValue('V'. $row, count($order->items)); // Pieces 
                    $this->setCellValue('W'. $row, $order->weight); // Weight in pounds
                    $this->setCellValue('X'. $row, $order->weight.' '.$order->measurement_unit); // Weight in kilograms
                    $this->setCellValue('Y'. $row, $order->gross_total); // Declared value in USD
                    $this->setCellValue('Z'. $row, 'NEWS'); // News
                    $this->setCellValue('AA'. $row, 'POSICION ARANCELARIA'); // Tariff position
                    $this->setCellValue('AB'. $row, $order->isShipped() ? 1 : 0); // Last mile: stop delivery 0=Deliver Pass 1=Do not deliver Pass
                    $this->setCellValue('AC'. $row, 'ULTIMA MILLA NOVEDADES'); // Last mile news
                    $this->setCellValue('AD'. $row, 'ULTIMA MILLA VALOR_RECAUDAR'); // Last mile: value to collect
                    $this->setCellValue('AE'. $row, 'CUENTA PASAREX'); // PASAREX account
                    $this->setCellValue('AF'. $row, $order->consolidation); // Consolidated number
                    $this->setCellValue('AG'. $row, 'GUIA DE REFERENCIA'); // Reference guide  
                    $row++;
                 }
            }
    }

    private function setExcelHeaderRow()
    {


        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'GUIA');// Guide number

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'FECHA YYYY//MM//DD');// Date YYYY//MM//DD
        
        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'COMPAÑÍA REMITENTE');// Sender company

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'CONTACTO REMITENTE');// Sender contact

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'EMAIL REMITENTE');// Sender email
        
        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'DIRECCION REMITENTE');// Sender address

        $this->setColumnWidth('G', 20);
        $this->setCellValue('G1', 'TELEFONO REMITENTE ');// Sender phone
 
        $this->setColumnWidth('H', 20);
        $this->setCellValue('H1', 'CODIGO POSTAL REMITENTE');// Sender postal code 
      
        $this->setColumnWidth('I', 20);
        $this->setCellValue('I1','CIUDAD REMITENTE');// Sender city

        $this->setColumnWidth('J', 20);
        $this->setCellValue('J1', 'ESTADO REMITENTE' );// Sender state 
        $this->setColumnWidth('K', 20);
        $this->setCellValue('K1',  'PAIS REMITENTE');// Sender country

        $this->setColumnWidth('L', 30);
        $this->setCellValue('L1',  'COMPAÑÍA DESTINO');// Destination company
        $this->setColumnWidth('M', 30);
        $this->setCellValue('M1',  'CONTACTO DESTINO');// Destination contact
        $this->setColumnWidth('N', 30);
        $this->setCellValue('N1', 'EMAIL DESTINO');// Destination email
        $this->setColumnWidth('O', 30);
        $this->setCellValue('O1', 'DIRECCION DESTINO');// Destination address 
        $this->setColumnWidth('P', 30);
        $this->setCellValue('P1',   'TELEFONO DESTINO');// Destination phone  
        $this->setColumnWidth('Q', 30);
        $this->setCellValue('Q1',  'CODIGO POSTAL DESTINO' );// Destination postal code
        $this->setColumnWidth('R', 30);
        $this->setCellValue('R1',   'CIUDAD DESTINO');// Destination city 
        $this->setColumnWidth('S', 30); 
        $this->setCellValue('S1', 'ESTADO DESTINO' );// Destination state
        $this->setColumnWidth('T', 30); 
        $this->setCellValue('T1', 'PAIS DESTINO' );// Destination country
        $this->setColumnWidth('U', 30); 
        $this->setCellValue('U1', 'CONTENIDO' );// Content
        $this->setColumnWidth('V', 30);
        $this->setCellValue('V1',  'PIEZAS' );// Pieces
        $this->setColumnWidth('W', 30);
        $this->setCellValue('W1', 'PESO LIBRAS' );// Weight in pounds
        $this->setColumnWidth('X', 30); 
        $this->setCellValue('X1', 'PESO KILOS' );// Weight in kilograms
        $this->setColumnWidth('Y', 30); 
        $this->setCellValue('Y1',  'VALOR DECLARADO USD' );// Declared value in USD
        $this->setColumnWidth('Z', 30);
        $this->setCellValue('Z1', 'NEWS' );// News
        $this->setColumnWidth('AA', 30);
        $this->setCellValue('AA1', 'POSICION ARANCELARIA' );// Tariff position
        $this->setColumnWidth('AB', 30); 
        $this->setCellValue('AB1', 'ULTIMA MILLA DETENER_ENTREGA 0=Entrega Pasar 1=NO entrega Pasar' );// Last mile: stop delivery 0=Deliver Pass 1=Do not deliver Pass
        $this->setColumnWidth('AC', 30); 
        $this->setCellValue('AC1', 'ULTIMA MILLA NOVEDADES' );// Last mile news
        $this->setColumnWidth('AD', 30); 
        $this->setCellValue('AD1', 'ULTIMA MILLA VALOR_RECAUDAR' );// Last mile: value to collect
        $this->setColumnWidth('AE', 30);
        $this->setCellValue('AE1', 'CUENTA PASAREX' );// PASAREX account
        $this->setColumnWidth('AF', 30);
        $this->setCellValue('AF1',  'NUMERO DE CONSOLIDADO' );// Consolidated number
        $this->setColumnWidth('AG', 30); 
        $this->setCellValue('AG1',  'GUIA DE REFERENCIA' );// Reference guide 

        $this->setBackgroundColor('A1:AG1', '2b5cab');
        $this->setColor('A1:AG1', 'FFFFFF');


        $this->currentRow++;
    }
}
