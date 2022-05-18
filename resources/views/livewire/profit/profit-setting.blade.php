<div>

    <div class="controls row mb-1 align-items-center">
        <label class="col-md-3 text-md-right">Service<span class="text-danger"></span></label>
        <div class="col-md-6">
            
            <select class="form-control"  wire:model.defer="service_id">
                <option value="">Select service</option>
                @foreach ($services as $service)
                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                @endforeach
            </select>
            <div class="help-block">
                @error('service_id')
                    <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="controls row mb-1 align-items-center">
        <label class="col-md-3 text-md-right">@lang('user.Package')<span class="text-danger"></span></label>
        <div class="col-md-6">
            <select class="form-control" data-live-search="true"  wire:model.defer="package_id">
                <option value="">@lang('user.Select Package')</option>
                @isset($packages)
                    @foreach ($packages as $package)
                        <option value="{{ $package->id }}">{{ $package->name }}</option>
                    @endforeach
                @endisset
            </select>
            <div class="help-block">
                @error('package_id')
                    <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="col-6 d-flex flex-sm-row flex-column justify-content-end mt-1 offset-3">
        <button type="" class="btn btn-primary" wire:click.prevent="save">
            Save Settings
        </button>
    </div>

    <h4 class="ml-5">Profit Package</h4>
    <div class="controls row mb-1 align-items-center">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Profit Package</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($profitSettings as $profit)
                        <tr>
                            <td>{{ optional($profit->shippingService)->name }}</td>
                            <td>{{ optional($profit->profitPackage)->name }}</td>
                            <td>
                                <button type="" class="btn btn-danger" wire:click.prevent="remove({{ $profit }})">
                                    Remove
                                </button>
                            </td>
                                
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-danger">No Record found</td>
                        </tr>
                    @endforelse
                    
                </tbody>
            </table>
        </div>
        <div class="col-md-3"></div>
    </div>
    @include('layouts.livewire.loading')
</div>

