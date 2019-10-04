<?php
add_action( 'wp_head', 'woo_booking_top_bar', 99 );

/*
 * top_bar_tag.html(`<?php echo $full_name; ?> خوش آمدید ‍!
			<a style="color: #d81a1a;margin-right: 10px;" onclick="return confirm('آیا مطمئن هستید ؟')" href="<?php echo wp_logout_url( home_url() ); ?>">خروج</a>
			`);
 */
function woo_booking_top_bar() {
	?>
    <script>
        jQuery(document).ready(function ($) {
            var top_bar_tag = $("#top-user-panel-state");
			<?php
			if(is_user_logged_in()) {
	        $current_user = get_userdata( get_current_user_id() );
	        $full_name = $current_user->first_name.' '.$current_user->last_name;
			?>
			top_bar_tag.html(`<?php echo $full_name; ?> خوش آمدید ‍!
			<a style="color: #3385ff;margin-right: 10px;" href="<?php echo (get_current_user_id() <2 ? admin_url('index.php') : get_permalink( wc_get_page_id( 'myaccount' ) )); ?>">پنل کاربری</a>
			`);
			<?php
			} else {
			?>
	        top_bar_tag.html(`<a href="<?php echo get_permalink( wc_get_page_id( 'myaccount' ) ); ?>" style="
    display: block;
    background: #3385ff;
    color: #fff;
    padding: 8px;
    border-radius: 10px;
    min-width: 150px;
    text-align: center;
    margin-left: -14px;
">ورود یا ثبت نام</a>`);

			<?php
			}
			?>
        });
    </script>

	<?php
}