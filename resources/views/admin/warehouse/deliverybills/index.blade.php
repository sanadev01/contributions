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
                        <div>
                            <a href="{{ route('warehouse.delivery_bill.create') }}" class="pull-right btn btn-primary"> @lang('warehouse.deliveryBill.Create Delivery Bill') </a>
                            <a class="mr-2 btn btn-success waves-effect waves-light" href="{{ route('warehouse.delivery_bill.index') }}"> Back to lists </a>
                        </div>

                    </div>
                    <div class="card-content card-body" style="min-height: 100vh;">
                        <div class="mt-1">
                            <div class="row">
                                <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-1">
                                        <div class="col-md-1">
                                            <div class="ml-3">
                                                <form action="{{ route('warehouse.combine_delivery_bill.manifest.download') }}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="dbills[]" id="dbills">
                                                    <button type="submit" id="btn_combine" class="btn btn-sm btn-success d-none">
                                                        <i class="fa fa-download" aria-hidden="true"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-11">
                                        <div class="row">
                                            <form action="" class="col-md-10">
                                                <div class="row">
                                                    <div class="col-md-1">
                                                    </div>
                                                        <div class="col-md-3">
                                                                <label>Start Date</label>
                                                                <input type="date" class="form-control mb-2 mr-sm-2" value="{{ Request('startDate') }}" name="startDate">
                                                    </div>
                                                    <div class="col-md-3"> 
                                                                <label>End Date</label>
                                                                <input type="date" class="form-control" value="{{ Request('endDate') }}"  name="endDate">
                                                    </div>
                                                    
                                                    <div class="col-md-3">
                                                                <label>Service</label>
                                                                <select class="form-control mb-2 mr-sm-2" name="type">
                                                                    <option value="">All</option>
                                                                    <option value="{{json_encode(['NX','IX'])}}">Correios Brazil</option>
                                                                    <option value="{{json_encode(['537','540'])}}">Global eParcel</option>
                                                                    <option value="{{json_encode(['773','357'])}}">Prime5</option>
                                                                    <option value="{{json_encode(['734','367','778','777'])}}">Post Plus</option>                                                           
                                                                    <option value="{{json_encode(['PostNL'])}}">Post NL</option>                                                           
                                                                    <option value="{{json_encode(['AJ-IX','AJ-NX'])}}">Anjun </option>                                                    
                                                                    <option value="{{json_encode(['BCN-IX','BCN-NX'])}}">Correios B </option>
                                                                    <option value="{{json_encode(['AJC-IX','AJC-NX'])}}">Anjun China</option>                                                                 
                                                                    <option value="{{json_encode([App\Models\ShippingService::HoundExpress])}}">Hound Express</option>
                                                                </select>
                                                    </div>
                                                    <div class="col-md-2 mt-1">
                                                        <button class="btn btn-success waves-effect waves-light mt-4" type="submit" title="Search">
                                                            Search <i class="fa fa-search" aria-hidden="true"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                            <div class="col-md-1 d-flex mt-2">
                                                <form action="{{ route('warehouse.download.create') }}" class="mt-4">
                                                    <input type="hidden" value="{{ Request('startDate') }}" name="startDate">
                                                    <input type="hidden" value="{{ Request('endDate') }}"  name="endDate">
                                                    <input type="hidden" value="{{ Request('type') }}"  name="type">
                                                    <button class="btn btn-success waves-effect waves-light" type="submit">Download</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
                                    {{-- <th>
                                        @lang('warehouse.deliveryBill.Dispatch Numbers')
                                    </th> --}}
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
                                                @elseif($deliveryBill->isPostPlus())
                                                <span class="badge badge-warning text-black">P</span>
                                                @elseif($deliveryBill->isGDE())
                                                    <span class="badge badge-secondary">GDE</span>
                                                @elseif($deliveryBill->isGSS())
                                                <span class="badge badge-secondary text-black">GSS</span>
                                                @elseif($deliveryBill->isTotalExpress())
                                                <span class="badge badge-warning text-black">T</span>
                                                @elseif($deliveryBill->isBCN())
                                                <span class="badge">B</span>
                                                @elseif($deliveryBill->isHoundExpress())
                                                <span class="badge text-dark" style="background-color:#b4e2ef">HE</span>
                                                @elseif($deliveryBill->isPasarEx())
                                                <span class="badge text-dark" style="background-color:#b4e2ef">PEx</span>
                                                @elseif($deliveryBill->isSenegal())
                                                <span class="badge badge-secondary text-dark">SN</span>
                                                @elseif($deliveryBill->isVipParcel())
                                                <span class="badge badge-secondary text-dark">VIP</span>
                                                @elseif($deliveryBill->isAnjunChina())
                                                <span class="badge badge-secondary text-dark">AJ</span>
                                                @elseif($deliveryBill->isFoxCourier())
                                                <span class="badge text-dark" style="background-color:#b4e2ef">Fox</span>
                                                @elseif($deliveryBill->isPhxCourier())
                                                <span class="badge text-dark" style="background-color:#b4e2ef">Phx</span>
                                                @elseif($deliveryBill->is_cainiao)
                                                <span class="badge badge-secondary text-danger">CIA</span>
                                                @elseif($deliveryBill->isMileExpress())
                                                <span class="badge badge-warning text-black">Mile</span>
                                                @else
                                                    <span class="badge badge-primary">{{ $deliveryBill->code_name??H }}</span>
                                                @endif
                                            @endif
                                            @if(optional($deliveryBill)->Containers->first()->isPRC())
                                                <strong class="primary">(PRC)</strong>
                                            @endif
                                            @admin @if(optional($deliveryBill->user)->isUser()) <span class="badge badge-danger">GoBox</span> @endif @endadmin
                                        </td>
                                        <td>
                                        <span class="{{ ($deliveryBill->request_id == 'waiting...' ? 'text-danger' : '') }}">
                                            {{ $deliveryBill->request_id }}
                                        </span>
                                            
                                        </td>
                                        <td>{{ $deliveryBill->cnd38_code }}</td>
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
                                                            <a href="{{ route('warehouse.download.show',$deliveryBill) }}" class="dropdown-item w-100">
                                                                <i class="fa fa-cloud-download"></i> GET CN38
                                                            </a>
                                                            <!-- @if($deliveryBill->isRegistered() && $deliveryBill->isPostPlus())
                                                                <a href="{{ route('warehouse.postplus.cn38.download',$deliveryBill) }}" class="dropdown-item w-100">
                                                                    <i class="fa fa-cloud-download"></i> GET Post Plus CN38
                                                                </a>
                                                            @endif -->
                                                        @endif
                                                        

                                                        @if(optional(optional($deliveryBill->containers->first()->orders->first())->shippingService)->is_hound_express)
                                                            <a href="{{ route('warehouse.delivery_bill.manifest', [$deliveryBill, 'service'=> 'hound_mexico']) }}"
                                                                class="dropdown-item w-100"><i class="fa fa-cloud-download"></i> Download Mexico Manifest 
                                                            </a>
                                                        @endif

                                                        @if(optional(optional(optional($deliveryBill->containers->first()->orders->first())->recipient)->country)->code=="MX" 
                                                        && optional($deliveryBill->containers->first()->orders->first())->shippingService->isSwedenPostService())
                                                        <a href="{{ route('warehouse.delivery_bill.manifest', [$deliveryBill, 'service'=> 'sweden_mexico']) }}"
                                                            class="dropdown-item w-100"><i class="fa fa-cloud-download"></i> Download Mexico Manifest 
                                                        </a>
                                                        @endif
                                                        <a href="{{ route('warehouse.delivery_bill.manifest', $deliveryBill) }}"
                                                        {{-- @dd(optional($deliveryBill->containers->first()->orders->first())->shippingService->service_sub_class) --}}
                                                        class="dropdown-item w-100"><i class="fa fa-cloud-download"></i>Download Manifest
                                                       </a>
                                                        <!-- @if($deliveryBill->isRegistered() && $deliveryBill->isPostPlus())
                                                            <a href="{{ route('warehouse.postplus.manifest.download',[$deliveryBill, 'service'=> true]) }}" class="dropdown-item w-100">
                                                                <i class="fa fa-cloud-download"></i> Download PostPlus Manifest
                                                            </a>
                                                        @endif -->
                                                        @if(!$deliveryBill->isGDE()&&!$deliveryBill->isHoundExpress()&&!$deliveryBill->isPasarEx())
                                                            <a href="{{ route('warehouse.delivery_bill.manifest',[$deliveryBill, 'service'=> true]) }}" class="dropdown-item w-100">
                                                                <i class="fa fa-cloud-download"></i> Download Manifest By Service
                                                            </a>
                                                        @endif
                                                        @if ($deliveryBill->isRegistered() && $deliveryBill->isGDE()) 
                                                           <a href="{{ route('warehouse.gde.manifest.download',[$deliveryBill,'type'=>'us-customers']) }}" class="dropdown-item w-100">
                                                                <i class="fa fa-cloud-download"></i>   Manifest to US Customs
                                                            </a>
                                                            <a href="{{ route('warehouse.gde.manifest.download',[$deliveryBill,'type'=>'brazil-customers']) }}" class="dropdown-item w-100">
                                                                <i class="fa fa-cloud-download"></i>   Manifest to Brazilian Customs 
                                                            </a>
                                                        @endif
                                                        
                                                        @if($deliveryBill->isRegistered() && $deliveryBill->isGSS())
                                                            @foreach (json_decode($deliveryBill->containers[0]->unit_response_list)->manifest->reports as $report)
                                                                <a href="{{ route('warehouse.gss_container.reports.download',[explode(',', $report)[0], json_decode($deliveryBill->containers[0]->unit_response_list)->dispatchID]) }}" class="dropdown-item w-100">
                                                                    <i class="fa fa-cloud-download"></i> {{ explode(',', $report)[0] }}
                                                                </a>
                                                            @endforeach
                                                        @endif

                                                        <a href="{{ route('warehouse.audit-report.show',$deliveryBill) }}" class="dropdown-item w-100">
                                                            <i class="fa fa-cloud-download"></i> Audit Report
                                                        </a>

                                                        @if( $deliveryBill->isRegistered() && !$deliveryBill->isReady())
                                                            <a href="{{ route('warehouse.delivery_bill.status.refresh',$deliveryBill) }}" class="dropdown-item w-100">
                                                                <i class="fa fa-refresh"></i> Update Status
                                                            </a>
                                                        @endif

                                                        @if (!$deliveryBill->isReady() && $deliveryBill->isTotalExpress()) 
                                                           <a href="{{ route('warehouse.totalexpress_manifest.createFlight',$deliveryBill) }}" type="button" class=" btn dropdown-item w-100">
                                                                <i class="fa fa-plane"></i>   Create Flight
                                                            </a>
                                                        @endif

                                                        @if (!$deliveryBill->isRegistered() && $deliveryBill->isTotalExpress()) 
                                                           <a href="javascript:void(0)" id="flightInfo" data-id="{{ $deliveryBill->id }}" type="button" class=" btn dropdown-item w-100">
                                                                <i class="fa fa-plane"></i>   Update Flight Details
                                                            </a>
                                                        @endif

                                                        @if ($deliveryBill->isReady() && $deliveryBill->isTotalExpress()) 
                                                            <a href="{{ route('warehouse.totalexpress_manifest.closeManifest',$deliveryBill) }}"  type="button" class=" btn dropdown-item w-100">
                                                                <i class="fa fa-plane"></i>   Close Manifest
                                                            </a>
                                                        @endif

                                                        @if ($deliveryBill->isRegistered() && $deliveryBill->isTotalExpress()) 
                                                            <a href="{{ route('warehouse.totalexpress_manifest.closeFlight',$deliveryBill) }}"  type="button" class=" btn dropdown-item w-100">
                                                                <i class="fa fa-plane"></i>   Close Flight
                                                            </a>
                                                        @endif
                                                        
                                                        @if( !$deliveryBill->isRegistered() )
                                                            <a href="{{ route('warehouse.delivery_bill.edit',$deliveryBill) }}" class="dropdown-item w-100">
                                                                <i class="fa fa-edit"></i> @lang('warehouse.actions.Edit')
                                                            </a>
                                                        
                                                        @endif
                                                       @if( !$deliveryBill->isRegistered() ) 
                                                            {{-- <a href="{{ route('warehouse.delivery_bill.edit',$deliveryBill) }}" class="dropdown-item w-100">
                                                                <i class="fa fa-edit"></i> @lang('warehouse.actions.Edit')
                                                            </a> --}}
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
    <!--Total Express Flight Information Modal-->
    <div class="modal fade" id="flightModal" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Add Flight Information for Total Express Manifest</b></h5>
                </div>
                <form class="form" action="{{ route('warehouse.totalexpress_manifest.addFlight') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="">
                    <div class="modal-body">
                        <div class="container-fluid"> 
                            <div class="row justify-content-center">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="departure_date">Departure Date</label>
                                        <input type="date" class="form-control" name="departure_date" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="departure_time">Departure Time</label>
                                        <input type="time" class="form-control" name="departure_time" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="airline">Airline</label>
                                        <input type="text" class="form-control" name="airline" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="departure_airport">Departure Airport</label>
                                        <input type="text" class="form-control" name="departure_airport" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="mawb_number">MAWB Number</label>
                                        <input type="text" class="form-control" name="mawb_number" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="flight_freight">Flight Freight Value</label>
                                        <input type="text" class="form-control" name="flight_freight" required>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="arrival_date">Arrival Date</label>
                                        <input type="date" class="form-control" name="arrival_date" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="arrival_time">Arrival Time</label>
                                        <input type="time" class="form-control" name="arrival_time" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="flight_number">Flight Number (Min 4 Digits)</label>
                                        <input type="text" class="form-control" name="flight_number" minlength="4" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="arrival_airport">Arrival Airport</label>
                                        <input type="text" class="form-control" name="arrival_airport" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="mawb_file">MAWB File</label>
                                        <input type="file" class="form-control-file" name="mawb_file" accept=".pdf" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
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
    $(document).ready(function(){
        $("#flightInfo").click(function(){
            $("#flightModal").modal('show');
            var deliveryBillId = $(this).data('id');
            $("#flightModal input[name='id']").val(deliveryBillId);
        });
        
    });
</script>
@endpush