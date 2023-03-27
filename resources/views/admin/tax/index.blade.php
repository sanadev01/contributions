@extends('layouts.master')
@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            @lang('tax.Manage Tax Services')
                        </h4>
                        @can('create', App\Models\HandlingService::class)
                        <div>
                        <a href="{{ route('admin.adjustment.create') }}" class="btn btn-success">
                            @lang('tax.Adjustment')
                        </a>
                        <a href="{{ route('admin.tax.create') }}" class="btn btn-primary">
                            @lang('tax.Pay Tax')
                        </a>
                        </div>
                        @endcan
                    </div></br>
                    <div class="table-responsive-md mt-1 mr-4 ml-4">
                        <div class="filters p-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <form action="" method="GET">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="search" class="form-control" name="search" value="{{ old('search',request('search')) }}" placeholder="@lang('tax.Search By Name, Warehouse No. or Tracking Code')">
                                            </div>
                                            <div class="col-md-4">
                                                <button class="btn btn-primary">
                                                    @lang('user.Search')
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form action="{{ route('admin.reports.tax-report') }}" method="GET">
                                        <input type="hidden" class="form-control" name="search" value="{{ old('search',request('search')) }}">
                                        <div class="row col-md-12">
                                            <div class="col-md-2 text-right">
                                                <label>Start Date</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="date" class="form-control" name="start_date" >
                                            </div>
                                            <div class="col-md-2 text-right">
                                                <label>End Date</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="date" class="form-control" name="end_date" >
                                            </div>
                                            <div class="col-md-2">
                                                <button class="btn btn-primary">
                                                    Download
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <table class="table mb-0 table-responsive-md">
                            <thead>
                                <tr>
                                    <th style="min-width: 100px;">
                                        <select name="" id="bulk-actions" class="form-control">
                                            <option value="clear">Clear All</option>
                                            <option value="checkAll">Select All</option>
                                            <option value="refund">Refund</option>
                                        </select>
                                    </th>
                                    <th>@lang('parcel.Date')</th>
                                    <th>@lang('tax.User Name')</th>
                                    <th>@lang('tax.Warehouse No.')</th>
                                    <th>@lang('tax.Tracking Code')</th>
                                    <th>@lang('tax.Tax Payment')</th> 
                                    <th>@lang('tax.Herco Buying Rate') </th>
                                    <th>@lang('tax.Herco Selling Rate') </th>
                                    <th>@lang('tax.Herco Buying USD') </th>
                                    <th>@lang('tax.Herco Selling USD')</th>
                                    <th>@lang('Profit USD')</th>
                                    <th>@lang('tax.Adjustment')</th>
                                    <th>@lang('tax.Receipt')</th>
                                    <th>@lang('tax.Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($taxes as $tax)
                                <tr>
                                    <td>                            
                                        @if(optional($tax->deposit)->last_four_digits != 'Tax refunded') 
                                                  @if($tax->adjustment==null)
                                                  <div class="vs-checkbox-con vs-checkbox-primary" title="@lang('orders.Bulk Print')">
                                                    <input type="checkbox" name="taxes[]" class="bulk-taxes" value="{{$tax->id}}">
                                                    <span class="vs-checkbox vs-checkbox-lg">
                                                        <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                                    <span class="h3 mx-2 text-primary my-0 py-0"></span>
                                                </div>
                                                  </form>
                                                  @endif
                                      @elseif(optional($tax->deposit)->last_four_digits == 'Tax refunded')
                                      {{-- <button  class="btn btn-danger mr-2">
                                        <i class="feather icon-check"></i>
                                    </button> --}}
                                      @endif
                                    </td>
                                    <td>{{ optional($tax->created_at)->format('m/d/Y') }}</td>
                                    <td>{{ $tax->user->name }}</td>
                                    <td>
                                        <span> 
                                            <a href="#" title="Click to see Shipment" data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.parcel.shipment-info',$tax->order_id) }}">
                                              {{  $tax->order?" WRH#".$tax->order->warehouse_number:""}} 
                                            </a>
                                        </span>
                                    </td>
                                    <td>{{ optional($tax->order)->corrios_tracking_code }}</td>
                                    <td>{{ $tax->tax_payment }}</td>
                                    <td>{{ $tax->buying_br }}</td>
                                    <td>{{ $tax->selling_br }}</td>
                                    <td>{{ $tax->buying_usd }}</td>
                                    <td>{{ $tax->selling_usd }}</td>
                                    <td>{{ ( $tax->selling_usd - $tax->buying_usd ) }}</td>
                                    <td>{{  $tax->adjustment }}</td>
                                    <td>
                                        @if(optional($tax->deposit)->depositAttchs)
                                            @foreach ($tax->deposit->depositAttchs as $attachedFile )
                                                <a target="_blank" href="{{ $attachedFile->getPath() }}" data-toggle="tooltip" data-placement="top" title="{{ basename($attachedFile->getPath()) }}">Download</a><br>
                                            @endforeach
                                        @else
                                            Not Found
                                        @endif
                                    </td>
                                    <td class="d-flex">
                                       
                                          @if(!$tax->is_refunded)  
                                            @if(!$tax->is_adjustment)
                                                <a href="{{  route('admin.tax.edit',$tax->id) }}" title="Edit tax" class="btn btn-primary mr-2" title="Edit">
                                                    <i class="feather icon-edit"></i>
                                                </a>
                                                <button  class="btn btn-danger mr-2"  title="Refund" onclick="return refund(['{{$tax->id}}']);">
                                                    <i class="feather icon-corner-down-left"></i>
                                                </button>
                                            @else
                                                <a href="{{  route('admin.adjustment.edit',$tax->id) }}" title="Edit adjustment" class="btn btn-primary mr-2" title="Edit">
                                                    <i class="feather icon-edit"></i>
                                                </a>
                                            @endif
                                        @elseif($tax->is_refunded)
                                        <button  class="btn btn-danger mr-2" title="Refunded">
                                            <i class="feather icon-check"></i>
                                        </button>
                                        @endif
                                        
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        {{ $taxes->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('modal')
<!--Refun Reason Modal-->
<div class="modal fade" id="refundModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><b>Add Reason for Tax Refund</b></h5>
            </div>
            <form action="{{ route('admin.refund-tax') }}" method="POST" id="admin-refund-tax" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="taxes" id="taxes" value="">
                <div class="modal-body"><br>
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="projectinput1">Give Reason for Refund</label>
                                <textarea class="form-control" name="reason", rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="projectinput1">Attach File</label>
                                <input type="file" class="form-control" name="attachment[]" multiple>
                                @error('csv_file')
                                    <div class="text-danger">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" id="proceedRefund">Refund Tax</button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>    
        </div>
    </div>
</div>
<x-modal />
@endsection
@section('js')
    <script>
        $('body').on('change','#bulk-actions',function(){
            if ( $(this).val() == 'clear' ){
                $('.bulk-taxes').prop('checked',false)
            }else if ( $(this).val() == 'checkAll' ){
                $('.bulk-taxes').prop('checked',true)
            }else if ( $(this).val() == 'refund' ){
                var taxesIds = [];
                $.each($(".bulk-taxes:checked"), function(){
                    taxesIds.push($(this).val());
                }); 
                refund(taxesIds)
            }
        })
        function refund(taxesIds){             
                $('#refundModal').modal('toggle');
                $('#admin-refund-tax #taxes').val(JSON.stringify(taxesIds));
        }
      
    </script>
@endsection