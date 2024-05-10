<nav class="{{ $navbarClass }}">
    <div class="row col-md-3">
        <span class="btn" data-toggle="modal" data-target="#popupModal">
            <span style="font-weight: bold; font-size: 1.2em; color:#EA5455;">Disclaimer</span> <i class="fa fa-info" aria-hidden="true"></i>
        </span>
    </div>
    <div class="navbar-header d-xl-block d-none">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item">
                <a class="navbar-brand" href="/">
                    <div class="brand-logo"></div>
                </a>
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
                    <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i class="ficon feather icon-maximize"></i></a></li>

                    {{-- <x-web-search></x-web-search> --}}
                    {{--Notification--}}
                    {{-- <x-notification></x-notification> --}}
                    <x-user-profile></x-user-profile>
                </ul>
            </div>
        </div>
    </div>
</nav>
<!-- The Modal -->
<div class="modal" id="popupModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #EA5455;">
                <h5 class="modal-title text-white">Disclaimer</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>
                    <strong>Amount included</strong> (DDP - Delivered Duty Paid)
                    Sender of the package pays for <strong>import taxes and duties</strong>.
                    Import tax and duty charges will be included in the <strong>Total Charge.</strong>
                </p>
                <p>
                    If customs determines that the actual value of the goods in the package is higher than declared,
                    <strong>import tax and duty charges</strong> will increase.
                </p>
                <p>
                    <strong>Amount to be paid by receiver</strong> (DDU- Delivered Duty Unpaid)
                    Receiver will have to pay indicated amount for <strong>import taxes and duties</strong>.
                    In addition, a courier-specific handling fee may apply.
                </p>
                <p>
                    The risk is that the receiver may reject the package if he/she is unhappy with
                    <strong>import taxes and duties charges.</strong>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>