<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.profile.index') }}">
        <img src="{{ asset('images/icon/profile.svg') }}" alt="Profile">
        <span data-i18n="Apps"> @lang('menu.profile') </span>
    </a>
</li>
@user
    @php $user = Auth()->user(); @endphp
    @if(setting('amazon_sp', null, $user->id))
        <li class="nav-item mb-3" style="background: #70a6fa; color: #fff; font-weight: 700; font-size: 16px;">
            <a class="nav-link" href="{{ route('amazon.home') }}" style="color: #fff;">
                <img src="{{ asset('images/icon/amazon.svg') }}" alt="amazon api" width="20px">
                <span data-i18n="Apps"> Amazon SP Account</span>
            </a>
        </li>
    @endif
@enduser

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
@if(!auth()->user()->hideBoxControl())
@can('view_box_control')
<li class="nav-item">
    <a class="nav-link" target="__blank" href="https://app.ideainfo.com.br/index.php?app=boxcontrol">
        <img src="{{ asset('images/icon/box-control.svg') }}">

        <span data-i18n="Apps"> Box Control </span>
    </a>
</li>
@endcan
@endif
@can('view_label_post',)
<li class="nav-item">
    <a class="nav-link" target="__blank" href="https://labelposteasy.com/entre.php?tk={{ hash_hmac("sha256",Auth()->user()->email.Auth()->user()->pobox_number.date("YmdH" ,strtotime("now + 60 minutes")),'6a3db6e59e693493f3518d1b39e39dbb26730d2ce0ee1185a2e90ef025d1a5c7') }}&id={{ Auth()->user()->pobox_number }}">
      <img src="{{ asset('images/icon/label.svg') }}">
        <span data-i18n="Apps">Label Post</span>
    </a>
</li>
@endcan
@can('view_api_docs')
<li class="nav-item">
    <a class="nav-link" target="__blank" href="https://documenter.getpostman.com/view/16057364/TzeXmSxT">
        <img src="{{ asset('images/icon/api-docs.svg') }}">
        <span data-i18n="Apps"> @lang('menu.API Documents') </span>
    </a>
</li>
@endcan