@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="{{ asset('app-assets/css/plugins/forms/wizard.css') }}">
    @yield('wizard-css')
@endsection

@section('page')
    <section >
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            @lang('consolidation.Consolidate Request')
                        </h4>
                        <a href="{{ route('admin.parcels.index') }}" class="btn btn-primary">
                            @lang('consolidation.Back to List')
                        </a>
                    </div>
                    <hr>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="number-tab-steps wizard-circle wizard clearfix p-3" role="application" id="steps-uid-0">
                                <div class="steps clearfix no-print">
                                    <ul role="tablist">
                                        <li role="tab" class="first {{ in_array(request()->route()->getName(),['admin.consolidation.parcels.index'])? 'current' : 'disabled' }}" aria-disabled="false" aria-selected="true">
                                            <a id="steps-uid-0-t-0" href="#" aria-controls="steps-uid-0-p-0">
                                                {{-- <span class="current-info audible">current step: </span> --}}
                                                <span class="step">1</span> @lang('consolidation.parcels')
                                            </a>
                                        </li>
                                        <li role="tab" class="{{ in_array(request()->route()->getName(),['admin.consolidation.parcels.services.index'])? 'current' : 'disabled' }}" aria-disabled="true">
                                            <a id="steps-uid-0-t-1" href="#" aria-controls="steps-uid-0-p-1">
                                                <span class="step">2</span> @lang('consolidation.services')
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                @yield('wizard-form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
