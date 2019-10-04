<?php

add_action( 'wp_head', 'woo_booking_css', 99 );
function woo_booking_css() {
	?>
	<style>
		.main-nav.mainnav--active-bg > ul > .menu-item.active > a:before, .main-nav.mainnav--active-bg > ul > .menu-item > a:hover:before, .main-nav.mainnav--active-bg > ul > .menu-item:hover > a:before {
			background-color: #cd2122;
		}

		form.woocommerce-form.woocommerce-form-register.register {
			border: 0px;
		}

		form.woocommerce-form.woocommerce-form-register.register button[name=register] {
			display: block !important;
			width: 100%;
			background: #cd2122 !important;
			border: 0px;
			font-family: tahoma;
			color: #fff;
			font-weight: normal;
		}

		.zn-wc-pages-style2 .woocommerce form.login {
			background-color: transparent !important;
			padding: 25px 0px 35px !important;
			border: 0;
			border-radius: 0;
			margin: 0;
			border-right: 1px solid #e3e3e3;
			padding-right: 50px !important;
		}

		.u-column1.col-1 h2 {
			margin-right: 50px;
		}

		button.woocommerce-button.button.woocommerce-form-login__submit {
			display: block !important;
			width: 100%;
			background: #3385ff;
			border: 0px;
			font-family: tahoma;
			color: #fff;
			font-weight: normal;
			margin: 21px 0px 0px 0px !important;
		}

		.zn-wc-pages-style2 .woocommerce #respond input#submit:hover, .zn-wc-pages-style2 .woocommerce a.button:hover, .zn-wc-pages-style2 .woocommerce button.button:hover, .zn-wc-pages-style2 .woocommerce input.button:hover {
			background: #ad0c0d !important;
		}

		.woocommerce-account .woocommerce-MyAccount-navigation {
			float: right;
			width: 200px;
			background: #fff;
			padding: 20px 10px;
			border-radius: 15px;
		}

		.woocommerce-account .woocommerce-MyAccount-content {
			float: left;
			width: calc(100% - 220px);
		}

		button.woocommerce-Button.button {
			display: block !important;
			width: 100%;
			background: #3385ff !important;
			border: 0px !important;
			font-family: tahoma;
			color: #fff !important;
			font-weight: normal !important;
			margin: 21px 0px 0px 0px !important;
		}

        .site-footer {
            font-family: "iransans", Helvetica, Arial, sans-serif;
            font-size: 16px !important;
            line-height: 35px !important;
        }

        .panel {
            margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid transparent;
            border-radius: 4px;
            -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
            box-shadow: 0 1px 1px rgba(0,0,0,.05);
            font-weight: normal;
            padding: 25px;
            font-size: 14.5px;
            line-height: 26px;
        }

        div#customer_details {
            display: none;
        }
        h3#order_review_heading {
            display: none;
        }
        .zn-wc-pages-style2 .checkout.woocommerce-checkout {
            background-color: #fff;
            padding: 50px;
            margin-top: 0px !important;
        }
        .woocommerce-form-coupon-toggle {
            display: none;
        }
        button#place_order {
            width: 100%;
            font-family: iranSans;
        }
        .return-to-shop {
            display:none;
        }


        /* Media Query */
        @media (max-width: 768px) {

            .woocommerce-MyAccount-navigation, .woocommerce-MyAccount-content {
                float: none !important;
                width: 100% !important;
            }
            .panel-default {
                width: 100% !important;
                float: none !important;
                overflow-x: auto !important;
                margin-top: 15px !important;
            }
        }


        /* Remove sub Header Page */
        .page-subheader.uh_flat_redish, .kl-slideshow.uh_flat_redish {
            background-color: transparent !important;
        }
        .ph-content-v-center {
            display:none;
        }
        .page-subheader.uh_flat_redish.page-subheader--inherit-hp {
            height: 150px !important;
            min-height: 150px !important;
        }
	</style>
	<?php
}