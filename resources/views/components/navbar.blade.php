<nav class="{{ $navbarClass }}">
    <div class="navbar-header d-xl-block d-none">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item">
                {{-- <a class="navbar-brand"> --}}
                    <h4 class="mb-0 pt-1">@yield('title')</h4>
                    {{-- <div class="brand-logo">@yield('title')</div> --}}
                {{-- </a> --}}
            </li>
        </ul>
    </div>
    <div class="navbar-wrapper">
        <div class="navbar-container content">
            <div class="navbar-collapse" id="navbar-mobile">
                @if(!Auth::user()->isActive())
                <div class="row col-9">
                    <div class="col-12">
                        <div class="alert alert-danger text-center">
                            <h3 class="text-danger">@lang('validation.Message')</h3> 
                        </div>
                    </div>
                </div>
                @endif
                <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                    <ul class="nav navbar-nav">
                        <li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ficon feather icon-menu"></i></a></li>
                    </ul>
                </div>
                <ul class="nav navbar-nav float-right">
                   <x-lang-switcher></x-lang-switcher>
                   <li class="nav-item d-none d-lg-block">
                    <a class="nav-link" onclick="darkMode()">
                        <i id="toggle" class="ficon feather icon-moon"></i>
                    </a>
                </li> 
                   <li class="nav-item d-none d-lg-block">
                        <a class="nav-link nav-link-expand">
                            <i class="ficon feather icon-maximize">
                                </i>
                            </a>
                        </li>

                    <li>
                    </li>
                    {{-- <x-web-search></x-web-search> --}}
                    {{--Notification--}}
                    {{-- <x-notification></x-notification> --}}
                    <x-user-profile></x-user-profile>
                </ul>
            </div>
        </div>
    </div>
</nav>
<script>
    function darkMode() {
        const allcards = document.querySelectorAll('.card');
        let layout = document.getElementById('toggle');
        console.log(layout.classList);
        if(layout.classList.contains('icon-moon'))
        {
            layout.classList.remove("icon-moon");
            layout.classList.add("icon-sun");
            const headings = document.getElementsByTagName("h6");
            document.getElementById("example").style.color = 'white';
            document.getElementById("example").style.border = 'red';
            let th = document.getElementById('th');
            th.classList.add('table-dark-th');
            document.getElementById("th").style.backgroundColor = '#1a1a3c ';
            document.getElementById("th").style.color = 'white ';
            document.getElementById("addressHeader").style.color = 'white ';
            // console.log(h6);
            for (let h6 of headings) {
                h6.classList.add('text-white');
            }
        
        }
        else
        {
            layout.classList.remove("icon-sun");
            layout.classList.add("icon-moon");
            const headings = document.getElementsByTagName("h6");
            document.getElementById("addressHeader").style.color = 'black ';
            document.getElementById("example").style.color = 'black';
            document.getElementById("th").style.backgroundColor = 'white ';
            document.getElementById("th").style.color = 'black';


            // console.log(h6);
            for (let h6 of headings) {
                h6.classList.remove('text-white');
            }
        }
      allcards.forEach(card => card.classList.toggle('card-dark'));
      document.body.classList.toggle('dark-mode');
      const cards = document.querySelectorAll('.overflow-hidden');
      cards.forEach(card => card.classList.toggle('card-dark'));
      const chartCard = document.querySelectorAll('.card.overflow-hidden');
      chartCard.forEach(card => card.classList.toggle('card-dark'));
      let figures = document.querySelectorAll('.figures');
      figures.forEach(card => card.classList.toggle('card-dark'));
      let sidebar = document.querySelectorAll('.main-menu');
      sidebar.forEach(card => card.classList.toggle('card-dark'));
      let nav = document.querySelectorAll('.main-menu-content');
      nav.forEach(card => card.classList.toggle('card-dark'));
      let navigation = document.querySelectorAll('.navigation');
      navigation.forEach(card => card.classList.toggle('card-dark'));
      let navShadow = document.querySelectorAll('.navbar-shadow');
      navShadow.forEach(card => card.classList.toggle('card-dark'));
      let navFloating = document.querySelectorAll('.header-navbar-shadow');
      navFloating.forEach(card => card.classList.toggle('navbar-dark'));
      let cardHeader = document.querySelectorAll('.');
      cardHeader.forEach(card => card.classList.toggle('h3'));

    }
  </script>
