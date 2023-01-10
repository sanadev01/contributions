<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\SinerlogLabelRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class SinerlogHandleRepository
{
    public $order;
    public $request;
    public $error;

    public function __construct(Request $request,Order $order)
    {
        $this->order = $order;
        $this->request = $request;
        $this->error = null;
    }
    
    public function handle()
    {
        Log::info('Sinerlog label'); 
        /**
         * Variable to handle Sinerlog label creation
         */
        $labelSinerlogRep = new SinerlogLabelRepository();

        $renderLabel = $labelSinerlogRep->get($this->order);

        $this->order->refresh();

        $this->error = $labelSinerlogRep->getError();

        return $this->renderSinerlogLabel($this->request, $this->order, $this->error, $renderLabel);
    }

    public function renderSinerlogLabel($request, $order, $error, $renderLabel)
    {
        $buttonsOnly = $request->has('buttons_only');

        return view('admin.orders.label.label', compact('order', 'error', 'renderLabel', 'buttonsOnly'));
    }
}
