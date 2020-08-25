<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow menu-collapsed" data-scroll-to-active="true" style="touch-action: none; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
    {!! $header !!}
    <div class="shadow-bottom"></div>
    <div class="main-menu-content ps ps--active-y">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item {{ $isActive('admin.home') }}">
                <a class="nav-link" href="#">
                    <i class="feather icon-home"></i>
                    <span data-i18n="Dashboard"> @lang('menu.dashboard') </span>
                </a>
            </li>
            <li class="nav-item {{ $isActive(['admin.prealerts.index','admin.prealerts.edit']) }}">
                <a class="nav-link" href="#"><i class="feather icon-alert-triangle"></i>
                    <span data-i18n="Apps">@lang('menu.prealerts')</span>
                </a>
            </li>
            <li class="nav-item {{ $isActive(['admin.orders.index','admin.orders.show']) }}">
                <a class="nav-link" href="#"><i class="feather icon-package"></i>
                    <span data-i18n="Apps">@lang('menu.orders')</span>
                </a>
            </li>
            <li class="nav-item {{ $isActive(['calculator.index']) }}">
                <a class="nav-link" href="#" target="_blank">
                    <i class="fa fa-calculator"></i>
                    <span data-i18n="Apps">@lang('menu.calculator')</span>
                </a>
            </li>
            {{-- <li class="nav-item {{ $isActive(['user.transactions.index']) }}">
                <a class="nav-link" href="{{ route('user.transactions.index') }}">
                    <i class="feather icon-repeat"></i>
                    <span data-i18n="Apps">@lang('menu.transactions')</span>
                </a>
            </li> --}}
            <li class="nav-item {{ $isActive(['admin.addresses.index','admin.addresses.edit','admin.addresses.create']) }}">
                <a class="nav-link" href="#"><i class="feather icon-home"></i>
                    <span data-i18n="Apps">@lang('menu.addresses')</span>
                </a>
            </li>
            <li class="nav-item {{ $isActive(['admin.tickets.index','admin.tickets.show']) }}">
                <a class="nav-link" href="#"><i class="feather icon-message-circle"></i>
                    <span data-i18n="Apps">@lang('menu.support tickets')</span>
                </a>
            </li>

            {{-- <x-shared-menu></x-shared-menu> --}}
        </ul>
    </div>
</div>
<!-- END: Main Menu-->
