<div>
    <div class="row">
        <div class="col-12">
            <div class="card p-2"> 
                <h2 class="m-2">
                        @lang('dashboard.your-pobox')
                    </h2> 
                    <div class="row container">
                        <div class="col-xl-2  col-md-4 col-sm-6">
                            <input class="c-card" type="checkbox" wire:click="setAddress('{{ $user->id }}', 'default')" name="defaultAddress" id="defaultAddress"  @if(setting('default_address', null, $user->id)) checked @endif>
                            <strong><label>Default Address</label></strong>
                            <div class="card-content">
                                <div class="card-state-icon"></div>
                                2200 NW, 129th Ave - Suite S 100<br>
                                Miami, FL, 33132<br>
                                United States<br>
                                <span>Ph#: +13058885191</span>
                            </div>
                         </div>
                        <div class="col-xl-2  col-md-4 col-sm-6">
                            <strong><label>Parcels via UPS | FedEx | USPS sent to </label></strong>
                            <div class="card-content"> 
                                            8305 NW 116<sup>th</sup> Avenue<br>
                                            Doral , FL 33178<br>
                                            United States <br>
                                            <span>Ph#: +13058885191</span>
                            </div> 
                        </div>
                        <div class="col-xl-2  col-md-4 col-sm-6">
                               
                                <input class="c-card" type="checkbox"   @if(!$user->address) disabled @endif wire:click="setAddress('{{ $user->id }}', null)" name="userAddress" id="userAddress"  @if(setting('user_address', null, $user->id)) checked @endif>
                            <strong><label>User Address</label></strong>
                            <div class="card-content">
                                <div class="card-state-icon"></div>
                                @if($user->address)
                                    {{$user->address}}<sup>th</sup> {{$user->address2}}
                                    {{$user->street_no}}, {{$user->state->code}} {{$user->zipcode}}<br>
                                    {{$user->country->name}} <br>
                                    <span>Ph#: {{$user->phone}}</span><br>
                                    <span>{{$user->email}}</span>
                                @else
                                    <span>Address Not Found!!</span>
                                @endif
                            </div>
                        </div>
                    </div>                        
            </div>
        </div>
    </div>
</div>
@include('layouts.livewire.loading')
@section('js')
<script type="text/javascript">
    $('#defaultAddress').change(function() {
        if($(this).is(":checked")){
            $('#userAddress').prop('checked', false);
            toastr.success('Default address selected.');
        }
        else{
            toastr.error('Default address de-selected.');
        }
    });
    $('#userAddress').change(function() {
        if($(this).is(":checked")){
            $('#defaultAddress').prop('checked', false);
            toastr.success('User address selected.');
        }
        else{
            toastr.error('User address de-selected.');
        }
    });
</script>
@endsection