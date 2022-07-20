<div class="card-body">
    <div class="row">
        <div class="col-md-12">
            <div class="row my-2 pr-0">
                <div class="col-md-12 text-right">
                    <strong>Statement From: </strong> {{ $dateFrom }} - {{ $dateTo }} <br>
                    {{-- <strong>Total Deposit:</strong> {{ 0 }} <br>
                    <strong>Total Debit: </strong>  {{ 0 }} <br> --}}
                    <strong>Balance: <span style="font-size: 16px;">{{ getBalance() }} USD </span></strong>
                </div>
            </div>
            <div class="hd-card mt-1 pl-3 mb-3">
                <div class="mb-2 row col-md-12 pl-1 p-0 m-0" id="dateSearch">

                    @if (auth()->user()->isAdmin())
                        <div class=" col-2 p-0">
                            <label>Select User</label>
                            <livewire:components.search-user />
                        </div>
                    @endif
                    <div class="col-2">
                        <label for="">Date From</label>
                        <input type="date" class="form-control hd-search" name="date" wire:model="dateFrom">
                    </div>
                    <div class="col-2 pl-0">
                        <label for="">Date To</label>
                        <input type="date" class="form-control hd-search" name="date" wire:model="dateTo">
                    </div>
                    <div class="col-1 pt-1 d-flex justify-content-start p-0">
                        <a href="{{ $downloadLink }}" class="mt-4 btn btn-success"><i
                                class="feather icon-download"></i></a>
                    </div>
                </div>
            </div>
            <div class="mb-2 row col-md-12 hide"
                @if ($this->search) style="display: block !important;" @endif id="logSearch">
                <form class="col-12 d-flex pl-0" wire:submit.prevent="render">
                    <div class="col-6 pl-0">
                        <label>Search</label>
                        <input type="search" class="form-control" wire:model.defer="search">
                    </div>
                    <button type="submit" class="btn btn-primary ml-2 mt-4">
                        <i class="fa fa-search"></i>
                    </button>
                </form>
            </div>
            <div class="col-md-12 p-0">
                <table class="table table-bordered table-hover-animation mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
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
                        {{-- <tr>
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
                            <th></th>
                        </tr> --}}
                    </thead>
                    <tbody>
                        @foreach ($deposits as $deposit)
                            <tr>
                                <td>{{ $deposit->uuid }}</td>
                                @admin
                                    <td>{{ optional($deposit->user)->name }}</td>
                                @endadmin
                                <td>
                                    @if ($deposit->hasOrder() && $deposit->firstOrder()->hasSecondLabel())
                                        <a data-toggle="modal" href="javascript:void(0)" data-target="#hd-modal"
                                            data-url="{{ route('admin.modals.order.invoice', $deposit->orders()->first()) }}"
                                            class="w-100" title="Show Order Details">
                                            {{ $deposit->firstOrder()->us_api_tracking_code }}
                                        </a>
                                    @elseif($deposit->order_id && $deposit->getOrder($deposit->order_id))
                                        <a data-toggle="modal" href="javascript:void(0)" data-target="#hd-modal"
                                            data-url="{{ route('admin.modals.order.invoice', $deposit->getOrder($deposit->order_id)) }}"
                                            class="w-100" title="Show Order Details">
                                            {{ $deposit->getOrder($deposit->order_id)->corrios_tracking_code }}
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if ($deposit->order_id != null)
                                        @if ($deposit->getOrder($deposit->order_id))
                                            <a data-toggle="modal" href="javascript:void(0)" data-target="#hd-modal"
                                                data-url="{{ route('admin.modals.order.invoice', $deposit->getOrder($deposit->order_id)) }}"
                                                class="w-100" title="Show Order Details">
                                                {{ $deposit->getOrder($deposit->order_id)->warehouse_number }}
                                            </a>
                                        @else
                                            <p class="font-italic text-danger">{{ $deposit->order_id }} : Order
                                                Deleted
                                            </p>
                                        @endif
                                    @endif
                                    {{-- @if ($deposit->hasOrder())
                                    <a data-toggle="modal" href="javascript:void(0)" data-target="#hd-modal" data-url="{{ route('admin.modals.order.invoice',$deposit->orders()->first()) }}" class="w-100" title="Show Order Details">
                                        {{ $deposit->orders()->first()->warehouse_number }}
                                    </a>
                                @endif --}}
                                </td>
                                <td>
                                    {{ $deposit->last_four_digits }}
                                </td>
                                <td>
                                    @if ($deposit->depositAttchs)
                                        @foreach ($deposit->depositAttchs as $attachedFile)
                                            <a target="_blank" href="{{ $attachedFile->getPath() }}">Download</a><br>
                                            {{-- <a target="_blank" href="{{route('admin.download_attachment', [$deposit->attachment])}}">Download</a> --}}
                                        @endforeach
                                    @else
                                        Not Found
                                    @endif
                                </td>
                                <td>
                                    @if ($deposit->description != null)
                                        <button data-toggle="modal" data-target="#hd-modal"
                                            data-url="{{ route('admin.deposit.description', $deposit) }}"
                                            class="btn btn-primary">
                                            Description View
                                        </button>
                                    @endif
                                </td>
                                <th>
                                    @if ($deposit->isCredit())
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
            </div>
        </div>
    </div>
    <div class="row d-flex justify-content-between">
        <div class=" col-1 hd-mt-20 table-actions">
            <select wire:model='pageSize' class="form-control hd-search">
                <option value="10">10</option>
                <option value="30">30</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="500">500</option>
            </select>
        </div>
        <div>
            {{ $deposits->links() }}
        </div>
    </div>
    @include('layouts.livewire.loading')
</div>
