jQuery(document).ready(function ($) {

    $(document).on('woocommerce_variations_loaded', function(event) {
        console.log('wc-init-datepickers');
        $('.date-picker').datepicker({
            dateFormat: 'yy-mm-dd',
            numberOfMonths: 1,
            showButtonPanel: true
        });
    });


});

