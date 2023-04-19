<div>
        <div class="row">
            <div class="col-md-4">
                <div class="card-wrapper h-auto my-2 w-auto">
                    <input class="c-card" type="checkbox" wire:click="setAddress('{{ $user->id }}', 'default')" name="defaultAddress" id="defaultAddress"  @if(setting('default_address', null, $user->id)) checked @endif>
                    <strong><label>Default Address</label></strong>
                    <div class="card-content">
                        <div class="card-state-icon"></div>
                        {!! auth()->user()->getPoboxAddress() ?? '' !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-wrapper h-auto my-2 w-auto">
                    <strong><label>Parcels via UPS | FedEx | USPS</label></strong>
                    <div class="card-content">
                        <div class="card-state-icon"></div>
                        8305 NW 116<sup>th</sup> Avenue<br>
                        Doral , FL 33178<br>
                        United States <br>
                        <span>Ph#: +13058885191</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-wrapper h-auto my-2 w-auto">
                    <input class="c-card" type="checkbox" wire:click="setAddress('{{ $user->id }}', null)" name="userAddress" id="userAddress"  @if(setting('user_address', null, $user->id)) checked @endif>
                    <strong><label>User Address</label></strong>
                    <div class="card-content">
                        <div class="card-state-icon"></div>
                        @if($user->address)
                            {{$user->address}}<sup>th</sup> {{$user->address2}}
                            {{$user->street_no}}, {{$user->state->code}} {{$user->zipcode}}<br>
                            {{$user->country->name}} <br>
                            <span>Ph#: {{$user->phone}}</span>
                        @else
                            <span>Address Not Found!!</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @include('layouts.livewire.loading')
</div>
@section('js')
    <script type="text/javascript">

        $('#defaultAddress').change(function() {
            if($(this).is(":checked")){
            $('#userAddress').prop('checked', false);
            }    
        });
        $('#userAddress').change(function() {
            if($(this).is(":checked")){
            $('#defaultAddress').prop('checked', false);
            }    
        });
    </script>
@endsection
