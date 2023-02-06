<div class="card-body">
    <div class="row">
        <div class="col-md-12 text-right">
            <strong>Statement From: </strong> {{ $dateFrom }} - {{ $dateTo }} <br>
            {{-- <strong>Total Deposit:</strong> {{ 0 }} <br>
            <strong>Total Debit: </strong>  {{ 0 }} <br> --}}
            <strong>Balance: <span style="font-size: 16px;">{{  number_format($totalBalance, 2) }} USD </span></strong>
        </div>
    </div>
    <div class="row justify-content-end mb-4">
        <div class="col-md-1 table-actions mt-4">
            <select wire:model='pageSize' class="form-control d-flex w-auto">
                <option value="10">10</option>
                <option value="30">30</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="500">500</option>
            </select>
        </div>
        @if (auth()->user()->isAdmin())
        <div class="offset-4 col-md-2">
            <label>Select User</label>
            <livewire:components.search-user />
        </div>
        @endif
        <div class="col-md-2">
            <div class="row justify-content-end">
                <div class="col-md-12">
                    <label for="">Date From</label>
                    <input type="date" class="form-control"  name="date" wire:model="dateFrom">
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="row justify-content-end">
                <div class="col-md-12">
                    <label for="">Date To</label>
                    <input type="date" class="form-control" name="date" wire:model="dateTo">
                </div>
            </div>
        </div>
        <div class="col-md-1 text-right mt-4">
            <a href="{{$downloadLink}}" class="btn btn-primary">Download</a>
        </div>
    </div>
    
    <table class="table table-hover-animation mb-0">
        <thead>
        <tr>
            <th><a href="#" wire:click.prevent="sortBy('name')">User</a></th>
            <th><a href="#" wire:click.prevent="sortBy('pobox_number')">WHR#</a> </th>
            <th><a href="#" wire:click.prevent="sortBy('balance')">Balance</a></th>
        </tr>
        <tr>
            <th>
                <input type="search" wire:model.debounce.500ms="user" class="form-control">
            </th>
           
            <th>
                <input type="search" wire:model.debounce.500ms="poboxNumber" class="form-control">
            </th>
           
            <th>
                <input type="search" wire:model.debounce.500ms="balance" class="form-control">
            </th>
        </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->pobox_number }}</td>
                <td>{{ getBalance($user) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{-- {{ $deposits->links() }} --}}
    @include('layouts.livewire.loading')
</div>
