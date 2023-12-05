@extends('layouts.master')

@section('page') 
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Amazon Selling Partner Connections</h4>
                    </div>
                    <div class="card-content">
                        <div class="mt-1">
                            <div class="card-body">
                                <p class="card-title-desc">{{ __('Click to establish OAuth2.0 connection with Amazon\'s Selling Partner API') }}</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ url('auth') }}?region={{\App\Models\Marketplace::REGION_NA}}" type="button"
                                    class="btn btn-outline-primary mr-2 waves-effect waves-light">Connect [North America]</a>
                                    <a href="{{ url('auth') }}?region={{\App\Models\Marketplace::REGION_EU}}" type="button"
                                    class="btn btn-outline-primary mr-2 waves-effect waves-light">Connect [Europe]</a>
                                    <a href="{{ url('auth') }}?region={{\App\Models\Marketplace::REGION_FE}}" type="button"
                                    class="btn btn-outline-primary waves-effect waves-light">Connect [Far East]</a>
                                </div>

                                <div class="table-responsive mt-2">
                                    {{-- @dd($data_table->table()) --}}
                                    {!! $data_table->table() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    @php $parameters = ['info' => false, 'paging' => false, 'scrollY' => false]; @endphp

    <script type="text/javascript">
        loadDataTable("{{$data_table->getTableId()}}", {!! json_encode($data_table->getDTParameters($parameters)) !!});

        $(document).on('click', '.btn-status', function (e) {
            e.preventDefault();
            let tableId = $(this).closest('.dt-wrapper').attr('id');
            $.get($(this).data('url'), {}, function (r) {
                redrawDataTable(tableId);
                success_alert(r.message);
            }).fail(function (r) {
                error_alert(r.message);
            });
        });
    </script>
@endpush

