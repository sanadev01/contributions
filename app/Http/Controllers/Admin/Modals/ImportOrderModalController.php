<?php

namespace App\Http\Controllers\Admin\Modals;

use App\Http\Controllers\Controller;
use App\Models\ImportedOrder;
use Illuminate\Http\Request;

class ImportOrderModalController extends Controller
{
    public function edit(ImportedOrder $error, $edit = '')
    {
        $order = $error;
        return view('admin.modals.import-order.edit',compact('order', 'edit'));
    }

    public function show(ImportedOrder $error)
    {
        $order = $error;
        return view('admin.modals.import-order.error',compact('order'));
    }
}
