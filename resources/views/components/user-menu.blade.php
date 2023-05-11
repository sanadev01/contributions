<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow menu-collapsed" data-scroll-to-active="true"
    style="touch-action: none; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
    {!! $header !!}
    <div class="shadow-bottom"></div>
    <div class="main-menu-content ps ps--active-y">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation"
            style="color: #454f5b; font-family: 'Karla-Regular', Helvetica, Arial, sans-serif;">
            <li class="sub-category"> <span class="text-white">MAIN</span> </li>
            @if (auth()->user()->hasRole('scanner') ||
                auth()->user()->hasRole('driver'))
                <li class="nav-item {{ $isActive('home') }}">
                    <a class="nav-link" href="{{ route('admin.home') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-home">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <span data-i18n="Dashboard"> @lang('menu.dashboard') </span>
                    </a>
                </li>
                @if (auth()->user()->hasRole('driver'))
                    <li class="nav-item {{ $isActive(['warehouse.scan-label.index']) }}">
                        <a class="nav-link" href="{{ route('warehouse.scan-label.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-credit-card">
                                <rect x="1" y="4" width="22" height="16" rx="2"
                                    ry="2"></rect>
                                <line x1="1" y1="10" x2="23" y2="10"></line>
                            </svg>
                            <span class="menu-title">@lang('menu.Scan Parcel')</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item {{ $isActive(['warehouse.scan.index']) }}">
                        <a class="nav-link" href="{{ route('warehouse.scan.index') }}">
                            <i class="fab fa-searchengin"></i>
                            <span class="menu-title">Check In Parcel</span>
                        </a>
                    </li>
                @endif
                @can('view',App\Models\OrderTracking::class)
                <li class="nav-item {{ $isActive(['admin.tracking.index']) }}">
                    <a href="{{ route('admin.tracking.index') }}" target="_blank">
                        <i class=" feather icon-map-pin"></i>
                        <span class="menu-title">@lang('menu.trackings')</span>
                    </a>
                </li>
                @endcan
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.profile.index') }}">
                        <i class=" feather icon-user-check"></i>
                        <span data-i18n="Apps"> @lang('menu.profile') </span>
                    </a>
                </li>
            @else
                <li class="nav-item {{ $isActive('admin.home') }}">
                    <a class="nav-link" href="{{ route('admin.home') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-home">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        <span data-i18n="Dashboard"> @lang('menu.dashboard') </span>
                    </a>
                </li>
                <li class="sub-category"> <span class="text-white">ORDER MANAGEMENT</span> </li>
                <li
                    class="nav-item {{ $isActive(['admin.parcels.index', 'admin.parcels.shipments.edit', 'admin.parcels.shipments.create']) }}">
                    <a href="{{ route('admin.parcels.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-package">
                            <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line>
                            <path
                                d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                            </path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                        <span class="menu-title">@lang('menu.Parcels')</span>
                    </a>
                </li>
                @can('viewAny', App\Models\Order::class)
                    <li class="nav-item nav-item has-sub sidebar-group">
                        <a href="{{ route('admin.orders.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-shopping-cart">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span class="menu-title">@lang('menu.orders')</span>
                        </a>
                        <ul class="menu-content">

                            <li class="{{ $isActive(['admin.orders.index']) }}">
                                <a href="{{ route('admin.orders.index') }}"><i class="feather icon-circle"></i><span
                                        class="menu-title">All Orders</span></a>

                            </li>
                            @admin
                                <li class=" @if (collect(request()->segments())->last() == 'wholesale') active @endif ">
                                    <a href="{{ route('admin.orders.show', 'wholesale') }}"><i
                                            class="feather icon-circle"></i><span class="menu-title">Wholesales</span></a>

                                </li>
                                <li class="@if (collect(request()->segments())->last() == 'retailer') active @endif ">
                                    <a href="{{ route('admin.orders.show', 'retailer') }}"><i
                                            class="feather icon-circle"></i><span class="menu-title">Retail</span></a>

                                </li>
                                <li class="@if (collect(request()->segments())->last() == 'domestic') active @endif ">
                                    <a href="{{ route('admin.orders.show', 'domestic') }}"><i
                                            class="feather icon-circle"></i><span class="menu-title">Domestic</span></a>
                                </li>
                                <li class="@if (collect(request()->segments())->last() == 'pickups') active @endif ">
                                    <a href="{{ route('admin.orders.show', 'pickups') }}"><i
                                            class="feather icon-circle"></i><span class="menu-title">Pickups</span></a>
                                </li>
                            @endadmin
                            @can('view_trashed_order',App\Models\Order::class)
                                <li class="nav-item {{ $isActive(['admin.trash-orders.index']) }}">
                                    <a href="{{ route('admin.trash-orders.index') }}">
                                        <i class="feather icon-circle"></i>
                                        <span class="menu-title">@lang('menu.Trashed Orders')</span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @include('components.warehouse-menu')

                @can('viewAny', App\Models\AffiliateSale::class)
                    <li class="nav-item has-sub sidebar-group">
                        <a href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-user-plus">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <line x1="20" y1="8" x2="20" y2="14"></line>
                                <line x1="23" y1="11" x2="17" y2="11"></line>
                            </svg>
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

                <li class="nav-item has-sub sidebar-group">
                    <a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-shopping-cart">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <span class="menu-title">@lang('inventory.Inventory Management')</span>
                    </a>
                    <ul class="menu-content">

                        <li
                            class="{{ $isActive(['admin.inventory.product.index', 'admin.inventory.product.create', 'admin.inventory.product.edit', 'admin.inventory.product-import.create']) }}">
                            <a href="{{ route('admin.inventory.product.index') }}">
                                <i class=" feather icon-circle"></i>
                                <span class="menu-title">@lang('inventory.Products')</span>
                            </a>
                        </li>
                        <li class="{{ $isActive(['admin.inventory.orders', 'admin.inventory.product.sale.order']) }}">
                            <a href="{{ route('admin.inventory.orders') }}">
                                <i class=" feather icon-circle"></i>
                                <span class="menu-title">@lang('inventory.Sales Orders')</span>
                            </a>
                        </li>
                        <li class="{{ $isActive(['admin.inventory.product.pickup']) }}">
                            <a href="{{ route('admin.inventory.product.pickup') }}">
                                <i class=" feather icon-circle"></i>
                                <span class="menu-title">@lang('inventory.Pickup')</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="sub-category"> <span class="text-white">FINANCIALS</span> </li>

                @if (auth()->user()->can('viewAny', App\Models\ProfitPackage::class) ||
                    auth()->user()->can('viewAny', App\Models\HandlingService::class) ||
                    auth()->user()->can('viewAny', App\Models\ShippingService::class) ||
                    auth()->user()->can('viewAny', App\Models\Rate::class))
                    <li class="nav-item has-sub sidebar-group">
                        <a href="#">
                            <svg viewBox="0 0 24 24" height="15" stroke="currentColor" stroke-width="2"
                                fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                                <polyline points="17 6 23 6 23 12"></polyline>
                            </svg>
                            <span class="menu-title" data-i18n="Dashboard">@lang('menu.Rates')</span>
                        </a>
                        <ul class="menu-content">

                            @can('viewAny', App\Models\ProfitPackage::class)
                                <li
                                    class="{{ $isActive(['admin.rates.profit-packages.index', 'admin.rates.profit-packages.create', 'admin.rates.profit-packages.edit', 'admin.rates.profit-packages-upload.create']) }}">
                                    <a href="{{ route('admin.rates.profit-packages.index') }}">
                                        <i class="feather icon-circle"></i>
                                        <span class="menu-title">@lang('menu.Profit Packages')</span>
                                    </a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\HandlingService::class)
                                <li
                                    class="{{ $isActive(['admin.services.index', 'admin.services.edit', 'admin.services.create', 'admin.handling-services.index']) }}">
                                    <a href="{{ route('admin.handling-services.index') }}">
                                        <i class="feather icon-circle"></i>
                                        <span class="menu-title">@lang('menu.Handling Services')</span>
                                    </a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\ShippingService::class)
                                <li
                                    class="nav-item {{ $isActive(['admin.shipping-services.index', 'admin.shipping-services.create']) }}">
                                    <a href="{{ route('admin.shipping-services.index') }}">
                                        <i class="feather icon-circle"></i>
                                        <span class="menu-title">@lang('menu.Shipping Services')</span>
                                    </a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\Rate::class)
                                <li
                                    class="{{ $isActive(['admin.rates.shipping-rates.index', 'admin.rates.shipping-rates.create', 'admin.rates.view-shipping-rates', 'admin.rates.region-rates', 'admin.rates.view-shipping-region-rates']) }}">
                                    <a href="{{ route('admin.rates.shipping-rates.index') }}">
                                        <i class="feather icon-circle"></i>
                                        <span class="menu-title">@lang('menu.Homedeliverybr Cost')</span>
                                    </a>
                                </li>
                            @endcan

                            @can('viewAny', App\Models\Rate::class)
                                <li
                                    class="{{ $isActive(['admin.rates.accrual-rates.index', 'admin.rates.accrual-rates.create', 'admin.rates.show-accrual-rates']) }}">
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
                <li class="nav-item has-sub sidebar-group">
                    <a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-dollar-sign">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                        <span class="menu-title" data-i18n="Dashboard">@lang('menu.Accounting')</span>
                    </a>
                    <ul class="menu-content">
                        @can('view_payment_invoice', App\Models\PaymentInvoice::class)
                            <li class="nav-item {{ $isActive(['admin.payment-invoices.index', 'admin.payment-invoices.edit']) }}">
                                <a href="{{ route('admin.payment-invoices.index') }}">
                                    <i class=" feather icon-circle"></i>
                                    <span class="menu-title" id="invoice">@lang('menu.payment-invoice')</span>
                                </a>
                            </li>
                        @endcan
                        @can('viewAny', App\Models\BillingInformation::class)
                            <li class="nav-item {{ $isActive(['admin.billing-information.index', 'admin.billing-information.edit', 'admin.billing-information.create']) }}">
                                <a href="{{ route('admin.billing-information.index') }}">
                                    <i class=" feather icon-circle"></i>
                                    <span class="menu-title">@lang('menu.Billing Informations')</span>
                                </a>
                            </li>
                            <li class="nav-item {{ $isActive(['admin.deposit.index', 'admin.deposit.edit', 'admin.deposit.create']) }}">
                                <a href="{{ route('admin.deposit.index') }}">
                                    <i class=" feather icon-circle"></i>
                                    <span class="menu-title">@lang('menu.Balance')</span>
                                </a>
                            </li>
                        @endcan
                
                        @admin
                            <li class="nav-item {{ $isActive(['admin.liability.index', 'admin.liability.edit', 'admin.liability.create']) }}">
                                <a href="{{ route('admin.liability.index') }}">
                                    <i class=" feather icon-circle"></i>
                                    <span class="menu-title">@lang('HD Liability')</span>
                                </a>
                            </li>
                            <li class="nav-item {{ $isActive(['admin.tax.index']) }}">
                                <a href="{{ route('admin.tax.index') }}">
                                    <i class=" feather icon-circle"></i>
                                    <span class="menu-title">Tax Payment</span>
                                </a>
                            </li>
                        @endadmin
                    </ul>
                </li>
                <li class="sub-category"> <span class="text-white"
                        style="padding-left:16px; padding-top:15px; padding-bottom:8px">UTILITIES</span> </li>
                @if (auth()->user()->isAdmin())
                    <li class="nav-item has-sub sidebar-group">
                        <a href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-zoom-in">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                <line x1="11" y1="8" x2="11" y2="14"></line>
                                <line x1="8" y1="11" x2="14" y2="11"></line>
                            </svg>
                            <span class="menu-title" data-i18n="Dashboard">Scan</span>
                        </a>
                        <ul class="menu-content">
                            <li class="nav-item {{ $isActive(['warehouse.scan-label.index']) }}">
                                <a class="nav-link" href="{{ route('warehouse.scan-label.index') }}">
                                    <i class="feather icon-circle"></i>
                                    <span data-i18n="Apps">@lang('menu.Scan Parcel')</span>
                                </a>
                            </li>
                            <li class="nav-item {{ $isActive(['warehouse.scan-label.create']) }} ">
                                <a class="nav-link" href="{{ route('warehouse.scan-label.create') }}">
                                    <i class="feather icon-circle"></i>
                                    <span data-i18n="Apps">@lang('menu.Driver Report')</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                @can('printBulkLabel', App\Models\Order::class)
                    <li class="{{ $isActive(['admin.label.scan.create']) }}">
                        <a href="{{ route('admin.label.scan.create') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-printer">
                                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2">
                                </path>
                                <rect x="6" y="14" width="12" height="8"></rect>
                            </svg>
                            <span class="menu-title">@lang('menu.Print Label')</span>
                        </a>
                    </li>
                @endcan
                {{-- Reports --}}
                <li class="nav-item has-sub sidebar-group">
                    <a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-file">
                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                            <polyline points="13 2 13 9 20 9"></polyline>
                        </svg>
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
                        <li
                            class="{{ $isActive(['admin.reports.commission.index', 'admin.reports.commission.show']) }}">
                            <a href="{{ route('admin.reports.commission.index') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">@lang('menu.Reports.Commission Report')</span>
                            </a>
                        </li>
                        @endcan
                        @can('viewKPIReport', App\Models\Reports::class)
                        <li class="@if(request('type')=='report') active @endif">
                            <a href="{{ route('admin.reports.kpi-report.index',['type' =>'report']) }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">@lang('menu.Reports.KPI Report')</span>
                            </a>
                        </li> 
                        <li class="@if(request('type')=='scan') active @endif">
                            <a href="{{ route('admin.reports.kpi-report.index',['type' =>'scan']) }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">@lang('menu.Reports.Tax Report Scan')</span>
                            </a>
                        </li>
                    @endcan
                        @admin
                        <li class="{{ $isActive(['admin.reports.unpaid-orders']) }}">
                            <a href="{{ route('admin.reports.unpaid-orders') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">Un Paid Orders Report</span>
                            </a>
                        </li>
                    @endadmin
                    </ul>
                </li>
                @can('view_calculator')
                    <li class="nav-item has-sub sidebar-group">
                        <a href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-divide-square">
                                <rect x="3" y="3" width="18" height="18" rx="2"
                                    ry="2"></rect>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                                <line x1="12" y1="16" x2="12" y2="16"></line>
                                <line x1="12" y1="8" x2="12" y2="8"></line>
                            </svg>
                            <span class="menu-title" data-i18n="Dashboard">Calculators</span>
                        </a>
                        <ul class="menu-content">
                            <li class="nav-item {{ $isActive(['calculator.index']) }} ">
                                <a class="nav-link" href="{{ route('calculator.index') }}" target="_blank">
                                    <i class="feather icon-circle"></i>
                                    <span class="menu-title" data-i18n="Apps">@lang('menu.calculator')</span>
                                </a>
                            </li>
                            <li class="nav-item {{ $isActive(['us-calculator.index']) }} ">
                                <a class="nav-link" href="{{ route('us-calculator.index') }}" target="_blank">
                                    <i class="feather icon-circle"></i>
                                    <span class="menu-title" data-i18n="Apps">@lang('menu.uscalculator')</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan
                @can('view',App\Models\OrderTracking::class)
                    <li class="nav-item {{ $isActive(['admin.tracking.index']) }}">
                        <a href="{{ route('admin.tracking.index') }}" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-search">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <span class="menu-title">@lang('menu.trackings')</span>
                        </a>
                    </li>
                @endcan

                @can('importExcel', App\Models\Order::class)
                    <li
                        class="{{ $isActive(['admin.import.import-excel.index', 'admin.import.import-excel.show', 'admin.import.import-excel.create']) }}">
                        <a href="{{ route('admin.import.import-excel.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-upload-cloud">
                                <polyline points="16 16 12 12 8 16"></polyline>
                                <line x1="12" y1="12" x2="12" y2="21"></line>
                                <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path>
                                <polyline points="16 16 12 12 8 16"></polyline>
                            </svg>
                            <span class="menu-title">@lang('menu.import-excel-order.excel')</span>
                        </a>
                    </li>
                @endcan

                @can('viewAny', App\Models\Connect::class)
                    <li class="nav-item {{ $isActive(['admin.connect.index']) }}">
                        <a class="nav-link" href="{{ route('admin.connect.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-link">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                            </svg>
                            <span data-i18n="Apps">@lang('menu.connect')</span>
                        </a>
                    </li>
                @endcan

                @if (auth()->user()->isUser())
                    @can('userSellingRates', App\Models\ProfitPackage::class)
                        <li class="nav-item {{ $isActive(['admin.rates.user-rates.index']) }}">
                            <a class="nav-link" href="{{ route('admin.rates.user-rates.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-dollar-sign">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                <span data-i18n="Apps">@lang('menu.My Rates')</span>
                            </a>
                        </li>
                    @endcan
                @endif

                @can('viewAny', App\Models\Address::class)
                    <li
                        class="nav-item {{ $isActive(['admin.addresses.index', 'admin.addresses.edit', 'admin.addresses.create']) }}">
                        <a class="nav-link" href="{{ route('admin.addresses.index') }}">
                            <svg viewBox="0 0 24 24" height="15" stroke="currentColor" stroke-width="2"
                                fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <span data-i18n="Apps">@lang('menu.addresses')</span>
                        </a>
                    </li>
                @endcan
                @admin
                    <li
                        class="nav-item {{ $isActive(['admin.shcode.index', 'admin.shcode.create', 'admin.shcode.edit']) }}">
                        <a href="{{ route('admin.shcode.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-codepen">
                                <polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5 12 2"></polygon>
                                <line x1="12" y1="22" x2="12" y2="15.5"></line>
                                <polyline points="22 8.5 12 15.5 2 8.5"></polyline>
                                <polyline points="2 15.5 12 8.5 22 15.5"></polyline>
                                <line x1="12" y1="2" x2="12" y2="8.5"></line>
                            </svg>
                            <span class="menu-title">SH Codes</span>
                        </a>
                    </li>
                @endadmin
                <li class="sub-category"> <span class="text-white">GENERAL</span> </li>


                {{-- Inventory --}}
                {{-- @can('viewAny', App\Models\Product::class) --}}

                {{-- @endcan --}}
                {{-- Affiliate --}}


                @can('viewAny', App\Models\User::class)
                    <li class="nav-item {{ $isActive(['admin.users.index']) }}">
                        <a href="{{ route('admin.users.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-users">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span class="menu-title">@lang('menu.Users')</span>
                        </a>
                    </li>
                @endcan

                @can('viewAny', App\Models\Role::class)
                    <li class="nav-item {{ $isActive(['admin.roles.index']) }}">
                        <a href="{{ route('admin.roles.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-key">
                                <path
                                    d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4">
                                </path>
                            </svg>
                            <span class="menu-title">@lang('menu.Roles')</span>
                        </a>
                    </li>
                @endcan

                <li class="nav-item {{ $isActive(['admin.profile.index']) }} ">
                    <a href="{{ route('admin.profile.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-user">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                          <span data-i18n="Apps"> @lang('menu.profile') </span>
                    </a>
                </li>

                @can('viewAny', Spatie\Activitylog\Models\Activity::class)
                    <li class="nav-item {{ $isActive(['admin.activity.log.index']) }}">
                        <a href="{{ route('admin.activity.log.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-activity">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                            </svg>
                            <span class="menu-title">@lang('menu.activity')</span>
                        </a>
                    </li>
                @endcan
                @admin
                    @can('viewAny', App\Models\Setting::class)
                        <li class="nav-item {{ $isActive(['admin.settings.index']) }}">
                            <a href="{{ route('admin.settings.index') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-settings">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path
                                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                                    </path>
                                </svg>
                                <span class="menu-title">@lang('menu.Settings')</span>
                            </a>
                        </li>
                    @endcan
                @endadmin
                <x-shared-menu></x-shared-menu>
                @can('view_api_docs')
                <li class="sub-category"> <span class="text-white">HELP</span> </li>
                    <li class="nav-item">
                        <a class="nav-link" target="__blank"
                            href="https://documenter.getpostman.com/view/16057364/TzeXmSxT">
                            <svg viewBox="0 0 24 24" height="15" stroke="currentColor" stroke-width="2"
                                fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg> <span data-i18n="Apps"> @lang('menu.API Documents') </span>
                        </a>
                    </li>
                @endcan
            @endif            
            @can('show_ticket', App\Models\Ticket::class)
                <li class="nav-item {{ $isActive(['admin.tickets.index', 'admin.tickets.show']) }}">
                    <a class="nav-link" href="{{ route('admin.tickets.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-message-circle">
                            <path
                                d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                            </path>
                        </svg>
                        <span data-i18n="Apps">@lang('menu.support tickets')</span>
                        <livewire:components.support-ticket />
                    </a>
                </li>
            @endcan
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
