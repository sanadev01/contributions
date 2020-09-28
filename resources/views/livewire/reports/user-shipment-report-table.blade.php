<div>
    <div class="row">
        <div class="col-12 text-right">
            <button class="btn btn-primary" wire:click="downloadReport">
                Download
            </button>
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
    <table class="table mb-0">
        <thead>
            <tr>
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
                    <input type="search" class="form-control" wire:model.debounce.500ms="user">
                </th>
                <th>
                    <input type="search" class="form-control"  wire:model.debounce.500ms="user">
                </th>
                <th>
                    <input type="search" class="form-control"  wire:model.debounce.500ms="user">
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
                        {{ number_format($user->order_count,2) }} 
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
