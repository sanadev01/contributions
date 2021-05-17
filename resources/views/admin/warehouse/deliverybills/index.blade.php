@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            @lang('warehouse.deliveryBill.Delivery Bills')
                        </h4>
                        <a href="{{ route('warehouse.delivery_bill.create') }}" class="pull-right btn btn-primary"> @lang('warehouse.deliveryBill.Create Delivery Bill') </a>
                    </div>
                    <div class="card-content card-body" style="min-height: 100vh;">
                        <div class="mt-1">
                            <table class="table mb-0">
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
                                @foreach($deliveryBills as $deliveryBill)
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
                                                    <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-success dropdown-toggle waves-effect waves-light">
                                                        @lang('user.Action')
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right dropright">
                                                        <a href="{{ route('warehouse.delivery_bill.show',$deliveryBill) }}" class="dropdown-item w-100">
                                                            <i class="fa fa-list"></i> Show Containers
                                                        </a>
                                                        @if( $deliveryBill->isRegistered() && $deliveryBill->isReady())
                                                            <a href="{{ route('warehouse.delivery_bill.download',$deliveryBill) }}" class="dropdown-item w-100">
                                                                <i class="fa fa-cloud-download"></i> GET CN38
                                                            </a>
                                                        @endif

                                                        @if( $deliveryBill->isRegistered() && !$deliveryBill->isReady())
                                                            <a href="{{ route('warehouse.delivery_bill.status.refresh',$deliveryBill) }}" class="dropdown-item w-100">
                                                                <i class="fa fa-refresh"></i> Update Status
                                                            </a>
                                                        @endif

                                                        @if( !$deliveryBill->isRegistered() )
                                                            <a href="{{ route('warehouse.delivery_bill.edit',$deliveryBill) }}" class="dropdown-item w-100">
                                                                <i class="fa fa-edit"></i> @lang('warehouse.actions.Edit')
                                                            </a>
                                                            <a href="{{ route('warehouse.delivery_bill.register',$deliveryBill) }}" class="dropdown-item w-100">
                                                                <i class="feather icon-box"></i> Register Delivery Bill
                                                            </a>
                                                        @endif

                                                        @if(!$deliveryBill->isReady())
                                                            <form action="{{ route('warehouse.delivery_bill.destroy',$deliveryBill) }}" class="d-flex" method="post" onsubmit="return confirmDelete()">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="dropdown-item w-100 text-danger">
                                                                    <i class="feather icon-trash-2"></i> @lang('warehouse.actions.Delete')
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
