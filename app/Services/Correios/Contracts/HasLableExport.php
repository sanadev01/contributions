<?php 

namespace App\Services\Correios\Contracts;

interface HasLableExport{

    public function render();

    public function download();

    public function saveAs($path);

}