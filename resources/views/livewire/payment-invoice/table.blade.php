<div>
    <div class="table-actions">
        <select wire:model='pageSize' class="form-control d-flex w-auto">
            <option value="10">10</option>
            <option value="30">30</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="200">200</option>
            <option value="500">500</option>
        </select>
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
                <th></th>
                <th></th>
                <th>
                    <input type="search" wire:model.debounce.500ms="last_four_digits" class="form-control">
                </th>
                <th></th>
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
