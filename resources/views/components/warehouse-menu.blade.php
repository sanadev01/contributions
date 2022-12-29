@can('do_warehouse_operations')
<li class="nav-item has-sub sidebar-group">
    <a href="#">
        <svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-archive"><polyline points="21 8 21 21 3 21 3 8"></polyline><rect x="1" y="3" width="22" height="5"></rect><line x1="10" y1="12" x2="14" y2="12"></line></svg>
        <span class="menu-title">@lang('menu.Warehouse.menu')</span>
    </a>
    <ul class="menu-content">

        @admin
            <li class="{{ $isActive(['warehouse.scan.index']) }}">
                <a href="{{ route('warehouse.scan.index') }}">
                    <i class="feather icon-circle"></i>
                    <span class="menu-title">Check In Parcel</span>
                </a>
            </li>
            <li class="{{ $isActive(['warehouse.search_package.index','warehouse.search_package.show']) }}">
                <a href="{{ route('warehouse.search_package.index') }}">
                    <i class="feather icon-circle"></i>
                    <span class="menu-title">Search packages</span>
                </a>
            </li>
            <li class="{{ $isActive(['warehouse.containers.index','warehouse.containers.create','warehouse.containers.edit','warehouse.containers.packages.index']) }}">
                <a href="{{ route('warehouse.containers.index') }}">
                    <i class="feather icon-circle"></i>
                    <span class="menu-title">@lang('menu.Warehouse.Containers')</span>
                </a>
            </li>

            <li class="{{ $isActive(['warehouse.delivery_bill.index','warehouse.delivery_bill.create','warehouse.delivery_bill.edit','warehouse.delivery_bill.show']) }}">
                <a href="{{ route('warehouse.delivery_bill.index') }}">
                    <i class="feather icon-circle"></i>
                    <span class="menu-title">@lang('menu.Warehouse.Delivery Bill')</span>
                </a>
            </li>
            <li class="{{ $isActive(['warehouse.usps_containers.index','warehouse.usps_containers.create','warehouse.usps_containers.edit','warehouse.usps-container.packages']) }}">
                <a href="{{ route('warehouse.usps_containers.index') }}">
                    <i class="feather icon-circle"></i>
                    <span class="menu-title">@lang('menu.Warehouse.USPS Containers')</span>
                </a>
            </li>
            <li class="{{ $isActive(['warehouse.sinerlog_containers.index','warehouse.sinerlog_containers.create','warehouse.sinerlog_containers.edit','warehouse.sinerlog_container.packages.index']) }}">
                <a href="{{ route('warehouse.sinerlog_containers.index') }}">
                    <i class="feather icon-circle"></i>
                    <span class="menu-title">@lang('menu.Warehouse.Sinerlog Containers')</span>
                </a>
            </li>
            <li class="{{ $isActive(['warehouse.mile-express-containers.index','warehouse.mile-express-containers.create','warehouse.mile-express-containers.edit']) }}">
                <a href="{{ route('warehouse.mile-express-containers.index') }}">
                    <i class="feather icon-circle"></i>
                    <span class="menu-title">@lang('menu.Warehouse.MileExpress Containers')</span>
                </a>
            <li class="{{ $isActive(['warehouse.postnl_containers.index','warehouse.postnl_containers.create','warehouse.postnl_containers.edit','warehouse.postnl_container.packages.index']) }}">
                <a href="{{ route('warehouse.postnl_containers.index') }}">
                    <i class="feather icon-circle"></i>
                    <span class="menu-title">@lang('menu.Warehouse.PostNL Containers')</span>
                </a>
            <li class="{{ $isActive(['warehouse.chile_containers.index','warehouse.chile_containers.create','warehouse.chile_containers.edit','warehouse.chile_container.packages.index']) }}">
                <a href="{{ route('warehouse.chile_containers.index') }}">
                    <i class="feather icon-circle"></i>
                    <span class="menu-title">@lang('menu.Warehouse.Chile Containers')</span>
                </a>
            </li>
            <li class="{{ $isActive(['warehouse.colombia-containers.index','warehouse.colombia-containers.create','warehouse.colombia-containers.edit','warehouse.colombia-container.packages']) }}">
                <a href="{{ route('warehouse.colombia-containers.index') }}">
                    <i class="feather icon-circle"></i>
                    <span class="menu-title">@lang('menu.Warehouse.Colombia Containers')</span>
                </a>
            </li>
            <li class="{{ $isActive(['warehouse.geps_containers.index','warehouse.geps_containers.create','warehouse.geps_containers.edit','warehouse.geps_container.packages.index']) }}">
                <a href="{{ route('warehouse.geps_containers.index') }}">
                    <i class="feather icon-circle"></i>
                    <span class="menu-title">@lang('menu.Warehouse.GePS Containers')</span>
                </a>
            </li>
            <li class="{{ $isActive(['warehouse.swedenpost_containers.index','warehouse.swedenpost_containers.create','warehouse.swedenpost_containers.edit','warehouse.swedenpost_container.packages.index']) }}">
                <a href="{{ route('warehouse.swedenpost_containers.index') }}">
                    <i class="feather icon-circle"></i>
                    <span class="menu-title">@lang('menu.Warehouse.Prime5 Containers')</span>
                </a>
            </li>
            <li class="#">
                <a href="{{ route('warehouse.unitinfo.create') }}">
                    <i class="feather icon-circle"></i>
                    <span class="menu-title">Unit Info</span>
                </a>
            </li>
        @endadmin
        
    </ul>
</li>
@endcan
