@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card min-vh-100">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('user.All Registered Users')</h4>

                        <form action="{{ route('admin.users.export.index') }}" method="POST">
                            @csrf
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <button href="" class="btn btn-primary">
                                @lang('user.Export Excel')
                            </button>
                        </form>
                    </div>
                    <div class="card-content card-body">
                        <div class="filters p-2">
                            <form action="" method="GET">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="search" class="form-control" name="search" value="{{ old('search',request('search')) }}" placeholder="@lang('user.Search By Name, Pobox, Email')">
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-primary btn-lg">
                                            @lang('user.Search')
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="table-responsive-md mt-1">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>@lang('user.Date')</th> 
                                        <th>@lang('user.POBOX')#</th>
                                        <th>@lang('user.Name')</th>
                                        <th>@lang('user.Email')</th>
                                        <th>@lang('user.Phone')</th>
                                        <th>@lang('user.Roles')</th>
                                        <th>@lang('user.Account Type')</th>
                                        <th>@lang('user.Package')</th>
                                        <th>@lang('user.Referral')</th>
                                        <th>@lang('user.Action')</th>
                                    </tr>
                                </thead>
                                <tbody>

                                @foreach($users as $user)
                                    <tr>
                                        <td>
                                            {{ $user->created_at->format('Y-m-d') }}
                                        </td>
                                        <td>
                                            {{ $user->pobox_number }}
                                        </td>
                                        <td>
                                            {{ $user->name.$user->last_name }}
                                        </td>
                                        <td>
                                            {{ $user->email }}
                                        </td>
                                        <td>
                                            {{ $user->phone }}
                                        </td>
                                        <td>
                                            {{ optional($user->role)->name }}
                                        </td>
                                        <td>
                                            {{ $user->accountType() }}
                                        </td>
                                        <td>
                                            {{ optional($user->profitPackage)->name }}
                                        </td>
                                        <td>
                                            {{ $user->come_from }}
                                        </td>
                                        <td class="d-flex">
                                            <div class="btn-group">
                                                <div class="dropdown">
                                                    <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-success dropdown-toggle waves-effect waves-light">
                                                        @lang('user.Action')
                                                    </button> 
                                                    <div class="dropdown-menu dropdown-menu-right dropright">
                                                        {{-- <a href="{{ route('admin.call-flows.edit',$user) }}" title="Edit Call Flows" class="dropdown-item w-100">
                                                            <i class="feather icon-edit"></i> Edit
                                                        </a> --}}
                                                        {{-- <a href="{{ route('admin.users.permissions.index',$user) }}" title="Edit User Permissions" class="dropdown-item w-100">
                                                            <i class="fa fa-key"></i> Roles & Permission
                                                        </a> --}}
                                                        <a href="{{ route('admin.users.setting.index',$user) }}" title="@lang('user.User Setting')" class="dropdown-item w-100">
                                                            <i class="fa fa-cog"></i> @lang('user.User Setting')
                                                        </a>

                                                        <a href="{{ route('admin.activity.log.index', [ 'id'=> $user ]) }}" title="Check User Activity" class="dropdown-item w-100">
                                                            <i class="feather icon-activity"></i> Activity Logs
                                                        </a>
                                                        @can('impersonate',App\Models\User::class)
                                                        <form action="{{ route('admin.users.login',$user) }}" class="d-flex" method="post">
                                                            @csrf
                                                            <button class="dropdown-item w-100">
                                                                <i class="feather icon-lock"></i> @lang('user.Login')
                                                            </button>
                                                        </form>
                                                        @endcan

                                                        @can('delete', $user)
                                                            <form action="{{ route('admin.users.destroy',$user) }}" class="d-flex" method="post" onsubmit="return confirmDelete()">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="dropdown-item w-100 text-danger">
                                                                    <i class="feather icon-trash-2"></i> @lang('user.Delete')
                                                                </button>
                                                            </form>
                                                        @endcan

                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end">
                        {{ $users->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
