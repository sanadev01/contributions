<?php

namespace App\View\Components;

use Illuminate\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;


class UserMenu extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.user-menu');
    }

    public function isActive($route)
    {
        if (is_array($route)) {
            foreach ($route as $r) {
                if (request()->routeIs($r)) {
                    return 'active new-active ';
                }
            }

            return '';
        }

        return request()->routeIs($route) ? 'active new-active ' : '';
    }

    public function header()
    {
        return '
            <div class="navbar-header expanded mb-2">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item mr-auto">
                        <a class="navbar-brand" href="/">
                            <img src="'.asset('images/hd-logo.png').'" class="mb-0" style="width:80%;"/>
                        </a>
                    </li>
                    <li class="nav-item nav-toggle" style="position: absolute;right: 10px;">
                        <a class="nav-link modern-nav-toggle pr-0 shepherd-modal-target" data-toggle="collapse">
                            <i class="icon-x d-block d-xl-none font-medium-4 primary toggle-icon feather icon-disc"></i>
                            <i class="toggle-icon icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary feather" data-ticon="icon-disc" tabindex="0"></i>
                        </a>
                    </li>
                </ul>
            </div>
        ';
    }
}
