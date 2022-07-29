@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pr-1">
                    @section('title', __('profitpackage.profit-packages'))
                    <div class="col-12 d-flex justify-content-end">
                        @can('create', App\Models\ProfitPackage::class)
                            <a href="{{ route('admin.rates.profit-packages.create') }}" class="btn mr-1 btn-primary">
                                @lang('profitpackage.create-profit-package') </a>
                        @endcan
                        @can('create', App\Models\ProfitPackage::class)
                            <a href="{{ route('admin.rates.profit-packages-upload.create') }}" class=" btn btn-info">
                                @lang('profitpackage.upload-profit-package') </a>
                        @endcan
                    </div>
                </div>
                <div class="card-content card-body">
                    <div class="mt-1 table-responsive order-table">
                        <table class="table mb-0 table-bordered">
                            <thead>
                                <tr>
                                    <th>
                                        @lang('profitpackage.name')
                                    </th>
                                    <th>
                                        @lang('profitpackage.Shipping Service')
                                    </th>
                                    <th>
                                        @lang('profitpackage.users')
                                    </th>
                                    <th>
                                        @lang('profitpackage.type')
                                    </th>
                                    <th>
                                        @lang('profitpackage.action')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($packages as $package)
                                    <tr>
                                        <td>
                                            {{ $package->name }}
                                        </td>
                                        <td>
                                            {{ optional($package->shippingService)->name }}
                                        </td>
                                        <td>
                                            <button data-toggle="modal" data-target="#hd-modal"
                                                data-url="{{ route('admin.modals.package.users', $package) }}"
                                                class="btn btn-info btn-sm">@lang('profitpackage.view users')</button>
                                        </td>
                                        <td>
                                            {{ $package->type }}
                                        </td>
                                        <td class="d-flex">
                                            <a href="{{ route('admin.rates.rates.exports', $package) }}"
                                                class="btn btn-success mr-2" title="@lang('profitpackage.download-profit-package')">
                                                <i class="feather icon-download"></i>
                                            </a>
                                            @can('update', App\Models\ProfitPackage::class)
                                                <a href="{{ route('admin.rates.profit-packages.edit', $package) }}"
                                                    class="btn btn-primary mr-2" title="@lang('profitpackage.edit-profit-package')">
                                                    <i class="feather icon-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.rates.profit-packages-upload.edit', $package) }}"
                                                    class="btn btn-primary mr-2" title="@lang('profitpackage.edit-profit-package')">
                                                    <i class="feather icon-upload"></i>
                                                </a>
                                            @endcan

                                            @can('delete', App\Models\ProfitPackage::class)
                                                <form
                                                    action="{{ route('admin.rates.profit-packages.destroy', $package) }}"
                                                    onsubmit="return confirmDelete()" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger" title="@lang('profitpackage.delete-profit-package')">
                                                        <i class="feather icon-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
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
@section('modal')
<x-modal />
@endsection
