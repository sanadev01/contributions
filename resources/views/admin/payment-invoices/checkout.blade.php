@extends('layouts.master') 
@section('css')
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}" >
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/creditcard.min.css') }}">
    <link rel="stylesheet" href="{{ asset('app-assets/select/css/bootstrap-select.min.css') }}">
@endsection
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Checkout
                        </h4>
                        <a href="{{ route('admin.payment-invoices.index') }}" class="btn btn-primary">
                            Back to List
                        </a>
                    </div>
                    <div class="card-content">
                        <form action="{{ route('admin.payment-invoices.invoice.checkout.store',$invoice) }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <p class="h5 dim">Select from Saved Billing methods or create new one by clicking on create new. You can also save newly created address for future use. jus check the save address box.</p>
                                <hr>
                                <div class="grid-wrapper w-100">
                                    @foreach (auth()->user()->billingInformations as $billingInfo)
                                        <div class="card-wrapper h-auto my-2 w-100">
                                            <input class="c-card" type="radio" name="billingInfo" id="{{$billingInfo->id}}" {{ request('billingInfo') == $billingInfo->id ? 'checked': '' }} value="{{$billingInfo->id}}">
                                            <div class="card-content">
                                                <div class="card-state-icon"></div>
                                                <label for="{{$billingInfo->id}}" class="w-100">
                                                    <div class="h5 py-1 px-2">
                                                        <strong class="border-bottom-dark mr-2">Card No:</strong> <span class="text-info">**** **** **** {{ substr ($billingInfo->card_no, -4)}}</span>
                                                    </div>
                                                    <div class="h5 py-1 px-2">
                                                        <strong class="border-bottom-dark mr-2">Full Name</strong> <span class="text-info">{{ $billingInfo->first_name }} {{ $billingInfo->last_name }}</span>
                                                    </div>
                                                    <div class="h5 py-1 px-2">
                                                        <strong class="border-bottom-dark mr-2">Phone#</strong> <span class="text-info">{{ $billingInfo->phone }}</span>
                                                    </div>
                                                    <div class="h5 py-1 px-2">
                                                        <strong class="border-bottom-dark mr-2">Address</strong> <span class="text-info">{{ $billingInfo->address }}</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="card-wrapper h-auto my-2 w-100">
                                        <input class="c-card" type="radio" name="billingInfo" id="new" {{ request('billingInfo') == "" ? 'checked': '' }} value="">
                                        <div class="card-content">
                                            <div class="card-state-icon"></div>
                                            <label for="new" class="w-100">
                                                <div class="h5 py-1 px-2">
                                                    <strong class="border-bottom-dark mr-2">Create New</strong>
                                                    <p class="mt-4">Select to Add new Address You can Save address for later use.</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix mt-4"></div>
                                <div class="billingInfo-wrapper position-relative border p-4" style="display: none;cursor: default;">
                                    <x-authorize-card billingInformationId=""></x-authorize-card>
                                    <div class="row mt-4 p-4">
                                        <div class="col-md-12">
                                            <fieldset>
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" name="save-address" value="false">
                                                    <span class="vs-checkbox vs-checkbox-lg">
                                                        <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                                    <span class="">Save Billing Information</span>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row mt-4 p-4">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-lg btn-success" type="submit">
                                            <i class="feather icon-dollar-sign"></i> Pay
                                        </button>
                                        <a href="{{ route('admin.payment-invoices.index') }}" class="btn btn-lg btn-warning">
                                            <i class="feather icon-x"></i> Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script src="https://unpkg.com/imask"></script>
    <script src="{{ asset('app-assets/js/scripts/creditcard.js') }}"></script>
    <script src="{{ asset('app-assets/select/js/bootstrap-select.min.js') }}"></script>
    @include('layouts.states-ajax')

    <script>
        $('input[name=billingInfo]').on('change',function(){
            if ( $(this).val() == "" ){
                $('.billingInfo-wrapper').fadeIn();
                $('form').removeAttr('novalidate');
            }else{
                $('.billingInfo-wrapper').fadeOut();
                $('form').attr('novalidate','novalidate');
            }
        })

        $(function(){
            $('input[name=billingInfo]').trigger('change');
        })
    </script>
@endsection