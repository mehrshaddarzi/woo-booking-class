<?php

// Admin Menu
add_action( 'admin_menu', 'woo_booking_admin_menu', 10 );
function woo_booking_admin_menu() {
	$hook = add_menu_page( __( 'لیست کلاس ها', 'wp-extensions' ), __( 'لیست کلاس ها', 'wp-extensions' ), 'manage_options', 'woo_class_list', array( 'Woo_class_list_init', 'init' ), 'dashicons-megaphone' );
	add_action( "load-$hook", array( 'Woo_class_list_init', 'screen_option' ) );
}

// Show Page
function woo_class_list_init() {
	echo 'df';
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Wp_List_Table_Woo_class_list extends \WP_List_Table {

	/** Class constructor */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'wp_extensions_notifications',
			'plural'   => 'wp_extensions_notifications',
			'ajax'     => false
		) );
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		//Column Option
		$this->_column_headers = $this->get_column_info();

		//Process Bulk and Row Action
		$this->process_bulk_action();

		//Prepare Data
		$per_page     = $this->get_items_per_page( 'woo_booking_list_table_per_page', 15 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		//Create Pagination
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page
		) );

		//return items
		$this->items = self::get_actions( $per_page, $current_page );
	}

	/**
	 * Retrieve Items data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_actions( $per_page = 15, $page_number = 1 ) {
		global $wpdb;

		// Base Table
		$tbl = $wpdb->prefix . 'woo_booking';

		// We Get Only `ID` from Table
		$sql = "SELECT * FROM `$tbl`";

		// Where conditional
		$conditional = self::conditional_sql();
		if ( ! empty( $conditional ) ) {
			$sql .= ' WHERE ' . implode( ' AND ', $conditional );
		}

		// Check Order By
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
		} else {
			$sql .= ' ORDER BY `ID`';
		}

		//Check Order Fields
		$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' DESC';
		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		// Return Data
		return $wpdb->get_results( $sql, 'ARRAY_A' );
	}

	/**
	 * Conditional sql
	 */
	public static function conditional_sql() {
		global $wpdb;

		//Where conditional
		$where = false;

		// Only Not Bargozar shode
		$where[] = "`status` = 1";

		// Check Search
		if ( isset( $_REQUEST['s'] ) and ! empty( $_REQUEST['s'] ) ) {
			$search  = sanitize_text_field( $_REQUEST['s'] );
			$where[] = "(`title` LIKE '%{$search}%' OR `message` LIKE '%{$search}%')";
		}

		return $where;
	}

	/**
	 * Delete a action record.
	 *
	 * @param int $ID action ID
	 */
	public static function delete_action( $ID ) {

	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		// Table name
		$tbl = $wpdb->prefix . 'woo_booking';

		// Base SQL
		$sql = "SELECT COUNT(*) FROM `$tbl`";

		//Where conditional
		$conditional = self::conditional_sql();
		if ( ! empty( $conditional ) ) {
			$sql .= ' WHERE ' . implode( ' AND ', $conditional );
		}

		return $wpdb->get_var( $sql );
	}

	/**
	 * Not Found Item Text
	 */
	public function no_items() {
		_e( 'هیچ کلاسی در دسترس نیست', 'wp-extensions' );
	}

	/**
	 *  Associative array of columns
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'      => '<input type="checkbox" />',
			'name'    => __( 'نام و نام خانوادگی', 'wp-extensions' ),
			'mobile'  => __( 'شماره همراه', 'wp-extensions' ),
			'date'    => __( 'تاریخ کلاس', 'wp-extensions' ),
			'time'    => __( 'ساعت کلاس', 'wp-extensions' ),
			'type'    => __( 'نوع کلاس', 'wp-extensions' ),
			'payment' => __( 'مبلغ پرداخت شده', 'wp-extensions' ),
			'id' => __( 'شناسه کلاس', 'wp-extensions' ),
		);

		return $columns;
	}

	/**
	 * Render the bulk edit checkbox
	 * @param array $item
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="bulk-read[]" value="%s" />', $item['ID'] );
	}

	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {


		// List fields
		switch ( $column_name ) {
            case 'id':
                return $item['ID'];
                break;

			case 'name' :

				// User
				$user = get_userdata( $item['user_id'] );

				// Get Name
				$text = $user->first_name . ' ' . $user->last_name;

				// row actions to mark as read
				$actions['mark_success'] = '<a onclick="return confirm(\'' . __( 'آیا مطمئن هستید ؟', 'wp-extensions' ) . '\');" href="' . add_query_arg( array( 'page' => 'woo_class_list', 'action' => 'mark_success', '_wpnonce' => wp_create_nonce( 'mark_as_success' ), 'ID' => $item['ID'] ), admin_url( "admin.php" ) ) . '">' . __( 'کلاس برگزار شد', 'wp-extensions' ) . '</a>';

				// View Notification
				add_thickbox();
				$text .= '<div id="my-content-id-' . $item['ID'] . '" style="display:none;">
								     
	<form action="" method="post">
	<input name="update_link_class" type="hidden" value="' . $item['ID'] . '">
    <table class="form-table">
        <tbody>
        <tr class="form-field form-required">
            <th scope="row">
                <label for="default_access_cap">لینک ورود به کلاس</label>
            </th>
            <td>
            <input type="text" name="link" value="' . $item['link'] . '" style="text-align: left; direction: ltr;" autocomplete="off">
             </td>
        </tr>
        
         <tr class="form-field form-required">
            <th scope="row">
                <label for="default_access_cap">تاریخ تشکیل کلاس</label>
            </th>
            <td>
             <select name="class-date-day">
			';

				$select_day = parsidate( "j", strtotime( $item['class_date'] . ' ' . $item['class_time'] ), "eng" );
				for ( $i = 1; $i <= 31; $i ++ ) {

					$text .= ' <option value="' . $i . '" ' . ( $i == $select_day ? "selected" : "" ) . '>' . $i . '</option>';

				}

				$text .= '
            </select>
              ';

				$select_mon = parsidate( "n", strtotime( $item['class_date'] . ' ' . $item['class_time'] ), "eng" );

				$text .= '
            <select name="class-date-month">
                <option value="1" ' . ( $select_mon == 1 ? "selected" : "" ) . '>فروردین</option>
                <option value="2" ' . ( $select_mon == 2 ? "selected" : "" ) . '>اردیبهشت</option>
                <option value="3" ' . ( $select_mon == 3 ? "selected" : "" ) . '>خرداد</option>
                <option value="4" ' . ( $select_mon == 4 ? "selected" : "" ) . '>تیر</option>
                <option value="5" ' . ( $select_mon == 5 ? "selected" : "" ) . '>مرداد</option>
                <option value="6" ' . ( $select_mon == 6 ? "selected" : "" ) . '>شهریور</option>
                <option value="7" ' . ( $select_mon == 7 ? "selected" : "" ) . '>مهر</option>
                <option value="8" ' . ( $select_mon == 8 ? "selected" : "" ) . '>آبان</option>
                <option value="9" ' . ( $select_mon == 9 ? "selected" : "" ) . '>آذر</option>
                <option value="10" ' . ( $select_mon == 10 ? "selected" : "" ) . '>دی</option>
                <option value="11" ' . ( $select_mon == 11 ? "selected" : "" ) . '>بهمن</option>
                <option value="12" ' . ( $select_mon == 12 ? "selected" : "" ) . '>اسفند</option>
            </select>
';

				$select_year = parsidate( "Y", strtotime( $item['class_date'] . ' ' . $item['class_time'] ), "eng" );
				$year_now    = parsidate( "Y", current_time( 'timestamp' ), "eng" );

				$text .= '
            <select name="class-date-year">
                <option value="' . $year_now . '" ' . ( $select_year == $year_now ? "selected" : "" ) . '>' . $year_now . '</option>
                <option value="' . ( $year_now + 1 ) . '" ' . ( $select_year == ( $year_now ) + 1 ? "selected" : "" ) . '>' . ( $year_now + 1 ) . '</option>
            </select>
             </td>
        </tr>
        
        
          <tr class="form-field form-required">
            <th scope="row">
                <label for="default_access_cap">ساعت تشکیل کلاس</label>
            </th>
            <td>
            
            <select name="class-date-time-hour">';
				for ( $i = 6; $i <= 23; $i ++ ) {
					$real_timer = explode( ":", $item['class_time'] );
					$real_time  = $real_timer[0];
					$text       .= '<option value="' . ( $i < 10 ? '0' . $i : $i ) . '" ' . ( $real_time == ( $i < 10 ? '0' . $i : $i ) ? "selected" : "" ) . '>' . $i . '</option>';
				}
				$text            .= '
            </select>
            </td>
            </tr>
        

        <tr class="form-field">
            <th scope="row" style="padding-bottom: 10px;">
                <input type="submit" class="button button-primary" value="ویرایش">
            </th>
        </tr>
        </tbody>
    </table>
</form>

<div class="wp-clearfix"></div>
								     
								     
								     
								     
								</div>';
				$actions['view'] = '<a href="#TB_inline?&width=600&height=550&inlineId=my-content-id-' . $item['ID'] . '" class="thickbox">' . __( 'اطلاعات', 'wp-extensions' ) . '</a>';

				// show
				return $text . $this->row_actions( $actions ) . '<div class="wp-clearfix"></div>';
				break;

			case 'mobile':

				return get_user_meta( $item['user_id'], 'billing_phone', true );
				break;


			case 'date' :
				$date = date_i18n( "l j F Y", strtotime( $item['class_date'] ) );
				return $date;
				break;

			case 'time' :
				$time = explode( ":", $item['class_time'] );
				return $time[0] . ":" . $time[1];
				break;

			case 'type' :
				return ( $item['type_class'] == "1" ? 'کلاس غیر رایگان' : 'کلاس آزمایشی' );
				break;

			case 'payment' :
				return wp_strip_all_tags( wc_price( $item['payment'] ) );
				break;

			default:
				return '<span aria-hidden="true">—</span><span class="screen-reader-text">' . __( "Unknown", 'wp-extensions' ) . '</span>';
				break;
		}

	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'date' => array( 'class_date', false )
		);

		return $sortable_columns;
	}

	/**
	 * Show SubSub Filter
	 */
	protected function get_views() {
		global $wpdb;
		$views = array();

		// Return Data
		return $views;
	}

	/**
	 * Advance Custom Filter
	 *
	 * @param $which
	 */
	function extra_tablenav( $which ) {
		global $wpdb;

	}

	/**
	 * Returns an associative array containing the bulk action
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(//'bulk-read' => __( 'Mark as Read', 'wp-extensions' ),
		);

		return $actions;
	}

	/**
	 * Search Box
	 *
	 * @param $text
	 * @param $input_id
	 */
	public function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
			return;
		}

		$input_id = $input_id . '-search-input';
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		}
		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		}
		?>
        <p class="search-box">
            <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
            <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" autocomplete="off"/>
			<?php submit_button( $text, 'button', false, false, array( 'id' => 'search-submit' ) ); ?>
        </p>
		<?php
	}

	/**
	 * Bulk and Row Actions
	 */
	public function process_bulk_action() {
		global $wpdb;

		// Row Action Mark Read
		if ( 'mark_success' === $this->current_action() ) {
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'mark_as_success' ) ) {
				wp_die( __( "You are not permission for this action.", "wp-extensions" ) );
			} else {

				$wpdb->update(
					$wpdb->prefix . 'woo_booking',
					array(
						'status' => 2,    // string
					),
					array( 'ID' => $_REQUEST['ID'] )
				);

				wp_redirect( esc_url_raw( add_query_arg( array( 'page' => 'woo_class_list', 'alert' => 'mark_as_success' ), admin_url( "admin.php" ) ) ) );
				exit;
			}
		}


		// Update Class
		if ( isset( $_REQUEST['update_link_class'] ) and isset( $_REQUEST['link'] ) ) {

			// Now Class Date and Class time
			$ID         = $_REQUEST['update_link_class'];
			$current    = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}woo_booking WHERE ID = {$ID}", ARRAY_A );
			$class_date = $current['class_date'];
			$class_time = $current['class_time'];

			// convert New Date
			$new_shamsi_year   = $_REQUEST['class-date-year'];
			$new_shamsi_mounth = ( $_REQUEST['class-date-month'] < 10 ? '0' . $_REQUEST['class-date-month'] : $_REQUEST['class-date-month'] );
			$new_shamsi_day    = ( $_REQUEST['class-date-day'] < 10 ? '0' . $_REQUEST['class-date-day'] : $_REQUEST['class-date-day'] );
			$new_shamsi_hour   = $_REQUEST['class-date-time-hour'] . ":00:00";
			$new_date          = gregdate( "Y-m-d", $new_shamsi_year . '-' . $new_shamsi_mounth . '-' . $new_shamsi_day );

			// Check Before Get this Time
			$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woo_booking WHERE `class_date` = '$new_date' AND `class_time` = '$new_shamsi_hour'" );
			if ( $count > 0 ) {
				$ids = $wpdb->get_var( "SELECT `ID` FROM {$wpdb->prefix}woo_booking WHERE `class_date` = '$new_date' AND `class_time` = '$new_shamsi_hour'" );
				if ( $ids != $_REQUEST['update_link_class'] ) {
					wp_die( "خطا : این ساعت کلاس قبلا برای یک کلاس دیگر انتخاب شده است و قابلیت انتخاب برای این کلاس ندارد" );
				}
			}

			// Check Time is changed
			if ( $new_date != $class_date || $class_time != $new_shamsi_hour ) {
				$class_date = $new_date;
				$class_time = $new_shamsi_hour;

				// Send Mail To User
				$subscriber_data = get_userdata( $current['user_id'] );
				add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
				$to      = $subscriber_data->user_email;
				$subject = 'تغییر ساعت کلاس شما توسط استاد';
				$body    = '<html dir="rtl">';
				$body    .= '<body style = "text-align:right; direction:rtl;">';
				$body    .= '<p style="font-family: Tahoma; font-size: 12px;">';
				$body    .= $subscriber_data->first_name . ' ' . $subscriber_data->last_name;
				$body    .= ' عزیز, ';
				$body    .= 'استاد ساعت کلاس شما را از ';
				$body    .= parsidate( "Y-m-d H:i", strtotime( $current['class_date'] . ' ' . $current['class_time'] ), "eng" );
				$body    .= ' به ';
				$body    .= parsidate( "Y-m-d H:i", strtotime( $new_date . ' ' . $new_shamsi_hour ), "eng" );
				$body    .= ' تغییر داده است. ';
				$body    .= '</p>';
				$body    .= '</body>';
				$body    .= '</html>';
				wp_mail( $to, $subject, $body );
				remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
			}

			// Update Data
			$wpdb->update(
				$wpdb->prefix . 'woo_booking',
				array(
					'link'       => trim( $_REQUEST['link'] ),
					'class_date' => $class_date,
					'class_time' => $class_time,
				),
				array( 'ID' => $_REQUEST['update_link_class'] )
			);

			wp_redirect( esc_url_raw( add_query_arg( array( 'page' => 'woo_class_list', 'alert' => 'update' ), admin_url( "admin.php" ) ) ) );
			exit;
		}


		// New Class
		if ( isset( $_REQUEST['new_class_by_admin'] ) ) {

			// Convert New Date
			$new_shamsi_year   = $_REQUEST['class-date-year'];
			$new_shamsi_mounth = ( $_REQUEST['class-date-month'] < 10 ? '0' . $_REQUEST['class-date-month'] : $_REQUEST['class-date-month'] );
			$new_shamsi_day    = ( $_REQUEST['class-date-day'] < 10 ? '0' . $_REQUEST['class-date-day'] : $_REQUEST['class-date-day'] );
			$new_shamsi_hour   = $_REQUEST['class-date-time-hour'] . ":00:00";
			$new_date          = gregdate( "Y-m-d", $new_shamsi_year . '-' . $new_shamsi_mounth . '-' . $new_shamsi_day );

			// Check Before Create this class
			$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woo_booking WHERE `class_date` = '$new_date' AND `class_time` = '$new_shamsi_hour'" );
			if ( $count > 0 ) {
				wp_die( "خطا : این ساعت کلاس قبلا برای یک کلاس دیگر انتخاب شده است و قابلیت ایجاد را ندارد" );
			}

			$wpdb->insert(
				$wpdb->prefix . 'woo_booking',
				array(
					'user_id'             => $_REQUEST['user'],
					'class_date'          => $new_date,
					'class_time'          => $new_shamsi_hour . ":00:00",
					'status'              => 1,
					'payment'             => $_REQUEST['payment'],
					'type_class'          => 1,
					'number_change_class' => 0,
				)
			);

			wp_redirect( esc_url_raw( add_query_arg( array( 'page' => 'woo_class_list', 'alert' => 'new_class' ), admin_url( "admin.php" ) ) ) );
			exit;
		}

	}
}

/**
 * Class notification
 * @package WP_Extensions\Admin\page
 */
class Woo_class_list_init {
	/**
	 * WP_List_Table object
	 */
	public static $wp_list_table;
	public static $page_slug = 'woo_class_list';

	public static function in_page( $page ) {
		global $pagenow;
		return ( is_admin() and $pagenow == "admin.php" and isset( $_REQUEST['page'] ) and $_REQUEST['page'] == $page );
	}

	public static function wp_admin_notice( $text, $model = "info", $close_button = true, $id = false, $echo = true, $style_extra = 'padding:12px;' ) {
		$text = '
        <div class="notice notice-' . $model . '' . ( $close_button === true ? " is-dismissible" : "" ) . '"' . ( $id != false ? ' id="' . $id . '"' : '' ) . '>
           <div style="' . $style_extra . '">' . $text . '</div>
        </div>
        ';
		if ( $echo ) {
			echo $text;
		} else {
			return $text;
		}
	}

	public function __construct() {
		if ( self::in_page( self::$page_slug ) ) {
			add_filter( 'set-screen-option', array( $this, 'set_screen' ), 10, 3 );
			add_action( 'admin_init', array( $this, 'set_list_table_redirect' ) );
			add_action( 'admin_head', array( $this, 'wp_list_table_css' ) );
			add_action( 'admin_print_scripts', array( $this, 'disable_all_admin_notices' ) );
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		}
	}

	/**
	 * Screen Option
	 */
	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	/**
	 * Set init redirect in wp_list_table
	 */
	public function set_list_table_redirect() {

		// Redirect For $_POST Form Performance
		$list_field = array( "s", "date", "type", "sender", "status" );
		foreach ( $list_field as $post ) {
			if ( isset( $_POST[ $post ] ) and ! empty( $_POST[ $post ] ) ) {

				// Create Base Url
				$args = array( 'page' => self::$page_slug );

				// Push Arg
				foreach ( $list_field as $parameter ) {
					if ( isset( $_POST[ $parameter ] ) and ! empty( $_POST[ $parameter ] ) ) {
						$args[ $parameter ] = urlencode( $_POST[ $parameter ] );
					}
				}

				// Check SUB SUB
				if ( isset( $_REQUEST['type'] ) ) {
					$args['type'] = urlencode( $_REQUEST['type'] );
				}

				// Redirect
				wp_redirect( add_query_arg( $args, admin_url( "admin.php" ) ) );
				exit;
			}
		}

		// Remove Admin Notice From Pagination
		if ( isset( $_GET['alert'] ) and isset( $_GET['paged'] ) ) {
			wp_redirect( remove_query_arg( array( 'alert' ) ) );
			exit;
		}
	}

	/**
	 * Wp List Table Column Css
	 */
	public function wp_list_table_css() {
		?>
        <style>
            th#title {
                width: 290px;
            }
        </style>
		<?php
	}

	/**
	 * Admin Notice
	 */
	public static function admin_notice() {
		if ( isset( $_GET['alert'] ) ) {
			switch ( $_GET['alert'] ) {
				case "mark_as_success":
					self::wp_admin_notice( __( "کلاس به حالت برگزار شده تغییر پیدا کرد", "wp-extensions" ), "success" );
					break;
				case "update":
					self::wp_admin_notice( __( "اطلاعات بروز شد", "wp-extensions" ), "success" );
					break;
				case "new_class":
					self::wp_admin_notice( __( "کلاس ایجاد شد", "wp-extensions" ), "success" );
					break;
			}
		}
	}

	/**
	 * Screen options
	 */
	public static function screen_option() {

		//Set Screen Option Per Page
		$option = 'per_page';
		$args   = array(
			'label'   => __( "Number of items per page", 'wp-extensions' ),
			'default' => 15,
			'option'  => 'woo_booking_list_table_per_page'
		);
		add_screen_option( $option, $args );

		//Load WP_List_Table
		self::$wp_list_table = new Wp_List_Table_Woo_class_list();
		self::$wp_list_table->prepare_items();
	}

	/**
	 * Disable All Admin Notice in list table page
	 */
	public function disable_all_admin_notices() {
		if ( ( ! isset( $_GET['alert'] ) ) ) {
			global $wp_filter;
			if ( isset( $wp_filter['user_admin_notices'] ) ) {
				unset( $wp_filter['user_admin_notices'] );
			}
			if ( isset( $wp_filter['admin_notices'] ) ) {
				unset( $wp_filter['admin_notices'] );
			}
			if ( isset( $wp_filter['all_admin_notices'] ) ) {
				unset( $wp_filter['all_admin_notices'] );
			}
		}
	}

	/**
	 * Show Admin Page
	 */
	public static function init() {
		global $wpdb;
		?>
        <div class="wrap">
        <h1 class="wp-heading-inline"><?php _e( "لیست کلاس ها", "wp-extensions" ); ?></h1>
        <a href="<?php echo home_url(); ?>/wp-admin/admin.php?page=woo_class_list&create_new=yes" class="page-title-action">ایجاد کلاس</a>
        <hr class="wp-header-end">


		<?php
		if ( isset( $_GET['create_new'] ) ) {
		?>
        <div class="new_class_form">

            <form action="<?php echo home_url(); ?>/wp-admin/admin.php?page=woo_class_list" method="post">
                <input name="new_class_by_admin" type="hidden" value="yes">
                <table class="form-table">
                    <tbody>
                    <tr class="form-field form-required">
                        <th scope="row">
                            <label for="default_access_cap">کاربر</label>
                        </th>
                        <td>
                            <select name="user">
								<?php
								$blogusers = get_users();
								foreach ( $blogusers as $user ) {
									?>
                                    <option value="<?php echo $user->ID; ?>"><?php echo $user->first_name . ' ' . $user->last_name; ?></option>
									<?php
								}
								?>
                            </select>
                        </td>
                    </tr>
                    <tr class="form-field form-required">
                        <th scope="row">
                            <label for="default_access_cap">مبلغ</label>
                        </th>
                        <td>
                            <input type="text" name="payment" value="0" style="width: 120px; text-align: left; direction: ltr;">
                        </td>
                    </tr>
					<?php

					echo '

                            <tr class="form-field form-required">
                                <th scope="row">
                                    <label for="default_access_cap">تاریخ تشکیل کلاس</label>
                                </th>
                                <td>
                                    <select name="class-date-day">
                                        ';

					$select_day = parsidate( "j", current_time( 'timestamp' ), "eng" );
					for ( $i = 1; $i <= 31; $i ++ ) {

						echo ' <option value="' . $i . '" ' . ( $i == $select_day ? "selected" : "" ) . '>' . $i . '</option>';

					}

					echo '
                                    </select>
                                    ';

					$select_mon = parsidate( "n", current_time( 'timestamp' ), "eng" );

					echo '
                                    <select name="class-date-month">
                                        <option value="1" ' . ( $select_mon == 1 ? "selected" : "" ) . '>فروردین</option>
                                        <option value="2" ' . ( $select_mon == 2 ? "selected" : "" ) . '>اردیبهشت</option>
                                        <option value="3" ' . ( $select_mon == 3 ? "selected" : "" ) . '>خرداد</option>
                                        <option value="4" ' . ( $select_mon == 4 ? "selected" : "" ) . '>تیر</option>
                                        <option value="5" ' . ( $select_mon == 5 ? "selected" : "" ) . '>مرداد</option>
                                        <option value="6" ' . ( $select_mon == 6 ? "selected" : "" ) . '>شهریور</option>
                                        <option value="7" ' . ( $select_mon == 7 ? "selected" : "" ) . '>مهر</option>
                                        <option value="8" ' . ( $select_mon == 8 ? "selected" : "" ) . '>آبان</option>
                                        <option value="9" ' . ( $select_mon == 9 ? "selected" : "" ) . '>آذر</option>
                                        <option value="10" ' . ( $select_mon == 10 ? "selected" : "" ) . '>دی</option>
                                        <option value="11" ' . ( $select_mon == 11 ? "selected" : "" ) . '>بهمن</option>
                                        <option value="12" ' . ( $select_mon == 12 ? "selected" : "" ) . '>اسفند</option>
                                    </select>
                                    ';

					$select_year = parsidate( "Y", current_time( 'timestamp' ), "eng" );
					$year_now    = parsidate( "Y", current_time( 'timestamp' ), "eng" );

					echo '
                                    <select name="class-date-year">
                                        <option value="' . $year_now . '" ' . ( $select_year == $year_now ? "selected" : "" ) . '>' . $year_now . '</option>
                                        <option value="' . ( $year_now + 1 ) . '" ' . ( $select_year == ( $year_now ) + 1 ? "selected" : "" ) . '>' . ( $year_now + 1 ) . '</option>
                                    </select>
                                </td>
                            </tr>


                            <tr class="form-field form-required">
                                <th scope="row">
                                    <label for="default_access_cap">ساعت تشکیل کلاس</label>
                                </th>
                                <td>

                                    <select name="class-date-time-hour">';
					for ( $i = 6; $i <= 23; $i ++ ) {
						$real_timer = explode( ":", current_time( 'timestamp' ) );
						$real_time  = $real_timer[0];
						echo '<option value="' . ( $i < 10 ? '0' . $i : $i ) . '" ' . ( $real_time == ( $i < 10 ? '0' . $i : $i ) ? "selected" : "" ) . '>' . $i . '</option>';
					}
					echo '
                                    </select>
                                </td>
                            </tr>


                            <tr class="form-field">
                                <th scope="row" style="padding-bottom: 10px;">
                                    <input type="submit" class="button button-primary" value="ایجاد کلاس">
                                </th>
                            </tr>
                            </tbody>
                        </table>
                    </form>

                    <div class="wp-clearfix"></div>


                </div>';
					}


					self::$wp_list_table->views(); ?>
                    <!--            <form method="post" action="--><?php //echo remove_query_arg( array( 'alert' ) ); ?><!--">-->
					<?php
					//self::$wp_list_table->search_box( __( "Search", 'wp-extensions' ), 'search' );
					self::$wp_list_table->display();
					?>
                    <!--            </form>-->
        </div>
        <br class="clear">






		<?php
		// Number Class
		echo '<div style="padding: 5px;">کل کلاس ها : ' . number_format( $wpdb->get_var( "SELECT COUNT(*) FROM `{$wpdb->prefix}woo_booking`" ) ) . '</div>';
		echo '<div style="padding: 5px;">کلاس های برگزار شده : ' . number_format( $wpdb->get_var( "SELECT COUNT(*) FROM `{$wpdb->prefix}woo_booking` WHERE status = 2" ) ) . '</div>';
		echo '<div style="padding: 5px;">در انتظار برگزاری : ' . number_format( $wpdb->get_var( "SELECT COUNT(*) FROM `{$wpdb->prefix}woo_booking` WHERE status = 1" ) ) . '</div>';
		echo '<div style="padding: 5px;">مجموع درآمد : ' . wp_strip_all_tags( wc_price( $wpdb->get_var( "SELECT SUM(payment) FROM `{$wpdb->prefix}woo_booking`" ) ) ) . '</div>';
	}

}

new Woo_class_list_init;