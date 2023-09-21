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
                    <a href="{{ route('admin.rates.show-accrual-rates',$service['value']) }}" class="btn btn-primary btn-sm">
                       <i class="feather icon-eye"></i> View
                    </a>
                    |
                    <a href="{{ route('admin.rates.download-accrual-rates',$service['value']) }}" class="btn btn-success btn-sm">
                        <i class="feather icon-download"></i> Download
                    </a>
                </th>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
