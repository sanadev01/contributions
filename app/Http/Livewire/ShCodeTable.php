<?php

namespace App\Http\Livewire;

use App\Models\ShCode;
use Livewire\Component;

class ShCodeTable extends Component
{
    public $search = '';

    public function render()
    {
        $shCodes = ShCode::where('code', 'like', '%' . $this->search . '%')
            ->orWhere('description', 'like', '%' . $this->search . '%')
            ->orWhere('type', 'like', '%' . $this->search . '%')
            ->paginate(10);
        return view('livewire.sh-code-table', compact('shCodes'));
    }
}
