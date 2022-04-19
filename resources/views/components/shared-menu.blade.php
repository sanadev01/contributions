<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.profile.index') }}">
        <img src="{{ asset('images/icon/profile.svg') }}" alt="Orders">
        <span data-i18n="Apps"> @lang('menu.profile') </span>
    </a>
</li>
@user
    @if(Auth()->user()->amazon_api_enabled)
        <li class="nav-item">
            <a class="nav-link" target="__blank" href="https://app.labelsup.com/api-token-login/@if(Auth()->user()->amazon_api_key){{ '?token='.Auth()->user()->amazon_api_key }}@else{{'?first_name='.Auth()->user()->name.'&last_name='.Auth()->user()->last_name.'&email='.Auth()->user()->email.'&phone='.Auth()->user()->phone.'&api_token='.Auth()->user()->api_token}}@endif">
                <i class="fa fa-list-alt" style="color: #90f3bc;"></i>
                <span data-i18n="Apps"> Amazon Fulfilment</span>
            </a>
        </li>
    @endif
@enduser
<li class="nav-item">
    <a class="nav-link" target="__blank" href="https://documenter.getpostman.com/view/16057364/TzeXmSxT">
        <i class="fa fa-list-alt" style="color: #28c76f;"></i>
        <span data-i18n="Apps"> @lang('menu.API Documents') </span>
    </a>
</li>