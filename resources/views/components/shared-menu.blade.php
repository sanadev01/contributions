@user
<li class="sub-category"> <span>Apps</span> </li>

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
        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="1em" height="1em"
            preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24"><path fill="none"
            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
            stroke-width="2" d="M6 4h8a4 4 0 0 1 4 4a4 4 0 0 1-4 4H6zm0 8h9a4 4 0 0 1 4 4a4 4 0 0 1-4 4H6z"/>
        </svg>
        <span class="menu-title"> Box Control </span>
    </a>
</li>
@endcan
@endif
@can('view_label_post',)
<li class="nav-item">
    <a class="nav-link" target="__blank" href="https://labelposteasy.com/entre.php?tk={{ hash_hmac("sha256",Auth()->user()->email.Auth()->user()->pobox_number.date("YmdH" ,strtotime("now + 60 minutes")),'6a3db6e59e693493f3518d1b39e39dbb26730d2ce0ee1185a2e90ef025d1a5c7') }}&id={{ Auth()->user()->pobox_number }}">
        <svg viewBox="0 0 24 24" height="15" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
            <polyline points="14 2 14 8 20 8"></polyline>
            <line x1="16" y1="13" x2="8" y2="13"></line>
            <line x1="16" y1="17" x2="8" y2="17"></line>
            <polyline points="10 9 9 9 8 9"></polyline>
        </svg>        <span data-i18n="Apps">Label Post</span>
    </a>
</li>
@endcan

{{-- 
@can('view_api_docs')
    <li class="nav-item">
    <a class="nav-link" target="__blank" href="https://documenter.getpostman.com/view/16057364/TzeXmSxT">
        <i class="fa fa-list-alt" style="color: #28c76f;"></i>
        <span data-i18n="Apps"> @lang('menu.API Documents') </span>
    </a>
</li> 
@endcan--}}
