function myFunction()
{
    let orderID;
    let delName;
    let postcode;
    let orderIdVal;
    
    let inputTxt = document.getElementById('filterTxt').value.toLowerCase();
    let tr = document.getElementById('tblSearch').getElementsByTagName('tr');
    let total = $('.cbx-sel').length;
    
    
    if (inputTxt.length > 2) {
        $('[name="txt_filter_length"]').val(inputTxt.length);
        
        // Hide non-matching table rows
        for (let i = 0; i < tr.length; i++) {
            orderID = tr[i].getElementsByTagName('td')[1];
            delName = tr[i].getElementsByTagName('td')[6];
            postcode = tr[i].getElementsByTagName('td')[8];
            
            if (orderID || delName || postcode) {
                orderIdVal = orderID.textContent || orderID.innerText;
                delNameVal = delName.textContent || delName.innerText;
                postcodeVal = postcode.textContent || postcode.innerText;
                
                if (
                    orderIdVal.toLowerCase().indexOf(inputTxt) > -1 ||
                    delNameVal.toLowerCase().indexOf(inputTxt) > -1 ||
                    postcodeVal.toLowerCase().indexOf(inputTxt) > -1
                ) {
                    tr[i].style.display = '';
                }
                else {
                    tr[i].style.display = "none";
                    
                    total--;
                }
            }
        }
        
        $('[name="total_filtered_orders"]').val(total);
        
        // Uncheck all checkboxes
        $('.tbl1 > tbody > tr').attr('data-modal-tickers-ticked', 'false');
        $('.tbl1 > tbody > tr > td > input').prop('checked', false);
        
        // Update footer bar info
        let total_selected_txt = 'No orders selected ' + total + ' total orders';
        $('#total_selected').html(total_selected_txt);
    }
    else if ($('[name="txt_filter_length"]').val() > inputTxt.length) {
        // $('#filterTxt').val('');
        
        $('.tbl1 > tbody > tr').show();
        // for (let i = 0; i < tr.length; i++) {
        //     tr[i].style.display = '';
        // }
        
        total = $('.cbx-sel').length;
        
        // Update footer bar info
        let total_selected_txt = 'No orders selected ' + total + ' total orders';
        $('#total_selected').html(total_selected_txt);
    }
}
