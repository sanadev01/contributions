@can('do_warehouse_operations')
<li class="nav-item has-sub sidebar-group">
    <a href="#">
        <i class="fa fa-home"></i>
        <span class="menu-title">@lang('menu.Warehouse.menu')</span>
    </a>
    <ul class="menu-content">

        <li class="{{ $isActive(['warehouse.containers.index']) }}">
            <a href="{{ route('warehouse.containers.index') }}">
                <i class="feather icon-circle"></i>
                <span class="menu-title">@lang('menu.Warehouse.Containers')</span>
            </a>
        </li>

        <li class="{{ $isActive(['warehouse.delivery_bill.index']) }}">
            <a href="{{ route('warehouse.delivery_bill.index') }}">
                <i class="feather icon-circle"></i>
                <span class="menu-title">@lang('menu.Warehouse.Delivery Bill')</span>
            </a>
        </li>

    </ul>
</li>
@endcan
