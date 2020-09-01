@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('address.My Addresses') </h4>
                        <a href="{{ route('admin.addresses.create') }}" class="pull-right btn btn-primary"> @lang('address.Add Address') </a>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive-md mt-1">
                            <table class="table table-hover-animation mb-0">
                                <thead>
                                <tr>
                                    {{-- @admin --}}
                                    <th>
                                        User
                                    </th>
                                    {{-- @endadmin --}}
                                    {{-- <th>
                                        @lang('address.Default')
                                    </th> --}}
                                    <th>@lang('address.First Name')</th>
                                    <th>@lang('address.Last Name')</th>
                                    <th>@lang('address.Address') </th>
                                    <th>@lang('address.Country') </th>
                                    <th>@lang('address.City') </th>
                                    <th>@lang('address.State') </th>
                                    <th>@lang('address.Tax') </th>
                                    <th>Telefone </th>
                                    <th>@lang('address.Actions') </th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($addresses as $address)
                                        <tr>
                                            {{-- @admin --}}
                                            <td>
                                                {{ $address->user->name .' '. $address->user->last_name }}
                                            </td>
                                            {{-- @endadmin --}}
                                            {{-- <td class="font-large-1">
                                                @if( $address->isDefault() )
                                                    <i class="feather icon-check text-success"></i>
                                                @else
                                                    <i class="fa fa-close text-danger"></i>
                                                @endif
                                            </td> --}}
                                            <td>{{ $address->first_name }}</td>
                                            <td>{{ $address->last_name }}</td>
                                            <td>{{ $address->address }}</td>
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
                                                @if ( !$address->isBusiness() )
                                                    {{ $address->tax_id }}
                                                @endif
                                            </td>
                                            
                                            <td>
                                                {{ $address->phone }}
                                            </td>
                                            <td class="d-flex">
                                                <a href="{{ route('admin.addresses.edit',$address->id) }}" class="btn btn-primary mr-2" title="@lang('address.Edit')">
                                                    <i class="feather icon-edit"></i>
                                                </a>
                                                {{-- @if ($address->orders->count()) --}}
                                                <form action="{{ route('admin.addresses.destroy',$address->id) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger" title="@lang('address.Delete')">
                                                        <i class="feather icon-trash"></i>
                                                    </button>
                                                </form>
                                                {{-- @endif --}}
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
