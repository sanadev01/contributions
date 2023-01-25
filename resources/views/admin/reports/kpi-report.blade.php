@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('orders.Key Performance Indicator Report')</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row mb-4 no-print ">
                                <div class="col-1">
                                    <select class="form-control" wire:model="pageSize">
                                        <option value="1">1</option>
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="300">300</option>
                                    </select>
                                </div>
                                <div class="col-11 text-right">
                                    <form action="{{ route('admin.reports.kpi-report.create') }}" method="GET" target="_blank">
                                        @csrf
                                        <div class="row">
                                            <div class="offset-2 col-md-3">
                                                <div class="row">
                                                    <div class="col-md-4 mt-2">
                                                        <label>Start Date</label>
                                                    </div>
                                                    <div class="col-md-8 pl-0 pr-0">
                                                        <input type="date" name="start_date" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="row">
                                                    <div class="col-md-4 mt-2 pl-0">
                                                        <label>End Date</label>
                                                    </div>
                                                    <div class="col-md-8 pl-0">
                                                        <input type="date" name="end_date" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <button class="btn btn-primary btn-md">
                                                    @lang('user.Search')
                                                </button>
                                            </div>
                                            <div class="offset-2 col-md-1">
                                                <button class="btn btn-success" title="@lang('orders.import-excel.Download')">
                                                    <i class="fa fa-arrow-down"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <table class="table mb-0 table-responsive-md">
                                <thead>
                                    <tr>
                                        <th>@lang('orders.Tracking')</th>
                                        <th>@lang('orders.Type Package')</th>
                                        <th>@lang('orders.First Event')</th>
                                        <th>@lang('orders.Last Event')</th>
                                        <th>@lang('orders.Days Between')</th>
                                        <th>@lang('orders.Last Event')</th>
                                        <th>@lang('orders.Taxed')</th>
                                        <th>@lang('orders.Delivered')</th>
                                        <th>@lang('orders.Returned')</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trackings['return']['objeto'] as $data)
                                        @if(isset($data['evento']))
                                            @php
                                                $delivered = "No";
                                                $returned = "No";
                                                $taxed = "No";
                                                $diffDates = "0";        
                                                for($t=count($data['evento'])-1;$t>=0;$t--) {   ##start the looping beginning from the first event
                                                    
                                                    switch($data['evento'][$t]['descricao']) {

                                                        case "Objeto entregue ao destinatário":     ## if the package was delivered mark as delivered
                                                            $delivered = "Yes";

                                                            if($taxed == "")       ## if the package was delivered and the status of taxed is blank our package wasn't taxed
                                                                $taxed = "No";

                                                        break;

                                                        case "Devolução autorizada pela Receita Federal":   ## if the package was declined by inspection
                                                        case "A entrada do objeto no Brasil não foi autorizada pelos órgãos fiscalizadores":
                                                            $returned = "Yes";
                                                        break;

                                                        case "Aguardando pagamento":        ## if we have the event of "waiting for payment" or "payment approved" is because the package was taxed
                                                        case "Pagamento confirmado":
                                                            $taxed = "Yes";
                                                        break;

                                                        case "Fiscalização aduaneira finalizada":  ## if we have the status of "inspection finished" is because the package wasn't taxed YET
                                                            if($taxed == "")
                                                                $taxed = "No";
                                                        break;

                                                    }
                                                    
                                                }
                                                $eventsQtd = count($data['evento'])-1; ## number of events for the package
                                                $dateFirstEvent = \DateTime::createFromFormat('d/m/Y', $data['evento'][$eventsQtd]['data']); ##date of first event
                                                $dateLastEvent = \DateTime::createFromFormat('d/m/Y', $data['evento'][0]['data']); ##date of last event
                                                $interval = $dateFirstEvent->diff($dateLastEvent);  ##days between first and last event
                                                $diffDates = $interval->format('%R%a days');
                                            @endphp
                                        <tr>
                                            @if(optional($data) && isset($data['numero']))
                                                <td>{{ $data['numero'] }}</td>
                                                <td><span>{{ $data['categoria'] }}</span></td>
                                                <td>{{ $data['evento'][count($data['evento'])-1]['data'] }}</td>
                                                <td>{{ $data['evento'][0]['data'] }}</td>
                                                <td>
                                                    
                                                    {{ $diffDates }}
                                                </td>
                                                <td>{{ $data['evento'][0]['descricao'] }}</td>
                                                <td>{{ $taxed }}</td>
                                                <td>{{ $delivered }}</td>
                                                <td>{{ $returned }}</td>
                                            @else
                                            <td colspan='9'>No Trackings Found</td>
                                            @endif
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                            
                            @include('layouts.livewire.loading')
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
