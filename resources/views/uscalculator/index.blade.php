@extends('layouts.app')
@section('content')
    @livewire('calculator.us-calculator-rates', [
        'apiRates' => $apiRates, 
        'ratesWithProfit' => $ratesWithProfit,
        'order' => $order,
        'weightInOtherUnit' => $weightInOtherUnit,
        'chargableWeight' => $chargableWeight,
        'userLoggedIn' => $userLoggedIn,
        'shippingServiceTitle' => $shippingServiceTitle,
    ])
@endsection