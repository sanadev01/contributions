<?php

namespace App\Providers;

use App\Models\Order;
use App\Policies\OrderPolicy;
use App\Models\Address;
use App\Models\BillingInformation;
use App\Policies\AddressPolicy;
use App\Models\Country;
use App\Policies\CountryPolicy;
use App\Models\Document;
use App\Policies\DocumentPolicy;
use App\Models\HandlingService;
use App\Policies\HandlingServicePolicy;
use App\Models\OrderItem;
use App\Policies\OrderItemPolicy;
use App\Models\OrderOrder;
use App\Policies\OrderOrderPolicy;
use App\Models\PaymentInvoice;
use App\Policies\PaymentInvoicePolicy;
use App\Models\Permission;
use App\Policies\PermissionPolicy;
use App\Models\Pobox;
use App\Policies\PoboxPolicy;
use App\Models\ProfitPackage;
use App\Policies\ProfitPackagePolicy;
use App\Models\Rate;
use App\Policies\RatePolicy;
use App\Models\Recipient;
use App\Models\Reports;
use App\Policies\RecipientPolicy;
use App\Models\Role;
use App\Policies\RolePolicy;
use App\Models\Setting;
use App\Policies\SettingPolicy;
use App\Models\ShCode;
use App\Policies\ShCodePolicy;
use App\Models\ShippingService;
use App\Policies\ShippingServicePolicy;
use App\Models\State;
use App\Policies\StatePolicy;
use App\Models\TempModel;
use App\Policies\TempModelPolicy;
use App\Models\TicketComment;
use App\Policies\TicketCommentPolicy;
use App\Models\Ticket;
use App\Policies\TicketPolicy;
use App\Models\Transaction;
use App\Policies\TransactionPolicy;
use App\Models\User;
use App\Policies\BillingInformationPolicy;
use App\Policies\ReportsPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
        Reports::class => ReportsPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
