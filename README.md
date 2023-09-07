# Elixir Gardens Laravel system for processing orders

![Products View — details](https://github.com/daveswaves/FESP/blob/master/files_md/products_view.md)

```
Displays all the Elixir products (sku, title, weight, length).

Single products can be displayed by entering a sku in 'Search' field.

Products can be sorted by any column (click column header).

Existing products can be edited / new products added (modal popup).

Records can be exported to CSV or imported from CSV.

NOTES:
  * BUG — CSV import returns error if no file selected.
  * Need to have example CSV documentation for CSV import.
```

The above also includes useful links to various video [tutorials](https://github.com/daveswaves/FESP/blob/master/files_md/products_view.md#laravel-tutorials)



![Undispatched View — details](https://github.com/daveswaves/FESP/blob/master/files_md/undispatched_view.md)

Displays all orders that have not yet been added to the 'dispatched_orders' table.

Excludes void orders ('status' = 'V' in 'barcode' table), and manual orders ('platform' = 'ma' in 'barcode' table).

The process for deciding which orders to display has changed from the original. The order IDs from new orders now get added to the 'undispatched_orderIDs' table. Orders in this table (not void or manual) are displayed. When orders are dispatched (the 'Dispatch all scanned orders' button has been clicked) they get removed from this table.
