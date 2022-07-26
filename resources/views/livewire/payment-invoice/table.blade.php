<div>
    <div class="row">
        <div class="row col-12 pr-0 m-0 pl-0" id="datefilters">
            <div class=" col-6 text-left mb-2 pl-3">
                <div class="row col-12 pl-0" id="dateSearch">
                    <form class="col-12 pl-0" action="{{ route('admin.payment-invoices.exports') }}" method="GET"
                        target="_blank">
                        @csrf
                        <div class="form-group mb-2 col-4" style="float:left;margin-right:20px;">
                            <label>Start Date</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>
                        <div class="form-group mx-sm-3 mb-2 col-4" style="float:left;margin-right:20px;">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <button class="btn btn-success searchDateBtn waves-effect waves-light" title="Download Sales">
                            <i class="fa fa-arrow-down" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <form class="col-12 d-flex pl-0" wire:submit.prevent="render">
            <div class="mb-2 row col-md-12 pl-4 mb-1 hide"
                @if ($this->search) style="display: flex !important;" @endif id="logSearch">
                <div class="col-6 pl-2">
                    <label>Search</label>
                    <input type="search" class="form-control" wire:model.defer="search">
                </div>
                <div class="mt-1">
                    <button type="submit" class="btn btn-primary mt-4">
                        <i class="fa fa-search"></i>
                    </button>
                    <button class="btn btn-primary ml-1 mt-4 waves-effect waves-light"
                        onclick="window.location.reload();">
                        <i class="fa fa-undo" data-bs-toggle="tooltip" title=""
                            data-bs-original-title="fa fa-undo" aria-label="fa fa-undo" aria-hidden="true"></i></button>
                </div>
            </div>
        </form>
    </div>
    <table class="table table-hover-animation table-bordered mb-0">
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
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
                @include('admin.payment-invoices.components.table-row')
            @endforeach
        </tbody>
    </table>
    <div class="row d-flex justify-content-between ">
        <div class="col-1 table-actions pt-5">
            <select wire:model='pageSize' class="form-control d-flex w-auto">
                <option value="10">10</option>
                <option value="30">30</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="500">500</option>
            </select>
        </div>
        <div class="pt-5 mr-1 pr-3">
            {{ $invoices->links() }}
        </div>
    </div>
    @include('layouts.livewire.loading')
</div>
