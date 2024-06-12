@extends('layouts.master')

@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card min-vh-100">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Balance Transactions
                        </h4>
                        <a href="{{ route('admin.deposit.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Add Balance
                        </a>
                    </div>
                    <div class="card-content card-body">
                        <div class="table-responsive-md mt-1" >
                            <livewire:deposit.table-component />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('modal')
    <x-modal/>
@endsection


