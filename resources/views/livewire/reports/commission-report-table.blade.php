<div>
    <div>
        <div class="row">
            <div class="col-12 text-right mb-2">
                <button onclick="toggleLogsSearch()" class="mr-1 btn btn-primary waves-effect waves-light">
                    <i class="feather icon-search"></i>
                </button>
                <button type="btn" onclick="toggleDateSearch()" id="customSwitch8"
                    class="btn btn-primary mr-1 waves-effect waves-light"><i class="feather icon-filter"></i></button>
            </div>
        </div>
        <div class="my-3" @if( $year) style="display:flex !important" @endif id="dateSearch">
            <div class="col-md-3 pl-0">
                <select class="form-control" wire:model="year">
                    <option value="">Select Year </option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}"
                            @if ($year == $year) selected @endif>
                            {{ $year }} </option>
                    @endforeach
                </select>
            </div> 
            <div class="col-md-1">
                <a href="{{ $downloadLink }}&yearReport=1" class="btn btn-primary" {{ !$downloadLink ? 'disabled' : '' }}
                    target="_blank">
                    Download
                </a>
            </div>
        </div>
        <div class="row col-md-12 mb-2 pl-3 mb-3 @if( $start_date|| $end_date || $search) show @else hide @endif" id="logSearch">
            <form class="col-md-6 d-flex pl-0" wire:submit.prevent="render">
                <div class="col-12 pl-0">
                    <label>Search</label>
                    <input type="search" class="form-control" wire:model.debounce.500ms="search">
                </div>
                {{-- <div class="mt-1">
                    <button type="submit" class="btn btn-primary mt-4">
                        <i class="fa fa-search"></i>
                    </button>
                    <button class="btn btn-primary ml-1 mt-4 waves-effect waves-light"
                        onclick="window.location.reload();">
                        <i class="fa fa-undo" data-bs-toggle="tooltip" title=""
                            data-bs-original-title="fa fa-undo" aria-label="fa fa-undo" aria-hidden="true"></i></button>
                </div> --}}
                
            </form>
            <div class="col-md-6 row pr-0">
                <div class="col-md-4">
                    <label for="">Start Date</label>
                    <input type="date" class="form-control" wire:model.debounce.500ms='start_date'>
                </div>
                <div class="col-md-4">
                    <label for="">End Date</label>
                    <input type="date" class="form-control" wire:model.debounce.500ms='end_date'>
                </div>
                <div class="col-md-1 mt-4">
                    <a href="{{ $downloadLink }}&yearReport=0" class="btn btn-primary mt-1" {{ !$downloadLink ? 'disabled' : '' }}
                        target="_blank">
                        Download
                    </a>
                </div>
            </div>
        </div>
        <div class="table-responsive order-table">
            <table class="table mb-0 table-bordered">
                <thead>
                    <tr>
                        <th>

                        </th>
                        <th>
                            <a href="#" wire:click="sortBy('name')">
                                User
                            </a>
                            @if ($sortBy == 'name' && $sortAsc)
                                <i class="fa fa-arrow-down ml-2"></i>
                            @elseif($sortBy == 'name' && !$sortAsc)
                                <i class="fa fa-arrow-up ml-2"></i>
                            @endif
                        </th>
                        <th>
                            <a href="#" wire:click="sortBy('pobox_number')">
                                POBOX Number
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
                            <a href="#" wire:click="sortBy('sale_count')">
                                Sales Count
                            </a>
                            @if ($sortBy == 'sale_count' && $sortAsc)
                                <i class="fa fa-arrow-down ml-2"></i>
                            @elseif($sortBy == 'sale_count' && !$sortAsc)
                                <i class="fa fa-arrow-up ml-2"></i>
                            @endif
                        </th>

                        <th>
                            <a href="#" wire:click="sortBy('commission')">
                                Commission
                            </a>
                            @if ($sortBy == 'commission' && $sortAsc)
                                <i class="fa fa-arrow-down ml-2"></i>
                            @elseif($sortBy == 'commission' && !$sortAsc)
                                <i class="fa fa-arrow-up ml-2"></i>
                            @endif
                        </th>
                        <th>
                            Action
                        </th>
                    </tr>

                </thead>
                <tbody>
                    @if (\Auth::user()->isAdmin())

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
                                    {{ $user->sale_count }}
                                </td>

                                <td class="h4">
                                    {{ number_format($user->commission, 2) }} USD
                                </td>
                                <td class="h4">
                                    <a href="{{ route('admin.reports.commission.show', $user) }}">
                                        <i class="fa fa-eye text-success"></i>
                                    </a>
                                </td>
                            </tr>
                            @if($loop->last)
                            <tr>
                                <td colspan="3"></td>
                                <td ><h3>Total</h3></td>                            
                                <td class="h4">{{ number_format($users->sum('sale_count'),2) }} </td>
                                <td colspan="2" class="h4">{{ number_format($users->sum('commission'),2) }} </td>
                            </tr>
                            @endif
                        @endforeach
                    @else
                        @foreach ($users as $commission)
                            <tr>
                                <td class="details-control">
                                    <input type="hidden" class="user_id"
                                        value="{{ optional($commission->referrer)->id }}">
                                </td>
                                <td>
                                    {{ optional($commission->referrer)->name }}
                                    {{ optional($commission->referrer)->last_name }}
                                </td>
                                <td>
                                    {{ optional($commission->referrer)->pobox_number }}
                                </td>
                                <td>
                                    {{ optional($commission->referrer)->email }}
                                </td>
                                <td class="h4">
                                    {{ $commission->sale_count }}
                                </td>

                                <td class="h4">
                                    {{ number_format($commission->commission, 2) }} USD
                                </td>
                                <td class="h4">
                                    <a  class="d-flex justify-content-center" href="{{ route('admin.reports.commission.show', $commission->referrer) }}">
                                        <i class="fa fa-eye text-success"></i>
                                    </a>
                                </td>
                            </tr> 
                            @if($loop->last)
                            <tr>
                                <td colspan="3"></td>
                                <td ><h3>Total</h3></td>                            
                                <td class="h4">{{ number_format($users->sum('sale_count'),2) }} </td>
                                <td colspan="2" class="h4">{{ number_format($users->sum('commission'),2) }} </td>
                            </tr>
                        @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end pr-0">
            {{ $users->links() }}
        </div>
        <!-- @include('layouts.livewire.loading') -->
    </div>

</div>
