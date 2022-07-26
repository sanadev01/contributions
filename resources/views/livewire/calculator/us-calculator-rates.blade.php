<div>
    <section id="vue-calculator">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-8">
                    <div class="card p-2">
                        <div class="card-header pb-0">
                            <h2 class="mb-2 text-center w-100">
                                Rate Calculated For {{ $shippingServiceTitle }}
                            </h2>
                        </div>
                        <div class="col-md-12">
                            <x-flash-message></x-flash-message>
                        </div>
                        <div class="card-body">
                            @if ($ratesWithProfit)
                                <div class="text-center">
                                    @foreach ($ratesWithProfit as $profitRate)
                                        <div class="card-body">
                                            <div class="row justify-content-center mb-2 full-height align-items-center">
                                                <div class="col-10">
                                                    <div class="row justify-content-center">
                                                        <div
                                                            class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                                            Service Name
                                                        </div>
                                                        <div class="border col-5 py-1">
                                                            {{ $profitRate['name'] }}
                                                        </div>
                                                    </div>
                                                    <div class="row justify-content-center">
                                                        <div
                                                            class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                                            Weight
                                                        </div>
                                                        <div class="border col-5 py-1">
                                                            @if ($tempOrder['measurement_unit'] == 'kg/cm')
                                                                {{ $chargableWeight }} Kg ( {{ $weightInOtherUnit }}
                                                                lbs)
                                                            @else
                                                                {{ $chargableWeight }} lbs ( {{ $weightInOtherUnit }}
                                                                kg)
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="row justify-content-center">
                                                        <div
                                                            class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                                            Cost
                                                        </div>
                                                        <div class="border col-5 py-1 text-danger h2">

                                                            {{ $profitRate['rate'] }} USD

                                                            <br>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    @endforeach
                                </div>
                                @if ($userLoggedIn)
                                    @if ($serviceResponse)
                                        <div class="row mb-1 ml-4">
                                            <div class="controls col-12">
                                            </div>
                                        </div>
                                    @endif
                                    @if ($error)
                                        <div class="row mb-1 ml-4">
                                            <div class="controls col-12 text-danger">
                                                {{ $error }}
                                            </div>
                                        </div>
                                    @endif
                                    <form wire:submit.prevent="getSenderLabel">
                                        <div class="row mb-1 ml-4">
                                            <div class="controls col-6">
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            @endif
                            <br>
                            <div class="row">
                                <div class="col-md-12 d-flex justify-content-center">
                                    <a href="@if ($shippingServiceTitle == 'UPS') {{ route('ups-calculator.index') }} @else {{ route('us-calculator.index') }} @endif"
                                        class="btn btn-primary btn-lg">
                                        Go Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @auth
                        @if (auth()->user()->hasRole('admin'))
                            <div class="card p-2">
                                <div class="card-header pb-0">
                                    <h2 class="mb-2 text-center w-100">
                                        Rate Calculated For {{ $shippingServiceTitle }} (without Profit)
                                    </h2>
                                </div>
                                <div class="col-md-12">
                                    <x-flash-message></x-flash-message>
                                </div>
                                <div class="card-body">
                                    @if ($apiRates)
                                        <div class="text-center">
                                            @foreach ($apiRates as $apiRate)
                                                <div class="card-body">
                                                    <div
                                                        class="row justify-content-center mb-2 full-height align-items-center">
                                                        <div class="col-10">
                                                            <div class="row justify-content-center">
                                                                <div
                                                                    class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                                                    {{ $shippingServiceTitle }}
                                                                </div>
                                                                <div class="border col-5 py-1">
                                                                    {{ $apiRate['name'] }}
                                                                </div>
                                                            </div>
                                                            <div class="row justify-content-center">
                                                                <div
                                                                    class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                                                    Weight
                                                                </div>
                                                                <div class="border col-5 py-1">
                                                                    @if ($tempOrder['measurement_unit'] == 'kg/cm')
                                                                        {{ $chargableWeight }} Kg (
                                                                        {{ $weightInOtherUnit }} lbs)
                                                                    @else
                                                                        {{ $chargableWeight }} lbs (
                                                                        {{ $weightInOtherUnit }} kg)
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="row justify-content-center">
                                                                <div
                                                                    class="pb-1 pt-1 border-bottom-light col-md-5 bg-primary text-white">
                                                                    Cost
                                                                </div>
                                                                <div class="border col-5 py-1 text-danger h2">

                                                                    {{ $apiRate['rate'] }} USD

                                                                    <br>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
        @include('layouts.livewire.loading')
    </section>
</div>
