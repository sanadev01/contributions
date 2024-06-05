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
                <th>
                    
                </th>
                <th>
                    <select class="form-control" wire:model="selectedCountry">
                        <option value="" selected>ALL</option>
                        <option value="30">Brazil</option>
                        <option value="46">Chile</option>
                    </select>
                </th>
                <th>
                    <input type="search" class="form-control" wire:model.debounce.500ms="weight">
                </th>
                <th>
                    <input type="search" class="form-control" wire:model.debounce.500ms="cwb">
                </th>
                <th>
                    <input type="search" class="form-control" wire:model.debounce.500ms="gru">
                </th>
                @if ($anjunService)
                    <th>
                        <input type="search" class="form-control" wire:model.debounce.500ms="commission">
                        
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
                        ${{ number_format($rate->cwb, 2) }}
                    </th>

                    <th>
                        ${{ number_foramt($rate->gru, 2) }}
                    </th>
                    @if ($anjunService)
                        <th>
                            ${{ number_format($rate->commission, 2) }}
                        </th>
                    @endif
                </tr>
                
            @endforeach
        </tbody>
    </table>
</div>
