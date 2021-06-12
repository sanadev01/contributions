<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        
        @page {
            /* width height */
            size: 10cm 12cm; 
            margin: 0px;
            padding: 0px;
        }

        *{
            font-family: Arial, Helvetica, sans-serif;
            box-sizing:border-box !important;
        }
        
    </style>
</head>
<body>
      <table style="width: 9cm; height:7.3cm; border:1px solid;" cellspacing="0" border="1" cellpadding="2">
        <tr>
            <td rowspan="3">
                <img src="{{ asset('images/correios-1.png') }}" alt="" style="width: 139px; height: 60px;">
            </td>
            <td style="text-align: center;"><small style="font-size: 12px;">A.R.</small></td>
            <td style="text-align: center;"><small style="font-size: 12px;">RDOC</small></td>
            <td style="text-align: center;"><small style="font-size: 12px;">A.TEL</small></td>
            <td style="text-align: center;"><small style="font-size: 12px;">P.D</small></td>
            <td style="text-align: center;"><small style="font-size: 12px;">REEM</small></td>
            <td style="text-align: center;"><small style="font-size: 12px;">PRG</small></td>
            <td style="text-align: center;"><small style="font-size: 12px;">SAB</small></td>
            <td style="text-align: center;"><small style="font-size: 12px;">FIVPS</small></td>
        </tr>
        <tr>
            <td colspan="8">DE: <small>{{$order->sender_first_name}}  {{$order->sender_last_name}}</small></td>
        </tr>
        <tr>
            <td colspan="9">
                <small>RUT : 12.345.345-0</small>
                <small>CTA :</small>
                <small style="margin-left: 3rem;">Tel : +{{$order->sender_phone}}</small>
            </td>
        </tr>
        <tr>
            <td rowspan="2" style="text-align: center;">{{$chile_response->AbreviaturaServicio}}</td>
            <td colspan="8"><small>Referencia : {{$order->customer_reference}}</small></td>
        </tr>
        <tr>
            <td colspan="4">
                <small style="font-size: 0.6rem;">Description Del Production :</small><br>
                <small style="font-size: 0.6rem;">{{$description}}</small>
            </td>
            <td colspan="5"><small>Valor Declarado USD: {{$order->order_value}}</small></td>
        </tr>
        <tr style="height: 3cm;">
            <td colspan="9">BARCODE</td>
        </tr>
        <tr>
            <td colspan="3"><small>Encaminamiento : {{$chile_response->CodigoEncaminamiento}}</small></td>
            <td colspan="3"><small>N Envio : {{$chile_response->NumeroEnvio}}</small></td>
            <td colspan="3" style="text-align: center;"><small>Bulto(s)</small><br>001</td>
        </tr>
        <tr style="height: 1cm;">
            <td colspan="9">
                <small>A : </small><span><small style="margin-left: 52px;">{{$chile_response->ComunaDestino}}</small><span>
                    <small style="margin-left: 52px;">Para:</small><span>
                        <small>{{$order->recipient->first_name}} {{$order->recipient->last_name}}</small>
                <br><small>Dir.: </small><span>
                    <small style="margin-left: 43px;">{{$chile_response->DireccionDestino}}</small><span>
                        <small style="margin-left: 12px;">Tel.:</small><span>
                            <small style="font-size: 0.6rem;">+{{$order->recipient->phone}}</small>

                <br><small>Obs.: </small><span>
                    <small style="margin-left: 25px; font-size: 0.8rem;"></small>
            </td>
        </tr>
        <tr style="height: 0.2cm">
            <td colspan="1"><small>SDP</small></td>
            <td colspan="3"><small style="font-size: 0.6rem;">PLANTA DESTINO :</small></td>
            <td colspan="3"><small style="font-size: 0.6rem;">SUCURSAL :</small></td>
            <td colspan="2"><small style="font-size: 0.6rem;">CDP/CURATEL</small></td>
        </tr>
        <tr>
            <td colspan="1">001</td>
            <td colspan="3" style="min-width: 4.6cm; text-align: center;"><small>{{$chile_response->NombreDelegacionDestino}}</small></td>
            <td colspan="3" style="text-align: center;"><small style="font-size: 0.7rem;">SUCURSAL PLAZA DE ARMAS</small></td>
            <td colspan="2" style="text-align: center;">25 / 23</td>
        </tr>
      </table>
</body>
</html>
