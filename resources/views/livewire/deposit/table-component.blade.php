<div>
    <div class="row my-2 px-5">
        <div class="col-md-12 text-right">
            <strong>Statement From: </strong> {{ date('m/01/Y') }} - {{ date('m/d/Y') }} <br>
            {{-- <strong>Total Deposit:</strong> {{ 0 }} <br>
            <strong>Total Debit: </strong>  {{ 0 }} <br> --}}
            <strong>Balance: </strong> {{ getBalance() }} USD
        </div>
    </div>
    <div class="row">
        <div class="col-1 table-actions">
            <select wire:model='pageSize' class="form-control d-flex w-auto">
                <option value="10">10</option>
                <option value="30">30</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="500">500</option>
            </select>
        </div>
    </div>
    <table class="table table-hover-animation mb-0">
        <thead>
        <tr>
            <td>ID</td>
            @admin
            <th>User</th>
            @endadmin
            <th>Tracking Code</th>
            <th>WHR#</th>
            <th>Card Last 4 Digits</th>
            <th>Debit/Credit</th>
            <th>Balance</th>
            <th>Created At</th>
        </tr>
        <tr>
            <th>
                <input type="search" wire:model.debounce.500ms="uuid" class="form-control">
            </th>            
            @admin
            <th>
                <input type="search" wire:model.debounce.500ms="user" class="form-control">
            </th>
            @endadmin
            <th>
                <input type="search" wire:model.debounce.500ms="trackingCode" class="form-control">
            </th>
            <th>
                <input type="search" wire:model.debounce.500ms="warehouseNumber" class="form-control">
            </th>
            <th>
                <input type="search" wire:model.debounce.500ms="card" class="form-control">
            </th>
            <th>
                <select name="" class="form-control" wire:model="type">
                    <option value="">All</option>
                    <option value="1">Credit</option>
                    <option value="0">Debit</option>
                </select>
            </th>
        </tr>
        </thead>
        <tbody>
            @foreach($deposits as $deposit)
            <tr>
                <td>{{ $deposit->uuid }}</td>
                @admin
                <td>{{ optional($deposit->user)->name }}</td>
                @endadmin
                <td>
                    @if($deposit->hasOrder())
                        <button data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.order.invoice',$deposit->orders()->first()) }}" class="btn dropdown-item w-100" title="Show Order Details">
                            {{ $deposit->orders()->first()->corrios_tracking_code }}
                        </button>
                    @endif
                </td>
                <td>
                    @if($deposit->hasOrder())
                        <button data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.modals.order.invoice',$deposit->orders()->first()) }}" class="btn dropdown-item w-100" title="Show Order Details">
                            {{ $deposit->orders()->first()->warehouse_number }}
                        </button>
                    @endif
                </td>
                <td>
                    {{ $deposit->last_four_digits  }}
                </td>
                <th>
                    @if( $deposit->isCredit() )
                        <i class="fa fa-arrow-up text-success"></i>
                    @else
                        <i class="fa fa-arrow-down text-danger"></i>
                    @endif
                </th>
                <td>
                    {{ $deposit->balance }}
                </td>
                <td>
                    {{ optional($deposit->created_at)->format('m/d/Y') }}
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $deposits->links() }}
    @include('layouts.livewire.loading')
</div>
