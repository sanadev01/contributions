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
                @if ($chileService == true)
                <th>
                    SCL (SRM)
                </th>

                <th>
                    SCL (SRP)
                </th>
                @else
                <th>
                    CWB
                </th>

                <th>
                    GRU  
                </th>
                    @if ($anjunService)
                        <th>
                            Commission
                        </th>
                    @endif
                @endif
            </tr>
            <tr>
                <th style="width: 20% !important;">
                    
                </th>
                <th style="width: 20% !important;">
                    <select class="form-control" wire:model="selectedCountry">
                        <option value="" selected>ALL</option>
                        <option value="30">Brazil</option>
                        <option value="46">Chile</option>
                    </select>
                </th>
                <th style="width: 20% !important;">
                    <input type="search" class="form-control" wire:model.debounce.500ms="weight">
                </th>
                <th style="width: 20% !important;">
                    <input type="search" class="form-control" wire:model.debounce.500ms="cwb">
                </th>
                <th style="width: 20% !important;">
                    <input type="search" class="form-control" wire:model.debounce.500ms="gru">
                </th>
                @if ($anjunService)
                    <th style="width: 20% !important;">
                        
                    </th>
                @endif
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
                    @if ($anjunService)
                        <th>
                            ${{ $rate->commission }}
                        </th>
                    @endif
                </tr>
                
            @endforeach
        </tbody>
    </table>
</div>
