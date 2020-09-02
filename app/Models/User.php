<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    const ROLE_ADMIN = 1;

    const ACCOUNT_TYPE_BUSINESS = 'business';
    const ACCOUNT_TYPE_INDIVIDUAL = 'individual';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function isAdmin()
    {
        return $this->role_id == self::ROLE_ADMIN;
    }

    public function isUser()
    {
        return !$this->isAdmin();
    }

    public function addresses()
    {
        return $this->hasMany(Address::class, 'user_id');
    } 

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    public function settings()
    {
        return $this->hasMany(Setting::class, 'user_id');
    }

    public function scopeAdmin(Builder $query)
    {
        return $query->where('role_id',self::ROLE_ADMIN);
    }

    public static function generatePoBoxNumber()
    {
        $lastUserID = self::latest()->limit(1)->get()->first()->id;

        $lastUserID++;

        if ($lastUserID < 10) {
            $lastUserID = "000{$lastUserID}";
        }
        if ($lastUserID > 10 && $lastUserID < 100) {
            $lastUserID = "00{$lastUserID}";
        }
        if ($lastUserID > 100 && $lastUserID < 1000) {
            $lastUserID = "0{$lastUserID}";
        }

        return "HERCO {$lastUserID}";
    }
}
