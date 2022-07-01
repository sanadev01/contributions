@can('do_warehouse_operations')
<li class="nav-item has-sub sidebar-group">
    <a href="#">
        <img src="{{ asset('images/icon/warehouse.svg') }}" alt="warehouse">
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
        @endadmin
        <li class="{{ $isActive(['warehouse.chile_containers.index','warehouse.chile_containers.create','warehouse.chile_containers.edit','warehouse.chile-container.packages']) }}">
            <a href="{{ route('warehouse.chile_containers.index') }}">
                <i class="feather icon-circle"></i>
                <span class="menu-title">@lang('menu.Warehouse.Chile Containers')</span>
            </a>
        </li>
    </ul>
</li>
@endcan
