@extends('layouts.app')
@section('content')
    @livewire('calculator.us-calculator-rates', [
        'apiRates' => $apiRates, 
        'ratesWithProfit' => $ratesWithProfit,
        'tempOrder' => $tempOrder,
        'weightInOtherUnit' => $weightInOtherUnit,
        'chargableWeight' => $chargableWeight,
        'userLoggedIn' => $userLoggedIn,
        'shippingServiceTitle' => $shippingServiceTitle,
    ])
@endsection