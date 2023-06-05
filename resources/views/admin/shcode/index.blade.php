@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pr-1">
                    @section('title', __('ShCodes'))
                    <div class="col-12 d-flex justify-content-end">
                        <a href="{{ route('admin.shcode-export.create') }}"
                            class="pull-right btn btn-secondary mr-1">@lang('shcode.Import Sh Code')</a>
                        <a href="{{ route('admin.shcode-export.index') }}"
                            class="pull-right btn btn-success mr-1">@lang('shcode.Download')</a>
                        <a href="{{ route('admin.shcode.create') }}"
                            class="pull-right btn btn-primary">@lang('shcode.Create Sh Code')</a>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="mt-1">
                            <table class="table mb-0  table-bordered">
                                <thead>
                                    <tr>
                                        <th>
                                            Code
                                        </th>
                                        <th>
                                            English
                                        </th>
                                        <th>
                                            Portuguese
                                        </th>
                                        <th>
                                            Spanish
                                        </th>
                                        <th>
                                            @lang('role.Action')
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($shCodes as $shCode)
                                        <tr>
                                            <td>
                                                {{ $shCode['code'] }}
                                            </td>
                                            <td>
                                                {{ optional(explode('-------', $shCode['description']))[0] }}
                                            </td>
                                            <td>
                                                {{ optional(explode('-------', $shCode['description']))[1] }}
                                            </td>
                                            <td>
                                                {{ optional(explode('-------', $shCode['description']))[2] }}
                                            </td>
                                            <td class="d-flex">
                                                <a href="{{ route('admin.shcode.edit', $shCode['id']) }}"
                                                    class="btn btn-primary mr-2" title="Edit Shcode">
                                                    <i class="feather icon-edit"></i>
                                                </a>

                                                <form action="{{ route('admin.shcode.destroy', $shCode['id']) }}"
                                                    method="post" onsubmit="return confirmDelete()">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger" title="Delete Shcode">
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
    </div>
</section>
@endsection
