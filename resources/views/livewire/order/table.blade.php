<div class="row">
    <div class="col-md-12">
        <div class="hd-card mt-1 mb-3">
            <div class="row col-12 p-0 m-0" id="togglers" style="justify-content: space-between;">
            </div>
            <div class="row col-8 pr-0 pl-4 " id="singleSearch"
                @if ($this->search) style="display: block !important;" @endif>
                <div class="form-group singleSearchStyle col-12">

                    <form wire:submit.prevent="render">
                        <div class="form-group mb-2 col-12 row">
                            <label class="col-12 text-left pl-1"> Search</label>
                            <input type="text" class="form-control col-8 hd-search" wire:model.defer="search">
                            <button type="submit" class="btn btn-primary ml-2">
                                <i class="fa fa-search"></i>
                            </button>
                            <button class="btn btn-primary ml-1 waves-effect waves-light"
                                onclick="window.location.reload();">
                                <i class="fa fa-undo" data-bs-toggle="tooltip" title=""
                                    data-bs-original-title="fa fa-undo" aria-label="fa fa-undo"
                                    aria-hidden="true"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row col-12 pr-0 m-0 pl-0" id="datefilters">
                <div class=" col-6 text-left">
                    <div class="row col-12 pl-1" id="dateSearch">
                        <form action="{{ route('admin.order.exports') }}" method="GET" target="_blank">
                            @csrf
                            <div class="form-group mb-2 col-4" style="float:left;margin-right:20px;">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control">
                            </div>
                            <div class="form-group mx-sm-3 mb-2 col-4" style="float:left;margin-right:20px;">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control">
                            </div>
                            <button class="btn btn-success searchDateBtn" title="@lang('orders.import-excel.Download')">
                                <i class="fa fa-arrow-down"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>

        </div>

        <div class="table-responsive order-table">
            <table class="table mb-0  table-bordered">
                <thead>
                    <tr>

                        @if (\Request::route()->getName() != 'admin.trash-orders.index')
                            <th id="optionChkbx">
                                <div class="vs-checkbox-con vs-checkbox-primary" title="Select All">
                                    <input type="checkbox" id="checkAll" name="orders[]" class="check-all"
                                        value="">
                                    <span class="vs-checkbox vs-checkbox-sm">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                </div>
                            </th>
                        @endif
                        @admin
                            <th id="userNameCol">USER NAME</th>
                        @endadmin
                        <th>
                            @if (\Request::route()->getName() != 'admin.trash-orders.index')
                                {{-- <span class="mr-4"> @lang('Edit Order')</span> --}}
                            @endif
                            @lang('orders.date')<a wire:click.prevent="sortBy('created_at')"
                                class="fas fa-sort text-right custom-sort-arrow"></a>
                        </th>
                        <th>
                            @lang('orders.order-id')<a wire:click.prevent="sortBy('id')"
                                class="fas fa-sort text-right custom-sort-arrow"></a>
                        </th>

                        {{-- <th>Loja/Cliente</th>
                        <th>Carrier Tracking</th> --}}
                        {{-- <th>Reference</th> --}}
                        {{-- <th>Carrier</th>
                        @admin
                            <th>Carrier Cost</th>
                        @endadmin --}}
                        <th>TRACKING CODE</th>
                        <th>@lang('orders.amount')<a wire:click.prevent="sortBy('gross_total')"
                                class="fas fa-sort text-right custom-sort-arrow"></a></th>
                        <th>@lang('orders.status')</th>
                        {{-- <th>@lang('orders.type')</th> --}}
                        <th>@lang('orders.payment-status')</th>
                        <th class="no-print">@lang('orders.actions.actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        @include('admin.orders.components.order-row', ['order' => $order])
                    @empty
                        <x-tables.no-record colspan="15"></x-tables.no-record>
                    @endforelse
                </tbody>
            </table>
            <livewire:order.bulk-edit.modal />
        </div>
        <div class="row">
            <div class="col-1 pt-2">
                <select class="form-control hd-search" wire:model="pageSize">
                    <option value="1">1</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="300">300</option>
                </select>
            </div>
            <div class="col-11 pr-0 d-flex justify-content-end pr-3 pt-2">
                {{ $orders->links() }}
            </div>
        </div>
        @include('layouts.livewire.loading')
    </div>
</div>

@push('lvjs-stack')
    <script>
        function toggleDateSearch() {
            const div = document.getElementById('dateSearch');
            if (div.style.display != 'block') {
                div.style.display = 'block';
            } else {
                div.style.display = 'none';
            }

        }

        function toggleOrderPageSearch() {
            const div = document.getElementById('singleSearch');
            console.log(div);
            if (div.style.display != 'block') {
                div.style.display = 'block';
            } else {
                div.style.display = 'none';
            }
        }
        window.addEventListener('DOMContentLoaded', () => {

            @this.on('updated-status', function(orderId, status) {
                $.post('{{ route('api.order.status.update') }}', {
                        order_id: orderId,
                        status: status
                    })
                    .then(function(response) {
                        if (response.success) {
                            @this.call('render')
                        } else {
                            toastr.error(response.message)
                        }
                    }).catch(function(data) {
                        toastr.error(response.message)
                    })
            })

            // @this.on('edit-order',function(){
            //     $('#order-table').addClass('w-25');
            //     $('#order-table').removeClass('w-100');
            // })
        });
    </script>
@endpush
