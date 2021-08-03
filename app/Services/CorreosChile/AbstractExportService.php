<?php

namespace App\Services\CorreosChile;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

abstract class AbstractExportService
{
    protected $spreadSheet;

    protected $sheet;

    protected $path;

    protected $container;

    private $clienteRemitente;

    public function __construct($container)
    {
        
        $this->spreadSheet = new Spreadsheet();
        $this->sheet = $this->spreadSheet->getActiveSheet();

        $this->path = 'manifests/excel';

        $this->container = $container;
        $this->clienteRemitente = config('correoschile.codeId');

        if (! file_exists(storage_path('app/'.$this->path))) {
            mkdir(storage_path('app/'.$this->path), 0777, true);
        }
    }

    abstract public function handle();

    public function download()
    {
        $excelWriter = new Xlsx($this->spreadSheet);

        $current_date = (Carbon::now())->toDateTimeString();
        $combine_date_time = str_replace(['-','', ' ',':'],'',$current_date); 
        $filename = $this->clienteRemitente.'_'.$combine_date_time.'.xlsx';

        $excelWriter->save(
            storage_path("app/{$this->path}/{$filename}")
        );

        return Storage::download("{$this->path}/{$filename}", $filename);
    }

    protected function setCellValue($cellRange, $value)
    {
        $this->sheet->setCellValue($cellRange, $value);

        return $this;
    }

    protected function getCellValue($cellRange, $value)
    {
        return $this->sheet->getCell($cellRange)->getValue();
    }

    protected function setColumnWidth($cell, $width)
    {
        $this->sheet->getColumnDimension($cell)->setWidth($width);

        return $this;
    }

    protected function setRowHeight($row, $value)
    {
        $this->sheet->getRowDimension($row)->setRowHeight($value);

        return $this;
    }

    public function setTextWrap($cellRange, $wrap = true)
    {
        $this->sheet->getStyle($cellRange)->getAlignment()->setWrapText($wrap);

        return $this;
    }

    public function setBold($cellRange, $value)
    {
        $this->sheet->getStyle($cellRange)->getFont()->setBold($value);

        return $this;
    }

    public function setAlignment($cellRange, $alignment)
    {
        $this->sheet->getStyle($cellRange)->getAlignment()->setHorizontal($alignment);

        return $this;
    }

    protected function setBackgroundColor($cellRange, $color)
    {
        $this->sheet->getStyle($cellRange)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB($color);

        return $this;
    }

    public function setFontSize($cellRange, $size)
    {
        $this->sheet->getStyle($cellRange)->getFont()->setSize($size);

        return $this;
    }

    protected function setColor($cellRange, $color)
    {
        $this->sheet->getStyle($cellRange)
            ->getFont()
            ->getColor()
            ->setRGB($color);

        return $this;
    }

    protected function mergeCells($range)
    {
        $this->sheet->mergeCells($range);

        return $this;
    }
}
