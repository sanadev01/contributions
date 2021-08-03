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
                        <form action="{{ route('admin.deposit.store') }}" method="POST" enctype="multipart/form-data" @admin onSubmit="return confirm('Are you sure! you want to add balance to User account') " @endadmin>
                            @csrf
                            <div class="card-body">
                                @admin
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right" style="font-size: 18px;" for="balance">Admin Add Balance<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="Pay By HD Account">
                                                <input type="radio" name="adminpay" onclick="paybyadmin();" value="{{ old('adminpay',1) }}" @if(old('adminpay') == 1) checked  @endif class="col-md-1" id="balance">
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
                                        <label class="col-md-3 text-md-right" style="font-size: 18px;" for="card">Add Balance From Card<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="Pay By Card">
                                                <input type="radio" name="adminpay" onclick="paybyadmin();" value="{{ old('adminpay',0) }}" @if(old('adminpay') == 0) checked  @endif required class="col-md-1" id="card">
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
                                        <div class="col-md-4 balanceuser"  @admin @if(old('adminpay') == 0) style="display: none" @endif  @endadmin>
                                            <label>Select User</label>
                                            <livewire:components.search-user />
                                        </div>
                                        <div class="col-md-4 balanceuser" @admin @if(old('adminpay') == 0) style="display: none" @endif  @endadmin>
                                            <label>Description</label>
                                            <textarea class="form-control" required name="description" placeholder="Enter Description"  rows="7"></textarea>
                                            @error('description')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    @endadmin
                                    <div class="col-md-4">
                                        <label>Amount</label>
                                        <input type="number" min="0" class="form-control" required name="amount" placeholder="Enter Amount to Deposit">
                                        @error('amount')
                                            <div class="text-danger">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    @admin
                                    <div class="col-md-4 balanceuser"  style="display: none;">
                                        {{-- <input type="file" class="form-control-file" name="attachment" style="padding: 0px !important; border: 0px !important;">
                                    </div> --}}
                                    {{-- <div style="position:relative;"> --}}
                                        <label>Receipt or Docs</label>
                                        <a class='btn' href='javascript:;'>
                                            <i class="fa fa-paperclip" style="font-size: 38px;"></i>
                                            <input type="file" style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' name="attachment" size="40"  onchange='$("#upload-file-info").html($(this).val());'>
                                        </a>
                                        &nbsp;
                                        <span class='label label-info' id="upload-file-info"></span>
                                    {{-- </div> --}}
                                    </div>
                                    @endadmin
                                </div>
                                <hr>
                                <div class="billingInfo-div" @admin @if(old('adminpay')) style="display: none" @endif @endadmin>
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

    <script>
        function paybyadmin() {
            
            if(document.getElementById('balance').checked){
                $('.balanceuser').fadeIn();
                $('.billingInfo-div').fadeOut();
                $('form').attr('novalidate','novalidate');
            }

            if (document.getElementById('card').checked) {
                $('.balanceuser').fadeOut();
                $('.billingInfo-div').fadeIn();
                $('form').removeAttr('novalidate');
            }
        }
    </script>
@endsection
