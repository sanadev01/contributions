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

        * {
            font-family: Arial, Helvetica, sans-serif;
            box-sizing: border-box !important;
        }

        hr {
            border: 0;
            border-top: 1px solid #000;  
        } 
        .font-bold {
            font-weight: bold;
        }

        img.partner-logo {
            width: 4.7cm;
            height: 1.7cm;
            position: absolute;
            top: 1mm;
            left: 28%;
            object-fit: contain;
        }
 
        .destination-country-name {
            position: absolute;
            left: 50%;
            top: 1.4cm;
            transform: translateX(-50%);
            text-align: center;
            font-size: 12pt;
            margin-bottom: 5px;
            text-transform: uppercase;
            white-space: nowrap;
            /* Prevent text wrapping */
            width: 100%;
            /* Ensure the div takes full width */
        }

        .tracking_code {
            position: absolute;
            top: 3.5cm;
            display: block;
            text-align: center;
            width: 9.3cm;
        }

        .tracking_code img {
            position: absolute;
            display: block;
            left: 6.25mm;
            width: 79.5mm;
            height: 18mm;
        }

        .tracking_code span {
            position: absolute;
            display: block;
            left: 96%;
            top: 6mm;
            font-size: 25px
        }

        .barcode-label {
            position: absolute;
            top: 2.7cm;
            width: 9.4cm;
            font-size: 10pt;
            text-align: center;
            left: 0.2cm;
        }

        .second-hr {
            position: absolute;
            top: 5.3cm;
        }

        .destination-address {
            position: absolute;
            top: 5.5cm;
            display: block;
            left: 5.5cm;
            right: 0;
            padding: 0px;
            margin: 0px;
        }

        .destination {
            display: inline-block;
        }


        .border-left-green {
            width: 45%;
            padding: 10px;
            border-left: 2px solid #81d08b;
        }

        .sender-address {
            top: 5.5cm;
            position: absolute;
            left: 0.5cm;
            margin: 0px;
            padding: 0px;
        }

        .padding-top-10 {
            padding-top: 22px;
        }

        .sender {
            position: relative;
            text-align: left;
            font-size: 9px;
            margin: 0px;
            padding: 0px;
        }


        .third-hr {
            position: absolute;
            top: 122mm;
        }

        .barcode_zipcode {
            position: absolute;
            top: 125mm;
            display: block;
            left: 3.0cm;
            text-align: center;
        }

        .barcode_zipcode img {
            width: 40mm;
            height: 18mm;
            display: block;
        }

        .zipcode-label {
            position: absolute;
            left: 4.4cm;
            top: 140mm;
            font-size: 10pt;
            text-align: center;
        }


        .page-break-before {
            page-break-before: always;
        }

        .page-break-after {
            page-break-after: always;
        }

        .perfume {
            top: 131mm;
            position: absolute;
            right: 2.5mm;
            background-color: black;
            color: white;
            display: block;
            width: 4mm;
            height: 5mm;
            padding: 0.5mm;
            box-sizing: border-box;
            text-align: center;
            border-radius: 1mm;
        }

        .battery {
            border-radius: 1mm;
            top: 131mm;
            position: absolute;
            right: 2.5mm;
            border: 1px solid black;
            color: black;
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
    <!-- partner log -->
    <div>

        <img class="partner-logo" src="{{ $partnerLogo }}">
        <hr style="position: relative;top:1.6cm">
    </div>
    <!-- country  -->
    <div class="destination-country-name font-bold">
        <h1>GUA-{{optional($recipient->country)->name}}</h1>
    </div>
    <div> 
        <!-- tracking code and tracking code barcode -->
        <div class="tracking_code font-bold">
            <img src="data:image/png;base64,{{ base64_encode($barcodeNew->getBarcode($order->corrios_tracking_code, $barcodeNew::TYPE_CODE_128, 1,94, [0,0,0]))}}" alt="barcode" />
            <span class="font-bold">US</span>
        </div>
        <p class="barcode-label font-bold">{{$order->corrios_tracking_code}}</p> 
        <hr style="position: relative;top:5cm">
    </div>
    <!-- sender address -->
    <div class="sender-address">
        <div>
            <h5><strong class="border-left-green" style="font-size: 18px;">SENDER:</strong></h5>
        </div>
        <div class="sender" style="font-size:12px">
            <p>
                <strong>Name#: &nbsp;</strong> {{ $order->sender_first_name }} {{ $order->sender_last_name }} <br>
            </p>
            <p>
                <strong>Order#: &nbsp;</strong>{{ $order->warehouse_number }}
            </p>
            <p>
                <strong>CR#: &nbsp; &nbsp;&nbsp; </strong>{{ $order->customer_reference }}
            </p>
            <p>
                <strong>Weight &nbsp;</strong> {{ $order->getOriginalWeight('kg') }}kg|{{ $order->getOriginalWeight('lbs') }}lbs <br>
            </p>
            <p>
                <strong>{{ $order->length }} x {{ $order->width }} x {{$order->height}} ({{$order->isWeightInKg() ? 'cm' :'in'}})</strong>
            </p>

        </div>
    </div>
    <!-- destination address -->
    <div class="destination-address">
        <div>
            <h5><strong class="border-left-green" style="font-size: 18px;">DESTINATION:</strong></h5>
        </div>
        <div class="destination" style="font-size: 12px">
            @if ($recipient->first_name || $recipient->last_name)
            <p>{{ $recipient->first_name }} {{ $recipient->last_name }}</p>
            @endif
            @if($recipient->address)
            <p>{{ $recipient->address }}</p>
            @endif
            @if ($recipient->street_no != 0||true )
            <p>{{ $recipient->street_no }} </p>
            @endif
            @if($recipient->address2 || $recipient->city || $recipient->zipcode)
            <p>{{ $recipient->address2 }} {{ $recipient->city }} {{ $recipient->zipcode }}</p>
            @endif
            @if (optional($recipient->state)->name)
            <p>{{ optional($recipient->state)->name }} </p>
            @endif
            @if (optional($recipient->country)->name)
            <p>{{ optional($recipient->country)->name }} </p>
            @endif
        </div>
    </div>
    <p style="font-style:italic;position: absolute;top:11.4cm;right:4mm;font-size:12px">https://homedeliverybr.com</p>
    <hr style="position: relative;top:11cm">

    <!-- zipcode and zipcode barcode -->
    @if($order->hasBattery())
    <div class="battery font-bold">B</div>
    @endif
    @if($order->hasPerfume())
    <div class="perfume font-bold">P</div>
    @endif
    <div class="barcode_zipcode">
        <img src="data:image/png;base64,{{ base64_encode($barcodeNew->getBarcode(cleanString($recipient->zipcode), $barcodeNew::TYPE_CODE_128, 1,94, [0,0,0]))}}" alt="barcode" />
    </div>
    <p class="zipcode-label font-bold">{{ cleanString($recipient->zipcode) }}</p>

    </div>

</body>

</html>