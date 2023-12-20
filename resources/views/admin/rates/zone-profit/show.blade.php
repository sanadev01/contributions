@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="">
                            <h4 class="mb-0 mr-3">
                                Profit Rates of Group {{$groupId}}
                            </h4>
                            <hr>
                        </div>
                        @can('create', App\Models\Rate::class)
                        <div class="row col-md-6">
                            <div class="ml-auto">
                                <a href="{{ route('admin.rates.zone-profit.index') }}" class="pull-right btn btn-primary ml-2">
                                    @lang('shipping-rates.Return to List')
                                </a>
                                <a href="{{ route('admin.rates.downloadZoneProfit', ['group_id' => $groupId, 'shipping_service_id' => $serviceId]) }}" class="pull-right btn btn-success">
                                    @lang('shipping-rates.Download')
                                </a>
                            </div>    
                        </div>
                            
                        @endcan
                    </div>
                    <hr>
                    <div class="card-content card-body">
                        <table class="table table-bordered table-responsive-md">
                            <thead>
                                <tr>
                                    <th>
                                        Shipping Service
                                    </th>
                                    <th>
                                        Country
                                    </th>
                                    <th>
                                        Profit
                                    </th>
                                    <th>
                                        Edit
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($zoneProfit as $rate)
                                    <tr>
                                        <th>
                                            {{ $rate->shippingService->name }}
                                        </th>
                                        <th>
                                            {{ $rate->country->name }}
                                        </th>
                                        <th>
                                            {{ $rate->profit_percentage }}
                                        </th>
                                        <th>
                                            <a href="#" class="btn btn-primary mr-2" title="Update Profit Percentage"
                                                data-data_id="{{$rate->id}}"
                                                data-service="{{$rate->shippingService->name}}"
                                                data-country="{{$rate->country->name}}"
                                                data-profit="{{$rate->profit_percentage}}"
                                                data-toggle="modal" 
                                                data-target="#updateProfit"><i class="feather icon-edit"></i>
                                            </a>
                                        </th>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Update Profit Modal-->
    <div class="modal fade" id="updateProfit" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Update Profit Value</b></h5>
                </div>
                <form class="form" action="{{ route('admin.rates.updateZoneProfit', $groupId) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="data_id" id="data_id" value="">
                    <div class="modal-body"><br>
                        <div class="row justify-content-center">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="service">Shipping Service</label>
                                    <input type="text" class="form-control" id="service" name="service" readonly required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="group">Group</label>
                                    <input type="text" class="form-control" value="Group {{ $groupId }}" readonly required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <input type="text" class="form-control" id="country" name="country" readonly required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="profit">Profit</label>
                                    <input type="text" class="form-control" id="profit" name="profit" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Update</button>
                            <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>    
            </div>
        </div>
    </div>
    @section('js')
        <script type="text/javascript">
            $(document).ready(function(){
                $('#updateProfit').on('show.bs.modal', function(event){

                    var button = $(event.relatedTarget)
                    var data_id = button.data('data_id')
                    var service = button.data('service')
                    var country = button.data('country')
                    var profit = button.data('profit')

                    var modal = $(this)
                    $('#data_id').val(data_id);
                    $('#service').val(service);
                    $('#country').val(country);
                    $('#profit').val(profit);
                });
            });
        </script>
    @endsection
@endsection

