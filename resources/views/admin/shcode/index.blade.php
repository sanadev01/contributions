@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">ShCodes</h4>
                        <div>
                            <a href="{{ route('admin.shcode-export.create') }}" class="pull-right btn btn-secondary">@lang('shcode.Import Sh Code')</a>
                            <a href="{{ route('admin.shcode-export.index') }}" class="pull-right btn btn-success mr-2">@lang('shcode.Download')</a>
                            <a href="{{ route('admin.shcode.create') }}" class="pull-right btn btn-primary mr-2">@lang('shcode.Create Sh Code')</a>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="mt-1"> 
                            <livewire:sh-code-table />  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
