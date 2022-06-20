@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            @if(auth()->user()->isAdmin())
                                @lang('tickets.Support Tickets')
                            @else
                                @lang('tickets.My Tickets')
                            @endif
                        </h4>
                        @user
                            <a href="{{ route('admin.tickets.create') }}" class="pull-right btn btn-primary"> @lang('tickets.Create New Ticket') </a>
                        @enduser
                    </div>
                    <div class="card-content">
                        <livewire:tickets/>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
