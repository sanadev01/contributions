<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        @page {
            size: 22cm 11cm;
            margin: 0px;
            padding: 0px;
        }
        *{
            padding: 0px !important;
            margin: 0px !important;
            font-family: Arial, Helvetica, sans-serif;
            text-align: center;
        }
        .page{
            box-sizing:border-box !important;
            text-align: center;
            vertical-align: text-top;
            page-break-inside: avoid;
            page-break-before: avoid;
            page-break-after: avoid;
            position: absolute;
            top: 0;
            left:0;
        }
        table{
            /* margin: 0.05cm !important; */
            border-collapse: collapse;
            width: 22cm;
            height: 11cm;
        }
        .column1{
            width: 5cm;
        }
        .column{
            width: 49%;
            height: 100%;
            display: inline-block;
        }
        .column.origin-column{
            border-right: 1px solid black;
        }
        small{
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="page">
        <table border="2">
            <tr style="height: 1cm !important;position: relative;">
                <td class="column1" style="text-align: center;font-size:28px;font-weight:bold;">{!! $companyName !!}</td>
                <td rowspan="2" colspan="2">
                    <img src="{{ asset('images/corrieosChile-logo.png') }}" alt="Correos Chile" style="height: 1.8cm;">
                </td>
            </tr>
            <tr>
                <td>
                    <div style="height: 1.6cm !important;text-align: center;display:block;">
                        numero de envio <br/>
                        <small><i>(Dispatch No.)</i></small> <br/>
                        <strong>{{ $dispatchNumber }}</strong>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="position: relative">
                    <div style="height: 1.5cm !important;text-align: center;display:block;position: absolute;top:0;margin:auto;width:100%;">
                        <small>numero de serie de la maleta</small> <br/>
                        <small><i>(Receptacle Serial No.)</i></small> <br/>
                        <strong>{{ $serialNumber }}</strong>
                    </div>
                </td>
                <td colspan="2" style="text-align: center;font-size:12px;">
                    <div style="height: 1.6cm !important;">
                        <img style="width: 5cm; height:1.5cm;display:block; margin-top: 5px !important;" src="data:image/png;base64,{{DNS1D::getBarcodePNG($bar_code, 'C128',1,100,[0,0,0])}}" alt="barcode"   />
                    </div>
                    {{$bar_code}}
                </td>
            </tr>
            <tr>
                <td>
                    <div style="height: 1.4cm !important;display:block;">
                        Fecha de envio <small><i>(Date)</i></small> <br/>
                        <strong>{{ $dispatchDate }}</strong>
                    </div>
                </td>
                <td colspan="2">
                    <div class="text-align:center;">
                        @if ($destinationAirport == 'Santiago')
                            Region Metropolitana ({{$destinationAirport}}) <br>
                            <br/>
                        @else
                            Other Region <br>
                            <br/>
                        @endif
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="height: 1.5cm !important;display:block;">
                        Cantidad de articulos
                        <small><i>(Quantity)</i></small> <br/>
                        <strong>{{ $itemsCount }}</strong>
                    </div>
                </td>
                <td>
                    <div class="">
                        aeropuerto de origen <br/>
                        <small><i>(Airport of Departure)</i></small> <br/>
                        <strong>{{ ($originAirpot == 'HERC') ? 'MIA' : $originAirpot }}</strong>
                    </div>
                </td>
                <td>
                    <div class="">
                        aeropuerto de destino <br/>
                        <small><i>(Airport of Offloading)</i></small> <br/>
                        <strong>{{($destinationAirport == 'Santiago') ? 'SCL' : $destinationAirport}}</strong>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="height: 1.6cm !important;display:block;">
                        Peso <br/>
                        <small><i>(Weight)</i></small> <br/>
                        <strong>{{$weight}}</strong>
                    </div>
                </td>
                <td rowspan="2" colspan="2">
                    @if ($awb != null)
                        <div style="height: 1.6cm !important; margin-bottom: 4px !important;">
                            <img style="width: 6cm; height:1.5cm;display:block; margin-bottom: 4px !important;" src="data:image/png;base64,{{DNS1D::getBarcodePNG($awb, 'C128',1,100,[0,0,0])}}" alt="barcode"   />
                        </div>
                        <span style="font-weight: bold; text-transform: uppercase;">{{$awb}}</span> 
                    @endif
                </td>
            </tr>
            <tr>
                <td>
                    <div style="height: 1.5cm !important;display:block;">
                        Servicio <br/>
                        <small><i>(Service)</i></small><br/>
                        <strong>{{$service}}</strong>
                    </div>
                </td>
                {{-- <td></td> --}}
            </tr>
        </table>
    </div>
</body>
</html>