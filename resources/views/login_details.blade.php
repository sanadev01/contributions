@extends('layouts.master')
@section('page')
<div class="container mt-5">
    <h2 class="mb-4">User Risk Details</h2>
    <table>
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Unique Ips</th>
                    <th>Unique Device</th>
                    <th>Total Attempts</th>
                    <th>Successful Attempts</th>
                    <th>Risks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($userRisk as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>

                    <td>{{ $detail->user->name ?? 'Unknown User' }}</td>
                    <td>
                        <span class="badge badge-primary">
                            {{ $detail->unique_ips }}
                        </span>
                    </td>

                    <td>
                        <span class="badge badge-primary">
                            {{ $detail->unique_devices }}
                        </span>
                    </td>

                    <td>
                        <span class="badge badge-primary">
                            {{ $detail->total_attempts }}
                        </span>
                    </td>

                    <td>
                        <span class="badge badge-success">
                            {{ $detail->successful_attempts }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-{{ strtolower($detail->risk)=='high'?'danger':(strtolower($detail->risk)=='low'?'info':'warning') }}">
                            {{ $detail->risk }}
                        </span>
                    </td>
                </tr>
                @endforeach

        </table>

        <h2 class="my-4 mt-5">User Login Details</h2>
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>IP Address</th>
                    <th>Device</th>
                    <th>Location</th>
                    <th>Attempted At</th>
                    <th>Successful</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loginDetails as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->user->name ?? 'Unknown User' }}</td>
                    <td>{{ $detail->ip_address }}</td>
                    <td>{{ $detail->device }}</td>
                    <td>{{ $detail->location }}</td>
                    <td>{{ $detail->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>
                        <span class="badge badge-{{ $detail->successful ? 'success' : 'danger' }}">
                            {{ $detail->successful ? 'Yes' : 'No' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No login details found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination Links (if using pagination) -->
        @if($loginDetails->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $loginDetails->links() }}
        </div>
        @endif
</div>

@endsection