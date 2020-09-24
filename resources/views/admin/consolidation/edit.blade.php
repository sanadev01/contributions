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
    <form action="{{ route('admin.consolidation.parcels.update',$parcel) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row justify-content-center my-3">
            <div class="col-md-12">
                <div class="grid-wrapper w-auto border" style="max-height: 80vh; overflow-y:auto;">
                    <livewire:consolidation.parcels :user-id="old('user_id',optional($parcel)->user_id)" :selected="__default( optional($parcel)->subOrders->pluck('id')->toArray(),[] )" />
                </div>
            </div>
        </div>
        <div class="row justify-content-end">
            <div class="col-md-8 text-right">
                <button class="btn btn-primary btn-lg">@lang('consolidation.Save')</button>
            </div>
        </div>
    </form>
@endsection
