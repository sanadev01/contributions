@extends('layouts.master')

@section('page')
    <div class="card min-vh-100">
        <div class="card-header">
            <h4 class="mb-0">{{ $orders->file_name }} @lang('orders.orders')</h4>
            <a href="{{ route('admin.import.import-excel.index') }}" class="pull-right btn btn-primary"> @lang('orders.leve.Return to List')
            </a>
        </div>
        <div class="card-content">
            <div class="card-body no-print" style="overflow-y: visible">
                <div>
                    <div class="p-2">

                        <div class="table-wrapper position-relative">
                            <table class="table mb-0 table-responsive-md table-striped" id="">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        @admin
                                            <th>User Name</th>
                                        @endadmin
                                        <th>Loja/Cliente</th>
                                        <th>Carrier Tracking</th>
                                        <th>Referência do Cliente</th>
                                        <th>Tracking Code</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($orders->importOrders as $order)
                                        @include('admin.import-order.components.order-row', [
                                            'order' => $order,
                                        ])
                                    @empty
                                        <x-tables.no-record colspan="7"></x-tables.no-record>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end my-2 pb-4 mx-2">
                            {{-- {{ $orders->links() }} --}}
                        </div>
                        @include('layouts.livewire.loading')
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
