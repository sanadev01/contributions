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
    <form action="{{ route('admin.consolidation.parcels.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        @admin
        <div class="row">
            <div class="col-md-4">
                <label for="">Select User</label>
                <livewire:components.search-user />
            </div>
        </div>
        @endadmin
        <div class="row justify-content-center my-3">
            <div class="col-md-12">
                <div class="grid-wrapper w-auto border" style="max-height: 80vh; overflow-y:auto;">
                    <livewire:consolidation.parcels :user-id="old('user_id')" />
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
            </div>
        @enduser
        <div class="row justify-content-end">
            <div class="col-md-8 text-right">
                <button class="btn btn-primary btn-lg">@lang('consolidation.Save')</button>
            </div>
        </div>
    </form>
@endsection
