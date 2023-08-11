<div class="card-body">
    <div class="row my-2 px-5">
        <div class="col-md-12 text-right">
            <strong>Statement From: </strong> {{ $dateFrom }} - {{ $dateTo }} <br>
            {{-- <strong>Total Deposit:</strong> {{ 0 }} <br>
            <strong>Total Debit: </strong>  {{ 0 }} <br> --}}
            <strong>Balance: <span style="font-size: 16px;">{{ getBalance() }} USD </span></strong>
        </div>
    </div>
    <div class="row justify-content-end mb-1">
        @if (auth()->user()->isAdmin())
        <div class="col-md-3">
            <label>Select User</label>
            <livewire:components.search-user />
        </div>
        @endif
        <div class="col-md-4">
            <div class="row justify-content-end">
                <div class="col-md-9">
                    <label for="">Date From</label>
                    <input type="date" class="form-control"  name="date" wire:model="dateFrom">
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row justify-content-end">
                <div class="col-md-9">
                    <label for="">Date To</label>
                    <input type="date" class="form-control" name="date" wire:model="dateTo">
                </div>
            </div>
        </div>
        <div class="col-md-1 mt-4">
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
            <th>Attachment</th>
            <th>Description</th>
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

            </th>
            <th>
                <input type="search" wire:model.debounce.500ms="description" class="form-control">
            </th>
            <th>
                <select name="" class="form-control" wire:model="type">
                    <option value="">All</option>
                    <option value="1">Credit</option>
                    <option value="0">Debit</option>
                </select>
            </th>
            <th>
                <input type="search" wire:model.debounce.500ms="balance" class="form-control">
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
                    
                    @if(optional($deposit->firstOrder())->hasSecondLabel())
                        <a data-toggle="modal" href="javascript:void(0)" data-target="#hd-modal" data-url="{{ route('admin.modals.order.invoice',$deposit->orders()->first()) }}" class="w-100" title="Show Order Details">
                            {{ $deposit->firstOrder()->us_api_tracking_code }}
                        </a>
                    @elseif($deposit->order_id)
                        <a data-toggle="modal" href="javascript:void(0)" data-target="#hd-modal" data-url="{{ route('admin.modals.order.invoice',$deposit->order_id) }}" class="w-100" title="Show Order Details">
                            {{ $deposit->order->corrios_tracking_code }}
                        </a>    
                    @endif
                </td>
                <td> 
                    @if($deposit->order_id)
                    <a data-toggle="modal" href="javascript:void(0)" data-target="#hd-modal" data-url="{{ route('admin.modals.order.invoice',$deposit->order_id) }}" class="w-100" title="Show Order Details">
                        {{ optional($deposit->order)->warehouse_number??"$deposit->order_id  Order Deleted "}}
                    </a>
                    @elseif
                         {{  "$deposit->order_id  Order Deleted "}} 
                    @endif
                </td>
                <td>
                    {{ $deposit->last_four_digits  }}
                </td>
                <td>
                    @if($deposit->depositAttchs)
                    @foreach ($deposit->depositAttchs as $attachedFile )
                        <a target="_blank" href="{{ $attachedFile->getPath() }}">Download</a><br>
                        {{-- <a target="_blank" href="{{route('admin.download_attachment', [$deposit->attachment])}}">Download</a> --}}
                    @endforeach
                    @else
                        Not Found
                    @endif
                </td>
                <td>
                    @if($deposit->description != null)
                    <button data-toggle="modal" data-target="#hd-modal" data-url="{{ route('admin.deposit.description',$deposit) }}" class="btn btn-primary">
                        Description View
                    </button>
                    @endif
                </td>
                <th>
                    @if( $deposit->isCredit() )
                        <i class="fa fa-arrow-up text-success"></i>
                        <br>
                         <span class="text-success">$ {{ $deposit->amount }}</span>
                    @else
                        <i class="fa fa-arrow-down text-danger"></i>
                        <br>
                         <span class="text-danger">$ {{ $deposit->amount }}</span>
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
