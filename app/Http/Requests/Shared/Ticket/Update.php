<?php

namespace App\Http\Requests\Shared\Ticket;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return  $this->route('ticket') && $this->route('ticket')->isOpen() && ($this->route('ticket')->user_id == Auth::id() || \auth()->user()->can('update',$this->route('ticket')));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'text' => 'required|min:20'
        ];
    }
}
