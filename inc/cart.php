<?php
add_action( 'init', 'woo_booking_add_to_cart' );
function woo_booking_add_to_cart() {

	if ( isset( $_REQUEST['_action_nonce'] ) and isset( $_REQUEST['date'] ) and isset( $_REQUEST['time'] ) and isset( $_REQUEST['class_id'] ) and is_user_logged_in() ) {

		// Check Nonce
		if ( wp_verify_nonce( $_REQUEST['_action_nonce'], 'wp-nonce-rezerv-class' ) ) {

			// Check Before Time Rezerv class
			$_before_rezerv = is_rezerv_class( $_REQUEST['date'], $_REQUEST['time'] );
			if ( $_before_rezerv === false ) {

				// Add to cart
				$product_id       = $_REQUEST['class_id'];
				$current_quantity = woo_get_item_qty( $product_id );
				if ( $current_quantity > 1 ) {

					// Set Quantity
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						if ( $product_id == $cart_item['product_id'] ) {

							// Set Quantity
							WC()->cart->set_quantity( $cart_item_key, $current_quantity + 1 );

							// Set Time
							$cart_item['time'][]                        = array( 'date' => $_REQUEST['date'], 'time' => $_REQUEST['time'] );
							WC()->cart->cart_contents[ $cart_item_key ] = $cart_item;
							WC()->cart->set_session();
						}
					}

				} else {

					$custom_data           = array();
					$custom_data['time'][] = array( 'date' => $_REQUEST['date'], 'time' => $_REQUEST['time'] );
					WC()->cart->add_to_cart( $_REQUEST['class_id'], 1, null, array(), $custom_data );
				}

				wp_redirect( home_url() . '/my-account/booking_class/' );
				exit;

			}
		}
	}


}

// Limit Time Cart
add_filter( 'wc_session_expiring', 'filter_ExtendSessionExpiring' );
add_filter( 'wc_session_expiration', 'filter_ExtendSessionExpired' );
function filter_ExtendSessionExpiring( $seconds ) {
	return 60 * 20;
}

function filter_ExtendSessionExpired( $seconds ) {
	return 60 * 20;
}

// Disable Address for Virtual Product
add_filter( 'woocommerce_checkout_fields', 'bbloomer_simplify_checkout_virtual' );
function bbloomer_simplify_checkout_virtual( $fields ) {
	$only_virtual = true;
	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		if ( ! $cart_item['data']->is_virtual() ) {
			$only_virtual = false;
		}
	}

	if ( $only_virtual ) {
		unset( $fields['billing']['billing_company'] );
		unset( $fields['billing']['billing_address_1'] );
		unset( $fields['billing']['billing_address_2'] );
		unset( $fields['billing']['billing_city'] );
		unset( $fields['billing']['billing_postcode'] );
		unset( $fields['billing']['billing_country'] );
		unset( $fields['billing']['billing_state'] );
		unset( $fields['billing']['billing_phone'] );
		add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );
	}

	return $fields;
}

// Automatic complete after purchase
add_filter( 'woocommerce_payment_complete_order_status', 'silkwave_autocomplete_paid_orders', 10, 2 );
function silkwave_autocomplete_paid_orders( $order_status, $order_id ) {
	$order = new WC_Order( $order_id );
	if ( $order_status == 'processing' && ( $order->get_status() == 'on-hold' || $order->get_status() == 'pending' || $order->get_status() == 'failed' ) ) {
		return 'completed';
	}
	return $order_status;
}

// Change thank-you Page
add_action( 'woocommerce_thankyou', 'bbloomer_redirectcustom' );
function bbloomer_redirectcustom( $order_id ) {
	$order = wc_get_order( $order_id );
	$url   = get_page_link( 3343 );
	if ( ! $order->has_status( 'failed' ) ) {
		wp_safe_redirect( $url );
		exit;
	}
}

// Success Payment
add_action( 'woocommerce_payment_complete', 'wp_sms_woocommerce_group_payment_complete' );
function wp_sms_woocommerce_group_payment_complete( $order_id ) {
	global $wpdb;

	// Get Order Detail
	$order = wc_get_order( $order_id );

	// Get User Mobile @see https://businessbloomer.com/woocommerce-easily-get-order-info-total-items-etc-from-order-object/
	$user_id = $order->get_user_id();
	if ( $user_id < 1 ) {
		return;
	}

	// Get List Of Product in this Order
	$items = $order->get_items();
	foreach ( $items as $item ) {

		// Get Basic Information of Product
		$product_name         = $item->get_name();
		$product_id           = $item->get_product_id();
		$product_variation_id = $item->get_variation_id();


		// Set Class
		$class_date = $item->get_meta( 'class_date' );
		if ( ! empty( $class_date ) and is_array( $class_date ) and isset( $class_date['date'] ) and isset( $class_date['time'] ) ) {
			$wpdb->insert(
				$wpdb->prefix . 'woo_booking',
				array(
					'user_id'             => $user_id,
					'class_date'          => $class_date['date'],
					'class_time'          => $class_date['time'] . ":00:00",
					'status'              => 1,
					'payment'             => $item->get_total(),
					'type_class'          => 1,
					'number_change_class' => 0,
				)
			);
		}

	}
}

// Replace Class time
add_action( 'init', 'woo_booking_change_class_time_action' );
function woo_booking_change_class_time_action() {
	global $wpdb;

	if ( isset( $_REQUEST['replace_class_ID'] ) and isset( $_REQUEST['_action_change_class_time_nonce'] ) and isset( $_REQUEST['date'] ) and isset( $_REQUEST['time'] ) and is_user_logged_in() ) {

		// Check Nonce
		if ( wp_verify_nonce( $_REQUEST['_action_change_class_time_nonce'], 'wp-nonce-change-class-time' ) ) {

			$class_id = $_REQUEST['replace_class_ID'];
			$user_id  = get_current_user_id();
			$item     = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}woo_booking WHERE `user_id` = {$user_id} AND `status` = 1 AND `ID` = {$class_id}", ARRAY_A );
			if ( null !== $item ) {

				// Check User Past 36
				$now        = current_time( 'timestamp' );
				$class_time = strtotime( $item['class_date'] . ' ' . $item['class_time'] );
				if ( ( $class_time - $now ) >= WooCommerce_Booking_Before_Cancel_Class ) {

					$wpdb->update(
						$wpdb->prefix . 'woo_booking',
						array(
							'class_date'          => $_REQUEST['date'],
							'class_time'          => $_REQUEST['time'] . ":00:00",
							'number_change_class' => 1,
						),
						array(
							'ID' => $class_id
						)
					);

					// Email to Admin
					add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
					$user_id         = 1;
					$user            = get_userdata( $user_id );
					$to              = $user->user_email;
					$subject         = 'تغییر ساعت کلاس توسط ';
					$subscriber_id   = get_current_user_id();
					$subscriber_data = get_userdata( $subscriber_id );
					$subject         .= $subscriber_data->first_name . ' ' . $subscriber_data->last_name;

					$body = '<html dir="rtl">';
					$body .= '<body style = "text-align:right; direction:rtl;">';
					$body .= '<p style="font-family: Tahoma; font-size: 12px;">';
					$body .= 'کاربر با نام ';
					$body .= $subscriber_data->first_name . ' ' . $subscriber_data->last_name;
					$body .= ' ';
					$body .= 'ساعت کلاس خود را از ';
					$body .= parsidate( "Y-m-d H:i", $class_time, "eng" );
					$body .= ' به ';
					$body .= parsidate( "Y-m-d H:i", strtotime( $_REQUEST['date'] . ' ' . $_REQUEST['time'] . ":00:00" ), "eng" );
					$body .= ' تغییر داد. ';
					$body .= '</p>';
					$body .= '</body>';
					$body .= '</html>';
					wp_mail( $to, $subject, $body );
					remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );

					// Redirect User
					wp_redirect( add_query_arg( array( 'alert' => 'change_class' ), home_url() . '/my-account/my_class/' ) );
					exit;
				}
			}
		}
	}
}

// Rezerv Test Class
add_action( 'init', 'woo_booking_get_test_action' );
function woo_booking_get_test_action() {
	global $wpdb;

	if ( isset( $_REQUEST['_action_class_time_test_nonce'] ) and isset( $_REQUEST['date'] ) and isset( $_REQUEST['time'] ) and is_user_logged_in() ) {

		// Check Nonce
		if ( wp_verify_nonce( $_REQUEST['_action_class_time_test_nonce'], 'wp-nonce-class-time-test' ) ) {


			// Check First Class
			$user_id      = get_current_user_id();
			$number_class = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woo_booking WHERE `user_id` = {$user_id}" );
			if ( $number_class < 1 ) {

				$wpdb->insert(
					$wpdb->prefix . 'woo_booking',
					array(
						'user_id'             => $user_id,
						'class_date'          => $_REQUEST['date'],
						'class_time'          => $_REQUEST['time'] . ":00:00",
						'status'              => 1,
						'payment'             => 0,
						'type_class'          => 2,
						'number_change_class' => 0,
					)
				);

				wp_redirect( add_query_arg( array( 'alert' => 'test_class' ), home_url() . '/my-account/my_class/' ) );
				exit;
			}
		}
	}
}


// Add Meta From Cart To Order
// https://stackoverflow.com/questions/43597491/accessing-order-items-protected-data-in-woocommerce-3
add_action( 'woocommerce_add_order_item_meta', 'process_woo_item_meta', 10, 3 );
function process_woo_item_meta( $item_id, $item_values, $cart_item_key ) {
	if ( ! empty( $item_values['time'][0] ) ) {
		wc_update_order_item_meta( $item_id, 'class_date', $item_values['time'][0] );
	}
}


// change Order Btn Text
add_filter( 'woocommerce_order_button_text', 'bbloomer_rename_place_order_button' );
function bbloomer_rename_place_order_button() {
	return 'پرداخت';
}

/**
 * Unhook and remove WooCommerce default emails.
 * @see https://docs.woocommerce.com/document/unhookremove-woocommerce-emails/
 */
add_action( 'woocommerce_email', 'unhook_those_pesky_emails' );

function unhook_those_pesky_emails( $email_class ) {

	/**
	 * Hooks for sending emails during store events
	 **/
	remove_action( 'woocommerce_low_stock_notification', array( $email_class, 'low_stock' ) );
	remove_action( 'woocommerce_no_stock_notification', array( $email_class, 'no_stock' ) );
	remove_action( 'woocommerce_product_on_backorder_notification', array( $email_class, 'backorder' ) );

	// New order emails
	remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );

	// Processing order emails
	remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
	remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );

	// Completed order emails
	remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );

	// Note emails
	remove_action( 'woocommerce_new_customer_note_notification', array( $email_class->emails['WC_Email_Customer_Note'], 'trigger' ) );
}