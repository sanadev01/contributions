<?php

namespace App\Providers;

use App\Models\Address;
use Spatie\Activitylog\Models\Activity;
use App\Models\AffiliateSale;
use App\Models\BillingInformation;
use App\Models\Connect;
use App\Models\Country;
use App\Models\Document;
use App\Models\HandlingService;
use App\Models\GSSRate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderOrder;
use App\Models\OrderTracking;
use App\Models\PaymentInvoice;
use App\Models\Permission;
use App\Models\Pobox;
use App\Models\ProfitPackage;
use App\Models\Rate;
use App\Models\Recipient;
use App\Models\Reports;
use App\Models\Role;
use App\Models\Setting;
use App\Models\ShCode;
use App\Models\ShippingService;
use App\Models\State;
use App\Models\TempModel;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Product;
use App\Models\Tax;
use App\Policies\ActivityPolicy;
use App\Policies\AddressPolicy;
use App\Policies\AffiliateSalePolicy;
use App\Policies\BillingInformationPolicy;
use App\Policies\ConnectPolicy;
use App\Policies\CountryPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\HandlingServicePolicy;
use App\Policies\GssRatePolicy;
use App\Policies\OrderItemPolicy;
use App\Policies\OrderOrderPolicy;
use App\Policies\OrderPolicy;
use App\Policies\OrderTrackingPolicy;
use App\Policies\PaymentInvoicePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\PoboxPolicy;
use App\Policies\ProfitPackagePolicy;
use App\Policies\RatePolicy;
use App\Policies\RecipientPolicy;
use App\Policies\ReportsPolicy;
use App\Policies\RolePolicy;
use App\Policies\SettingPolicy;
use App\Policies\ShCodePolicy;
use App\Policies\ShippingServicePolicy;
use App\Policies\StatePolicy;
use App\Policies\TempModelPolicy;
use App\Policies\TicketCommentPolicy;
use App\Policies\TicketPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\UserPolicy;
use App\Policies\ProductPolicy;
use App\Policies\TaxPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Order::class => OrderPolicy::class,
        Address::class => AddressPolicy::class,
        Country::class => CountryPolicy::class,
        Document::class => DocumentPolicy::class,
        HandlingService::class => HandlingServicePolicy::class,
        GSSRate::class => GSSRatePolicy::class,
        OrderItem::class => OrderItemPolicy::class,
        OrderOrder::class => OrderOrderPolicy::class,
        PaymentInvoice::class => PaymentInvoicePolicy::class,
        Permission::class => PermissionPolicy::class,
        Pobox::class => PoboxPolicy::class,
        ProfitPackage::class => ProfitPackagePolicy::class,
        Rate::class => RatePolicy::class,
        Recipient::class => RecipientPolicy::class,
        Role::class => RolePolicy::class,
        Setting::class => SettingPolicy::class,
        ShCode::class => ShCodePolicy::class,
        ShippingService::class => ShippingServicePolicy::class,
        State::class => StatePolicy::class,
        TempModel::class => TempModelPolicy::class,
        TicketComment::class => TicketCommentPolicy::class,
        Ticket::class => TicketPolicy::class,
        Transaction::class => TransactionPolicy::class,
        User::class => UserPolicy::class,
        BillingInformation::class => BillingInformationPolicy::class,
        Reports::class => ReportsPolicy::class,
        Connect::class => ConnectPolicy::class,
        AffiliateSale::class => AffiliateSalePolicy::class,
        Activity::class => ActivityPolicy::class,
        Product::class => ProductPolicy::class,
        Tax::class => TaxPolicy::class,
        OrderTracking::class => OrderTrackingPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('do_warehouse_operations',function(User $user){
            return $user->isAdmin() || $user->hasPermission('warehouse_operations');
        });
        Gate::define('view_box_control',function(User $user){
            return   $user->hasPermission('view_box_control') ||$user->isAdmin();
        });
        Gate::define('view_label_post',function(User $user){
            return   $user->hasPermission('view_label_post') || $user->isAdmin();
        });
        Gate::define('view_api_docs',function(User $user){
            return   $user->hasPermission('view_api_docs') ||$user->isAdmin();
        }); 
    }
}
