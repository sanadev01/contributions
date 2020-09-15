@extends('admin.orders.layouts.wizard')

@section('wizard-form')
    <section class="invoice-print mb-1">
        <div wire:id="MhlECvHN71T5Xdylc5Vi">
      
            <div class="row">
                <fieldset class="col-12 col-md-5 mb-1 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-append" id="button-addon2">
                            <button wire:click="sendEmail" class="btn btn-outline-primary waves-effect waves-light" type="button">Send Invoice</button>
                        </div>
                    </div>
                </fieldset>
                <div class="col-12 col-md-7 d-flex flex-column flex-md-row justify-content-end">
                    <button class="btn btn-primary btn-print mb-1 mb-md-0 waves-effect waves-light" onclick="mw = window.open('https://app.homedeliverybr.com/orders/2152/receipt','','width=768,height=768');"> <i class="feather icon-file-text"></i> Print</button>
                </div>
            </div>
        </div>
    </section>
    <div id="invoice-wrapper">
    <!-- invoice functionality end -->
    <!-- invoice page -->
    <section class="card invoice-page">
        <div id="invoice-template" class="card-body">
            <!-- Invoice Company Details -->
            <div id="invoice-company-details" class="row">
                <div class="col-sm-6 col-12 text-left pt-1">
                    <div class="media pt-1">
                        <img src="https://app.homedeliverybr.com/images/hd-logo.png" alt="Home Deliverybr">
                    </div>
                </div>
                <div class="col-sm-6 col-12 text-right">
                    <h1>Invoice</h1>
                    <div class="invoice-details mt-2">
                        <h6>INVOICE NO.</h6>
                        <p>TEMPWHR-2384</p>
                        <h6 class="mt-2">INVOICE DATE</h6>
                        <p>15 Sep 2020</p>
                    </div>
                </div>
            </div>
            <!--/ Invoice Company Details -->

            <!-- Invoice Recipient Details -->
            <div id="invoice-customer-details" class="pt-2 d-flex w-100 justify-content-between">
                <div class="text-left w-50">
                    <h5>Recipient</h5>
                    <div class="recipient-info my-2">
                        <p> Gilberto </p>
                        <p> HERCO 0013 </p>
                        <p>2200 NW, 129th Ave – Suite # 100<br> Miami, FL, 33182<br>United States<br>Ph#: +13058885191</p>
                    </div>
                    <div class="recipient-contact pb-2">
                        <p>
                            <i class="feather icon-mail"></i>
                            contato@importadoslaiaenvia.com
                        </p>
                        <p>
                            <i class="feather icon-phone"></i>
                            +5531996683266
                        </p>
                    </div>
                </div>
                <div class="text-righ justify-self-end">
                    <h5>Home Delivery Br</h5>
                    <div class="company-info my-2">
                        2200 NW, 129th Ave – Suite # 100<br> Miami, FL, 33182<br>United States<br>Ph#: +13058885191
                    </div>
                    
                </div>
            </div>
            <!--/ Invoice Recipient Details -->

            <!-- Invoice Items Details -->
            <div id="invoice-items-details" class="pt-1 invoice-items-table">
                <div class="row">
                    <div class="table-responsive-md col-12">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Light Service</td>
                                    <td>85.70 USD</td>
                                </tr>
                                                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div id="invoice-total-details" class="invoice-total-table">
                <div class="row">
                    <div class="col-7 offset-5">
                        <div class="table-responsive-md">
                            <table class="table table-borderless">
                                <tbody>
                                    
                                    <tr>
                                        <th>TOTAL</th>
                                        <td>85.70USD</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Footer -->
            
            <!--/ Invoice Footer -->
        </div>
    </section>
</div>
@endsection
