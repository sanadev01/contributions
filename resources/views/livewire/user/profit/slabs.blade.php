<div>
    <table class="table table-bordered">
        <tr>
            <th>@lang('profitpackage.min-weight (grams)')</th>
            <th>@lang('profitpackage.max-weight (grams)')</th>
            <th>@lang('profitpackage.profit')</th>
            <th>Shipping</th>
            <th>@lang('profitpackage.selling')</th>
        </tr>
        @foreach ($slabs as $key => $slab)
            <livewire:user.profit.slab-field :slab="$slab" :key="$key" :index="$key" :package="$profitPackage"/>

        @endforeach
        <tr>
            <td colspan="2">
                <button class="btn btn-primary" role="button" type="button" wire:click='addSlab'>
                    @lang('profitpackage.add-slab')
                </button>
            </td>
        </tr>
    </table>
@include('layouts.livewire.loading')
</div>
