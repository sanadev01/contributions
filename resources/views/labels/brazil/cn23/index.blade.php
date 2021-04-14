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
            background-color: black;
            border-radius: 0.9cm;
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
            top: 23mm;
            font-size: 8pt;
            width: 100%;
        }
        .service-info{
            position: absolute;
            left: 65mm;
            top:22mm;
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
            font-size: 10pt;
            font-family: Arial;
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
            top: 100mm;
            position: absolute;
            left: 0.2cm;
            width: 9.6cm;
            padding: 5px;
        }
        .serivce-zipcode .left-block{
            width: 6cm;
            display: inline-block;
            font-family: Arial;
            font-size: 8pt;
        }
        .return-address{
            color: rgb(124, 124, 124);
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
        
    </style>
</head>
<body>
    <div class="cn23-text">
        CN23
    </div>
    <img class="partner-logo" src="{{ $partnerLogo }}">
    <img class="corrioes-lable" src="{{ $corriosLogo }}" alt="">
    <p class="screening-code">CJA01</p>
    <div class="service-type"></div>
    <div class="service-info-wrapper">
        <div class="order-infoline">
            <strong>Order#:</strong>{{ $order->warehouse_number }} 
            {{-- <strong>CR#:</strong>{{ $order->customer_reference }} --}}
            {{-- <strong>Weight</strong> {{ $order->getOriginalWeight('kg') }}kg|{{ $order->getOriginalWeight('lbs') }}lbs |  --}}
            {{-- <strong>{{ $order->length }} x {{ $order->width }} x {{$order->height}} ({{$order->isWeightInKg() ? 'cm' :'in'}})</strong> --}}
        </div>
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
        {{-- <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($order->corrios_tracking_code, 'C128',1,94,[0,0,0],true)}}" alt="barcode"   /> --}}
        <img src="data:image/png;base64,{{ base64_encode($barcodeNew->getBarcode($order->corrios_tracking_code, $barcodeNew::TYPE_CODE_128, 1,94, [0,0,0]))}}" alt="barcode"   />
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
            {{ $recipient->address }} {{ $recipient->address2 }}, {{ $recipient->stree_no }}, {{ $recipient->city }}, {{ $recipient->zipcode }} <br>
            {{ $recipient->state->name }}
            {{ $recipient->country->name }}
            {{-- <strong>PHONE:</strong> {{ $recipient->phone }} <br>
            <strong>email:</strong> {{ $recipient->email }} <br> --}}
        </div>
    </div>
    <div class="barcode_zipcode">
        {{-- <img src="data:image/png;base64,{{DNS1D::getBarcodePNG(cleanString($recipient->zipcode), 'C128',1,94,[0,0,0],true)}}" alt="barcode"   /> --}}
        <img src="data:image/png;base64,{{ base64_encode($barcodeNew->getBarcode(cleanString($recipient->zipcode), $barcodeNew::TYPE_CODE_128, 1,94, [0,0,0]))}}" alt="barcode"   />
    </div>
    <p class="zipcode-label">{{ cleanString($recipient->zipcode) }}</p>
    <div class="serivce-zipcode">
        <div class="left-block">
            <div class="return-address">
                <strong>DEVOLUCÃO:</strong>
                <p>
                    {!! $returnAddress !!}
                </p>
                <strong>Order#:</strong>{{ $order->warehouse_number }} <br>
                <strong>CR#:</strong>{{ $order->customer_reference }} <br>
                <strong>Weight</strong> {{ $order->getOriginalWeight('kg') }}kg|{{ $order->getOriginalWeight('lbs') }}lbs <br>
                <strong>{{ $order->length }} x {{ $order->width }} x {{$order->height}} ({{$order->isWeightInKg() ? 'cm' :'in'}})</strong>
            </div>
        </div>
        <div class="right-block">
            <h2>Remetente:</h2>
            {{ $order->sender_first_name }} {{ $order->sender_last_name }} <br>
            {{ optional($order)->user->pobox_number }}, 
            2200 NW, 129th Ave – Suite # 100 <br>
            Miami, FL, 33182 <br>
            United States <br>
            Ph#: +13058885191
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
