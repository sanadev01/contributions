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
                <li class="#">
                    <a href="{{ route('warehouse.unitinfo.create') }}">
                        <i class="feather icon-circle"></i>
                        <span class="menu-title">Unit Info</span>
                    </a>
                </li>
            @endadmin
            <li class="{{ $isActive(['warehouse.delivery_bill.index','warehouse.delivery_bill.create','warehouse.delivery_bill.edit','warehouse.delivery_bill.show']) }}">
                <a href="{{ route('warehouse.delivery_bill.index') }}">
                    <i class="feather icon-circle"></i>
                    <span class="menu-title">@lang('menu.Warehouse.Delivery Bill')</span>
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
                    @admin
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
                        <li class="{{ $isActive(['warehouse.gde_containers.index','warehouse.gde_containers.create','warehouse.gde_containers.edit','warehouse.gde_container.packages.index']) }}">
                            <a href="{{ route('warehouse.gde_containers.index') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">GDE</span>
                            </a>
                        </li>
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
                        <li class="{{ $isActive(['warehouse.gss_containers.index','warehouse.gss_containers.create','warehouse.gss_containers.edit','warehouse.gss_container.packages.index']) }}">
                            <a href="{{ route('warehouse.gss_containers.index') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">GSS</span>
                            </a>
                        </li>
                        <li class="{{ $isActive(['warehouse.totalexpress_containers.index','warehouse.totalexpress_containers.create','warehouse.totalexpress_containers.edit','warehouse.totalexpress_container.packages.index']) }}">
                            <a href="{{ route('warehouse.totalexpress_containers.index') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">Total Express</span>
                            </a>
                        </li>
                        <li > <a href="{{ route('warehouse.containers_factory.index',['service_sub_class'=>App\Models\ShippingService::PasarEx]) }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">PasarEx</span>
                            </a>
                        </li>
                        <li class="{{ $isActive(['warehouse.hound_containers.index','warehouse.hound_containers.create','warehouse.hound_containers.edit','warehouse.hound_container.packages.index']) }}">
                            <a href="{{ route('warehouse.hound_containers.index') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">Hound Express</span>
                            </a>
                        </li>
                        <li class="{{ $isActive(['warehouse.hd-express-containers.index','warehouse.hd-express-containers.create','warehouse.hd-express-containers.edit','warehouse.hd-express-containers.packages.index']) }}">
                            <a href="{{ route('warehouse.hd-express-containers.index') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">@lang('menu.Warehouse.HD Express')</span>
                            </a>
                        </li>
                        <li class="{{ $isActive(['warehouse.hd-senegal-containers.index','warehouse.hd-senegal-containers.create','warehouse.hd-senegal-containers.edit','warehouse.hd-senegal-containers.packages.index']) }}">
                            <a href="{{ route('warehouse.hd-senegal-containers.index') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">@lang('menu.Warehouse.DSS Senegal')</span>
                            </a>
                        </li>
                        <li > <a href="{{ route('warehouse.containers_factory.index',['service_sub_class'=>App\Models\ShippingService::FOX_ST_COURIER]) }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">Fox Courier</span>
                            </a>
                        </li>
                        <li > <a href="{{ route('warehouse.containers_factory.index',['service_sub_class'=>App\Models\ShippingService::PHX_ST_COURIER]) }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">Phx Courier</span>
                            </a>
                        </li>

                        <li class="{{ $isActive(['warehouse.chile_containers.index','warehouse.chile_containers.create','warehouse.chile_containers.edit','warehouse.chile_container.packages.index']) }}">
                            <a href="{{ route('warehouse.chile_containers.index') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-title">@lang('menu.Warehouse.Chile')</span>
                            </a>
                        </li>
                    @endadmin
                </ul>
            </li>
        {{-- @endadmin --}}
    </ul>
</li>
@endcan
