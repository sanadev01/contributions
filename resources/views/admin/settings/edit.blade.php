@extends('layouts.master')
@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Edit Settings</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            @if( $errors->count() )
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>
                                                {{ $error }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{ route('admin.settings.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Authorize ID<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" value="{{ old('AUTHORIZE_ID') }}" name="AUTHORIZE_ID" required placeholder="AUTHORIZE .Net ID">
                                        <div class="help-block"></div>
                                        {{-- setting('AUTHORIZE_ID') --}}
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Authorize Key<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="AUTHORIZE_KEY" value="{{ old('AUTHORIZE_KEY') }}" required placeholder="AUTHORIZE .Net KEY">
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <hr>
                                {{-- <livewire:token-generator/> --}}
                                <div>
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">Application Token<span class="text-danger">*</span></label>
                                        <div class="col-md-5">
                                            <textarea type="text" rows="4" class="form-control" name="token" required placeholder="Token">{{  auth()->user()->api_token  }}</textarea>
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary" type="button" wire:click="revoke">Revoke</button>
                                        </div>
                                    </div>
                                
                                    <div class="position-absolute">
                                        {{-- @include('layouts.livewire.loading') --}}
                                    </div>
                                </div>

                                <div class="row mt-1">
                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            Save Changes
                                        </button>
                                        <button type="reset" class="btn btn-outline-warning waves-effect waves-light">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
