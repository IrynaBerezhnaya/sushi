<?php

add_action( 'woocommerce_review_order_before_payment', function () { ?>
	<h3><?php esc_html_e( 'Оплата', 'woocommerce' ); ?></h3>
<?php } );