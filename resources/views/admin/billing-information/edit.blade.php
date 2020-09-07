@extends('layouts.master')
@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/creditcard.min.css') }}">
@endsection
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Add Billing Information</h4>
                        <a href="{{ route('admin.billing-information.index') }}" class="pull-right btn btn-primary">Back to List </a>
                    </div>
                    <hr>
                    <div class="card-content card-body pl-md-5 pr-md-5" id="credit-card">
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
                        <form class="needs-validation" novalidate='novalidate' id="payment-form" method="POST" action="{{ route('admin.billing-information.update',$billingInformation->id) }}">
                            @csrf
                            @method('PUT')

                            <x-authorize-card billingInformationId="{{$billingInformation->id}}"></x-authorize-card>

                                <div class="row mt-1">
                                <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                    <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                        Save
                                    </button>
                                    <button type="reset" class="btn btn-outline-warning waves-effect waves-light">Reset</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <x-payment-input-edit></x-payment-input-edit>

@endsection

@section('js')
    <script src="https://unpkg.com/imask"></script>
    <script src="{{ asset('app-assets/js/scripts/creditcard.js') }}"></script>
@endsection
