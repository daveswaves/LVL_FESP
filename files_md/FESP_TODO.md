## FESP operations that need adding to the new system

- Searchable by orderId, title, postcode, sku (by barcode etc if need more)
- Scan: mark/hold/unmark/take of hold [link](http://deepthought/sandbox/barcodedb/?action=scan); void/unvoid [link](http://192.168.0.24/SANDBOX/BarcodeDB/updateOrderStatus.php)
- Manually recreate orders from existing order [link](http://192.168.0.24/fesp-refactor/?action=reorder&orderID=<ORDER_ID>)
- Tracking system
- Push dispatch notification to platforms (API) and upload tracking info to platforms.
- Finish logic script - needs surcharge code adding.
- Add/Delete tracking [link](http://192.168.0.24/resources/addTrackingID.php)
- Saved Invoice (add function print by rooms) [link](http://deepthought/fesp-refactor/fespmvc/view_saved_invoices.php)
- 
- 



## The following are extra scripts used alongside FESP

Note: These may need modifying to work with new database formats.

- get Hermes data - display as editable table with inflated price and option to create csv [link](http://192.168.0.24/FESP-REFACTOR/get_hermes_data.php)
- Create Multiple Invoices (fix error after create order optional)[link](http://192.168.0.24/resources/multi_order.php)
- ECS (TripleR) (probably need rebuild) [link](http://192.168.0.125/ecs/frontend/dist/Login)