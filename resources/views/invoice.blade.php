<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Invoice</title>
<link rel="stylesheet" type="text/css" href="{{ $css }}">
@if(isset($css_preview))
<link rel="stylesheet" type="text/css" href="{{ $css_preview }}">
@endif
</head>
<body class="preview">

@foreach($orders as $order)
<div class="preview">
<div style="height: {{ $loop->index ? '30px' : '0' }};"></div>
<table class="">
    <tr>
        <td class="border">
            <div style="float: left; width: 320px;">
                <h2>Invoice</h2>
                <b>Elixir Garden Supplies</b><br>
                Unit 1<br>
                Middlegate<br>
                White Lund Industrial Estate<br>
                Morecambe<br>
                LA3 3BN <b>VAT Reg No:</b> GB137223825
            </div>
            <div style="float: right;">
                <p style="text-align: right;"><img src="{{ public_path('/imgs/elixir-logo.png') }}" alt="logo" class="logo"></p>
                <p>
                    <b>Tel:</b> 01524 741229<br>
                    <b>Email:</b> info@elixirgardens.co.uk<br>
                    <b>Web:</b> www.elixirgardensupplies.co.uk<br>
                    <b>Date:</b> {{ $order['date'] }}
                </p>
            </div>
        </td>
    </tr>
    <tr>
        <td class="border">
            <div style="float: left;">
                <b>Buyer Name:</b> {{ $order['shippingName'] }}<br>
                <b>Email:</b> {{ $order['email'] }}<br>
                <b>Telephone No:</b> {{ $order['phone'] }}
            </div>
            <div style="float: right;">
                <b>Order No:</b> {{ $order['orderID'] }}<br>
                <b>Courier:</b> {{ $order['courier'] }}<br>
                <b>Order Source:</b> {{ $order['source'] }}
            </div>
        </td>
    </tr>
    <tr>
        <td class="border" style="padding: 0; margin: 0; height: 574px;">
            <table class="header;" style="background: #fff;">
                <thead>
                    <tr class="header" style="background: #000;">
                        <th>SKU</th>
                        <th>Image</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Net</th>
                        <th>VAT</th>
                        <th>Gross</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order['items'] as $items)
                    <tr class="items">
                        <td style="vertical-align: top">{{ $items['sku'] }}</td>
                        <td style="vertical-align: top"><img src="{{ public_path() }}/imgs/products/{{ strtolower($order['source']) }}/{{ $items['img'] }}.jpg" style="border: 1px solid #000;" alt="product"></td>
                        <td style="vertical-align: top">{{ $items['title'] }}
                        @if('' != $items['variation'])
                        <br><b class='item-purchased'>{{ $items['variation'] }}</b>
                        @endif
                        </td>
                        <td style="vertical-align: top">{{ $items['qty'] }}</td>
                        <td style="vertical-align: top">&pound;{{ $items['price_net'] }}</td>
                        <td style="vertical-align: top">&pound;{{ $items['price_vat'] }}</td>
                        <td style="vertical-align: top">&pound;{{ $items['price'] }}<p class='small'>(&pound;{{ $items['shipping'] }} P&P)</p></td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="6" style="text-align: right"><b>TOTAL:</b></td>
                        <td style="text-align: center">&pound;{{ $order['price'] }}</td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td class="border">Customer satisfaction is very important to us. Should you have any issues with this delivery please contact us directly by emailing us at <b>support@elixirgardens.co.uk</b> we aim to respond within 24 hours – we will be happy to resolve any issues</td>
    </tr>
    <tr>
        <td class="border" id="label" style="padding-top: 10px;">
            <address style="float: left;">
                @if(isset($css_preview))
                <div class="address-label"> 
                    <h3>Ship To:</h3>
                    <p>{{ $order['shippingName'] }}</p>
                    <p>{{ $order['addressLine1'] }}</p>
                    <p>{{ $order['addressLine2'] }}</p>
                    <p>{{ $order['city'] }}</p>
                    <p>{{ $order['county'] }}</p>
                    <p>{{ $order['postcode'] }}</p>
                </div>
                @else
                <img src="{{ public_path() }}/imgs/courier_labels/{{ $order['orderID'] }}.png" alt="label" style="width:98%;" class="courier-label">
                @endif
            </address>
            <div style="float: right; position: absolute; bottom: 0; right: 0; text-align: right;">
                <img src="data:image/png;base64,{{ base64_encode($order['generator']->getBarcode($order['barcode'],$order['generator']::TYPE_INTERLEAVED_2_5,2,24)) }}" alt="barcode">
                <br><span style="font-size: 20px; margin-right: 4px;">{{ $order['barcode'] }}</span>
            </div>
        </td>
    </tr>
</table>
</div>
@endforeach
</body>
</html>