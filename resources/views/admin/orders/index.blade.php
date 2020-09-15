@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"> 
                        <h4 class="mb-0">@lang('orders.orders')</h4>
                        {{-- <a href="{{ route('admin.roles.create') }}" class="pull-right btn btn-primary"> Create Role </a> --}}
                    </div>
                    <div class="card-content">
                        <div class="mt-1">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th>
                                        @lang('orders.name')
                                    </th>
                                    <th>
                                        @lang('orders.action')
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection