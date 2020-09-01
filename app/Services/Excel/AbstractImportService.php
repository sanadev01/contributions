<?php

namespace App\Services\Excel;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

abstract class AbstractImportService
{
    const SERVICE_LIGHT_SERVICE = 'light-service';
    const SERVICE_BPS = 'bps';
    const SERVICE_PACKAGE_PLUS = 'package-plus';
    protected $file;
    protected $fileReader;
    protected $spreadSheet;
    protected $workSheet;
    protected $noRows;
    protected $noColumns;
    private $service;

    /**
     * AbstractImportService constructor.
     * @param $file
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws Exception
     */
    public function __construct($file)
    {
        $this->file = $file;
        $this->fileReader = IOFactory::createReaderForFile($file);
        $this->fileReader->setReadDataOnly(true);
        $this->fileReader->setLoadSheetsOnly(0);
        $this->spreadSheet = $this->fileReader->load($this->file);
        $this->workSheet = $this->spreadSheet->getSheet(0);
        $this->noRows = $this->spreadSheet->getSheet(0)->getHighestDataRow();
        $this->noColumns = $this->spreadSheet->getSheet(0)->getHighestDataColumn();
    }

    public function getDataAsArray()
    {
        return $this->workSheet->rangeToArray(
            'A1:'.$this->noColumns.$this->noRows,
            null,
            true,
            true,
            true
        );
    }

    public function getService()
    {
        return $this->service;
    }

    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    public function getStoragePath($filename)
    {
        return storage_path("app/excels/{$this->service}/{$filename}");
    }

    abstract public function handle();

    public function importFile(UploadedFile $file)
    {
        $fiename = md5(microtime()).'.'.$file->getClientOriginalExtension();
        $file->storeAs("excels/{$this->getService()}", $fiename);
        return $fiename;
    }
}
