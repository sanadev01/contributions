@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Update Billing Information</h4>
                        <a href="{{ route('admin.billing-information.index') }}" class="pull-right btn btn-primary">Back to List </a>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
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
                            <form action="{{ route('admin.billing-information.update',$billingInformation->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">First Name<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                    <input type="text" class="form-control" name="first_name" value="{{$billingInformation->first_name}}" placeholder="First Name">
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Last Name<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="last_name"  value="{{$billingInformation->last_name}}" placeholder="Last Name">
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Card No<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" id="card_no_input" class="form-control" readonly  placeholder="**** **** **** {{ substr ($billingInformation->card_no, -4)}}" >
                                        <div class="help-block"></div>
                                    </div>
                                    <div class="col-md-1">
                                        <a href="#" onclick="editInput('card_no_input')" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Expiration<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" value="{{$billingInformation->expiration}}" name="expiration" > 
                                        <div class="help-block"></div>
                                    </div>
                                    
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">CVV<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" id="cvv_input" readonly class="form-control" placeholder="***">
                                        <div class="help-block"></div>
                                    </div>
                                    <div class="col-md-1">
                                        <a href="#" onclick="editInput('cvv_input')" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Phone<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="phone" value="{{$billingInformation->phone}}" placeholder="phone">
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Address<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="address" value="{{$billingInformation->address}}" placeholder="Address">
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">State<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="state" value="{{$billingInformation->state}}" placeholder="State">
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Zipcode<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="zipcode" value="{{$billingInformation->zipcode}}" placeholder="Zipcode">
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Country<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="country" value="{{$billingInformation->country}}" placeholder="Country">
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="row mt-1">
                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            Update
                                        </button>
                                        <button type="reset" class="btn btn-outline-warning waves-effect waves-light">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-payment-input-edit></x-payment-input-edit>

@endsection
