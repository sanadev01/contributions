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
        
    </style>
</head>
<body style="margin-bottom: 0px !important;">
    <div >
      <table style="width:100%; height:7cm;  margin: 0.07cm;" cellspacing="0" border="1" cellpadding="0">
        <tr style="height: 0.283cm; font-size: 9px !important;">
            <td rowspan="3">
                <img src="{{ asset('images/chile-logo.png') }}" alt="" style="height: 0.85cm;">
            </td>
            <td>
                <small style="text-align: center;">A.R.</small>
            </td>
            <td style="text-align: center;"><small>RDOC</small></td>
            <td style="text-align: center;"><small>A.TEL</small></td>
            <td style="text-align: center;"><small>P.D</small></td>
            <td style="text-align: center;"><small>REEM</small></td>
            <td style="text-align: center;"><small>PRG</small></td>
            <td style="text-align: center;"><small>SAB</small></td>
            <td style="text-align: center;"><small>FIVPS</small></td>
        </tr>
        <tr style="height: 0.283cm; font-size: 9px !important;">
            <td colspan="8" style="font-size: 7px;">DE: <small style="font-size: 7px;">{{$order->sender_first_name}}  {{$order->sender_last_name}}</small></td>
        </tr>
        <tr style="height: 0.283cm; font-size: 9px !important;">
            <td colspan="8" style="border-right: none;">
                <small style="font-size: 7px;">RUT :</small>
                <small style="font-size: 7px;">CTA :</small>
                <small style="margin-left: 2rem; font-size: 7px">Tel : {{$order->sender_phone}}</small>
            </td>
        </tr>
        <tr style="height: 0.33cm !important;">
            <td rowspan="2" style="text-align: center; font-size: 6px !important;">
                <small style="font-size: 3px;">{{$date}}</small><br />
                <small style="font-size: 12px !important;">{{$chile_response->AbreviaturaServicio}}</small>
            </td>
            <td colspan="8" style="height: 0.5px !important; line-height: 2px !important;"><small style="font-size: 7px;">Referencia : {{$order->customer_reference}}</small></td>
        </tr>
        <tr style="height: 0.33cm !important; line-height: 5px !important;">
            <td colspan="6">
                <small style="font-size: 5px; font-weight: bold !important">Description Del Production :</small><br>
                <small style="font-size: 7px;">{{$description}}</small>
            </td>
            <td colspan="2" style="border-right: none;">
                <small style="font-size: 5px; font-weight: bold !important;">Valor Declarado USD:</small>
                <small style="font-size: 6px !important;">{{$order->order_value}}</small>
            </td>
        </tr>
        <tr style="height: 3cm;">
            <td colspan="9">
                <img src="data:image/png;base64,{{ base64_encode($barcodeNew->getBarcode($order->corrios_tracking_code, $barcodeNew::TYPE_CODE_128, 1,94, [0,0,0]))}}" alt="barcode" style="height: 2.1cm !important; width: 6.5cm !important; margin: 0.4cm 1.4cm 0.4cm 1.3cm !important;"   />
            </td>
        </tr>
        <tr style="line-height: 8px;">
            <td colspan="1" style="text-align: center;"><small style="font-size: 7px;">Encaminamiento</small><br /><small style="font-size: 7px;">{{$chile_response->CodigoEncaminamiento}}</small></td>
            <td colspan="6"><small style="font-size: 7px; display: block !important">N Envio :</small> <small style="font-size: 12px; font-weight: bold; padding-left: 33px;">{{$chile_response->NumeroEnvio}}</small></td>
            <td colspan="2" style="text-align: center;"><small style="font-size: 7px;">Bulto(s)</small><br /><small style="font-size: 9px !important">001</small></td>
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
                        <td>
                            Para :
                        </td>
                        <td>
                            {{$order->recipient->first_name}} {{$order->recipient->last_name}}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">
                            Dir. :
                        </td>
                        <td style="font-weight: bold;">
                            {{$chile_response->DireccionDestino}}
                        </td>
                        <td>
                            Tel.:
                        </td>
                        <td>
                            {{$order->recipient->phone}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Obs.:
                        </td>
                        <td>
                            
                        </td>
                        <td>
                            
                        </td>
                        <td>
                            
                        </td>
                        <td>
                            
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
                            <small style="font-size: 7px;"></small>    
                        </td>
                        <td style="border-right: 1px solid; height:0.6cm; width: 150px; text-align: center;">
                            <small style="font-size: 7px;">{{$chile_response->NombreDelegacionDestino}}</small>
                        </td>
                        <td style="border-right: 1px solid;  height:0.6cm; width: 70px; text-align: center;">
                            <small style="font-size: 7px;"></small>
                        </td>
                        <td style="border-right: 1px solid; height:0.6cm; width: 60px; text-align: center;">
                            <small style="font-size: 7px;"></small>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
      </table>