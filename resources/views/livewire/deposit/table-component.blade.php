<div class="card-body">
    <div class="row my-2 px-5">
        <div class="col-md-12 text-right">
            <strong>Statement From: </strong> {{ $dateFrom }} - {{ $dateTo }} <br>
            {{-- <strong>Total Deposit:</strong> {{ 0 }} <br>
            <strong>Total Debit: </strong>  {{ 0 }} <br> --}}
            <strong>Balance: </strong> {{ getBalance() }} USD
        </div>
    </div>
    <div class="row justify-content-end">
        <div class="col-md-4">
            <div class="row justify-content-end">
                <div class="col-md-3">
                    <label for="">Date From</label>
                </div>
                <div class="col-md-9">
                    <input type="date" class="form-control"  name="date" wire:model="dateFrom">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row justify-content-end">
                <div class="col-md-3">
                    <label for="">Date To</label>
                </div>
                <div class="col-md-9">
                    <input type="date" class="form-control" name="date" wire:model="dateTo">
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <a href="{{$downloadLink}}" class="btn btn-primary">Download</a>
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
                        <a data-toggle="modal" href="javascript:void(0)" data-target="#hd-modal" data-url="{{ route('admin.modals.order.invoice',$deposit->orders()->first()) }}" class="w-100" title="Show Order Details">
                            {{ $deposit->firstOrder()->corrios_tracking_code }}
                        </a>
                    @endif
                </td>
                <td>
                    @if($deposit->hasOrder())
                        <a data-toggle="modal" href="javascript:void(0)" data-target="#hd-modal" data-url="{{ route('admin.modals.order.invoice',$deposit->orders()->first()) }}" class="w-100" title="Show Order Details">
                            {{ $deposit->orders()->first()->warehouse_number }}
                        </a>
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
