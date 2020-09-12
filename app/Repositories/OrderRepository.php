<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class OrderRepository extends Model
{
    public function updateSenderAddress(Request $request, Order $order)
    {
        $order->update([
            'sender_first_name' => $request->first_name,
            'sender_last_name' => $request->last_name,
            'sender_email' => $request->email,
            'sender_taxId' => $request->phone,
        ]);

        return $order;
    }

    public function updateRecipientAddress(Request $request, Order $order)
    {

    }

    public function updateHandelingServices(Request $request, Order $order)
    {

    }

    public function createConsolidationRequest(Request $request, Order $order)
    {

    }

    public function updateShippingAndItems(Request $request, Order $order)
    {

    }
    

}
