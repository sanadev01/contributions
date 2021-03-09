<?php

namespace App\Http\Livewire\ImportExcel\Order;

use App\Models\ImportedOrder;
use App\Rules\PhoneNumberValidator;
use Livewire\Component;

class EditSender extends Component
{
    public $order;
    public $edit;

    public $sender_first_name;
    public $sender_last_name;
    public $sender_email;
    public $sender_phone;
    public $sender_taxId;

    public function mount($order, $edit= '')
    {
        $this->order = $order;
        $this->edit = $edit;
        
        $this->sender_first_name = old('sender_first_name',  $this->order->sender_first_name );
        $this->sender_last_name = old('sender_last_name', $this->order->sender_last_name);
        $this->sender_email = old('sender_email', $this->order->sender_email);
        $this->sender_phone = old('sender_phone', $this->order->sender_phone);
    }

    public function render()
    {
        return view('livewire.import-excel.order.edit-sender');
    }

    public function save()
    {
        $data = $this->validate($this->rules(), $this->messages());
        
        $error = $this->order->error;
        if($error){
            $remainError = array_diff($error, $this->messages());
            $error = $remainError ? $remainError : null;
        }

        $this->order->update([
            'sender_first_name' => $data['sender_first_name'],
            'sender_last_name' => $data['sender_last_name'],
            'sender_email' => $data['sender_email'],
            'sender_phone' => $data['sender_phone'],
            'error' => $error,
        ]);
    }

        /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'sender_first_name' => 'required|max:100',
            'sender_last_name' => 'max:100',
            'sender_email' => 'nullable|max:100|email',
            'sender_phone' => [
                'nullable','max:15','min:13'
            ],
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'sender_first_name.required' => 'sender first name is required',
            'sender_last_name.nullable' => 'sender last name is required',
            'sender_email.nullable' => 'sender Email is required',
            'sender_phone.nullable' => 'sender phone is required',
        ];
    }
}
