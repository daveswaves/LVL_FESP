## Undispatched View Description

```
Displays all new (undispatched) orders. UndispatchedController calls the getOrders@DBQueryService method,
which returns a multi_dim array (['obj_orders'],['obj_items'],['obj_sku_img']).

The returned data is then passed to the createDataArray@createDataService method.
This returns an array of complete orders. Eg:
```

```
Array
(
    [026-0050977-9828368] => Array
        (
            [orderID] => 026-0050977-9828368
            [source] => Amazon
            [platform] => am
            [price] => 36.99
            [date] => Mon 17th Oct 2022 11:59
            [ts] => 1666007966
            [email] => 25y8crm3skbzjh2@marketplace.amazon.co.uk
            [shippingName] => clare ryan
            [postcode] => NN96ER
            [items] => Array
                (
                    [0] => Array
                        (
                            [url] => https://www.amazon.co.uk/dp/B09FFGK2TL
                            [qty] => 1
                            [sku] => Playground-Sand_25_3
                            [img] => sand1
                            [title] => Elixir Gardens Playground Surface Sand 25 kg Bag | Childrenu2019s Play Sand, Non***
                            [variation] => Size: 25kg Bag x 3
                            [price] => 36.99
                            [shipping] => 0.00
                            [price_net] => 29.592
                            [price_vat] => 7.398
                        )

                )

            [courier] => W48
            [message] => 
            [channel] => Elixir
            [service] => Standard
            [status] => G
            [weight] => 75
            [print_scan] => 18/10/22 06:59-18/10/22 06:59
        )
    [026-0177135-6212329] => etc.
```

```
The orders array is then passed to the undispatched.blade view.
This view uses a foreach [ @foreach ($tbl_body as $row) ] to display all orders.

The records are sorted by 'Date' (ascending order) - indicated by the downward arrow.
The date's original timestamp format is also saved as a separate field 'ts' (see example array above).
This is used to sort by date (difficult to do on formatted date). This is done via a
usort() function in the UndispatchedController.

The downward arrow, indicating sorted by Date, is displayed by including sortcol="asc" in the
Date's <th> tag (undispatched.blade).

The records can be sorted by clicking any column's header. If the 'Date' column header (already
assorted ascending) is clicked it will sort descending. Sorting toggles between ass / desc.
The javascript/jQuery sort functions are saved in the 'public/js/tbl_sort.js' file.
The 'disable_sort_col1' flag is used (tbl_sort.js?disable_sort_col1) to skip sort on first column.

The records can also be filtered by Order ID, Delivery Name & Postcode. The unwanted rows are hidden
by setting their style.display  = "none". Filtering only starts once a minimum of 3 characters have
been entered. The total number of orders is displayed in the footer bar. The number also updates
for filtered records.
The javascript/jQuery filter functions are saved in the 'public/js/tbl_filter.js' file.
The javascript/jQuery code that displays the number of records in the footer bar is in the
'public/js/display_total_and_checked_orders.js' script.
```