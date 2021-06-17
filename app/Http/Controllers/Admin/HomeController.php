<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Picqer\Barcode\BarcodeGeneratorPNG;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        
       
        $order = Order::where('id', 66804)->with('recipient', 'items')->first();
        $chile_response = json_decode($order->chile_response);
        $description = $this->itemsDescription($order->items);
        $date = \Carbon\Carbon::parse($order->updated_at)->format('d/m/Y H:i');

        // dd($order->toArray());
        return view('labels.chile.index', [
            'order' => $order,
            'chile_response' => $chile_response,
            'description' => $description,
            'date' => $date,
            'barcodeNew' => new BarcodeGeneratorPNG(),
        ]);
        // return view('home');   
    }

    public function itemsDescription($items)
    {
        foreach($items as $item)
        {
            $itemDescription[] = $item->description;
        }

        $description = implode(" ", $itemDescription);

        return $description;
    }
}
