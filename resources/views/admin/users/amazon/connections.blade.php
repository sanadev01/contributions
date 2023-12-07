@extends('layouts.master')

@section('page')
<section id="prealerts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Amazon Selling Partner Connections</h4>
                </div>
                <div class="alert {{ Session::get('alert-class')}}" role="alert">
                    {{ Session::get('message') }}
                </div>

                <div class="card-content">
                    <div class="mt-1">
                        <div class="card-body">
                            <p class="card-title-desc">{{ __('Click to establish OAuth2.0 connection with Amazon\'s Selling Partner API') }}</p>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ url('auth') }}?region={{\App\Models\Marketplace::REGION_NA}}" type="button" class="btn btn-outline-primary mr-2 waves-effect waves-light">Connect [North America]</a>
                                <a href="{{ url('auth') }}?region={{\App\Models\Marketplace::REGION_EU}}" type="button" class="btn btn-outline-primary mr-2 waves-effect waves-light">Connect [Europe]</a>
                                <a href="{{ url('auth') }}?region={{\App\Models\Marketplace::REGION_FE}}" type="button" class="btn btn-outline-primary waves-effect waves-light">Connect [Far East]</a>
                            </div>

                            <div class="mt-4">

                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Seller Id</th>
                                            <th>Marketplace</th>
                                            <th>Last Updated At</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            @if($user->seller_id)
                                                <tr>
                                                    <td>
                                                        {{$user->seller_id}}
                                                    </td>
                                                    <td>
                                                        {{$user->marketplace->code}}
                                                    </td>
                                                    <td>
                                                        {{$user->updated_at}}
                                                    </td>
                                                    <td>
                                                        <a href="/status-change/{{$user->id}}" class="btn btn-sm btn-{{ $user->is_active ? 'danger' : 'success' }}">
                                                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        No Connection found !
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection