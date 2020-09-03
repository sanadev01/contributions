@extends('layouts.master')

@section('page')
    <section id="vue-prealerts-index">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="mb-0">
                            @lang('prealerts.my-prealerts')
                        </h1>
                        {{-- @admin --}}
                        <a href="{{ route('admin.prealerts.create') }}" class="pull-right btn btn-primary"> @lang('prealerts.create-prealert') </a>
                        {{-- @endadmin --}}
                        {{-- @user --}}
                        <a href="{{ route('admin.prealerts.create') }}" class="pull-right btn btn-primary"> @lang('prealerts.create-prealert') </a>
                        {{-- @enduser --}}
                    </div>
                    <div class="card-content card-body">
                        <div class="table-responsive-md mt-1">
                            {{-- <livewire:pre-alert-table></livewire:pre-alert-table> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
