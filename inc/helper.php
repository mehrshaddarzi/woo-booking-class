<?php

function woo_booking_class_success( $user_id ) {
	global $wpdb;
	return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woo_booking WHERE `user_id` = {$user_id} AND `status` = 2" );
}

function woo_booking_class_pre_success( $user_id ) {
	global $wpdb;
	return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woo_booking WHERE `user_id` = {$user_id} AND `status` = 1" );
}

function is_rezerv_class( $date, $time ) {
	global $wpdb;
	$check = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woo_booking WHERE `class_date` = '{$date}' AND `class_time` = '{$time}:00:00'" );
	return ( $check > 0 );
}

function woo_get_item_qty( $product_id ) {
	foreach ( WC()->cart->get_cart() as $cart_key => $cart_item ) {
		if ( $product_id == $cart_item['product_id'] ) {
			return $cart_item['quantity'];
		}
	}
	return 0;
}


function wpdocs_set_html_mail_content_type() {
	return 'text/html';
}


