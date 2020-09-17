@extends('layouts.master') 

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Checkout
                        </h4>
                        <a href="{{ route('admin.payment-invoices.index') }}" class="btn btn-primary">
                            Back to List
                        </a>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive-md mt-1">
                            
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
