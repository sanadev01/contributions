@extends('admin.consolidation.wizard')

@section('wizard-css')
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
@endsection
@section('wizard-form')
    <p class="h5 dim">@lang('consolidation.Consolidation Message')</p>
    @if( $errors->count() )
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.consolidation.parcels.update',$parcel) }}" method="post" enctype="multipart/form-data" onsubmit="return validate(this);">
        @csrf
        @method('PUT')
        <div class="row justify-content-center my-3">
            <div class="col-md-12">
                <div class="grid-wrapper w-auto border" style="max-height: 80vh; overflow-y:auto;">
                    <livewire:consolidation.parcels :user-id="old('user_id',optional($parcel)->user_id)" :selected="__default( optional($parcel)->subOrders->pluck('id')->toArray(),[] )" />
                </div>
            </div>
        </div>
        @user
            
            <div class="row col-md-12">
                <div class="col-md-12">
                    <h5>@lang('consolidation.warning_notice')</h5>
                </div>
                <div class="col-md-12">
                    <p>@lang('consolidation.warning_notice_message')</p>
                    <p class="text-danger">@lang('consolidation.warning_notice_message_danger')</p>
                </div>
            </div>
            <div class="row col-md-12">
                <div class="col-md-6">
                    <input type="checkbox" required name="agree" value="1" id="agree">
                    <label for="agree">I Agree to Terms</label>
                </div>
                <div class="col-md-6">
                    <input type="checkbox" name="default" value="1" id="default">
                    <label for="default">Make Default</label>
                </div>
            </div>
        @enduser
        <div class="row justify-content-end">
            <div class="col-md-8 text-right">
                <button class="btn btn-primary btn-lg">@lang('consolidation.Save')</button>
            </div>
        </div>
    </form>
@endsection
@section('js')

    <script>
        function validate(form) {
        
        if(!valid) {
            alert('Please correct the errors in the form!');
            return false;
        }
        else {
            return confirm('Do you really want to submit the form?');
        }
        }

    </script>

@endsection
