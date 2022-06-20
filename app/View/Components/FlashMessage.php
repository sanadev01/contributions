<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Session;

class FlashMessage extends Component
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
        return view('components.flash-message');
    }

    public function messages()
    {
        $possibleMessages = [
            'alert-success',
            'alert-error',
            'alert-info',
            'alert-danger',
            'alert-warning'
        ];

        $availableMessages = [];

        foreach ($possibleMessages as $message) {
            if (Session::has($message)) {
                $availableMessages[$message] = Session::get($message);
            }
        }

        return $availableMessages;
    }

    public function name($message)
    {
        $parts = explode('-', $message);

        if (count($parts) > 1) {
            return ucfirst(__("alerts.{$parts[1]}"));
        }

        return ucfirst(__("alerts.{$parts[0]}"));
    }
}
