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
            <li class="{{ $isActive(['warehouse.delivery_bill.index','warehouse.delivery_bill.create','warehouse.delivery_bill.edit','warehouse.delivery_bill.show']) }}">
                <a href="{{ route('warehouse.delivery_bill.index') }}">
                    <i class="feather icon-circle"></i>
                    <span class="menu-title">@lang('menu.Warehouse.Delivery Bill')</span>
                </a>
            </li>
            <li class="#">
                <a href="{{ route('warehouse.unitinfo.create') }}">
                    <i class="feather icon-circle"></i>
                    <span class="menu-title">Unit Info</span>
                </a>
            </li>
            <li class="nav-item has-sub sidebar-group">
                <a href="#">
                    <!-- <img src="{{ asset('images/icon/warehouse.svg') }}" alt="warehouse"> -->
                    <span class="menu-title">@lang('menu.Warehouse.Containers')</span>
                </a>
                <ul class="menu-content">
                    <li class="{{ $isActive(['warehouse.containers.index','warehouse.containers.create','warehouse.containers.edit','warehouse.containers.packages.index']) }}">
                        <a href="{{ route('warehouse.containers.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Warehouse.Correios')</span>
                        </a>
                    </li>
                    <li class="{{ $isActive(['warehouse.usps_containers.index','warehouse.usps_containers.create','warehouse.usps_containers.edit','warehouse.usps_container.packages.index']) }}">
                        <a href="{{ route('warehouse.usps_containers.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Warehouse.USPS')</span>
                        </a>
                    </li>
                    <!-- <li class="{{ $isActive(['warehouse.sinerlog_containers.index','warehouse.sinerlog_containers.create','warehouse.sinerlog_containers.edit','warehouse.sinerlog_container.packages.index']) }}">
                        <a href="{{ route('warehouse.sinerlog_containers.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Warehouse.Sinerlog')</span>
                        </a>
                    </li> -->
                    <li class="{{ $isActive(['warehouse.geps_containers.index','warehouse.geps_containers.create','warehouse.geps_containers.edit','warehouse.geps_container.packages.index']) }}">
                        <a href="{{ route('warehouse.geps_containers.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Warehouse.GePS')</span>
                        </a>
                    </li>
                    <li class="{{ $isActive(['warehouse.swedenpost_containers.index','warehouse.swedenpost_containers.create','warehouse.swedenpost_containers.edit','warehouse.swedenpost_container.packages.index']) }}">
                        <a href="{{ route('warehouse.swedenpost_containers.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Warehouse.Prime5')</span>
                        </a>
                    </li>
                    <li class="{{ $isActive(['warehouse.postplus_containers.index','warehouse.postplus_containers.create','warehouse.postplus_containers.edit','warehouse.postplus_container.packages.index']) }}">
                        <a href="{{ route('warehouse.postplus_containers.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Warehouse.PostPlus')</span>
                        </a>
                    </li>
                    
                    <li class="{{ $isActive(['warehouse.chile_containers.index','warehouse.chile_containers.create','warehouse.chile_containers.edit','warehouse.chile_container.packages.index']) }}">
                        <a href="{{ route('warehouse.chile_containers.index') }}">
                            <i class="feather icon-circle"></i>
                            <span class="menu-title">@lang('menu.Warehouse.Chile')</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endadmin
    </ul>
</li>
@endcan
