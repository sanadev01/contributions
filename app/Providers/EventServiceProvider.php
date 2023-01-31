<?php

namespace App\Providers;

use App\Models\AffiliateSale;
use App\Models\Order;
use App\Events\OrderPaid;
use App\Events\OrderStatusUpdated;
use App\Listeners\CalculateCommission;
use App\Listeners\OrderStatusChanged;
use App\Observers\AffiliateSaleObserver;
use App\Observers\OrderObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\AutoChargeAmountEvent;
use App\Listeners\AutoChargeAmountListener;
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderPaid::class =>[
            CalculateCommission::class, 
        ],
        AutoChargeAmountEvent::class => [
            AutoChargeAmountListener::class,
        ],
        OrderStatusUpdated::class =>[
            OrderStatusChanged::class, 
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Order::observe(OrderObserver::class);
    }
}
