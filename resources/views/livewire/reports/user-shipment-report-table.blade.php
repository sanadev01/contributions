<div>
    <div class="row col-md-12 mb-2">
        <div class="row col-md-6">
            <div class="col-md-4">
                <label for="">Start Date</label>
                <input type="date" class="form-control" wire:model='start_date'>
            </div>
            <div class="col-md-4">
                <label for="">End Date</label>
                <input type="date" class="form-control" wire:model='end_date'>
            </div>
            <div class="col-md-4 mt-4">
                <a href="{{ $downloadLink }}" class="btn btn-primary" {{ !$downloadLink ? 'disabled': '' }} target="_blank">
                    Download
                </a>
            </div>
        </div>
        <div class="col-md-6">
            <form action="{{ route('admin.reports.user-shipments.index') }}" method="GET" target="_blank">
                <div class="row">
                    <div class="col-md-12 row">
        
                        <div class="col-md-4">
                            <div class="controls">
                                <label>@lang('parcel.User POBOX Number') <span class="text-danger">*</span></label>
                                <livewire:components.search-user />
                                @error('pobox_number')
                                <div class="help-block text-danger"> {{ $message }} </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="">Year</label>
                            <select class="form-control" name="year" id="DefaultSelect">
                                <option value="">Select Year </option>
                                @foreach( $years as $year )
                                <option value="{{$year}}" @if($year == $currentYear) selected @endif > {{$year}} </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mt-4">
                            <button type="submit" class="btn btn-primary"> Download Yearly</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <table class="table mb-0" id="example">
        <thead>
            <tr>
                <th>
                    
                </th>
                <th>
                    <a href="#" wire:click="sortBy('name')">
                        Name
                    </a>
                    @if ( $sortBy == 'name' && $sortAsc )
                        <i class="fa fa-arrow-down ml-2"></i>
                    @elseif( $sortBy =='name' && !$sortAsc )
                        <i class="fa fa-arrow-up ml-2"></i>
                    @endif
                </th>
                <th>
                    <a href="#" wire:click="sortBy('pobox_number')">
                        Pobox Number
                    </a>
                    @if ( $sortBy == 'pobox_number' && $sortAsc )
                        <i class="fa fa-arrow-down ml-2"></i>
                    @elseif( $sortBy =='pobox_number' && !$sortAsc )
                        <i class="fa fa-arrow-up ml-2"></i>
                    @endif
                </th>
                <th>
                    <a href="#" wire:click="sortBy('email')">
                        Email
                    </a>
                    @if ( $sortBy == 'email' && $sortAsc )
                        <i class="fa fa-arrow-down ml-2"></i>
                    @elseif( $sortBy =='email' && !$sortAsc )
                        <i class="fa fa-arrow-up ml-2"></i>
                    @endif
                </th>
                <th>
                    <a href="#" wire:click="sortBy('order_count')">
                        Shipment Count
                    </a>
                    @if ( $sortBy == 'order_count' && $sortAsc )
                        <i class="fa fa-arrow-down ml-2"></i>
                    @elseif( $sortBy =='order_count' && !$sortAsc )
                        <i class="fa fa-arrow-up ml-2"></i>
                    @endif
                </th>
                <th>
                    <a href="#" wire:click="sortBy('weight')">
                        Weight
                    </a>
                    @if ( $sortBy == 'weight' && $sortAsc )
                        <i class="fa fa-arrow-down ml-2"></i>
                    @elseif( $sortBy =='weight' && !$sortAsc )
                        <i class="fa fa-arrow-up ml-2"></i>
                    @endif
                </th>
                <th>
                    <a href="#" wire:click="sortBy('spent')">
                        Spent
                    </a>
                    @if ( $sortBy == 'spent' && $sortAsc )
                        <i class="fa fa-arrow-down ml-2"></i>
                    @elseif( $sortBy =='spent' && !$sortAsc )
                        <i class="fa fa-arrow-up ml-2"></i>
                    @endif
                </th>
            </tr>
            <tr>
                <th>
                    
                </th>
                <th>
                    <input type="search" class="form-control" wire:model.debounce.500ms="name">
                </th>
                <th>
                    <input type="search" class="form-control"  wire:model.debounce.500ms="pobox_number">
                </th>
                <th>
                    <input type="search" class="form-control"  wire:model.debounce.500ms="email">
                </th>
                <th>
                    
                </th>
                <th>
                    
                </th>
                <th>
                    
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>

                    <td class="details-control">
                        <input type="hidden" class="user_id" value="{{$user->id}}">
                    </td>
                    <td>
                        {{ $user->name }} {{ $user->last_name }}
                    </td>
                    <td>
                        {{ $user->pobox_number }} 
                    </td>
                    <td>
                        {{ $user->email }} 
                    </td>
                    <td class="h4">
                        <a href="#" title="Click to see Shipment" data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.report.shipment-user',['user'=>$user,'start_date'=>$start_date,'end_date'=>$end_date]) }}">
                            {{ number_format($user->order_count,2) }}
                        </a>
                    </td>
                    <td class="h4">
                        {{ number_format($user->weight,2) }} Kg
                    </td>
                    <td class="h4">
                        {{ number_format($user->spent,2) }} USD
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-end px-3">
        {{ $users->links() }}
    </div>
    @include('layouts.livewire.loading')
</div>
