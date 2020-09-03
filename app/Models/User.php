<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


class User extends Authenticatable
{
    use Notifiable;
    const ROLE_ADMIN = 1;
    const ROLE_USER = 2;

    const ACCOUNT_TYPE_BUSINESS = 'business';
    const ACCOUNT_TYPE_INDIVIDUAL = 'individual';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pobox_number', 'package_id', 'state_id', 'country_id', 'role_id','name', 'email', 'last_name', 'password', 'phone', 'city', 'street_no', 'address', 'address2', 'account_type', 'tax_id', 'zipcode', 'locale' 
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

    public function role()
    {
        return $this->belongsTo(Role::class);
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

    public function isBusinessAccount()
    {
        return $this->account_type == self::ACCOUNT_TYPE_BUSINESS;
    }
    
    public function scopeUser(Builder $query)
    {
        return $query->where('role_id','<>',self::ROLE_ADMIN);
    }

    public function accountType()
    {
        return Str::of($this->account_type)->replace('_', ' ')->title();
    }

    public function profitPackage()
    {
        return $this->belongsTo(ProfitPackage::class,'package_id');
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
    
    public function hasPermission($permissionIdOrSlug)
    {
        return $this->role->permissions()->where(function($query) use($permissionIdOrSlug){
            return $query->where('id',$permissionIdOrSlug)
                ->orWhere('slug',$permissionIdOrSlug);
        })->first();;

    }
}
