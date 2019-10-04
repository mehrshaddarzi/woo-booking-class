<?php

add_filter( 'woocommerce_account_menu_items', 'woo_booking_modify_my_account_links' );

function woo_booking_modify_my_account_links( $menu_links ) {
	unset( $menu_links['edit-address'] ); // Addresses
	//unset( $menu_links['dashboard'] ); // Remove Dashboard
	unset( $menu_links['payment-methods'] ); // Remove Payment Methods
	unset( $menu_links['orders'] ); // Remove Orders
	unset( $menu_links['downloads'] ); // Disable Downloads
	//unset( $menu_links['edit-account'] ); // Remove Account details tab
	//unset( $menu_links['woo-wallet'] ); // Remove Account details tab
	unset( $menu_links['woo-wallet'] ); // Remove Logout link
	//unset( $menu_links['customer-logout'] ); // Remove Logout link

	$lists = array();
	foreach ( $menu_links as $menu_key => $menu_name ) {
		if ( $menu_key == "customer-logout" ) {
			$lists['booking_class'] = 'رزرو کلاس';
			$lists['my_class']      = 'کلاس های من';
		}

		$lists[ $menu_key ] = $menu_name;
	}

	//rename
	$lists['edit-account'] = 'ویرایش مشخصات';

	return $lists;
}

// Add Endpoint
add_action( 'init', 'iconic_add_my_account_endpoint' );
function iconic_add_my_account_endpoint() {
	add_rewrite_endpoint( 'booking_class', EP_PAGES );
	add_rewrite_endpoint( 'my_class', EP_PAGES );
	add_rewrite_endpoint( 'change_avatar', EP_PAGES );
	add_rewrite_endpoint( 'change_class_time', EP_PAGES );
	add_rewrite_endpoint( 'test_class', EP_PAGES );
}

// Booking class
add_action( 'woocommerce_account_booking_class_endpoint', 'booking_class_endpoint_content' );
function booking_class_endpoint_content() {
	include WooCommerce_Booking_Class_Path . '/template/booking_class_endpoint_content.php';
}

// My class List
add_action( 'woocommerce_account_my_class_endpoint', 'my_class_endpoint_content' );
function my_class_endpoint_content() {
	include WooCommerce_Booking_Class_Path . '/template/my_class_endpoint_content.php';
}

// change Avatar
add_action( 'woocommerce_account_change_avatar_endpoint', 'change_avatar_endpoint_content' );
function change_avatar_endpoint_content() {
	include WooCommerce_Booking_Class_Path . '/template/change_avatar_endpoint_content.php';
}

// Test class
add_action( 'woocommerce_account_test_class_endpoint', 'test_class_endpoint_content' );
function test_class_endpoint_content() {
	include WooCommerce_Booking_Class_Path . '/template/test_class_endpoint_content.php';
}

// Change Class time
add_action( 'woocommerce_account_change_class_time_endpoint', 'change_class_time_endpoint_content' );
function change_class_time_endpoint_content() {
	include WooCommerce_Booking_Class_Path . '/template/change_class_time_endpoint_content.php';
}

