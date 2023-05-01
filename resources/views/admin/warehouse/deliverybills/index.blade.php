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
                        <button class="btn btn-primary waves-effect waves-light" onclick="toggleLogsSearch()"
                            title="Search">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                        <a href="{{ route('warehouse.delivery_bill.create') }}"
                            class="pull-right btn btn-success ml-1">
                            @lang('warehouse.deliveryBill.Create Delivery Bill')
                        </a>
                        <a class="btn btn-primary waves-effect waves-light ml-1" href="{{ url('delivery_bill') }}">
                            back to lists
                        </a>
                    </div>
                </div>
                <div class="card-content card-body" style="min-height: 100vh;" >
                    <div class="mt-1">
                        <div class="row text-right d-flex justify-content-center hide" id="logSearch">
                            <div class="col-10  pl-5  " >
                                
                            <form action="" >
                                <div class="row justify-content-start"   @if (Request('startDate') || Request('endDate')) style="display:flex !important" @endif >
                                    <div class="col-md-3">
                                        <div class="row justify-content-start">
                                            <div class="col-md-3 pl-0 text-left">
                                                <label>Start Date</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="date" class="form-control col-md-12 mb-2 mr-sm-2"
                                                    value="{{ Request('startDate') }}" name="startDate">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="row justify-content-start">
                                            <div class="col-md-3 pl-0 text-left">
                                                <label>End Date</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="date" class="form-control col-md-12"
                                                    value="{{ Request('endDate') }}" name="endDate">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="row justify-content-start">
                                            <div class="col-md-3">
                                                <label>Service</label>
                                            </div> 
                                            <div class="col-md-9">
                                                <select class="form-control mb-2 mr-sm-2" name="type">
                                                    <option value="">All</option>
                                                    <option value="{{json_encode(['NX','IX'])}}">Correios Brazil</option>
                                                    <option value="{{json_encode(['537','540'])}}">Global eParcel</option>
                                                    <option value="{{json_encode(['773','357'])}}">Prime5</option>
                                                    <option value="{{json_encode(['734','367','778','777'])}}">Post Plus</option>                                                           
                                                    <option value="{{json_encode(['PostNL'])}}">Post NL</option>                                                           
                                                    <option value="{{json_encode(['AJ-IX','AJ-NX'])}}">Anjun </option>                                                                    
                                                    <option value="{{json_encode(['AJC-IX','AJC-NX'])}}">Anjun China</option>                                                                     
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pl-0">
                                    <div class="col-md-12">
                                        <button class="btn btn-success waves-effect waves-light" title="Search">
                                            <i class="fa fa-search" aria-hidden="true"></i>
                                        </button>
                                        <button class="btn btn-primary ml-1 waves-effect waves-light"
                                            onclick="window.location.reload();">
                                            <i class="fa fa-undo" data-bs-toggle="tooltip" title=""
                                                data-bs-original-title="fa fa-undo" aria-label="fa fa-undo"
                                                aria-hidden="true"></i></button>
                                    </div>
                                    </div>
                                </div>
                            </form> 
                            </div>
                            <div class="col-2 d-flex justify-content-start">
                                <form action="{{ route('warehouse.download.create') }}"  >
                                    <input type="hidden" value="{{ Request('startDate') }}" name="startDate">
                                    <input type="hidden" value="{{ Request('endDate') }}"  name="endDate">
                                    <input type="hidden" value="{{ Request('type') }}"  name="type">
                                    <button class="btn btn-success waves-effect waves-light" type="submit">Download</button>
                                </form> 
                            </div>
                        </div>
                        <table class="table mb-0 table-bordered">
                            <thead>
                                <tr>
                                    {{-- <th><s/th> --}}
                                    <th>@lang('warehouse.deliveryBill.Name')</th>
                                    <th>Request ID</th>
                                    <th>@lang('warehouse.deliveryBill.CN38 Code')</th>
                                    {{-- <th>
                                        @lang('warehouse.deliveryBill.Dispatch Numbers')
                                    </th> --}}
                                    <th>@lang('warehouse.actions.Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deliveryBills as $deliveryBill)
                                    <tr>
                                        {{-- <td>
                                            <input type="checkbox" name="deliveryBills[]" class="form-control container" value="{{$deliveryBill->id}}">
                                        </td> --}}
                                        <td>
                                            {{ $deliveryBill->name }}
                                            @if ($deliveryBill->Containers->count() > 0 && optional(optional($deliveryBill->Containers->first())->orders)->count() > 0)
                                                @if( optional($deliveryBill->Containers->first()->orders->first())->shippingService && $deliveryBill->Containers->first()->orders->first()->shippingService->isAnjunService())
                                                    <span class="badge badge-success">A</span>
                                                @elseif($deliveryBill->hasMileExpressService())
                                                    <span class="badge badge-primary">M</span>
                                                @elseif($deliveryBill->isGePS())
                                                    <span class="badge badge-secondary">G</span>
                                                @elseif($deliveryBill->isSwedenPost())
                                                    <span class="badge badge-info text-white">D</span>
                                                @elseif($deliveryBill->isPostPlus())
                                                <span class="badge badge-warning text-black">P</span>
                                                @elseif($deliveryBill->hasColombiaService())
                                                    <span class="badge badge-success">C</span>
                                                @else
                                                    <span class="badge badge-primary">H</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$deliveryBill->isPostNL())
                                            {{ $deliveryBill->request_id }}
                                            @endif
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
                                                        class="btn btn-success btn-sm dropdown-toggle waves-effect waves-light"
                                                        style="width:100px;">
                                                        @lang('user.Action')
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right dropright">
                                                        <a href="{{ route('warehouse.delivery_bill.show', $deliveryBill) }}"
                                                            class="dropdown-item w-100">
                                                            <i class="fa fa-list"></i> Show Containers
                                                        </a>
                                                        @if ($deliveryBill->isRegistered() && $deliveryBill->isReady())
                                                            <a href=" {{ route('warehouse.download.show',$deliveryBill->id) }} "
                                                                class="dropdown-item w-100">
                                                                <i class="fa fa-cloud-download"></i> GET CN38
                                                            </a>
                                                            <!-- @if($deliveryBill->isRegistered() && $deliveryBill->isPostPlus())
                                                                <a href="{{ route('warehouse.postplus.cn38.download',$deliveryBill) }}" class="dropdown-item w-100">
                                                                    <i class="fa fa-cloud-download"></i> GET Post Plus CN38
                                                                </a>
                                                            @endif -->
                                                        @endif
                                                        @if($deliveryBill->isPostNL())
                                                            <a href="{{ $deliveryBill->request_id }}" target="_blank" class="dropdown-item w-100">
                                                        @else
                                                            <a href="{{ route('warehouse.delivery_bill.manifest', $deliveryBill) }}"
                                                            class="dropdown-item w-100">
                                                        @endif
                                                            <i class="fa fa-cloud-download"></i> Download Manifest
                                                        
                                                        <a href="{{ route('warehouse.delivery_bill.manifest',[$deliveryBill, 'service'=> true]) }}" class="dropdown-item w-100">
                                                            <i class="fa fa-cloud-download"></i> Download Manifest By Service
                                                        </a>
                                                        <!-- @if($deliveryBill->isRegistered() && $deliveryBill->isPostPlus())
                                                            <a href="{{ route('warehouse.postplus.manifest.download',[$deliveryBill, 'service'=> true]) }}" class="dropdown-item w-100">
                                                                <i class="fa fa-cloud-download"></i> Download PostPlus Manifest
                                                            </a>
                                                        @endif -->

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