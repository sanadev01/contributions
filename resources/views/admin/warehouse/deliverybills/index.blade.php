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
                            <div class="col-12 text-right">

                                <div class="row justify-content-start">
                                    <div class="col-md-1">
                                        <div class="row justify-content-start ml-3">
                                            <form action="{{ route('warehouse.combine_delivery_bill.manifest.download') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="dbills[]" id="dbills">
                                                <button type="submit" id="btn_combine" class="btn btn-sm btn-success d-none">
                                                    <i class="fa fa-download" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="row justify-content-start">
                                            <div class="col-md-3">
                                                <label>Start Date</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="date" class="form-control mb-2 mr-sm-2" value="{{ Request('startDate') }}" name="startDate">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="row justify-content-start">
                                            <div class="col-md-3">
                                                <label>End Date</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="date" class="form-control" value="{{ Request('endDate') }}"  name="endDate">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-success waves-effect waves-light" title="Search">
                                            Search <i class="fa fa-search" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <a class="btn btn-success waves-effect waves-light" href="{{url('delivery_bill')}}">
                                            back to lists
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th></th>
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
                                            <input type="checkbox" name="deliveryBills[]" class="form-control container" value="{{$deliveryBill->id}}">
                                        </td>
                                        <td>
                                            {{ $deliveryBill->name }}
                                            @if ($deliveryBill->Containers->count() > 0)
                                                @if(optional($deliveryBill->Containers->first()->orders->first())->shippingService && $deliveryBill->Containers->first()->orders->first()->shippingService->isAnjunService())
                                                    <span class="badge badge-success">A</span>
                                                @elseif($deliveryBill->isGePS())
                                                    <span class="badge badge-secondary">G</span>
                                                @elseif($deliveryBill->isSwedenPost())
                                                    <span class="badge badge-info text-white">D</span>
                                                @else
                                                    <span class="badge badge-primary">H</span>
                                                @endif
                                            @endif
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
                                                        <a href="{{ route('warehouse.delivery_bill.manifest', $deliveryBill) }}"
                                                            class="dropdown-item w-100"><i class="fa fa-cloud-download"></i> Download Manifest
                                                        <a href="{{ route('warehouse.delivery_bill.manifest',[$deliveryBill, 'service'=> true]) }}" class="dropdown-item w-100">
                                                            <i class="fa fa-cloud-download"></i> Download Manifest By Service
                                                        </a>
                                                        
                                                        <a href="{{ route('warehouse.audit-report.show',$deliveryBill) }}" class="dropdown-item w-100">
                                                            <i class="fa fa-cloud-download"></i> Audit Report
                                                        </a>

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
@push('js')
<script>
    totalChecked = 0;
    deliveryBillIds = []
    
    $("[name='deliveryBills[]']").on('change', function(){
        if($(this).is(':checked')){
            totalChecked++;
            deliveryBillIds.push($(this).val());
        }else{
            if (totalChecked > 0) {
                totalChecked--;
                deliveryBillIds.splice(deliveryBillIds.indexOf($(this).val()), 1);
            }
        }

        if (totalChecked > 0) {
            $('#btn_combine').removeClass('d-none');
            $('#dbills').val(deliveryBillIds);
        }

        if (totalChecked == 0) {
            $('#btn_combine').addClass('d-none');
        }
    });
</script>
@endpush