<?php
/**
 * Plugin Name: WooCommerce Booking Class
 * Description: WooCommerce Booking class System
 * Plugin URI:  https://realwp.net
 * Version:     1.0
 * Author:      Mehrshad Darzi
 * Author URI:  ttps://realwp.net
 * License:     MIT
 * Text Domain: woo-booing-system
 * Domain Path: /languages
 */

// Create a taxonomy in woocommece has_term( 'class-time', 'product_cat', $post )
// Use wp-user-avatar @https://wordpress.org/plugins/wp-user-avatar/
// Use Wp-parsidate For Calender
// Refresh permalink After setup

// Define Variable
define( 'WooCommerce_Booking_Class_Url', plugins_url( '/', __FILE__ ) );
define( 'WooCommerce_Booking_Class_Path', plugin_dir_path( __FILE__ ) );

// Content Variable
define( 'WooCommerce_Booking_Before_Cancel_Class', 60 * 60 * 40 );


// Activation Hook
register_activation_hook( __FILE__, 'woo_booking_activate' );
function woo_booking_activate() {
	global $wpdb;

	/*
	 * Create Base Table in mysql
	 */
	$charset_collate = $wpdb->get_charset_collate();
	$table_name      = $wpdb->prefix . 'woo_booking';
	$sql             = "CREATE TABLE $table_name (
				`ID` BIGINT(48) NOT NULL AUTO_INCREMENT, 
				`user_id` BIGINT NOT NULL, 
				`class_date` DATE NOT NULL, 
				`class_time` TIME NOT NULL, 
				`status` INT(1) NOT NULL COMMENT '1= bargoza nashod 2 = shod', 
				`link` TEXT NOT NULL, 
				`payment` INT NOT NULL, 
				`comment` TEXT NOT NULL,
				`type_class` INT(1) NOT NULL COMMENT '1= asli 2 = azmayeshi',
				`number_change_class` INT(1) NOT NULL,
                PRIMARY KEY  (ID)) {$charset_collate};";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

// Top Login Btn
require_once WooCommerce_Booking_Class_Path . '/inc/helper.php';
require_once WooCommerce_Booking_Class_Path . '/inc/top.php';
require_once WooCommerce_Booking_Class_Path . '/inc/css.php';
require_once WooCommerce_Booking_Class_Path . '/inc/register.php';
require_once WooCommerce_Booking_Class_Path . '/inc/admin_head.php';
require_once WooCommerce_Booking_Class_Path . '/inc/user_menu.php';
require_once WooCommerce_Booking_Class_Path . '/inc/edit_account.php';
require_once WooCommerce_Booking_Class_Path . '/inc/product_tab.php';
require_once WooCommerce_Booking_Class_Path . '/inc/cart.php';
require_once WooCommerce_Booking_Class_Path . '/inc/admin_page.php';

// WooCommerce
add_filter( 'woocommerce_locate_template', 'woo_booking_locate_template', 10, 3 );
function woo_booking_locate_template( $template, $template_name, $template_path ) {
	$re = '/woocommerce\/(templates\/)?(.*)/m';
	preg_match( $re, $template, $matches );
	if ( isset( $matches[2] ) && ! empty( $matches[2] ) && file_exists( WooCommerce_Booking_Class_Path . 'woocommerce/' . $matches[2] ) ) {
		$template = WooCommerce_Booking_Class_Path . 'woocommerce/' . $matches[2];
	}
	return $template;
}
