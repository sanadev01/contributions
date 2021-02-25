<div>
    <div class="row my-2 px-5">
        <div class="col-md-12 text-right">
            <strong>Statement From: </strong> {{ date('m/01/Y') }} - {{ date('m/d/Y') }} <br>
            <strong>Total Deposit:</strong> {{ 0 }} <br>
            <strong>Total Debit: </strong>  {{ 0 }} <br>
            <strong>Balance: </strong> {{ 0 }}
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
{{--        <div class="col-11 text-right">--}}
{{--            <form action="{{ route('admin.payment-invoices.exports') }}" method="GET" target="_blank">--}}
{{--                @csrf--}}
{{--                <label>Start Date</label>--}}
{{--                <input type="date" name="start_date" class="from-control col-2">--}}

{{--                <label>End Date</label>--}}
{{--                <input type="date" name="end_date" class="from-control col-2">--}}

{{--                <button class="btn btn-success">--}}
{{--                    Download Invoice <i class="fa fa-arrow-down"></i>--}}
{{--                </button>--}}
{{--            </form>--}}
{{--        </div>--}}
    </div>
    <table class="table table-hover-animation mb-0">
        <thead>
        <tr>
            @admin
            <th>User</th>
            @endadmin
            <th>Order</th>
            <th>Card Last 4 Digits</th>
            <th>Debit/Credit</th>
            <th>Balance</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
        <tr>
            @admin
            <th>
                <input type="search" wire:model.debounce.500ms="user" class="form-control">
            </th>
            @endadmin
            <th>
                <input type="search" wire:model.debounce.500ms="order" class="form-control">
            </th>
            <th>
                <input type="search" wire:model.debounce.500ms="card" class="form-control">
            </th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
{{--        @foreach($invoices as $invoice)--}}
{{--            @include('admin.payment-invoices.components.table-row')--}}
{{--        @endforeach--}}
        </tbody>
    </table>
{{--    {{ $invoices->links() }}--}}
    @include('layouts.livewire.loading')
</div>
