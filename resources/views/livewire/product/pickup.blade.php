<div>
    <div class="col-12 row mb-5">
        <div class="form-group row col-4">
            <label class="col-2 text-right">@lang('inventory.Scan SKU')</label>
            <input type="text" class="form-control col-8" wire:model.debounce.500ms="search">
            @error('search')
                <span class="text-danger ml-5">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group row col-4" wire:ignore>
            <select class="test form-control form-select text-uppercase select2-size-sm">
                <option value="1">Test</option>
                <option value="2">Test</option>
                <option value="3">Test</option>
            </select>
        </div>
    </div>
</div>
@push('js')
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>

{{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
<script>
    </script>
@endpush
