@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-end">
                    @section('title', 'Create Delivery Bill')
                    <a href="{{ route('warehouse.delivery_bill.index') }}"
                        class="pull-right btn btn-primary">@lang('warehouse.deliveryBill.List Delivery Bills')</a>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form action="{{ route('warehouse.delivery_bill.store') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf

                            <table class="table table-bordered">
                                <tr>
                                    <th>
                                        <input type="checkbox" class="form-control" value="" id="selectAll">
                                    </th>
                                    <th>Dispatch Code</th>
                                    <th>Seal #</th>
                                    <th>Distribution Service Class</th>
                                    <th>Weight/Pieces</th>
                                </tr>

                                @foreach ($containers as $container)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="container[]" class="form-control container"
                                                value="{{ $container->id }}">
                                        </td>
                                        <td>
                                            {{ $container->dispatch_number }}
                                        </td>
                                        <td>
                                            {{ $container->seal_no }}
                                        </td>
                                        <td>
                                            {{ $container->getServiceSubClass() }}
                                        </td>
                                        <td>
                                            {{ $container->getWeight() }} KG / {{ $container->getPiecesCount() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                            <hr class="mx-5 mt-5">
                            <div class="row mt-1">
                                <div class="col-md-9 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                    <button type="submit"
                                        class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light btn-lg">
                                        @lang('warehouse.containers.Save')
                                    </button>
                                    {{-- <button type="reset" class="btn btn-outline-warning waves-effect waves-light">@lang('role.Reset')</button> --}}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('js')
<script>
    $('#selectAll').on('click', function() {
        if ($(this).prop('checked') == true) {
            $('.container').prop('checked', true);
        } else {
            $('.container').prop('checked', false);
        }


    });
</script>
@endpush
