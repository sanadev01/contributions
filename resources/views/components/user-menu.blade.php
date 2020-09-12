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

            <li class="nav-item has-sub sidebar-group">
                <a href="#">
                    <i class="feather icon-dollar-sign"></i>
                    <span class="menu-title" data-i18n="Dashboard">@lang('menu.Rates')</span>
                </a>
                <ul class="menu-content">
                    <li class="{{ $isActive(['admin.rates.profit-packages.index']) }}">
                        <a href="{{ route('admin.rates.profit-packages.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Profit Packages')</span>
                        </a>
                    </li>
        
                    <li class="{{ $isActive(['admin.services.index','admin.services.edit','admin.services.create']) }}">
                        <a href="{{ route('admin.services.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Handling Services')</span>
                        </a>
                    </li>
        
                    <li class="nav-item {{ $isActive(['admin.shipping-services.index','admin.shipping-services.create']) }}">
                        <a href="{{ route('admin.shipping-services.index') }}">
                            <i class="feather icon-truck"></i>
                            <span class="menu-title">@lang('menu.Shipping Services')</span>
                        </a>
                    </li>
        
                    <li class="{{ $isActive(['admin.rates.bps-leve.index','admin.rates.bps-leve.create']) }}">
                        <a href="{{ route('admin.rates.bps-leve.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.LEVE & BPS Rates')</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <li class="nav-item {{ $isActive(['admin.addresses.index','admin.addresses.edit','admin.addresses.create']) }}">
                <a class="nav-link" href="{{ route('admin.addresses.index') }}"><i class="feather icon-home"></i>
                    <span data-i18n="Apps">@lang('menu.addresses')</span>
                </a>
            </li>

            <li class="nav-item {{ $isActive(['calculator.index']) }}">
                <a class="nav-link" href="{{ route('calculator.index') }}" target="_blank">
                    <i class="fa fa-calculator"></i>
                    <span data-i18n="Apps">@lang('menu.calculator')</span>
                </a>
            </li>

            <li class="nav-item {{ $isActive(['admin.users.index']) }}">
                <a href="{{ route('admin.users.index') }}">
                    <i class="feather icon-users"></i>
                    <span class="menu-title">@lang('menu.Users')</span>
                </a>
            </li>

            <li class="nav-item {{ $isActive(['admin.roles.index']) }}">
                <a href="{{ route('admin.roles.index') }}">
                    <i class="fa fa-key"></i>
                    <span class="menu-title">@lang('menu.Roles')</span>
                </a>
            </li>

            <li class="nav-item {{ $isActive(['admin.tickets.index','admin.tickets.show']) }}">
                <a class="nav-link" href="{{ route('admin.tickets.index') }}"><i class="feather icon-message-circle"></i>
                    <span data-i18n="Apps">@lang('menu.support tickets')</span>
                </a>
            </li>

            <li class="nav-item {{ $isActive(['admin.billing-information.index','admin.billing-information.edit','admin.billing-information.create']) }}">
                <a href="{{ route('admin.billing-information.index') }}">
                    <i class="feather icon-alert-triangle"></i>
                    <span class="menu-title">@lang('menu.Billing Informations')</span>
                </a>
            </li>

            <li class="nav-item {{ $isActive(['admin.settings.index']) }}">
                <a href="{{ route('admin.settings.index') }}">
                    <i class="feather icon-settings"></i>
                    <span class="menu-title">@lang('menu.Settings')</span>
                </a>
            </li>
            

            <x-shared-menu></x-shared-menu>
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
