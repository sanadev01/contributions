@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Manage Services
                        </h4>
                        <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                            Create Service
                        </a>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive-md mt-1">
                            <table class="table table-hover-animation mb-0">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Cost</th>
                                    <th>Price</th>
                                    <th>Profit</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($services as $service)
                                    <tr>
                                        <td>{{ $service->name }}</td>
                                        <td>
                                            {{ $service->cost }} USD
                                        </td>
                                        <td>
                                            {{ $service->price }} USD
                                        </td>
                                        <td>
                                            {{ $service->price -$service->cost  }} USD
                                        </td>
                                        
                                        <td>
                                            <a href="{{ route('admin.services.edit',$service) }}" title="Edit Service" class="btn btn-sm btn-primary mr-2">
                                                <i class="feather icon-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.services.destroy',$service) }}" method="POST" onsubmit="return confirmDelete()" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button title="Delete Service" class="btn btn-sm btn-danger mr-2">
                                                    <i class="feather icon-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
