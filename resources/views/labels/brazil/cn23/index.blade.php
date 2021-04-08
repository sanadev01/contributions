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
        }
        img.corrioes-lable{
            width: 2cm;
            height: 2.5cm;
            position: absolute;
            top: 0.3cm;
            left: 0.1cm;
            object-fit: contain;
        }
        img.partner-logo{
            position: absolute;
            top: 0.3cm;
            left: 2.3cm;
            width: 2cm;
            height: 2.5cm;
            object-fit: contain;
        }
        .service-type{
            position: absolute;
            top: 0.3cm;
            left: 4.6cm;
            width: 1.8cm;
            height: 1.8cm;
            display: block;
            background-color: black;
            border-radius: 0.9cm;
        }
        .cn23-text{
            right: 0.1cm;
            top: 0.1cm;
            font-size: 10px;
            position: absolute;
        }
        .service-info{
            position: absolute;
            left: 6.7cm;
            top: 0.3cm;
        }
        .service-name{
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }
        .contract-code{
            font-size: 8px;
            text-align: center;
        }
        .address{
            position: absolute;
            top: 2.7cm;
            display: block;
            border: 2px solid black;
            padding-top: 20px;
            padding-left: 5px;
            padding-right: 5px;
            width: 9.3cm;
            font-size: 7px;
            left: 0.2cm;
            right: 0;
        }
        .destination{
            width: 5cm;
            display: inline-block;
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
            top: 5.8cm;
            position: absolute;
            left: 0.2cm;
            width: 9.6cm;
            padding: 5px;
        }
        .serivce-zipcode .left-block{
            width: 4cm;
            display: inline-block;
        }
        .return-address{
            color: rgb(124, 124, 124);
        }
        .right-block{
            display: inline-block;
            width: 4.5cm;
            position: relative;
            text-align: center;
        }
        .right-block .barcode_zipcode{
            display: block;
            position: absolute;
            right: 10px;
            top: -60px;
            text-align: center;
        }
        .right-block .barcode_zipcode img{
            width: 2.8cm;
            height: 2.3cm;
            display: block;
        }
        .right-block .zipcode-label{
            position: absolute;
            right: 35px;
            top: 14px;
            font-size: 12px;
            text-align: center;
            font-weight: bold;
        }
        .complain_address{
            position: absolute;
            top: 8.45cm;
            text-align: center;
            font-size: 7px;
            width: 9cm;
            left: 0.2cm;
        }
        .tracking_code{
            position: absolute;
            top: 8.85cm;
            left: 0.2cm;
            display: block;
            text-align: center;
            width: 9.3cm;
        }
        .tracking_code img{
            position: absolute;
            display: block;
            left: 0.7cm;
            width: 8cm;
            height: 2.3cm;
        }
        .empty-lines{
            font-size: 9px;
            position: absolute;
            top: 11.47cm;
            width: 9.4cm;
            text-align: center;
            left: 0.2cm;
        }
        .items-table{
            position: absolute;
            top: 11.96cm;
            font-size: 7px;
            font-weight: bold;
            width: auto;
        }
        .items-table table{
            margin: 0.1cm;
            border-collapse: collapse;
            width: 100%;
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
        
        .barcode-label{
            position: absolute;
            top: 10.82cm;
            width: 9.4cm;
            font-size: 12px;
            text-align: center;
            left: 0.2cm;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="cn23-text">
        CN23
    </div>
    <img class="corrioes-lable" src="{{ $corriosLogo }}" alt="">
    <img class="partner-logo" src="{{ $partnerLogo }}">
    <div class="service-type"></div>
    <div class="service-info">
        <div class="service-name">
            {!! $packetType !!}
        </div>
        <div class="contract-code">
            {!! $contractNumber !!}
        </div>
    </div>
    <div class="address">
        <div class="destination">
            <h4><strong>DESTINATARIO:</strong></h4>
            <strong>NAME:</strong> {{ $recipient->first_name }} {{ $recipient->last_name }} <br>
            <strong>ADDRESS:</strong> {{ $recipient->address }} {{ $recipient->address2 }}, {{ $recipient->stree_no }}, {{ $recipient->city }}<br>
            <strong>ZIP CODE:</strong> {{ $recipient->zipcode }} <br>
            <strong>CITY/STATE:</strong> {{ $recipient->state->name }} <br>
            <strong>COUNTRY:</strong> {{ $recipient->country->name }}  <br>
            <strong>PHONE:</strong> {{ $recipient->phone }} <br>
            <strong>email:</strong> {{ $recipient->email }} <br>
        </div>
        <div class="origin">
            <h4>Origem:</h4>
            {{ $order->sender_first_name }} <br>
            <strong>WHR#:</strong>{{ $order->warehouse_number }} <br>
            <strong>CR#:</strong>{{ $order->customer_reference }} <br>
            <strong>Weight</strong> {{ $order->getOriginalWeight('kg') }}kg|{{ $order->getOriginalWeight('lbs') }}lbs
            <br>
            <strong>{{ $order->length }} x {{ $order->width }} x {{$order->height}} ({{$order->isWeightInKg() ? 'cm' :'in'}})</strong>

        </div>
    </div>
    <div class="serivce-zipcode">
        <div class="left-block">
            <strong>Service: </strong> {{ $service }} <br>
            <div class="return-address">
                <strong>DEVOLUCÃO:</strong>
                <p>
                    {{ $returnAddress }}
                </p>
            </div>
        </div>
        <div class="right-block">
            <div class="barcode_zipcode">
                {{-- <img src="data:image/png;base64,{{DNS1D::getBarcodePNG(cleanString($recipient->zipcode), 'C128',1,94,[0,0,0],true)}}" alt="barcode"   /> --}}
                <img src="data:image/png;base64,{{ base64_encode($barcodeNew->getBarcode(cleanString($recipient->zipcode), $barcodeNew::TYPE_CODE_128, 1,94, [0,0,0]))}}" alt="barcode"   />
            </div>
            <p class="zipcode-label">{{ cleanString($recipient->zipcode) }}</p>
        </div>
    </div>
    <div class="complain_address">
        {{ $complainAddress }}
    </div>
    <div class="tracking_code">
        {{-- <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($order->corrios_tracking_code, 'C128',1,94,[0,0,0],true)}}" alt="barcode"   /> --}}
        <img src="data:image/png;base64,{{ base64_encode($barcodeNew->getBarcode($order->corrios_tracking_code, $barcodeNew::TYPE_CODE_128, 1,94, [0,0,0]))}}" alt="barcode"   />
    </div>
    <p class="barcode-label">{{$order->corrios_tracking_code}}</p>
    <div class="empty-lines">
        Nome legível: _______________________________________________ <br>
        Documento: ___________________Rúbrica:______________________
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
