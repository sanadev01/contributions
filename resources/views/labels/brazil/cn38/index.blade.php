<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        @page{
            size: 21cm 29.7cm;
            margin: 0cm;
            padding: 0cm;
        }
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .page{
            padding: 0.5cm;
            position: absolute;
            top: 0cm;
            left: 0cm;
            width: 100%;
            height: 100%;
            page-break-after: avoid;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
        }
        .page table{
            width: 20cm;
            /* height: 100%; */
            border-collapse: collapse;
        }
        .page table td{
            padding: 5px;
        }
        small{
            font-size: 8px;
        }

        strong{
            font-size: 13px;
        }

        .data td{
            height: 0.3cm;
        }
    </style>
</head>
<body>
    <div class="page">
        <table border="1">
            <tr>
                <td style="width: 3cm;">
                    <img src="{{ $logo }}" style="width: 3cm; height:0.8cm;display:block;position:relative;" alt="">
                </td>
                <td colspan="3" style="width:15cm">
                    <div style="display:block;width:12cm !important;height:1cm;">
                        CN 38 - FATURA DE ENTREGA <br>
                        <small><i>(Delivery Bill)</i></small>
                    </div>
                </td>
                <td style="width: 3cm;">
                    <div style="display: block;width:5cm;">
                        1 de 1
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3" rowspan="2" style="width:17cm; ">
                    OPERADOR DE ORIGEM <br>
                    <small><i>(Office of Origin)</i></small> <br>
                    @if(strpos($originLogo, ".png") !== false || strpos($originLogo, ".jpg") !== false || strpos($originLogo, "/") !== false)
                    <img src="{{ $originLogo }}" style="width: 3cm; height:0.8cm;display:block;position:relative;" alt="">
                    @else 

                    <div class="column1" style="text-align: center;font-size:15px;font-weight:bold;" >
                                {!! $originLogo !!}
                    </div>
                    @endif
                </td>
                <td colspan="2" style="width: 3cm;">
                    Nº FATURA DE ENTREGA <br>
                    <small><i>(Delivery Bill No.)</i></small> <br>
                    <strong>{{$deliveryBillNo}}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="width: 3cm;">
                    Nº DO CONTRATO <br>
                    <small><i>(Contract No.)</i></small> <br>
                    <strong>{{$contractNo}}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="width:17cm; ">
                    CIA AÉREA <br>
                    <small>
                        <i>(Airline)</i>
                    </small>
                    <br>
                    <br>
                </td>
                <td colspan="2" style="width: 3cm;">
                    Nº VÔO <br>
                    <small>
                        <i>(Flight No.)</i>
                    </small>
                    <br>
                    <br>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="width:17cm; ">
                    DATA <br>
                    <small>
                        <i>(Date)</i>
                    </small> <br>
                    <strong>{{$date}}</strong>
                </td>
                <td colspan="2" style="width: 3cm;">
                    HORA <br>
                    <small>
                        <i>(Time)</i>
                    </small> <br>
                    <strong>{{$time}}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="3" rowspan="2" style="width:17cm; ">
                    DATA DE PARTIDA <br>
                    <small>
                        <i>(Date Of Departure)</i>
                    </small>
                </td>
                <td colspan="2" style="width: 3cm;">
                    EXPRESSO [{{$deliveryBill->unit_type == 1 ? 'X' : '  '}}] <br>
                    <small><i>(express)</i></small> <br>
                    NÃO URGENTE [{{$deliveryBill->unit_type == 2 ? 'X' : '  '}}] <br>
                    <small><i>(Non Priority)</i></small>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="width: 3cm;">
                    SERVICE 1 [{{$deliveryBill->tax_modality == 'ddp' ? 'X' : '  '}}] <br>
                    SERVICE 2 [{{$deliveryBill->tax_modality == 'ddu' ? 'X' : '  '}}]
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    AERO PORTO DE PARTIDA <br>
                    <small><i>(Airport of Departure)</i></small> <br>
                    <strong>{{$originAirpot}}</strong>
                </td>
                <td colspan="2">
                    AERO PORTO DE TRANSBORDO <br>
                    <small><i>(Airport of Transhipment)</i></small>

                </td>
                <td>
                    AERO PORTO DE CHEGADA <br>
                    <small><i>(Airport of Offloading)</i></small> <br>
                    <strong>{{$destinationAirpot}}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    DADOS DO DESPACHO <br>
                    <small><i>(Dispatch Data)</i></small>
                </td>
            </tr>

            <tr>
                <td>
                    Nº DO
                    DESPACHO <br>
                    <small><i>(Dispatch No.)</i></small>
                </td>
                <td>
                    Nº SERIAL DE MALA <br>
                    <small><i>(Receptacle Serial No.)</i></small>
                </td>
                <td>
                    PESO BRUTO DA MALA <br>
                    <small><i>(Gross Weight of Bags)</i></small>
                </td>
                <td>
                    Nº DO LACRE DA MALA <br>
                    <small><i>(Seal No.)</i></small>
                </td>
                <td>
                    OBSERVAÇÕES <br>
                    <small><i>(Observations)</i></small>
                </td>
            </tr>


            @foreach ($containers as $container)
                <tr class="data">
                    <td style="width: 1cm;">
                        <strong>
                            {{ $container->dispatch_number }}
                        </strong>
                    </td>
                    <td style="width: 5cm;">
                        {{ $container->unit_code }}
                    </td>
                    <td>
                        {{ number_format($container->getWeight(),2) }}
                    </td>
                    <td>
                        {{ $container->seal_no }}
                    </td>
                    <td></td>
                </tr>
            @endforeach
            <tr>
                <td>SUBTOTAL</td>
                <td></td>
                <td>{{ $containers->count() }}</td>
                <td colspan="2" rowspan="2"></td>
            </tr>
            <tr>
                <td>TOTAL</td>
                <td></td>
                <td>{{ number_format($deliveryBill->getWeight(),2) }}</td>
            </tr>
            <tr>
                <td colspan="5">
                    ASSINATURA DOS OPERADORES <br>
                    <small><i>(Signature)</i></small>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    OPERADOR DE ORIGEM <br>
                    <small><i>(Dispatching Office of Exchange)</i></small>
                </td>
                <td colspan="2">
                    TRANSPORTADOR <br>
                    <small><i>(The official of CARRIER or Airport)</i></small>
                </td>
                <td>
                    OPERADOR DE DESTINO <br>
                    <small><i>(Office of Exchange of Destination)</i></small>
                </td>
            </tr>
            <tr class="data">
                <td colspan="2"></td>
                <td colspan="2"></td>
                <td></td>
            </tr>
        </table>
    </div>
</body>
</html>
