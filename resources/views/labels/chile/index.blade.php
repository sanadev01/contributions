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
<body style="margin-bottom: -28px !important;">
    
      <table style="width: 9cm; height:7.3cm; border:1px solid;" cellspacing="0" border="1" cellpadding="2">
        <tr>
            <td rowspan="3">
                <img src="{{ asset('images/chile-logo.png') }}" alt="" style="width: 2.6cm; height: 1cm;">
            </td>
            <td>
                <small style="font-size: 9px; text-align: center;">A.R.</small>
            </td>
            <td style="text-align: center;"><small style="font-size: 9px;">RDOC</small></td>
            <td style="text-align: center;"><small style="font-size: 9px;">A.TEL</small></td>
            <td style="text-align: center;"><small style="font-size: 9px;">P.D</small></td>
            <td style="text-align: center;"><small style="font-size: 9px;">REEM</small></td>
            <td style="text-align: center;"><small style="font-size: 9px;">PRG</small></td>
            <td style="text-align: center;"><small style="font-size: 9px;">SAB</small></td>
            <td style="text-align: center;"><small style="font-size: 9px;">FIVPS</small></td>
        </tr>
        <tr style="height: 0.1cm;">
            <td colspan="8" style="font-size: 9px;">DE: <small style="font-size: 9px;">{{$order->sender_first_name}}  {{$order->sender_last_name}}</small></td>
        </tr>
        <tr style="height: 0.1cm;">
            <td colspan="9">
                <small style="font-size: 9px;">RUT :</small>
                <small style="font-size: 9px;">CTA :</small>
                <small style="margin-left: 2rem; font-size: 9px">Tel : +{{$order->sender_phone}}</small>
            </td>
        </tr>
        <tr>
            <td rowspan="2" style="text-align: center;">
                <small style="font-size: 9px;">{{$date}}</small>
                {{$chile_response->AbreviaturaServicio}}
            </td>
            <td colspan="8"><small style="font-size: 9px;">Referencia : {{$order->customer_reference}}</small></td>
        </tr>
        <tr>
            <td colspan="4">
                <small style="font-size: 9px;">Description Del Production :</small><br>
                <small style="font-size: 9px;">{{$description}}</small>
            </td>
            <td colspan="5"><small style="font-size: 9px;">Valor Declarado USD: {{$order->order_value}}</small></td>
        </tr>
        <tr style="height: 3cm;">
            <td colspan="9">Barcode</td>
        </tr>
        <tr>
            <td colspan="3"><small style="font-size: 9px;">Encaminamiento : {{$chile_response->CodigoEncaminamiento}}</small></td>
            <td colspan="3"><small style="font-size: 9px;">N Envio : {{$chile_response->NumeroEnvio}}</small></td>
            <td colspan="3" style="text-align: center; font-size: 9px;"><small style="font-size: 9px;">Bulto(s)</small><br>001</td>
        </tr>
        <tr style="height: 0.3cm;">
            <td colspan="9">
                <table style="font-size: 9px; width:100%">
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
        <tr style="height: 0.7cm">
            <td colspan="1"><small style="font-size: 9px;">SDP</small></td>
            <td colspan="3"><small style="font-size: 9px;">PLANTA DESTINO :</small></td>
            <td colspan="3"><small style="font-size: 9px;">SUCURSAL :</small></td>
            <td colspan="2"><small style="font-size: 9px;">CDP</small></td>
        </tr>
        <tr style="height: 0.7cm">
            <td colspan="1"><small style="font-size: 9px;"></small></td>
            <td colspan="3"><small style="font-size: 9px;">{{$chile_response->NombreDelegacionDestino}}</small></td>
            <td colspan="3"><small style="font-size: 9px;"></small></td>
            <td colspan="2" style="font-size: 9px;"><small style="font-size: 9px;"></small></td>
        </tr>
      </table>
    
</body>
</html>