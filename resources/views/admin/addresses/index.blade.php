@extends('layouts.master')

@section('page')
    <section id="prealerts"> 
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('address.My Addresses') </h4>
                        @can('create', App\Models\Address::class)
                        <a href="{{ route('admin.addresses.create') }}" class="pull-right btn btn-primary"> @lang('address.Add Address') </a>
                        @endcan
                    </div>
                    <div class="card-content">
                        <div class="table-responsive-md mt-1">
                            <table class="table table-hover-animation mb-0">
                                <thead>
                                <tr>
                                    <th>
                                        @lang('address.User')
                                    </th>
                                    <th>@lang('address.Name')</th>
                                    <th>@lang('address.Address') </th>
                                    <th>@lang('address.Address')2 </th>
                                    <th>@lang('address.Country') </th>
                                    <th>@lang('address.City') </th>
                                    <th>@lang('address.State') </th>
                                    <th>@lang('address.CPF') </th>
                                    <th>@lang('address.CNPJ') </th>
                                    <th>@lang('address.Telefone') </th>
                                    <th>@lang('address.Actions') </th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($addresses as $address)
                                        <tr>
                                            <td>
                                                {{ $address->user->name .' '. $address->user->last_name }}
                                            </td>
                                            <td>{{ $address->first_name }} {{ $address->last_name }}</td>
                                            <td>{{ $address->address }}</td>
                                            <td>{{ $address->address2 }}</td>
                                            <td>
                                                {{ $address->country->name }}
                                            </td>
                                            <td class="p-1">
                                                {{ $address->city }}
                                            </td>
                                            <td>
                                                {{ $address->state->code }}
                                            </td>
                                            <td> 
                                                @if ( $address->account_type == 'individual' )
                                                    {{ $address->tax_id }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ( $address->account_type == 'business' )
                                                    {{ $address->tax_id }}
                                                @endif
                                            </td>
                                            
                                            <td>
                                                {{ $address->phone }}
                                            </td>
                                            <td class="d-flex">
                                                <a href="{{ route('admin.addresses.edit',$address->id) }}" class="btn btn-primary mr-2" title="@lang('address.Edit Address')">
                                                    <i class="feather icon-edit"></i>
                                                </a>

                                                <form action="{{ route('admin.addresses.destroy',$address->id) }}" method="post" onsubmit="return confirmDelete()">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger" title="@lang('address.Delete Address')">
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
