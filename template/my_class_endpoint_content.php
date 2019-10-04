<?php
global $wpdb;
$user_id = get_current_user_id();
$query   = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}woo_booking` WHERE `user_id` = {$user_id} AND `status` = 1 ORDER BY `class_date` ASC, `class_time` ASC", ARRAY_A );
if ( $wpdb->num_rows == 0 ) {
	?>
    <div class="panel panel-default">
        <div class="panel-body">
            <p style="text-align: center;">شما هیچ کلاسی برای تشکیل در آینده ندارید.</p>
        </div>
    </div>
	<?php
} else {

	// Alert
	if ( isset( $_GET['alert'] ) and $_GET['alert'] == "change_class" ) {
		?>
        <div class="panel panel-default" style="    background: #3385ff;
    color: #fff;">
            <div class="panel-body">
                <p style="margin: 0px;">تاریخ کلاس با موفقیت تغییر پیدا کرد.</p>
            </div>
        </div>
		<?php
	}

	// Alert Class Test
	if ( isset( $_GET['alert'] ) and $_GET['alert'] == "test_class" ) {
		?>
        <div class="panel panel-default" style="    background: #3385ff;
    color: #fff;">
            <div class="panel-body">
                <p style="margin: 0px;">کلاس آزمایشی شما با موفقیت رزرو شد.</p>
            </div>
        </div>
		<?php
	}


	foreach ( $query as $item ) {
		?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-2">
						<?php
						$user_id     = 1;
						$user_avatar = get_user_meta( $user_id, 'wp_user_avatar', true );
						$avatar_src  = wp_get_attachment_image_src( $user_avatar, 'thumbnail' );
						?>

                        <img src="<?php echo $avatar_src[0]; ?>" alt="استاد علی زرین قبا" title="استاد علی زرین قبا" style="width: 50px; height: auto; border-radius: 50%">
                    </div>
                    <div class="col-sm-3" style="line-height: 45px;">تاریخ کلاس : <?php echo parsidate( "l j F y", strtotime( $item['class_date'] ) ); ?></div>
                    <div class="col-sm-2" style="line-height: 45px;">ساعت کلاس : <?php $e = explode( ":", $item['class_time'] );
						echo $e[0] . ':' . $e[1]; ?></div>
                    <div class="col-sm-5">

						<?php
						$now        = current_time( 'timestamp' );
						$class_time = strtotime( $item['class_date'] . ' ' . $item['class_time'] );
						if ( ( $class_time - $now ) >= WooCommerce_Booking_Before_Cancel_Class and $item['number_change_class'] == 0 ) {
							?>
                            <a href="<?php echo add_query_arg( array( 'class_id' => $item['ID'], 'nonce_change_class' => wp_create_nonce( 'change-class-time' ) ), home_url() . '/my-account/change_class_time/' ); ?>" class="change-btn-class">تغییر ساعت کلاس</a>
							<?php
						}
						?>

						<?php
						if ( trim( $item['link'] ) == "" ) {
							?>
                            <a href="#" onClick="alert('چند دقیقه قبل از شروع کلاس لینک ورود به کلاس فعال می شود')" class="no-btn-class">ورود به کلاس</a>
							<?php
						} else {
							?>
                            <a target="_blank" href="<?php echo trim( $item['link'] ); ?>" class="yes-btn-class">ورود به کلاس</a>
							<?php
						}
						?>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}
}

?>
<style>

    .container {
        width: 95%;
    }

    .yes-btn-class {
        display: inline-block;
        width: 150px;
        background: #4f94fa;
        color: #fff;
        text-align: center;
        padding: 5px;
        border-radius: 5px;
        margin-top: 6px;
    }

    .yes-btn-class:hover, .change-btn-class:hover {
        color: #fff !important;
    }

    .no-btn-class {
        display: inline-block;
        width: 150px;
        background: #c5c5c5 !important;
        color: #343434 !important;
        text-align: center;
        padding: 5px;
        border-radius: 5px;
        margin-top: 6px;
    }

    .change-btn-class {
        display: inline-block;
        width: 150px;
        background: #fa9f1c;
        color: #fff;
        text-align: center;
        padding: 5px;
        border-radius: 5px;
        margin-top: 6px;
    }
</style>
