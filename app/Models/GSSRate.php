<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ShippingService;
use App\Models\Country;
class GSSRate extends Model
{

   protected $fillable = ['country_id','shipping_service_id','api_discount','user_discount','user_id'];

   public function user() {
      return $this->belongsTo(User::class);
   }
   public function country() {
       return $this->belongsTo(Country::class);
   }
   public function shipping_service() {
      return $this->belongsTo(ShippingService::class);
   }
}
