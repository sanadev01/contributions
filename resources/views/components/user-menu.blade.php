<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow menu-collapsed" data-scroll-to-active="true" style="touch-action: none; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
    {!! $header !!}
    <div class="shadow-bottom"></div>
    <div class="main-menu-content ps ps--active-y">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" style="color: #454f5b; font-family: 'Karla-Regular', Helvetica, Arial, sans-serif;">
            @if (auth()->user()->hasRole('scanner'))
                <li class="nav-item {{ $isActive('home') }}">
                    <a class="nav-link" href="{{ route('admin.home') }}">
                        {{-- <i class="feather icon-home"></i> --}}
                        <img src="{{ asset('images/icon/dashboard.svg') }}" alt="dashboard">
                        <span data-i18n="Dashboard"> @lang('menu.dashboard') </span>
                    </a>
                </li>
                <li class="nav-item {{ $isActive(['warehouse.scan.index']) }}">
                    <a class="nav-link" href="{{ route('warehouse.scan.index') }}">
                        <i class="fab fa-searchengin"></i>
                        <span class="menu-title">Check In Parcel</span>
                    </a>
                </li>
                <li class="nav-item {{ $isActive(['admin.tracking.index']) }}">
                    <a href="{{ route('admin.tracking.index') }}" target="_blank">
                        <i class="feather icon-map-pin"></i>
                        <span class="menu-title">@lang('menu.trackings')</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.profile.index') }}">
                        <i class="feather icon-user-check"></i>
                        <span data-i18n="Apps"> @lang('menu.profile') </span>
                    </a>
                </li>
            @else
            <li class="nav-item {{ $isActive('home') }}">
                <a class="nav-link" href="{{ route('admin.home') }}">
                    <img src="{{ asset('images/icon/dashboard.svg') }}" alt="dashboard">
                    {{-- <i class="feather icon-home"></i> --}}
                    <span data-i18n="Dashboard"> @lang('menu.dashboard') </span>
                </a>
            </li>

            <li class="nav-item {{ $isActive(['admin.parcels.index','admin.parcels.shipments.edit','admin.parcels.shipments.create']) }}">
                <a href="{{ route('admin.parcels.index') }}">
                    <img src="{{ asset('images/icon/parcel.svg') }}" alt="Parcels">
                    {{-- <i class="feather icon-alert-triangle"></i> --}}
                    <span class="menu-title">@lang('menu.Parcels')</span>
                </a>
            </li>

            @can('viewAny', App\Models\Order::class)
                <li class="nav-item {{ $isActive(['admin.orders.index','admin.orders.edit','admin.orders.show']) }}">
                    <a href="{{ route('admin.orders.index') }}">
                        <img src="{{ asset('images/icon/order.svg') }}" alt="Orders">
                        {{-- <i class="feather icon-truck"></i> --}}
                        <span class="menu-title">@lang('menu.orders')</span>
                    </a>
                </li>
            @endcan
            
            @can('viewAny', App\Models\Order::class)
                <li class="nav-item {{ $isActive(['admin.tracking.index']) }}">
                    <a href="{{ route('admin.tracking.index') }}" target="_blank">
                        <img src="{{ asset('images/icon/tracking.svg') }}" alt="Tracking">
                        <span class="menu-title">@lang('menu.trackings')</span>
                    </a>
                </li>
            @endcan
            
            @can('importExcel', App\Models\Order::class)
                <li class="{{ $isActive(['admin.import.import-excel.index','admin.import.import-excel.show','admin.import.import-excel.create']) }}">
                    <a href="{{ route('admin.import.import-excel.index') }}">
                        <img src="{{ asset('images/icon/upload.svg') }}" alt="Upload files">
                        <span class="menu-title">@lang('menu.import-excel-order.excel')</span>
                    </a>
                </li>
            @endcan
            @can('labelPrint', App\Models\Order::class)
                <li class="{{ $isActive(['admin.label.scan.create']) }}">
                    <a href="{{ route('admin.label.scan.create') }}">
                        <img src="{{ asset('images/icon/print.svg') }}" alt="Printer">
                        <span class="menu-title">@lang('menu.Print Label')</span>
                    </a>
                </li>
            @endcan
            @can('viewAny', App\Models\PaymentInvoice::class)
                <li class="nav-item {{ $isActive(['admin.payment-invoices.index','admin.payment-invoices.edit']) }}">
                    <a href="{{ route('admin.payment-invoices.index') }}">
                        <img src="{{ asset('images/icon/payment.svg') }}" alt="payment">
                        <span class="menu-title">@lang('menu.payment-invoice')</span>
                    </a>
                </li>
            @endcan

            @can('viewAny', App\Models\Connect::class)
                <li class="nav-item {{ $isActive(['admin.connect.index']) }}">
                    <a class="nav-link" href="{{ route('admin.connect.index') }}">
                        <i class="fa fa-plug" style="color: #28c76f;"></i>
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
                    <img src="{{ asset('images/icon/dollar.png') }}" alt="Rates" width="19px">
                    <span class="menu-title" data-i18n="Dashboard">@lang('menu.Rates')</span>
                </a>
                <ul class="menu-content">

                    @can('viewAny', App\Models\ProfitPackage::class)
                    <li class="{{ $isActive(['admin.rates.profit-packages.index','admin.rates.profit-packages.create','admin.rates.profit-packages.edit','admin.rates.profit-packages-upload.create']) }}">
                        <a href="{{ route('admin.rates.profit-packages.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Profit Packages')</span>
                        </a>
                    </li>
                    @endcan

                    @can('viewAny', App\Models\HandlingService::class)
                    <li class="{{ $isActive(['admin.services.index','admin.services.edit','admin.services.create']) }}">
                        <a href="{{ route('admin.handling-services.index') }}">
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
                    <li class="{{ $isActive(['admin.rates.shipping-rates.index','admin.rates.shipping-rates.create', 'admin.rates.view-shipping-rates', 'admin.rates.region-rates', 'admin.rates.view-shipping-region-rates']) }}">
                        <a href="{{ route('admin.rates.shipping-rates.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Correios Cost')</span>
                        </a>
                    </li>
                    @endcan
                    
                    @can('viewAny', App\Models\Rate::class)
                    <li class="{{ $isActive(['admin.rates.accrual-rates.index','admin.rates.accrual-rates.create', 'admin.rates.show-accrual-rates']) }}">
                        <a href="{{ route('admin.rates.accrual-rates.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">Accrual Rates</span>
                        </a>
                    </li>

                    <li class="{{ $isActive(['admin.rates.usps-accrual-rates.index']) }}">
                        <a href="{{ route('admin.rates.usps-accrual-rates.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">USPS Accrual Rates</span>
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

            @if(auth()->user()->isUser())
                @can('userSellingRates', App\Models\ProfitPackage::class)
                    <li class="nav-item {{ $isActive(['admin.rates.user-rates.index']) }}">
                        <a class="nav-link" href="{{ route('admin.rates.user-rates.index') }}">
                            <img src="{{ asset('images/icon/dollar.png') }}" alt="Rates" width="19px">
                            <span data-i18n="Apps">@lang('menu.My Rates')</span>
                        </a>
                    </li>
                @endcan
            @endif
            
            @can('viewAny', App\Models\Address::class)
                <li class="nav-item {{ $isActive(['admin.addresses.index','admin.addresses.edit','admin.addresses.create']) }}">
                    <a class="nav-link" href="{{ route('admin.addresses.index') }}">
                        <img src="{{ asset('images/icon/address.svg') }}" alt="Address">
                        <span data-i18n="Apps">@lang('menu.addresses')</span>
                    </a>
                </li>
            @endcan


            <li class="nav-item has-sub sidebar-group">
                <a href="#">
                    <img src="{{ asset('images/icon/calculator.svg') }}" alt="Rates" width="19px">
                    <span class="menu-title" data-i18n="Dashboard">Calculators</span>
                </a>
                <ul class="menu-content">
                    <li class="nav-item {{ $isActive(['calculator.index']) }} ml-2">
                        <a class="nav-link" href="{{ route('calculator.index') }}" target="_blank">
                            <img src="{{ asset('images/icon/calculator.svg') }}" alt="Calculator">
                            <span data-i18n="Apps">@lang('menu.calculator')</span>
                        </a>
                    </li>
                    @if (setting('usps', null, auth()->user()->id))
                        <li class="nav-item {{ $isActive(['calculator.index']) }} ml-2">
                            <a class="nav-link" href="{{ route('usps-calculator.index') }}"
                                target="_blank">
                                <img src="{{ asset('images/icon/calculator.svg') }}" alt="Calculator">
                                <span data-i18n="Apps">@lang('menu.uspscalculator')</span>
                            </a>
                        </li>
                    @endif
                    @if (setting('ups', null, auth()->user()->id))
                        <li class="nav-item {{ $isActive(['calculator.index']) }} ml-2">
                            <a class="nav-link" href="{{ route('ups-calculator.index') }}" target="_blank">
                                <img src="{{ asset('images/icon/calculator.svg') }}" alt="Calculator">
                                <span data-i18n="Apps">@lang('menu.upscalculator')</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </li>

            {{-- Reports --}}
            <li class="nav-item has-sub sidebar-group">
                <a href="#">
                    <img src="{{ asset('images/icon/report.svg') }}" alt="Orders">
                    <span class="menu-title">@lang('menu.Reports.menu')</span>
                </a>
                <ul class="menu-content">

                    @can('viewUserShipmentReport', App\Models\Reports::class)
                    <li class="{{ $isActive(['admin.reports.user-shipments.index']) }}">
                        <a href="{{ route('admin.reports.user-shipments.index') }}">
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
                        @can('viewComissionReport', App\Models\Reports::class)
                        <li class="{{ $isActive(['admin.reports.commission.index','admin.reports.commission.show']) }}">
                            <a href="{{ route('admin.reports.commission.index') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">@lang('menu.Reports.Commission Report')</span>
                            </a>
                        </li>
                        @endcan
                    @admin
                        {{-- <li class="{{ $isActive(['admin.reports.audit-report.index','admin.reports.audit-report.show']) }}">
                            <a href="{{ route('admin.reports.audit-report.index') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">@lang('menu.Reports.Audit Report')</span>
                            </a>
                        </li> --}}
                    @endadmin
                </ul>
            </li>

            {{-- Inventory --}}
            {{-- @can('viewAny', App\Models\Product::class) --}}
                <li class="nav-item has-sub sidebar-group">
                    <a href="#">
                        <i class="feather icon-shopping-cart"></i>
                        <span class="menu-title">Inventory Management</span>
                    </a>
                    <ul class="menu-content">

                        <li class="{{ $isActive(['admin.inventory.product.index','admin.inventory.product.create','admin.inventory.product.edit']) }}">
                            <a href="{{ route('admin.inventory.product.index') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">Products</span>
                            </a>
                        </li>
                        <li class="{{ $isActive(['admin.inventory.status.approved']) }}">
                            <a href="{{ route('admin.inventory.status.approved','approved') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">Approved Products</span>
                            </a>
                        </li>
                        <li class="{{ $isActive(['admin.inventory.status.pending']) }}">
                            <a href="{{ route('admin.inventory.status.pending','pending') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">Pending Products</span>
                            </a>
                        </li>
                        
                    </ul>
                </li>
            {{-- @endcan --}}
            {{-- Affiliate --}}
            @can('viewAny', App\Models\AffiliateSale::class)
                <li class="nav-item has-sub sidebar-group">
                    <a href="#">
                        <img src="{{ asset('images/icon/affiliate.svg') }}" alt="Orders">
                        <span class="menu-title">@lang('menu.Affiliate.menu')</span>
                    </a>
                    <ul class="menu-content">

                        <li class="{{ $isActive(['admin.affiliate.dashboard.index']) }}">
                            <a href="{{ route('admin.affiliate.dashboard.index') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">@lang('menu.Affiliate.Dashboard')</span>
                            </a>
                        </li>

                        <li class="{{ $isActive(['admin.affiliate.sales-commission.index']) }}">
                            <a href="{{ route('admin.affiliate.sales-commission.index') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">@lang('menu.Affiliate.Sale Commission')</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @include('components.warehouse-menu')

            @can('viewAny', App\Models\User::class)
            <li class="nav-item {{ $isActive(['admin.users.index']) }}">
                <a href="{{ route('admin.users.index') }}">
                    <img src="{{ asset('images/icon/users.svg') }}" alt="Users">
                    <span class="menu-title">@lang('menu.Users')</span>
                </a>
            </li>
            @endcan

            @can('viewAny', App\Models\Role::class)
            <li class="nav-item {{ $isActive(['admin.roles.index']) }}">
                <a href="{{ route('admin.roles.index') }}">
                    <img src="{{ asset('images/icon/key.png') }}" alt="Role" width="19px">
                    <span class="menu-title">@lang('menu.Roles')</span>
                </a>
            </li>
            @endcan
            
            @admin
            <li class="nav-item {{ $isActive(['admin.shcode.index','admin.shcode.create','admin.shcode.edit']) }}">
                <a href="{{ route('admin.shcode.index') }}">
                    <i class="fa fa-codepen" style="color: #28c76f;"></i>
                    <span class="menu-title">SH Codes</span>
                </a>
            </li>
            @endadmin

            <li class="nav-item {{ $isActive(['admin.tickets.index','admin.tickets.show']) }}">
                <a class="nav-link" href="{{ route('admin.tickets.index') }}">
                    <i class="feather icon-message-circle" style="color: #28c76f;"></i>
                    <span data-i18n="Apps">@lang('menu.support tickets')</span>
                    <livewire:components.support-ticket/>
                </a>
            </li>

            @can('viewAny', App\Models\BillingInformation::class)
            <li class="nav-item {{ $isActive(['admin.billing-information.index','admin.billing-information.edit','admin.billing-information.create']) }}">
                <a href="{{ route('admin.billing-information.index') }}">
                    <i class="feather icon-alert-triangle" style="color: #28c76f;"></i>
                    <span class="menu-title">@lang('menu.Billing Informations')</span>
                </a>
            </li>
            @endcan

            @can('viewAny', App\Models\BillingInformation::class)
                <li class="nav-item {{ $isActive(['admin.deposit.index','admin.deposit.edit','admin.deposit.create']) }}">
                    <a href="{{ route('admin.deposit.index') }}">
                        <img src="{{ asset('images/icon/balance.svg') }}" alt="Balance">
                        <span class="menu-title">@lang('menu.Balance')</span>
                    </a>
                </li>
            @endcan

            @can('viewAny', Spatie\Activitylog\Models\Activity::class)
            <li class="nav-item {{ $isActive(['admin.activity.log.index']) }}">
                <a href="{{ route('admin.activity.log.index') }}">
                    <i class="feather icon-activity" style="color: #28c76f;"></i>
                    <span class="menu-title">@lang('menu.activity')</span>
                </a>
            </li>
            @endcan

            @can('viewAny', App\Models\Setting::class)
            <li class="nav-item {{ $isActive(['admin.settings.index']) }}">
                <a href="{{ route('admin.settings.index') }}">
                    <img src="{{ asset('images/icon/setting.svg') }}" alt="settings">
                    <span class="menu-title">@lang('menu.Settings')</span>
                </a>
            </li>
            @endcan

            <x-shared-menu></x-shared-menu>
            @endif
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
