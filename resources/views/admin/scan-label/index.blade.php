@extends('layouts.master')

@section('page')
    <section id="vue-barcode">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                    @section('title', __('Scan Parcel'))
                    {{-- <h4 class="card-title">Scan Parcel</h4> --}}
                </div>
                <div class="card-content card-body">
                    <div class="mt-1">
                        <scan-label></scan-label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@push('js')
<script src="{{ mix('js/pages/barcode/reader.js') }}"></script>
@endpush
