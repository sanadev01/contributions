<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Excel\Export\OrderExport;
use App\Http\Livewire\Order\Table;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderExportController extends Controller
{
    public function __invoke()
    {
        // $orders =  new Table();
        $orders = Order::where('status','>=',Order::STATUS_ORDER)
            ->has('user')->get();
        if (Auth::user()->isUser()) {
            $orders->where('user_id', Auth::id())->get();
        }
        // dd($orders);
        $trackingExportService = new OrderExport($orders);
        return $trackingExportService->handle();
    }
}
