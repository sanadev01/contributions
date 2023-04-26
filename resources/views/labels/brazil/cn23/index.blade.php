<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>

        @page {
            size: 10cm 15cm;
            margin: 0px;
            padding: 0px;
        }

        *{
            font-family: Arial, Helvetica, sans-serif;
            box-sizing:border-box !important;
            font-weight: bold;
        }

        img.partner-logo{
            width: 2cm;
            height: 2.5cm;
            position: absolute;
            top: 2.5mm;
            left: 2.5mm;
            object-fit: contain;
        }

        img.corrioes-lable{
            position: absolute;
            top: 2.5mm;
            left: 2.7cm;
            width: 2cm;
            height: 2.5cm;
            object-fit: contain;
        }

        p.screening-code{
            position: absolute;
            top: 5mm;
            left: 5cm;
            width: 2cm;
            height: 2.5cm;
            font-size: 15pt;
            object-fit: contain;
        }
        
        .service-type{
            position: absolute;
            top: 2.5mm;
            right: 2.5mm;
            width: 20mm;
            height: 20mm;
            display: block;
        }
        .cn23-text{
            right: 0.1cm;
            top: 0.1cm;
            font-size: 10px;
            position: absolute;
        }
        .service-info-wrapper{
            position: absolute;
            left: 2.5mm;
            top: 24mm;
            font-size: 8pt;
            width: 100%;
        }
        .service-info{
            position: absolute;
            left: 65mm;
            top:23mm;
        }
        .service-name{
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
        }
        .contract-code{
            font-size: 8pt;
            text-align: center;
        }

        .tracking_code{
            position: absolute;
            top: 3.5cm;
            display: block;
            text-align: center;
            width: 9.3cm;
        }
        .tracking_code img{
            position: absolute;
            display: block;
            left: 6.25mm;
            width: 79.5mm;
            height: 18mm;
        }
        .tracking_code span{
            position: absolute;
            display: block;
            left: 96%;
            top: 6mm;
            font-size: 25px
        }

        .barcode-label{
            position: absolute;
            top: 2.7cm;
            width: 9.4cm;
            font-size: 10pt;
            text-align: center;
            left: 0.2cm;
            font-weight: bold;
        }
        .empty-lines{
            font-size: 9px;
            position: absolute;
            top: 53mm;
            width: 9.4cm;
            text-align: center;
            left: 0.2cm;
        }

        .address{
            position: absolute;
            top: 6cm;
            display: block;
            border: 2px solid black;
            padding: 1mm;
            width: 9.3cm;
            font-size: 7px;
            left: 0.2cm;
            right: 0;
            height: 26mm;
        }
        .destination{
            width: 4cm;
            display: inline-block;
            font-size: 7pt;
            /* font-family: Arial; */
        }
        .destination h4{
            margin: 0px !important;
            padding: 0px !important;
        }
        .origin{
            width: 3.7cm;
            position: relative;
            display: inline-block;
            padding: 0px 5px;
        }
        .serivce-zipcode{
            font-size: 8px;
            top: 97mm;
            position: absolute;
            left: 0.2cm;
            width: 9.6cm;
            padding: 5px;
        }
        .serivce-zipcode .left-block{
            width: 6cm;
            display: inline-block;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7pt;
        }
        .return-address{
            /* color: rgb(124, 124, 124); */
        }
        .right-block{
            display: inline-block;
            width: 3cm;
            position: relative;
            text-align: left;
        }
        .barcode_zipcode{
            position: absolute;
            top: 6.4cm;
            display: block;
            right: 10mm;
            text-align: center;
        }
        .barcode_zipcode img{
            width: 40mm;
            height: 18mm;
            display: block;
        }
        .zipcode-label{
            position: absolute;
            right: 20mm;
            top: 80mm;
            font-size: 10pt;
            text-align: center;
            font-weight: bold;
        }
        .complain_address{
            position: absolute;
            top: 90mm;
            text-align: center;
            font-size: 7px;
            width: 9cm;
            left: 0.2cm;
        }
        
        .items-table{
            position: absolute;
            top: 11.86cm;
            font-size: 7px;
            font-weight: bold;
            width: auto;
        }
        .items-table table{
            margin: 0.1cm;
            border-collapse: collapse;
            width: 98%;
            /* page-break-inside: auto; */
            margin-bottom: 0.2cm;
        }
        .page-break-before{
            page-break-before:always;
        }
        .page-break-after{
            page-break-after:always;
        }
        .items-table .td1{
            width: 210.4px;
            height: 12px;
        }
        .items-table .td2{
            width: 144px;
            height: 12px;
        }

        .items-table .sh_code{
            width: 60.8px;
            height: 12px;
        }
        .items-table .qtd{
            width: 28.8px;
            height: 12px;
        }
        .items-table .description{
            width: 120.8px;
            height: 12px;
        }
        .items-table .weight{
            width: 49.6px;
            height: 12px;
        }
        .items-table .unit{
            width: 42.4px;
            height: 12px;
        }
        .items-table .value{
            width: 52px;
            height: 12px;
        }

        .items-table .table .tr{
            display: block;
            width: 100%;
        }

        .items-table .table .td{
            padding: 2px;
            margin: 0px !important;
            border: 1px solid black;
            float: left;
            /* display:table-column; */
            display: block;
        }
        .perfume{
            top: 90mm;
            position:absolute;
            right: 2.5mm;
            background-color: black;
            color: white;
            font-weight: bold;
            display: block;
            width: 4mm;
            height: 5mm;
            padding: 0.5mm;
            box-sizing: border-box;
            text-align: center;
            border-radius: 1mm;
        }
        .battery{
            border-radius: 1mm;
            top: 90mm;
            position:absolute;
            right: 2.5mm;
            border: 1px solid black;
            color: black;
            font-weight: bold;
            display: block;
            width: 4mm;
            height: 5mm;
            padding: 0.5mm;
            box-sizing: border-box;
            text-align: center;
        }
        .bottom-block{
            position: absolute;
            top: 11mm;
            left: 48mm;
            font-size: 8pt !important;
        }
        .box-g{
            border-style: solid;
            border-width: 3px;
            white-space:pre;
            width:10px !important;
            height:10px !important;
        }
        .box-p1{
            border-style: solid;
            border-width: 2px;
            white-space:pre;
            width:4px !important;
            margin-left:4.5px;
            height:4px !important;
        }
        .box-p2{
            margin-top:1px;
            border-style: solid;
            border-width: 2px;
            white-space:pre;
            width:4px !important;
            height:4px !important;
        }
        .box-p3{
            position: absolute;
            top: 9px !important;
            margin-left:9px;
            border-style: solid;
            border-width: 2px;
            white-space:pre;
            width:4px !important;
            height:4px !important;
        }
        .box-text{
            position: absolute;
            top: -0.5mm;
            font-size: 16px !important;
            margin-left:20px;
        }
        .return-box{
            margin-bottom: 2px !important;
            border-style: solid;
            border-width: 1px;
            border-radius: 1mm;
            white-space:pre;
            width:4px !important;
            height:4px !important;
        }
        .site-text{
            position: absolute;
            top: 15px !important;
            margin-top:5px;
        }
        .return-box-text{
            position: absolute;
            top: 0.6mm;
            /* left: 0.1mm; */
            /* font-size: 16px !important; */
            margin-left: -3px !important;
            margin-top: 4px !important;
        }
    </style>
</head>
<body>
    <div class="cn23-text">
        CN23
    </div>
    <img class="partner-logo" src="{{ $partnerLogo }}">
    <img class="corrioes-lable" src="{{ $corriosLogo }}" alt="">
    <p class="screening-code">CJA01</p>
    <img src="{{ $serviceLogo }}" class="service-type"/>
    <div class="service-info-wrapper">
        <div class="order-infoline"></div>
        <strong>Service: </strong> {{ $service }} <br>
    </div>
    <div class="service-info">
        <div class="service-name">
            {!! $packetType !!}
        </div>
        <div class="contract-code">
            {!! $contractNumber !!}
        </div>
    </div>
    <div class="tracking_code">
        <img src="data:image/png;base64,{{ base64_encode($barcodeNew->getBarcode($order->corrios_tracking_code, $barcodeNew::TYPE_CODE_128, 1,94, [0,0,0]))}}" alt="barcode"   />
        <span class="cn-label">US</span>
    </div>
    <p class="barcode-label">{{$order->corrios_tracking_code}}</p>
    <div class="empty-lines">
        Nome legível: _______________________________________________ <br>
        Documento: ___________________Rúbrica:______________________
    </div>
    <div class="address">
        <div class="destination">
            <h4><strong>DESTINATARIO:</strong></h4>
            {{ $recipient->first_name }} {{ $recipient->last_name }} <br>
            {{ $recipient->address }}, @if ($recipient->street_no != 0 ) {{ $recipient->street_no }}, @endif {{ $recipient->address2 }}, {{ $recipient->city }}, {{ $recipient->zipcode }} <br>
            {{ $recipient->state->name }}
            {{ $recipient->country->name }}
        </div>
    </div>
    @if($order->hasBattery())
    <div class="battery">B</div>
    @endif
    @if($order->hasPerfume())
    <div class="perfume">P</div>
    @endif
    <div class="barcode_zipcode">
        <img src="data:image/png;base64,{{ base64_encode($barcodeNew->getBarcode(cleanString($recipient->zipcode), $barcodeNew::TYPE_CODE_128, 1,94, [0,0,0]))}}" alt="barcode"   />
    </div>
    <p class="zipcode-label">{{ cleanString($recipient->zipcode) }}</p>
    <div class="serivce-zipcode">
        <div class="left-block">
            <div class="return-address">
                <span class="return-box"> <i class="return-box-text">X</i> </span> &nbsp; Retorno a origem  <span class="return-box">   </span> Tratar como abandono<br>
                <span class="site-text" >Dúvidas e reclamações: homedeliverybr.com</span><br><br>
                <strong>DEVOLUCÃO:</strong> <br>
                {!! $returnAddress !!}
            </div>
            @if($order->getOriginalWeight('kg') > 3)
                <div class="bottom-block">
                    <div class="box-g">    </div>
                    <div class="box-text">G</div>
                </div>
            @else
                <div class="bottom-block">
                    <div class="box-p1">    </div>
                    <div class="box-p2">    </div>
                    <div class="box-p3">    </div>
                    <div class="box-text">P</div>
                </div>
            @endif
            <br>
        </div>
        <div class="right-block">
            <h2>Remetente: @if($hasAnjunLabel) <span style="margin-left: 8px; border:solid 1px; padding-right:2px">A</span> @endif </h2>
            {{ $order->sender_first_name }} {{ $order->sender_last_name }} <br>
            {{ $order->sender_email }} <br>
            <strong>Order#:</strong>{{ $order->warehouse_number }} <br>
            <strong>CR#:</strong>{{ $order->customer_reference }} <br>
            <strong>Weight</strong> {{ $order->getOriginalWeight('kg') }}kg|{{ $order->getOriginalWeight('lbs') }}lbs <br>
            <strong>{{ $order->length }} x {{ $order->width }} x {{$order->height}} ({{$order->isWeightInKg() ? 'cm' :'in'}})</strong>
        </div>
    </div>
    <div class="complain_address">
        {{ $complainAddress }}
    </div>

    @include('labels.brazil.cn23.items')

    @if ($hasSumplimentary)
        @foreach ($suplimentaryItems as $items)
            <div class="page-break-before"></div>
            @include('labels.brazil.cn23.suplimentary',[
                'items' => $items
            ])
        @endforeach
    @endif
</body>
</html>
