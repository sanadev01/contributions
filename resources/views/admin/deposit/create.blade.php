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
                            Deposit Balance
                        </h4>
                        <a href="{{ route('admin.deposit.index') }}" class="btn btn-primary">
                            @lang('invoice.Back to List')
                        </a>
                    </div>
                    <div class="card-content">
                        <form action="{{ route('admin.deposit.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <hr>
                                <div class="row justify-content-center">
                                    <div class="col-md-4">
                                        <label>Amount</label>
                                        <input type="number" min="0" class="form-control" required name="amount" placeholder="Enter Amount to Deposit">
                                    </div>
                                </div>
                                <hr>
                                <div class="grid-wrapper w-100">
                                    @foreach (auth()->user()->billingInformations as $billingInfo)
                                        <div class="card-wrapper h-auto my-2 w-100">
                                            <input class="c-card" type="radio" name="billingInfo" id="{{$billingInfo->id}}" {{ request('billingInfo') == $billingInfo->id ? 'checked': '' }} value="{{$billingInfo->id}}">
                                            <div class="card-content">
                                                <div class="card-state-icon"></div>
                                                <label for="{{$billingInfo->id}}" class="w-100">
                                                    <div class="h5 py-1 px-2">
                                                        <strong class="border-bottom-dark mr-2">@lang('invoice.Card No'):</strong> <span class="text-info">**** **** **** {{ substr ($billingInfo->card_no, -4)}}</span>
                                                    </div>
                                                    <div class="h5 py-1 px-2">
                                                        <strong class="border-bottom-dark mr-2">@lang('invoice.Full Name')</strong> <span class="text-info">{{ $billingInfo->first_name }} {{ $billingInfo->last_name }}</span>
                                                    </div>
                                                    <div class="h5 py-1 px-2">
                                                        <strong class="border-bottom-dark mr-2">@lang('invoice.Phone')#</strong> <span class="text-info">{{ $billingInfo->phone }}</span>
                                                    </div>
                                                    <div class="h5 py-1 px-2">
                                                        <strong class="border-bottom-dark mr-2">@lang('invoice.Address')</strong> <span class="text-info">{{ $billingInfo->address }}</span>
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
                                                    <strong class="border-bottom-dark mr-2">@lang('invoice.Create New')</strong>
                                                    <p class="mt-4">@lang('invoice.Select to Add new Address You can Save address for later use.')</p>
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
                                                    <span class="">@lang('invoice.Save Billing Information')</span>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row mt-4 p-4">
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-lg btn-success" type="submit">
                                            <i class="feather icon-dollar-sign"></i> @lang('invoice.Pay')
                                        </button>
                                        <a href="{{ route('admin.deposit.index') }}" class="btn btn-lg btn-warning">
                                            <i class="feather icon-x"></i> @lang('invoice.Cancel')
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
