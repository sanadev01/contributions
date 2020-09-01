@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Roles</h4>
                        <a href="{{ route('admin.roles.create') }}" class="pull-right btn btn-primary"> Create Role </a>
                    </div>
                    <div class="card-content">
                        <div class="mt-1">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th>
                                        Name
                                    </th>
                                    <th>
                                        Action
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
                                                <a href="{{ route('admin.roles.edit',$role) }}" class="btn btn-primary mr-2" title="Edit Role">
                                                    <i class="feather icon-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.roles.permissions.index',$role) }}" class="btn btn-primary mr-2" title="Edit Permissions">
                                                    <i class="fa fa-key"></i>
                                                </a>
                                                <form action="{{ route('admin.roles.destroy',$role) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger" title="Delete Role">
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
