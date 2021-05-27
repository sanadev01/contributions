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
                        <h4 class="mb-0">@lang('report.shipment-report')</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            {{-- <livewire:reports.user-shipment-report-table /> --}}
                            <div>
                                <div class="row">
                                    <div class="col-12 text-right">
                                        <a href="{{ $downloadLink }}" class="btn btn-primary" {{ !$downloadLink ? 'disabled': '' }} target="_blank">
                                            @lang('report.download')
                                        </a>
                                    </div>
                                </div>
                                <div class="row my-1 ml-1">
                                    <form action="{{ route('admin.reports.user-shipments.index') }}" class="col-md-12">
                                        <div class="form-row">
                                            {{-- sorting attribute --}}
                                            <input type="hidden" value="name" name="sortBy" id="sortBy" class="form-control">
                                            <input type="hidden" value="{{$sortAsc}}" name="sortAsc" id="sortAsc" class="form-control">
                                            <div class="form-group col-md-2">
                                                <label for="start_date">@lang('report.start-date')</label>
                                                <input type="date" name="start_date" value="{{old('start_date', request('start_date'))}}" id="start_date" class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="end_date">@lang('report.end-date')</label>
                                                <input type="date" name="end_date" value="{{old('end_date', request('end_date'))}}" id="end_date" class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="name">@lang('report.name')</label>
                                                <input type="text" type="text" name="name" value="{{old('name', request('name'))}}" id="user" placeholder="@lang('report.search-by') @lang('report.name')" class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="pobox_number">@lang('report.pobox-number')</label>
                                                <input type="text" name="pobox_number" value="{{old('pobox_number', request('pobox_number'))}}" id="pobox_number" placeholder="@lang('report.search-by') @lang('report.pobox-number')" class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="email">@lang('report.email')</label>
                                                <input type="email" name="email" id="email" value="{{old('email', request()->get('email'))}}" placeholder="@lang('report.search-by') @lang('report.email')" class="form-control">
                                            </div>
                                            <div class="form-group col-md-2">
                                                <button type="submit" class="btn btn-primary" style="margin-top: 1.8rem; !important">@lang('report.search')</button>
                                            </div>
                                        </div>    
                                    </form>
                                </div>    

                                <table class="table mb-0" id="example">
                                    <thead>
                                        <tr>
                                            <th>
                                                
                                            </th>
                                            <th>
                                                @if(request()->has('name') && (request('start_date') == '' || request('end_date') == '' ))
                                                    <a href="{{ route('admin.reports.sortBy',['sortBy'=>'name', 'sortAsc' => $sortAsc, 'name' => request('name')]) }}">
                                                        Name
                                                    </a>
                                                @elseif(request()->has('start_date') && !(request()->has('end_date')))
                                                    <a href="{{ route('admin.reports.sortBy',['sortBy'=>'name', 'sortAsc' => $sortAsc, 'start_date' => request('start_date')]) }}">
                                                        Name
                                                    </a>
                                                @elseif(request()->has('end_date') && !request()->has('start_date'))
                                                    <a href="{{ route('admin.reports.sortBy',['sortBy'=>'name', 'sortAsc' => $sortAsc, 'end_date' => request('end_date')]) }}">
                                                        Name
                                                    </a>
                                                @elseif(request()->has('end_date') && request()->has('start_date'))
                                                    <a href="{{ route('admin.reports.sortBy',['sortBy'=>'name', 'sortAsc' => $sortAsc, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}">
                                                        Name
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.reports.sortBy',['sortBy'=>'name', 'sortAsc' => $sortAsc]) }}">
                                                        Name
                                                    </a>
                                                @endif
                                                
                                                @if ( $sortBy == 'name' && $sortAsc == 'asc' )
                                                    <i class="fa fa-arrow-down ml-2"></i>
                                                @elseif( $sortBy =='name' && $sortAsc == 'desc' )
                                                    <i class="fa fa-arrow-up ml-2"></i>
                                                @endif
                                            </th>
                                            <th>
                                                <a href="{{ route('admin.reports.sortBy',['sortBy'=>'pobox_number', 'sortAsc' => $sortAsc]) }}">
                                                    Pobox Number
                                                </a>
                                                @if ( $sortBy == 'pobox_number' && $sortAsc == 'asc' )
                                                    <i class="fa fa-arrow-down ml-2"></i>
                                                @elseif( $sortBy =='pobox_number' && $sortAsc == 'desc' )
                                                    <i class="fa fa-arrow-up ml-2"></i>
                                                @endif
                                            </th>
                                            <th>
                                                <a href="{{ route('admin.reports.sortBy',['sortBy'=>'email', 'sortAsc' => $sortAsc]) }}">
                                                    Email
                                                </a>
                                                @if ( $sortBy == 'email' && $sortAsc == 'asc' )
                                                    <i class="fa fa-arrow-down ml-2"></i>
                                                @elseif( $sortBy =='email' && $sortAsc == 'desc' )
                                                    <i class="fa fa-arrow-up ml-2"></i>
                                                @endif
                                            </th>
                                            <th>
                                                <a href="{{ route('admin.reports.sortBy',['sortBy'=>'order_count', 'sortAsc' => $sortAsc]) }}">
                                                    Shipment Count
                                                </a>
                                                @if ( $sortBy == 'order_count' && $sortAsc == 'asc' )
                                                    <i class="fa fa-arrow-down ml-2"></i>
                                                @elseif( $sortBy =='order_count' && $sortAsc == 'desc' )
                                                    <i class="fa fa-arrow-up ml-2"></i>
                                                @endif
                                            </th>
                                            <th>
                                                <a href="{{ route('admin.reports.sortBy',['sortBy'=>'weight', 'sortAsc' => $sortAsc]) }}">
                                                    Weight
                                                </a>
                                                @if ( $sortBy == 'weight' && $sortAsc == 'asc' )
                                                    <i class="fa fa-arrow-down ml-2"></i>
                                                @elseif( $sortBy =='weight' && $sortAsc == 'desc' )
                                                    <i class="fa fa-arrow-up ml-2"></i>
                                                @endif
                                            </th>
                                            <th>
                                                <a href="{{ route('admin.reports.sortBy',['sortBy'=>'spent', 'sortAsc' => $sortAsc]) }}">
                                                    Spent
                                                </a>
                                                @if ( $sortBy == 'spent' && $sortAsc == 'asc' )
                                                    <i class="fa fa-arrow-down ml-2"></i>
                                                @elseif( $sortBy =='spent' && $sortAsc == 'desc' )
                                                    <i class="fa fa-arrow-up ml-2"></i>
                                                @endif
                                            </th>
                                        </tr>
                                       
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