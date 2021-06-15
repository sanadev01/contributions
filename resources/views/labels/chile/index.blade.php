<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        
        @page {
            /* width height */
            size: 9.2cm 7.7cm; 
            margin: 0px;
            padding: 0px;
        }
        *{
            font-family: Arial, Helvetica, sans-serif;
            box-sizing:border-box !important;
        }
        
    </style>
</head>
<body style="margin-bottom: -28px !important;">
    <div style="border: 1px solid black; margin: 0.07cm; border-right: 2px solid black;">
      <table style="width:100%; height:6.5cm; border-width: none;" cellspacing="0" border="1" cellpadding="2">
        <tr>
            <td rowspan="3">
                <img src="{{ asset('images/chile-logo.png') }}" alt="" style="width: 2.6cm; height: 1cm;">
            </td>
            <td>
                <small style="font-size: 7px; text-align: center;">A.R.</small>
            </td>
            <td style="text-align: center;"><small style="font-size: 7px;">RDOC</small></td>
            <td style="text-align: center;"><small style="font-size: 7px;">A.TEL</small></td>
            <td style="text-align: center;"><small style="font-size: 7px;">P.D</small></td>
            <td style="text-align: center;"><small style="font-size: 7px;">REEM</small></td>
            <td style="text-align: center;"><small style="font-size: 7px;">PRG</small></td>
            <td style="text-align: center;"><small style="font-size: 7px;">SAB</small></td>
            <td style="text-align: center;"><small style="font-size: 7px;">FIVPS</small></td>
        </tr>
        <tr style="height: 0.1cm;">
            <td colspan="8" style="font-size: 7px;">DE: <small style="font-size: 7px;">{{$order->sender_first_name}}  {{$order->sender_last_name}}</small></td>
        </tr>
        <tr style="height: 0.1cm;">
            <td colspan="9" style="border-right: none;">
                <small style="font-size: 7px;">RUT :</small>
                <small style="font-size: 7px;">CTA :</small>
                <small style="margin-left: 2rem; font-size: 7px">Tel : +{{$order->sender_phone}}</small>
            </td>
        </tr>
        <tr style="height: 0.1cm;">
            <td rowspan="2" style="text-align: center;">
                <small style="font-size: 7px;">{{$date}}</small>
                {{$chile_response->AbreviaturaServicio}}
            </td>
            <td colspan="8"><small style="font-size: 7px;">Referencia : {{$order->customer_reference}}</small></td>
        </tr>
        <tr style="height: 0.05cm;">
            <td colspan="4">
                <small style="font-size: 7px;">Description Del Production :</small><br>
                <small style="font-size: 7px;">{{$description}}</small>
            </td>
            <td colspan="5" style="border-right: none;"><small style="font-size: 7px;">Valor Declarado USD: {{$order->order_value}}</small></td>
        </tr>
        <tr style="height: 2.2cm;">
            <td colspan="9">
                <img src="data:image/png;base64,{{ base64_encode($barcodeNew->getBarcode($order->corrios_tracking_code, $barcodeNew::TYPE_CODE_128, 1,94, [0,0,0]))}}" alt="barcode" style="margin-left: 1.3cm; height: 1.8cm; width: 6.5cm; margin-right: 1.4cm; margin-top: 0.1cm; margin-bottom: 0.1cm;"   />
            </td>
        </tr>
        <tr>
            <td colspan="3"><small style="font-size: 7px;">Encaminamiento : {{$chile_response->CodigoEncaminamiento}}</small></td>
            <td colspan="3"><small style="font-size: 7px;">N Envio : {{$chile_response->NumeroEnvio}}</small></td>
            <td colspan="3" style="text-align: center; font-size: 7px;"><small style="font-size: 7px;">Bulto(s)</small><br>001</td>
        </tr>
        <tr style="height: 0.3cm;">
            <td colspan="9">
                <table style="font-size: 7px; width:100%">
                    <tr>
                        <td>
                            A
                        </td>
                        <td>
                            :
                        </td>
                        <td>
                            {{$chile_response->ComunaDestino}}
                        </td>
                        <td>
                            Para
                        </td>
                        <td>
                            :
                        </td>
                        <td>
                            {{$order->recipient->first_name}} {{$order->recipient->last_name}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Dir.
                        </td>
                        <td>
                            :
                        </td>
                        <td>
                            {{$chile_response->DireccionDestino}}
                        </td>
                        <td>
                            Tel.
                        </td>
                        <td>
                            :
                        </td>
                        <td>
                            +{{$order->recipient->phone}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Obs.
                        </td>
                        <td>
                            :
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
        <tr style="height: 0.1cm">
            <td colspan="1"><small style="font-size: 7px;">SDP</small></td>
            <td colspan="3"><small style="font-size: 7px;">PLANTA DESTINO :</small></td>
            <td colspan="3"><small style="font-size: 7px;">SUCURSAL :</small></td>
            <td colspan="2"><small style="font-size: 7px;">CDP</small></td>
        </tr>
        <tr>
            <td colspan="1"><small style="font-size: 7px;"></small></td>
            <td colspan="3"><small style="font-size: 7px;">{{$chile_response->NombreDelegacionDestino}}</small></td>
            <td colspan="3"><small style="font-size: 7px;"></small></td>
            <td colspan="2" style="font-size: 7px;"><small style="font-size: 7px;"></small></td>
        </tr>
      </table>
    </div>  
    
</body>
</html>