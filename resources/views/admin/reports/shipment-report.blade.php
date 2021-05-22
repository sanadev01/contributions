@extends('layouts.master')
@section('css')
    <style>
        td.details-control {
            background-image: url("{{ URL::asset('images/plus.png') }}");
            cursor: pointer;
            background-repeat: no-repeat;
            background-position: center;
        }
        tr.shown td.details-control {
            background-image: url("{{ URL::asset('images/minus.png')}}");
        }
    </style>
@endsection
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Shipment Report</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            {{-- <livewire:reports.user-shipment-report-table /> --}}
                            <div>
                                <div class="row">
                                    <div class="col-12 text-right">
                                        <a href="{{ $downloadLink }}" class="btn btn-primary" {{ !$downloadLink ? 'disabled': '' }} target="_blank">
                                            Download
                                        </a>
                                    </div>
                                </div>
                                <div class="row my-1 ml-1">
                                    <form action="{{ route('admin.reports.user-shipments.index') }}" class="form-inline">
                                        <label class="my-1 mr-2" for="start_date">Start Date</label>
                                        <input type="date" name="start_date" id="start_date" class="my-1 mr-sm-2">

                                        <label class="my-1 mr-2" for="end_date">End Date</label>
                                        <input type="date" name="end_date" id="end_date"  class="form-control my-1 mr-sm-2">
                                        
                                        <label class="my-1 mr-2" for="name">Name</label>
                                        <input type="text" name="name" id="user" placeholder="Search by Name" class="form-control my-1 mr-sm-2">

                                        <label class="my-1 mr-2" for="pobox_number">Pobox Number</label>
                                        <input type="text" name="pobox_number" id="pobox_number" placeholder="Search by PoBox Number" class="form-control my-1 mr-sm-2">

                                        <label class="my-1 mr-2" for="email">Email</label>
                                        <input type="email" name="email" id="email" placeholder="Search by Email" class="form-control my-1 mr-sm-2">
                                    
                                        <button type="submit" class="btn btn-primary my-1 mr-sm-2">Search</button>
                                    </form>
                                </div>    
                                {{-- <div class="row my-3">
                                    <div class="col-md-4">
                                        <label for="">Start Date</label>
                                        <input type="date" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="">End Date</label>
                                        <input type="date" class="form-control">
                                    </div>
                                </div> --}}
                                <table class="table mb-0" id="example">
                                    <thead>
                                        <tr>
                                            <th>
                                                
                                            </th>
                                            <th>
                                                <a href="#" wire:click="sortBy('name')">
                                                    Name
                                                </a>
                                                @if ( $sortBy == 'name' && $sortAsc )
                                                    <i class="fa fa-arrow-down ml-2"></i>
                                                @elseif( $sortBy =='name' && !$sortAsc )
                                                    <i class="fa fa-arrow-up ml-2"></i>
                                                @endif
                                            </th>
                                            <th>
                                                <a href="#" wire:click="sortBy('pobox_number')">
                                                    Pobox Number
                                                </a>
                                                @if ( $sortBy == 'pobox_number' && $sortAsc )
                                                    <i class="fa fa-arrow-down ml-2"></i>
                                                @elseif( $sortBy =='pobox_number' && !$sortAsc )
                                                    <i class="fa fa-arrow-up ml-2"></i>
                                                @endif
                                            </th>
                                            <th>
                                                <a href="#" wire:click="sortBy('email')">
                                                    Email
                                                </a>
                                                @if ( $sortBy == 'email' && $sortAsc )
                                                    <i class="fa fa-arrow-down ml-2"></i>
                                                @elseif( $sortBy =='email' && !$sortAsc )
                                                    <i class="fa fa-arrow-up ml-2"></i>
                                                @endif
                                            </th>
                                            <th>
                                                <a href="#" wire:click="sortBy('order_count')">
                                                    Shipment Count
                                                </a>
                                                @if ( $sortBy == 'order_count' && $sortAsc )
                                                    <i class="fa fa-arrow-down ml-2"></i>
                                                @elseif( $sortBy =='order_count' && !$sortAsc )
                                                    <i class="fa fa-arrow-up ml-2"></i>
                                                @endif
                                            </th>
                                            <th>
                                                <a href="#" wire:click="sortBy('weight')">
                                                    Weight
                                                </a>
                                                @if ( $sortBy == 'weight' && $sortAsc )
                                                    <i class="fa fa-arrow-down ml-2"></i>
                                                @elseif( $sortBy =='weight' && !$sortAsc )
                                                    <i class="fa fa-arrow-up ml-2"></i>
                                                @endif
                                            </th>
                                            <th>
                                                <a href="#" wire:click="sortBy('spent')">
                                                    Spent
                                                </a>
                                                @if ( $sortBy == 'spent' && $sortAsc )
                                                    <i class="fa fa-arrow-down ml-2"></i>
                                                @elseif( $sortBy =='spent' && !$sortAsc )
                                                    <i class="fa fa-arrow-up ml-2"></i>
                                                @endif
                                            </th>
                                        </tr>
                                        {{-- <tr>
                                            <th>
                                                
                                            </th>
                                            <th>
                                                <input type="search" class="form-control" wire:model.debounce.500ms="user">
                                            </th>
                                            <th>
                                                <input type="search" class="form-control"  wire:model.debounce.500ms="user">
                                            </th>
                                            <th>
                                                <input type="search" class="form-control"  wire:model.debounce.500ms="user">
                                            </th>
                                            <th>
                                                
                                            </th>
                                            <th>
                                                
                                            </th>
                                            <th>
                                                
                                            </th>
                                        </tr> --}}
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr>
                            
                                                <td class="details-control">
                                                    <input type="hidden" class="user_id" value="{{$user->id}}">
                                                </td>
                                                <td>
                                                    {{ $user->name }} {{ $user->last_name }}
                                                </td>
                                                <td>
                                                    {{ $user->pobox_number }} 
                                                </td>
                                                <td>
                                                    {{ $user->email }} 
                                                </td>
                                                <td class="h4">
                                                    {{ number_format($user->order_count,2) }} 
                                                </td>
                                                <td class="h4">
                                                    {{ number_format($user->weight,2) }} Kg
                                                </td>
                                                <td class="h4">
                                                    {{ number_format($user->spent,2) }} USD
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-end px-3">
                                    {{ $users->links() }}
                                </div>
                                @include('layouts.livewire.loading')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('js')
    <script>
        function format () {
            return '<table cellpadding="5" cellspacing="0" border="0" class="tbodyrow" style="padding-left:50px; background: #d8d8d878;">'+
                '<tr>'+
                    '<th>Min Weight</th>'+
                    '<th>Max Weight</th>'+
                    '<th>Order</th>'+
                '</tr>'+
            '</table>';
        }
        $(document).ready(function() {
            var table = $('#example').DataTable( {
                "searching": false,
                paging: false,
                "ordering": false
            } );
            // Add event listener for opening and closing details
            $('#example tbody').on('click', 'td.details-control', function () {
                var id = $(this).closest("tr").find(".user_id").val();
                var tr = $(this).closest('tr');
                var row = table.row( tr );
               
                if ( row.child.isShown() ) {
                    row.child.hide();
                    tr.removeClass('shown');
                }
                else {
                    $.ajax({
                        url: "{{route('admin.reports.user-shipments.create')}}",
                        type: 'GET',
                        data: {id:id},
                        dataType: 'JSON',
                        success: function (result) {
                            row.child( format(result) ).show();
                            tr.addClass('shown');
                            result.forEach(function(entry) {
                                var tdata = '<tr>'+
                                '<td>'+entry.min_weight+ ' kg ('+ parseFloat(entry.min_weight * 2.205).toFixed(2) + ' lbs)'+'</td>'+
                                '<td>'+entry.max_weight+ ' kg ('+ parseFloat(entry.max_weight * 2.205).toFixed(2) + ' lbs)'+'</td>'+
                                '<td>'+entry.orders+'</td>'+
                                '</tr>';
                                $('.tbodyrow').append(tdata);
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection