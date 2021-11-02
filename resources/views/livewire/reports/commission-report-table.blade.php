<div>
    <div>
        <div class="row">
            <div class="col-12 text-right">
                <a href="{{ $downloadLink }}" class="btn btn-primary" {{ !$downloadLink ? 'disabled': '' }} target="_blank">
                    Download
                </a>
            </div>
        </div>
        <div class="row my-3">
            <div class="col-md-4">
                <label for="">Start Date</label>
                <input type="date" class="form-control" wire:model='start_date'>
            </div>
            <div class="col-md-4">
                <label for="">End Date</label>
                <input type="date" class="form-control" wire:model='end_date'>
            </div>
        </div>
        <table class="table mb-0" id="example">
            <thead>
                <tr>
                    <th>
                        
                    </th>
                    <th>
                        <a href="#" wire:click="sortBy('name')">
                            User
                        </a>
                        @if ( $sortBy == 'name' && $sortAsc )
                            <i class="fa fa-arrow-down ml-2"></i>
                        @elseif( $sortBy =='name' && !$sortAsc )
                            <i class="fa fa-arrow-up ml-2"></i>
                        @endif
                    </th>
                    <th>
                        <a href="#" wire:click="sortBy('pobox_number')">
                            POBOX Number
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
                        <a href="#" wire:click="sortBy('sale_count')">
                            Sales Count
                        </a>
                        @if ( $sortBy == 'sale_count' && $sortAsc )
                            <i class="fa fa-arrow-down ml-2"></i>
                        @elseif( $sortBy =='sale_count' && !$sortAsc )
                            <i class="fa fa-arrow-up ml-2"></i>
                        @endif
                    </th>
                    
                    <th>
                        <a href="#" wire:click="sortBy('commission')">
                            Commission
                        </a>
                        @if ( $sortBy == 'commission' && $sortAsc )
                            <i class="fa fa-arrow-down ml-2"></i>
                        @elseif( $sortBy =='commission' && !$sortAsc )
                            <i class="fa fa-arrow-up ml-2"></i>
                        @endif
                    </th>
                    <th>
                        Action
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
                    <th>
                        
                    </th>
                </tr>
            </thead>
            <tbody>
                @if(auth()->user()->isAdmin())
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
                                {{ $user->sale_count }} 
                            </td>
                            
                            <td class="h4">
                                {{ number_format($user->commission,2) }} USD
                            </td>
                            <td class="h4">
                                <a href="{{ route('admin.reports.commission.show',$user) }}">
                                    <i class="fa fa-eye text-success"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    @foreach($users as $commission)
                        <tr>
                            <td class="details-control">
                                <input type="hidden" class="user_id" value="{{optional($commission->referrer)->id}}">
                            </td>
                            <td>
                                {{ optional($commission->referrer)->name }} {{ optional($commission->referrer)->last_name }}
                            </td>
                            <td>
                                {{ optional($commission->referrer)->pobox_number }} 
                            </td>
                            <td>
                                {{ optional($commission->referrer)->email }} 
                            </td>
                            <td class="h4">
                                {{ $commission->sale_count }} 
                            </td>
                            
                            <td class="h4">
                                {{ number_format($commission->commission,2) }} USD
                            </td>
                            <td class="h4">
                                <a href="{{ route('admin.reports.commission.show',$commission->user) }}">
                                    <i class="fa fa-eye text-success"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        <div class="d-flex justify-content-end px-3">
            {{ $users->links() }}
        </div>
        @include('layouts.livewire.loading')
    </div>
    
</div>
