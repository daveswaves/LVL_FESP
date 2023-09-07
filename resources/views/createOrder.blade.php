<!-- Uses layouts.blade.php -->
@extends('layout')

<!-- Send page title to layout -->
@section('title')
Create Order View
@endsection

@section('content')

<!-- Display sku search, export /import CSV and Add Product button in layout -->
@section('header')
<h1 style="margin-top: 0px; margin-bottom: -1px; text-align: center;"><?= $page_title ?></h1>
@endsection

<style>
    * {
        box-sizing: border-box;
    }
    .center {
        margin: auto;
        width: 60%;
    }
    .co_style {
        border: 1px solid #000;
        margin-top: 20px;
        padding: 20px;
        border-radius: 8px;
        background: #ccc;
    }
    .header_bar {
        background: #000;
        color: #fff;
        text-align: center;
    }
    .full_width {
        width: 100%;
    }
    .fld_div {
        padding: 6px;
        background: #fff;
        border: 1px solid #000;
        border-top: none;
        height: 36px;
    }
    .clear_txt_border {
        border: 0;
    }
    select {
        border: none;
        appearance: none;
        background: #fff;
    }
    .pt10 {
        padding-top: 10px;
    }
    .pointer {
        cursor: pointer;
    }
    .show_hide {
        display: none;
        background: #ddd;
        padding: 6px;
        border: 1px solid #000;
        border-top: none;
    }
    .w220 {
        width: 220px;
    }
    .w260 {
        width: 260px;
    }
    .w560 {
        width: 560px;
    }
    .w60 {
        width: 60px;
    }
    .w80 {
        width: 80px;
    }
    .mr10 {
        margin-right: 10px;
    }
    #addItemBtn, #removeItemBtn {
        display: none;
        width: 24px;
        height: 24px;
        margin-right: 6px;
    }
    #removeItemBtn {
        padding-left: 4px;
    }
    .input_group {
        margin-bottom: 6px;
    }
    .message {
        height: 108px;
    }
    [name="process_order_btn"] {
        width: 240px;
        height: 40px;
        font-size: 20px;
    }
    
    
    /*=======================================*/
    /* Styling for SKU autocomplete dropdown */
    /*=======================================*/
    
    /*the container must be positioned relative:*/
    .autocomplete {
        position: relative;
        /*display: inline-block;*/
    }

    .autocomplete-items {
        position: absolute;
        border: 1px solid #d4d4d4;
        border-bottom: none;
        border-top: none;
        z-index: 99;
        /*position the autocomplete items to be the same width as the container:*/
        top: 100%;
        left: 0;
        right: 0;
    }

    .autocomplete-items div {
        /*padding: 10px;*/
        padding: 2px;
        padding-left: 4px;
        padding-right: 4px;
        cursor: pointer;
        background-color: #fff; 
        border-bottom: 1px solid #d4d4d4; 
    }

    /*when hovering an item:*/
    .autocomplete-items div:hover {
        background-color: #e9e9e9; 
    }

    /*when navigating through the items using the arrow keys:*/
    .autocomplete-active {
        background-color: DodgerBlue !important; 
        color: #ffffff; 
    }
</style>

<?php
// echo '<pre style="background:#111; color:#b5ce28; font-size:11px;">'; print_r($order['order']['buyer']); echo '</pre>'; die();
?>

<form action="***ENDPOINT_HERE***" method="post">
    <div class="center co_style">
        <!-- START: 1st Section / Basic | Contact Details -->
        <div>
            <div class="fl" style="width: 45%;">
                <div class="header_bar full_width">Basic</div>
                <div class="fld_div">
                    <input
                        type="text"
                        class="full_width clear_txt_border"
                        name="orderID"
                        placeholder="Reference (Leave blank to auto-generate)"
                        pattern="[A-Z0-9-]+"
                        title="Only Uppercase Letters, Number and Hyphens"
                    >
                </div>
                <div class="fld_div">
                    <select name="courier" class="full_width" required="">
                        <option disabled="" selected="selected" value="">Please choose a courier ▼</option>
                        <?= implode("\n", $couriers) ?>
                    </select>
                </div>
                <div class="fld_div">
                    <select name="courier" class="full_width" required="">
                        <option selected="selected" value="elixir">Elixir ▼</option>
                        <option value="floorword">Floor World</option>
                        <option value="prosalt">Prosalt</option>
                    </select>
                </div>
            </div>
            
            <div class="fr" style="width: 54%;">
                <div class="header_bar full_width">Contact Details</div>
                <div class="fld_div">
                    <!-- Uses null coalescing operator  ?? -->
                    <input
                        type="text"
                        class="full_width clear_txt_border"
                        name="shipping[name]"
                        value="<?= $order['order']['buyer'] ?? '' ?>"
                        placeholder="Delivery Name"
                        required=""
                        pattern="[a-zA-Z0-9- \.]+)("
                        title="Only Letters, Number, Spaces, Hyphens, . and ()"
                    >
                </div>
                <div class="fld_div">
                    <input
                        type="text"
                        class="full_width clear_txt_border"
                        name="phone"
                        value="<?= $order['order']['phone'] ?? '' ?>"
                        placeholder="Phone No."
                    >
                </div>
                <div class="fld_div">
                    <input
                        type="text"
                        class="full_width clear_txt_border"
                        name="email"
                        value="<?= $order['order']['email'] ?? '' ?>"
                        placeholder="Email"
                    >
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
        <!-- END: 1st Section / Basic | Contact Details -->
        
        <!-- START: 2nd Section / Need Invoice? -->
        <div class="full_width pt10">
            <div class="header_bar full_width">Items</div>
            <div class="fld_div">
                <label for="checkInvoice" class="pointer">
                    <input id="checkInvoice" class="pointer" type="checkbox" name="checkInvoice">
                Need Invoice?</label>
                <?php if (isset($order['order']['orderId'])) { ?>
                <span id="repeat_last_order" class="pointer fr">Repeat last order</span>
                <?php } ?>
                <button id="addItemBtn" type="button" class="pointer fr btn">+</button>
                <button id="removeItemBtn" type="button" class="pointer fr btn">—</button>
            </div>
            <div class="show_hide" id="show_hide">
                <div id="input_container">
                    <?php foreach ($order['items'] as $i => $_) { ?>
                    
                    <div class="input_group" data-id="<?= $i ?>">
                        <div class="autocomplete" style="display: inline;">
                            <input
                                type="text"
                                name="items[sku][]"
                                value="<?= $order['items'][$i]['sku'] ?? '' ?>"
                                class="sku w220"
                                placeholder="SKU"
                                onclick="autocompleteFnc(this, skus, 0)"
                            >
                        </div>
                        <input
                            type="text"
                            name="items[title][]"
                            value="<?= $order['products'][$i]['elix_title'] ?? '' ?>"
                            class="title w560"
                            placeholder="Item Title"
                            required=""
                        >
                        <input
                            type="text"
                            name="items[quantity][]"
                            value="<?= $order['items'][$i]['qty'] ?? '' ?>"
                            class="quantity w60"
                            placeholder="Qty."
                            required=""
                        >
                        <input
                            type="text"
                            name="items[shipping][]"
                            value="<?= $order['items'][$i]['shipping'] ?? '0.00' ?>"
                            class="shipping w60"
                            placeholder="Shipping"
                            pattern="[0-9]+.[0-9]{2}"
                            title="Only 2 Decimal Place Numbers"
                        >
                        <input
                            type="text"
                            name="items[price][]"
                            value="<?= $order['products'][$i]['price'] ?? '' ?>"
                            class="price w60"
                            placeholder="Price"
                            required=""
                            pattern="[0-9]+\.[0-9]{2}"
                            title="Only 2 Decimal Place Numbers"
                        >
                        <button
                            type="button"
                            data-id="<?= $i ?>"
                            class="removeBtn pointer fr btn"
                        >&ndash;</button>
                        <button
                            type="button"
                            data-id="<?= $i ?>"
                            class="percBtn pointer fr mr10 btn"
                        >-10%</button>
                    </div>
                    
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- END: 2nd Section / Need Invoice? -->
        
        <!-- START: 3rd Section / Address | Miscellaneous -->
        <div class="full_width pt10">
            <div class="fl" style="width: 54%;">
                <div class="header_bar full_width">Address</div>
                <div class="fld_div">
                    <input
                        type="text"
                        class="full_width clear_txt_border"
                        name="shipping[address1]"
                        value="<?= $order['order']['addressLine1'] ?? '' ?>"
                        placeholder="Address line 1"
                        required=""
                    >
                </div>
                <div class="fld_div">
                    <input
                        type="text"
                        class="full_width clear_txt_border"
                        name="shipping[address2]"
                        value="<?= $order['order']['addressLine2'] ?? '' ?>"
                        placeholder="Address line 2"
                    >
                </div>
                <div class="fld_div">
                    <input
                        type="text"
                        class="full_width clear_txt_border"
                        name="shipping[city]"
                        value="<?= $order['order']['city'] ?? '' ?>"
                        placeholder="City"
                        required=""
                    >
                </div>
                <div class="fld_div">
                    <input
                        type="text"
                        class="full_width clear_txt_border"
                        name="shipping[county]"
                        value="<?= $order['order']['county'] ?? '' ?>"
                        placeholder="County"
                    >
                </div>
                <div class="fld_div">
                    <input
                        type="text"
                        class="full_width clear_txt_border"
                        name="shipping[countryCode]"
                        value="GB"
                        required=""
                    >
                </div>
                <div class="fld_div">
                    <input
                        type="text"
                        class="full_width clear_txt_border"
                        name="shipping[postCode]"
                        value="<?= $order['order']['postcode'] ?? '' ?>"
                        placeholder="Postcode"
                        required=""
                    >
                </div>
            </div>
            
            <div class="fr" style="width: 45%;">
                <div class="header_bar full_width">Miscellaneous</div>
                <div class="fld_div message">
                    <textarea style="height: 100%; resize: none;" class="full_width clear_txt_border" name="message" placeholder="Special Instructions"></textarea>
                </div>
                <div class="fld_div">
                    <input type="number" class="full_width clear_txt_border" name="parcelCount" placeholder="No. of Labels" min="1" max="14" value="1" required="">
                </div>
                <div class="fld_div">
                    <input type="text" class="full_width clear_txt_border" name="weight" placeholder="Weight (Kg)" required="">
                </div>
                <div class="fld_div">
                    <input type="text" class="full_width clear_txt_border" name="length" placeholder="Length (M)">
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
        <!-- END: 3rd Section / Address | Miscellaneous -->
        
        <!-- End Buttons -->
        <div class="full_width pt10">
            <input type="submit" name="process_order_btn" value="Process Order" class="btn">
        </div>
        
        
        <div style="background: #fff; padding: 20px; border-radius: 8px; margin-top: 30px;">
            <div>Tasks:</div>
            <ul>
                <li>Add delete buttons to individual invoice items.</li>
                <li>Add 'deduct 10%' buttons to individual invoice items.</li>
            </ul>
        </div>
    </div>
</form>


<script>
    // https://www.w3schools.com/howto/howto_js_autocomplete.asp
    /**
     * input: The selected 'sku' field (this) to target.
     * skus_lkup: An array of existing skus and their names.
     * index: The nth sku field. This is used to target the
     *        title field in the same row as the sku field.
     */
    function autocompleteFnc(input, skus_lkup, index) {
        let currentFocus;
        input.addEventListener("input", function(e) {
            let a, b, i, val = this.value;
            closeAllLists();
            if (!val) { return false;}
            currentFocus = -1;
            a = document.createElement("div");
            a.setAttribute("id", this.id + "autocomplete-list");
            a.setAttribute("class", "autocomplete-items");
            this.parentNode.appendChild(a);
            for (i = 0; i < skus_lkup.length; i++) {
                let skuName = skus_lkup[i].title;
                let skuPrice = skus_lkup[i].price;
                // console.log(skuName);
                if (skus_lkup[i].sku.substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                    b = document.createElement("div");
                    b.innerHTML = "<strong>" + skus_lkup[i].sku.substr(0, val.length) + "</strong>";
                    b.innerHTML += skus_lkup[i].sku.substr(val.length);
                    b.innerHTML += "<input type='hidden' value='" + skus_lkup[i].sku + "'>";
                    // Insert autocomplete sku into sku input field when autocomplete selected
                    b.addEventListener("click", function(e) {
                        input.value = this.getElementsByTagName("input")[0].value;
                        $('.quantity').eq(index).val('1');
                        $('.shipping').eq(index).val('0.00');
                        $('.title').eq(index).val(skuName);
                        $('.price').eq(index).val(skuPrice);
                        closeAllLists();
                    });
                    a.appendChild(b);
                }
            }
        });
      
        input.addEventListener("keydown", function(e) {
            let x = document.getElementById(this.id + "autocomplete-list");
            if (x) x = x.getElementsByTagName("div");
            if (e.keyCode == 40) {
                currentFocus++;
                addActive(x);
            } else if (e.keyCode == 38) { //up
                currentFocus--;
                addActive(x);
            } else if (e.keyCode == 13) {
                e.preventDefault();
                if (currentFocus > -1) {
                    if (x) x[currentFocus].click();
                }
            }
        });
      
        function addActive(x) {
            if (!x) return false;
            removeActive(x);
            if (currentFocus >= x.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = (x.length - 1);
            x[currentFocus].classList.add("autocomplete-active");
        }
      
        function removeActive(x) {
            for (let i = 0; i < x.length; i++) {
                x[i].classList.remove("autocomplete-active");
            }
        }
      
        function closeAllLists(elmnt) {
            let x = document.getElementsByClassName("autocomplete-items");
            for (let i = 0; i < x.length; i++) {
                if (elmnt != x[i] && elmnt != input) {
                    x[i].parentNode.removeChild(x[i]);
                }
            }
        }
      
        document.addEventListener("click", function (e) {
            closeAllLists(e.target);
            
        });
    }
    
    <?= $websiteSkuLkup ?>
    
    /*
        let skus = [
            {sku: "plum_slate_10kg", title: "name - plum_slate_10kg"},
            {sku: "dec_stones_plum_slate_5kg", title: "name - dec_stones_plum_slate_5kg"},
            {sku: "plum_slate_20kg_06", title: "name - plum_slate_20kg_06"},
            {sku: "AGGREGATE_PLUM_SLATE", title: "name - AGGREGATE_PLUM_SLATE"},
        ];
        
        function autocompleteFnc(input) {
            input.style.background = 'blue';
            console.log(input);
        }
        document.addEventListener("click", function (e) {
            console.log(e);
            console.log(e.target);
            console.log(this);
            e.target.style.background = 'blue';
        });
    */
    
    $(function() {
        $('.removeBtn').click(function() {
            let id = $(this).attr('data-id');
            $("div.input_group[data-id="+id+"]").remove();
        });
        
        $('.percBtn').click(function() {
            let id = $(this).attr('data-id');
            let colour = $("input[name='items[price][]']:eq("+id+")").css("background");
            let white = 'rgb(255, 255, 255)';
            let price = $("input[name='items[price][]']:eq("+id+")").val();
            
            // deduct 10% from price
            if (colour == white) {
                $("input[name='items[price][]']:eq("+id+")").val((price * 0.9).toFixed(2));
                $("input[name='items[price][]']:eq("+id+")").css("background", "#0f0");
            }
            // reset price to original figure
            else {
                $("input[name='items[price][]']:eq("+id+")").val((price * 1.111).toFixed(2));
                $("input[name='items[price][]']:eq("+id+")").css("background", "#fff");
            }
        });

        
        $("input[name='shipping[name]']").focus();
        
        // Emulate 'Need Invoice?' checked/unchecked wen up/down | left/right keys pressed.
        $(document).keydown(function(e) {
            if (e.keyCode === 40 || e.keyCode === 39) {
                $("#checkInvoice").prop("checked", true);
                
                $('#show_hide').show(200);
                $('#addItemBtn').show();
            }
            else if (e.keyCode === 38 || e.keyCode === 37) {
                $("#checkInvoice").prop("checked", false);
                
                $('#show_hide').hide(200);
                $('#addItemBtn').hide();
                
                $('#input_container .input_group:gt(0)').remove();
                $('#removeItemBtn').hide();
                // Clear first .input_group inputs
                $('#input_container .input_group').find('input').val('');
            }
        });
        
        // Show/hide order input fields and '+' button when 'Need Invoice?' checkbox checked/uncheck.
        // Remove all input field rows (except first), clear first row's input fields,
        // and hide '—' button when checked 'Need Invoice?' checkbox is unchecked.
        $("#checkInvoice").click(function() {
            $('#repeat_last_order').hide();
            
            $('#input_container .input_group:gt(0)').remove();
            $('#removeItemBtn').hide();
            // Clear first .input_group inputs
            $('#input_container .input_group').find('input').val('');
            
            $('#show_hide').toggle(200);
            $('#addItemBtn').toggle();
            
            
            // let isChecked = $("#checkInvoice").prop("checked");
            
            // if (!isChecked) {
            //     $('#input_container .input_group:gt(0)').remove();
            //     $('#removeItemBtn').hide();
            //     // Clear first .input_group inputs
            //     $('#input_container .input_group').find('input').val('');
            // }
        });
        
        // let autocompleteCounter = 0;
        $('#repeat_last_order').click(function() {
            $('#repeat_last_order').hide();
            $("#checkInvoice").prop("checked", true);
            
            $('#show_hide').show(200);
            $('#addItemBtn').show();
        });
        
        // Create extra input field rows ('Need Invoice?' section) when '+' button clicked.
        let autocompleteCounter = 0;
        $("#addItemBtn").click(function() {
            let clonedInputGroup = $('#input_container .input_group:last').clone();
            // Stops the first row's shipping field being empty and stops the values
            //  of fields that have already been entered being copied when cloned.
            clonedInputGroup.find("input").val('');
            
            // Set the value of the cloned input (name='items[shipping][]) to '0.00'
            clonedInputGroup.find("input[name='items[shipping][]']").val('0.00');
            
            // Increment the counter and use it as the last argument for autocompleteFnc()
            autocompleteCounter++;
            clonedInputGroup.find("input:first").attr("onclick", `autocompleteFnc(this, skus, ${autocompleteCounter})`);
            
            $('#input_container').append(clonedInputGroup);
            $('#removeItemBtn').show();
        });
        
        // Remove last row of input fields ('Need Invoice?' section) when '—' button clicked.
        $("#removeItemBtn").click(function() {
            let inputGroups = $('#input_container .input_group');
            if (inputGroups.length > 1) {
                inputGroups.last().remove();
                if (2 == inputGroups.length) {
                    $('#removeItemBtn').hide();
                }
            }
        });
    });
</script>
@endsection
