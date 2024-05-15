<?php

namespace App\Repositories;

use App\Factories\LabelRepositoryFactory;
use App\Models\Order;
use Illuminate\Http\Request;

class HandleCorreiosLabelsRepository
{
    public $order;
    public $request;
    public $error;
    public $update;

    public function __construct(Request $request, Order $order)
    {
        $this->order = $order;
        $this->request = $request;
        $this->error = null;
        $this->update = $this->request->update_label  === 'false' ? false : true;
    }
    public function handle()
    {
        $labelRepository = LabelRepositoryFactory::create($this->order);
        if ($labelRepository) {
            $labelRepository->run($this->order, $this->update);
            return $this->renderLabel($this->request, $this->order, $labelRepository->getError());
        }
    }

    public function renderLabel($request, $order, $error)
    {
        $buttonsOnly = $this->request->has('buttons_only');
        return view('admin.orders.label.label', compact('order', 'error', 'buttonsOnly'));
    }
    

}
