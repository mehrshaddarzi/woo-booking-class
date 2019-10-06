<?php

// https://www.cloudways.com/blog/add-woocommerce-registration-form-fields/

add_action( 'woocommerce_register_form_start', 'bbloomer_add_name_woo_account_registration' );
function bbloomer_add_name_woo_account_registration() {
	?>

    <p class="form-row form-row-first">
        <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) {
			esc_attr_e( $_POST['billing_first_name'] );
		} ?>"/>
    </p>

    <p class="form-row form-row-last">
        <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?>
            <span class="required">*</span></label>
        <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) {
			esc_attr_e( $_POST['billing_last_name'] );
		} ?>"/>
    </p>

    <p class="form-row form-row-wide">
        <label for="reg_billing_phone"><?php _e( 'شماره همراه', 'woocommerce' ); ?></label>
        <input type="text" style="text-align: left;" placeholder="09xxxxxxxxx" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php esc_attr_e( $_POST['billing_phone'] ); ?>"/>
    </p>

    <div class="clear"></div>

	<?php
}

///////////////////////////////
// 2. VALIDATE FIELDS

add_filter( 'woocommerce_registration_errors', 'bbloomer_validate_name_fields', 10, 3 );

function bbloomer_validate_name_fields( $errors, $username, $email ) {
	if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
		$errors->add( 'billing_first_name_error', __( ' نام خود را وارد کنید!', 'woocommerce' ) );
	}
	if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
		$errors->add( 'billing_last_name_error', __( 'نام خانوادگی خود را وارد کنید!.', 'woocommerce' ) );
	}
	if ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) {
		$errors->add( 'billing_phone_error', __( 'شماره همراه را وارد نمایید.', 'woocommerce' ) );
	}

	return $errors;
}

///////////////////////////////
// 3. SAVE FIELDS

add_action( 'woocommerce_created_customer', 'bbloomer_save_name_fields' );

function bbloomer_save_name_fields( $customer_id ) {
	if ( isset( $_POST['billing_first_name'] ) ) {
		update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
		update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
	}
	if ( isset( $_POST['billing_phone'] ) ) {
		update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
	}
	if ( isset( $_POST['billing_last_name'] ) ) {
		update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
		update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
	}

}


// Add Fields To Users.php
add_filter( 'manage_users_columns', 'pippin_add_user_id_column' );
function pippin_add_user_id_column( $columns ) {
	$columns['billing_phone'] = 'شماره همراه';
	$columns['all_class']     = 'کل کلاس ها';
	$columns['anjam_class']   = 'برگزار شده';
	unset( $columns['posts'] );
	return $columns;
}

add_action( 'manage_users_custom_column', 'pippin_show_user_id_column_content', 10, 3 );
function pippin_show_user_id_column_content( $value, $column_name, $user_id ) {
	global $wpdb;
	$user = get_userdata( $user_id );
	if ( 'billing_phone' == $column_name ) {
		return get_user_meta( $user_id, 'billing_phone', true );
	}
	if ( 'all_class' == $column_name ) {
		$_s = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woo_booking WHERE `user_id` = {$user_id}" );
		return number_format( $_s );
	}
	if ( 'anjam_class' == $column_name ) {
		$_s = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woo_booking WHERE `user_id` = {$user_id} AND `status` = 2" );
		return number_format( $_s );
	}

	return $value;
}