<div>
    <table class="table table-bordered table-responsive-md">
        <thead>
            <tr>
                <th>
                    Service
                </th>

                <th>
                    Country
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
                    
                </th>
                <th style="width: 25% !important;">
                    <select class="form-control" wire:model="selectedCountry">
                        <option value="" selected>ALL</option>
                        <option value="30">Brazil</option>
                        <option value="46">Chile</option>
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
                        {{ $rate->getServiceName() }}
                    </th>
                    <th>
                        {{$rate->country->name}}
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
