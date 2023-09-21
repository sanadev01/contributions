<div>
    <div class="form-wrapper">
        <h2>Search Brazil Zipcodes</h2>
        <div class="row">
            <div class="col-md-3">
                <label for="">ZipCode</label>
                <input type="search" class="form-control" wire:model.debounce.500ms="zipcode">
            </div>
            <div class="col-md-3">
                <label for="">City</label>
                <input type="search"  class="form-control" wire:model.debounce.500ms="city">
            </div>
            <div class="col-md-3">
                <label for="">Address</label>
                <input type="search"  class="form-control" wire:model.debounce.500ms="address">
            </div>
            <div class="col-md-3">
                <label for="">State</label>
                <select name="" id="" class="form-control" wire:model.debounce.500ms="state">
                    <option value="">Select State</option>
                    @foreach (states(30) as $state)
                        <option value="{{ $state->code }}"> {{ $state->code }} </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <table class="table">
                    <thead>
                        <th> Zip Code </th>
                        <th> City </th>
                        <th> State </th>
                        <th> Address </th>
                        <th> Neighborhood </th>
                    </thead>
                    <tbody>
                        @forelse ($zipcodes as $code)
                            <tr>
                                <td>{{ $code->zipcode }}</td>
                                <td>{{ $code->city }}</td>
                                <td>{{ $code->state }}</td>
                                <td>{{ $code->address }}</td>
                                <td>{{ $code->neighborhood }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="h4 text-center text-danger">Record not found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $zipcodes->links() }}
            </div>
        </div>
    </div>
    @include('layouts.livewire.loading')
</div>
