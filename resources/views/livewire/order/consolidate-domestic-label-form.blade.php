<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-form">Domestic Label</h4>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary pull-right">
                @lang('shipping-rates.Return to List')
            </a>
    
            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="container">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" style="width: 6%;">#</th>
                        <th scope="col-3">Order ID</th>
                        <th scope="col-3">Loja/Cliente</th>
                        <th scope="col-3">Weight</th>
                        <th scope="col-3">Tracking Code</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $order->id }}</td>
                        <td>{{ ucfirst($order->merchant) }}</td>
                        <td>{{ $order->getWeight('kg') }} Kg</td>
                        <td>{{ $order->corrios_tracking_code }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <th scope="row"></th>
                        <td></td>
                        <td>Total Weight</td>
                        <td>
                            {{ $totalWeight['totalWeightInKg']}} Kg /
                            <span>{{ $totalWeight['totalWeightInLbs'] }} Lbs</span>
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            @if ($consolidationErrors)
                <div class="alert alert-danger" role="alert">
                    @foreach ($consolidationErrors as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </div>
            @endif
        </div>
        <hr>
        <form wire:submit.prevent="getRates">
            <div class="ml-3">
                <div class="row ml-3">
                    <h2 class="mb-2">
                        Consolidated Order Details
                    </h2>
                </div>
                <div class="row mt-3 ml-3">
                    <div class="container">
                        <livewire:order.shipment-info />
                    </div>
                </div>
            </div>
            <div class="ml-3 mt-3">
                <div class="row ml-3">
                    <h2 class="mb-2">
                        Sender Address
                    </h2>
                </div>
                <div class="container">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="sender_zipcode">Sender Phone <span class="text-danger">*</span></label>
                            @livewire('components.search-address', ['user_id' => $userId])
                            @error('senderPhone') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="first_name">First Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model.lazy="firstName" class="form-control" name="first_name" id="first_name" placeholder="Enter your First Name" required>
                            @error('firstName') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="last_name">Last Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model.lazy="lastName" class="form-control" name="last_name" id="last_name" placeholder="Enter your last Name" required>
                            @error('lastName') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="state">Select State <span class="text-danger">*</span></label>
                            <select wire:model.lazy="senderState" name="sender_state" id="sender_state" class="form-control" required>
                                <option value="" disabled>Select @lang('address.State')</option>
                                @foreach ($states as $state)
                                    <option value="{{$state->code}}">{{ $state->code }}</option>
                                @endforeach
                            </select>
                            @error('senderState') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="sender_address">Sender Address <span class="text-danger">*</span></label>
                            <input type="text" wire:model.lazy="senderAddress" class="form-control" name="sender_address" id="sender_address" placeholder="Enter you street address">
                            @error('senderAddress') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="sender_city">Sender City <span class="text-danger">*</span></label>
                            <input type="text" wire:model.lazy="senderCity" class="form-control" name="sender_city" id="sender_city" placeholder="Enter your city">
                            @error('senderCity') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="sender_zipcode">Sender Zip Code <span class="text-danger">*</span></label>
                            <input type="text" wire:model.lazy="senderZipCode" class="form-control" name="sender_zipcode" id="sender_zipcode" placeholder="Enter your zipcode">
                            @if($zipCodeResponse) <p class="{{ $zipCodeClass }}">{{ $zipCodeResponseMessage }}</p>@endif
                            @error('senderZipCode') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <div class="input-group">
                                <div class="vs-checkbox-con vs-checkbox-primary" title="pickup">
                                    <input type="checkbox" value="true" wire:model="pickupType" name="pickup" id="pickup_type">
                                    <span class="vs-checkbox vs-checkbox-lg">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                </div>
                                <label class="mt-2 h4 text-danger">Pick Up</label>
                            </div>  
                        </div>
                        <div class="form-group col-md-3">
                            <div class="input-group">
                                <div class="vs-checkbox-con vs-checkbox-primary" title="save address">
                                    <input type="checkbox" value="true" wire:click="setAddress" name="setAddress" id="setAddress">
                                    <span class="vs-checkbox vs-checkbox-lg">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                </div>
                                <label class="mt-2 h4 text-danger">Save Address</label>
                            </div>  
                        </div>
                    </div>
                    @if ($pickupType)
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="pickup_date">Pickup Date<span class="text-danger">*</span></label>
                                <input type="date" wire:model.lazy="pickupDate" name="pickup_date" id="pickup_date" class="form-control" />
                                @error('pickupDate') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="earliest_pickup_time">Earliest Pickup Time<span class="text-danger">*</span></label>
                                <input type="time" wire:model.lazy="earliestPickupTime" name="earliest_pickup_time" id="earliest_pickup_time" class="form-control" />
                                @error('earliestPickupTime') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="latest_pickup_time">Latest Pickup Time<span class="text-danger">*</span></label>
                                <input type="time" wire:model.lazy="latestPickupTime" name="latest_pickup_time" id="latest_pickup_time" class="form-control" />
                                @error('latestPickupTime') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="pickup_location">Preferred Pickup Location <span class="text-danger">*</span></label>
                                <input type="text" wire:model.lazy="pickupLocation" class="form-control" name="pickup_location" value="{{ old('pickup_location') }}" id="pickup_location" placeholder="Enter your preferred prickup point e.g Front">
                                <span class="error text-danger">available pickup locations are: 'FRONT', 'BACK', 'NONE'</span>
                                @error('pickupLocation') <span class="error text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif        
                </div>
            </div>
            <div class="container pb-3">
                <div class="row mr-3">
                    <div class="ml-auto">
                        <button type="submit" id="submitBtn" class="btn btn-primary">Get Quote</button>
                    </div>
                </div>    
            </div>
        </form>
        @if ($shippingSerivceErrors)
            <div class="container">
                <div class="alert alert-danger" role="alert">
                    @foreach ($shippingSerivceErrors as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif
        @if ($upsError || $uspsError || $fedexError)
            <div class="container">
                <div class="alert alert-danger" role="alert">
                    {{ $upsError ? $upsError : $uspsError }}
                    {{ $fedexError ? $fedexError : '' }}
                </div>
            </div>
        @endif
        @if($usRates)
            <div class="container">
                <table class="table table-striped">
                    <thead class="thead-dark">
                      <tr>
                        <th scope="col" style="width: 6%;">#</th>
                        <th scope="col-3">Service</th>
                        <th scope="col-3">Cost</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($usRates as $rate)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>{{ $rate['service'] }}</td>
                                <td>{{ $rate['cost'] }} USD</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="ml-3 mt-3">
                <div class="row ml-3">
                    <h2 class="mb-2">
                        Service
                    </h2>
                </div>
                <div class="container pb-5">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="service">Choose Service <span class="text-danger">*</span></label>
                            <select name="service" wire:model.debounce.500ms="selectedService" id="ups_shipping_service" class="form-control" required style="height: 80%;">
                                <option value="">@lang('orders.order-details.Select Shipping Service')</option>
                                @foreach ($usShippingServices as $shippingService)
                                    <option style="font-size: 15px;" value="{{ $shippingService['service_sub_class'] }}">{{ $shippingService['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('selectedService') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="container pb-3">
                <div class="row mr-3">
                    <div class="ml-auto">
                        <button type="button" wire:click="getLabel()" class="btn btn-primary">Buy Label</button>
                    </div>
                </div>    
            </div>
        @endif
        @if ($weight && $length && $width && $height && $unit)
            @include('layouts.livewire.loading')
        @endif
        
    </div>
</div>
