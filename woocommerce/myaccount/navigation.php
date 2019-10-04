<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation">

    <!-- User Image -->
	<?php
	$user_id = get_current_user_id();
	if ( has_wp_user_avatar( $user_id ) ) {
		$user_avatar = get_user_meta( $user_id, 'wp_user_avatar', true );
	} else {
		$user_avatar = 3357;
	}
	$avatar_src = wp_get_attachment_image_src( $user_avatar, 'thumbnail' );
	?>
    <div class="photo">
        <a href="<?php echo home_url(); ?>/my-account/change_avatar/">
        <img style="cursor: pointer;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: block;
    margin: 0px auto;
    margin-top: -57px;
    margin-bottom: 19px;" data-function="select_user_avatar" src="<?php echo $avatar_src[0]; ?>">
        </a>
    </div>

    <ul>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
            <li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
                <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
            </li>
		<?php endforeach; ?>
    </ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
