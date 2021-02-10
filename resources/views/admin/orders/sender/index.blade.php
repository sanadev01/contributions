@extends('admin.orders.layouts.wizard')

@section('wizard-form')
<form action="{{ route('admin.orders.sender.store',$order) }}" method="POST" class="wizard">
    @csrf
    <div class="content clearfix"> 
        <!-- Step 1 -->
        <h6 id="steps-uid-0-h-0" tabindex="-1" class="title current">@lang('orders.sender.Step 1')</h6>
        <fieldset id="steps-uid-0-p-0" role="tabpanel" aria-labelledby="steps-uid-0-h-0" class="body current" aria-hidden="false">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                    <label for="firstName1">@lang('orders.sender.First Name') </label>
                        <input type="text" class="form-control" name="first_name" required value="{{ old('first_name',__default($order->sender_first_name,optional($order->user)->name)) }}" id="firstName1">
                        @error('first_name')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
    
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="lastName1">@lang('orders.sender.Last Name')</label>
                        <input type="text" class="form-control" name="last_name" value="{{ old('last_name',__default($order->sender_last_name,optional($order->user)->last_name)) }}">
                        @error('last_name')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
    
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="emailAddress1">@lang('orders.sender.Email')</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email',__default($order->sender_email,null)) }}">
                        @error('email')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="emailAddress1">@lang('orders.sender.Phone')</label>
                        <input type="text" class="form-control" name="phone" value="{{ old('phone',__default($order->sender_phone,null)) }}">
                        @error('phone')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="emailAddress1">@lang('orders.sender.Tax Id')</label>
                        <input type="text" class="form-control" name="taxt_id" value="{{ old('tax_id',__default($order->sender_taxId,null)) }}">
                        @error('taxt_id')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
    <div class="actions clearfix">
        <ul role="menu" aria-label="Pagination">
            <li class="disabled" aria-disabled="true">
                {{-- <a href="{{ route('admin.orders.packages.index') }}" role="menuitem">Previous</a> --}}
            </li>
            <li aria-hidden="false" aria-disabled="false">
                <button class="btn btn-primary">@lang('orders.sender.Next')</button>
            </li>
        </ul>
    </div>
</form>
@endsection