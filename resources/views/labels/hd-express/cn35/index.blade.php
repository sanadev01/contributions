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
                <td class="column1" style="text-align: center;font-size:25px;">{!! $companyName !!}</td>
                <td rowspan="2" colspan="2">
                    <img src={{ public_path('images/hd-1cm.png') }} style="height:18mm;display:block;position:absolute:top:-10;left:0;"/>
                    
                    <div style="display:block;width:400px;font-weight:bold;font-size:35px;text-align:center;vertical-align:middle;position:absolute;top:30px;left:440px;">
                        <!-- {{ $packetType }} -->
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="height: 1.6cm !important;text-align: center;display:block;">
                        Nº Do Despacho <br/>
                        <small><i>(Dispatch No.)</i></small> <br/>
                        <strong>{{ $dispatchNumber }}</strong>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="position: relative">
                    <div style="height: 1.5cm !important;text-align: center;display:block;position: absolute;top:0;margin:auto;width:100%;">
                        Nº Serial da Mala <br/>
                        <small><i>(Receptacle Serial No.)</i></small> <br/>
                        <strong>{{ $serialNumber }}</strong>
                    </div>
                </td>
                <td colspan="2" style="text-align: center;font-size:50px;">
                    {!! $officeAddress !!}
                </td>
            </tr>
            <tr>
                <td>
                    <div style="height: 1.4cm !important;display:block;">
                        Data do despacho <small><i>(Date)</i></small> <br/>
                        <strong>{{ $dispatchDate }}</strong>
                    </div>
                </td>
                <td colspan="2">
                    <div class="text-align:center;">
                        Nº VÔO <small><i>(Flight Number)</i></small> <br>
                                 <strong> {{$flightNumber}} </strong>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="height: 1.5cm !important;display:block;">
                        Quantidade de Items
                        <small><i>(Quantity)</i></small> <br/>
                        <strong>{{ $itemsCount }}</strong>
                    </div>
                </td>
                <td>
                    <div class="">
                        Aeroporto de Origem <br/>
                        <small><i>(Airport of Departure)</i></small> <br/>
                        <strong>{{ $originAirpot }}</strong>
                    </div>
                </td>
                <td>
                    <div class="">
                        Aeroporto de Destino <br/>
                        <small><i>(Airport of Offloading)</i></small> <br/>
                        <strong>{{$destinationAirport}}</strong>
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
                    <div style="text-align:center; @if($OrderWeight <= 3) margin-top: 2% !important; @endif">
                        <img style="width: @if(!empty($colombiaContainer)) 7cm @else 14cm @endif; height:1.5cm;display:block;" src="data:image/png;base64,{{DNS1D::getBarcodePNG($unitCode, 'C128',1,100,[0,0,0])}}" alt="barcode"   />
                        <div class="unit-code" style="width: 100%;display:block;">
                            {{$unitCode}}
                        </div>
                        &nbsp;<br/>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="height: 1.5cm !important;display:block;">
                        Serviço <br/>
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