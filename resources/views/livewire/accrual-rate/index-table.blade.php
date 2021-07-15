<div>
    <table class="table table-bordered table-responsive-md">
        <thead>
            <tr>
                <th>
                    Service
                </th>
                <th>
                    Action
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($services as $service)
            <tr>
                <th>
                    {{$service['name']}}
                <th>
                    <a href="{{ route('admin.rates.accrual-rates.show',$service['value']) }}" class="btn btn-success btn-sm">
                        View
                    </a>
                </th>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
