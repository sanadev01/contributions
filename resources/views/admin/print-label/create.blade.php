@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                @section('title', __('orders.print-label.Scan Packages'))

                <div class="card-content collapse show">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <livewire:label.scan-label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('modal')
<x-modal />
@endsection
@section('js')
<script>
    function addClass(id) {
        $("#" + id).css({
            "background-color": "#77ff77"
        });
    }

    function removeCss() {
        $("#removeModal").removeClass('show');
        $("#removeModal").removeClass('d-block');
        $("#removeModal").addClass('hide');
        $("#removeModal").addClass('d-none');
    }
</script>
@endsection
