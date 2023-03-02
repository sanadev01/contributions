<div>
    <h5 class="ml-5">Auto Charge Settings</h5>
    <div class="pl-3 pr-3 card-content">
        <div class="row col-12">
            <div class="col-3">
                <label>Auto charge Amount</label>
                <input type="number" wire:model.defer="charge_amount" min="0" class="form-control">
                @error('charge_amount')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="col-3">
               
                <label>When Balance less than</label>
                <input type="number" wire:model.defer="charge_limit" min="0" class="form-control">
                @error('charge_limit')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="col-3">
                <label> Billing information</label>
                <select class="form-control" wire:model="charge_biling_information" >
                    <option value="">Please Select</option>
                    @forelse (auth()->user()->billingInformations as $billingInfo)
                        <option value="{{ $billingInfo->id }}"
                            {{ setting('charge_biling_information', null, auth()->id()) ? 'selected' : '' }}>
                            **** **** **** {{ substr($billingInfo->card_no, -4) }}</option>
                    @empty
                        <option value="">No Record Found / Nenhum Registro Encontrado</option>
                    @endforelse
                </select>
                @error('charge_biling_information')
                    <div class="text-danger">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="col-3">
                <label>Auto debit authorization apply towards account balance </label><br>
                <input type="hidden" wire:model="charge">
                <div class="btn-group btn-toggle" id="btn-toggle">
                    <label class="AutoChargeSwitch" class="btn btn-primary" data-toggle="modal"
                        data-target="#exampleModal">
                        <input type="checkbox" @if ($charge) checked @endif>
                        <span class="AutoChargeSlider AutoChargeRound"></span>
                    </label>
                </div>


                {{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                    Open Form
                </button> --}}

                <!-- Modal -->
                <div wire:ignore.self class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Billing Confirmation</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true close-btn">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    HERCO FREIGHT DBA Homedeliverybr
                                    2200 NW 129th Ave, Miami, FL, 33182
                                    +1(305)888-5191
                                    Recurring Payment Authorization Form
                                    Schedule your payment to be automatically deducted from your Visa, MasterCard,
                                    American Express or
                                    Discover Card that is on file in the HERCO FREIGHT DBA Home DeliveryBR website.
                                </p>
                                <p>
                                    Here’s How Recurring Payments Work:
                                    You authorize regularly scheduled charges to your credit card on file, based on
                                    metrics you set up. You
                                    will be charged the amount indicated every time your balance hits the amount you
                                    determine.
                                    Each transaction may be viewed in your account, under ‘’activity’’. You may change
                                    the metrics or
                                    disable this function at your discretion.
                                </p>
                                <p>
                                    I  <u> &nbsp; {{auth()->user()->full_name}}  &nbsp;</u> authorize HERCO FREIGHT DBA Home DeliveryBR to charge
                                    my credit card
                                    that is on file account to the metrics I insert on my account.
                                </p>
                                <table>
                                    <tr>
                                        <td>Billing Address </td>
                                        <td>  <u> &nbsp; **** **** **** {{ substr($selected_card_no, -4) }}   &nbsp;</u>  </td>
                                        <td>Phone# </td>
                                        <td><u> &nbsp; {{auth()->user()->phone}}  &nbsp;</u> </td>
                                        
                                    </tr>
                                    <tr>
                                        <td>City, State, Zip</td>
                                        <td><u> {{auth()->user()->address}}  {{auth()->user()->city}}  {{auth()->user()->zipcode}} </u> </td> 
                                        <td>Email# </td>
                                        <td><u> &nbsp; {{auth()->user()->email}}  &nbsp;</u> </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"> </td> 
                                        <td>Date# </td>
                                        <td><u> &nbsp; {{ date('Y-m-d') }}  &nbsp;</u> </td>
                                        
                                    </tr>
                                </table>
                                <p> 
 
                                    I understand  that this authorization will remain in effect until I cancel it in writing or disable the function 
                                    on my HERCO FREIGHT DBA Home DeliveryBR account.
                                    I understand that not having sufficient funds in my account may effect my ability to generate labels and 
                                    pay for taxes and duties. 
                                    For ACH debits to my checking/savings account, I understand that because these are electronic 
                                    transactions, 
                                    these funds may be withdrawn from my account as soon as the above noted periodic transaction 
                                    dates. In the case of an ACH Transaction being rejected for Non Sufficient Funds (NSF)
                                    I understand that HERCO FREIGHT DBA  Home DeliveryBR may at its discretion attempt to process the 
                                    charge again within
                                    30 days, and agree to an additional charge for each attempt returned NSF which will be initiated as a 
                                    separate transaction from the authorized recurring payment.
                                    I understand that I will need to give written authorization in case additional funds may need to be 
                                    debited from my card,and exceed my current balance and or auto debit. 
                                    I acknowledge that the origination of ACH transactions to my account must comply with the provisions 
                                    of U.S. law. 
                                    I certify that I am an authorized user of this credit card/bank account and will not dispute these 
                                    scheduled
                                </p>
                                
                            </div>
                            <div class="modal-footer">
                                <div class="vs-checkbox-con vs-checkbox-danger"  >
                                    <input type="checkbox"  class="bulk-container"  wire:click.prevent="dismiss()" data-dismiss="modal">
                                    <span class="vs-checkbox vs-checkbox-lg">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-x"></i>
                                            
                                        </span>
                                    </span>
                                    <span class="h3 mx-2 text-primary my-0 py-0"></span>
                                    No
                                </div>
                                <div class="vs-checkbox-con vs-checkbox-primary" >
                                    <input type="checkbox" class="bulk-container" wire:click.prevent="save()" data-dismiss="modal" >
                                    <span class="vs-checkbox vs-checkbox-lg">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i> 
                                        </span>
                                    </span>
                                    <span class="h3 mx-2 text-primary my-0 py-0"></span>
                                    Yes
                                </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>
    <hr>
    @include('layouts.livewire.loading')
</div>

<script>
    window.addEventListener('alert', event => {
        toastr[event.detail.type](event.detail.message,
            event.detail.title ?? ''), toastr.options = {
            "closeButton": true,
            "progressBar": true,
        }
    });
</script>
