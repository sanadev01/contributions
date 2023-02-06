<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow menu-collapsed" data-scroll-to-active="true" style="touch-action: none; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
    {!! $header !!}
    <div class="shadow-bottom"></div>
    <div class="main-menu-content ps ps--active-y">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" style="color: #454f5b; font-family: 'Karla-Regular', Helvetica, Arial, sans-serif;">
            @if (auth()->user()->hasRole('scanner') || auth()->user()->hasRole('driver'))
                <li class="nav-item {{ $isActive('home') }}">
                    <a class="nav-link" href="{{ route('admin.home') }}">
                        {{-- <i class="icon_adjst feather icon-home"></i> --}}
                        <img src="{{ asset('images/icon/dashboard.svg') }}" alt="dashboard">
                        <span data-i18n="Dashboard"> @lang('menu.dashboard') </span>
                    </a>
                </li>
                @if (auth()->user()->hasRole('driver'))
                    <li class="nav-item {{ $isActive(['warehouse.scan-label.index']) }}">
                        <a class="nav-link" href="{{ route('warehouse.scan-label.index') }}">
                            <i class="icon_adjst fab fa-searchengin"></i>
                            <span class="menu-title">@lang('menu.Scan Parcel')</span>
                        </a>
                    </li>
                @else
                <li class="nav-item {{ $isActive(['warehouse.scan.index']) }}">
                    <a class="nav-link" href="{{ route('warehouse.scan.index') }}">
                        <i class="icon_adjst fab fa-searchengin"></i>
                        <span class="menu-title">Check In Parcel</span>
                    </a>
                </li>
                @endif
                <li class="nav-item {{ $isActive(['admin.tracking.index']) }}">
                    <a href="{{ route('admin.tracking.index') }}" target="_blank">
                        <i class="icon_adjst feather icon-map-pin"></i>
                        <span class="menu-title">@lang('menu.trackings')</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.profile.index') }}">
                        <i class="icon_adjst feather icon-user-check"></i>
                        <span data-i18n="Apps"> @lang('menu.profile') </span>
                    </a>
                </li>
            @else
            <li class="nav-item {{ $isActive('home') }}">
                <a class="nav-link" href="{{ route('admin.home') }}">
                    <img src="{{ asset('images/icon/dashboard.svg') }}" alt="dashboard">
                    {{-- <i class="icon_adjst feather icon-home"></i> --}}
                    <span data-i18n="Dashboard"> @lang('menu.dashboard') </span>
                </a>
            </li>

            <li class="nav-item {{ $isActive(['admin.parcels.index','admin.parcels.shipments.edit','admin.parcels.shipments.create']) }}">
                <a href="{{ route('admin.parcels.index') }}">
                    <img src="{{ asset('images/icon/parcel.svg') }}" alt="Parcels">
                    {{-- <i class="icon_adjst feather icon-alert-triangle"></i> --}}
                    <span class="menu-title">@lang('menu.Parcels')</span>
                </a>
            </li>

            @can('viewAny', App\Models\Order::class)
                <li class="nav-item {{ $isActive(['admin.orders.index','admin.orders.edit','admin.orders.show']) }}">
                    <a href="{{ route('admin.orders.index') }}">
                        <img src="{{ asset('images/icon/order.svg') }}" alt="Orders">
                        {{-- <i class="icon_adjst feather icon-truck"></i> --}}
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

            @if (auth()->user()->isAdmin())
            <li class="nav-item has-sub sidebar-group">
                <a href="#">
                    <i class="icon_adjst fab fa-searchengin" style="color: #3CB64B;"></i>
                    <span class="menu-title" data-i18n="Dashboard">Scan</span>
                </a>
                <ul class="menu-content">
                    <li class="nav-item {{ $isActive(['warehouse.scan-label.index']) }} ml-2">
                        <a class="nav-link" href="{{ route('warehouse.scan-label.index') }}">
                            <i class="icon_adjst feather icon-circle"></i>
                            <span data-i18n="Apps">@lang('menu.Scan Parcel')</span>
                        </a>
                    </li>
                    <li class="nav-item {{ $isActive(['warehouse.scan-label.create']) }} ml-2">
                        <a class="nav-link" href="{{ route('warehouse.scan-label.create') }}">
                            <i class="icon_adjst feather icon-circle"></i>
                            <span data-i18n="Apps">@lang('menu.Driver Report')</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

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
                        <i class="icon_adjst fa fa-plug" style="color: #3db64c;"></i>
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
                            <i class="icon_adjst feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Profit Packages')</span>
                        </a>
                    </li>
                    @endcan

                    @can('viewAny', App\Models\HandlingService::class)
                    <li class="{{ $isActive(['admin.services.index','admin.services.edit','admin.services.create']) }}">
                        <a href="{{ route('admin.handling-services.index') }}">
                            <i class="icon_adjst feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Handling Services')</span>
                        </a>
                    </li>
                    @endcan

                    @can('viewAny', App\Models\ShippingService::class)
                    <li class="nav-item {{ $isActive(['admin.shipping-services.index','admin.shipping-services.create']) }}">
                        <a href="{{ route('admin.shipping-services.index') }}">
                            <i class="icon_adjst feather icon-truck"></i>
                            <span class="menu-title">@lang('menu.Shipping Services')</span>
                        </a>
                    </li>
                    @endcan

                    @can('viewAny', App\Models\Rate::class)
                    <li class="{{ $isActive(['admin.rates.shipping-rates.index','admin.rates.shipping-rates.create', 'admin.rates.view-shipping-rates', 'admin.rates.region-rates', 'admin.rates.view-shipping-region-rates']) }}">
                        <a href="{{ route('admin.rates.shipping-rates.index') }}">
                            <i class="icon_adjst feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Homedeliverybr Cost')</span>
                        </a>
                    </li>
                    @endcan

                    @can('viewAny', App\Models\Rate::class)
                    <li class="{{ $isActive(['admin.rates.accrual-rates.index','admin.rates.accrual-rates.create', 'admin.rates.show-accrual-rates']) }}">
                        <a href="{{ route('admin.rates.accrual-rates.index') }}">
                            <i class="icon_adjst feather icon-circle"></i>
                            <span class="menu-title">Accrual Rates</span>
                        </a>
                    </li>

                    <li class="{{ $isActive(['admin.rates.usps-accrual-rates.index']) }}">
                        <a href="{{ route('admin.rates.usps-accrual-rates.index') }}">
                            <i class="icon_adjst feather icon-circle"></i>
                            <span class="menu-title">USPS Accrual Rates</span>
                        </a>
                    </li>
                    @endcan

                    @can('viewAny', App\Models\Rate::class)
                    <li class="{{ $isActive(['admin.rates.fixed-charges.index']) }}">
                        <a href="{{ route('admin.rates.fixed-charges.index') }}">
                            <i class="icon_adjst feather icon-circle"></i>
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
                    <li class="nav-item {{ $isActive(['us-calculator.index']) }} ml-2">
                        <a class="nav-link" href="{{ route('us-calculator.index') }}" target="_blank">
                            <img src="{{ asset('images/icon/calculator.svg') }}" alt="Calculator">
                            <span data-i18n="Apps">@lang('menu.uscalculator')</span>
                        </a>
                    </li>
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
                            <i class="icon_adjst feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Reports.Shipment Report')</span>
                        </a>
                    </li>
                    @endcan

                    @can('downloadTrackingReport', App\Models\Reports::class)
                    <li class="{{ $isActive(['admin.reports.order-trackings.index']) }}">
                        <a href="{{ route('admin.reports.order-trackings.index') }}">
                            <i class="icon_adjst feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Reports.Tracking Report')</span>
                        </a>
                    </li>
                    @endcan
                    @can('viewOrderReport', App\Models\Reports::class)
                    <li class="{{ $isActive(['admin.reports.order.index']) }}">
                        <a href="{{ route('admin.reports.order.index') }}">
                            <i class="icon_adjst feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Reports.Orders Report')</span>
                        </a>
                    </li>
                    @endcan
                    @can('viewComissionReport', App\Models\Reports::class)
                    <li class="{{ $isActive(['admin.reports.commission.index','admin.reports.commission.show']) }}">
                        <a href="{{ route('admin.reports.commission.index') }}">
                            <i class="icon_adjst feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Reports.Commission Report')</span>
                        </a>
                    </li>
                    @endcan
                    @can('viewAnjunReport', App\Models\Reports::class)
                    <li class="{{ $isActive(['admin.reports.anjun.index']) }}">
                        <a href="{{ route('admin.reports.anjun.index') }}">
                            <i class="icon_adjst feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Reports.Anjun Report')</span>
                        </a>
                    </li>
                    @endcan
                    @can('viewKPIReport', App\Models\Reports::class)
                    <li class="{{ $isActive(['admin.reports.kpi-report.index']) }}">
                        <a href="{{ route('admin.reports.kpi-report.index') }}">
                            <i class="icon_adjst feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Reports.KPI Report')</span>
                        </a>
                    </li>
                    @endcan
                    @admin
                        {{-- <li class="{{ $isActive(['admin.reports.audit-report.index','admin.reports.audit-report.show']) }}">
                            <a href="{{ route('admin.reports.audit-report.index') }}">
                                <i class="icon_adjst feather icon-circle"></i>
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
                        <i class="icon_adjst feather icon-shopping-cart" style="color: #3db64c;"></i>
                        <span class="menu-title">@lang('inventory.Inventory Management')</span>
                    </a>
                    <ul class="menu-content">

                        <li class="{{ $isActive(['admin.inventory.product.index','admin.inventory.product.create','admin.inventory.product.edit']) }}">
                            <a href="{{ route('admin.inventory.product.index') }}">
                                <i class="icon_adjst feather icon-circle"></i>
                                <span class="menu-title">@lang('inventory.Products')</span>
                            </a>
                        </li>
                        <li class="{{ $isActive(['admin.inventory.orders','admin.inventory.product.sale.order']) }}">
                            <a href="{{ route('admin.inventory.orders') }}">
                                <i class="icon_adjst feather icon-circle"></i>
                                <span class="menu-title">@lang('inventory.Sales Orders')</span>
                            </a>
                        </li>
                        <li class="{{ $isActive(['admin.inventory.product.pickup']) }}">
                            <a href="{{ route('admin.inventory.product.pickup') }}">
                                <i class="icon_adjst feather icon-circle"></i>
                                <span class="menu-title">@lang('inventory.Pickup')</span>
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
                                <i class="icon_adjst feather icon-circle"></i>
                                <span class="menu-title">@lang('menu.Affiliate.Dashboard')</span>
                            </a>
                        </li>

                        <li class="{{ $isActive(['admin.affiliate.sales-commission.index']) }}">
                            <a href="{{ route('admin.affiliate.sales-commission.index') }}">
                                <i class="icon_adjst feather icon-circle"></i>
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
                    <i class="icon_adjst fa fa-codepen" style="color: #3db64c;"></i>
                    <span class="menu-title">SH Codes</span>
                </a>
            </li>
            @endadmin

            <li class="nav-item {{ $isActive(['admin.tickets.index','admin.tickets.show']) }}">
                <a class="nav-link" href="{{ route('admin.tickets.index') }}">
                    <i class="icon_adjst feather icon-message-circle" style="color: #3db64c;"></i>
                    <span data-i18n="Apps">@lang('menu.support tickets')</span>
                    <livewire:components.support-ticket/>
                </a>
            </li>

            @can('viewAny', App\Models\BillingInformation::class)
            <li class="nav-item {{ $isActive(['admin.billing-information.index','admin.billing-information.edit','admin.billing-information.create']) }}">
                <a href="{{ route('admin.billing-information.index') }}">
                    <i class="icon_adjst feather icon-alert-triangle" style="color: #3db64c;"></i>
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
                @admin
                    <li class="nav-item {{ $isActive(['admin.liability.index','admin.liability.edit','admin.liability.create']) }}">
                        <a href="{{ route('admin.liability.index') }}">
                            <img src="{{ asset('images/icon/liability.svg') }}" alt="HD Liability">
                            <span class="menu-title">@lang('HD Liability')</span>
                        </a>
                    </li>
                @endadmin
            @endcan
            @admin
            <li class="nav-item {{ $isActive(['admin.tax.index']) }}">
                <a href="{{ route('admin.tax.index') }}">
                    <i class="icon_adjst feather icon-activity" style="color: #3db64c;"></i>
                    <span class="menu-title">Tax Payment</span>
                </a>
            </li>
            @endadmin

            @can('viewAny', Spatie\Activitylog\Models\Activity::class)
            <li class="nav-item {{ $isActive(['admin.activity.log.index']) }}">
                <a href="{{ route('admin.activity.log.index') }}">
                    <i class="icon_adjst feather icon-activity" style="color: #3db64c;"></i>
                    <span class="menu-title">@lang('menu.activity')</span>
                </a>
            </li>
            @endcan
            @admin
                @can('viewAny', App\Models\Setting::class)
                    <li class="nav-item {{ $isActive(['admin.settings.index']) }}">
                        <a href="{{ route('admin.settings.index') }}">
                            <img src="{{ asset('images/icon/setting.svg') }}" alt="settings">
                            <span class="menu-title">@lang('menu.Settings')</span>
                        </a>
                    </li>
                @endcan
            @endadmin

            <li class="nav-item {{ $isActive(['admin.trash-orders.index']) }}">
                <a href="{{ route('admin.trash-orders.index') }}">
                    <i class="icon_adjst feather icon-trash" style="color: #ff5a5a;"></i>
                    <span class="menu-title">@lang('menu.Trashed Orders')</span>
                </a>
            </li>

            {{-- @can('viewAny', Spatie\Activitylog\Models\Activity::class) --}}
            {{-- @endcan --}}
            <x-shared-menu></x-shared-menu>
            @endif
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
