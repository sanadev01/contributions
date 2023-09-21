@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('connect.Integrations')</h4>
                        @can('create', App\Models\Connect::class)
                            <a href="{{ route('admin.connect.create') }}" class="pull-right btn btn-primary"> @lang('connect.New Integration') </a>
                        @endcan
                    </div>
                    <div class="card-content card-body">
                        <div class="mt-1">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th>
                                        @lang('connect.Name')
                                    </th>
                                    <th>
                                        @lang('connect.Url')
                                    </th>
                                    <th>
                                        @lang('connect.Type')
                                    </th>
                                    <th>
                                        @lang('connect.Action')
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($connects as $connect)
                                        <tr>
                                            <td>
                                                {{ $connect->store_name }}
                                            </td>
                                            <td>
                                                {{ $connect->store_url }}
                                            </td>
                                            <td>
                                                {{ $connect->type }}
                                            </td>
                                            <td class="d-flex">
                                                
                                                @can('update', $connect)
                                                <a href="{{ route('admin.connect.edit',$connect) }}" class="btn btn-primary mr-2" title="@lang('connect.Edit')">
                                                    <i class="fa fa-cogs"></i>
                                                </a>
                                                @endcan

                                                @can('delete', $connect)
                                                <form action="{{ route('admin.connect.destroy',$connect) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger" title="@lang('connect.Delete')">
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
