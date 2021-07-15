<div>
    <table class="table table-bordered table-responsive-md">
        <thead>
            <tr>
                <th>
                    Country
                </th>

                <th>
                    Service
                </th>

                <th>
                    Weight (Grams)
                </th>

                <th>
                    CWB
                </th>

                <th>
                    GRU  
                </th>
            </tr>
            <tr>
                <th>
                    <select class="form-control" wire:model="selectedCountry">
                        <option value="" selected>ALL</option>
                        <option value="30">Brazil</option>
                        <option value="46">Chile</option>
                    </select>
                </th>
                <th style="width: 28% !important;">
                    <select class="form-control" wire:model="selectedService">
                        <option value="" selected>ALL</option>
                        <option value="33162">Standard</option>
                        <option value="33170">Express</option>
                        <option value="33197">Mini</option>
                        <option value="28">SRP</option>
                        <option value="32">SRM</option>
                    </select>
                </th>
                <th style="width: 30% !important;">
                    <input type="search" class="form-control" wire:model.debounce.500ms="weight">
                </th>
                <th>
                    
                </th>
                <th>
                    
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shippingRates as $rate)
                <tr>
                    <th>
                        {{$rate->country->name}}
                    </th>
                    <th>
                        {{ $rate->getServiceName() }}
                    </th>

                    <th>
                        {{ $rate->weight }}
                    </th>

                    <th>
                        ${{ $rate->cwb }}
                    </th>

                    <th>
                        ${{ $rate->gru }}
                    </th>
                </tr>
                
            @endforeach
        </tbody>
    </table>
</div>
