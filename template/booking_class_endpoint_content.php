<?php
if ( ! WC()->cart->is_empty() ) {

	//Refresh Data From Cart Item
	foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
		if ( isset( $values['time'] ) ) {
			foreach ( $values['time'] as $list_time ) {
				$d = date( "Y-m-d", strtotime( $list_time['date'] ) );
				$t = ( $list_time['time'] < 10 ? '0' . $list_time['time'] : $list_time['time'] );
				if ( is_rezerv_class( $d, $t ) ) {
					WC()->cart->remove_cart_item( $cart_item_key );
				}
			}
		}
	}

	?>
    <div class="panel panel-default">
        <div class="panel-body">
			<?php echo do_shortcode( '[woocommerce_cart]' ); ?><?php echo do_shortcode( '[woocommerce_checkout]' ); ?>
        </div>
    </div>
	<?php
}
?>

<div class="panel panel-default">
    <div class="panel-body">
        <p>لطفا ساعت کلاس هایی که قصد رزرو آن را دارید انتخاب نمایید :</p>

        <table id="customers">
            <tr>
                <td style="width: 100px;"></td>
				<?php
				$now_day = current_time( 'timestamp' );
				for ( $x = 1; $x <= 12; $x ++ ) {
					$date_time = strtotime( "+" . $x . " day", $now_day );
					?>
                    <td style="text-align: center; width: 100px;"> <?php echo parsidate( "l", $date_time, "eng" ); ?>
                        <br> <?php echo parsidate( "j F", $date_time, "eng" ); ?> </td>
					<?php
				}
				?>
            </tr>

			<?php
			for ( $i = 6; $i <= 23; $i ++ ) {
				?>
                <tr>
                    <td> ساعت <?php echo $i; ?>:00</td>
					<?php
					$now_day = current_time( 'timestamp' );
					for ( $x = 1; $x <= 12; $x ++ ) {
						$date_time = strtotime( "+" . $x . " day", $now_day );
						$params    = array(
							'post_type'              => 'product',
							'meta_query'             => array(
								array(
									'key'     => 'class_date',
									'value'   => parsidate( "Y-n-j", $date_time, "eng" ),
									'compare' => '=',
								)
							),
							'posts_per_page'         => 1,
							'fields'                 => 'ids',
							'cache_results'          => false,
							'update_post_meta_cache' => false,
							'update_post_term_cache' => false,
						);
						$wc_query  = new WP_Query( $params );
						$p_id      = 0;
						if ( count( $wc_query->posts ) > 0 ) {
							$p_id = $wc_query->posts[0];
						}
						wp_reset_postdata();
						?>
                        <td style="text-align: center;">
							<?php
							if ( $p_id > 0 ) {
								// Check Active Class time
								$_is_active_time = get_post_meta( $p_id, 'class_time_' . $i, true );
								if ( ! empty( $_is_active_time ) and $_is_active_time == "yes" ) {

									// Is rezerv Before
									$_is_rezerv_before = is_rezerv_class( date( "Y-m-d", $date_time ), ( $i < 10 ? '0' . $i : $i ) );
									if ( $_is_rezerv_before ) {
										echo '<div class="box-select rezerve-shode" title="این ساعت توسط شخص دیگری رزرو شده است"></div>';
									} else {
										$alert = "شما کلاس روز ";
										$alert .= parsidate( "l j F y", $date_time ) . " ساعت ";
										$alert .= $i;
										$alert .= " را انتخاب کرده اید آیا مطمئن هستید ؟";

										$_if_in_cart = false;
										foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
											if ( $values['product_id'] == $p_id and isset( $values['time'] ) ) {
												foreach ( $values['time'] as $list_time ) {
													if ( $list_time['date'] == date( "Y-m-d", $date_time ) and $list_time['time'] == ( $i < 10 ? '0' . $i : $i ) ) {
														$_if_in_cart = true;
													}
												}
											}
										}

										if ( $_if_in_cart ) {
											echo '<div class="box-select" style="background: #3385ff !important;"></div>';
										} else {
											echo '<a href="' . add_query_arg( array( 'class_id' => $p_id, '_action_nonce' => wp_create_nonce( 'wp-nonce-rezerv-class' ), 'date' => date( "Y-m-d", $date_time ), 'time' => ( $i < 10 ? '0' . $i : $i ) ), home_url() . '/my-account/booking_class/' ) . '" onclick="return confirm(\'' . $alert . '\')" class="box-select ghabel-rezerv"></a>';
										}
									}

								}
							}
							?>

                        </td>
						<?php
					}
					?>
                </tr>
				<?php
			}
			?>
        </table>


    </div>
</div>

<style>
    #customers {
        font-size: 13px;
        border-collapse: collapse;
        width: 100%;
    }

    #customers td, #customers th {
        border: 1px solid rgba(221, 221, 221, 0.37);
        padding: 8px;
    }

    #customers tr:nth-child(even) {
    }

    #customers th {
        padding-top: 12px;
        padding-bottom: 12px;
        background-color: #4CAF50;
        color: white;
    }

    .container {
        width: 95%;
    }

    .box-select {
        display: block;
        width: 99%;
        height: 30px;
        background: #e3e3e3;
        border-radius: 5px;
        transition: 1s all;
    }

    .ghabel-rezerv:hover {
        background: #afafaf;
    }

    .rezerve-shode {
        background: #e1e1e1;
        cursor: no-drop;
        background-image: -webkit-repeating-linear-gradient(-45deg, transparent, transparent 4px, #f8f8f8 0, #f8f8f8 15px);
        background-image: repeating-linear-gradient(-45deg, transparent, transparent 4px, #f8f8f8 0, #f8f8f8 15px);
    }
</style>