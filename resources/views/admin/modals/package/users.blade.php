<div class="container">
    <h3>{{ $package->name}} Users</h3>
    <div class="row">
        <div class="col-md-12">
            @if ($users)
                <ul class="list-group">
                    @foreach($users as $user)
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-md-4 border-right-warning">
                                {{ $user->name }}
                            </div>
                            <div class="col-md-4 border-right-warning">
                                {{ $user->pobox_number }}
                            </div>
                            <div class="col-md-4">
                                {{ $user->email }}
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>