@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            @can('reply',App\Models\Ticket::class)
                                @lang('tickets.Support Tickets') 
                            @endcan

                            @cannot('reply',App\Models\Ticket::class)
                            @lang('tickets.My Tickets') 
                            @endcannot
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
