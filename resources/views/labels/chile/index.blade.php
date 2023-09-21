<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        
        @page {
            /* width height */
            size: 9cm 7.3cm; 
            margin: 0px;
            padding: 0px;
        }
        *{
            font-family: Arial, Helvetica, sans-serif;
            box-sizing:border-box !important;
        }

        .circle {
            width: 0.5cm !important;
            height: 0.5cm !important;
            line-height: 100px;
            border-radius: 50%; /* the magic */
            -moz-border-radius: 50%;
            -webkit-border-radius: 50%;
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            margin: 2px auto 6px;
            background-color: green;
        }
        .square {
            height: 0.5cm !important; 
            width: 0.5cm !important; 
            background-color: red; 
            margin: auto !important; 
            position: absolute; 
            top: 1.9px !important; 
            left: 1.9px !important; 
            right: 0 !important; 
            bottom: 0 !important; 
            margin: auto !important;
        }
        
    </style>
</head>
<body style="margin-bottom: 0px !important;">
    <div >
      <table style="width:100%; height:7cm;  margin: 0.07cm;" cellspacing="0" border="1" cellpadding="0">
        <tr style="height: 0.283cm; font-size: 7px !important;">
            <td rowspan="3" style="text-align: center">
                <img src="{{ asset('images/corrieos-chile-logo.png') }}" alt="" style="height: 0.85cm;">
            </td>
            <td style="text-align: center;">
                <small>A.R.</small>
            </td>
            <td style="text-align: center;"><small>RDOC</small></td>
            <td style="text-align: center;"><small>A.TEL</small></td>
            <td style="text-align: center;"><small>P.D</small></td>
            <td style="text-align: center;"><small>REEM</small></td>
            <td style="text-align: center;"><small>PRG</small></td>
            <td style="text-align: center;"><small>SAB</small></td>
            <td style="text-align: center;"><small>FIVPS</small></td>
        </tr>
        <tr style="height: 0.283cm; font-size: 7px !important;">
            <td colspan="8" style="font-size: 7px; padding-bottom: 2px !important">DE: <small style="font-size: 7px;">HERCO INC</small></td>
        </tr>
        <tr style="height: 0.283cm; font-size: 7px !important;">
            <td colspan="3" style="border-right: none !important; border-left: none !important;">
                <small style="font-size: 7px; font-weight: bold !important">RUT :</small>
            </td>
            <td colspan="2" style="border-right: none !important; border-left: none !important;">
                <small style="font-size: 7px; font-weight: bold !important">CTA :</small><small>{{$clienteRemitente}}</small>
            </td>
            <td colspan="3" style="border-right: 1px solid !important; border-left: none !important;">
                <small style="font-size: 7px; font-weight: bold !important">Tel : </small><small>{{$order->sender_phone}}</small>
            </td>
        </tr>
        <tr style="height: 0.33cm !important; max-height: 0.33cm !important;">
            <td rowspan="2" style="text-align: center; font-size: 6px !important;">
                <small style="font-size: 3px;">{{$date}}</small><br />
                <small style="font-size: 12px !important;">{{$chile_response->AbreviaturaServicio}}</small>
            </td>
            <td colspan="8" style="height: 0.5px !important; line-height: 3.3px !important; padding-top: 1.5px !important;"><small style="font-size: 7px;">Referencia : @if (strlen($order->customer_reference) > 20) {{str_limit($order->customer_reference, 17)}} @else {{ $order->customer_reference}} @endif <span style="margin-left: 30px !important;">{{$order->warehouse_number}}<span></small></td>
        </tr>
        <tr style="height: 0.33cm !important; line-height: 5px !important; max-height: 0.33cm !important;">
            <td colspan="6">
                <small style="font-size: 6px !important; font-weight: bold !important;">Description Del Production :</small><br>
                <small style="font-size: 6px !important; line-height: 6px !important;">{{$description}}</small>
            </td>
            <td colspan="2" style="border-right: 1px solid;">
                <small style="font-size: 5px; font-weight: bold !important;">Valor Declarado USD:</small><br />
                <small style="font-size: 6px !important; margin: 19px !important; line-height: 6px !important;">{{ number_format($order->order_value, 2)}}</small>
            </td>
        </tr>
        <tr style="height: 3cm;">
            <td colspan="9">
                <img src="data:image/png;base64,{{ base64_encode($barcodeNew->getBarcode($bar_code, $barcodeNew::TYPE_CODE_128, 1,94, [0,0,0]))}}" alt="barcode" style="height: 2.1cm !important; width: 6.5cm !important; margin: 0.4cm 1.4cm 0.4cm 1.3cm !important;"   />
            </td>
        </tr>
        <tr style="line-height: 10px;">
            <td colspan="1" style="text-align: center;"><small style="font-size: 7px;">Encaminamiento</small><br /><small style="font-size: 7px;">{{$chile_response->CodigoEncaminamiento}}</small></td>
            <td colspan="6"><small style="font-size: 7px; display: block !important">N Envio :</small> <small style="font-size: 12px; font-weight: bold; padding-left: 33px;">{{$chile_response->NumeroEnvio}}</small></td>
            <td colspan="2" style="text-align: center;"><small style="font-size: 7px;">Bulto(s)</small><br /><small style="font-size: 10px !important">001</small></td>
        </tr>
        <tr style="height: 1cm !important;">
            <td colspan="9">
                <table style="font-size: 7px; width:100%">
                    <tr>
                        <td style="font-weight: bold;">
                            A :
                        </td>
                        <td style="font-weight: bold;">
                            {{$chile_response->ComunaDestino}}
                        </td>
                        <td style="text-align: right !important;">
                            Para :
                        </td>
                        <td style="@if (strlen($recipient_name) > 15) font-size: 6px !important; @endif">
                            {{ $recipient_name }}
                        </td>
                        <td rowspan="3">
                            <div style="height: 0.6cm !important; width: 0.6cm !important; position: relative !important; float: right !important; margin-top: 4px !important;">
                                @if($order->recipient->region == '214')   {{-- 214 is region code of Santiago metropolitan region--}}
                                    <div class="circle"></div>
                                @else
                                    <div class="square"></div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">
                            Dir. :
                        </td>
                        <td style="font-weight: bold; @if (strlen($chile_response->DireccionDestino) > 20)  font-size: 5px !important; @endif">
                            {{ str_limit($chile_response->DireccionDestino, 30) }}
                        </td>
                        
                        <td style="text-align: right !important;">
                            Tel.:
                        </td>
                        <td style="text-align: left !important;">
                            {{$order->recipient->phone}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Obs.:
                        </td>
                        <td colspan="6" style="@if(strlen($order->recipient->address2) > 30) font-size: 6px !important; @endif">
                            {{str_limit($order->recipient->address2, 70)}}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="9" style="padding: 0px !important;">
                <table cellspacing="0" cellpadding="0" style="margin: 0px !important;">
                    <tr>
                        <td style="border-right: 1px solid; height:0.2cm; width: 58px; text-align: center;">
                            <small style="font-size: 7px;">SDP</small>
                        </td>
                        <td style="border-right: 1px solid; height:0.2cm; width: 150px; text-align: center;">
                            <small style="font-size: 7px;">PLANTA DESTINO :</small>
                        </td>
                        <td style="border-right: 1px solid; height:0.2cm; width: 70px; text-align: center;">
                            <small style="font-size: 7px;">SUCURSAL :</small>
                        </td>
                        <td style="border-right: 1px solid; height:0.2cm; width: 60px; text-align: center;">
                            <small style="font-size: 5px;">CDP/CUARTEL</small>    
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr style="height: 0.6cm">
            <td colspan="9" style="padding: 0px !important;">
                <table cellspacing="0" cellpadding="0" style="margin: 0px !important;">
                    <tr>
                        <td style="border-right: 1px solid; height:0.6cm; width: 58px; text-align: center;">
                            <small style="font-size: 13px; font-weight: normal bold !important">00{{ $chile_response->SDP ?? '' }}</small>    
                        </td>
                        <td style="border-right: 1px solid; height:0.6cm; width: 150px; text-align: center;">
                            <small style="font-size: 13px; font-style: italic !important; font-weight: normal bold !important">{{$chile_response->NombreDelegacionDestino}}</small>
                        </td>
                        <td style="border-right: 1px solid;  height:0.6cm; width: 70px; text-align: center;">
                            <small style="font-size: 7px;"></small>
                        </td>
                        <td style="border-right: 1px solid; height:0.6cm; width: 60px; text-align: center;">
                            <small style="font-size: 13px; font-weight: normal bold !important">{{$chile_response->Sector ?? ''}} / {{$chile_response->Cuartel ?? ''}}</small>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
      </table>