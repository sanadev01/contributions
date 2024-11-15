<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserLoginDetail extends Model
{
    protected $fillable = ['user_id', 'ip_address','device','location','successful'];
    function user() {
        return $this->belongsTo(User::class);   
    }
}
