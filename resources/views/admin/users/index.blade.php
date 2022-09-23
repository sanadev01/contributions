@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card min-vh-100">
                    <div class="card-header pr-3">
                        <div class="col-12 d-flex justify-content-end pr-0">
                            <div class="col-2 text-right p-0 pr-1" style="float: right">
                                <form action="{{ route('admin.users.export.index') }}" method="POST">
                                    @csrf
                                    <input type="hidden" wire:model.debounce.500ms="search" name="search"
                                        value="{{ request('search') }}">
                                    <button href="" class="mt-1 btn btn-success">
                                        <i class="feather icon-download"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="p-0 pr-1">
                                <button onclick="toggleUserSearch()" class="mt-1 btn btn-primary">
                                    <i class="feather icon-search"></i>
                                </button>
                            </div>
                        </div>
                    @section('title', __('user.All Registered Users'))
                </div>
                <div class="card-content card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="hd-card mb-3">
                                <div class="d-flex">
                                    <div class="row col-12">
                                        <div class="col-10">
                                            <form action="" method="GET">
                                                <div class="row">
                                                    <div class="col-7 mt-1" id="userSearch"
                                                        @if (request('search')) style="display: flex !important" @endif>
                                                        <input type="search" class="form-control hd-search"
                                                            wire:model.defer="search" name="search"
                                                            value="{{ old('search', request('search')) }}"
                                                            placeholder="@lang('user.Search By Name, Pobox, Email')">
                                                        <button type="submit"
                                                            class="btn btn-primary waves-effect ml-1 waves-light">
                                                            <i class="fa fa-search" aria-hidden="true"></i>
                                                        </button>
                                                        <button class="btn btn-primary ml-1 waves-effect waves-light"
                                                            onclick="window.location.reload();">
                                                            <i class="fa fa-undo" data-bs-toggle="tooltip"
                                                                title="" data-bs-original-title="fa fa-undo"
                                                                aria-label="fa fa-undo" aria-hidden="true"></i></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive mt-1">
                        <table class="table table-bordered mb-0">
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
                                @foreach ($users as $user)
                                    <tr>
                                        <td>
                                            {{ $user->created_at->format('Y-m-d') }}
                                        </td>
                                        <td>
                                            {{ $user->pobox_number }}
                                        </td>
                                        <td>
                                            {{ $user->name . $user->last_name }}
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
                                                    <button type="button" data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false"
                                                        class="btn btn-success btn-sm dropdown-toggle waves-effect waves-light" style="width: 100px">
                                                        @lang('user.Action')
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right dropright">
                                                        {{-- <a href="{{ route('admin.call-flows.edit',$user) }}" title="Edit Call Flows" class="dropdown-item w-100">
                                                            <i class="feather icon-edit"></i> Edit
                                                        </a> --}}
                                                        {{-- <a href="{{ route('admin.users.permissions.index',$user) }}" title="Edit User Permissions" class="dropdown-item w-100">
                                                            <i class="fa fa-key"></i> Roles & Permission
                                                        </a> --}}
                                                        <a href="{{ route('admin.users.setting.index', $user) }}"
                                                            title="@lang('user.User Setting')" class="dropdown-item w-100">
                                                            <i class="fa fa-cog"></i> @lang('user.User Setting')
                                                        </a>
                                                        <a href="{{ route('admin.activity.log.index', ['id' => $user]) }}"
                                                            title="Check User Activity" class="dropdown-item w-100">
                                                            <i class="feather icon-activity"></i> Activity Logs
                                                        </a>
                                                        <form action="{{ route('admin.users.login', $user) }}"
                                                            class="d-flex" method="post">
                                                            @csrf
                                                            <button class="dropdown-item w-100">
                                                                <i class="feather icon-lock"></i> @lang('user.Login')
                                                            </button>
                                                        </form>
                                                        @can('delete', $user)
                                                            <form action="{{ route('admin.users.destroy', $user) }}"
                                                                class="d-flex" method="post"
                                                                onsubmit="return confirmDelete()">
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
