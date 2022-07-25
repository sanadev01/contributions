<div>
    <div class="col-md-12">
        <div class="col-12 pt-0 d-flex justify-content-end pr-0">

            <button class="mt-2 btn btn-primary" title="search" onclick="toggleLogsSearch()">
                <i class="fa fa-search"></i>
            </button>
        </div>
        <div class="hd-card mt-4 mb-3">
            <div class="mb-2 row col-md-12 hide"
                @if ($this->search || $this->date || $this->name || $this->model) style="display: flex !important;" @endif id="logSearch">
                <div class="col-2">
                    <label>@lang('activity.Date')</label>
                    <input type="search" class="form-control hd-search" wire:model.defer="date">
                </div>
                <div class="col-2">
                    <label>@lang('activity.Name')</label>
                    <input type="search" class="form-control hd-search" wire:model.defer="name">
                </div>
                <div class="col-2">
                    <label>@lang('activity.Model')</label>
                    <select class="form-control hd-search" wire:model.defer="model">
                        <option value="">@lang('Select Model')</option>
                        @foreach ($models as $model)
                            <option value="{{ $model }}">{{ $model }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3">
                    <label>Search</label>
                    <input type="search" class="form-control hd-search" wire:model.defer="search">
                </div>
                <div class="mt-1">
                    <button class="mt-4 pt-2 btn btn-primary" wire:click.prevent="getActivities" title="search">
                        <i class="fa fa-search"></i>
                    </button>
                    <button class="btn btn-primary ml-1 mt-4 waves-effect waves-light"
                        onclick="window.location.reload();">
                        <i class="fa fa-undo" data-bs-toggle="tooltip" title=""
                            data-bs-original-title="fa fa-undo" aria-label="fa fa-undo" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
        <div class="table-wrapper position-relative">
            <table class="table mb-0 table-responsive-md table-bordered table-striped" id="">
                <thead>
                    <tr>
                        <th>@lang('activity.Created at')</th>
                        <th>@lang('activity.Content')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $activity)
                        @include('admin.activity.components.log-row', ['activity' => $activity])
                    @empty
                        <x-tables.no-record colspan="7"></x-tables.no-record>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="row d-flex justify-content-between">
            <div class="col-1 hd-mt-1 pt-5">
                <select class="form-control hd-search" wire:model="pageSize">
                    <option value="1">1</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="300">300</option>
                </select>
            </div>
            <div class=" col-10 d-flex justify-content-end my-2 pt-5 mx-2">
                {{ $activities->links() }}
            </div>
        </div>
        @include('layouts.livewire.loading')
    </div>
</div>
