<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.profile.index') }}">
        <img src="{{ asset('images/icon/profile.svg') }}" alt="Orders">
        <span data-i18n="Apps"> @lang('menu.profile') </span>
    </a>
</li>
@user
    @if(Auth()->user()->amazon_api_enabled)
        <li class="nav-item">
            <a class="nav-link" target="__blank" href="https://amazon.aleem.dev/api-token-login/{{ Auth()->user()->amazon_api_key }}">
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