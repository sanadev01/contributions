<?php


namespace App\Http\Controllers\Admin\Deposit;


use App\Http\Controllers\Controller;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index()
    {
        return view('admin.deposit.index');
    }

    public function create()
    {
        return view('admin.deposit.create');
    }

    public function store(Request $request, OrderRepository $orderRepository)
    {
        if ( $orderRepository->checkout($request) ){
            session()->flash('alert-success', __('orders.payment.alert-success'));
            return redirect()->route('admin.payment-invoices.index');
        }

        session()->flash('alert-danger',$orderRepository->getError());
        return \back()->withInput();

    }
}
