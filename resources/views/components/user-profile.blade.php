<li class="dropdown dropdown-user nav-item" title="@lang('profile.Edit Profile')">
    <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
        <div class="user-nav d-sm-flex d-none">
            <span class="user-name text-bold-600">
                {{ auth()->user()->name }}
            </span>
            <span class="user-status">{{ auth()->user()->pobox_number }}</span>
        </div>
        <span>
            <img class="round" src="{{ auth()->user()->getImage() }}" alt="avatar" height="40" width="40">
        </span>
    </a>
    <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="{{ route('admin.profile.index') }}">
            <i class="feather icon-user"></i> @lang('user.Edit Profile')
        </a>
        <div class="dropdown-divider"></div>
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button class="dropdown-item w-100">
                <i class="feather icon-power"></i> @lang('user.Logout')
            </button>
        </form>
    </div>
</li>
 