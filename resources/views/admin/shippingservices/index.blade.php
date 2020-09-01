@extends('layouts.master')

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            Manage Shipping Services
                        </h4>
                        <a href="{{ route('admin.shipping-services.create') }}" class="btn btn-primary">
                            Create Shipping Service
                        </a>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive-md mt-1">
                            <table class="table table-hover-animation mb-0">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Max length allowed</th>
                                    <th>Max width allowed</th>
                                    <th>Min width allowed</th>
                                    <th>Min length allowed</th>
                                    <th>max sum of all sides</th>
                                    <th>Contains battery charges</th>
                                    <th>Contains perfume charges</th>
                                    <th>Contains flammable liquid charges</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($shippingservices as $service)
                                    <tr>
                                        <td>{{ $service->name }}</td>
                                        <td>{{ $service->max_length_allowed }}</td>
                                        <td>{{ $service->max_width_allowed }}</td>
                                        <td>{{ $service->min_width_allowed }}</td>
                                        <td>{{ $service->min_length_allowed }}</td>
                                        <td>{{ $service->max_sum_of_all_sides }}</td>
                                        <td>{{ $service->contains_battery_charges }}</td>
                                        <td>{{ $service->contains_perfume_charges }}</td>
                                        <td>{{ $service->contains_flammable_liquid_charges }}</td>
                                        <td>
                                            <a href="{{ route('admin.shipping-services.edit',$service) }}" title="Edit Service" class="btn btn-sm btn-primary mr-2">
                                                <i class="feather icon-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.shipping-services.destroy',$service) }}" method="POST" onsubmit="return confirmDelete()" class="d-inline-block">
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
