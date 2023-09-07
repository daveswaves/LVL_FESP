<!-- Uses layouts.blade.php -->
@extends('layout')

<!-- Send page title to layout -->
@section('title')
Undispatched Orders
@endsection

@section('content')

@section('header')
<input type="search" id="filterTxt" onkeyup="myFunction()" name="q" placeholder="Order ID | Delivery Name | Postcode" autocomplete="off">
<!--
https://www.w3schools.com/howto/howto_js_filter_table.asp

https://datatables.net/index
-->

<div class="dropdown" style="float: left;">
    <a href="" class="btn h30 lh30 mr20 fr" data-id="none" onclick="return false;">Action &#9662;</a>
    <div class="dropdown-content2 dc2-width">
        <a href="#">[✗] Force courier &amp; process selected orders</a>
        <a id="preview_inv" href="#" onclick="document.getElementById('undispatched_form').submit(); return false;">[✗] Preview Invoices</a>
        <a id="print_inv" href="#" onclick="document.getElementById('undispatched_form').submit(); return false;">[✗] Print Invoices</a>
        <a href="#" target="_blank">[✗] Send emails with tracking</a>
        <a href="#" target="_blank">[✗] Get CSV (MyHermes, Items etc)</a>
    </div>
</div>
@endsection

<!-- Page Content -->
<!-- Products Table -->
<script>
// Modify form tag to send to different routes
$(function() {
    $('#preview_inv').on('mouseover', function () {
        $('[name="inv_type"]').val('preview');
        
        // $('#undispatched_form').attr('action', '<?= route('invoice') ?>');
        // $('#undispatched_form').attr('target', '_blank');
    });
    $('#print_inv').on('mouseover', function () {
        $('[name="inv_type"]').val('print');
    });
});
</script>
<form id="undispatched_form" action="invoice" method="post" target="_blank">
@CSRF

<input type="hidden" name="inv_type">
<input type="hidden" name="total_filtered_orders">
<input type="hidden" name="txt_filter_length" value="0">

<table class='tbl1' id="tblSearch">
    <thead>
        <tr>
            <th>
                <input id="allCheck" class="sudo_cbx" type="checkbox" data-modal-tickers-master="true">
                <label class="cbx" for="allCheck"></label>
            </th>
            <th>Order ID</th>
            <th><span style="font-size: 20px; line-height: 10px;">&#9993;</span></th>
            <th>Source</th>
            <!-- <th>Channel</th> -->
            <th>Order Total</th>
            <th style='width:94px' sortcol="asc">Date</th>
            <th>Delivery Name</th>
            <th>Shipping Service</th>
            <th>Postcode</th>
            <th>Items</th>
            <th>Courier</th>
            <th>Weight<br>(kg)</th>
            <th style='width:94px'>Print/Scan</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($tbl_body as $row)
        <tr data-id="" data-id_lkup="{{ $row['orderID'] }}" data-modal-tickers-ticked="false">
            <td class="{{ $row['status'] }} tick fvis-status-generated" title="{{ $lkp_status[$row['status']] }}">
                <input type="checkbox" name="orderIDs[]" class="sudo_cbx cbx-sel" id="{{ $row['platform'] }}_{{ $row['orderID'] }}" value="{{ $row['platform'] }}_{{ $row['orderID'] }}">
                <label class="cbx" for="{{ $row['platform'] }}_{{ $row['orderID'] }}"></label>
            </td>
            <td class="dropdown">
                <span id="" class="droptd">{{ $row['orderID'] }}</span>
                <div class="dropdown-content dc-width" style="left:0;">
                    <a href="mailto:{{ $row['orderID'] }}">Copy Order ID</a>
                    
                    <a href="https://sellercentral.amazon.co.uk/orders-v3/order/{{ $row['orderID'] }}" target="_blank">View Order on amazon</a>
                    <a href="#" target="_blank">[✗] View Tracking</a>

                    <a href="{{ route('invoice') }}?id={{ $row['platform'] }}_{{ $row['orderID'] }}" target="_blank">View Invoice</a>
                    
                    <a href="#" target="_blank">[✗] Send Amazon Tracking Message</a>
                    <a href="#" target="_blank">[✗] Edit Order</a>
                    <a href="#" target="_blank">[✗] New order to customer</a>
                    <a href="https://sellercentral.amazon.co.uk/gp/communication-manager/inbox.html/ref=ag_cmin_cont_cmread?searchBoxText={{ $row['orderID'] }}" target="_blank">View messages from buyer</a>
                    <a href="mailto:{{ $row['email'] }}?subject=Regarding Order: #{{ $row['orderID'] }}, Elixir Garden Supplies" target="_blank">Send email to customer</a>
                    <a href="http://192.168.0.125/ecs/frontend/dist/Return/{{ $row['orderID'] }}" target="_blank">Return</a>
                    <a href="http://192.168.0.125/ecs/frontend/dist/Resend/{{ $row['orderID'] }}" target="_blank">Resend</a>
                    <a href="http://192.168.0.125/ecs/frontend/dist/Refund/{{ $row['orderID'] }}" target="_blank">Refund</a>
                    <a href="#" target="_blank">[✗] Add Note</a>
                </div>
            </td>
            <td style='max-width:140px'>{{ $row['message'] }}</td>
            
            <td>{{ $row['source'] }}
            @if('eBay' == $row['source'] && 'Elixir' != $row['channel'])
            <br>{{ $row['channel'] }}
            @endif
            </td>
            
            <td>&pound;{{ $row['price'] }}</td>
            <td>{{ $row['date'] }}</td>
            <td>{{ $row['shippingName'] }}</td>
            <!-- Only display non standard: NextDay, SecondDay etc. -->
            <td>{{ 'Standard' == $row['service'] ? '' : $row['service'] }}</td>
            
            <td>{{ $row['postcode'] }}</td>
            
            <td>
                @foreach ($row['items'] as $item)
                    <a href="{{ $item['url'] }}" class="no-underline" target="_blank">
                        <b>{{ $item['qty'] }} x {{ $item['sku'] }}</b>
                        {{ $item['title'] }} <b>{{ $item['variation'] }}</b>
                    </a>
                    @if ($loop->remaining)
                        <hr>
                    @endif
                @endforeach
            </td>
            
            <td>{{ $row['courier'] }}</td>
            <td>{{ $row['weight'] }}</td>
            <td>{!! str_replace('-', "<br>", $row['print_scan']) !!}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</form>

<!-- <link rel="stylesheet" type="text/css" href="/css/dataTables.css"> -->

<script>
    /*$(document).ready( function () {
        $('#tblSearch').DataTable({
            columnDefs: [
                { orderable: false, targets: 0 } // disable ordering on the first column
            ],
            order: [[5, 'asc']], // set default to order on 'Date' column
            paging: false, // disable pagination
            searching: false, // disable search
            "info": false // hide "Showing # to # of # entries"
            // "dom": '<"header"f>'
            // "dom": 'lrtip'
        });
    });*/
</script>

<script src="/js/multiselect_cbx.js"></script>
<script src="/js/tbl_sort.js?disable_sort_col1"></script>
<script src="/js/tbl_filter.js"></script>
<script src="/js/display_total_and_checked_orders.js"></script>

@endsection

@section('footer')
<span id="total_selected"></span>
@endsection