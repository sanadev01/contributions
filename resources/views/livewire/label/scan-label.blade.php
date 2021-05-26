<div>
    @if( $orderStatus )
        <div class="row mb-3 col-12 alert alert-danger">
            <div class="">
                {{ $orderStatus }}
            </div>
        </div>
    @endif
    <div class="col-12 row mb-5">
        <div class="form-group row col-5">
            <label class="col-2 text-right"> @lang('orders.print-label.Scan Package') </label>
            <input type="text" class="form-control col-8" wire:model.debounce.500ms="tracking">
        </div>
        <div class="col-7 d-flex justify-content-end">
            <form action="{{ route('admin.label.scan.store') }}" method="post">
                @csrf
                @foreach ($packagesRows as $key => $package)
                    <input type="hidden" name="order[]" value="{{ $package['reference'] }}">
                @endforeach
                <button type="submit" class="btn btn-success mr-2" title="@lang('orders.import-excel.Download')">
                    <i class="feather icon-download"></i> @lang('orders.import-excel.Download') All
                </button>
                
            </form>
        </div>
    </div>
    <table class="table table-bordered">
        <tr>
            <th>@lang('orders.print-label.Barcode')</th>
            <th>@lang('orders.print-label.Client')</th>
            <th>@lang('orders.print-label.Dimensions')</th>
            <th>@lang('orders.print-label.Kg')</th>
            <th>@lang('orders.print-label.Reference')#</th>
            <th>@lang('orders.print-label.Recpient')</th>
            <th>@lang('orders.print-label.Action')</th>
        </tr>
        @foreach ($packagesRows as $key => $package)
        
            <tr id="{{ $key }}">
                <td>
                    {{ $package['tracking_code'] }}
                    {{-- <input type="text" class="form-control" name="tracking[{{$key}}][tracking_code]"
                    value="{{ $package['tracking_code'] }}" wire:keydown.enter="getTrackingCode($event.target.value, {{$key}})">
                    @error("tracking.$key.tracking_code")
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                    @enderror --}}
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
                    {{ $package['recpient'] }}
                </td>
               
                <td>
                    
                    @if( !$error )
                        @if( $package['client'] )
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
        {{-- <tr>
            <td colspan="7">
                <button class="btn btn-primary" role="button" type="button" wire:click='addRow'>
                    @lang('orders.print-label.Add Row')
                </button>
            </td>
        </tr> --}}
    </table>
@include('layouts.livewire.loading')
</div>
