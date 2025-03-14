<?php

namespace App\Providers;

use App\Events\AutoChargeAmountEvent;
use App\Models\Order;
use App\Events\OrderPaid;
use App\Listeners\AutoChargeAmountListener;
use App\Models\AffiliateSale;
use App\Observers\OrderObserver;
use Illuminate\Support\Facades\Event;
use App\Listeners\CalculateCommission;
use Illuminate\Auth\Events\Registered;
use App\Observers\AffiliateSaleObserver;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        // Order::observe(OrderObserver::class);
    }
}
