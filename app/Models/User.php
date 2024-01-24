<?php

namespace App\Models;

use App\Models\Order;
use Milon\Barcode\DNS2D;
use Illuminate\Support\Str;
use App\Models\AffiliateSale;
use App\Models\CommissionSetting;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Cache;
use App\Models\Warehouse\DeliveryBill;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Traits\CausesActivity;
use Illuminate\Foundation\Auth\User as Authenticatable;

use AmazonSellingPartner\Exception\ApiException;
use AmazonSellingPartner\Exception\InvalidArgumentException;
use App\AmazonSPClients\SellersApiClient; 
use Exception;
use JsonException; 
use Psr\Http\Client\ClientExceptionInterface;

class User extends Authenticatable
{
    use Notifiable, LogsActivity ,CausesActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                            ->logOnly([
                                'pobox_number', 'package_id', 'state_id', 'country_id', 'role_id','name', 'email', 'last_name', 
                                'phone', 'city', 'street_no', 'address', 'address2', 'account_type', 'tax_id', 'zipcode', 
                                'locale','market_place_name','image_id','reffered_by', 'reffer_code', 'battery', 'perfume',
                                'status', 'insurance', 'stripe', 'usps', 'ups', 'api_profit','amazon_api_enabled','amazon_api_key'
                            ])
                            ->logOnlyDirty()
                            ->dontSubmitEmptyLogs();
    }

    const ROLE_ADMIN = 1;
    const ROLE_USER = 2;
    const ROLE_DRIVER = 'driver';
    const ROLE_SCANNER = 'scanner';

    const ACCOUNT_TYPE_BUSINESS = 'business';
    const ACCOUNT_TYPE_INDIVIDUAL = 'individual';
    
    const USER_TYPE_ADMIN = 'ADMIN';
    const USER_TYPE_SELLER = 'SELLER';

    const GILBERTO_ACCOUNT_ID = 13;
    
    protected static $ignoreChangedAttributes = ['password','api_token','api_enabled'];
    protected static $logAttributes = [
        'pobox_number', 'package_id', 'state_id', 'country_id', 'role_id','name', 'email', 'last_name', 
        'phone', 'city', 'street_no', 'address', 'address2', 'account_type', 'tax_id', 'zipcode', 
        'locale','market_place_name','image_id','reffered_by', 'reffer_code', 'battery', 'perfume',
        'status', 'insurance', 'stripe', 'usps', 'ups', 'api_profit','amazon_api_enabled','amazon_api_key'
    ];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
 
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pobox_number', 'package_id', 'state_id', 'country_id', 'role_id','name', 'email', 'last_name', 
        'password', 'phone', 'city', 'street_no', 'address', 'address2', 'account_type', 'tax_id', 'zipcode', 
        'api_token', 'api_enabled', 'locale','market_place_name','image_id','reffered_by', 'reffer_code','come_from', 'battery', 'perfume','status', 'insurance',
        'api_token', 'api_enabled', 'locale','market_place_name','image_id','reffered_by', 'reffer_code','come_from', 'battery', 'perfume','status', 
        'usps', 'api_profit', 'order_dimension', 'sinerlog', 'stripe', 'ups','amazon_api_enabled','amazon_api_key', 
        'email_verified_at', 
        'is_active',
        'user_type',
        'parent_id',
        'seller_id',
        'marketplace_id',
        'region_code',
        'delete_status',
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

    public function isDriver()
    {
        return $this->role->name == self::ROLE_DRIVER;
    }

    public function isScanner()
    {
        return $this->role->name == self::ROLE_SCANNER;
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
    
    public function products()
    {
        return $this->hasMany(Product::class, 'user_id');
    }
    public function profitSettings()
    {
        return $this->hasMany(ProfitSetting::class, 'user_id');
    }

    public function importOrders()
    {
        return $this->hasMany(ImportOrder::class, 'user_id');
    }
    
    public function importedOrders()
    {
        return $this->hasMany(ImportedOrder::class, 'user_id');
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

    public function trackings()
    {
        return $this->hasMany(Tracking::class, 'created_by');
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
        if($reffer_code){
            $referral = self::query()->where('reffer_code', $reffer_code)->first();
            if ($referral) {
                return $referral;
            }
            return null;
        }

        return null;
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

    public static function getBarcode($reffer_code)
    {        
        return '<img src="data:image/png;base64,'.\DNS2D::getBarcodePNG(route('register',['ref'=>$reffer_code]), 'QRCODE', 10, 10).'" alt="barcode"   />';
    }

    public function getRefferCode(){

        if ( !$this->reffer_code ){
            $this->update([
                'reffer_code' => generateRandomString()
            ]);
        }

        return $this->reffer_code;
    }

    public function getFullName()
    {
        return $this->name . ' '. $this->last_name;
    }
    
    public function getFullNameAttribute()
    {
        return $this->name . ' '. $this->last_name;
    }

    public function getPoboxNameAttribute()
    {
        return  $this->name.' '.$this->pobox_number;
    }
    
    public function isActive(){
        return ($this->status == "active" || $this->status == NULL) ? true :false;
    }

    public function hideBoxControl()
    {
        if (collect($this->accountIds())->contains($this->id)) {
            return true;
        }

        return false;
    }

    private function accountIds()
    {
        return[
            self::GILBERTO_ACCOUNT_ID,
        ];
    }
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }
    public function containers()
    {
        return $this->hasMany(Container::class);
    }
    public function deliveryBills()
    {
        return $this->hasMany(DeliveryBill::class);
    }


    /**
     * @return BelongsTo
     */
    public function parent(){
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany
     */
    public function children(){
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @return HasMany
     */
    public function siblings() {
        return $this->hasMany(self::class, ['parent_id', 'user_type', 'seller_id'], ['parent_id', 'user_type', 'seller_id']);
    }
 
    public function marketplace(){
        return $this->belongsTo(Marketplace::class);
    }

    /**
     * @return HasOne
     */
    public function sp_token(){
        return $this->hasOne(SpToken::class);
    }

    /**
     * @return string
     */
    public function getRegion(): string {
        return $this->region_code ?: ($this->marketplace ? $this->marketplace->region_code : 'na');
    }
 

    
}

