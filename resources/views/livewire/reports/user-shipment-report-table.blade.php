<div>

    <div class="row my-3" id="dateSearch"
        @if(!empty($start_date) || !empty($end_date)) style="display:block !important" @endif>
        <div class="row col-12 pr-0">
            <div class="col-md-2">
                <label for="">Start Date</label>
                <input type="date" class="form-control" wire:model='start_date'>
            </div>
            <div class="col-md-2">
                <label for="">End Date</label>
                <input type="date" class="form-control" wire:model='end_date'>
            </div>
            <div class="col-md-1 pt-4 mt-1">
                <a href="{{ $downloadLink }}" class="btn btn-success" {{ !$downloadLink ? 'disabled' : '' }}
                    target="_blank">
                    <i class="fa fa-arrow-down"></i>
                </a>
            </div>
            <div class="col-md-7 pr-0">
                <form action="{{ route('admin.reports.user-shipments.index') }}" method="GET"
                    target="_blank">
                    <div class="row">
                        <div class="col-md-12 pr-0 d-flex justify-content-end row mb-2 ">
                            <div class="col-md-3">
                                <div class="controls">
                                    <label>@lang('parcel.User POBOX Number') <span class="text-danger">*</span></label>
                                    <livewire:components.search-user />
                                    @error('pobox_number')
                                    <div class="help-block text-danger"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="">Year</label>
                                <select class="form-control" name="year" id="DefaultSelect">
                                    <option value="">Select Year </option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}"
                                            @if ($year == $year) selected @endif>
                                            {{ $year }} </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-25">
                                <button type="submit" class="btn btn-primary btn-block mr-2">Download Yearly</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- <div class="row col-12 my-3" id="downloadsDiv">
        <form class="col-12" action="{{ route('admin.reports.user-shipments.index') }}" method="GET" target="_blank">
            <div class="row">
                <div class="col-md-12 row mb-2 ">
                    <div class="col-lg-2 pl-0 col-md-3 col-sm-3 col-xs-3">
                        <label for="">Year</label>
                        <select class="form-control" name="year" id="DefaultSelect">
                            <option value="">Select Year </option>
                            @foreach ($years as $year)
                                <option value="{{ $year }}" @if ($year == $year) selected @endif>
                                    {{ $year }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2 mt-25">
                        <button type="submit" class="btn btn-primary mr-2">Download Yearly</button>

                    </div>
                </div>
            </div>
        </form>
    </div> -->
    <div class="mb-2 row col-md-12 pl-3 mb-1 {{ !$search ? 'hide' : '' }}" id="logSearch">
        <form class="col-12 d-flex pl-0" wire:submit.prevent="render">
            <div class="col-6 pl-0">
                <label>Search</label>
                <input type="search" class="form-control" wire:model.defer="search">
            </div>
            <div class="mt-1">
                <button type="submit" class="btn btn-primary mt-4">
                    <i class="fa fa-search"></i>
                </button>
                <button class="btn btn-primary ml-1 mt-4 waves-effect waves-light" onclick="window.location.reload();">
                    <i class="fa fa-undo" data-bs-toggle="tooltip" title="" data-bs-original-title="fa fa-undo"
                        aria-label="fa fa-undo" aria-hidden="true"></i></button>
            </div>
        </form>

    </div>
    <div class="table-responsive">
    <table class="table table-bordered table-striped mb-0 row-border" id="example">
        <thead>
            <tr>
                <th>

                </th>
                <th>
                    <a href="#" wire:click="sortBy('name')">
                        Name
                    </a>
                    @if ($sortBy == 'name' && $sortAsc)
                        <i class="fa fa-arrow-down ml-2"></i>
                    @elseif($sortBy == 'name' && !$sortAsc)
                        <i class="fa fa-arrow-up ml-2"></i>
                    @endif
                </th>
                <th>
                    <a href="#" wire:click="sortBy('pobox_number')">
                        Pobox Number
                    </a>
                    @if ($sortBy == 'pobox_number' && $sortAsc)
                        <i class="fa fa-arrow-down ml-2"></i>
                    @elseif($sortBy == 'pobox_number' && !$sortAsc)
                        <i class="fa fa-arrow-up ml-2"></i>
                    @endif
                </th>
                <th>
                    <a href="#" wire:click="sortBy('email')">
                        Email
                    </a>
                    @if ($sortBy == 'email' && $sortAsc)
                        <i class="fa fa-arrow-down ml-2"></i>
                    @elseif($sortBy == 'email' && !$sortAsc)
                        <i class="fa fa-arrow-up ml-2"></i>
                    @endif
                </th>
                <th>
                    <a href="#" wire:click="sortBy('order_count')">
                        Shipment Count
                    </a>
                    @if ($sortBy == 'order_count' && $sortAsc)
                        <i class="fa fa-arrow-down ml-2"></i>
                    @elseif($sortBy == 'order_count' && !$sortAsc)
                        <i class="fa fa-arrow-up ml-2"></i>
                    @endif
                </th>
                <th>
                    <a href="#" wire:click="sortBy('weight')">
                        Weight
                    </a>
                    @if ($sortBy == 'weight' && $sortAsc)
                        <i class="fa fa-arrow-down ml-2"></i>
                    @elseif($sortBy == 'weight' && !$sortAsc)
                        <i class="fa fa-arrow-up ml-2"></i>
                    @endif
                </th>
                <th>
                    <a href="#" wire:click="sortBy('spent')">
                        Spent
                    </a>
                    @if ($sortBy == 'spent' && $sortAsc)
                        <i class="fa fa-arrow-down ml-2"></i>
                    @elseif($sortBy == 'spent' && !$sortAsc)
                        <i class="fa fa-arrow-up ml-2"></i>
                    @endif
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>

                    <td class="details-control">
                        <input type="hidden" class="user_id" value="{{ $user->id }}">
                    </td>
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
                        <a href="#" title="Click to see Shipment" data-toggle="modal" data-target="#hd-modal"
                            data-url="{{ route('admin.modals.report.shipment-user', ['user' => $user, 'start_date' => $start_date, 'end_date' => $end_date]) }}">
                            {{ number_format($user->order_count, 2) }}
                        </a>
                    </td>
                    <td class="h4">
                        {{ number_format($user->weight, 2) }} Kg
                    </td>
                    <td class="h4">
                        {{ number_format($user->spent, 2) }} USD
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    
    <div class="d-flex justify-content-end pr-0">
        {{ $users->links() }}
    </div>
    @include('layouts.livewire.loading')
</div>
