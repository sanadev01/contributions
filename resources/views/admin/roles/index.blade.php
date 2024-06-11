@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('role.Roles')</h4>
                        @can('create', App\Models\Role::class)
                            <a href="{{ route('admin.roles.create') }}" class="pull-right btn btn-primary"> @lang('role.Create Role') </a>
                        @endcan
                    </div>
                    <div class="card-content">
                        <div class="mt-1">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th>
                                        @lang('role.Name')
                                    </th>
                                    <th>
                                        @lang('role.Action')
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                        <tr>
                                            <td>
                                                {{ $role->name }}
                                            </td>
                                            <td class="d-flex">

                                                @can('update', $role)
                                                <a href="{{ route('admin.roles.edit',$role) }}" class="btn btn-primary mr-2" title="@lang('role.Edit Role')">
                                                    <i class="feather icon-edit"></i>
                                                </a>
                                                @endcan
                                                
                                                @can('update', $role)
                                                <a href="{{ route('admin.roles.permissions.index',$role) }}" class="btn btn-primary mr-2" title="@lang('role.Edit Permissions')">
                                                    <i class="fa fa-key"></i>
                                                </a>
                                                @endcan

                                                @can('delete', $role)
                                                <form action="{{ route('admin.roles.destroy',$role) }}" method="post" onsubmit="return confirmDelete()">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger" title="@lang('role.Delete Role')">
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
