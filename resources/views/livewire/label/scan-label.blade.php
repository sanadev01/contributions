<div>
    @if( $orderStatus )
        <div class="row mb-3 col-12 alert alert-danger">
            <div class="">
                {{ $orderStatus }}
            </div>
        </div>
    @endif
    <div class="row mb-3 col-12" id="error_message">

    </div>
    <div class="col-12 row mb-5 justify-content-between p-0 m-0"> 
            <div class="form-group row col-4 ">
                <label class="col-3 text-right"> @lang('orders.print-label.Scan Package')</label>
                <input type="text" @if (count($packagesRows) == 300) readonly @endif class="form-control col-8" wire:model.debounce.500ms="tracking">
                <span class="text-danger offset-2"> @lang('orders.print-label.Scan Package Message') {{ count($packagesRows)}} / 300</span>
            </div>
            <div class="form-group row col-4 ">
                <label class="col-3 text-right"> Ref</label>
                <input type="text" @if (count($packagesRows) == 300) readonly @endif class="form-control col-8" wire:model.debounce.500ms="customerReference">
                
            </div>
            <div class="col-4 d-flex justify-content-end float-right">
                @if(!$searchOrder)
                    <form action="{{ route('admin.label.scan.store') }}" method="post">
                        @csrf
                        @foreach ($packagesRows as $key => $package)
                            <input type="hidden" name="order[]" value="{{ $package['reference'] }}">
                            <input type="hidden" name="excel" value="1">
                        @endforeach
                        <button type="submit" class="btn btn-primary mr-2" title="@lang('orders.import-excel.Download')">
                            <i class="feather icon-download"></i> @lang('orders.import-excel.Download') Arrival Report
                        </button>
                        
                    </form>
                    @if (!auth()->user()->hasRole('driver'))
                        <form action="{{ route('admin.label.scan.store') }}" method="post">
                            @csrf
                            @foreach ($packagesRows as $key => $package)
                                <input type="hidden" name="order[]" value="{{ $package['reference'] }}">
                                <input type="hidden" name="excel" value="0">
                            @endforeach
                            <button type="submit" class="btn btn-success mr-2" title="@lang('orders.import-excel.Download')">
                                <i class="feather icon-download"></i> @lang('orders.import-excel.Download') All
                            </button>
                        
                        </form>
                    @endif
                @else
                    @if (!$searchOrder->isEmpty())
                        <br>
                        <form action="{{ route('admin.label.scan.update', 10) }}" method="post">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="start_date" value="{{ $start_date }}">
                            <input type="hidden" name="end_date" value="{{ $end_date }}">
                            <input type="hidden" name="userId" value="{{ $user_id }}">
                            <button type="submit" class="btn btn-success mr-2" title="@lang('orders.import-excel.Download')">
                                <i class="feather icon-download"></i> @lang('orders.import-excel.Download') Sacn List
                            </button>

                        </form>
                    @endif
                @endif 
            </div>
         
            @if($searchOrder)
                <div class="form-group row col-4">
                    <h4>Total Weight: <span class="text-danger">{{ number_format((float)$totalWeight, 2, '.', '') }} Kg</span></h4>
                    <h4 class="ml-2">Total Pieces: <span class="text-danger">{{ $totalPieces }}</span></h4>
                </div>
            @endif
        <div class="row col-12 d-flex justify-content-end">
            <form wire:submit.prevent="search" class="col-12 m-0 p-0">
                <div class="row col-12 p-0 m-0">
                    <div class="offset-5 col-2">
                        <div class="form-group">
                            <div class="controls">
                                <label class="d-flex">@lang('parcel.User POBOX Number')</label>
                                <livewire:components.search-user />
                                @error("start_date")
                                <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <div class="controls">
                                <label class="d-flex">Start Date</label>
                                <input class="form-control" type="date" wire:model.defer="start_date">
                                @error("start_date")
                                <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <div class="controls">
                                <label class="d-flex">End Date</label>
                                <input class="form-control" type="date" wire:model.defer="end_date">
                                @error("end_date")
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-1 d-flex justify-content-end p-0 m-0">
                        <div class="form-group  p-0 m-0">
                            <div class="controls">
                                <button type="submit" class="btn btn-primary mt-4 " wire:click="search">
                                    <i class="feather icon-search"></i>  Search
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
    </div>
    <table class="table table-bordered">
        <tr>
            <th>@lang('orders.print-label.Barcode')</th>
            <th>PO Box#</th>
            <th>@lang('orders.print-label.Driver')</th>
            <th>@lang('orders.print-label.Client')</th>
            <th>@lang('orders.print-label.Dimensions')</th>
            <th>@lang('orders.print-label.Kg')</th>
            <th>@lang('orders.print-label.Reference')#</th>
            <th>Additional Reference #</th>
            <th>@lang('Carrier Tracking')</th>
            <th>@lang('orders.print-label.Recpient')</th>
            <th>@lang('orders.print-label.Date')</th>
            <th>@lang('orders.print-label.Pickup Date')</th>
            <th>@if($searchOrder) Arrival Date @else @lang('orders.print-label.Action')@endif</th>
            @if($searchOrder)
                <th> Status </th>
            @endif
        </tr>
        @if($searchOrder)
            @foreach ($searchOrder as $package)
                <tr>
                    <td> 
                            {{ $package->corrios_tracking_code }}
                          
                                {{ $package->us_api_tracking_code }}
                            
                    </td>
                    <td>{{ $package->user->pobox_number }}</td>
                    <td>{{ optional(optional($package->driverTracking)->user)->name }}</td>
                    <td>{{ $package->merchant }}</td>
                    <td>{{ $package->length }} x {{ $package->length }} x {{ $package->height }}</td>
                    <td>{{ number_format($package->getWeight('kg'),2)}}</td>
                    <td>{{ $package->id }}</td>
                    <td>{{ $package->customer_reference }}</td>
                    <td>{{ $package->tracking_id }}</td>
                    <td>{{ $package->recipient->first_name }}</td>
                    <td>{{ $package->order_date }}</td>
                    <td>{{ optional(optional($package->driverTracking)->created_at)->format('m-d-y') }}</td>
                    <td>{{ $package->arrived_date }}</td>
                    <td>
                        @if($package->status < 80 )
                            Scanned in the warehouse
                        @endif
                        @if($package->status >= 80 )
                            Shipped
                        @endif
                    </td>
                </tr>
            @endforeach
        @else
            @foreach ($packagesRows as $key => $package)
                <tr id="{{ $key }}">
                    <td>
                        {{ $package['tracking_code'] }}
                        <hr>
                        {{ $package['us_api_tracking_code'] }}
                    </td>
                    <td>
                        {{ $package['pobox'] }}
                    </td>
                    <td>
                        {{ $package['driver'] }}
                    </td>
                    <td>
                        {{ $package['client'] }}
                    </td>
                    <td>
                        {{ $package['dimensions'] }}
                    </td>
                    <td>
                        {{ $package['kg'] }}
                    </td>
                    <td>
                        @if ($package['reference'])
                            HD-{{ $package['reference'] }}
                        @endif 
                    </td>
                    <td> 
                             {{ $package['customer_reference'] }} 
                    </td>
                    <td>
                        {{ $package['tracking_id'] }}
                    </td>
                    <td>
                        {{ $package['recpient'] }}
                    </td>
                    <td>
                        {{ $package['order_date'] }}
                    </td>
                    <td>
                        {{ $package['pickup_date'] }}
                    </td>
                    <td>
                        
                        @if( !$error && !auth()->user()->hasRole('driver'))
                            @if( $package['client'] )
                                {{-- <a href="{{route('admin.label.scan.show',$package['reference'].'?search=1')}}" class="btn btn-primary mr-2" onclick="addClass({{$key}})" title="@lang('orders.import-excel.Download')">
                                    
                                </a> --}}

                                <a href="#" title="Click to see Tracking" class="btn btn-primary mr-2" data-toggle="modal" data-target="#hd-modal" data-url="{{route('admin.label.scan.show',$package['reference'].'?search=1')}}">
                                    <i class="fa fa-search"></i>Find
                                </a>

                                <a href="{{route('admin.label.scan.show',$package['reference'])}}" target="_blank" class="btn btn-success mr-2" onclick="addClass({{$key}})" title="@lang('orders.import-excel.Download')">
                                    <i class="feather icon-download"></i>@lang('orders.import-excel.Download')
                                </a>
                            @endif
                        @endif
                        
                        <button class="btn btn-danger" role="button" tabindex="-1" type="button" wire:click='removeRow({{$key}})'>
                            @lang('orders.print-label.Remove')
                        </button>
                    </td>
                </tr>
            @endforeach
        @endif
    </table>
    
    @if (count($packagesRows) == 300)
      <!-- Modal -->
        <div class="modal fade show d-block" id="removeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" >
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h3 class="modal-title text-danger" id="exampleModalLabel"><b>STOP</b></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="removeCss()">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <i class="feather icon-x-circle text-danger display-1"> </i>
                    </div>
                    <div class="row justify-content-center">
                        <p class="h3 text-danger" style="text-align: center !important;">You have reached your labels print limit</p>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="removeCss()" data-dismiss="modal">Close</button>
                </div>
            </div>
            </div>
        </div>
    @endif

    @include('layouts.livewire.loading')
</div>
<script>
    window.addEventListener('get-error', event => {
        $('#error_message').addClass('alert alert-'+event.detail.type);
        $('#error_message').empty().append("<h4 class='text-'"+event.detail.type+">" +event.detail.message+ "</h4>"); 
    }) 
</script>