@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">ShCodes</h4>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.shcode-export.create') }}" class="btn btn-secondary mr-2">@lang('shcode.Import Sh Code')</a>
                        
                            <div class="dropdown">
                                <button class="btn btn-success dropdown-toggle pt-1 pb-1 mr-2" type="button" id="downloadDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    @lang('shcode.Download')
                                </button>
                                <div class="dropdown-menu" aria-labelledby="downloadDropdown">
                                    <a class="dropdown-item" href="{{ route('admin.shcode-export.index', ['type' => 'All']) }}">All</a>
                                    <a class="dropdown-item" href="{{ route('admin.shcode-export.index', ['type' => 'Postal (Correios)']) }}">Postal (Correios)</a>
                                    <a class="dropdown-item" href="{{ route('admin.shcode-export.index', ['type' => 'Courier']) }}">Courier</a>
                                </div>
                            </div>
                        
                            <a href="{{ route('admin.shcode.create') }}" class="btn btn-primary">@lang('shcode.Create Sh Code')</a>
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
