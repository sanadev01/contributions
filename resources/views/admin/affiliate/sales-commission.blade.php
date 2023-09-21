@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    {{-- <div class="card-header"> --}}
                    @if (auth()->user()->isAdmin())
                        @section('title', __('sales-commission.Sales Commissions'))
                    @else
                    @section('title', __('sales-commission.My Sales Commissions'))
                @endif
                {{-- </div> --}}
                <div class="card-content">
                    <div class="card-body">
                        <div class="table-responsive-md">
                            <livewire:affiliate.table />
                        </div>
                    </div>
                </div>
            </div>
        </div>



           
        </div>
    </section>
@endsection
@section('modal')
<x-modal />
@endsection


@section('js')
    <script>

    </script>
  
@endsection