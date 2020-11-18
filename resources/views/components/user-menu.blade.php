<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow menu-collapsed" data-scroll-to-active="true" style="touch-action: none; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
    {!! $header !!}
    <div class="shadow-bottom"></div>
    <div class="main-menu-content ps ps--active-y">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item {{ $isActive('home') }}">
                <a class="nav-link" href="{{ route('admin.home') }}">
                    <i class="feather icon-home"></i>
                    <span data-i18n="Dashboard"> @lang('menu.dashboard') </span>
                </a>
            </li>
           
            <li class="nav-item {{ $isActive(['admin.parcels.index','admin.parcels.shipments.edit','admin.parcels.shipments.create']) }}">
                <a href="{{ route('admin.parcels.index') }}">
                    <i class="feather icon-alert-triangle"></i>
                    <span class="menu-title">@lang('menu.Parcels')</span>
                </a>
            </li>

            @can('viewAny', App\Models\Order::class)
                <li class="nav-item {{ $isActive(['admin.orders.index','admin.orders.edit']) }}">
                    <a href="{{ route('admin.orders.index') }}">
                        <i class="feather icon-truck"></i>
                        <span class="menu-title">@lang('menu.orders')</span>
                    </a>
                </li>
            @endcan

            @can('importExcel', App\Models\Order::class)
                <li class="nav-item {{ $isActive(['admin.import-excel.index','admin.import-excel.edit']) }}">
                    <a href="{{ route('admin.import-excel.index') }}">
                        <i class="feather icon-file"></i>
                        <span class="menu-title">@lang('menu.import-excel')</span>
                    </a>
                </li>
            @endcan

            @can('viewAny', App\Models\PaymentInvoice::class)
                <li class="nav-item {{ $isActive(['admin.payment-invoices.index','admin.payment-invoices.edit']) }}">
                    <a href="{{ route('admin.payment-invoices.index') }}">
                        <i class="feather icon-file"></i>
                        <span class="menu-title">@lang('menu.payment-invoice')</span>
                    </a>
                </li>
            @endcan

            @can('viewAny', App\Models\Connect::class)
                <li class="nav-item {{ $isActive(['admin.connect.index']) }}">
                    <a class="nav-link" href="{{ route('admin.connect.index') }}">
                        <i class="fa fa-plug"></i>
                        <span data-i18n="Apps">@lang('menu.connect')</span>
                    </a>
                </li>
            @endcan

            @if ( 
                auth()->user()->can('viewAny', App\Models\ProfitPackage::class) ||
                auth()->user()->can('viewAny', App\Models\HandlingService::class) ||
                auth()->user()->can('viewAny', App\Models\ShippingService::class) ||
                auth()->user()->can('viewAny', App\Models\Rate::class) 
             )
                
            <li class="nav-item has-sub sidebar-group">
                <a href="#">
                    <i class="feather icon-dollar-sign"></i>
                    <span class="menu-title" data-i18n="Dashboard">@lang('menu.Rates')</span>
                </a>
                <ul class="menu-content">

                    @can('viewAny', App\Models\ProfitPackage::class)
                    <li class="{{ $isActive(['admin.rates.profit-packages.index']) }}">
                        <a href="{{ route('admin.rates.profit-packages.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Profit Packages')</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('viewAny', App\Models\HandlingService::class)
                    <li class="{{ $isActive(['admin.services.index','admin.services.edit','admin.services.create']) }}">
                        <a href="{{ route('admin.services.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Handling Services')</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('viewAny', App\Models\ShippingService::class)
                    <li class="nav-item {{ $isActive(['admin.shipping-services.index','admin.shipping-services.create']) }}">
                        <a href="{{ route('admin.shipping-services.index') }}">
                            <i class="feather icon-truck"></i>
                            <span class="menu-title">@lang('menu.Shipping Services')</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('viewAny', App\Models\Rate::class)
                    <li class="{{ $isActive(['admin.rates.shipping-rates.index','admin.rates.shipping-rates.create']) }}">
                        <a href="{{ route('admin.rates.shipping-rates.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Shipping Charges')</span>
                        </a>
                    </li>
                    @endcan

                    @can('viewAny', App\Models\Rate::class)
                    <li class="{{ $isActive(['admin.rates.fixed-charges.index']) }}">
                        <a href="{{ route('admin.rates.fixed-charges.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Fixed Charges')</span>
                        </a>
                    </li>
                    @endcan

                </ul>
            </li>
            @endif


            @can('viewAny', App\Models\Address::class)
                <li class="nav-item {{ $isActive(['admin.addresses.index','admin.addresses.edit','admin.addresses.create']) }}">
                    <a class="nav-link" href="{{ route('admin.addresses.index') }}"><i class="feather icon-home"></i>
                        <span data-i18n="Apps">@lang('menu.addresses')</span>
                    </a>
                </li>
            @endcan


            <li class="nav-item {{ $isActive(['calculator.index']) }}">
                <a class="nav-link" href="{{ route('calculator.index') }}" target="_blank">
                    <i class="fa fa-calculator"></i>
                    <span data-i18n="Apps">@lang('menu.calculator')</span>
                </a>
            </li>

            {{-- Reports --}}
            <li class="nav-item has-sub sidebar-group">
                <a href="#">
                    <i class="feather icon-file"></i>
                    <span class="menu-title">@lang('menu.Reports.menu')</span>
                </a>
                <ul class="menu-content">

                    @can('viewUserShipmentReport', App\Models\Reports::class)
                    <li class="{{ $isActive(['admin.reports.user-shipments']) }}">
                        <a href="{{ route('admin.reports.user-shipments') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Reports.Shipment Report')</span>
                        </a>
                    </li>
                    @endcan

                    @can('downloadTrackingReport', App\Models\Reports::class)
                    <li class="{{ $isActive(['admin.reports.order-trackings.index']) }}">
                        <a href="{{ route('admin.reports.order-trackings.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Reports.Tracking Report')</span>
                        </a>
                    </li>
                    @endcan
                    @can('viewOrderReport', App\Models\Reports::class)
                    <li class="{{ $isActive(['admin.reports.order.index']) }}">
                        <a href="{{ route('admin.reports.order.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Reports.Orders Report')</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </li>

            @can('viewAny', App\Models\User::class)
            <li class="nav-item {{ $isActive(['admin.users.index']) }}">
                <a href="{{ route('admin.users.index') }}">
                    <i class="feather icon-users"></i>
                    <span class="menu-title">@lang('menu.Users')</span>
                </a>
            </li>
            @endcan

            @can('viewAny', App\Models\Role::class)
            <li class="nav-item {{ $isActive(['admin.roles.index']) }}">
                <a href="{{ route('admin.roles.index') }}">
                    <i class="fa fa-key"></i>
                    <span class="menu-title">@lang('menu.Roles')</span>
                </a>
            </li>
            @endcan

            <li class="nav-item {{ $isActive(['admin.tickets.index','admin.tickets.show']) }}">
                <a class="nav-link" href="{{ route('admin.tickets.index') }}"><i class="feather icon-message-circle"></i>
                    <span data-i18n="Apps">@lang('menu.support tickets')</span>
                </a>
            </li>

            @can('viewAny', App\Models\BillingInformation::class)
            <li class="nav-item {{ $isActive(['admin.billing-information.index','admin.billing-information.edit','admin.billing-information.create']) }}">
                <a href="{{ route('admin.billing-information.index') }}">
                    <i class="feather icon-alert-triangle"></i>
                    <span class="menu-title">@lang('menu.Billing Informations')</span>
                </a>
            </li>
            @endcan

            @can('viewAny', App\Models\Setting::class)
            <li class="nav-item {{ $isActive(['admin.settings.index']) }}">
                <a href="{{ route('admin.settings.index') }}">
                    <i class="feather icon-settings"></i>
                    <span class="menu-title">@lang('menu.Settings')</span>
                </a>
            </li>
            @endcan
            

            <x-shared-menu></x-shared-menu>
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
