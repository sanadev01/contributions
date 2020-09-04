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
                    <span class="menu-title">Parcels</span>
                </a>
            </li>

            <li class="nav-item has-sub sidebar-group">
                <a href="#">
                    <i class="feather icon-dollar-sign"></i>
                    <span class="menu-title" data-i18n="Dashboard">Rates</span>
                </a>
                <ul class="menu-content">
                    <li class="{{ $isActive(['admin.rates.profit-packages.index']) }}">
                        <a href="{{ route('admin.rates.profit-packages.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">Profit Packages</span>
                        </a>
                    </li>
        
                    <li class="{{ $isActive(['admin.services.index','admin.services.edit','admin.services.create']) }}">
                        <a href="{{ route('admin.services.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">Handling Services</span>
                        </a>
                    </li>
        
                    <li class="nav-item {{ $isActive(['admin.shipping-services.index','admin.shipping-services.create']) }}">
                        <a href="{{ route('admin.shipping-services.index') }}">
                            <i class="feather icon-truck"></i>
                            <span class="menu-title">Shipping Services</span>
                        </a>
                    </li>
        
                    <li class="{{ $isActive(['admin.rates.bps-leve.index','admin.rates.bps-leve.create']) }}">
                        <a href="{{ route('admin.rates.bps-leve.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">LEVE & BPS Rates</span>
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
                    <span class="menu-title">Users</span>
                </a>
            </li>

            <li class="nav-item {{ $isActive(['admin.roles.index']) }}">
                <a href="{{ route('admin.roles.index') }}">
                    <i class="fa fa-key"></i>
                    <span class="menu-title">Roles</span>
                </a>
            </li>

            <li class="nav-item {{ $isActive(['admin.tickets.index','admin.tickets.show']) }}">
                <a class="nav-link" href="{{ route('admin.tickets.index') }}"><i class="feather icon-message-circle"></i>
                    <span data-i18n="Apps">@lang('menu.support tickets')</span>
                </a>
            </li>

            <li class="nav-item {{ $isActive(['admin.settings.index']) }}">
                <a href="{{ route('admin.settings.index') }}">
                    <i class="feather icon-settings"></i>
                    <span class="menu-title">Settings</span>
                </a>
            </li>
            

            <x-shared-menu></x-shared-menu>
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
