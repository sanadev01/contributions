<div>
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
        <div class="col-11 text-right">
            <form action="{{ route('admin.payment-invoices.exports') }}" method="GET" target="_blank">
                @csrf
                <label>Start Date</label>
                <input type="date" name="start_date" class="from-control col-2">

                <label>End Date</label>
                <input type="date" name="end_date" class="from-control col-2">

                <button class="btn btn-success">
                    Download Invoice <i class="fa fa-arrow-down"></i>
                </button>
            </form>
        </div>
    </div>
    <table class="table table-hover-animation mb-0">
        <thead>
            <tr>
                <th>Invoice #</th>
                @admin
                <th>User</th>
                @endadmin
                <th>Orders Count</th>
                <th>Amount</th>
                <th>Card Last 4 Digits</th>
                <th>Status</th>
                <th>Type</th>
                <th>Created At</th>
                <th>Action</th>
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
                    <input type="text" wire:model.debounce.500ms="orderID" class="form-control">
                </th>
                <th></th>
                <th>
                    <input type="search" wire:model.debounce.500ms="last_four_digits" class="form-control">
                </th>
                <th>
                    <select class="form-control" wire:model="is_paid">
                        <option value="">All</option>
                        <option value="1">Paid</option>
                        <option value="0">Unpaid</option>
                    </select>
                </th>
                <th>
                    <select class="form-control" wire:model="type">
                        <option value="">All</option>
                        <option value="prepaid">Pre-Paid</option>
                        <option value="postpaid">Post-Paid</option>
                    </select>
                </th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                @include('admin.payment-invoices.components.table-row')
            @endforeach
        </tbody>
    </table>
    {{ $invoices->links() }}
    @include('layouts.livewire.loading')  
</div>
