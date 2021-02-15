<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function getStatusClass()
    {
        $class = "";
        if ( $this->status == 'pending' ){
            $class = 'btn btn-sm btn-danger';
        }

        if ( $this->status == 'approved' ){
            $class = 'btn btn-sm btn-success';
        }
        return $class;
    }
}
