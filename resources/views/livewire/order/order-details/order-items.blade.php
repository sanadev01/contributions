<div class="my-5">
    <h3 class="my-3 border-bottom-light py-2">@lang('orders.order-details.Order Items')</h3>
    <div class="row my-3">
        <div class="col-12">
            {{--<button class="btn btn-success" type="button" role="button" wire:click="addItem" @if(optional($order->products)->isNotEmpty()) disabled @endif>@lang('orders.order-details.Add Item')</button> --}}
        </div>
    </div>
    @if(optional($order->items)->isNotEmpty())
    <table class="table table-bordered">
        <thead>
            <tr>
                <th> No# </th>
                <th> @lang('orders.order-details.order-item.Harmonized Code') </th>
                <th> @lang('orders.order-details.order-item.Description') </th>
                <th> @lang('orders.order-details.order-item.Quantity') </th>
                <th> @lang('orders.order-details.order-item.Unit Value') </th>
                <th> @lang('orders.order-details.order-item.Total') </th>
                <th> @lang('orders.order-details.order-item.restricted goods')</th>
                <th> Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $key => $item)            
            <tr class="{{$this->isValidShCode($item->sh_code)?'bg-danger text-white':''}}">
                <td>{{$loop->iteration}}</td>
                <td>
                <span class="badge badge-primary">{{$item->sh_code}}</span>
                    
                    <?php $sh_code = App\Models\ShCode::where('code', $item->sh_code)->first() ?>
                    @if(app()->getLocale() == 'en'){{ optional(explode('-------',optional($sh_code)->description))[0] }}@endif
                    @if(app()->getLocale() == 'pt'){{ optional(explode('-------',optional($sh_code)->description))[1] }}@endif
                    @if(app()->getLocale() == 'es'){{ optional(explode('-------',optional($sh_code)->description))[2] }}@endif
                </td>
                <td>{{ substr(optional($item)->description,0, 50) }}</td>
                <td>{{$item->quantity}}</td>
                <td>{{$item->value}}</td>
                <td>{{$item->quantity *$item->value }}</td>
                <td>
                    @if(optional($item)['contains_battery'] == 1 )
                    Yes
                    @elseif(optional($item)['contains_perfume'] == 1)
                    Yes
                    @elseif(optional($item)['contains_flammable_liquid'] == 1)
                    Yes
                    @else
                    No
                    @endif
                </td>
                <td>
                    <button class="btn btn-danger" type="button" role="button" wire:click="deleteItem({{ $item->id }})"> 
                        
            <i class="fa fa-trash"></i> @lang('orders.actions.remove-item')</button>
                    <button class="btn btn-primary" type="button" role="button" wire:click="editItem({{ $item->id }})"> <i class="fa fa-edit  "></i> @lang('orders.actions.edit-item') </button>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>
    @endif
    <livewire:order.order-details.order-item :order="$order" />
    @include('layouts.livewire.loading')
</div>
@push('lvjs-stack')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        $('body').on('change', '.items input.quantity,.items input.value', function() {
            let quantity = $(this).closest('.items').find('.quantity').val();
            let unitValue = $(this).closest('.items').find('.value').val();
            let total = parseFloat(quantity) * parseFloat(unitValue);
            $(this).closest('.items').find('.total').val(
                isNaN(total) ? 0 : (total).toFixed(2)
            );


        });
    })
</script>
@endpush