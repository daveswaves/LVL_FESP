$(function() {
    displayTotalOrdersFnc();
    
    $('.cbx-sel, #allCheck').on('click', function () {
        displayTotalOrdersFnc();
    });
});

function displayTotalOrdersFnc()
{
    let total = '' == $('[name="total_filtered_orders"]').val() ? $('.cbx-sel').length : $('[name="total_filtered_orders"]').val();
    let total_selected = $('.cbx-sel:checked').length;
    
    // console.log($('[name="total_filtered_orders"]').val());
    
    if (0 === total_selected) {
        total_selected = 'No';
    }
    
    let total_selected_txt = total_selected + ' orders selected ' + total + ' total orders';
    $('#total_selected').html(total_selected_txt);
}