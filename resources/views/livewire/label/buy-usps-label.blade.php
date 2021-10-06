<div>
    <div class="form-row">
        <div class="form-group col-md-2">
            <label for="start_date">Start Date</label>
            <input type="date" wire:model.defer="start_date" class="form-control" id="start_date">
        </div>
        <div class="form-group col-md-2">
            <label for="end_date">End Date</label>
            <input type="date" wire:model.defer="end_date" class="form-control" id="end_date">
        </div>
        <div class="form-group col-md-2 mt-4 ml-3">
            <button type="button" wire:click="search" class="btn btn-primary">Search</button>
        </div>
        <div class="form-group col-md-2 mt-4 ml-3">
            <button type="button" wire:click="buyLabel" class="btn btn-primary" @if(!$selectedOrders) disabled @endif>Buy Label</button>
        </div>
    </div>
    <div class="mt-3">
        <table class="table table-bordered">
            <tr>
                <th>
                    
                </th>
                <th>@lang('orders.print-label.Barcode')</th>
                <th>PO Box#</th>
                <th>@lang('orders.print-label.Client')</th>
                <th>@lang('orders.print-label.Dimensions')</th>
                <th>@lang('orders.print-label.Kg')</th>
                <th>@lang('orders.print-label.Reference')#</th>
                <th>@lang('orders.print-label.Recpient')</th>
                <th>@lang('orders.print-label.Date')</th>
            </tr>
            @if($searchOrders)
                @foreach ($searchOrders as $order)
                <tr>
                    <td>
                        <div class="vs-checkbox-con vs-checkbox-primary">
                            <input type="checkbox" wire:model="selectedOrders" value="{{ $order->id }}">
                            <span class="vs-checkbox vs-checkbox-lg">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        </div>
                    </td>
                    <td>{{ $order->corrios_tracking_code }}</td>
                    <td>{{ $order->user->pobox_number }}</td>
                    <td>{{ $order->merchant }}</td>
                    <td>{{ $order->length }} x {{ $order->length }} x {{ $order->height }}</td>
                    <td>{{ $order->getWeight('kg') }}</td>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->recipient->first_name }}</td>
                    <td>{{ $order->order_date }}</td>
                    <td>{{ $order->arrived_date }}</td>
                </tr>
                @endforeach
            @endif
        </table>   
    </div>
    {{-- sender address modal --}}
    @if ($shippingServices)
    <div class="modal d-block" id="senderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Buy USPS Label <span class="text-danger ml-3">Total Weight : {{ $totalWeight}} Kg</span></h5>
              <button type="button" class="close" wire:click="closeModal()" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-row">
                      <div class="form-group col-md-6">
                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model.lazy="firstName" name="firstName" id="first_name" placeholder="Enter your First Name" required>
                        @error('firstName') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                      <div class="form-group col-md-6">
                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model.lazy="lastName" name="lastName" id="last_name" placeholder="Enter your last Name" required>
                        @error('lastName') <span class="error text-danger">{{ $message }}</span> @enderror
                    </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="state">Select State <span class="text-danger">*</span></label>
                            <select name="selectedState" wire:model="selectedState" id="sender_state" class="form-control" required>
                                <option value="">Select @lang('address.State')</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->code }}">{{ $state->code }}</option>
                                @endforeach
                            </select>
                            @error('selectedState') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="sender_address">Sender Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model.lazy="senderAddress" name="senderAddress" id="sender_address" placeholder="Enter you street address">
                            @error('senderAddress') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="sender_city">Sender City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model.lazy="senderCity" name="sender_city" id="sender_city" placeholder="Enter your city">
                            @error('senderCity') <span class="error text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="sender_zipcode">Sender Zip Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="senderZipCode" name="sender_zipcode" value="{{ old('sender_zipcode') }}" id="sender_zipcode" placeholder="Enter your zipcode">
                            @if ($zipcodeResponse)
                                <span class="error {{ $reposnseClass }}"> {{$zipcodeResponse}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputEmail4">Choose Service <span class="text-danger">*</span></label>
                            <select name="service" wire:model="selectedService" id="usps_shipping_service" class="form-control" required>
                                <option value="">@lang('orders.order-details.Select Shipping Service')</option>
                                @foreach ($shippingServices as $shippingService)
                                    <option value="{{ $shippingService['service_sub_class'] }}">{{ "{$shippingService['name']}"}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($uspsRate)
                            <div class="form-group col-md-6 border border-danger">
                                <h5 class="text-danger mt-4">Total Charges : {{ $uspsRate}} USD</h5>
                            </div>
                        @endif
                    </div>
                    @if ($uspsError)
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <h4 class="text-danger mt-4">{{ $uspsError}}</h4>
                        </div> 
                    </div>
                    @endif
                  </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" wire:click="closeModal()">Close</button>
              <button type="button" class="btn btn-primary" wire:click="getLabel()" @if($uspsRate == null) disabled @endif>Buy USPS Label</button>
            </div>
          </div>
        </div>
    </div>
    @endif
    <div class="position-absolute">
        @include('layouts.livewire.loading')
    </div>
</div>

