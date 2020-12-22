<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\CommissionSetting;
use App\Models\Order;
use App\Models\AffiliateSale;


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
        'pobox_number', 'package_id', 'state_id', 'country_id', 'role_id','name', 'email', 'last_name', 
        'password', 'phone', 'city', 'street_no', 'address', 'address2', 'account_type', 'tax_id', 'zipcode', 
        'api_token', 'api_enabled', 'locale','market_place_name','image_id','reffered_by', 'reffer_code'
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

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }

    public function affiliateSales()
    {
        return $this->hasMany(AffiliateSale::class, 'user_id');
    }

    public function billingInformations()
    {
        return $this->hasMany(BillingInformation::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'reffered_by');
    }
    public function referrer()
    {
        return $this->belongsTo(User::class, 'reffered_by');
    }
    
    public function commissionSetting()
    {
        return $this->hasOne(CommissionSetting::class, 'user_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class, 'user_id');
    }

    public function image()
    {
        return $this->belongsTo(Document::class,'image_id');
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

    public static function findRef($reffer_code)
    {
        $referral_id = self::query()->where('reffer_code', $reffer_code)->first();
        return $referral_id->id;
    }

    public static function generatePoBoxNumber()
    {
        $lastUserID = self::query()->latest('id')->limit(1)->first()->id;

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

    public function getPoboxAddress()
    {
        return optional(PoBox::first())->getCompleteAddress();
    }

    public function hasRole($slug)
    {
        return $this->role->name == $slug;
    }

    public function getImage()
    {
        if ( ! $this->image || ! \Storage::exists($this->image->getStoragePath())){
            return asset('app-assets/images/portrait/small/avatar-s-11.jpg');
        }
        
        return $this->image->getPath();
        
    }

    public function addAffiliateCommissionSale(Order $order, $commissionCalculator )
    {
        $affiliateSetting = $order->affiliateSale;
        
        $data = [
            'value' => $commissionCalculator->getValue(),
            'type' => $commissionCalculator->getCommissionSetting()->type,
            'commission' => $commissionCalculator->getCommission(),
        ];

        if(!$affiliateSetting){
            return AffiliateSale::create(array_merge(
                $data,[
                    'user_id' => $order->user->referrer->id,
                    'order_id' => $order->id,
                ]
            ));
        }
        return $order->affiliateSale->update($data);
    }

    public static function getBarcode($reffer_code)
    {        
        return '<img src="data:image/png;base64,'.\DNS2D::getBarcodePNG(route('register',['ref'=>$reffer_code]), 'QRCODE', 10, 10).'" alt="barcode"   />';
    }

    public static function getRefferCode(){
        $user = User::find(auth()->id());
        return $user->update([
            'reffer_code' => generateRandomString()
        ]);
    }

}
