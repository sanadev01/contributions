<div class="col-md-6">
    <div class="card">
        <div class="card-header d-flex align-items-start pb-0">
            <div>
                <h1 class="text-bold-700 mb-0">$ {{ $commission? number_format($commission, 2): 0 }}</h1>
                <p>@lang('affiliate-dashboard.Commission')</p>
            </div>
            <div class="avatar bg-rgba-success p-50 m-0">
                <div class="avatar-content">
                    <i class="feather icon-dollar-sign text-success font-medium-5"></i>
                </div>
            </div>
        </div>
    </div>
</div>
