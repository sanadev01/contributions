@can('do_warehouse_operations')
<li class="nav-item has-sub sidebar-group">
    <a href="#">
        <i class="fa fa-home"></i>
        <span class="menu-title">@lang('menu.Warehouse.menu')</span>
    </a>
    <ul class="menu-content">

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

    </ul>
</li>
@endcan
