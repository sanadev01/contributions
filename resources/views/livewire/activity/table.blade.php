<div>
    <div class="col-md-12">
        <div class="hd-card mt-4 mb-3">
            <div class="mb-2 row col-md-12 d-flex justify-content-end p-0 m-0">
                <div class="col-1 hd-mt-20 pl-0">
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
                    <label>@lang('activity.Content')</label>
                    <input type="search" class="form-control hd-search" wire:model.defer="content">
                </div>
                <div class="col-2 pt-4 ">
                    <button class="mt-2 btn btn-primary" title="search" wire:click.prevent="getActivities">
                        @lang('activity.Search') <i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="table-wrapper position-relative">
            <table class="table mb-0 table-responsive-md table-striped" id="">
                <thead>
                    <tr>
                        <th>@lang('activity.Created at')</th>
                        <th>@lang('activity.Content')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activities as $activity)
                        @include('admin.activity.components.log-row',['activity'=>$activity])
                    @empty
                        <x-tables.no-record colspan="7"></x-tables.no-record>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end my-2 pb-4 mx-2">
            {{ $activities->links() }}
        </div>
        @include('layouts.livewire.loading')
    </div>
</div>