@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                @section('title', __('warehouse.deliveryBill.Delivery Bills'))
                <div class="card-header">
                    {{-- <h4 class="mb-0"> --}}
                    {{-- </h4> --}}
                    <div class="row col-6 d-flex justify-content-end pr-0">
                    </div>
                    <div class="row col-6 d-flex justify-content-end pr-3">
                        <button class="btn btn-success waves-effect waves-light" onclick="toggleLogsSearch()"
                            title="Search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                        <a href="{{ route('warehouse.delivery_bill.create') }}"
                            class="pull-right btn btn-primary ml-1">
                            @lang('warehouse.deliveryBill.Create Delivery Bill') </a>
                        <a class="btn btn-success waves-effect waves-light ml-1" href="{{ url('delivery_bill') }}">
                            back to lists
                        </a>
                    </div>
                </div>
                <div class="card-content card-body" style="min-height: 100vh;">
                    <div class="mt-1">
                        <div class="col-12 text-right">
                            <form action="">
                                <div class="row justify-content-start hide" id="logSearch">
                                    <div class="col-md-4">
                                        <div class="row justify-content-start">
                                            <div class="col-md-2 pl-0 text-left">
                                                <label>Start Date</label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="date" class="form-control col-md-12 mb-2 mr-sm-2"
                                                    value="{{ Request('startDate') }}" name="startDate">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="row justify-content-start">
                                            <div class="col-md-2 pl-0 text-left">
                                                <label>End Date</label>
                                            </div>
                                            <div class="col-md-10">
                                                <input type="date" class="form-control col-md-12"
                                                    value="{{ Request('endDate') }}" name="endDate">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pl-0">
                                        <button class="btn btn-success waves-effect waves-light" title="Search">
                                            <i class="fa fa-search" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <table class="table mb-0 table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('warehouse.deliveryBill.Name')</th>
                                    <th>Request ID</th>
                                    <th>@lang('warehouse.deliveryBill.CN38 Code')</th>
                                    <th>
                                        @lang('warehouse.deliveryBill.Dispatch Numbers')
                                    </th>
                                    <th>@lang('warehouse.actions.Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deliveryBills as $deliveryBill)
                                    <tr>
                                        <td>
                                            {{ $deliveryBill->name }}
                                        </td>
                                        <td>
                                            {{ $deliveryBill->request_id }}
                                        </td>
                                        <td>{{ $deliveryBill->cnd38_code }}</td>
                                        <td>
                                            {{ $deliveryBill->origin_country }}
                                        </td>
                                        <td class="d-flex">
                                            <div class="btn-group">
                                                <div class="dropdown">
                                                    <button type="button" data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false"
                                                        class="btn btn-success dropdown-toggle waves-effect waves-light">
                                                        @lang('user.Action')
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right dropright">
                                                        <a href="{{ route('warehouse.delivery_bill.show', $deliveryBill) }}"
                                                            class="dropdown-item w-100">
                                                            <i class="fa fa-list"></i> Show Containers
                                                        </a>
                                                        @if ($deliveryBill->isRegistered() && $deliveryBill->isReady())
                                                            <a href="{{ route('warehouse.delivery_bill.download', $deliveryBill) }}"
                                                                class="dropdown-item w-100">
                                                                <i class="fa fa-cloud-download"></i> GET CN38
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('warehouse.delivery_bill.manifest', $deliveryBill) }}"
                                                            class="dropdown-item w-100">
                                                            <i class="fa fa-cloud-download"></i> Download Manifest
                                                        </a>

                                                        <a href="{{ route('warehouse.delivery_bill.manifest', [$deliveryBill, 'service' => true]) }}"
                                                            class="dropdown-item w-100">
                                                            <i class="fa fa-cloud-download"></i> Download Manifest By
                                                            Service
                                                        </a>

                                                        <a href="{{ route('warehouse.audit-report.show', $deliveryBill) }}"
                                                            class="dropdown-item w-100">
                                                            <i class="fa fa-cloud-download"></i> Audit Report
                                                        </a>

                                                        @if ($deliveryBill->isRegistered() && !$deliveryBill->isReady())
                                                            <a href="{{ route('warehouse.delivery_bill.status.refresh', $deliveryBill) }}"
                                                                class="dropdown-item w-100">
                                                                <i class="fa fa-refresh"></i> Update Status
                                                            </a>
                                                        @endif

                                                        @if (!$deliveryBill->isRegistered())
                                                            <a href="{{ route('warehouse.delivery_bill.edit', $deliveryBill) }}"
                                                                class="dropdown-item w-100">
                                                                <i class="fa fa-edit"></i> @lang('warehouse.actions.Edit')
                                                            </a>
                                                            <a href="{{ route('warehouse.delivery_bill.register', $deliveryBill) }}"
                                                                class="dropdown-item w-100">
                                                                <i class="feather icon-box"></i> Register Delivery Bill
                                                            </a>
                                                        @endif

                                                        @if (!$deliveryBill->isReady())
                                                            <form
                                                                action="{{ route('warehouse.delivery_bill.destroy', $deliveryBill) }}"
                                                                class="d-flex" method="post"
                                                                onsubmit="return confirmDelete()">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="dropdown-item w-100 text-danger">
                                                                    <i class="feather icon-trash-2"></i>
                                                                    @lang('warehouse.actions.Delete')
                                                                </button>
                                                            </form>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end py-2 px-3">
                            {{ $deliveryBills->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
