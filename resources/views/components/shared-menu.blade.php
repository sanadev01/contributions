@user
<li class="sub-category"> <span  style="padding-left:16px; padding-top:10px;">Apps</span> </li>

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
