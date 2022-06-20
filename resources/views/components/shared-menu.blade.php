<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.profile.index') }}">
        <img src="{{ asset('images/icon/profile.svg') }}" alt="Profile">
        <span data-i18n="Apps"> @lang('menu.profile') </span>
    </a>
</li>
@user
    @php $user = Auth()->user(); @endphp
    @if($user->amazon_api_enabled && $user->api_enabled && $user->api_token)
        <li class="nav-item" style="background: #f79400;color: #fff;font-weight: 700;font-size: 16px;">
            <a class="nav-link" target="__blank"
             href="https://app.labelsup.com/api-token-login?@if($user->amazon_api_key){{ 'token='.$user->amazon_api_key }}@else{{'first_name='.$user->name.'&last_name='.$user->last_name.'&email='.$user->email.'&phone='.$user->phone.'&api_token='.$user->api_token.'&city='.$user->city.'&street_no='.$user->street_no.'&address='.$user->address.'&address2='.$user->address2.'&zipcode='.$user->zipcode.'&state_id='.$user->state_id.'&country_id='.$user->country_id}}@endif">
             <img src="{{ asset('images/icon/amazon.svg') }}" alt="amazon api" width="20px">
                <span data-i18n="Apps"style="color: #fff;"> Amazon Fulfilment</span>
            </a>
        </li>
    @endif
@enduser
<li class="nav-item">
    <a class="nav-link" target="__blank" href="https://app.ideainfo.com.br/index.php?app=boxcontrol">
        <i class="fa fa-bold" style="color: #f4d03e;"></i>
        <span data-i18n="Apps"> Box Control </span>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" target="__blank" href="https://labelposteasy.com/entre.php?tk={{ md5("HERCO123".Auth()->user()->email.date("YmdH",strtotime("now + 60 minutes"))) }}">
        <i class="fa fa-file-powerpoint-o" style="color: #28c76f;"></i>
        <span data-i18n="Apps">Label Post</span>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" target="__blank" href="https://documenter.getpostman.com/view/16057364/TzeXmSxT">
        <i class="fa fa-list-alt" style="color: #28c76f;"></i>
        <span data-i18n="Apps"> @lang('menu.API Documents') </span>
    </a>
</li>