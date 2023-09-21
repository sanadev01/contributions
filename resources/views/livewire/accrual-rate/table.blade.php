<div>
    <div class="mb-2 row col-md-12 hide pl-0" @if ($this->search || $this->selectedCountry) style="display: flex !important" @endif
        id="logSearch">
        <form class="col-12 d-flex pl-0" wire:submit.prevent="render">
            <div class="col-5">
                <label>Search</label>
                <input type="search" class="form-control" wire:model.defer="search">
            </div>
            <div class="col-3">
                <select class="form-control hd-mt-20" wire:model="selectedCountry">
                    <option value="" selected>ALL</option>
                    <option value="30">Brazil</option>
                    <option value="46">Chile</option>
                </select>
            </div>
            <div class="mt-1">
                <button type="submit" class="btn btn-primary ml-1 mt-4">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>
                <button class="btn btn-primary ml-1 mt-4 waves-effect waves-light" onclick="window.location.reload();">
                    <i class="fa fa-undo" data-bs-toggle="tooltip" title="" data-bs-original-title="fa fa-undo"
                        aria-label="fa fa-undo" aria-hidden="true"></i></button>
            </div>
        </form>
    </div>
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
                        {{ $rate->country->name }}
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
