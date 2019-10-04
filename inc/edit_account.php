<?php

// Add the custom field "favorite_color"
add_action( 'woocommerce_edit_account_form', 'add_favorite_color_to_edit_account_form' );
function add_favorite_color_to_edit_account_form() {
	$user = wp_get_current_user();
	?>
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="billing_phone"><?php _e( 'شماره همراه', 'woocommerce' ); ?>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_phone" id="billing_phone" value="<?php echo esc_attr( get_user_meta( $user->ID, 'billing_phone', true ) ); ?>"/>
        </p>
	<?php
}

// Save the custom field 'favorite_color'
add_action( 'woocommerce_save_account_details', 'save_favorite_color_account_details', 12, 1 );
function save_favorite_color_account_details( $user_id ) {
	if ( isset( $_POST['billing_phone'] ) ) {
		update_user_meta( $user_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
	}
}

// Validate Error
add_action( 'woocommerce_save_account_details_errors', 'wooc_validate_custom_field', 10, 1 );
function wooc_validate_custom_field( $args ) {
	if ( isset( $_POST['billing_phone'] ) ) // Your custom field
	{
		if ( strlen( $_POST['billing_phone'] ) < 4 ) // condition to be adapted
		{
			$args->add( 'billing_phone_error', __( 'لطفا شماره همراه را وارد نمایید', 'woocommerce' ), '' );
		}
	}
}