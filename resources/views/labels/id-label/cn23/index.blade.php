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
            width: 2.6cm;
            height: 3.1cm;
            position: absolute;
            top: 5.7cm;
            left: 0.5cm;
            object-fit: contain;
        }
        .destination-country-name {
            position: absolute;
            left: 50%;
            top: 2mm;
            transform: translateX(-50%);
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 5px;
            text-transform: uppercase;
            white-space: nowrap; /* Prevent text wrapping */
            width: 100%; /* Ensure the div takes full width */
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
        .address{
            position: absolute;
            top: 8.9cm;
            display: block;
            /* border: 2px solid black; */
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
        .sender-address{
            font-size: 8px;
            top: 5.9cm;
            position: absolute;
            left: 5.8cm;
            width: 9.6cm;
            padding: 5px;
        }
        .sender{
            display: inline-block;
            width: 3cm;
            position: relative;
            text-align: left;
            font-size: 9px;
        }
        .sender h4{
            margin: 0px !important;
            padding: 0px !important;
        }
        .barcode_zipcode{
            position: absolute;
            top: 8.9cm;
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
            top: 10.4cm;
            font-size: 10pt;
            text-align: center;
            font-weight: bold;
        }        
        .items-table{
            position: absolute;
            top: 11.90cm;
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
    </style>
</head>
    <body>
        <img class="partner-logo" src="{{ $partnerLogo }}"> 
        <div class="destination-country-name">
            <h1>GUA-{{optional($recipient->country)->name}}</h1>
        </div>
        <div class="tracking_code">
            <img src="data:image/png;base64,{{ base64_encode($barcodeNew->getBarcode($order->corrios_tracking_code, $barcodeNew::TYPE_CODE_128, 1,94, [0,0,0]))}}" alt="barcode"   />
            <span class="cn-label">US</span>
        </div>
        <p class="barcode-label">{{$order->corrios_tracking_code}}</p>
        <div class="address">
            <div class="destination">
                <h4><strong>DESTINATION:</strong></h4> 
                {{ $recipient->first_name }} {{ $recipient->last_name }} <br>
                {{ $recipient->address }} @if ($recipient->street_no != 0 ) {{ $recipient->street_no }} @endif {{ $recipient->address2 }} {{ $recipient->city }} {{ $recipient->zipcode }} <br>
                {{ optional($recipient->state)->name }}
                {{ optional($recipient->country)->name }}
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
        <div class="sender-address">
            <div class="sender">
                <h4><strong>SENDER:</strong></h4> 
                {{ $order->sender_first_name }} {{ $order->sender_last_name }} <br>
                {{ $order->sender_email }} <br>
                <strong>Order#:</strong>{{ $order->warehouse_number }} <br>
                <strong>CR#:</strong>{{ $order->customer_reference }} <br>
                <strong>Weight</strong> {{ $order->getOriginalWeight('kg') }}kg|{{ $order->getOriginalWeight('lbs') }}lbs <br>
                <strong>{{ $order->length }} x {{ $order->width }} x {{$order->height}} ({{$order->isWeightInKg() ? 'cm' :'in'}})</strong>
            </div>
        </div>

        @include('labels.senegal.cn23.items')

        @if ($hasSumplimentary)
            @foreach ($suplimentaryItems as $items)
                <div class="page-break-before"></div>
                @include('labels.senegal.cn23.suplimentary',[
                    'items' => $items
                ])
            @endforeach
        @endif
    </body>
</html>
