@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Billing Informations</h4>
                        <a href="{{ route('admin.billing-information.create') }}" class="pull-right btn btn-primary"> Add Billing Information</a>
                    </div>
                    <div class="card-content">
                        <div class="mt-1">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th>
                                        Name
                                    </th>
                                    <th>Card No</th>
                                    <th>Expiration</th>
                                    <th>CVV</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>State</th>
                                    <th>Zipcode</th>
                                    <th>Country</th>
                                    <th>
                                        Action
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($billingInformation as $billingInfo)
                                        <tr>
                                            <td>
                                                {{ $billingInfo->first_name }} {{ $billingInfo->last_name }} 
                                            </td>
                                            <td>**** **** **** {{ substr ($billingInfo->card_no, -4)}}</td>
                                            <td>{{ $billingInfo->expiration }}</td>
                                            <td>***</td>
                                            <td>{{ $billingInfo->phone }}</td>
                                            <td>{{ $billingInfo->address }}</td>
                                            <td>{{ $billingInfo->state }}</td>
                                            <td>{{ $billingInfo->zipcode }}</td>
                                            <td>{{ $billingInfo->country }}</td>
                                            <td class="d-flex">
                                                <a href="{{ route('admin.billing-information.edit',$billingInfo) }}" class="btn btn-primary mr-2" title="Edit Billing Information">
                                                    <i class="feather icon-edit"></i>
                                                </a>

                                                <form action="{{ route('admin.billing-information.destroy',$billingInfo) }}"  onsubmit="return confirmDelete()" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger" title="Delete Billing Information">
                                                        <i class="feather icon-trash"></i>
                                                    </button>
                                                </form> 
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection