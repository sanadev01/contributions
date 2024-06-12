<li class="dropdown dropdown-language nav-item">
    @if ( auth()->user()->locale == 'en' )
        <a class="dropdown-toggle nav-link" id="dropdown-flag" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="flag-icon flag-icon-us"></i>
            <span class="selected-language">English</span>
        </a>
    @elseif ( auth()->user()->locale == 'pt' )
        <a class="dropdown-toggle nav-link" id="dropdown-flag" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="flag-icon flag-icon-br"></i>
            <span class="selected-language">Portuguese</span>
        </a>
    @else
        <a class="dropdown-toggle nav-link" id="dropdown-flag" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="flag-icon flag-icon-cl"></i>
            <span class="selected-language">Spanish</span>
        </a>
    @endif
    @auth
    <div class="dropdown-menu" aria-labelledby="dropdown-flag">
        <a class="dropdown-item {{ auth()->user()->locale == 'en' ? 'active' :'' }}" href="{{ route('admin.locale.change','en') }}" data-language="en">
            <i class="flag-icon flag-icon-us"></i> English
        </a>
        <a class="dropdown-item {{ auth()->user()->locale == 'pt' ? 'active' :'' }}" href="{{ route('admin.locale.change','pt') }}" data-language="pt">
            <i class="flag-icon flag-icon-br"></i> Portuguese
        </a>
        <a class="dropdown-item {{ auth()->user()->locale == 'es' ? 'active' :'' }}" href="{{ route('admin.locale.change','es') }}" data-language="es">
            <i class="flag-icon flag-icon-cl"></i> Spanish
        </a>
    </div>    
    @endauth
    {{-- @guest
        <div class="dropdown-menu" aria-labelledby="dropdown-flag">
            <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' :'' }}" href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true)  }}" data-language="en">
                <i class="flag-icon flag-icon-us"></i> English
            </a>
            <a class="dropdown-item {{ app()->getLocale() == 'pt' ? 'active' :'' }}" href="{{ LaravelLocalization::getLocalizedURL('pt', null, [], true) }}" data-language="pt">
                <i class="flag-icon flag-icon-br"></i> Portuguese
            </a>
        </div>
    @endguest --}}
</li>
