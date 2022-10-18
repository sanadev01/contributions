@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-end">
                    @section('title', __('taxservice.Manage Tax Services'))
                    @can('create', App\Models\HandlingService::class)
                        <button type="btn" onclick="toggleOrderPageSearch()" id="orderSearch"
                            class="btn btn-primary waves-effect waves-light mr-1"><i class="feather icon-search"></i></button>
                        <a href="{{ route('admin.tax.create') }}" class="btn btn-primary">
                            @lang('taxservice.Pay Tax')
                        </a>
                    @endcan
                </div></br>
                <div class="table-responsive-md mt-1 mr-4 ml-4">
                    <div class="filters p-2" id="singleSearch"
                        @if (old('search', request('search'))) style="display: block" @endif>
                        <form action="" method="GET">
                            <div class="row">
                                <div class="col-md-6 pl-2">
                                    <input type="search" class="form-control" name="search"
                                        value="{{ old('search', request('search')) }}" placeholder="@lang('taxservice.Search By Name, Warehouse No. or Tracking Code')">
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-primary btn-md">
                                        <i class="feather icon-search"></i></button>
                                    </button>
                                    <button class="btn btn-primary ml-1 waves-effect waves-light"
                                        onclick="window.location.reload();">
                                        <i class="fa fa-undo" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="fa fa-undo" aria-label="fa fa-undo"
                                            aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <table class="table mb-0 table-bordered table-responsive-sm">
                        <thead>
                            <tr>
                                <th>@lang('taxservice.User Name')</th>
                                <th>@lang('taxservice.Warehouse No.')</th>
                                <th>@lang('taxservice.Tracking Code')</th>
                                <th>@lang('taxservice.Tax Customer')</th>
                                <th>@lang('taxservice.Tax Herco')</th>
                                <th>@lang('taxservice.Receipt')</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($taxlist as $tax)
                                <tr>
                                    <td>{{ $tax->user->name }}</td>
                                    <td>
                                        <span>
                                            <a href="#" title="Click to see Shipment" data-toggle="modal"
                                                data-target="#hd-modal"
                                                data-url="{{ route('admin.modals.parcel.shipment-info', $tax->order_id) }}">
                                                WRH#: {{ $tax->order->warehouse_number }}
                                            </a>
                                        </span>
                                    </td>
                                    <td>{{ $tax->order->corrios_tracking_code }}</td>
                                    <td>{{ $tax->tax_1 }}</td>
                                    <td>{{ $tax->tax_2 }}</td>
                                    <td>
                                        @if($tax->depositAttchs)
                                        @foreach ($tax->depositAttchs as $attachedFile )
                                            <a target="_blank" href="{{ $attachedFile->getPath() }}">Download</a><br>
                                            {{-- <a target="_blank" href="{{route('admin.download_attachment', [$tax->attachment])}}">Download</a> --}}
                                        @endforeach
                                        @else
                                            Not Found
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    {{ $taxlist->links('pagination::bootstrap-4') }}
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
@section('modal')
<x-modal />
@endsection
