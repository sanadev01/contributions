@extends('layouts.master')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
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
                        <form action="{{ route('admin.deposit.store') }}" class="payment-form" method="POST"
                            enctype="multipart/form-data"
                            @if ($paymentGateway == 'STRIPE') data-stripe-payment="true" data-stripe-publishable-key="{{ $stripeKey }}" @else data-stripe-payment="false" @endif
                            @admin
                                onSubmit="return confirm('Are you sure! you want to add balance to User account') "
                            @endadmin>
                            @csrf
                            <div class="card-body">
                                @admin
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right" style="font-size: 18px;" for="balance">Admin Add
                                            Balance<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="Pay By HD Account">
                                                <input type="radio" name="adminpay" onclick="paybyadmin();"
                                                    value="{{ old('adminpay', 1) }}"
                                                    @if (old('adminpay') == 1) checked @endif class="col-md-1"
                                                    id="balance">
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="h3 mx-2 text-primary my-0 py-0"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right" style="font-size: 18px;" for="card">Add
                                            Balance From Card<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="Pay By Card">
                                                <input type="radio" name="adminpay" onclick="paybyadmin();"
                                                    value="{{ old('adminpay', 0) }}"
                                                    @if (old('adminpay') == 0) checked @endif required class="col-md-1"
                                                    id="card">
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="h3 mx-2 text-primary my-0 py-0"></span>
                                            </div>
                                        </div>
                                    </div>
                                @endadmin
                                <hr>
                                <div class="row justify-content-center">
                                    @admin
                                        <div class="col-md-4 balanceuser" @admin
                                                @if (old('adminpay') == 0) style="display: none" @endif
                                            @endadmin>
                                            <label>Select User</label>
                                            <livewire:components.search-user />
                                        </div>
                                        <div class="col-md-4 balanceuser" @admin
                                                @if (old('adminpay') == 0) style="display: none" @endif
                                            @endadmin>
                                            <label>Description</label>
                                            <textarea class="form-control" required name="description" placeholder="Enter Description" rows="7">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-2 balanceuser" @admin
                                                @if (old('adminpay') == 0) style="display: none" @endif
                                            @endadmin>
                                            <label>Select Operation</label>
                                            <select name="is_credit" required class="form-control">
                                                <option value="">Select Option</option>
                                                <option value="true"
                                                    @if (old('is_credit') == 'true') {{ 'selected' }} @endif>Credit Balance
                                                </option>
                                                <option value="false"
                                                    @if (old('is_credit') == 'false') {{ 'selected' }} @endif>Debit Balance
                                                </option>
                                            </select>
                                            @error('is_credit')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    @endadmin
                                    <div class="col-md-4" id="amount_div">
                                        <label>Amount</label>
                                        <input type="number" min="0" step="any" class="form-control"
                                            value="{{ old('amount') }}"   name="amount"
                                            placeholder="Enter Amount to Deposit">
                                        @error('amount')
                                            <div class="text-danger error_amount">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    @admin
                                        <div class="col-md-4 balanceuser" style="display: none;">
                                            <label>Receipt or Docs</label>
                                            <a class='btn' href='javascript:;'>
                                                <i class="fa fa-paperclip" style="font-size: 38px;"></i>
                                                <input type="file" multiple name="attachment[]"
                                                    style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;'
                                                    size="40" onchange='$("#upload-file-info").html($(this).val());'>
                                            </a>
                                            &nbsp;
                                            <span class='label label-info' id="upload-file-info"></span>
                                        </div>
                                    @endadmin
                                </div>
                                <hr>
                                <div class=" d-flex   justify-content-center"> 
                                <div class="row container text-center">
                                    <div class="col-3">
                                        <label>Auto charge Amount :</label>
                                        <input type="number" name="charge_amount" min=0 class="form-control"
                                            id="charge_amount"
                                            value="{{ old('charge_amount') ?? setting('charge_amount', null, auth()->id()) }}">
                                        @error('charge_amount')
                                            <div class="text-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>
                                    <div class="col-3">
                                        <label>When Balance less then :</label>
                                        <input type="number" name="charge_limit" min=0 class="form-control"
                                            id="charge_limit"
                                            value="{{ old('charge_limit') ?? setting('charge_limit', null, auth()->id()) }}">
                                        @error('charge_limit')
                                            <div class="text-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-3">
                                        <label> Billing information</label>
                                        <select class="form-control" name="charge_biling_information"
                                            id="charge_biling_information" required placeholder="billing info">

                                            @foreach (auth()->user()->billingInformations as $billingInfo)
                                                <option value="{{ $billingInfo->id }}"
                                                    {{ setting('charge_biling_information', null, auth()->id()) ? 'selected' : '' }}>
                                                    **** **** **** {{ substr($billingInfo->card_no, -4) }}</option>
                                            @endforeach
                                        </select>
                                        @error('charge_biling_information')
                                            <div class="text-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-3">
                                        <label> Auto Charge </label><br>
                                        <input type="hidden" name="charge" id="charge"
                                            value="{{ old('charge') ?? setting('charge', null, auth()->id()) }}">

                                        <div class="btn-group btn-toggle" id="btn-toggle">
                                            <button
                                                class="btn btn-xs  {{ old('charge') ?? setting('charge', null, auth()->id()) ? 'active btn-primary' : 'btn-default' }}"
                                                id="active" onclick="event.preventDefault();">Active</button>
                                            <button
                                                class="btn btn-xs {{ old('charge') ?? setting('charge', null, auth()->id()) ? 'btn-default' : 'active btn-danger' }} "
                                                id="inactive" onclick="event.preventDefault();">Inactive</button>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                <hr>
                                <div class="controls row mb-1 ">
                                    <div class="col-sm-12">
                                        <div class="input-group">



                                            <div class="row mt-1">
                                                <div class="form-group col-12 col-sm-6 col-md-4">
                                                    <div class="controls">

                                                        <div class="help-block"></div>
                                                    </div>
                                                </div>
                                            </div>



                                        </div>
                                    </div>
                                </div>


                                <div class="billingInfo-div" @admin
                                        @if (old('adminpay')) style="display: none" @endif
                                    @endadmin>
                                    <div class="grid-wrapper w-100">
                                        @foreach (auth()->user()->billingInformations as $billingInfo)
                                            <div class="card-wrapper h-auto my-2 w-100">
                                                <input class="c-card" type="radio" name="billingInfo"
                                                    id="{{ $billingInfo->id }}"
                                                    {{ request('billingInfo') == $billingInfo->id ? 'checked' : '' }}
                                                    value="{{ $billingInfo->id }}">
                                                <div class="card-content">
                                                    <div class="card-state-icon"></div>
                                                    <label for="{{ $billingInfo->id }}" class="w-100">
                                                        <div class="h5 py-1 px-2">
                                                            <strong
                                                                class="border-bottom-dark mr-2">@lang('invoice.Card No'):</strong>
                                                            <span class="text-info">**** **** ****
                                                                {{ substr($billingInfo->card_no, -4) }}</span>
                                                        </div>
                                                        <div class="h5 py-1 px-2">
                                                            <strong
                                                                class="border-bottom-dark mr-2">@lang('invoice.Full Name')</strong>
                                                            <span class="text-info">{{ $billingInfo->first_name }}
                                                                {{ $billingInfo->last_name }}</span>
                                                        </div>
                                                        <div class="h5 py-1 px-2">
                                                            <strong
                                                                class="border-bottom-dark mr-2">@lang('invoice.Phone')#</strong>
                                                            <span class="text-info">{{ $billingInfo->phone }}</span>
                                                        </div>
                                                        <div class="h5 py-1 px-2">
                                                            <strong
                                                                class="border-bottom-dark mr-2">@lang('invoice.Address')</strong>
                                                            <span class="text-info">{{ $billingInfo->address }}</span>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="card-wrapper h-auto my-2 w-100">
                                            <input class="c-card" type="radio" name="billingInfo" id="new"
                                                {{ request('billingInfo') == '' ? 'checked' : '' }} value="">
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
                                    <div class="billingInfo-wrapper position-relative border p-4"
                                        style="display: none;cursor: default;">
                                        <x-authorize-card billingInformationId="" :withRequiredInput="false"></x-authorize-card>
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
                                            <div class="row ml-3 mt-3" id="stripe_error" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
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
        $('input[name=billingInfo]').on('change', function() {
            if ($(this).val() == "") {
                $('.billingInfo-wrapper').fadeIn();
                $('form').removeAttr('novalidate');
            } else {
                $('.billingInfo-wrapper').fadeOut();
                $('form').attr('novalidate', 'novalidate');
            }
        })

        $(function() {
            $('input[name=billingInfo]').trigger('change');
            if ($('#balance').is(':checked')) {
                $('input[name=card_no]').attr('required', false);
                $('input[name=expiration]').attr('required', false);
                $('input[name=cvv]').attr('required', false);
                $('input[name=first_name]').attr('required', false);
                $('input[name=last_name]').attr('required', false);
                $('input[name=address]').attr('required', false);
                $('input[name=phone]').attr('required', false);
                $('select[name=country]').attr('required', false);
                $('select[name=state]').attr('required', false);
                $('input[name=zipcode]').attr('required', false);
            } else if ($('#card').is(':checked')) {
                $('input[name=user]').attr('required', false);
                $('textarea[name=description]').attr('required', false);
                $('select[name=is_credit]').attr('required', false);
                $('input[name=amount]').attr('required', false);

        }
    })
</script>

    <script>
        if ($(".error_amount")[0] || $(".help-block ")[0]) {
            $("#amount_div").removeAttr("Class");
            $("#amount_div").addClass("col-md-2");
        }

        function paybyadmin() {
            $("#amount_div").removeAttr("Class");
            if (document.getElementById('balance').checked) {
                $("#amount_div").addClass("col-md-2");
                $('.balanceuser').fadeIn();
                $('.billingInfo-div').fadeOut();
                $('form').attr('novalidate', 'novalidate');
            }

            if (document.getElementById('card').checked) {
                $("#amount_div").addClass("col-md-4");
                $('.balanceuser').fadeOut();
                $('.billingInfo-div').fadeIn();
                $('form').removeAttr('novalidate');
            }
        }


        $('#active').click(function() {
            $('#active').addClass('active btn-primary');
            $('#active').removeClass('btn-default');
            $('#inactive').addClass('btn-default');
            $('#inactive').removeClass('active btn-danger');
            $('#charge').val(1);

        });

        $('#inactive').click(function() {
            $('#active').removeClass('active btn-primary');
            $('#active').addClass('btn-default');

            $('#inactive').removeClass('btn-default');
            $('#inactive').addClass('active btn-danger');
            $('#charge').val(0);
        });
    </script>

    {{-- @include('admin.deposit.stripe') --}}
@endsection
