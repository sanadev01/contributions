@extends('layouts.master')

@section('page')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-form">BPS Rates</h4>
            <a href="{{ route('admin.rates.bps-leve.index') }}" class="btn btn-primary pull-right">
                Return to List
            </a>
            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body">
                <form class="form" action="{{ route('admin.rates.bps-leve.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-body">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <h4 class="form-section">Import BPS Leve Rates Excel</h4>
                            </div>
                        </div>
                        <div class="row justify-content-center">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Shipping Service <span class="text-danger">*</span></label>
                                        <select name="shipping_service_id" required class="form-control">
                                            @isset($shipping_services)
                                                @foreach ($shipping_services as $service)
                                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                @endforeach
                                            @endisset
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>Country <span class="text-danger">*</span></label>
                                        <select name="country_id" required class="form-control">
                                            @isset($countries)
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                            @endisset
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="projectinput1">Select Excel File to Upload</label>
                                    <input type="file" class="form-control" name="csv_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                    @error('csv_file')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="alert alert-warning">
                                    <ol>
                                        <li>* Carregar apenas arquivos do Excel</li>
                                        <li>* Arquivo maior que 15Mb não é permitido</li>
                                        <li>* Faça o download e preencha os dados no arquivo de exemplo abaixo para evitar erros</li>
                                        <li class="mt-2">* Download da amostra para taxas de bps <a href="{{ asset('uploads/bps/hd-leve.xlsx') }}" class="btn btn-success btn-sm">Download</a></li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions pl-5">
                        <a href="{{ route('admin.rates.bps-leve.index') }}" class="btn btn-warning mr-1 ml-3">
                            <i class="ft-x"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="la la-check-square-o"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
