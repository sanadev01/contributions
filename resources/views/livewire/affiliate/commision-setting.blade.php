<div>
    <div class="controls row mb-1 align-items-center">
        <label class="col-md-3 text-md-right">@lang('user.Select Referrer')<span class="text-danger"></span></label>
        <div class="col-md-6">
            
            <select class="form-control" wire:model.defer="referrer_id">
                <option value="">Select referrer</option>
                @foreach ($users as $userReferrer)
                    <option value="{{ $userReferrer->id }}">{{ $userReferrer->name }}</option>
                @endforeach
            </select>
            <div class="help-block">
                @error('referrer_id')
                    <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="controls row mb-1 align-items-center">
        <label class="col-md-3 text-md-right">@lang('user.Select Commision Type')<span class="text-danger"></span></label>
        <div class="col-md-6">
            <select class="form-control" wire:model.defer="type">
                <option value="flat">Flat</option>
                <option value="percentage">Percentage</option>
            </select>
            <div class="help-block">
                @error('type')
                    <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="controls row mb-1 align-items-center">
        <label class="col-md-3 text-md-right">@lang('user.Commision Value')<span class="text-danger"></span></label>
        <div class="col-md-6">
            <input type="text" class="form-control" wire:model.defer="value"> 
          
            <div class="help-block">
                @error('value')
                    <div class="help-block text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
   
    

    <div class="col-6 d-flex flex-sm-row flex-column justify-content-end mt-1 offset-3">
        <button type="" class="btn btn-primary" wire:click.prevent="save">
            Save Commision
        </button>
    </div>

    <h4 class="ml-5">Referrals</h4>
    <div class="controls row mb-1 align-items-center">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>Referral</th>
                        <th>Type</th>
                        <th>Commission</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($CommissionSettings as $userRefferer)
                        <tr>
                            <td>{{ $userRefferer->referrer ? $userRefferer->referrer->name : "Default" }}</td>
                            <td>{{ $userRefferer->type }}</td>
                            <td>{{ $userRefferer->value }}</td>
                                
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
