<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$user = get_userdata( get_current_user_id() );
?>

    <div class="panel panel-default">
        <div class="panel-body">
			<?php echo $user->first_name . ' ' . $user->last_name; ?> خوش آمدید !<br/>
            تاریخ عضویت :
			<?php echo parsidate( "l j F y", $user->user_registered ); ?>
            <br/>
            شماره همراه :
			<?php echo get_user_meta( $user->ID, 'billing_phone', true );; ?>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-body">
                <div style="text-align: center;">
                    کلاس های برگزار شده
                    <div style="font-size: 33px;font-family: Calibri; display: block; margin-top: 10px;color: #3385ff;"><?php echo number_format( woo_booking_class_success( get_current_user_id() ) ); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-body">
                <div style="text-align: center;">
                    کلاس های رزرو شده
                    <div style="font-size: 33px;font-family: Calibri; display: block; margin-top: 10px;color: #3385ff;"><?php echo number_format( woo_booking_class_pre_success( get_current_user_id() ) ); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>

<?php
global $wpdb;
$user_id      = get_current_user_id();
$number_class = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woo_booking WHERE `user_id` = {$user_id}" );
if ( $number_class < 1 ) {
	?>
    <a href="<?php echo home_url() . '/my-account/test_class/'; ?>" style="    display: inline-block;
    background: #cd2122;
    float: left;
    min-width: 280px;
    height: 45px;
    text-align: center;
    line-height: 19px;
    color: #fff;
    border-radius: 5px;
    font-size: 18px;
    padding: 15px;">رزرو جلسه آزمایشی رایگان</a>
	<?php
}
