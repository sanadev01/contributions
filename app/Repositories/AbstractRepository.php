<?php 

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class AbstractRepository{

    protected $error;

    public function getError()
    {
        return $this->error;
    }

}