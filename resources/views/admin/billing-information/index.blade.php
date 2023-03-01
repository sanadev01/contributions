@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card min-vh-100">
                    <div class="card-header pr-1">
                        <div class="col-12 d-flex justify-content-end">
                        @section('title', __('billing.Billing Informations'))
                        @can('create', App\Models\BillingInformation::class)
                            <a href="{{ route('admin.billing-information.create') }}" class="pull-right btn btn-primary">
                                @lang('billing.Add Billing Information')</a>
                        @endcan
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="mt-1">
                            <table class="table mb-0 table-bordered">
                                <thead>
                                    <tr>
                                        <th>
                                            @lang('billing.Name')
                                        </th>
                                        <th>@lang('billing.Card No')</th>
                                        <th>@lang('billing.Expiration')</th>
                                        <th>@lang('billing.CVV')</th>
                                        <th>@lang('billing.Phone')</th>
                                        <th>@lang('billing.Address')</th>
                                        <th>@lang('billing.State')</th>
                                        <th>@lang('billing.Zipcode')</th>
                                        <th>@lang('billing.Country')</th>
                                        <th>
                                            @lang('billing.Action')
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($billingInformation as $billingInfo)
                                        <tr>
                                            <td>
                                                {{ $billingInfo->first_name }} {{ $billingInfo->last_name }}
                                            </td>
                                            <td>**** **** **** {{ substr($billingInfo->card_no, -4) }}</td>
                                            <td>{{ $billingInfo->expiration }}</td>
                                            <td>***</td>
                                            <td>{{ $billingInfo->phone }}</td>
                                            <td>{{ $billingInfo->address }}</td>
                                            <td>{{ $billingInfo->state }}</td>
                                            <td>{{ $billingInfo->zipcode }}</td>
                                            <td>{{ $billingInfo->country }}</td>
                                            <td class="d-flex">
                                                @can('update', $billingInfo)
                                                    <a href="{{ route('admin.billing-information.edit', $billingInfo) }}"
                                                        class="btn btn-primary mr-2" title="@lang('billing.Edit Billing Information')">
                                                        <i class="feather icon-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('delete', $billingInfo)
                                                    <form
                                                        action="{{ route('admin.billing-information.destroy', $billingInfo) }}"
                                                        onsubmit="return confirmDelete()" method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger" title="@lang('billing.Delete Billing Information')">
                                                            <i class="feather icon-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{ $billingInformation->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
