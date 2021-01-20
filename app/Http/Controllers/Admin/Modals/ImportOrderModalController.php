<?php

namespace App\Http\Controllers\Admin\Modals;

use App\Http\Controllers\Controller;
use App\Models\ImportedOrder;
use Illuminate\Http\Request;

class ImportOrderModalController extends Controller
{
    public function __invoke(ImportedOrder $order)
    {
        
        return view('admin.modals.orders.edit',compact('order'));
    }
}
