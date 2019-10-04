<?php

/**
 * Add New WooCommerce Tab
 */
add_filter( 'woocommerce_product_data_tabs', 'wp_sms_woocommerce_group_product_data_tab' );
function wp_sms_woocommerce_group_product_data_tab( $product_data_tabs ) {
	$product_data_tabs['wp_class_woocommerce_group_product_data'] = array(
		'label'  => __( 'ساعت کلاس', 'woocommerce' ),
		'target' => 'wp_class_woocommerce_group_product_data',
		'class'  => array( 'show_if_virtual' ),
	);
	return $product_data_tabs;
}

add_action( 'admin_head', 'wp_sms_woocommerce_group_product_data_tab_style' );
function wp_sms_woocommerce_group_product_data_tab_style() { ?>
    <style>
        #woocommerce-product-data ul.wc-tabs li.wp_class_woocommerce_group_product_data a:before {
            font-family: WooCommerce;
            content: '\e023';
        }
    </style>
	<?php
}

/**
 * Data Field
 */
add_action( 'woocommerce_product_data_panels', 'wp_sms_woocommerce_group_product_data_field' );
function wp_sms_woocommerce_group_product_data_field() {
	global $post, $wpdb;
	// Note the 'id' attribute needs to match the 'target' parameter set above


	$class_date      = get_post_meta( $post->ID, 'class_date', true );
	$class_date_time = array();
	if ( ! empty( $class_date ) ) {
		$class_date_time = explode( "-", $class_date );
	}
	?>
    <div id='wp_class_woocommerce_group_product_data' class='panel woocommerce_options_panel'> <?php
		?>


        <p class="form-field class_time_6_field ">
            <label for="class_date">تاریخ کلاس</label>

            <select name="class-date-day">
				<?php
				$select_day = parsidate( "j", current_time( 'timestamp' ), "eng" );
				if ( ! empty( $class_date_time ) ) {
					$select_day = $class_date_time[2];
				}

				for ( $i = 1; $i <= 31; $i ++ ) {
					?>
                    <option value="<?php echo $i; ?>" <?php echo( $i == $select_day ? 'selected' : '' ) ?>><?php echo $i; ?></option>
					<?php
				}
				?>
            </select>

			<?php
			$select_mon = parsidate( "n", current_time( 'timestamp' ), "eng" );
			if ( ! empty( $class_date_time ) ) {
				$select_mon = $class_date_time[1];
			}
			?>
            <select name="class-date-month">
                <option value="1" <?php echo( $select_mon == 1 ? 'selected' : '' ) ?>>فروردین</option>
                <option value="2" <?php echo( $select_mon == 2 ? 'selected' : '' ) ?>>اردیبهشت</option>
                <option value="3" <?php echo( $select_mon == 3 ? 'selected' : '' ) ?>>خرداد</option>
                <option value="4" <?php echo( $select_mon == 4 ? 'selected' : '' ) ?>>تیر</option>
                <option value="5" <?php echo( $select_mon == 5 ? 'selected' : '' ) ?>>مرداد</option>
                <option value="6" <?php echo( $select_mon == 6 ? 'selected' : '' ) ?>>شهریور</option>
                <option value="7" <?php echo( $select_mon == 7 ? 'selected' : '' ) ?>>مهر</option>
                <option value="8" <?php echo( $select_mon == 8 ? 'selected' : '' ) ?>>آبان</option>
                <option value="9" <?php echo( $select_mon == 9 ? 'selected' : '' ) ?>>آذر</option>
                <option value="10" <?php echo( $select_mon == 10 ? 'selected' : '' ) ?>>دی</option>
                <option value="11" <?php echo( $select_mon == 11 ? 'selected' : '' ) ?>>بهمن</option>
                <option value="12" <?php echo( $select_mon == 12 ? 'selected' : '' ) ?>>اسفند</option>
            </select>

			<?php
			$year_now = $select_year = parsidate( "Y", current_time( 'timestamp' ), "eng" );


			if ( ! empty( $class_date_time ) ) {
				$select_year = $class_date_time[0];
			}
			?>
            <select name="class-date-year">
                <option value="<?php echo $year_now; ?>" <?php echo( $select_year == $year_now ? 'selected' : '' ) ?>><?php echo $year_now; ?></option>
                <option value="<?php echo $year_now + 1; ?>" <?php echo( $select_year == ( $year_now ) + 1 ? 'selected' : '' ) ?>><?php echo $year_now + 1; ?></option>
            </select>
        </p>


        <div class='options_group'>
            <p><?php _e( 'ساعت کلاس ها را انتخاب کنید', 'wp-sms-woocommerce-group' ); ?>: </p>
			<?php

			// Get List WP-SMS Group
			for ( $i = 6; $i <= 23; $i ++ ) {
				woocommerce_wp_checkbox(
					array(
						'id'          => 'class_time_' . $i,
						'label'       => 'ساعت ' . $i,
						'description' => '',
						'default'     => 'no'
					)
				);
			}
			?>
        </div>
    </div>
	<?php
}

add_action( 'woocommerce_process_product_meta_simple', 'wp_sms_woocommerce_group_product_save_custom_fields' );
function wp_sms_woocommerce_group_product_save_custom_fields( $post_id ) {
	global $post, $wpdb;
	for ( $i = 6; $i <= 23; $i ++ ) {
		$checkbox = isset( $_POST[ 'class_time_' . $i ] ) ? 'yes' : 'no';
		update_post_meta( $post_id, 'class_time_' . $i, $checkbox );
	}

	// save class date
	$class_date = $_POST['class-date-year'] . '-' . $_POST['class-date-month'] . '-' . $_POST['class-date-day'];
	update_post_meta( $post_id, 'class_date', $class_date );
}


// Automatic change Title
add_action( 'save_post', 'woo_booking_save_post_function', 10, 3 );
function woo_booking_save_post_function( $post_ID, $post, $update ) {
	global $wpdb;

	if ( $update and $post->post_type == "product" ) {
		$terms = wp_get_post_terms( $post->ID, 'product_cat' );
		if ( has_term( 'class-time', 'product_cat', $post ) ) {

			$get_class_time = get_post_meta( $post->ID, 'class_date', true );
			if ( ! empty( $get_class_time ) ) {

				$wpdb->update(
					$wpdb->posts,
					array(
						'post_title' => parsidate( "l j F y", gregdate( "Y-m-d", $get_class_time ) ),    // string
						'post_name'  => 'class-' . $get_class_time    // integer (number)
					),
					array( 'ID' => $post_ID )
				);

			}
		}
	}
}

// Redirect Class Product to home
add_action( 'wp', 'product_out_of_stock_redirect' );
function product_out_of_stock_redirect() {
	global $post;
	if ( is_product() ) { // Targeting single product pages only
		//$product = wc_get_product( $post->ID );
		//if ( ! $product->is_in_stock() ) {}
			if ( has_term( 'class-time', 'product_cat', $post ) ) {
				wp_redirect( home_url() );
				exit();
			}
	}
}

//Disabl WC-Ajax request Cart
add_action('wp_enqueue_scripts', 'perfmatters_disable_woocommerce_cart_fragmentation', 99);
function perfmatters_disable_woocommerce_cart_fragmentation() {
	wp_dequeue_script('wc-cart-fragments');
}