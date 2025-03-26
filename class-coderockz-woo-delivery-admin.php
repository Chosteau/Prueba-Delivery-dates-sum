<?php

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://coderockz.com
 * @since      1.0.0
 *
 * @package    Coderockz_Woo_Delivery
 * @subpackage Coderockz_Woo_Delivery/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Coderockz_Woo_Delivery
 * @subpackage Coderockz_Woo_Delivery/admin
 * @author     CodeRockz <admin@coderockz.com>
 */
class Coderockz_Woo_Delivery_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	public $helper;

	public $hpos;

	public $field_calculate;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		add_action( 'before_woocommerce_init', function() {
			if ( class_exists( \Automattic\WooCommerce\Utilities\OrderUtil::class ) ) {
				if ( \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
					// HPOS usage is enabled.
					$this->hpos = true;
				} else {
					// Traditional CPT-based orders are in use.
					$this->hpos = false;
				}
			}
		} );

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->helper = new Coderockz_Woo_Delivery_Helper();
		$this->field_calculate = new Coderockz_Woo_Delivery_Field_Calculate();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Coderockz_Woo_Delivery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Coderockz_Woo_Delivery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
		$delivery_date_calendar_theme = (isset($delivery_date_settings['calendar_theme']) && $delivery_date_settings['calendar_theme'] != "") ? $delivery_date_settings['calendar_theme'] : "";

		wp_enqueue_style( 'select2mincss', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'jquery-ui-css', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( "flatpickr_css", CODEROCKZ_WOO_DELIVERY_URL . 'public/css/flatpickr.min.css', array(), $this->version, 'all' );
		if($delivery_date_calendar_theme != "") {
			wp_enqueue_style( "flatpickr_calendar_theme_css", CODEROCKZ_WOO_DELIVERY_URL .'public/css/calendar-themes/' . $delivery_date_calendar_theme.'.css', array(), $this->version, 'all' );
		}
		if($this->helper->detect_plugin_settings_page()) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( "data_table_css", plugin_dir_url( __FILE__ ) . 'css/datatables.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( "date_range_css", plugin_dir_url( __FILE__ ) . 'css/daterangepicker.min.css', $this->version, 'all' );
			wp_enqueue_style( 'selectize_css',  plugin_dir_url(__FILE__) . 'css/selectize.min.css', array(),$this->version);
		}

		if($this->helper->detect_delivery_calendar_page()) {
			wp_enqueue_style( 'selectize_css',  plugin_dir_url(__FILE__) . 'css/selectize.min.css', array(),$this->version);
			wp_enqueue_style( "calendar_css", plugin_dir_url( __FILE__ ) . 'css/calendar.min.css', $this->version, 'all' );
		}

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/coderockz-woo-delivery-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Coderockz_Woo_Delivery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Coderockz_Woo_Delivery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
		$delivery_date_calendar_locale = (isset($delivery_date_settings['calendar_locale']) && !empty($delivery_date_settings['calendar_locale'])) ? $delivery_date_settings['calendar_locale'] : "default";
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
		$pickup_date_calendar_locale = (isset($pickup_date_settings['calendar_locale']) && !empty($pickup_date_settings['calendar_locale'])) ? $pickup_date_settings['calendar_locale'] : "default";
		wp_enqueue_script( 'jquery-effects-slide' );
		wp_enqueue_script( 'jquery-ui-accordion' );
		
		wp_enqueue_script( 'jquery-emojiRatings-js', plugin_dir_url( __FILE__ ) . 'js/jquery.emojiRatings.min.js', array( 'jquery' ), $this->version, true );
		
		if($this->helper->detect_plugin_settings_page()) {
			wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
			wp_enqueue_script( "animejs", plugin_dir_url( __FILE__ ) . 'js/anime.min.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( "data_table_js", plugin_dir_url( __FILE__ ) . 'js/datatables.min.js', [], $this->version, true );
			wp_enqueue_script( "moment_js", plugin_dir_url( __FILE__ ) . 'js/moment.min.js', [], $this->version, true );
			wp_enqueue_script( "date_range_js", plugin_dir_url( __FILE__ ) . 'js/jquery.daterangepicker.min.js', ['moment_js'], $this->version, true );
			wp_enqueue_script("selectize_js", plugin_dir_url(__FILE__) . 'js/selectize.min.js', array( 'jquery' ), $this->version, true);
			wp_enqueue_script( $this->plugin_name."_js_plugin", plugin_dir_url( __FILE__ ) . 'js/coderockz-woo-delivery-admin-js-plugin.js', array( 'jquery', 'data_table_js', 'date_range_js','selectize_js' ), $this->version, true );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coderockz-woo-delivery-admin.js', array( 'jquery', 'animejs', 'selectWoo', 'wp-color-picker' ), $this->version, true );
		}

		if($this->helper->detect_delivery_calendar_page()) {
			wp_enqueue_script("selectize_js", plugin_dir_url(__FILE__) . 'js/selectize.min.js', array( 'jquery' ), $this->version, true);
			wp_enqueue_script( "delivery_calendar_locale_js", plugin_dir_url( __FILE__ ) . 'js/calendar-locales-all.min.js', ["delivery_calendar_js"], $this->version, true );
			wp_enqueue_script( "delivery_calendar_js", plugin_dir_url( __FILE__ ) . 'js/calendar.min.js', ['jquery'], $this->version, true );
			wp_enqueue_script( "delivery_calendar_script_js", plugin_dir_url( __FILE__ ) . 'js/coderockz-woo-delivery-delivery-calendar.js', array( 'jquery', 'delivery_calendar_js','delivery_calendar_locale_js', 'selectWoo', 'selectize_js' ), $this->version, true );
		}

		global $pagenow;
		if(((get_post_type() == 'shop_order' || (function_exists( 'wc_get_page_screen_id' ) && wc_get_page_screen_id( 'shop-order' ) === 'woocommerce_page_wc-orders')) && isset($_GET['action'])  && $_GET['action'] === 'edit' ) || ((get_post_type() == 'shop_order' || (function_exists( 'wc_get_page_screen_id' ) && wc_get_page_screen_id( 'shop-order' ) === 'woocommerce_page_wc-orders')) && ($pagenow === 'post-new.php' || (isset($_GET['action'])  && $_GET['action'] === 'new')))) {
			wp_enqueue_script( "flatpickr_js", CODEROCKZ_WOO_DELIVERY_URL . 'public/js/flatpickr.min.js', [], $this->version, true );

			$enable_delivery_date = (isset($delivery_date_settings['enable_delivery_date']) && !empty($delivery_date_settings['enable_delivery_date'])) ? $delivery_date_settings['enable_delivery_date'] : false;

			if($enable_delivery_date) {
				wp_enqueue_script( "flatpickr_locale_js", CODEROCKZ_WOO_DELIVERY_URL . 'public/js/calendar_locale/'.$delivery_date_calendar_locale.'.js', ["flatpickr_js"], $this->version, true );
			}
			$enable_pickup_date = (isset($pickup_date_settings['enable_pickup_date']) && !empty($pickup_date_settings['enable_pickup_date'])) ? $pickup_date_settings['enable_pickup_date'] : false;
			if($enable_pickup_date) {

				wp_enqueue_script( "flatpickr_pickup_locale_js", CODEROCKZ_WOO_DELIVERY_URL . 'public/js/calendar_locale/'.$pickup_date_calendar_locale.'.js', ["flatpickr_js"], $this->version, true );
			}

			if(wp_script_is('flatpickr_locale_js') && !wp_script_is('flatpickr_pickup_locale_js')) {
				wp_enqueue_script( $this->plugin_name."_js_plugin", plugin_dir_url( __FILE__ ) . 'js/coderockz-woo-delivery-admin-js-single-order-js.js', array( 'jquery', 'flatpickr_js', 'flatpickr_locale_js' ), $this->version, true );
			} elseif(!wp_script_is('flatpickr_locale_js') && wp_script_is('flatpickr_pickup_locale_js')) {
				wp_enqueue_script( $this->plugin_name."_js_plugin", plugin_dir_url( __FILE__ ) . 'js/coderockz-woo-delivery-admin-js-single-order-js.js', array( 'jquery', 'flatpickr_js','flatpickr_pickup_locale_js' ), $this->version, true );
			} elseif(wp_script_is('flatpickr_locale_js') && wp_script_is('flatpickr_pickup_locale_js')) {
				wp_enqueue_script( $this->plugin_name."_js_plugin", plugin_dir_url( __FILE__ ) . 'js/coderockz-woo-delivery-admin-js-single-order-js.js', array( 'jquery', 'flatpickr_js', 'flatpickr_locale_js','flatpickr_pickup_locale_js'), $this->version, true );
			} else {
				wp_enqueue_script( $this->plugin_name."_js_plugin", plugin_dir_url( __FILE__ ) . 'js/coderockz-woo-delivery-admin-js-single-order-js.js', array( 'jquery', 'flatpickr_js'), $this->version, true );
			}
		}

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coderockz-woo-delivery-admin-script.js', array( 'jquery', 'jquery-emojiRatings-js', 'selectWoo' ), $this->version, true );
		
		$coderockz_woo_delivery_nonce = wp_create_nonce('coderockz_woo_delivery_nonce');
	    wp_localize_script($this->plugin_name, 'coderockz_woo_delivery_ajax_obj', array(
            'coderockz_woo_delivery_ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => $coderockz_woo_delivery_nonce,
        ));

	}

	public function coderockz_woo_delivery_menus_sections() {

        $access_shop_manager = (isset(get_option('coderockz_woo_delivery_other_settings')['access_shop_manager']) && !empty(get_option('coderockz_woo_delivery_other_settings')['access_shop_manager'])) ? get_option('coderockz_woo_delivery_other_settings')['access_shop_manager'] : false;

        if($access_shop_manager) {
        	if(current_user_can( 'manage_woocommerce' )) {
	        	add_menu_page(
					__('Woo Delivery', 'coderockz-woo-delivery'),
		            __('Woo Delivery', 'coderockz-woo-delivery'),
					'view_woocommerce_reports',
					'coderockz-woo-delivery-settings',
					array($this, "coderockz_woo_delivery_main_layout"),
					"dashicons-cart",
					null
				);
	        }

        } else {
        	add_menu_page(
				__('Woo Delivery', 'coderockz-woo-delivery'),
	            __('Woo Delivery', 'coderockz-woo-delivery'),
				'manage_options',
				'coderockz-woo-delivery-settings',
				array($this, "coderockz_woo_delivery_main_layout"),
				"dashicons-cart",
				null
			);
        }     
    }

    public function coderockz_woo_delivery_woocommerce_submenu() {

    	$calendar_access_shop_manager = (isset(get_option('coderockz_woo_delivery_other_settings')['calendar_access_shop_manager']) && !empty(get_option('coderockz_woo_delivery_other_settings')['calendar_access_shop_manager'])) ? get_option('coderockz_woo_delivery_other_settings')['calendar_access_shop_manager'] : false;

        if($calendar_access_shop_manager) {
        	if(current_user_can( 'manage_woocommerce' )) {
	        	add_submenu_page(
					'woocommerce',
					__( 'Delivery Calendar', 'coderockz-woo-delivery' ),
					__( 'Delivery Calendar', 'coderockz-woo-delivery' ),
					'view_woocommerce_reports',
					'coderockz-woo-delivery-delivery-calendar',
					array( $this, 'coderockz_woo_delivery_delivery_calendar' ),
					2
				);
	        }

        } else {
        	add_submenu_page(
				'woocommerce',
				__( 'Delivery Calendar', 'coderockz-woo-delivery' ),
				__( 'Delivery Calendar', 'coderockz-woo-delivery' ),
				'manage_options',
				'coderockz-woo-delivery-delivery-calendar',
				array( $this, 'coderockz_woo_delivery_delivery_calendar' ),
				2
			);
        } 
    }

	public function coderockz_woo_delivery_settings_link( $links ) {
    	if ( array_key_exists( 'deactivate', $links ) ) {
			$links['deactivate'] = str_replace( '<a', '<a class="coderockz-woo-delivery-deactivate-link"', $links['deactivate'] );
		}

        $links[] = '<a href="admin.php?page=coderockz-woo-delivery-settings">Settings</a>';

        return $links;
    }

    //Without this function of filter "woocommerce_order_data_store_cpt_get_orders_query" query with post_meta "delivery_date" is not possible
	public function coderockz_woo_delivery_handle_custom_query_var( $query, $query_vars ) {
		if ( ! empty( $query_vars['delivery_date'] ) ) {
			$query['meta_query'][] = array(
				'key' => 'delivery_date',
				'value' => esc_attr( $query_vars['delivery_date'] ),
			);
		}

		if ( ! empty( $query_vars['pickup_date'] ) ) {
			$query['meta_query'][] = array(
				'key' => 'pickup_date',
				'value' => esc_attr( $query_vars['pickup_date'] ),
			);
		}

		if ( ! empty( $query_vars['delivery_type'] ) ) {
			$query['meta_query'][] = array(
				'key' => 'delivery_type',
				'value' => esc_attr( $query_vars['delivery_type'] ),
			);
		}

		if ( ! empty( $query_vars['delivery_time'] ) ) {
			$query['meta_query'][] = array(
				'key' => 'delivery_time',
				'value' => esc_attr( $query_vars['delivery_time'] ),
			);
		}

		if ( ! empty( $query_vars['pickup_time'] ) ) {
			$query['meta_query'][] = array(
				'key' => 'pickup_time',
				'value' => esc_attr( $query_vars['pickup_time'] ),
			);
		}

		if ( ! empty( $query_vars['pickup_location'] ) ) {
			$query['meta_query'][] = array(
				'key' => 'pickup_location',
				'value' => esc_attr( $query_vars['pickup_location'] ),
			);
		}

		return $query;
	}

	public function coderockz_woo_delivery_spinner_clear_btn( ) {
		check_ajax_referer('coderockz_woo_delivery_nonce');
		$other_settings = get_option('coderockz_woo_delivery_other_settings');
		unset($other_settings['spinner-animation-id']);
		update_option('coderockz_woo_delivery_other_settings', $other_settings);
		wp_send_json_success();
	}

	public function register_bulk_delivery_completed_actions( $bulk_actions ) {

		$other_settings = get_option('coderockz_woo_delivery_other_settings');
		$remove_delivery_status_column = (isset($other_settings['remove_delivery_status_column']) && !empty($other_settings['remove_delivery_status_column'])) ? $other_settings['remove_delivery_status_column'] : false;
		if(!$remove_delivery_status_column) {
			$bulk_actions['coderockz_bulk_delivery_completed'] = __( 'Make Delivery/Pickup Completed', 'coderockz-woo-delivery' );
		}
		
		$google_calendar_settings = get_option('coderockz_woo_delivery_google_calendar_settings');

		$calendar_sync_customer_client_id = isset($google_calendar_settings['google_calendar_client_id']) && !empty($google_calendar_settings['google_calendar_client_id']) ? $google_calendar_settings['google_calendar_client_id'] : "";
		
		$calendar_sync_customer_client_secret = isset($google_calendar_settings['google_calendar_client_secret']) && !empty($google_calendar_settings['google_calendar_client_secret']) ? $google_calendar_settings['google_calendar_client_secret'] : "";
		
		if(get_option('coderockz_woo_delivery_google_calendar_access_token') && $google_calendar_settings['google_calendar_client_id'] != "" && $google_calendar_settings['google_calendar_client_secret'] != "" ) {

			$bulk_actions['coderockz_bulk_google_calendar_sync'] = __( 'Sync to Google Calendar', 'coderockz-woo-delivery' );

		}

		return $bulk_actions;
	}

    public function coderockz_woo_delivery_get_order_details() {
    	check_ajax_referer('coderockz_woo_delivery_nonce');
    	$order_id = sanitize_text_field($_POST[ 'orderId' ]);
    	$order = wc_get_order($order_id);
    	$order_extra_details = "";
	    $order_extra_details .= '<div class="coderockz-woo-delivery-address-section">
		    <div class="coderockz-woo-delivery-billing-address">
		        <p class="coderockz-woo-delivery-address-heading">'.__( 'Billing Address', 'coderockz-woo-delivery' ).'</p>
		        <p>'.$order->get_formatted_billing_address().'</p>';
		$order_extra_details .= '<p><span style="font-weight:700">'.__( 'Phone:', 'coderockz-woo-delivery' ).'</span> '.$order->get_billing_phone().'</p>';
		$order_extra_details .= '<p><span style="font-weight:700">'.__( 'Email address:', 'coderockz-woo-delivery' ).'</span> '.$order->get_billing_email().'</p>';
		$order_extra_details .= '<p><span style="font-weight:700">'.__( 'Payment Method:', 'coderockz-woo-delivery' ).'</span> '.$order->get_payment_method_title().'</p>';
		if($order->get_customer_note() != "") {
			$order_extra_details .= '<p><span style="font-weight:700">'.__( 'Customer provided note:', 'coderockz-woo-delivery' ).'</span> '.$order->get_customer_note().'</p>';
		}
	    $order_extra_details .= '</div>';
	    if($order->get_formatted_shipping_address()){
		    $order_extra_details .= '<div class="coderockz-woo-delivery-shipping-address">
		        <p class="coderockz-woo-delivery-address-heading">'.__( 'Shipping Address', 'coderockz-woo-delivery' ).'</p>
		        <p>'.$order->get_formatted_shipping_address().'</p>';

		    if($order->get_shipping_phone() != "") {
		    	$order_extra_details .= '<p><span style="font-weight:700">'.__( 'Phone:', 'coderockz-woo-delivery' ).'</span> '.$order->get_shipping_phone().'</p>';
		    }
		    $order_extra_details .= '</div>';
		}
	    $order_extra_details .= '<div class="coderockz-woo-delivery-order-details">
	        <p class="coderockz-woo-delivery-address-heading">'.__( 'Order Products', 'coderockz-woo-delivery' ).'</p>
	        <table>
	            <thead>
	                <tr>
	                    <th style="width:40px;">'.__( 'S/N', 'coderockz-woo-delivery' ).'</th>
	                    <th style="width:250px;">'.__( 'item', 'coderockz-woo-delivery' ).'</th>
	                    <th>'.__( 'Cost', 'coderockz-woo-delivery' ).'</th>
	                    <th>'.__( 'Qty', 'coderockz-woo-delivery' ).'</th>
	                    <th>'.__( 'Total', 'coderockz-woo-delivery' ).'</th>
	                </tr>
	            </thead>
	            <tbody>';
		$i=1;
		$other_settings = get_option('coderockz_woo_delivery_other_settings');
		$hide_metadata_reports_calendar = (isset($other_settings['hide_metadata_reports_calendar']) && !empty($other_settings['hide_metadata_reports_calendar'])) ? $other_settings['hide_metadata_reports_calendar'] : false;
		foreach ($order->get_items() as $item) {
		if($item->get_variation_id() == 0) {
		   	$product_name = $item->get_name();
			$item_meta_data = $item->get_formatted_meta_data();
			if(!empty($item_meta_data) && !$hide_metadata_reports_calendar) {
				foreach ( $item_meta_data as $meta_id => $meta ) {
					$product_name .= ', '.wp_kses_post( strip_tags($meta->value) );
				}
			}
	    } else {
			$variation = new WC_Product_Variation($item->get_variation_id());
			$item_meta_data = $item->get_formatted_meta_data();
			$product_name = $variation->get_title();
			if(array_filter($variation->get_variation_attributes())) {
				$product_name .= " - ".strip_tags(implode(", ", array_filter($variation->get_variation_attributes(), 'strlen')));	
			}
			if(!empty($item_meta_data) && !$hide_metadata_reports_calendar) {
				foreach ( $item_meta_data as $meta_id => $meta ) {
					if (!array_key_exists("attribute_".$meta->key,$variation->get_variation_attributes()) || (array_key_exists("attribute_".$meta->key,$variation->get_variation_attributes()) && $variation->get_variation_attributes()["attribute_".$meta->key] == "") )
						$product_name .= ', '.wp_kses_post( strip_tags($meta->display_value) );

				}
			}	
	    }
		$order_extra_details .= '<tr>';
		$order_extra_details .= '<td>'.$i.'</td>';
		$order_extra_details .= '<td>'.$this->helper->get_product_image($item->get_product_id()).$product_name.'</td>';
		$order_extra_details .= '<td>'.$this->helper->format_price($order->get_item_total( $item ),$order->get_id()).'</td>';
		$order_extra_details .= '<td>'.$item->get_quantity().'</td>';
		$order_extra_details .= '<td>'.$this->helper->format_price(number_format($item->get_total(),2),$order->get_id()).'</td>';
		$order_extra_details .= '</tr>';
		$i = $i+1;
		}
		$order_extra_details .= '</tbody>
		            
		        </table>
		    </div>
		</div>';
    	wp_send_json_success($order_extra_details);
    }
    public function coderockz_woo_delivery_submit_report_filter_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
    	
    	$filtered_date = sanitize_text_field($_POST[ 'filteredDate' ]);
    	$filtered_delivery_type = sanitize_text_field($_POST[ 'filteredDeliveryType' ]);
    	if(!empty($_POST[ 'filteredOrderStatus' ])) {
    		$filtered_order_status = $this->helper->coderockz_woo_delivery_array_sanitize($_POST[ 'filteredOrderStatus' ]);
    	} else {
    		$order_status_keys = array_keys(wc_get_order_statuses());
			$order_status = ['partially-paid'];
			foreach($order_status_keys as $order_status_key) {
				$order_status[] = substr($order_status_key,3);
			}
    		$filtered_order_status = array_diff($order_status,['cancelled','failed','refunded']);
    	}

    	if(!empty($_POST[ 'filteredPickupLocation' ])) {
    		$filtered_pickup_location = array_filter($_POST[ 'filteredPickupLocation' ], 'strlen');
    		$filtered_pickup_location = $this->helper->coderockz_woo_delivery_array_sanitize($filtered_pickup_location);
    	}  else {
    		$filtered_pickup_location = [];
    	}
   	
    	$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');			
    	$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');			
		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
		$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
		$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');
		$delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";
		$add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

		if($add_weekday_name) {
			$delivery_date_format = "l ".$delivery_date_format;
		}

		$pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";

		$pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

		if($pickup_add_weekday_name) {
			$pickup_date_format = "l ".$pickup_date_format;
		}

		$time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
		if($time_format == 12) {
			$time_format = "h:i A";
		} elseif ($time_format == 24) {
			$time_format = "H:i";
		}

		$pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
		if($pickup_time_format == 12) {
			$pickup_time_format = "h:i A";
		} elseif ($pickup_time_format == 24) {
			$pickup_time_format = "H:i";
		}

		$delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) :  __( "Delivery Date", 'coderockz-woo-delivery' );
		$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) :  __( "Pickup Date", 'coderockz-woo-delivery' );
		$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) :  __( "Delivery Time", 'coderockz-woo-delivery' );
		$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) :  __( "Pickup Time", 'coderockz-woo-delivery' );
		$pickup_location_field_label = (isset($pickup_location_settings['field_label']) && !empty($pickup_location_settings['field_label'])) ? stripslashes($pickup_location_settings['field_label']) :  __( "Pickup Location", 'coderockz-woo-delivery' );
		$additional_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? $additional_field_settings['field_label'] :  __( "Special Note for Delivery", 'coderockz-woo-delivery' );

		$localization_settings = get_option('coderockz_woo_delivery_localization_settings');
		$delivery_status_not_delivered_text = (isset($localization_settings['delivery_status_not_delivered_text']) && !empty($localization_settings['delivery_status_not_delivered_text'])) ? stripslashes($localization_settings['delivery_status_not_delivered_text']) :  __( "Not Delivered", 'coderockz-woo-delivery' );
		$delivery_status_delivered_text = (isset($localization_settings['delivery_status_delivered_text']) && !empty($localization_settings['delivery_status_delivered_text'])) ? stripslashes($localization_settings['delivery_status_delivered_text']) :  __( "Delivery Completed", 'coderockz-woo-delivery' );
		$pickup_status_not_picked_text = (isset($localization_settings['pickup_status_not_picked_text']) && !empty($localization_settings['pickup_status_not_picked_text'])) ? stripslashes($localization_settings['pickup_status_not_picked_text']) :  __( "Not Picked", 'coderockz-woo-delivery' );
		$pickup_status_picked_text = (isset($localization_settings['pickup_status_picked_text']) && !empty($localization_settings['pickup_status_picked_text'])) ? stripslashes($localization_settings['pickup_status_picked_text']) :  __( "Pickup Completed", 'coderockz-woo-delivery' );

		$other_settings = get_option('coderockz_woo_delivery_other_settings');
		$remove_delivery_status_column = (isset($other_settings['remove_delivery_status_column']) && !empty($other_settings['remove_delivery_status_column'])) ? $other_settings['remove_delivery_status_column'] : false;

		if(strpos($filtered_date, ' - ') !== false) {
			$filtered_dates = explode(' - ', $filtered_date);
			$orders = [];
			$delivery_orders = [];
			$pickup_orders = [];

			$dates = [];
			$period =  $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1]);
			$dates = array_merge($dates, $period);
		    foreach ($dates as $date) {
		    	if($filtered_delivery_type == "delivery"){
		    		if($this->hpos) {
				    	$args = array(
					        'limit' => -1,
							'type' => array( 'shop_order' ),
							'status' => $filtered_order_status,
							'meta_query' => array(
					            array(
					                'key'     => 'delivery_date',
					                'value'   => $date,
					                'compare' => '==',
					            ),
					            array(
					                'key'     => 'delivery_type',
					                'value'   => 'delivery',
					                'compare' => '==',
					            ),
					        ),
					    );
				    } else {
				    	$args = array(
					        'limit' => -1,
					        'delivery_date' => $date,
					        'delivery_type' => "delivery",
					        'status' => $filtered_order_status
					    );
				    }
				    $orders_array = wc_get_orders( $args );
				    foreach ($orders_array as $order) {
				    	$orders[] = $order;
				    }
		    	} elseif($filtered_delivery_type == "pickup") {

		    		if(!empty($filtered_pickup_location)) {
		    			foreach($filtered_pickup_location as $location) {
		    				if($this->hpos) {
								$args = array(
							        'limit' => -1,
									'type' => array( 'shop_order' ),
									'status' => $filtered_order_status,
									'meta_query' => array(
							            array(
							                'key'     => 'pickup_date',
							                'value'   => $date,
							                'compare' => '==',
							            ),
							            array(
							                'key'     => 'pickup_location',
							                'value'   => $location,
							                'compare' => '==',
							            ),
							            array(
							                'key'     => 'delivery_type',
							                'value'   => "pickup",
							                'compare' => '==',
							            ),
							        ),
							    );
							} else {
								$args = array(
							        'limit' => -1,
							        'pickup_date' => $date,
							        'delivery_type' => "pickup",
							        'status' => $filtered_order_status,
							        'pickup_location' => $location
							    );
							}
						    $orders_array = wc_get_orders( $args );
						    foreach ($orders_array as $order) {
						    	$orders[] = $order;
						    }
		    			}
			    		
		    		} else {
		    			if($this->hpos) {
							$args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $filtered_order_status,
								'meta_query' => array(
						            array(
						                'key'     => 'pickup_date',
						                'value'   => $date,
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'delivery_type',
						                'value'   => "pickup",
						                'compare' => '==',
						            ),
						        ),
						    );
						} else {
							$args = array(
						        'limit' => -1,
						        'pickup_date' => $date,
						        'delivery_type' => "pickup",
						        'status' => $filtered_order_status
						    );
						}

					    $orders_array = wc_get_orders( $args );
					    foreach ($orders_array as $order) {
					    	$orders[] = $order;
					    }
		    		}

		    	} else {
		    		if($this->hpos) {
				    	$args = array(
					        'limit' => -1,
							'type' => array( 'shop_order' ),
							'status' => $filtered_order_status,
							'meta_query' => array(
					            array(
					                'key'     => 'delivery_date',
					                'value'   => $date,
					                'compare' => '==',
					            ),
					        ),
					    );
				    } else {
				    	$args = array(
					        'limit' => -1,
					        'delivery_date' => $date,
					        'status' => $filtered_order_status
					    );
				    }

				    $orders_array = wc_get_orders( $args );
				    foreach ($orders_array as $order) {
				    	$delivery_orders[] = $order;
				    }

				    
				    if(!empty($filtered_pickup_location)) {
				    	foreach($filtered_pickup_location as $location) {
						    if($this->hpos) {
								$args = array(
							        'limit' => -1,
									'type' => array( 'shop_order' ),
									'status' => $filtered_order_status,
									'meta_query' => array(
							            array(
							                'key'     => 'pickup_date',
							                'value'   => $date,
							                'compare' => '==',
							            ),
							            array(
							                'key'     => 'pickup_location',
							                'value'   => $location,
							                'compare' => '==',
							            ),
							        ),
							    );
							} else {
								$args = array(
							        'limit' => -1,
							        'pickup_date' => $date,
							        'status' => $filtered_order_status,
							        'pickup_location' => $location
							    );
							}

						    $orders_array = wc_get_orders( $args );
						    foreach ($orders_array as $order) {
						    	$pickup_orders[] = $order;
						    }
						}
		    		} else {
		    			if($this->hpos) {
							$args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $filtered_order_status,
								'meta_query' => array(
						            array(
						                'key'     => 'pickup_date',
						                'value'   => $date,
						                'compare' => '==',
						            ),
						        ),
						    );
						} else {
							$args = array(
						        'limit' => -1,
						        'pickup_date' => $date,
						        'status' => $filtered_order_status
						    );
						}

					    $orders_array = wc_get_orders( $args );
					    foreach ($orders_array as $order) {
					    	$pickup_orders[] = $order;
					    }
		    		}

				    $orders = array_merge($delivery_orders, $pickup_orders);
		    	}		    	
			    
		    }
			
		} else {

		    if($filtered_delivery_type == "delivery"){

			    if($this->hpos) {
			    	$args = array(
				        'limit' => -1,
						'type' => array( 'shop_order' ),
						'status' => $filtered_order_status,
						'meta_query' => array(
				            array(
				                'key'     => 'delivery_date',
				                'value'   => date("Y-m-d", strtotime($filtered_date)),
				                'compare' => '==',
				            ),
				            array(
				                'key'     => 'delivery_type',
				                'value'   => 'delivery',
				                'compare' => '==',
				            ),
				        ),
				    );
			    } else {
			    	$args = array(
				        'limit' => -1,
				        'delivery_date' => date("Y-m-d", strtotime($filtered_date)),
				        'delivery_type' => "delivery",
				        'status' => $filtered_order_status
				    );
			    }
			    $orders = wc_get_orders( $args );
	    	} elseif($filtered_delivery_type == "pickup") {

	    		if(!empty($filtered_pickup_location)) {
	    			foreach($filtered_pickup_location as $location) {

					    if($this->hpos) {
							$args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $filtered_order_status,
								'meta_query' => array(
						            array(
						                'key'     => 'pickup_date',
						                'value'   => date("Y-m-d", strtotime($filtered_date)),
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'pickup_location',
						                'value'   => $location,
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'delivery_type',
						                'value'   => "pickup",
						                'compare' => '==',
						            ),
						        ),
						    );
						} else {
							$args = array(
						        'limit' => -1,
						        'pickup_date' => date("Y-m-d", strtotime($filtered_date)),
						        'delivery_type' => "pickup",
						        'status' => $filtered_order_status,
						        'pickup_location' => $location
						    );
						}

					    $orders_array = wc_get_orders( $args );
					    foreach ($orders_array as $order) {
					    	$orders[] = $order;
					    }
					}
	    		} else {

				    if($this->hpos) {
						$args = array(
					        'limit' => -1,
							'type' => array( 'shop_order' ),
							'status' => $filtered_order_status,
							'meta_query' => array(
					            array(
					                'key'     => 'pickup_date',
					                'value'   => date("Y-m-d", strtotime($filtered_date)),
					                'compare' => '==',
					            ),
					            array(
					                'key'     => 'delivery_type',
					                'value'   => "pickup",
					                'compare' => '==',
					            ),
					        ),
					    );
					} else {
						$args = array(
					        'limit' => -1,
					        'pickup_date' => date("Y-m-d", strtotime($filtered_date)),
					        'delivery_type' => "pickup",
					        'status' => $filtered_order_status
					    );
					}

				    $orders = wc_get_orders( $args );
	    		}

	    	} else {
	    		if($this->hpos) {
			    	$args = array(
				        'limit' => -1,
						'type' => array( 'shop_order' ),
						'status' => $filtered_order_status,
						'meta_query' => array(
				            array(
				                'key'     => 'delivery_date',
				                'value'   => date("Y-m-d", strtotime($filtered_date)),
				                'compare' => '==',
				            ),
				        ),
				    );
			    } else {
			    	$args = array(
				        'limit' => -1,
				        'delivery_date' => date("Y-m-d", strtotime($filtered_date)),
				        'status' => $filtered_order_status
				    );
			    }
			    $delivery_orders = wc_get_orders( $args );

			    if(!empty($filtered_pickup_location)) {
			    	foreach($filtered_pickup_location as $location) {
					    if($this->hpos) {
							$args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $filtered_order_status,
								'meta_query' => array(
						            array(
						                'key'     => 'pickup_date',
						                'value'   => date("Y-m-d", strtotime($filtered_date)),
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'pickup_location',
						                'value'   => $location,
						                'compare' => '==',
						            ),
						        ),
						    );
						} else {
							$args = array(
						        'limit' => -1,
						        'pickup_date' => date("Y-m-d", strtotime($filtered_date)),
						        'status' => $filtered_order_status,
						        'pickup_location' => $location
						    );
						}

					    $orders_array = wc_get_orders( $args );
					    foreach ($orders_array as $order) {
					    	$pickup_orders[] = $order;
					    }
					}
	    		} else {
	    			if($this->hpos) {
						$args = array(
					        'limit' => -1,
							'type' => array( 'shop_order' ),
							'status' => $filtered_order_status,
							'meta_query' => array(
					            array(
					                'key'     => 'pickup_date',
					                'value'   => date("Y-m-d", strtotime($filtered_date)),
					                'compare' => '==',
					            ),
					        ),
					    );
					} else {
						$args = array(
					        'limit' => -1,
					        'pickup_date' => date("Y-m-d", strtotime($filtered_date)),
					        'status' => $filtered_order_status
					    );
					}

				    $pickup_orders = wc_get_orders( $args );
	    		}

			    $orders = array_merge($delivery_orders, $pickup_orders);
	    	}
		    
		}

		$order_details_html_body = '';
		$i=1;
		$unsorted_orders = [];
		$total_sales = 0;
		foreach($orders as $order) {
			if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
		        $order_id = $order->get_id();
		    } else {
		        $order_id = $order->id;
		    }

		    if(metadata_exists('post', $order_id, 'delivery_type') || $order->meta_exists('delivery_type')) {
				if($this->hpos) {
					if($order->get_meta( 'delivery_type', true ) == 'delivery') {
						$delivery_complete_btn_text = __("Delivery","coderockz-woo-delivery");
					} elseif($order->get_meta( 'delivery_type', true ) == 'pickup') {
						$delivery_complete_btn_text = __("Pickup","coderockz-woo-delivery");
					}
				} else {
					if(get_post_meta($order_id, 'delivery_type', true) == 'delivery') {
						$delivery_complete_btn_text = __("Delivery","coderockz-woo-delivery");
					} elseif(get_post_meta($order_id, 'delivery_type', true) == 'pickup') {
						$delivery_complete_btn_text = __("Pickup","coderockz-woo-delivery");
					}
				}
				
			} else {
				$delivery_complete_btn_text = __("Delivery","coderockz-woo-delivery");
			}

		    $delivery_date_timestamp = 0;
	    	$delivery_time_start = 0;
	    	$delivery_time_end = 0;

	    	if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {
	    		if($this->hpos) {
					$delivery_date_timestamp = strtotime($order->get_meta( 'delivery_date', true ));
				} else {
					$delivery_date_timestamp = strtotime(get_post_meta( $order_id, 'delivery_date', true ));
				}
		    	
		    } elseif((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {
		    	if($this->hpos) {
					$delivery_date_timestamp = strtotime($order->get_meta( 'pickup_date', true ));
				} else {
					$delivery_date_timestamp = strtotime(get_post_meta( $order_id, 'pickup_date', true ));
				}

		    }

	    	if((metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id, 'delivery_time', true) !="") || ($order->meta_exists('delivery_time') && $order->get_meta( 'delivery_time', true ) != "")) {
	    		if(get_post_meta($order_id,"delivery_time",true) != "as-soon-as-possible" && $order->get_meta( 'delivery_time', true ) != "as-soon-as-possible") {
	    			
	    			if($this->hpos) {
						$minutes = $order->get_meta( 'delivery_time', true );
					} else {
						$minutes = get_post_meta($order_id,"delivery_time",true);
					}

			    	$slot_key = explode(' - ', $minutes);
					$slot_key_one = explode(':', $slot_key[0]);
					$delivery_time_start = ((int)$slot_key_one[0]*60*60+(int)$slot_key_one[1]*60);

			    	if(!isset($slot_key[1])) {
			    		$delivery_time_end = 0;
			    	} else {
			    		$slot_key_two = explode(':', $slot_key[1]);
			    		$delivery_time_end = ((int)$slot_key_two[0]*60*60+(int)$slot_key_two[1]*60);
			    	}
	    		} else {
	    			$delivery_time_end = 0;
	    		}
		    			    	
		    } elseif((metadata_exists('post', $order_id, 'pickup_time') && get_post_meta($order_id, 'pickup_time', true) !="") || ($order->meta_exists('pickup_time') && $order->get_meta( 'pickup_time', true ) != "")) {
		    	if($this->hpos) {
					$minutes = $order->get_meta( 'pickup_time', true );
				} else {
					$minutes = get_post_meta($order_id,"pickup_time",true);
				}

		    	$slot_key = explode(' - ', $minutes);
				$slot_key_one = explode(':', $slot_key[0]);
				$delivery_time_start = ((int)$slot_key_one[0]*60*60+(int)$slot_key_one[1]*60);
		    	if(!isset($slot_key[1])) {
		    		$delivery_time_end = 0;
		    	} else {
		    		$slot_key_two = explode(':', $slot_key[1]);
			    	$delivery_time_end = ((int)$slot_key_two[0]*60*60+(int)$slot_key_two[1]*60);
		    	}
		    }

	    	$delivery_details_in_timestamp = (int)$delivery_date_timestamp+(int)$delivery_time_start+(int)$delivery_time_end;

	    	$unsorted_orders['order_details_html_'.$i] = $delivery_details_in_timestamp;

	    	include_once(ABSPATH.'wp-admin/includes/plugin.php');

	    	if((metadata_exists('post', $order_id, '_wcj_order_number') && get_post_meta($order_id, '_wcj_order_number', true) !="") || ($order->meta_exists('_wcj_order_number') && $order->get_meta( '_wcj_order_number', true ) != "")) {
				if($this->hpos) {
					$order_id_with_custom = '#'.$order->get_meta( '_wcj_order_number', true );
				} else {
					$order_id_with_custom = '#'.get_post_meta($order_id, '_wcj_order_number', true);
				}

			} elseif(is_plugin_active('wt-woocommerce-sequential-order-numbers-pro/wt-advanced-order-number-pro.php') || is_plugin_active('wt-woocommerce-sequential-order-numbers/wt-advanced-order-number.php') || is_plugin_active('custom-order-numbers-for-woocommerce/custom-order-numbers-for-woocommerce.php') || is_plugin_active('yith-woocommerce-sequential-order-number-premium/init.php')) {
				$order_id_with_custom = '#'.$order->get_order_number();
			} else {
				$order_id_with_custom = '#'.$order->get_id();
			}

	    	${'order_details_html_'.$i} = "";
			${'order_details_html_'.$i} .= '<tr data-plugin_url ='.CODEROCKZ_WOO_DELIVERY_URL.' data-order_id='.$order_id.'>';
	        ${'order_details_html_'.$i} .= '<td class="details-control sorting_disabled"></td>';
	        ${'order_details_html_'.$i} .= '<td>'.$order_id_with_custom.'</td>';
	        $order_created_obj= current_datetime($order->get_date_created());
			$order_created = $order_created_obj->format("F j, Y");
			${'order_details_html_'.$i} .= '<td>'.$order_created.'</td>';

			
		    $delivery_details = "";
		    if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta($order_id, 'delivery_date', true) !="") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {
		    	if($this->hpos) {
		    		$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime($order->get_meta( 'delivery_date', true ))),"delivery"),"delivery");
					$delivery_details .= '<p><strong>'.$delivery_date_field_label.':</strong> ' . $delivery_date . '</p>';
				} else {
					$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime(get_post_meta( $post->ID, 'delivery_date', true ))),"delivery"),"delivery");
					$delivery_details .= '<p><strong>'.$delivery_date_field_label.':</strong> ' . $delivery_date . '</p>';
				}
		    }

		    if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta($order_id, 'pickup_date', true) !="") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {

		    	if($this->hpos) {
		    		$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime($order->get_meta( 'pickup_date', true ))),"pickup"),"pickup");
					$delivery_details .= '<p><strong>'.$pickup_date_field_label.':</strong> ' . $pickup_date . '</p>'; 
				} else {
					$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime(get_post_meta( $post->ID, 'pickup_date', true ))),"pickup"),"pickup");
					$delivery_details .= '<p><strong>'.$pickup_date_field_label.':</strong> ' . $pickup_date . '</p>'; 
				}

		    }

		    if((metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id, 'delivery_time', true) !="") || ($order->meta_exists('delivery_time') && $order->get_meta( 'delivery_time', true ) != "")) {

		    	if(get_post_meta($order_id,"delivery_time",true) != "as-soon-as-possible" && $order->get_meta( 'delivery_time', true ) != "as-soon-as-possible") {
			    	if($this->hpos) {
						$minutes = $order->get_meta( 'delivery_time', true );
					} else {
						$minutes = get_post_meta($order_id,"delivery_time",true);
					}
			    	$minutes = explode(' - ', $minutes);

		    		if(!isset($minutes[1])) {
		    			$delivery_details .= '<p><strong>'.$delivery_time_field_label.':</strong> ' . date($time_format, strtotime($minutes[0])) . '</p>';
		    		} else {

		    			$delivery_details .= '<p><strong>'.$delivery_time_field_label.':</strong> ' . date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1])) . '</p>';  			
		    		}
	    		} else {
	    			$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : __("As Soon As Possible","coderockz-woo-delivery");
	    			$delivery_details .= '<p><strong>'.$delivery_time_field_label.':</strong> ' . $as_soon_as_possible_text . '</p>';
	    		}
		    	
		    }

		    if((metadata_exists('post', $order_id, 'pickup_time') && get_post_meta($order_id, 'pickup_time', true) !="") || ($order->meta_exists('pickup_time') && $order->get_meta( 'pickup_time', true ) != "")) {
		    	if($this->hpos) {
					$pickup_minutes = $order->get_meta( 'pickup_time', true );
				} else {
					$pickup_minutes = get_post_meta($order_id,"pickup_time",true);
				}
		    	
		    	$pickup_minutes = explode(' - ', $pickup_minutes);

	    		if(!isset($pickup_minutes[1])) {
	    			$delivery_details .= '<p><strong>'.$pickup_time_field_label.':</strong> ' . date($pickup_time_format, strtotime($pickup_minutes[0])) . '</p>';
	    		} else {

	    			$delivery_details .= '<p><strong>'.$pickup_time_field_label.':</strong> ' . date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1])) . '</p>';  			
	    		}
		    	
		    }

		    if((metadata_exists('post', $order_id, 'pickup_location') && get_post_meta($order_id, 'pickup_location', true) !="") || ($order->meta_exists('pickup_location') && $order->get_meta( 'pickup_location', true ) != "")) {
				if($this->hpos) {
					$pickup_location = $order->get_meta( 'pickup_location', true );
				} else {
					$pickup_location = get_post_meta($order_id, 'pickup_location', true);
				}
				$delivery_details .= '<p><strong>'.$pickup_location_field_label.':</strong> ' . stripslashes(html_entity_decode($pickup_location, ENT_QUOTES)) . '</p>';
			}

			if((metadata_exists('post', $order_id, 'additional_note') && get_post_meta($order_id, 'additional_note', true) !="") || ($order->meta_exists('additional_note') && $order->get_meta( 'additional_note', true ) != "")) {
				if($this->hpos) {
					$additional_note = $order->get_meta( 'additional_note', true );
				} else {
					$additional_note = get_post_meta($order_id, 'additional_note', true);
				}
				$delivery_details .= '<p><strong>'.$additional_field_label.':</strong> ' . stripslashes(html_entity_decode($additional_note, ENT_QUOTES)). '</p>';
			}

			${'order_details_html_'.$i} .= '<td>'.$delivery_details.'</td>';

			if(!$remove_delivery_status_column) {

			if((metadata_exists('post', $order_id, 'delivery_status') && get_post_meta($order_id, 'delivery_status', true) !="" && get_post_meta($order_id, 'delivery_status', true) =="delivered") || ($order->meta_exists('delivery_status') && $order->get_meta( 'delivery_status', true ) != "" && $order->get_meta( 'delivery_status', true ) == "delivered")) {
				if((metadata_exists('post', $order_id, 'delivery_type') && get_post_meta($order_id, 'delivery_type', true) !="" && get_post_meta($order_id, 'delivery_type', true) == "pickup") || ($order->meta_exists('delivery_type') && $order->get_meta( 'delivery_type', true ) != "" && $order->get_meta( 'delivery_type', true ) == "pickup")) {
					${'order_details_html_'.$i} .= '<td class="coderockz_woo_delivery_status"><span class="coderockz_woo_delivery_delivered_text">'.$pickup_status_picked_text.'</span></td>';
				} else {
					${'order_details_html_'.$i} .= '<td class="coderockz_woo_delivery_status"><span class="coderockz_woo_delivery_delivered_text">'.$delivery_status_delivered_text.'</span></td>';
				}
				
			} else {

				if((metadata_exists('post', $order_id, 'delivery_type') && get_post_meta($order_id, 'delivery_type', true) !="" && get_post_meta($order_id, 'delivery_type', true) =="pickup") || ($order->meta_exists('delivery_type') && $order->get_meta( 'delivery_type', true ) != "" && $order->get_meta( 'delivery_type', true ) == "pickup")) {
					${'order_details_html_'.$i} .= '<td class="coderockz_woo_delivery_status"><span class="coderockz_woo_delivery_not_delivered_text">'.$pickup_status_not_picked_text.'</span></td>';
				} else {
					${'order_details_html_'.$i} .= '<td class="coderockz_woo_delivery_status"><span class="coderockz_woo_delivery_not_delivered_text">'.$delivery_status_not_delivered_text.'</span></td>';
				}
			}

			}

			if($order->get_status() == "completed") {
				${'order_details_html_'.$i} .= '<td class="coderockz_woo_delivery_order_status"><span class="coderockz_woo_delivery_delivered_text">'.__("Completed","coderockz-woo-delivery").'</span></td>';
			} else {
				${'order_details_html_'.$i} .= '<td class="coderockz_woo_delivery_order_status"><span>'.$order->get_status().'</span></td>';
			}
			$total_sales = $total_sales + ((float)$order->get_total()-$order->get_total_refunded());
			${'order_details_html_'.$i} .= '<td>'.$order->get_formatted_order_total().'</td>';
			${'order_details_html_'.$i} .= '<td>';
			if(!$remove_delivery_status_column) {
			${'order_details_html_'.$i} .= '<button class="coderockz-woo-delivery-complete-btn button coderockz-woo-delivery-tooltip" style="margin-right:5px!important;padding-left: 4px!important;" tooltip="'.__('Mark','coderockz-woo-delivery').' '.$delivery_complete_btn_text.' '.__('As Completed','coderockz-woo-delivery').'">
											<img src="'.CODEROCKZ_WOO_DELIVERY_URL.'admin/images/delivery_complete.png" alt="" style="width:17px;vertical-align: middle;margin-left: 1px;">
										</button>';
			}
			if($this->hpos) {
				${'order_details_html_'.$i} .= '<a href="'.get_site_url().'/wp-admin/admin.php?page=wc-orders&action=edit&id='.$order_id.'" target="_blank" class="button coderockz-woo-delivery-tooltip" style="margin-right:5px!important;padding-left: 4px!important;" tooltip="'.__("Go to The Order Page","coderockz-woo-delivery").'"><span class="dashicons dashicons-visibility" style="vertical-align:middle!important;"></span></a>';
			} else {
				${'order_details_html_'.$i} .= '<a href="'.get_site_url().'/wp-admin/post.php?post='.$order_id.'&action=edit" target="_blank" class="button coderockz-woo-delivery-tooltip" style="margin-right:5px!important;padding-left: 4px!important;" tooltip="'.__("Go to The Order Page","coderockz-woo-delivery").'"><span class="dashicons dashicons-visibility" style="vertical-align:middle!important;"></span></a>';
			}
			

			${'order_details_html_'.$i} .= '<button class="coderockz-woo-delivery-order-complete-btn button coderockz-woo-delivery-tooltip" style="margin-right:5px!important;padding-left: 4px!important;" tooltip="'.__("Make the Order Completed","coderockz-woo-delivery").'">
				<span class="dashicons dashicons-yes" style="vertical-align:middle!important;"></span>
			</button>
		</td>';
			${'order_details_html_'.$i} .= '</tr>';
			$i=$i+1;
		}

		asort($unsorted_orders);

		foreach ($unsorted_orders as $key => $value) {
			$order_details_html_body .= ${$key};
		}

		$order_details_html = '';
		$order_details_html .= '<table id="coderockz_woo_delivery_report_table" class="display" style="width:100%">';
		if(!$remove_delivery_status_column) {
			$order_details_html .= '<thead>
		            <tr>
		                <th class="details-control sorting_disabled"></th>
		                <th>'.__("Order No","coderockz-woo-delivery").'</th>
		                <th>'.__("Order Date","coderockz-woo-delivery").'</th>
		                <th>'.__("Delivery Details","coderockz-woo-delivery").'</th>
		                <th>'.__("Delivery Status","coderockz-woo-delivery").'</th>
		                <th>'.__("Order Status","coderockz-woo-delivery").'</th>
		                <th>'.__("Total","coderockz-woo-delivery").'</th>
		                <th>'.__("Action","coderockz-woo-delivery").'</th>
		            </tr>
		        </thead>';

		} else {
			$order_details_html .= '<thead>
		            <tr>
		                <th class="details-control sorting_disabled"></th>
		                <th>'.__("Order No","coderockz-woo-delivery").'</th>
		                <th>'.__("Order Date","coderockz-woo-delivery").'</th>
		                <th>'.__("Delivery Details","coderockz-woo-delivery").'</th>
		                <th>'.__("Order Status","coderockz-woo-delivery").'</th>
		                <th>'.__("Total","coderockz-woo-delivery").'</th>
		                <th>'.__("Action","coderockz-woo-delivery").'</th>
		            </tr>
		        </thead>';
		}
		$order_details_html .= '<tbody>';
		$order_details_html .= $order_details_html_body;
		$order_details_html .= '</tbody>';
		if(!$remove_delivery_status_column) {
			$order_details_html .= '<tfoot>
		            <tr>
		                <th class="details-control sorting_disabled"></th>
		                <th>'.__("Order No","coderockz-woo-delivery").'</th>
		                <th>'.__("Order Date","coderockz-woo-delivery").'</th>
		                <th>'.__("Delivery Details","coderockz-woo-delivery").'</th>
		                <th>'.__("Delivery Status","coderockz-woo-delivery").'</th>
		                <th>'.__("Order Status","coderockz-woo-delivery").'</th>
		                <th>'.__("Total","coderockz-woo-delivery").'</th>
		                <th>'.__("Action","coderockz-woo-delivery").'</th>
		            </tr>
		        </tfoot>';

		} else {
			$order_details_html .= '<tfoot>
		            <tr>
		                <th class="details-control sorting_disabled"></th>
		                <th>'.__("Order No","coderockz-woo-delivery").'</th>
		                <th>'.__("Order Date","coderockz-woo-delivery").'</th>
		                <th>'.__("Delivery Details","coderockz-woo-delivery").'</th>
		                <th>'.__("Order Status","coderockz-woo-delivery").'</th>
		                <th>'.__("Total","coderockz-woo-delivery").'</th>
		                <th>'.__("Action","coderockz-woo-delivery").'</th>
		            </tr>
		        </tfoot>';
		}
		
		$order_details_html .= '</table>';
		
		$response = [
			'order_details_html' => $order_details_html,
			'total_sales' => number_format((float)$total_sales, 2),
		];
		wp_send_json_success($response);
    }

    public function coderockz_woo_delivery_submit_report_product_quantity() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
    	
    	$filtered_date = sanitize_text_field($_POST[ 'filteredDate' ]);
    	$filtered_delivery_type = sanitize_text_field($_POST[ 'filteredDeliveryType' ]);

    	if(!empty($_POST[ 'filteredOrderStatus' ])) {
    		$filtered_order_status = $this->helper->coderockz_woo_delivery_array_sanitize($_POST[ 'filteredOrderStatus' ]);
    	} else {
    		$order_status_keys = array_keys(wc_get_order_statuses());
			$order_status = ['partially-paid'];
			foreach($order_status_keys as $order_status_key) {
				$order_status[] = substr($order_status_key,3);
			}
    		$filtered_order_status = array_diff($order_status,['cancelled','failed','refunded']);
    	}

    	if(!empty($_POST[ 'filteredPickupLocation' ])) {
    		$filtered_pickup_location = array_filter($_POST[ 'filteredPickupLocation' ], 'strlen');
    		$filtered_pickup_location = $this->helper->coderockz_woo_delivery_array_sanitize($filtered_pickup_location);
    	}  else {
    		$filtered_pickup_location = [];
    	}

		if(strpos($filtered_date, ' - ') !== false) {
			$filtered_dates = explode(' - ', $filtered_date);
			$orders = [];
			$delivery_orders = [];
			$pickup_orders = [];
		    $dates = [];
			$period =  $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1]);
			$dates = array_merge($dates, $period);
		    foreach ($dates as $date) {
		    	if($filtered_delivery_type == "delivery"){
		    		if($this->hpos) {
						$args = array(
					        'limit' => -1,
							'type' => array( 'shop_order' ),
							'status' => $filtered_order_status,
							'meta_query' => array(
					            array(
					                'key'     => 'delivery_date',
					                'value'   => $date,
					                'compare' => '==',
					            ),
					            array(
					                'key'     => 'delivery_type',
					                'value'   => 'delivery',
					                'compare' => '==',
					            ),
					        ),
					    );
					} else {
						$args = array(
					        'limit' => -1,
					        'delivery_date' => $date,
					        'delivery_type' => "delivery",
					        'status' => $filtered_order_status
					    );
					}
				    $orders_array = wc_get_orders( $args );
				    foreach ($orders_array as $order) {
				    	$orders[] = $order;
				    }
		    	} elseif($filtered_delivery_type == "pickup") {

		    		if(!empty($filtered_pickup_location)) {
		    			foreach($filtered_pickup_location as $location) {
		    				if($this->hpos) {
								$args = array(
							        'limit' => -1,
									'type' => array( 'shop_order' ),
									'status' => $filtered_order_status,
									'meta_query' => array(
							            array(
							                'key'     => 'pickup_date',
							                'value'   => $date,
							                'compare' => '==',
							            ),
							            array(
							                'key'     => 'pickup_location',
							                'value'   => $location,
							                'compare' => '==',
							            ),
							            array(
							                'key'     => 'delivery_type',
							                'value'   => "pickup",
							                'compare' => '==',
							            ),
							        ),
							    );
							} else {
								$args = array(
							        'limit' => -1,
							        'pickup_date' => $date,
							        'delivery_type' => "pickup",
							        'status' => $filtered_order_status,
							        'pickup_location' => $location
							    );
							}
						    $orders_array = wc_get_orders( $args );
						    foreach ($orders_array as $order) {
						    	$orders[] = $order;
						    }
		    			}
			    		
		    		} else {
		    			if($this->hpos) {
							$args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $filtered_order_status,
								'meta_query' => array(
						            array(
						                'key'     => 'pickup_date',
						                'value'   => $date,
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'delivery_type',
						                'value'   => "pickup",
						                'compare' => '==',
						            ),
						        ),
						    );
						} else {
							$args = array(
						        'limit' => -1,
						        'pickup_date' => $date,
						        'delivery_type' => "pickup",
						        'status' => $filtered_order_status
						    );
						}

					    $orders_array = wc_get_orders( $args );
					    foreach ($orders_array as $order) {
					    	$orders[] = $order;
					    }
		    		}

		    	} else {
		    		if($this->hpos) {
				    	$args = array(
					        'limit' => -1,
							'type' => array( 'shop_order' ),
							'status' => $filtered_order_status,
							'meta_query' => array(
					            array(
					                'key'     => 'delivery_date',
					                'value'   => $date,
					                'compare' => '==',
					            ),
					        ),
					    );
				    } else {
				    	$args = array(
					        'limit' => -1,
					        'delivery_date' => $date,
					        'status' => $filtered_order_status
					    );
				    }

				    $orders_array = wc_get_orders( $args );
				    foreach ($orders_array as $order) {
				    	$delivery_orders[] = $order;
				    }

				    
				    if(!empty($filtered_pickup_location)) {
				    	foreach($filtered_pickup_location as $location) {
						    if($this->hpos) {
								$args = array(
							        'limit' => -1,
									'type' => array( 'shop_order' ),
									'status' => $filtered_order_status,
									'meta_query' => array(
							            array(
							                'key'     => 'pickup_date',
							                'value'   => $date,
							                'compare' => '==',
							            ),
							            array(
							                'key'     => 'pickup_location',
							                'value'   => $location,
							                'compare' => '==',
							            ),
							        ),
							    );
							} else {
								$args = array(
							        'limit' => -1,
							        'pickup_date' => $date,
							        'status' => $filtered_order_status,
							        'pickup_location' => $location
							    );
							}

						    $orders_array = wc_get_orders( $args );
						    foreach ($orders_array as $order) {
						    	$pickup_orders[] = $order;
						    }
						}
		    		} else {
		    			if($this->hpos) {
							$args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $filtered_order_status,
								'meta_query' => array(
						            array(
						                'key'     => 'pickup_date',
						                'value'   => $date,
						                'compare' => '==',
						            ),
						        ),
						    );
						} else {
							$args = array(
						        'limit' => -1,
						        'pickup_date' => $date,
						        'status' => $filtered_order_status
						    );
						}

					    $orders_array = wc_get_orders( $args );
					    foreach ($orders_array as $order) {
					    	$pickup_orders[] = $order;
					    }
		    		}

				    $orders = array_merge($delivery_orders, $pickup_orders);
		    	}		    	
			    
		    }
			
		} else {

		    if($filtered_delivery_type == "delivery"){

			    if($this->hpos) {
						$args = array(
					        'limit' => -1,
							'type' => array( 'shop_order' ),
							'status' => $filtered_order_status,
							'meta_query' => array(
					            array(
					                'key'     => 'delivery_date',
					                'value'   => date("Y-m-d", strtotime($filtered_date)),
					                'compare' => '==',
					            ),
					            array(
					                'key'     => 'delivery_type',
					                'value'   => 'delivery',
					                'compare' => '==',
					            ),
					        ),
					    );
					} else {
						$args = array(
					        'limit' => -1,
					        'delivery_date' => date("Y-m-d", strtotime($filtered_date)),
					        'delivery_type' => "delivery",
					        'status' => $filtered_order_status
					    );
					}
			    $orders = wc_get_orders( $args );
	    	} elseif($filtered_delivery_type == "pickup") {

	    		if(!empty($filtered_pickup_location)) {
	    			foreach($filtered_pickup_location as $location) {
					    if($this->hpos) {
							$args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $filtered_order_status,
								'meta_query' => array(
						            array(
						                'key'     => 'pickup_date',
						                'value'   => date("Y-m-d", strtotime($filtered_date)),
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'pickup_location',
						                'value'   => $location,
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'delivery_type',
						                'value'   => "pickup",
						                'compare' => '==',
						            ),
						        ),
						    );
						} else {
							$args = array(
						        'limit' => -1,
						        'pickup_date' => date("Y-m-d", strtotime($filtered_date)),
						        'delivery_type' => "pickup",
						        'status' => $filtered_order_status,
						        'pickup_location' => $location
						    );
						}

					    $orders_array = wc_get_orders( $args );
					    foreach ($orders_array as $order) {
					    	$orders[] = $order;
					    }
					}
	    		} else {
	    			if($this->hpos) {
							$args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $filtered_order_status,
								'meta_query' => array(
						            array(
						                'key'     => 'pickup_date',
						                'value'   => date("Y-m-d", strtotime($filtered_date)),
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'delivery_type',
						                'value'   => "pickup",
						                'compare' => '==',
						            ),
						        ),
						    );
						} else {
							$args = array(
						        'limit' => -1,
						        'pickup_date' => date("Y-m-d", strtotime($filtered_date)),
						        'delivery_type' => "pickup",
						        'status' => $filtered_order_status
						    );
						}

				    $orders = wc_get_orders( $args );
	    		}

	    	} else {
	    		if($this->hpos) {
			    	$args = array(
				        'limit' => -1,
						'type' => array( 'shop_order' ),
						'status' => $filtered_order_status,
						'meta_query' => array(
				            array(
				                'key'     => 'delivery_date',
				                'value'   => date("Y-m-d", strtotime($filtered_date)),
				                'compare' => '==',
				            ),
				        ),
				    );
			    } else {
			    	$args = array(
				        'limit' => -1,
				        'delivery_date' => date("Y-m-d", strtotime($filtered_date)),
				        'status' => $filtered_order_status
				    );
			    }
			    $delivery_orders = wc_get_orders( $args );

			    if(!empty($filtered_pickup_location)) {
			    	foreach($filtered_pickup_location as $location) {
					    if($this->hpos) {
							$args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $filtered_order_status,
								'meta_query' => array(
						            array(
						                'key'     => 'pickup_date',
						                'value'   => date("Y-m-d", strtotime($filtered_date)),
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'pickup_location',
						                'value'   => $location,
						                'compare' => '==',
						            ),
						        ),
						    );
						} else {
							$args = array(
						        'limit' => -1,
						        'pickup_date' => date("Y-m-d", strtotime($filtered_date)),
						        'status' => $filtered_order_status,
						        'pickup_location' => $location
						    );
						}

					    $orders_array = wc_get_orders( $args );
					    foreach ($orders_array as $order) {
					    	$pickup_orders[] = $order;
					    }
					}
	    		} else {
	    			if($this->hpos) {
						$args = array(
					        'limit' => -1,
							'type' => array( 'shop_order' ),
							'status' => $filtered_order_status,
							'meta_query' => array(
					            array(
					                'key'     => 'pickup_date',
					                'value'   => date("Y-m-d", strtotime($filtered_date)),
					                'compare' => '==',
					            ),
					        ),
					    );
					} else {
						$args = array(
					        'limit' => -1,
					        'pickup_date' => date("Y-m-d", strtotime($filtered_date)),
					        'status' => $filtered_order_status
					    );
					}

				    $pickup_orders = wc_get_orders( $args );
	    		}

			    $orders = array_merge($delivery_orders, $pickup_orders);
	    	}
		    
		}
		$product_name = [];
		$product_quantity = [];
		$other_settings = get_option('coderockz_woo_delivery_other_settings');
		$hide_metadata_reports_calendar = (isset($other_settings['hide_metadata_reports_calendar']) && !empty($other_settings['hide_metadata_reports_calendar'])) ? $other_settings['hide_metadata_reports_calendar'] : false;
		foreach($orders as $order) {
		    foreach ( $order->get_items() as $item_id => $item ) {
			   if($item->get_variation_id() == 0) {
			   		
			   		if($hide_metadata_reports_calendar) {
						if(array_key_exists($item->get_product_id(),$product_quantity)) {
						   $product_quantity[$item->get_product_id()] = $product_quantity[$item->get_product_id()]+$item->get_quantity();
						} else {
							   $product_quantity[$item->get_product_id()] = $item->get_quantity();
						}
						if(!array_key_exists($item->get_product_id(),$product_name)) {
							   $product_name[$item->get_product_id()] = $item->get_name();
						}
					} else {

				   		$item_name = $item->get_name();
						$item_meta_data = $item->get_formatted_meta_data();
						if(!empty($item_meta_data)) {
							foreach ( $item_meta_data as $meta_id => $meta ) {
								$item_name .= ', '.wp_kses_post( strip_tags($meta->value) );
							}
						}

				   		if(array_key_exists($item_name,$product_quantity)) {
					   		$product_quantity[$item_name] = $product_quantity[$item_name]+$item->get_quantity();
					   } else {
					   		$product_quantity[$item_name] = $item->get_quantity();
					   }
					   if(!array_key_exists($item_name,$product_name)) {
					   		$product_name[$item_name] = $item_name;
					   }

					}
			   } else {

				   $variation = new WC_Product_Variation($item->get_variation_id());
				   $item_meta_data = $item->get_formatted_meta_data();
				   $item_name_with_meta = $variation->get_title();
				   
				   if(array_filter($variation->get_variation_attributes())) {
						$item_name_with_meta .= " - ".strip_tags(implode(", ", array_filter($variation->get_variation_attributes(), 'strlen')));   	
					}

					if(!empty($item_meta_data) && !$hide_metadata_reports_calendar) {
				        foreach ( $item_meta_data as $meta_id => $meta ) {
				        	if (!array_key_exists("attribute_".$meta->key,$variation->get_variation_attributes()) || (array_key_exists("attribute_".$meta->key,$variation->get_variation_attributes()) && $variation->get_variation_attributes()["attribute_".$meta->key] == "") )
				            	$item_name_with_meta .= ', '.wp_kses_post( strip_tags($meta->display_value) );

				        }
				    }

				    if(!array_key_exists($item_name_with_meta,$product_name)) {
				   		$product_name[$item_name_with_meta] = $item_name_with_meta;
				    }

				    if(array_key_exists($item_name_with_meta,$product_quantity)) {
				   		$product_quantity[$item_name_with_meta] = $product_quantity[$item_name_with_meta]+$item->get_quantity();
				   } else {
				   		$product_quantity[$item_name_with_meta] = $item->get_quantity();
				   } 
			    }

			}	
		}

		$order_details_html_body = '';
		foreach($product_name as $id => $name) {
			$order_details_html_body .= '<tr>';
	        $order_details_html_body .= '<td>'.$name.'</td>';
			$order_details_html_body .= '<td>'.$product_quantity[$id].'</td>';
			$order_details_html_body .= '</tr>';
		}

		$order_details_html = '';
		$order_details_html .= '<table id="coderockz_woo_delivery_report_product_quantity_table" style="width:50%">';
		$order_details_html .= '<thead>
		            <tr>
		                <th>'.__("Product","coderockz-woo-delivery").'</th>
		                <th>'.__("Quantity","coderockz-woo-delivery").'</th>
		            </tr>
		        </thead>
		        <tbody>';
		$order_details_html .= $order_details_html_body;
		$order_details_html .= '</tbody>';
		$order_details_html .= '<tfoot>
		            <tr>
		                <th>'.__("Product","coderockz-woo-delivery").'</th>
		                <th>'.__("Quantity","coderockz-woo-delivery").'</th>
		            </tr>
		        </tfoot>';
		$order_details_html .= '</table>';
		wp_send_json_success($order_details_html);
    }

    public function coderockz_woo_delivery_make_order_delivered() {
    	check_ajax_referer('coderockz_woo_delivery_nonce');
    	$order_id = sanitize_text_field($_POST[ 'orderId' ]);
    	$order = wc_get_order( $order_id );
    	
    	if($this->hpos) {
			$order->update_meta_data( 'delivery_status', 'delivered' );
			$order->save();
		} else {
			update_post_meta($order_id, 'delivery_status', 'delivered');
		}
    	if((metadata_exists('post', $order_id, 'delivery_type') && get_post_meta($order_id, 'delivery_type', true) !="" && get_post_meta($order_id, 'delivery_type', true) =="pickup") || ($order->meta_exists('delivery_type') && $order->get_meta( 'delivery_type', true ) != "" && $order->get_meta( 'delivery_type', true ) == "pickup")) {
			$delivery_type = "pickup";
		} else {
			$delivery_type = "delivery";
		}
		
		$localization_settings = get_option('coderockz_woo_delivery_localization_settings');

		$delivery_status_delivered_text = (isset($localization_settings['delivery_status_delivered_text']) && !empty($localization_settings['delivery_status_delivered_text'])) ? stripslashes($localization_settings['delivery_status_delivered_text']) : __("Delivery Completed","coderockz-woo-delivery");

		$pickup_status_picked_text = (isset($localization_settings['pickup_status_picked_text']) && !empty($localization_settings['pickup_status_picked_text'])) ? stripslashes($localization_settings['pickup_status_picked_text']) : __("Pickup Completed","coderockz-woo-delivery");

		$response=[
			"delivery_type" => $delivery_type,
			"delivery_status_delivered_text" => $delivery_status_delivered_text,
			"pickup_status_picked_text" => $pickup_status_picked_text,
		];
		$response = json_encode($response);
		wp_send_json_success($response);

    }

    public function coderockz_woo_delivery_make_order_delivered_bulk() {
    	if ( ! is_user_logged_in() ) {
			auth_redirect();
			exit;
		}
		check_ajax_referer('coderockz_woo_delivery_nonce');
    	$order_ids = array();
    	if ( isset( $_REQUEST['orderIds'] ) ) {
			if ( wc_user_has_role( get_current_user_id(), 'administrator' ) || wc_user_has_role( get_current_user_id(), 'shop_manager' ) ) {
				$order_ids = sanitize_text_field( wp_unslash( $_REQUEST['orderIds'] ) );
			} else {
				die( 'You are not allowed to make the delivery/pickup completed.' );
			}
		}
		$order_ids = explode( ',', $order_ids );
		foreach ($order_ids as $order_id) {
			$order = wc_get_order( $order_id );    	
	    	if($this->hpos) {
				$order->update_meta_data( 'delivery_status', 'delivered' );
				$order->save();
			} else {
				update_post_meta($order_id, 'delivery_status', 'delivered');
			}
		}
    	wp_send_json_success();
    }

    public function coderockz_woo_delivery_make_google_calendar_sync_bulk() {
    	if ( ! is_user_logged_in() ) {
			auth_redirect();
			exit;
		}
		check_ajax_referer('coderockz_woo_delivery_nonce');
    	$order_ids = array();
    	if ( isset( $_REQUEST['orderIds'] ) ) {
			if ( wc_user_has_role( get_current_user_id(), 'administrator' ) || wc_user_has_role( get_current_user_id(), 'shop_manager' ) ) {
				$order_ids = sanitize_text_field( wp_unslash( $_REQUEST['orderIds'] ) );
			} else {
				die( 'You are not allowed to sync order to Google calendar.' );
			}
		}
		$order_ids = explode( ',', $order_ids );

		$google_calendar_settings = get_option('coderockz_woo_delivery_google_calendar_settings');
		
		$calendar_sync_customer_client_id = isset($google_calendar_settings['google_calendar_client_id']) && !empty($google_calendar_settings['google_calendar_client_id']) ? $google_calendar_settings['google_calendar_client_id'] : "";
		
		$calendar_sync_customer_client_secret = isset($google_calendar_settings['google_calendar_client_secret']) && !empty($google_calendar_settings['google_calendar_client_secret']) ? $google_calendar_settings['google_calendar_client_secret'] : "";

		if($this->hpos) {
			$calendar_sync_customer_redirect_url = get_site_url().'/wp-admin/edit.php?post_type=shop_order';
		} else {
			$calendar_sync_customer_redirect_url = get_site_url().'/wp-admin/admin.php?page=wc-orders';
		}

		$order_status_keys = array_keys(wc_get_order_statuses());
		$order_status = ['partially-paid'];
		foreach($order_status_keys as $order_status_key) {
			$order_status[] = substr($order_status_key,3);
		}
		$order_status = array_diff($order_status,['cancelled','failed','refunded']);
		$order_status_sync = isset($google_calendar_settings['order_status_sync']) && !empty($google_calendar_settings['order_status_sync']) ? $google_calendar_settings['order_status_sync'] : $order_status;
		
		if(get_option('coderockz_woo_delivery_google_calendar_access_token') && $google_calendar_settings['google_calendar_client_id'] != "" && $google_calendar_settings['google_calendar_client_secret'] != "" ) {
		    
		    $client = new Google_Client();
            $client->setClientId($calendar_sync_customer_client_id);
            $client->setClientSecret($calendar_sync_customer_client_secret);
            $client->setRedirectUri($calendar_sync_customer_redirect_url);
            $client->addScope("https://www.googleapis.com/auth/calendar.events");
            $client->setAccessType('offline');
            
            $client->setAccessToken(get_option('coderockz_woo_delivery_google_calendar_access_token'));
	                    
            if($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken(get_option('coderockz_woo_delivery_google_calendar_access_token')['refresh_token']);
                $access_token = $client->getAccessToken();
                update_option('coderockz_woo_delivery_google_calendar_access_token',$access_token);
                
            }

            $service = new Google_Service_Calendar($client);

            $timezone = $this->helper->get_the_timezone();
            
    	    foreach ($order_ids as $order_id) {

    	    	$order = wc_get_order($order_id);

    	    	if(in_array($order->get_status(), $order_status_sync)) {
	    			
	    			$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
	    			$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');			
	            	$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');			
	        		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
	        		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
	        		$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
	        		$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');
	    			
	    			$order_type_field_label = (isset($delivery_option_settings['delivery_option_label']) && !empty($delivery_option_settings['delivery_option_label'])) ? stripslashes($delivery_option_settings['delivery_option_label']) : "Order Type";
	    			$delivery_field_label = (isset($delivery_option_settings['delivery_label']) && !empty($delivery_option_settings['delivery_label'])) ? stripslashes($delivery_option_settings['delivery_label']) : __("Delivery","coderockz-woo-delivery");
			        $pickup_field_label = (isset($delivery_option_settings['pickup_label']) && !empty($delivery_option_settings['pickup_label'])) ? stripslashes($delivery_option_settings['pickup_label']) : __("Pickup","coderockz-woo-delivery");
			        
			        $delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : __("Delivery Date","coderockz-woo-delivery");
	        		$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : __("Pickup Date","coderockz-woo-delivery");
	        		$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : __("Delivery Time","coderockz-woo-delivery");
	        		$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : __("Pickup Time","coderockz-woo-delivery");
	        		$pickup_location_field_label = (isset($pickup_location_settings['field_label']) && !empty($pickup_location_settings['field_label'])) ? stripslashes($pickup_location_settings['field_label']) : __("Pickup Location","coderockz-woo-delivery");
	        		$additional_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? $additional_field_settings['field_label'] : __("Special Note for Delivery","coderockz-woo-delivery");
	    			
	    			$delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";
			
	        		$add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;
	        
	        		if($add_weekday_name) {
	        			$delivery_date_format = "l ".$delivery_date_format;
	        		}
	        
	        		$pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";
	        
	        		$pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;
	        
	        		if($pickup_add_weekday_name) {
	        			$pickup_date_format = "l ".$pickup_date_format;
	        		}
	        
	        		$time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
	        		if($time_format == 12) {
	        			$time_format = "h:i A";
	        		} elseif ($time_format == 24) {
	        			$time_format = "H:i";
	        		}
	        
	        		$pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
	        		if($pickup_time_format == 12) {
	        			$pickup_time_format = "h:i A";
	        		} elseif ($pickup_time_format == 24) {
	        			$pickup_time_format = "H:i";
	        		}
	    			  			
	    			if((metadata_exists('post', $order_id, 'delivery_type') && get_post_meta( $order_id, 'delivery_type', true ) != "") || ($order->meta_exists('delivery_type') && $order->get_meta( 'delivery_type', true ) != "")) {
		    	

	        	    	if(get_post_meta($order_id, 'delivery_type', true) == "delivery" || $order->get_meta( 'delivery_type', true ) == "delivery") {
	        
	        	    		 $delivery_type = $delivery_field_label;
	        
	        			} elseif(get_post_meta($order_id, 'delivery_type', true) == "pickup" || $order->get_meta( 'delivery_type', true ) == "pickup") {
	        				
	        				$delivery_type = $pickup_field_label;
	        			} else {
	        			    $delivery_type = "";
	        			}
	        
	        	    }

	        	    include_once(ABSPATH.'wp-admin/includes/plugin.php');
	    			
	    			if((metadata_exists('post', $order_id, '_wcj_order_number') && get_post_meta($order_id, '_wcj_order_number', true) !="") || ($order->meta_exists('_wcj_order_number') && $order->get_meta( '_wcj_order_number', true ) != "")) {
	    				if($this->hpos) {
							$order_id_with_custom = '#'.$order->get_meta( '_wcj_order_number', true );
						} else {
							$order_id_with_custom = '#'.get_post_meta($order_id, '_wcj_order_number', true);
						}
	    			} elseif(is_plugin_active('wt-woocommerce-sequential-order-numbers-pro/wt-advanced-order-number-pro.php') || is_plugin_active('wt-woocommerce-sequential-order-numbers/wt-advanced-order-number.php') || is_plugin_active('custom-order-numbers-for-woocommerce/custom-order-numbers-for-woocommerce.php') || is_plugin_active('yith-woocommerce-sequential-order-number-premium/init.php')) {
	    				$order_id_with_custom = '#'.$order->get_order_number();
	    			} else {
	    				$order_id_with_custom = '#'.$order_id;
	    			}
	                
	                if((metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id, 'delivery_time', true) !="" && get_post_meta($order_id, 'delivery_time', true) =="as-soon-as-possible") || ($order->meta_exists('delivery_time') && $order->get_meta( 'delivery_time', true ) != "" && $order->get_meta( 'delivery_time', true ) == "as-soon-as-possible")) {
	                    $as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : __("As Soon As Possible", 'coderockz-woo-delivery');
	                    $summary = $delivery_type.$order_id_with_custom."(".$as_soon_as_possible_text.")". " - " . $order->get_billing_first_name() ." ".$order->get_billing_last_name();
	                } else {
	                    $summary = $delivery_type.$order_id_with_custom. " - " . $order->get_billing_first_name() ." ".$order->get_billing_last_name();
	                }
	                               
	                if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {
	                	if($this->hpos) {
							$date = $order->get_meta( 'delivery_date', true );
						} else {
							$date = get_post_meta( $order_id, 'delivery_date', true );
						}
				    	

				    }

				    if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {

				    	if($this->hpos) {
							$date = $order->get_meta( 'pickup_date', true );
						} else {
							$date = get_post_meta( $order_id, 'pickup_date', true );
						} 

				    }

				    if((metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id,"delivery_time",true) != "") || ($order->meta_exists('delivery_time') && $order->get_meta( 'delivery_time', true ) != "")) {

				    	if((get_post_meta($order_id, 'delivery_time', true) != "as-soon-as-possible" && get_post_meta($order_id, 'delivery_time', true) != "conditional-delivery") || ($order->get_meta( 'delivery_time', true ) != "as-soon-as-possible" && $order->get_meta( 'delivery_time', true ) != "conditional-delivery")) {
				    		if($this->hpos) {
								$minutes = $order->get_meta( 'delivery_time', true );
							} else {
								$minutes = get_post_meta($order_id,"delivery_time",true);
							}	
					    	$minutes = explode(' - ', $minutes);

				    		if(!isset($minutes[1])) {
				    			$time_start = "T".$minutes[0].':00';
				    			$time_end = "T".$minutes[0].':00';
				    		} else {

				    			$time_start = "T".$minutes[0].':00';
				    			$time_end = "T".$minutes[1].':00'; 			
				    		}
			    		} elseif(get_post_meta($order_id, 'delivery_time', true) == "conditional-delivery" || $order->get_meta( 'delivery_time', true ) == "conditional-delivery") {
			    		    $delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
						    $minutes = date($time_format, mktime(0, (int)((date("G")*60)+date("i")))) . " - ".date($time_format, mktime(0, (int)((date("G")*60)+date("i") + $delivery_fee_settings['conditional_delivery_fee_duration']))); 
						    $minutes = explode(' - ', $minutes);

				    		if(!isset($minutes[1])) {
				    			$time_start = "T".$minutes[0].':00';
				    			$time_end = "T".$minutes[0].':00';
				    		} else {

				    			$time_start = "T".$minutes[0].':00';
				    			$time_end = "T".$minutes[1].':00'; 			
				    		}
			    		}
				    	
				    }

				    if((metadata_exists('post', $order_id, 'pickup_time') && get_post_meta($order_id,"pickup_time",true) != "") || ($order->meta_exists('pickup_time') && $order->get_meta( 'pickup_time', true ) != "")) {
				    	if($this->hpos) {
							$pickup_minutes = $order->get_meta( 'pickup_time', true );
						} else {
							$pickup_minutes = get_post_meta($order_id,"pickup_time",true);
						}
				    	$pickup_minutes = explode(' - ', $pickup_minutes);

				    	if(!isset($pickup_minutes[1])) {
			    			$time_start = "T".$pickup_minutes[0].':00';
			    			$time_end = "T".$pickup_minutes[0].':00';
			    		} else {

			    			$time_start = "T".$pickup_minutes[0].':00';
			    			$time_end = "T".$pickup_minutes[1].':00'; 			
			    		}
				    	
				    }
				    
				    if((metadata_exists('post', $order_id, 'pickup_location') && get_post_meta($order_id, 'pickup_location', true) !="") || ($order->meta_exists('pickup_location') && $order->get_meta( 'pickup_location', true ) != "")) {
	    				if($this->hpos) {
							$location = stripslashes(html_entity_decode($order->get_meta( 'pickup_location', true ), ENT_QUOTES));
						} else {
							$location = stripslashes(html_entity_decode(get_post_meta($order_id, 'pickup_location', true), ENT_QUOTES));
						} 
	    			} else {
	    			    $location = "";
	    			}
	    				    			
	    			$start = "";
	    			$end = "";
	    			if(isset($date)) {
	    			    $start .= $date;
	    			    $end .= $date;
	    			} else {
	    			    $order_created_obj= current_datetime($order->get_date_created());
	    			    $start .= $order_created_obj->format("Y-m-d");
	    			    $end .= $order_created_obj->format("Y-m-d");
	    			}
	    			
	    			if(isset($time_start)) {
	    			    $start .= $time_start;
	    			    $dateCriteria = 'dateTime';
	    			} else {
	    			   $dateCriteria = 'date'; 
	    			}
	    			
	    			if(isset($time_end)) {
	    			    $end .= $time_end;
	    			    $dateCriteria = 'dateTime';
	    			} else {
	    			   $dateCriteria = 'date'; 
	    			}
	    			    			
	    			$delivery_details = "<b>"."Delivery Details:"."</b><br/>";
	    			if($delivery_type != "") {
					    $delivery_details .= $order_type_field_label.': ' . $delivery_type . "<br/>";
					}
	    		    if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {
	    
	    		    	if($this->hpos) {
	    		    		$delivery_date = $helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime($order->get_meta( 'delivery_date', true ))),"delivery"),"delivery");
							$delivery_details .= $delivery_date_field_label.': ' . $delivery_date . "<br/>";
						} else {
							$delivery_date = $helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime(get_post_meta( $order_id, 'delivery_date', true ))),"delivery"),"delivery");
							$delivery_details .= $delivery_date_field_label.': ' . $delivery_date . "<br/>";
						}
	    		    }
	    
	    		    if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {
	    
	    		    	if($this->hpos) {
	    		    		$pickup_date = $helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime($order->get_meta( 'pickup_date', true ))),"pickup"),"pickup");
							$delivery_details .= $pickup_date_field_label.': ' . $pickup_date . "<br/>"; 
						} else {
							$pickup_date = $helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime(get_post_meta( $order_id, 'pickup_date', true ))),"pickup"),"pickup");
							$delivery_details .= $pickup_date_field_label.': ' . $pickup_date . "<br/>"; 
						}
	    		    }
	    
	    		    if((metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id,"delivery_time",true) != "") || ($order->meta_exists('delivery_time') && $order->get_meta( 'delivery_time', true ) != "")) {
	    
	    		    	if((get_post_meta($order_id, 'delivery_time', true) != "as-soon-as-possible" && get_post_meta($order_id, 'delivery_time', true) != "conditional-delivery") || ($order->get_meta( 'delivery_time', true ) != "as-soon-as-possible" && $order->get_meta( 'delivery_time', true ) != "conditional-delivery")) {
	    			    	if($this->hpos) {
								$minutes = $order->get_meta( 'delivery_time', true );
							} else {
								$minutes = get_post_meta($order_id,"delivery_time",true);
							}
	    			    	$minutes = explode(' - ', $minutes);
	    
	    		    		if(!isset($minutes[1])) {
	    		    			$delivery_details .= $delivery_time_field_label.': ' . date($time_format, strtotime($minutes[0])) . "<br/>";
	    		    		} else {
	    
	    		    			$delivery_details .= $delivery_time_field_label.': ' . date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1])) . "<br/>";  			
	    		    		}
	    	    		} elseif (get_post_meta($order_id, 'delivery_time', true) == "conditional-delivery" || $order->get_meta( 'delivery_time', true ) == "conditional-delivery") {
	    	    		    $delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
						    $minutes = date($time_format, mktime(0, (int)((date("G")*60)+date("i")))) . " - ".date($time_format, mktime(0, (int)((date("G")*60)+date("i") + $delivery_fee_settings['conditional_delivery_fee_duration'])));
						    $minutes = explode(' - ', $minutes);
	    
	    		    		if(!isset($minutes[1])) {
	    		    			$delivery_details .= $delivery_time_field_label.': ' . date($time_format, strtotime($minutes[0])) . "<br/>";
	    		    		} else {
	    
	    		    			$delivery_details .= $delivery_time_field_label.': ' . date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1])) . "<br/>";  			
	    		    		}
	    	    		} else {
	    	    			$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : "As Soon As Possible";
	    	    			$delivery_details .= $delivery_time_field_label.': ' . $as_soon_as_possible_text . "<br/>";
	    	    		}
	    		    }
	    
	    		    if((metadata_exists('post', $order_id, 'pickup_time') && get_post_meta($order_id,"pickup_time",true) != "") || ($order->meta_exists('pickup_time') && $order->get_meta( 'pickup_time', true ) != "")) {
	    		    	if($this->hpos) {
							$pickup_minutes = $order->get_meta( 'pickup_time', true );
						} else {
							$pickup_minutes = get_post_meta($order_id,"pickup_time",true);
						}
	    		    	$pickup_minutes = explode(' - ', $pickup_minutes);
	    
	    	    		if(!isset($pickup_minutes[1])) {
	    	    			$delivery_details .= $pickup_time_field_label.': ' . date($pickup_time_format, strtotime($pickup_minutes[0])) . "<br/>";
	    	    		} else {
	    
	    	    			$delivery_details .= $pickup_time_field_label.': ' . date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1])) . "<br/>";  			
	    	    		}	
	    		    }
	    
	    		    if((metadata_exists('post', $order_id, 'pickup_location') && get_post_meta($order_id, 'pickup_location', true) !="") || ($order->meta_exists('pickup_location') && $order->get_meta( 'pickup_location', true ) != "")) {
	    		    	if($this->hpos) {
							$delivery_details .= $pickup_location_field_label.': ' . stripslashes(html_entity_decode($order->get_meta( 'pickup_location', true ), ENT_QUOTES)) . "<br/>";
						} else {
							$delivery_details .= $pickup_location_field_label.': ' . stripslashes(html_entity_decode(get_post_meta($order_id, 'pickup_location', true), ENT_QUOTES)) . "<br/>";
						}	    				
	    			}
	    
	    			if((metadata_exists('post', $order_id, 'additional_note') && get_post_meta($order_id, 'additional_note', true) !="") || ($order->meta_exists('additional_note') && $order->get_meta( 'additional_note', true )!= "")) {
	    				if($this->hpos) {
							$delivery_details .= $additional_field_label.': ' . stripslashes(html_entity_decode($order->get_meta( 'additional_note', true ), ENT_QUOTES));
						} else {
							$delivery_details .= $additional_field_label.': ' . stripslashes(html_entity_decode(get_post_meta($order_id, 'additional_note', true), ENT_QUOTES));
						}
	    			}
	    			
	    			$i=1;
	    			$other_settings = get_option('coderockz_woo_delivery_other_settings');
					$hide_metadata_reports_calendar = (isset($other_settings['hide_metadata_reports_calendar']) && !empty($other_settings['hide_metadata_reports_calendar'])) ? $other_settings['hide_metadata_reports_calendar'] : false;
	    			$product_details = "<br/><b>"."Products:"."</b><br/>";
	    			foreach ($order->get_items() as $item_id => $item) {

						if($item->get_variation_id() == 0) {
						   	$product_quantity = $item->get_quantity();
						   	$product_name = $item->get_name();
							$item_meta_data = $item->get_formatted_meta_data();
							if(!empty($item_meta_data) && !$hide_metadata_reports_calendar) {
								foreach ( $item_meta_data as $meta_id => $meta ) {
									$product_name .= ', '.wp_kses_post( strip_tags($meta->value) );
								}
							}
					   } else {
						    $variation = new WC_Product_Variation($item->get_variation_id());
							$item_meta_data = $item->get_formatted_meta_data();
							$product_quantity = $item->get_quantity();
							$product_name = $variation->get_title();
							if(array_filter($variation->get_variation_attributes())) {
								$product_name .= " - ".strip_tags(implode(", ", array_filter($variation->get_variation_attributes(), 'strlen')));	
							}
							if(!empty($item_meta_data) && !$hide_metadata_reports_calendar) {
								foreach ( $item_meta_data as $meta_id => $meta ) {
									if (!array_key_exists("attribute_".$meta->key,$variation->get_variation_attributes()) || (array_key_exists("attribute_".$meta->key,$variation->get_variation_attributes()) && $variation->get_variation_attributes()["attribute_".$meta->key] == "") )
										$product_name .= ', '.wp_kses_post( strip_tags($meta->display_value) );

								}
							}
					    }
	    				$product_details .= $i.'. ';
	    				$product_details .= $product_name;
	    				$product_details .= '   '.$this->helper->format_price($order->get_item_total( $item,true ),$order_id).'x';
	    				$product_details .= $product_quantity.'=';
	    				$product_details .= $this->helper->format_price($item->get_total() + $item->get_subtotal_tax(),$order_id);
	    				$product_details .= "<br/>";
	    				$i = $i+1;
	    			}
	    			
	    			$total = "<br/><b>"."Total: "."</b>".$order->get_currency() . $order->get_total()."<br/>";
	    			
	    			$order_billing_address = "<br/><b>"."Billing Address:"."</b><br/>".$order->get_formatted_billing_address();
				    $order_billing_address .= "<br/>".'Mobile: '.$order->get_billing_phone();
				    $order_billing_address .= "<br/>".'Email: '.$order->get_billing_email();
				    $order_billing_address .="<br/>";
				    $order_shipping_address = "<br/><b>"."Shipping Address:"."</b><br/>".$order->get_formatted_shipping_address()."<br/>";
				    
				    $sync_custom_field_name = isset($google_calendar_settings['sync_custom_field_name']) && !empty($google_calendar_settings['sync_custom_field_name']) ? $google_calendar_settings['sync_custom_field_name'] : [];

			        $custom_field = "";
			        if(!empty($sync_custom_field_name)) {
				        
				        foreach($sync_custom_field_name as $custom_field_name) {
				        
					        if((metadata_exists('post', $order_id, $custom_field_name) && get_post_meta($order_id, $custom_field_name, true) !="") || ($order->meta_exists($custom_field_name) && $order->get_meta( $custom_field_name, true )!= "")) {

					        	if($this->hpos) {
									$custom_field .= "<br/><b>".ucwords(str_replace('_', ' ', $custom_field_name)).':</b> '.$order->get_meta( $custom_field_name, true );
								} else {
									$custom_field .= "<br/><b>".ucwords(str_replace('_', ' ', $custom_field_name)).':</b> '.get_post_meta($order_id, $custom_field_name, true);
								}

						    }

						}
					}

				    $payment_method = "<br/><b>"."Payment Method: "."</b>".$order->get_payment_method_title()."<br/>";
				    $order_status = "<br/><b>"."Order Status: "."</b>".$order->get_status()."<br/>";
				    
				    $customer_note = "<br/><b>"."Customer Note: "."</b>".$order->get_customer_note();

				    if($this->hpos) {
				    	$edit_order = "<b><a href='".get_site_url()."/wp-admin/admin.php?page=wc-orders&action=edit&id=".$order_id."' target='_blank'>".get_site_url()."/wp-admin/admin.php?page=wc-orders&action=edit&id=".$order_id."</a></b><br/><br/>";
				    } else {
				    	$edit_order = "<b><a href='".get_site_url()."/wp-admin/post.php?post=".$order_id."&action=edit' target='_blank'>".get_site_url()."/wp-admin/post.php?post=".$order_id."&action=edit</a></b><br/><br/>";
				    }
				    
				    $description = $edit_order.$delivery_details.$product_details.$total.$order_billing_address.$order_shipping_address.$custom_field.$payment_method.$order_status.$customer_note;
		    	    
		    	    $event = new Google_Service_Calendar_Event(array(
	                  'id' => 'order'.$order_id,
	                  'summary' => $summary,
	                  'location' => $location,
	                  'description' => $description,
	                  'start' => array(
	                    $dateCriteria => $start,
	                    'timeZone' => $timezone,
	                  ),
	                  'end' => array(
	                    $dateCriteria => $end,
	                    'timeZone' => $timezone,
	                  ),
	                  'reminders' => array(
	                    'useDefault' => true,
	                  ),
	                ));
	                
		            $calendarId = isset($google_calendar_settings['google_calendar_id']) && !empty($google_calendar_settings['google_calendar_id']) ? $google_calendar_settings['google_calendar_id'] : 'primary';

		            $exists = false;
					try {
				        if($service->events->get($calendarId, 'order'.$order_id)){
				                $exists = true;
				                $service->events->update($calendarId, 'order'.$order_id, $event);
				        }
					} catch (Exception $e) {}

					if (!$exists) {
					        $service->events->insert($calendarId, $event);
					}
				}

			}

		}
    	wp_send_json_success();
    }

    public function coderockz_woo_delivery_make_order_complete() {
    	check_ajax_referer('coderockz_woo_delivery_nonce');
    	$order_id = sanitize_text_field($_POST[ 'orderId' ]);
    	$order = wc_get_order( $order_id );
    	$order->update_status( 'completed' );

    	$other_settings = get_option('coderockz_woo_delivery_other_settings');

    	$mark_delivery_completed_with_order_completed = (isset($other_settings['mark_delivery_completed_with_order_completed']) && !empty($other_settings['mark_delivery_completed_with_order_completed'])) ? $other_settings['mark_delivery_completed_with_order_completed'] : false;

    	if( $mark_delivery_completed_with_order_completed ) {
	    	if($this->hpos) {
				$order->update_meta_data( 'delivery_status', 'delivered' );
				$order->save();
			} else {
				update_post_meta($order_id, 'delivery_status', 'delivered');
			}
	    }

	    if((metadata_exists('post', $order_id, 'delivery_type') && get_post_meta($order_id, 'delivery_type', true) !="") || ($order->meta_exists('delivery_type') && $order->get_meta( 'delivery_type', true )!= "")) {
	    	if($this->hpos) {
				$delivery_type =  $order->get_meta( 'delivery_type', true );
			} else {
				$delivery_type = get_post_meta($order_id, 'delivery_type', true);
			}	    	
	    }

	    $localization_settings = get_option('coderockz_woo_delivery_localization_settings');

	    $delivery_status_delivered_text = (isset($localization_settings['delivery_status_delivered_text']) && !empty($localization_settings['delivery_status_delivered_text'])) ? stripslashes($localization_settings['delivery_status_delivered_text']) : __("Delivery Completed","coderockz-woo-delivery");
		$pickup_status_picked_text = (isset($localization_settings['pickup_status_picked_text']) && !empty($localization_settings['pickup_status_picked_text'])) ? stripslashes($localization_settings['pickup_status_picked_text']) : __("Pickup Completed","coderockz-woo-delivery");

	    $response=[
			"mark_delivery_completed_with_order_completed" => $mark_delivery_completed_with_order_completed,
			"delivery_type" => $delivery_type,
			"delivery_status_delivered_text" => $delivery_status_delivered_text,
			"pickup_status_picked_text" => $pickup_status_picked_text,
		];
		$response = json_encode($response);
		wp_send_json_success($response);
    }
    
    public function coderockz_woo_delivery_process_delivery_date_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		
		$date_form_settings = [];

		parse_str( $_POST[ 'dateFormData' ], $date_form_data );

		$enable_delivery_date = !isset($date_form_data['coderockz_enable_delivery_date']) ? false : true;
		
		$delivery_date_mandatory = !isset($date_form_data['coderockz_delivery_date_mandatory']) ? false : true;
		
		$delivery_date_field_label = sanitize_text_field($date_form_data['coderockz_delivery_date_field_label']);
		$delivery_date_field_placeholder = sanitize_text_field($date_form_data['coderockz_delivery_date_field_placeholder']);
		
		$delivery_date_selectable_date = sanitize_text_field($date_form_data['coderockz_delivery_date_selectable_date']);
		$delivery_date_selectable_date_until = sanitize_text_field($date_form_data['coderockz_delivery_date_selectable_date_until']);
		$maximum_order_per_day = sanitize_text_field($date_form_data['coderockz_delivery_date_maximum_order_per_day']);
		$maximum_order_product_per_day = sanitize_text_field($date_form_data['coderockz_delivery_date_maximum_order_product_per_day']);
		
		$delivery_date_format = sanitize_text_field($date_form_data['coderockz_delivery_date_format']);

		$add_weekday_name = !isset($date_form_data['coderockz_woo_delivery_add_weekday_name']) ? false : true;

		$delivery_date_calendar_locale = sanitize_text_field($date_form_data['coderockz_delivery_date_calendar_locale']);
		$calendar_theme = sanitize_text_field($date_form_data['coderockz_woo_delivery_calendar_theme']);

		$delivery_week_starts_from = sanitize_text_field($date_form_data['coderockz_delivery_date_week_starts_from']);
		
		$delivery_date_delivery_days="";
		if(isset($date_form_data['coderockz_delivery_date_delivery_days'])) {
			$delivery_days = $this->helper->coderockz_woo_delivery_array_sanitize($date_form_data['coderockz_delivery_date_delivery_days']);
			$delivery_date_delivery_days = implode(',', $delivery_days);
		}

		$same_day_delivery = !isset($date_form_data['coderockz_disable_same_day_delivery']) ? false : true;
		$auto_select_first_date = !isset($date_form_data['coderockz_auto_select_first_date']) ? false : true;
		
		$date_form_settings['enable_delivery_date'] = $enable_delivery_date;
		$date_form_settings['delivery_date_mandatory'] = $delivery_date_mandatory;
		$date_form_settings['field_label'] = $delivery_date_field_label;
		$date_form_settings['field_placeholder'] = $delivery_date_field_placeholder;
		$date_form_settings['selectable_date'] = $delivery_date_selectable_date;
		$date_form_settings['selectable_date_until'] = $delivery_date_selectable_date_until;
		$date_form_settings['maximum_order_per_day'] = $maximum_order_per_day;
		$date_form_settings['maximum_order_product_per_day'] = $maximum_order_product_per_day;
		$date_form_settings['date_format'] = $delivery_date_format;
		$date_form_settings['add_weekday_name'] = $add_weekday_name;
		$date_form_settings['delivery_days'] = $delivery_date_delivery_days;
		$date_form_settings['calendar_locale'] = $delivery_date_calendar_locale;
		$date_form_settings['calendar_theme'] = $calendar_theme;
		$date_form_settings['week_starts_from'] = $delivery_week_starts_from;
		$date_form_settings['disable_same_day_delivery'] = $same_day_delivery;
		$date_form_settings['auto_select_first_date'] = $auto_select_first_date;
		
		if(get_option('coderockz_woo_delivery_date_settings') == false) {
			update_option('coderockz_woo_delivery_date_settings', $date_form_settings);
		} else {
			$date_form_settings = array_merge(get_option('coderockz_woo_delivery_date_settings'),$date_form_settings);
			update_option('coderockz_woo_delivery_date_settings', $date_form_settings);
		}
		wp_send_json_success();
		
    }

    public function coderockz_woo_delivery_process_delivery_date_delivery_opendays_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
    	$year_array = [];
    	$opendays_array = [];
    	$date_form_settings = [];
    	parse_str( $_POST[ 'dateFormData' ], $date_form_data );
    	foreach($date_form_data as $key => $value) {
		    if (strpos($key, 'coderockz_woo_delivery_delivery_opendays_year_') === 0) {
		        array_push($year_array,sanitize_text_field($value));
		    }
		}
		foreach($year_array as $year) {
			$opendays_months = $this->helper->coderockz_woo_delivery_array_sanitize($date_form_data["coderockz_woo_delivery_delivery_opendays_month_".$year]);
			if(!empty($opendays_months)){
				foreach($opendays_months as $opendays_month) {
					if($opendays_month != "") {
						$opendays_days = sanitize_text_field($date_form_data["coderockz_woo_delivery_delivery_opendays_dates_".$opendays_month."_".$year]);
						if(isset($opendays_days) && $opendays_days != "") {
							$formated_opendays = [];
							$opendays_days = explode(',', $opendays_days);
							foreach($opendays_days as $opendays_day) {
								$formated_opendays[] = sprintf("%02d", $opendays_day);
							}
							$formated_opendays = implode(',', $formated_opendays);
							$opendays_array[$year][$opendays_month] = $formated_opendays;
						}	
					}
				}
			}
			
		}

		$overall_off_before = isset($date_form_data['coderockz_woo_delivery_overall_off_before_delivery']) && $date_form_data['coderockz_woo_delivery_overall_off_before_delivery'] != "" ? $date_form_data['coderockz_woo_delivery_overall_off_before_delivery'] : "";
		$date_form_settings['overall_off_before'] = $overall_off_before;

		$date_form_settings['open_days'] = $opendays_array;
		
		if(get_option('coderockz_woo_delivery_date_settings') == false) {
			update_option('coderockz_woo_delivery_date_settings', $date_form_settings);
		} else {
			$date_form_settings = array_merge(get_option('coderockz_woo_delivery_date_settings'),$date_form_settings);
			update_option('coderockz_woo_delivery_date_settings', $date_form_settings);
		}
		wp_send_json_success();
		
    }

    public function coderockz_woo_delivery_category_open_days_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $open_days_form_data );

		$open_processing_days = [];
		$open_days_categories = $this->helper->coderockz_woo_delivery_array_sanitize($open_days_form_data['coderockz_delivery_open_days_categories']);
		foreach($open_days_categories as $open_days_category) {
			$category = str_replace("c-w-d"," ", $open_days_category);
			
			if(isset($open_days_form_data['coderockz-woo-delivery-open-days-date-'.$open_days_category]) && $open_days_form_data['coderockz-woo-delivery-open-days-date-'.$open_days_category] != "" && $category != "") {

				$open_specific_dates = sanitize_text_field($open_days_form_data['coderockz-woo-delivery-open-days-date-'.$open_days_category]);

				if(strpos($open_specific_dates, '...') !== false) {

					$temporary_dates = explode(',', str_replace(' ', '', $open_specific_dates));
					$specific_date_array = [];
					foreach($temporary_dates as $temporary_date) {
						if(strpos($temporary_date, '...') !== false) {
							$filtered_dates = explode('...', $temporary_date);
							$period = $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1]);
							$specific_date_array = array_merge($specific_date_array,$period);
						} else {
							$specific_date_array[] = $temporary_date;
						}
					}
					
				    $open_processing_days[$category]['specific_date_open_string'] = str_replace(' ', '', $open_specific_dates);
				    $open_processing_days[$category]['specific_date_open'] = $specific_date_array;

				} else {
					$open_processing_days[$category]['specific_date_open_string'] = str_replace(' ', '', $open_specific_dates);
					$specific_date_array = explode(',', str_replace(' ', '', $open_specific_dates));
					$open_processing_days[$category]['specific_date_open'] = $specific_date_array;
				}



				if($open_days_form_data['coderockz-woo-delivery-open-days-date-off-before-'.$open_days_category] != "") {
					$open_processing_days[$category]['off_before'] .= sanitize_text_field($open_days_form_data['coderockz-woo-delivery-open-days-date-off-before-'.$open_days_category]);
				}

			}
		}

		$disable_opendays_regular_product = !isset($open_days_form_data['coderockz_woo_delivery_disable_opendays_regular_product']) ? false : true;
		$open_days_form_settings['disable_opendays_regular_product'] = $disable_opendays_regular_product;
		$open_days_form_settings['category_open_days'] = $open_processing_days;

		if(get_option('coderockz_woo_delivery_open_days_settings') == false) {
			update_option('coderockz_woo_delivery_open_days_settings', $open_days_form_settings);
		} else {
			$open_days_form_settings = array_merge(get_option('coderockz_woo_delivery_open_days_settings'),$open_days_form_settings);
			update_option('coderockz_woo_delivery_open_days_settings', $open_days_form_settings);
		}

		wp_send_json_success();

	}

	public function coderockz_woo_delivery_category_open_days_pickup_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $open_days_form_data );
		$open_processing_days = [];
		$open_days_categories = $this->helper->coderockz_woo_delivery_array_sanitize($open_days_form_data['coderockz_delivery_open_days_pickup_categories']);
		foreach($open_days_categories as $open_days_category) {
			$category = str_replace("c-w-d"," ", $open_days_category);
			
			if($open_days_form_data['coderockz-woo-delivery-open-days-pickup-date-'.$open_days_category] != "" && $category != "") {

				$open_specific_dates = sanitize_text_field($open_days_form_data['coderockz-woo-delivery-open-days-pickup-date-'.$open_days_category]);
				
				if(strpos($open_specific_dates, '...') !== false) {

					$temporary_dates = explode(',', str_replace(' ', '', $open_specific_dates));
					$specific_date_array = [];
					foreach($temporary_dates as $temporary_date) {
						if(strpos($temporary_date, '...') !== false) {
							$filtered_dates = explode('...', $temporary_date);
						    $period = $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1]);
							$specific_date_array = array_merge($specific_date_array,$period);
						} else {
							$specific_date_array[] = $temporary_date;
						}
					}
					
				    $open_processing_days[$category]['specific_date_open_string'] = str_replace(' ', '', $open_specific_dates);
				    $open_processing_days[$category]['specific_date_open'] = $specific_date_array;

				} else {
					$open_processing_days[$category]['specific_date_open_string'] = str_replace(' ', '', $open_specific_dates);
					$specific_date_array = explode(',', str_replace(' ', '', $open_specific_dates));
					$open_processing_days[$category]['specific_date_open'] = $specific_date_array;
				}

				if($open_days_form_data['coderockz-woo-delivery-open-days-pickup-date-off-before-'.$open_days_category] != "") {
					$open_processing_days[$category]['off_before'] .= sanitize_text_field($open_days_form_data['coderockz-woo-delivery-open-days-pickup-date-off-before-'.$open_days_category]);
				}

			}
		}

		$disable_opendays_pickup_regular_product = !isset($open_days_form_data['coderockz_woo_delivery_disable_opendays_pickup_regular_product']) ? false : true;

		$open_days_form_settings['disable_opendays_pickup_regular_product'] = $disable_opendays_pickup_regular_product;

		$open_days_form_settings['category_open_days_pickup'] = $open_processing_days;

		if(get_option('coderockz_woo_delivery_open_days_settings') == false) {
			update_option('coderockz_woo_delivery_open_days_settings', $open_days_form_settings);
		} else {
			$open_days_form_settings = array_merge(get_option('coderockz_woo_delivery_open_days_settings'),$open_days_form_settings);
			update_option('coderockz_woo_delivery_open_days_settings', $open_days_form_settings);
		}

		wp_send_json_success();
	}

    public function coderockz_woo_delivery_process_pickup_date_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		
		$date_form_settings = [];

		parse_str( $_POST[ 'dateFormData' ], $date_form_data );

		$enable_pickup_date = !isset($date_form_data['coderockz_enable_pickup_date']) ? false : true;
		
		$pickup_date_mandatory = !isset($date_form_data['coderockz_pickup_date_mandatory']) ? false : true;

		$pickup_date_field_label = sanitize_text_field($date_form_data['coderockz_pickup_date_field_label']);
		$pickup_date_field_placeholder = sanitize_text_field($date_form_data['coderockz_delivery_pickup_field_placeholder']);
		
		$pickup_date_selectable_date = sanitize_text_field($date_form_data['coderockz_pickup_date_selectable_date']);
		$pickup_date_selectable_date_until = sanitize_text_field($date_form_data['coderockz_pickup_date_selectable_date_until']);

		$maximum_pickup_per_day = sanitize_text_field($date_form_data['coderockz_delivery_date_maximum_pickup_per_day']);
		$maximum_pickup_product_per_day = sanitize_text_field($date_form_data['coderockz_delivery_date_maximum_pickup_product_per_day']);
		
		$pickup_date_format = sanitize_text_field($date_form_data['coderockz_pickup_date_format']);

		$add_weekday_name = !isset($date_form_data['coderockz_woo_delivery_pickup_add_weekday_name']) ? false : true;

		$pickup_date_calendar_locale = sanitize_text_field($date_form_data['coderockz_pickup_date_calendar_locale']);


		$pickup_week_starts_from = sanitize_text_field($date_form_data['coderockz_pickup_date_week_starts_from']);
		
		$pickup_date_delivery_days="";
		if(isset($date_form_data['coderockz_pickup_date_delivery_days'])) {
			$delivery_days = $this->helper->coderockz_woo_delivery_array_sanitize($date_form_data['coderockz_pickup_date_delivery_days']);
			$pickup_date_delivery_days = implode(',', $delivery_days);
		}

		$same_day_pickup = !isset($date_form_data['coderockz_disable_same_day_pickup']) ? false : true;
		$auto_select_first_pickup_date = !isset($date_form_data['coderockz_auto_select_first_pickup_date']) ? false : true;
		
		$date_form_settings['enable_pickup_date'] = $enable_pickup_date;
		$date_form_settings['pickup_date_mandatory'] = $pickup_date_mandatory;
		$date_form_settings['pickup_field_label'] = $pickup_date_field_label;
		$date_form_settings['pickup_field_placeholder'] = $pickup_date_field_placeholder;
		$date_form_settings['selectable_date'] = $pickup_date_selectable_date;
		$date_form_settings['selectable_date_until'] = $pickup_date_selectable_date_until;
		$date_form_settings['maximum_pickup_per_day'] = $maximum_pickup_per_day;
		$date_form_settings['maximum_pickup_product_per_day'] = $maximum_pickup_product_per_day;
		$date_form_settings['date_format'] = $pickup_date_format;
		$date_form_settings['add_weekday_name'] = $add_weekday_name;
		$date_form_settings['pickup_days'] = $pickup_date_delivery_days;
		$date_form_settings['calendar_locale'] = $pickup_date_calendar_locale;
		$date_form_settings['week_starts_from'] = $pickup_week_starts_from;
		$date_form_settings['disable_same_day_pickup'] = $same_day_pickup;
		$date_form_settings['auto_select_first_pickup_date'] = $auto_select_first_pickup_date;
		
		if(get_option('coderockz_woo_delivery_pickup_date_settings') == false) {
			update_option('coderockz_woo_delivery_pickup_date_settings', $date_form_settings);
		} else {
			$date_form_settings = array_merge(get_option('coderockz_woo_delivery_pickup_date_settings'),$date_form_settings);
			update_option('coderockz_woo_delivery_pickup_date_settings', $date_form_settings);
		}

		$pickup_date_form_settings = [];
		$calendar_theme = sanitize_text_field($date_form_data['coderockz_woo_delivery_pickup_calendar_theme']);

		$pickup_date_form_settings['calendar_theme'] = $calendar_theme;

		if(get_option('coderockz_woo_delivery_date_settings') == false) {
			update_option('coderockz_woo_delivery_date_settings', $pickup_date_form_settings);
		} else {
			$pickup_date_form_settings = array_merge(get_option('coderockz_woo_delivery_date_settings'),$pickup_date_form_settings);
			update_option('coderockz_woo_delivery_date_settings', $pickup_date_form_settings);
		}

		wp_send_json_success();
		
    }

    public function coderockz_woo_delivery_process_delivery_date_pickup_opendays_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
    	$year_array = [];
    	$opendays_array = [];
    	parse_str( $_POST[ 'dateFormData' ], $date_form_data );
    	foreach($date_form_data as $key => $value) {
		    if (strpos($key, 'coderockz_woo_delivery_pickup_opendays_year_') === 0) {
		        array_push($year_array,sanitize_text_field($value));
		    }
		}
		foreach($year_array as $year) {
			$opendays_months = $this->helper->coderockz_woo_delivery_array_sanitize($date_form_data["coderockz_woo_delivery_pickup_opendays_month_".$year]);
			if(!empty($opendays_months)){
				foreach($opendays_months as $opendays_month) {
					if($opendays_month != "") {
						$opendays_days = sanitize_text_field($date_form_data["coderockz_woo_delivery_pickup_opendays_dates_".$opendays_month."_".$year]);
						if(isset($opendays_days) && $opendays_days != "") {
							$formated_opendays = [];
							$opendays_days = explode(',', $opendays_days);
							foreach($opendays_days as $opendays_day) {
								$formated_opendays[] = sprintf("%02d", $opendays_day);
							}
							$formated_opendays = implode(',', $formated_opendays);
							$opendays_array[$year][$opendays_month] = $formated_opendays;
						}	
					}
				}
			}
			
		}
		$overall_off_before = isset($date_form_data['coderockz_woo_delivery_overall_off_before_pickup']) && $date_form_data['coderockz_woo_delivery_overall_off_before_pickup'] != "" ? $date_form_data['coderockz_woo_delivery_overall_off_before_pickup'] : "";
		$date_form_settings['overall_off_before'] = $overall_off_before;
		$date_form_settings['open_days'] = $opendays_array;
		if(get_option('coderockz_woo_delivery_pickup_date_settings') == false) {
			update_option('coderockz_woo_delivery_pickup_date_settings', $date_form_settings);
		} else {
			$date_form_settings = array_merge(get_option('coderockz_woo_delivery_pickup_date_settings'),$date_form_settings);
			update_option('coderockz_woo_delivery_pickup_date_settings', $date_form_settings);
		}
		wp_send_json_success();
		
    }

    public function coderockz_woo_delivery_process_delivery_date_offdays_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
    	$year_array = [];
    	$offdays_array = [];
    	parse_str( $_POST[ 'dateFormData' ], $date_form_data );
    	foreach($date_form_data as $key => $value) {
		    if (strpos($key, 'coderockz_woo_delivery_offdays_year_') === 0) {
		        array_push($year_array,sanitize_text_field($value));
		    }
		}
		foreach($year_array as $year) {
			$offdays_months = $this->helper->coderockz_woo_delivery_array_sanitize($date_form_data["coderockz_woo_delivery_offdays_month_".$year]);
			if(!empty($offdays_months)){
				foreach($offdays_months as $offdays_month) {
					if($offdays_month != "") {
						$offdays_days = sanitize_text_field($date_form_data["coderockz_woo_delivery_offdays_dates_".$offdays_month."_".$year]);
						if(isset($offdays_days) && $offdays_days != "") {
							$formated_offdays = [];
							$offdays_days = explode(',', $offdays_days);
							foreach($offdays_days as $offdays_day) {
								$formated_offdays[] = sprintf("%02d", $offdays_day);
							}
							$formated_offdays = implode(',', $formated_offdays);
							$offdays_array[$year][$offdays_month] = $formated_offdays;
						}	
					}
				}
			}
			
		}
		$date_form_settings['off_days'] = $offdays_array;
		if(get_option('coderockz_woo_delivery_date_settings') == false) {
			update_option('coderockz_woo_delivery_date_settings', $date_form_settings);
		} else {
			$date_form_settings = array_merge(get_option('coderockz_woo_delivery_date_settings'),$date_form_settings);
			update_option('coderockz_woo_delivery_date_settings', $date_form_settings);
		}
		wp_send_json_success();
		
    }

    public function coderockz_woo_delivery_process_pickup_date_offdays_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
    	$year_array = [];
    	$offdays_array = [];
    	parse_str( $_POST[ 'dateFormData' ], $date_form_data );
    	foreach($date_form_data as $key => $value) {
		    if (strpos($key, 'coderockz_woo_delivery_pickup_offdays_year_') === 0) {
		        array_push($year_array,sanitize_text_field($value));
		    }
		}
		foreach($year_array as $year) {
			$offdays_months = $this->helper->coderockz_woo_delivery_array_sanitize($date_form_data["coderockz_woo_delivery_pickup_offdays_month_".$year]);
			if(!empty($offdays_months)){
				foreach($offdays_months as $offdays_month) {
					if($offdays_month != "") {
						$offdays_days = sanitize_text_field($date_form_data["coderockz_woo_delivery_pickup_offdays_dates_".$offdays_month."_".$year]);
						if(isset($offdays_days) && $offdays_days != "") {
							$formated_offdays = [];
							$offdays_days = explode(',', $offdays_days);
							foreach($offdays_days as $offdays_day) {
								$formated_offdays[] = sprintf("%02d", $offdays_day);
							}
							$formated_offdays = implode(',', $formated_offdays);
							$offdays_array[$year][$offdays_month] = $formated_offdays;
						}	
					}
				}
			}
			
		}
		$date_form_settings['pickup_off_days'] = $offdays_array;
		if(get_option('coderockz_woo_delivery_pickup_date_settings') == false) {
			update_option('coderockz_woo_delivery_pickup_date_settings', $date_form_settings);
		} else {
			$date_form_settings = array_merge(get_option('coderockz_woo_delivery_pickup_date_settings'),$date_form_settings);
			update_option('coderockz_woo_delivery_pickup_date_settings', $date_form_settings);
		}
		wp_send_json_success();
		
    }

    public function coderockz_woo_delivery_category_wise_offdays_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $category_offdays_form_data );
		$category_offdays['both'] = [];
		$category_offdays['delivery'] = [];
		$category_offdays['pickup'] = [];
		$category_offdays_categories = $this->helper->coderockz_woo_delivery_array_sanitize($category_offdays_form_data['coderockz_delivery_category_wise_offdays_category']);

		foreach($category_offdays_categories as $category_offdays_category) {
			$temporary_category_offdays = [];
			$category_code = str_replace("c-w-d"," ", $category_offdays_category);

			if(!isset($both_completed[$category_offdays_category]) && (isset($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-weekday-'.$category_offdays_category.'-both']) || isset($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-specific-date-'.$category_offdays_category.'-both'])) && $category_code != "") {
				
				if(isset($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-weekday-'.$category_offdays_category.'-both']) && !empty($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-weekday-'.$category_offdays_category.'-both'])) {
					$weekday_array = $this->helper->coderockz_woo_delivery_array_sanitize($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-weekday-'.$category_offdays_category.'-both']);
				} else {
					$weekday_array = [];
				}
				
				if(isset($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-specific-date-'.$category_offdays_category.'-both']) && $category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-specific-date-'.$category_offdays_category.'-both'] != "") {
					$specific_date_string = sanitize_text_field($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-specific-date-'.$category_offdays_category.'-both']);			
					if(strpos($specific_date_string, '...') !== false) {

						$temporary_dates = explode(',', str_replace(' ', '', $specific_date_string));
						$specific_date_array = [];
						foreach($temporary_dates as $temporary_date) {
							if(strpos($temporary_date, '...') !== false) {
								$filtered_dates = explode('...', $temporary_date);
							    $period = $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1]);
								$specific_date_array = array_merge($specific_date_array,$period);
							} else {
								$specific_date_array[] = $temporary_date;
							}
						}
						
					    $temporary_category_offdays['specific_date_offdays_string'] = str_replace(' ', '', $specific_date_string);
					    $temporary_category_offdays['specific_date_offdays'] = $specific_date_array;

					} else {
						$temporary_category_offdays['specific_date_offdays_string'] = str_replace(' ', '', $specific_date_string);
						$specific_date_array = explode(',', str_replace(' ', '', $specific_date_string));
						$temporary_category_offdays['specific_date_offdays'] = $specific_date_array;
					}

				} else {
					$temporary_category_offdays['specific_date_offdays_string'] = "";
					$temporary_category_offdays['specific_date_offdays'] = [];
				}
				
				$temporary_category_offdays['weekday_offdays'] = $weekday_array;				

				$category_offdays['both'][$category_code] = $temporary_category_offdays;

				$both_completed[$category_offdays_category] = 'completed';
				
			}

			if(!isset($delivery_completed[$category_offdays_category]) && (isset($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-weekday-'.$category_offdays_category.'-delivery']) || isset($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-specific-date-'.$category_offdays_category.'-delivery'])) && $category_code != "") {

				if(isset($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-weekday-'.$category_offdays_category.'-delivery']) && !empty($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-weekday-'.$category_offdays_category.'-delivery'])) {
					$weekday_array = $this->helper->coderockz_woo_delivery_array_sanitize($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-weekday-'.$category_offdays_category.'-delivery']);
				} else {
					$weekday_array = [];
				}
				
				if(isset($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-specific-date-'.$category_offdays_category.'-delivery']) && $category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-specific-date-'.$category_offdays_category.'-delivery'] != "") {
					$specific_date_string = sanitize_text_field($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-specific-date-'.$category_offdays_category.'-delivery']);
					if(strpos($specific_date_string, '...') !== false) {

						$temporary_dates = explode(',', str_replace(' ', '', $specific_date_string));
						$specific_date_array = [];
						foreach($temporary_dates as $temporary_date) {
							if(strpos($temporary_date, '...') !== false) {
								$filtered_dates = explode('...', $temporary_date);
							    $period = $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1]);
								$specific_date_array = array_merge($specific_date_array,$period);
							} else {
								$specific_date_array[] = $temporary_date;
							}
						}
						
					    $temporary_category_offdays['specific_date_offdays_string'] = str_replace(' ', '', $specific_date_string);
					    $temporary_category_offdays['specific_date_offdays'] = $specific_date_array;

					} else {
						$temporary_category_offdays['specific_date_offdays_string'] = str_replace(' ', '', $specific_date_string);
						$specific_date_array = explode(',', str_replace(' ', '', $specific_date_string));
						$temporary_category_offdays['specific_date_offdays'] = $specific_date_array;
					}
				} else {
					$temporary_category_offdays['specific_date_offdays_string'] = "";
					$temporary_category_offdays['specific_date_offdays'] = [];
				}

				$temporary_category_offdays['weekday_offdays'] = $weekday_array;

				$category_offdays['delivery'][$category_code] = $temporary_category_offdays;

				$delivery_completed[$category_offdays_category] = 'completed';

			}

			if(!isset($pickup_completed[$category_offdays_category]) && (isset($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-weekday-'.$category_offdays_category.'-pickup']) || isset($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-specific-date-'.$category_offdays_category.'-pickup'])) && $category_code != "") {

				if(isset($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-weekday-'.$category_offdays_category.'-pickup']) && !empty($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-weekday-'.$category_offdays_category.'-pickup'])) {
					$weekday_array = $this->helper->coderockz_woo_delivery_array_sanitize($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-weekday-'.$category_offdays_category.'-pickup']);
				} else {
					$weekday_array = [];
				}
				
				if(isset($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-specific-date-'.$category_offdays_category.'-pickup']) && $category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-specific-date-'.$category_offdays_category.'-pickup'] != "") {
					$specific_date_string = sanitize_text_field($category_offdays_form_data['coderockz-delivery-category-wise-offdays-category-specific-date-'.$category_offdays_category.'-pickup']);
					if(strpos($specific_date_string, '...') !== false) {

						$temporary_dates = explode(',', str_replace(' ', '', $specific_date_string));
						$specific_date_array = [];
						foreach($temporary_dates as $temporary_date) {
							if(strpos($temporary_date, '...') !== false) {
								$filtered_dates = explode('...', $temporary_date);
							    $period = $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1]);
								$specific_date_array = array_merge($specific_date_array,$period);
							} else {
								$specific_date_array[] = $temporary_date;
							}
						}
						
					    $temporary_category_offdays['specific_date_offdays_string'] = str_replace(' ', '', $specific_date_string);
					    $temporary_category_offdays['specific_date_offdays'] = $specific_date_array;

					} else {
						$temporary_category_offdays['specific_date_offdays_string'] = str_replace(' ', '', $specific_date_string);
						$specific_date_array = explode(',', str_replace(' ', '', $specific_date_string));
						$temporary_category_offdays['specific_date_offdays'] = $specific_date_array;
					}
				} else {
					$temporary_category_offdays['specific_date_offdays_string'] = "";
					$temporary_category_offdays['specific_date_offdays'] = [];
				}
				
				$temporary_category_offdays['weekday_offdays'] = $weekday_array;

				$category_offdays['pickup'][$category_code] = $temporary_category_offdays;

				$pickup_completed[$category_offdays_category] = 'completed';
			}

		}

		$category_offdays_form_settings['category_wise_offdays'] = $category_offdays;

		if(get_option('coderockz_woo_delivery_off_days_settings') == false) {
			update_option('coderockz_woo_delivery_off_days_settings', $category_offdays_form_settings);
		} else {
			$category_offdays_form_settings = array_merge(get_option('coderockz_woo_delivery_off_days_settings'),$category_offdays_form_settings);
			update_option('coderockz_woo_delivery_off_days_settings', $category_offdays_form_settings);
		}

		wp_send_json_success($category_offdays);

	}

	public function coderockz_woo_delivery_product_wise_offdays_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $product_offdays_form_data );
		$product_offdays = [];
		if(get_option('coderockz_woo_delivery_large_product_list') == false) {
			$product_offdays_products = $this->helper->coderockz_woo_delivery_array_sanitize($product_offdays_form_data['coderockz_delivery_product_wise_offdays_product']);
		} else {

			$product_offdays_products = $this->helper->coderockz_woo_delivery_array_sanitize($_POST['offdaysProduct']);
		}
		
		foreach($product_offdays_products as $product_offdays_product) {
				
			if(!empty($product_offdays_form_data['coderockz-delivery-product-wise-offdays-product-weekday-'.$product_offdays_product.'']) && $product_offdays_product != "") {

				$product_offdays[$product_offdays_product]['weekday_offdays'] = $this->helper->coderockz_woo_delivery_array_sanitize($product_offdays_form_data['coderockz-delivery-product-wise-offdays-product-weekday-'.$product_offdays_product.'']);
			} else {
				$product_offdays[$product_offdays_product]['weekday_offdays'] = [];
			}

			if($product_offdays_form_data['coderockz-delivery-product-wise-offdays-product-specific-date-'.$product_offdays_product.''] != "" && $product_offdays_product != "") {

				$offdays_product_specific_date = sanitize_text_field($product_offdays_form_data['coderockz-delivery-product-wise-offdays-product-specific-date-'.$product_offdays_product.'']);

				if(strpos($offdays_product_specific_date, '...') !== false) {

					$temporary_dates = explode(',', str_replace(' ', '', $offdays_product_specific_date));
					$specific_date_array = [];
					foreach($temporary_dates as $temporary_date) {
						if(strpos($temporary_date, '...') !== false) {
							$filtered_dates = explode('...', $temporary_date);
						    $period = $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1]);
							$specific_date_array = array_merge($specific_date_array,$period);
						} else {
							$specific_date_array[] = $temporary_date;
						}
					}
					
				    $product_offdays[$product_offdays_product]['specific_date_offdays_string'] = str_replace(' ', '', $offdays_product_specific_date);
				    $product_offdays[$product_offdays_product]['specific_date_offdays'] = $specific_date_array;

				} else {
					$product_offdays[$product_offdays_product]['specific_date_offdays_string'] = str_replace(' ', '', $offdays_product_specific_date);
					$specific_date_array = explode(',', str_replace(' ', '', $offdays_product_specific_date));
					$product_offdays[$product_offdays_product]['specific_date_offdays'] = $specific_date_array;
				}

			} else {
				$product_offdays[$product_offdays_product]['specific_date_offdays_string'] = "";
				$product_offdays[$product_offdays_product]['specific_date_offdays'] = [];
			}
		}

		$product_offdays_form_settings['product_wise_offdays'] = $product_offdays;

		if(get_option('coderockz_woo_delivery_off_days_settings') == false) {
			update_option('coderockz_woo_delivery_off_days_settings', $product_offdays_form_settings);
		} else {
			$product_offdays_form_settings = array_merge(get_option('coderockz_woo_delivery_off_days_settings'),$product_offdays_form_settings);
			update_option('coderockz_woo_delivery_off_days_settings', $product_offdays_form_settings);
		}

		wp_send_json_success();
	}

    public function coderockz_woo_delivery_zone_wise_offdays_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $zone_offdays_form_data );
		$zone_offdays['both'] = [];
		$zone_offdays['delivery'] = [];
		$zone_offdays['pickup'] = [];
		$zone_offdays_zones = $this->helper->coderockz_woo_delivery_array_sanitize($zone_offdays_form_data['coderockz_delivery_zone_wise_offdays_zone']);

		foreach($zone_offdays_zones as $zone_offdays_zone) {
			$temporary_zone_offdays = [];
			$zone_code = $zone_offdays_zone;
			if(!isset($both_completed[$zone_offdays_zone]) && ((isset($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-weekday-'.$zone_offdays_zone.'-both']) && !empty($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-weekday-'.$zone_offdays_zone.'-both'])) || (isset($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-specific-date-'.$zone_offdays_zone.'-both']) && !empty($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-specific-date-'.$zone_offdays_zone.'-both'])))) {
				
				if(isset($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-weekday-'.$zone_offdays_zone.'-both']) && !empty($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-weekday-'.$zone_offdays_zone.'-both'])) {
					$weekday_array = $this->helper->coderockz_woo_delivery_array_sanitize($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-weekday-'.$zone_offdays_zone.'-both']);
				} else {
					$weekday_array = [];
				}
				
				if(isset($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-specific-date-'.$zone_offdays_zone.'-both']) && $zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-specific-date-'.$zone_offdays_zone.'-both'] != "") {
					$specific_date_string = sanitize_text_field($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-specific-date-'.$zone_offdays_zone.'-both']);

					if(strpos($specific_date_string, '...') !== false) {

						$temporary_dates = explode(',', str_replace(' ', '', $specific_date_string));
						$specific_date_array = [];
						foreach($temporary_dates as $temporary_date) {
							if(strpos($temporary_date, '...') !== false) {
								$filtered_dates = explode('...', $temporary_date);
							    $period = $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1]);
								$specific_date_array = array_merge($specific_date_array,$period);
							} else {
								$specific_date_array[] = $temporary_date;
							}
						}
						
					    $temporary_zone_offdays['specific_date_offdays_string'] = str_replace(' ', '', $specific_date_string);
					    $temporary_zone_offdays['specific_date_offdays'] = $specific_date_array;

					} else {
						$temporary_zone_offdays['specific_date_offdays_string'] = str_replace(' ', '', $specific_date_string);
						$specific_date_array = explode(',', str_replace(' ', '', $specific_date_string));
						$temporary_zone_offdays['specific_date_offdays'] = $specific_date_array;
					}

				} else {
					$temporary_zone_offdays['specific_date_offdays_string'] = "";
					$temporary_zone_offdays['specific_date_offdays'] = [];
				}

				if((!empty($weekday_array) || $specific_date_string != "") && $zone_code != "") {
					if(!empty($weekday_array)){
						$weekday_string = implode(",",$weekday_array);
					} else {
						$weekday_string = "";
					}
					$temporary_zone_offdays['off_days'] = $weekday_string;
					$zone_offdays['both'][$zone_code] = $temporary_zone_offdays;
					$both_completed[$zone_offdays_zone] = 'completed';
				}
			}

			if(!isset($delivery_completed[$zone_offdays_zone]) && ((isset($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-weekday-'.$zone_offdays_zone.'-delivery']) && !empty($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-weekday-'.$zone_offdays_zone.'-delivery'])) || (isset($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-specific-date-'.$zone_offdays_zone.'-delivery']) && !empty($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-specific-date-'.$zone_offdays_zone.'-delivery'])))) {

				if(isset($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-weekday-'.$zone_offdays_zone.'-delivery']) && !empty($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-weekday-'.$zone_offdays_zone.'-delivery'])) {
					$weekday_array = $this->helper->coderockz_woo_delivery_array_sanitize($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-weekday-'.$zone_offdays_zone.'-delivery']);
				} else {
					$weekday_array = [];
				}
				
				if(isset($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-specific-date-'.$zone_offdays_zone.'-delivery']) && !empty($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-specific-date-'.$zone_offdays_zone.'-delivery'])) {
					$specific_date_string = sanitize_text_field($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-specific-date-'.$zone_offdays_zone.'-delivery']);
					if(strpos($specific_date_string, '...') !== false) {

						$temporary_dates = explode(',', str_replace(' ', '', $specific_date_string));
						$specific_date_array = [];
						foreach($temporary_dates as $temporary_date) {
							if(strpos($temporary_date, '...') !== false) {
								$filtered_dates = explode('...', $temporary_date);
							    $period = $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1]);
								$specific_date_array = array_merge($specific_date_array,$period);
							} else {
								$specific_date_array[] = $temporary_date;
							}
						}
						
					    $temporary_zone_offdays['specific_date_offdays_string'] = str_replace(' ', '', $specific_date_string);
					    $temporary_zone_offdays['specific_date_offdays'] = $specific_date_array;

					} else {
						$temporary_zone_offdays['specific_date_offdays_string'] = str_replace(' ', '', $specific_date_string);
						$specific_date_array = explode(',', str_replace(' ', '', $specific_date_string));
						$temporary_zone_offdays['specific_date_offdays'] = $specific_date_array;
					}
				} else {
					$temporary_zone_offdays['specific_date_offdays_string'] = "";
					$temporary_zone_offdays['specific_date_offdays'] = [];
				}

				if((!empty($weekday_array) || $specific_date_string != "") && $zone_code != "") {
					if(!empty($weekday_array)){
						$weekday_string = implode(",",$weekday_array);
					} else {
						$weekday_string = "";
					}
					$temporary_zone_offdays['off_days'] = $weekday_string;
					$zone_offdays['delivery'][$zone_code] = $temporary_zone_offdays;
					$both_completed[$zone_offdays_zone] = 'completed';
				}
			}

			if(!isset($pickup_completed[$zone_offdays_zone]) && ((isset($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-weekday-'.$zone_offdays_zone.'-pickup']) && !empty($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-weekday-'.$zone_offdays_zone.'-pickup'])) || (isset($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-specific-date-'.$zone_offdays_zone.'-pickup']) && !empty($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-specific-date-'.$zone_offdays_zone.'-pickup'])))) {
				
				if(isset($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-weekday-'.$zone_offdays_zone.'-pickup']) && !empty($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-weekday-'.$zone_offdays_zone.'-pickup'])) {
					$weekday_array = $this->helper->coderockz_woo_delivery_array_sanitize($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-weekday-'.$zone_offdays_zone.'-pickup']);
				} else {
					$weekday_array = [];
				}
				
				if(isset($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-specific-date-'.$zone_offdays_zone.'-pickup']) && !empty($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-specific-date-'.$zone_offdays_zone.'-pickup'])) {
					$specific_date_string = sanitize_text_field($zone_offdays_form_data['coderockz-delivery-zone-wise-offdays-zone-specific-date-'.$zone_offdays_zone.'-pickup']);
					if(strpos($specific_date_string, '...') !== false) {

						$temporary_dates = explode(',', str_replace(' ', '', $specific_date_string));
						$specific_date_array = [];
						foreach($temporary_dates as $temporary_date) {
							if(strpos($temporary_date, '...') !== false) {
								$filtered_dates = explode('...', $temporary_date);
							    $period = $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1]);
								$specific_date_array = array_merge($specific_date_array,$period);
							} else {
								$specific_date_array[] = $temporary_date;
							}
						}
						
					    $temporary_zone_offdays['specific_date_offdays_string'] = str_replace(' ', '', $specific_date_string);
					    $temporary_zone_offdays['specific_date_offdays'] = $specific_date_array;

					} else {
						$temporary_zone_offdays['specific_date_offdays_string'] = str_replace(' ', '', $specific_date_string);
						$specific_date_array = explode(',', str_replace(' ', '', $specific_date_string));
						$temporary_zone_offdays['specific_date_offdays'] = $specific_date_array;
					}
				} else {
					$temporary_zone_offdays['specific_date_offdays_string'] = "";
					$temporary_zone_offdays['specific_date_offdays'] = [];
				}

				if((!empty($weekday_array) || $specific_date_string != "") && $zone_code != "") {
					if(!empty($weekday_array)){
						$weekday_string = implode(",",$weekday_array);
					} else {
						$weekday_string = "";
					}
					$temporary_zone_offdays['off_days'] = $weekday_string;
					$zone_offdays['pickup'][$zone_code] = $temporary_zone_offdays;
					$both_completed[$zone_offdays_zone] = 'completed';
				}
			}

		}

		$zone_offdays_form_settings['zone_wise_offdays'] = $zone_offdays;

		if(get_option('coderockz_woo_delivery_off_days_settings') == false) {
			update_option('coderockz_woo_delivery_off_days_settings', $zone_offdays_form_settings);
		} else {
			$zone_offdays_form_settings = array_merge(get_option('coderockz_woo_delivery_off_days_settings'),$zone_offdays_form_settings);
			update_option('coderockz_woo_delivery_off_days_settings', $zone_offdays_form_settings);
		}

		wp_send_json_success($zone_offdays);
	}

    public function coderockz_woo_delivery_state_wise_offdays_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $state_offdays_form_data );
		$state_offdays = [];
		$state_offdays_states = $this->helper->coderockz_woo_delivery_array_sanitize($state_offdays_form_data['coderockz_delivery_state_wise_offdays_state']);
		foreach($state_offdays_states as $state_offdays_state) {
			$state = str_replace("c-w-d"," ", $state_offdays_state);
			if(!empty($state_offdays_form_data['coderockz-delivery-state-wise-offdays-state-weekday-'.$state_offdays_state.'']) && $state != "") {
				$state_offdays[$state] = $this->helper->coderockz_woo_delivery_array_sanitize($state_offdays_form_data['coderockz-delivery-state-wise-offdays-state-weekday-'.$state_offdays_state.'']);
			}
			
		}

		$state_offdays_form_settings['state_wise_offdays'] = $state_offdays;

		if(get_option('coderockz_woo_delivery_off_days_settings') == false) {
			update_option('coderockz_woo_delivery_off_days_settings', $state_offdays_form_settings);
		} else {
			$state_offdays_form_settings = array_merge(get_option('coderockz_woo_delivery_off_days_settings'),$state_offdays_form_settings);
			update_option('coderockz_woo_delivery_off_days_settings', $state_offdays_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_postcode_wise_offdays_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $postcode_offdays_form_data );
		$postcode_offdays = [];
		$postcode_offdays_postcodes = $this->helper->coderockz_woo_delivery_array_sanitize($postcode_offdays_form_data['coderockz_delivery_postcode_wise_offdays_postcode']);
		foreach($postcode_offdays_postcodes as $postcode_offdays_postcode) {
			$postcode = str_replace(array("--","___"),array(" ","..."), $postcode_offdays_postcode);
			if(!empty($postcode_offdays_form_data['coderockz-delivery-postcode-wise-offdays-postcode-weekday-'.$postcode_offdays_postcode.'']) && $postcode != "") {
				$postcode_offdays[$postcode] = $this->helper->coderockz_woo_delivery_array_sanitize($postcode_offdays_form_data['coderockz-delivery-postcode-wise-offdays-postcode-weekday-'.$postcode_offdays_postcode.'']);
			}
			
		}

		$postcode_offdays_form_settings['postcode_wise_offdays'] = $postcode_offdays;

		if(get_option('coderockz_woo_delivery_off_days_settings') == false) {
			update_option('coderockz_woo_delivery_off_days_settings', $postcode_offdays_form_settings);
		} else {
			$postcode_offdays_form_settings = array_merge(get_option('coderockz_woo_delivery_off_days_settings'),$postcode_offdays_form_settings);
			update_option('coderockz_woo_delivery_off_days_settings', $postcode_offdays_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_shippingmethod_wise_offdays_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $shippingmethod_offdays_form_data );

		$shippingmethod_offdays['delivery'] = [];
		$shippingmethod_offdays['pickup'] = [];
		$shippingmethod_offdays_methods = $this->helper->coderockz_woo_delivery_array_sanitize($shippingmethod_offdays_form_data['coderockz_delivery_shippingmethod_wise_offdays_shippingmethod']);

		foreach($shippingmethod_offdays_methods as $shippingmethod_offdays_method) {

			$method_code = $shippingmethod_offdays_method;

			if(!isset($delivery_completed[$shippingmethod_offdays_method]) && isset($shippingmethod_offdays_form_data['coderockz-delivery-shippingmethod-wise-offdays-shippingmethod-weekday-'.$shippingmethod_offdays_method.'-delivery'])) {

				$weekday_array_delivery = $this->helper->coderockz_woo_delivery_array_sanitize($shippingmethod_offdays_form_data['coderockz-delivery-shippingmethod-wise-offdays-shippingmethod-weekday-'.$shippingmethod_offdays_method.'-delivery']);

				if(!empty($weekday_array_delivery) && $method_code != "") {

					$shippingmethod_offdays['delivery'][$method_code] = $weekday_array_delivery;

					$delivery_completed[$shippingmethod_offdays_method] = 'completed';

				}
			}

			if(!isset($pickup_completed[$shippingmethod_offdays_method]) && isset($shippingmethod_offdays_form_data['coderockz-delivery-shippingmethod-wise-offdays-shippingmethod-weekday-'.$shippingmethod_offdays_method.'-pickup'])) {
				$weekday_array_pickup = $this->helper->coderockz_woo_delivery_array_sanitize($shippingmethod_offdays_form_data['coderockz-delivery-shippingmethod-wise-offdays-shippingmethod-weekday-'.$shippingmethod_offdays_method.'-pickup']);

				if(!empty($weekday_array_pickup) && $method_code != "") {

					$shippingmethod_offdays['pickup'][$method_code] = $weekday_array_pickup;

					$pickup_completed[$shippingmethod_offdays_method] = 'completed';

				}
			}

		}


		$shippingmethod_offdays_form_settings['shippingmethod_wise_offdays'] = $shippingmethod_offdays;

		if(get_option('coderockz_woo_delivery_off_days_settings') == false) {
			update_option('coderockz_woo_delivery_off_days_settings', $shippingmethod_offdays_form_settings);
		} else {
			$shippingmethod_offdays_form_settings = array_merge(get_option('coderockz_woo_delivery_off_days_settings'),$shippingmethod_offdays_form_settings);
			update_option('coderockz_woo_delivery_off_days_settings', $shippingmethod_offdays_form_settings);
		}

		wp_send_json_success();
	}

    public function coderockz_woo_delivery_process_store_closing_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'formData' ], $closing_time_form_data );
		$time_form_settings = [];
		$enable_closing_time = !isset($closing_time_form_data['coderockz_woo_delivery_enable_closing_time']) ? false : true;

		$store_closing_time = "";

		if(isset($closing_time_form_data['coderockz_woo_delivery_closing_time_hour']) && $closing_time_form_data['coderockz_woo_delivery_closing_time_hour'] !="") {
			$store_closing_hour = (isset($closing_time_form_data['coderockz_woo_delivery_closing_time_hour']) && $closing_time_form_data['coderockz_woo_delivery_closing_time_hour'] !="") ? sanitize_text_field($closing_time_form_data['coderockz_woo_delivery_closing_time_hour']) : "0";
			
			$store_closing_min = (isset($closing_time_form_data['coderockz_woo_delivery_closing_time_min']) && $closing_time_form_data['coderockz_woo_delivery_closing_time_min'] !="") ? sanitize_text_field($closing_time_form_data['coderockz_woo_delivery_closing_time_min']) : "0"; 

			$store_closing_format = sanitize_text_field($closing_time_form_data['coderockz_woo_delivery_closing_time_format']);
			if($store_closing_format == "am") {
				$store_closing_hour_12 = ($store_closing_hour == "12") ? "0" : $store_closing_hour;
				$store_closing_time = ((int)$store_closing_hour_12 * 60) + (int)$store_closing_min;
			} else {
				$store_closing_hour = ($store_closing_hour == "12") ? "0" : $store_closing_hour;
				$store_closing_time = (((int)$store_closing_hour + 12)*60) + (int)$store_closing_min;
			}

			if($store_closing_format == "am" && $store_closing_hour == "12" && ($store_closing_min =="0" || $store_closing_min =="00" || $store_closing_min_[$key] =="")) {
				$store_closing_time = 0;
			}

		}

		$extended_closing_days = (isset($closing_time_form_data['coderockz_woo_delivery_extend_closing_time']) && $closing_time_form_data['coderockz_woo_delivery_extend_closing_time'] !="") ? sanitize_text_field($closing_time_form_data['coderockz_woo_delivery_extend_closing_time']) : "0";

		$extended_closing_time = (isset($closing_time_form_data['coderockz_woo_delivery_extend_closing_time_hour']) && $closing_time_form_data['coderockz_woo_delivery_extend_closing_time_hour'] !="") ? sanitize_text_field($closing_time_form_data['coderockz_woo_delivery_extend_closing_time_hour']) : "0";

		$time_form_settings['enable_closing_time'] = $enable_closing_time;
		$time_form_settings['store_closing_time'] = (string)$store_closing_time;
		$time_form_settings['extended_closing_days'] = $extended_closing_days;
		$time_form_settings['extended_closing_time'] = $extended_closing_time;


		if(get_option('coderockz_woo_delivery_time_settings') == false) {
			update_option('coderockz_woo_delivery_time_settings', $time_form_settings);
		} else {
			$time_form_settings = array_merge(get_option('coderockz_woo_delivery_time_settings'),$time_form_settings);
			update_option('coderockz_woo_delivery_time_settings', $time_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_process_store_closing_pickup() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'formData' ], $closing_time_form_data );
		$time_form_settings = [];
		$enable_closing_time = !isset($closing_time_form_data['coderockz_woo_delivery_enable_closing_time_pickup']) ? false : true;

		$store_closing_time = "";

		if(isset($closing_time_form_data['coderockz_woo_delivery_closing_time_hour_pickup']) && $closing_time_form_data['coderockz_woo_delivery_closing_time_hour_pickup'] !="") {
			$store_closing_hour = (isset($closing_time_form_data['coderockz_woo_delivery_closing_time_hour_pickup']) && $closing_time_form_data['coderockz_woo_delivery_closing_time_hour_pickup'] !="") ? sanitize_text_field($closing_time_form_data['coderockz_woo_delivery_closing_time_hour_pickup']) : "0";
			
			$store_closing_min = (isset($closing_time_form_data['coderockz_woo_delivery_closing_time_min_pickup']) && $closing_time_form_data['coderockz_woo_delivery_closing_time_min_pickup'] !="") ? sanitize_text_field($closing_time_form_data['coderockz_woo_delivery_closing_time_min_pickup']) : "0"; 

			$store_closing_format = sanitize_text_field($closing_time_form_data['coderockz_woo_delivery_closing_time_format_pickup']);
			if($store_closing_format == "am") {
				$store_closing_hour_12 = ($store_closing_hour == "12") ? "0" : $store_closing_hour;
				$store_closing_time = ((int)$store_closing_hour_12 * 60) + (int)$store_closing_min;
			} else {
				$store_closing_hour = ($store_closing_hour == "12") ? "0" : $store_closing_hour;
				$store_closing_time = (((int)$store_closing_hour + 12)*60) + (int)$store_closing_min;
			}

			if($store_closing_format == "am" && $store_closing_hour == "12" && ($store_closing_min =="0" || $store_closing_min =="00" || $store_closing_min_[$key] =="")) {
				$store_closing_time = 0;
			}

		}

		$extended_closing_days = (isset($closing_time_form_data['coderockz_woo_delivery_extend_closing_time_pickup']) && $closing_time_form_data['coderockz_woo_delivery_extend_closing_time_pickup'] !="") ? sanitize_text_field($closing_time_form_data['coderockz_woo_delivery_extend_closing_time_pickup']) : "0";
		
		$extended_closing_time = (isset($closing_time_form_data['coderockz_woo_delivery_extend_closing_time_hour_pickup']) && $closing_time_form_data['coderockz_woo_delivery_extend_closing_time_hour_pickup'] !="") ? sanitize_text_field($closing_time_form_data['coderockz_woo_delivery_extend_closing_time_hour_pickup']) : "0";

		$time_form_settings['enable_closing_time'] = $enable_closing_time;
		$time_form_settings['store_closing_time'] = (string)$store_closing_time;
		$time_form_settings['extended_closing_days'] = $extended_closing_days;
		$time_form_settings['extended_closing_time'] = $extended_closing_time;


		if(get_option('coderockz_woo_delivery_pickup_time_settings') == false) {
			update_option('coderockz_woo_delivery_pickup_time_settings', $time_form_settings);
		} else {
			$time_form_settings = array_merge(get_option('coderockz_woo_delivery_pickup_time_settings'),$time_form_settings);
			update_option('coderockz_woo_delivery_pickup_time_settings', $time_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_process_different_store_closing_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'formData' ], $closing_time_form_data );
		$time_form_settings = [];

		$enable_different_closing_time = !isset($closing_time_form_data['coderockz_woo_delivery_enable_different_closing_time']) ? false : true;
		$different_store_closing_time = [];
		$different_extended_closing_day = [];
		$different_extended_closing_time = [];

		$weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
        foreach ($weekday as $key => $value) {
       	
        	if(isset($closing_time_form_data['coderockz_woo_delivery_extend_closing_time_'.$key]) && $closing_time_form_data['coderockz_woo_delivery_extend_closing_time_'.$key] !="") {
        		$different_extended_closing_day[$key] = sanitize_text_field($closing_time_form_data['coderockz_woo_delivery_extend_closing_time_'.$key]);
        	}

        	if(isset($closing_time_form_data['coderockz_woo_delivery_extend_closing_time_hour_'.$key]) && $closing_time_form_data['coderockz_woo_delivery_extend_closing_time_hour_'.$key] !="") {
        		$different_extended_closing_time[$key] = sanitize_text_field($closing_time_form_data['coderockz_woo_delivery_extend_closing_time_hour_'.$key]);
        	}

        	if(isset($closing_time_form_data['coderockz_woo_delivery_closing_time_hour_'.$key]) && $closing_time_form_data['coderockz_woo_delivery_closing_time_hour_'.$key] !="") {
	        	$store_closing_hour_[$key] = (isset($closing_time_form_data['coderockz_woo_delivery_closing_time_hour_'.$key]) && $closing_time_form_data['coderockz_woo_delivery_closing_time_hour_'.$key] !="") ? sanitize_text_field($closing_time_form_data['coderockz_woo_delivery_closing_time_hour_'.$key]) : "0";
		
				$store_closing_min_[$key] = (isset($closing_time_form_data['coderockz_woo_delivery_closing_time_min_'.$key]) && $closing_time_form_data['coderockz_woo_delivery_closing_time_min_'.$key] !="") ? sanitize_text_field($closing_time_form_data['coderockz_woo_delivery_closing_time_min_'.$key]) : "0"; 

				$store_closing_format_[$key] = sanitize_text_field($closing_time_form_data['coderockz_woo_delivery_closing_time_format_'.$key]);
				if($store_closing_format_[$key] == "am") {
					$store_closing_hour_12_[$key] = ($store_closing_hour_[$key] == "12") ? "0" : $store_closing_hour_[$key];
					$store_closing_time_[$key] = ((int)$store_closing_hour_12_[$key] * 60) + (int)$store_closing_min_[$key];
				} else {
					$store_closing_hour_[$key] = ($store_closing_hour_[$key] == "12") ? "0" : $store_closing_hour_[$key];
					$store_closing_time_[$key] = (((int)$store_closing_hour_[$key] + 12)*60) + (int)$store_closing_min_[$key];
				}

				if($store_closing_format_[$key] == "am" && $store_closing_hour_[$key] == "12" && ($store_closing_min_[$key] =="0"||$store_closing_min_[$key] =="00" || $store_closing_min_[$key] =="")) {
					$store_closing_time_[$key] = 0;
				}

				$different_store_closing_time[$key] = $store_closing_time_[$key];
			}
        }

		$time_form_settings['enable_different_closing_time'] = $enable_different_closing_time;
		$time_form_settings['different_store_closing_time'] = $different_store_closing_time;
		$time_form_settings['different_extended_closing_day'] = $different_extended_closing_day;
		$time_form_settings['different_extended_closing_time'] = $different_extended_closing_time;

		if(get_option('coderockz_woo_delivery_time_settings') == false) {
			update_option('coderockz_woo_delivery_time_settings', $time_form_settings);
		} else {
			$time_form_settings = array_merge(get_option('coderockz_woo_delivery_time_settings'),$time_form_settings);
			update_option('coderockz_woo_delivery_time_settings', $time_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_category_cutoff_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $cutoff_form_data );
		$category_cutoff = [];
		$cutoff_categories = $this->helper->coderockz_woo_delivery_array_sanitize($cutoff_form_data['coderockz_delivery_cutoff_categories']);
		foreach($cutoff_categories as $cutoff_category) {
			$category = str_replace("c-w-d"," ", $cutoff_category);

			$store_closing_time = "";

			if(isset($cutoff_form_data['coderockz_woo_delivery_category_wise_cutoff_hour_'.$cutoff_category]) && !empty($cutoff_form_data['coderockz_woo_delivery_category_wise_cutoff_hour_'.$cutoff_category]) && $category != "") {
				
				$store_closing_hour = (isset($cutoff_form_data['coderockz_woo_delivery_category_wise_cutoff_hour_'.$cutoff_category]) && $cutoff_form_data['coderockz_woo_delivery_category_wise_cutoff_hour_'.$cutoff_category] !="") ? sanitize_text_field($cutoff_form_data['coderockz_woo_delivery_category_wise_cutoff_hour_'.$cutoff_category]) : "0";
				
				$store_closing_min = (isset($cutoff_form_data['coderockz_woo_delivery_category_wise_cutoff_min_'.$cutoff_category]) && $cutoff_form_data['coderockz_woo_delivery_category_wise_cutoff_min_'.$cutoff_category] !="") ? sanitize_text_field($cutoff_form_data['coderockz_woo_delivery_category_wise_cutoff_min_'.$cutoff_category]) : "0"; 

				$store_closing_format = sanitize_text_field($cutoff_form_data['coderockz_woo_delivery_category_wise_cutoff_format_'.$cutoff_category]);
				if($store_closing_format == "am") {
					$store_closing_hour_12 = ($store_closing_hour == "12") ? "0" : $store_closing_hour;
					$store_closing_time = ((int)$store_closing_hour_12 * 60) + (int)$store_closing_min;
				} else {
					$store_closing_hour = ($store_closing_hour == "12") ? "0" : $store_closing_hour;
					$store_closing_time = (((int)$store_closing_hour + 12)*60) + (int)$store_closing_min;
				}

				if($store_closing_format == "am" && $store_closing_hour == "12" && ($store_closing_min =="0" || $store_closing_min =="00" || $store_closing_min_[$key] =="")) {
					$store_closing_time = 0;
				}

				$category_cutoff[$category] = (string)$store_closing_time;

			}
			
		}
		$disable_category_wise_cutoff_regular_category = !isset($cutoff_form_data['coderockz_woo_delivery_disable_category_wise_cutoff_regular_category']) ? false : true;

		$consider_multiple_cutoff_category_condition = (isset($cutoff_form_data['coderockz_woo_delivery_consider_multiple_cutoff_category_condition']) && !empty($cutoff_form_data['coderockz_woo_delivery_consider_multiple_cutoff_category_condition'])) ? $cutoff_form_data['coderockz_woo_delivery_consider_multiple_cutoff_category_condition'] : "first";

		$cutoff_form_settings['disable_category_wise_cutoff_regular_category'] = $disable_category_wise_cutoff_regular_category;
		$cutoff_form_settings['consider_multiple_cutoff_category_condition'] = $consider_multiple_cutoff_category_condition;
		$cutoff_form_settings['category_wise_cutoff'] = $category_cutoff;

		if(get_option('coderockz_woo_delivery_time_settings') == false) {
			update_option('coderockz_woo_delivery_time_settings', $cutoff_form_settings);
		} else {
			$cutoff_form_settings = array_merge(get_option('coderockz_woo_delivery_time_settings'),$cutoff_form_settings);
			update_option('coderockz_woo_delivery_time_settings', $cutoff_form_settings);
		}

		wp_send_json_success();
	}

    public function coderockz_woo_delivery_process_delivery_time_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $date_form_data );
		$time_form_settings = [];
		$enable_delivery_time = !isset($date_form_data['coderockz_enable_delivery_time']) ? false : true;
		$delivery_time_mandatory = !isset($date_form_data['coderockz_delivery_time_mandatory']) ? false : true;
		$delivery_time_field_label = sanitize_text_field($date_form_data['coderockz_delivery_time_field_label']);
		$delivery_time_field_placeholder = sanitize_text_field($date_form_data['coderockz_delivery_time_field_placeholder']);
		$disable_current_time_slot = !isset($date_form_data['coderockz_delivery_time_disable_current_time_slot']) ? false : true;
		$free_up_slot_for_delivery_completed = !isset($date_form_data['coderockz_delivery_free_up_slot_for_delivery_completed']) ? false : true;
		$delivery_time_format = sanitize_text_field($date_form_data['coderockz_delivery_time_format']);
		$delivery_time_maximum_order = sanitize_text_field($date_form_data['coderockz_delivery_time_maximum_order']);
		$auto_select_first_time = !isset($date_form_data['coderockz_auto_select_first_time']) ? false : true;
		$enable_as_soon_as_possible_option = !isset($date_form_data['coderockz_woo_delivry_as_soon_as_possible_option']) ? false : true;
		$as_soon_as_possible_text = sanitize_text_field($date_form_data['coderockz_woo_delivery_as_soon_as_possible_text']);
		$as_soon_as_possible_fee = sanitize_text_field($date_form_data['coderockz_woo_delivery_as_soon_as_possible_fee']);
		$search_box_hidden = !isset($date_form_data['coderockz_hide_searchbox_time_field_dropdown']) ? false : true;
		
		if(isset($date_form_data['coderockz_delivery_time_slot_starts_hour']) && $date_form_data['coderockz_delivery_time_slot_starts_hour'] !="") {
			$delivery_time_slot_starts_hour = (isset($date_form_data['coderockz_delivery_time_slot_starts_hour']) && $date_form_data['coderockz_delivery_time_slot_starts_hour'] !="") ? sanitize_text_field($date_form_data['coderockz_delivery_time_slot_starts_hour']) : "0";
			
			$delivery_time_slot_starts_min = (isset($date_form_data['coderockz_delivery_time_slot_starts_min']) && $date_form_data['coderockz_delivery_time_slot_starts_min'] !="") ? sanitize_text_field($date_form_data['coderockz_delivery_time_slot_starts_min']) : "0"; 

			$delivery_time_slot_starts_format = sanitize_text_field($date_form_data['coderockz_delivery_time_slot_starts_format']);
			if($delivery_time_slot_starts_format == "am") {
				$delivery_time_slot_starts_hour = ($delivery_time_slot_starts_hour == "12") ? "0" : $delivery_time_slot_starts_hour;
				$delivery_time_slot_starts = ((int)$delivery_time_slot_starts_hour * 60) + (int)$delivery_time_slot_starts_min;
			} else {
				$delivery_time_slot_starts_hour = ($delivery_time_slot_starts_hour == "12") ? "0" : $delivery_time_slot_starts_hour;
				$delivery_time_slot_starts = (((int)$delivery_time_slot_starts_hour + 12)*60) + (int)$delivery_time_slot_starts_min;
			}

	    } else {
	    	$delivery_time_slot_starts = "";
	    }

		if(isset($date_form_data['coderockz_delivery_time_slot_ends_hour']) && $date_form_data['coderockz_delivery_time_slot_ends_hour'] !="") {

			$delivery_time_slot_ends_hour = (isset($date_form_data['coderockz_delivery_time_slot_ends_hour']) && $date_form_data['coderockz_delivery_time_slot_ends_hour'] !="") ? sanitize_text_field($date_form_data['coderockz_delivery_time_slot_ends_hour']) : "0";
			
			$delivery_time_slot_ends_min = (isset($date_form_data['coderockz_delivery_time_slot_ends_min']) && $date_form_data['coderockz_delivery_time_slot_ends_min'] !="") ? sanitize_text_field($date_form_data['coderockz_delivery_time_slot_ends_min']) : "0"; 

			$delivery_time_slot_ends_format = sanitize_text_field($date_form_data['coderockz_delivery_time_slot_ends_format']);

			if($delivery_time_slot_ends_format == "am") {
				$delivery_time_slot_ends_hour_12 = ($delivery_time_slot_ends_hour == "12") ? "0" : $delivery_time_slot_ends_hour;
				$delivery_time_slot_ends = ((int)$delivery_time_slot_ends_hour_12 * 60) + (int)$delivery_time_slot_ends_min;
			} else {
				$delivery_time_slot_ends_hour = ($delivery_time_slot_ends_hour == "12") ? "0" : $delivery_time_slot_ends_hour;
				$delivery_time_slot_ends = (((int)$delivery_time_slot_ends_hour + 12)*60) + (int)$delivery_time_slot_ends_min;
			}

			if($delivery_time_slot_ends_format == "am" && $delivery_time_slot_ends_hour == "12" && ($delivery_time_slot_ends_min =="0"||$delivery_time_slot_ends_min =="00")) {
					$delivery_time_slot_ends = 1440;
			}

		} else {
			$delivery_time_slot_ends = "";
		}

		$delivery_time_slot_duration_time = (isset($date_form_data['coderockz_delivery_time_slot_duration_time']) && $date_form_data['coderockz_delivery_time_slot_duration_time'] !="") ? sanitize_text_field($date_form_data['coderockz_delivery_time_slot_duration_time']) : "0";
		$delivery_time_slot_duration_format = sanitize_text_field($date_form_data['coderockz_delivery_time_slot_duration_format']);

		if($delivery_time_slot_duration_format == "hour") {
			$each_time_slot = (int)$delivery_time_slot_duration_time * 60;
			$each_time_slot = $each_time_slot != 0 ? $each_time_slot : "";
		} else {
			$each_time_slot = (int)$delivery_time_slot_duration_time;
			$each_time_slot = $each_time_slot != 0 ? $each_time_slot : "";
		}

		$time_form_settings['enable_delivery_time'] = $enable_delivery_time;
		$time_form_settings['delivery_time_mandatory'] = $delivery_time_mandatory;
		$time_form_settings['field_label'] = $delivery_time_field_label;
		$time_form_settings['field_placeholder'] = $delivery_time_field_placeholder;
		$time_form_settings['time_format'] = $delivery_time_format;
		$time_form_settings['delivery_time_starts'] = (string)$delivery_time_slot_starts;
		$time_form_settings['delivery_time_ends'] = (string)$delivery_time_slot_ends;
		$time_form_settings['each_time_slot'] = (string)$each_time_slot;
		$time_form_settings['max_order_per_slot'] = $delivery_time_maximum_order;
		$time_form_settings['enable_as_soon_as_possible_option'] = $enable_as_soon_as_possible_option;
		$time_form_settings['as_soon_as_possible_text'] = $as_soon_as_possible_text;
		$time_form_settings['as_soon_as_possible_fee'] = $as_soon_as_possible_fee;
		$time_form_settings['disabled_current_time_slot'] = $disable_current_time_slot;
		$time_form_settings['free_up_slot_for_delivery_completed'] = $free_up_slot_for_delivery_completed;
		$time_form_settings['auto_select_first_time'] = $auto_select_first_time;
		$time_form_settings['hide_searchbox'] = $search_box_hidden;

		if(get_option('coderockz_woo_delivery_time_settings') == false) {
			update_option('coderockz_woo_delivery_time_settings', $time_form_settings);
		} else {
			$time_form_settings = array_merge(get_option('coderockz_woo_delivery_time_settings'),$time_form_settings);
			update_option('coderockz_woo_delivery_time_settings', $time_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_process_pickup_time_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $pickup_form_data );
		$pickup_time_form_settings = [];
		$enable_pickup_time = !isset($pickup_form_data['coderockz_enable_pickup_time']) ? false : true;
		$pickup_time_mandatory = !isset($pickup_form_data['coderockz_pickup_time_mandatory']) ? false : true;
		$pickup_time_field_label = sanitize_text_field($pickup_form_data['coderockz_pickup_time_field_label']);
		$pickup_time_field_placeholder = sanitize_text_field($pickup_form_data['coderockz_pickup_time_field_placeholder']);
		$disable_current_time_slot = !isset($pickup_form_data['coderockz_pickup_time_disable_current_time_slot']) ? false : true;
		$max_pickup_consider_location = !isset($pickup_form_data['coderockz_woo_delivery_max_pickup_consider_location']) ? false : true;
		$free_up_slot_for_pickup_completed = !isset($pickup_form_data['coderockz_delivery_free_up_slot_for_pickup_completed']) ? false : true;
		$pickup_time_format = sanitize_text_field($pickup_form_data['coderockz_pickup_time_format']);
		$pickup_time_maximum_order = sanitize_text_field($pickup_form_data['coderockz_pickup_time_maximum_order']);
		$auto_select_first_time = !isset($pickup_form_data['coderockz_auto_select_first_pickup_time']) ? false : true;
		$search_box_hidden = !isset($pickup_form_data['coderockz_hide_searchbox_pickup_field_dropdown']) ? false : true;
		
		if(isset($pickup_form_data['coderockz_pickup_time_slot_starts_hour']) && $pickup_form_data['coderockz_pickup_time_slot_starts_hour'] !="") {

			$pickup_time_slot_starts_hour = (isset($pickup_form_data['coderockz_pickup_time_slot_starts_hour']) && $pickup_form_data['coderockz_pickup_time_slot_starts_hour'] !="") ? sanitize_text_field($pickup_form_data['coderockz_pickup_time_slot_starts_hour']) : "0";
			
			$pickup_time_slot_starts_min = (isset($pickup_form_data['coderockz_pickup_time_slot_starts_min']) && $pickup_form_data['coderockz_pickup_time_slot_starts_min'] !="") ? sanitize_text_field($pickup_form_data['coderockz_pickup_time_slot_starts_min']) : "0"; 

			$pickup_time_slot_starts_format = sanitize_text_field($pickup_form_data['coderockz_pickup_time_slot_starts_format']);
			if($pickup_time_slot_starts_format == "am") {
				$pickup_time_slot_starts_hour = ($pickup_time_slot_starts_hour == "12") ? "0" : $pickup_time_slot_starts_hour;
				$pickup_time_slot_starts = ((int)$pickup_time_slot_starts_hour * 60) + (int)$pickup_time_slot_starts_min;
			} else {
				$pickup_time_slot_starts_hour = ($pickup_time_slot_starts_hour == "12") ? "0" : $pickup_time_slot_starts_hour;
				$pickup_time_slot_starts = (((int)$pickup_time_slot_starts_hour + 12)*60) + (int)$pickup_time_slot_starts_min;
			}

		} else {
			$pickup_time_slot_starts = "";
		}

		if(isset($pickup_form_data['coderockz_pickup_time_slot_ends_hour']) && $pickup_form_data['coderockz_pickup_time_slot_ends_hour'] !="") {

			$pickup_time_slot_ends_hour = (isset($pickup_form_data['coderockz_pickup_time_slot_ends_hour']) && $pickup_form_data['coderockz_pickup_time_slot_ends_hour'] !="") ? sanitize_text_field($pickup_form_data['coderockz_pickup_time_slot_ends_hour']) : "0";
			
			$pickup_time_slot_ends_min = (isset($pickup_form_data['coderockz_pickup_time_slot_ends_min']) && $pickup_form_data['coderockz_pickup_time_slot_ends_min'] !="") ? sanitize_text_field($pickup_form_data['coderockz_pickup_time_slot_ends_min']) : "0"; 

			$pickup_time_slot_ends_format = sanitize_text_field($pickup_form_data['coderockz_pickup_time_slot_ends_format']);

			if($pickup_time_slot_ends_format == "am") {
				$pickup_time_slot_ends_hour_12 = ($pickup_time_slot_ends_hour == "12") ? "0" : $pickup_time_slot_ends_hour;
				$pickup_time_slot_ends = ((int)$pickup_time_slot_ends_hour_12 * 60) + (int)$pickup_time_slot_ends_min;
			} else {
				$pickup_time_slot_ends_hour = ($pickup_time_slot_ends_hour == "12") ? "0" : $pickup_time_slot_ends_hour;
				$pickup_time_slot_ends = (((int)$pickup_time_slot_ends_hour + 12)*60) + (int)$pickup_time_slot_ends_min;
			}

			if($pickup_time_slot_ends_format == "am" && $pickup_time_slot_ends_hour == "12" && ($pickup_time_slot_ends_min =="0"||$pickup_time_slot_ends_min =="00")) {
					$pickup_time_slot_ends = 1440;
			}

		} else {
			$pickup_time_slot_ends = "";
		}

		$pickup_time_slot_duration_time = (isset($pickup_form_data['coderockz_pickup_time_slot_duration_time']) && $pickup_form_data['coderockz_pickup_time_slot_duration_time'] !="") ? sanitize_text_field($pickup_form_data['coderockz_pickup_time_slot_duration_time']) : "0";
		$pickup_time_slot_duration_format = sanitize_text_field($pickup_form_data['coderockz_pickup_time_slot_duration_format']);

		if($pickup_time_slot_duration_format == "hour") {
			$each_time_slot = (int)$pickup_time_slot_duration_time * 60;
			$each_time_slot = $each_time_slot != 0 ? $each_time_slot : "";
		} else {
			$each_time_slot = (int)$pickup_time_slot_duration_time;
			$each_time_slot = $each_time_slot != 0 ? $each_time_slot : "";
		}

		$pickup_time_form_settings['enable_pickup_time'] = $enable_pickup_time;
		$pickup_time_form_settings['pickup_time_mandatory'] = $pickup_time_mandatory;
		$pickup_time_form_settings['field_label'] = $pickup_time_field_label;
		$pickup_time_form_settings['field_placeholder'] = $pickup_time_field_placeholder;
		$pickup_time_form_settings['time_format'] = $pickup_time_format;
		$pickup_time_form_settings['pickup_time_starts'] = (string)$pickup_time_slot_starts;
		$pickup_time_form_settings['pickup_time_ends'] = (string)$pickup_time_slot_ends;
		$pickup_time_form_settings['each_time_slot'] = (string)$each_time_slot;
		$pickup_time_form_settings['max_pickup_per_slot'] = $pickup_time_maximum_order;
		$pickup_time_form_settings['disabled_current_pickup_time_slot'] = $disable_current_time_slot;
		$pickup_time_form_settings['free_up_slot_for_pickup_completed'] = $free_up_slot_for_pickup_completed;
		$pickup_time_form_settings['auto_select_first_time'] = $auto_select_first_time;
		$pickup_time_form_settings['hide_searchbox'] = $search_box_hidden;
		$pickup_time_form_settings['max_pickup_consider_location'] = $max_pickup_consider_location;

		if(get_option('coderockz_woo_delivery_pickup_time_settings') == false) {
			update_option('coderockz_woo_delivery_pickup_time_settings', $pickup_time_form_settings);
		} else {
			$pickup_time_form_settings = array_merge(get_option('coderockz_woo_delivery_pickup_time_settings'),$pickup_time_form_settings);
			update_option('coderockz_woo_delivery_pickup_time_settings', $pickup_time_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_process_custom_time_slot_settings() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $custom_time_form_data );
		$custom_time_form_settings = [];
		$enable_custom_time = !isset($custom_time_form_data['coderockz_woo_delivery_enable_custom_time_slot']) ? false : true;
		
		$custom_time_form_settings['enable_custom_time_slot'] = $enable_custom_time;
		
		if(get_option('coderockz_woo_delivery_time_slot_settings') == false) {
			update_option('coderockz_woo_delivery_time_slot_settings', $custom_time_form_settings);
		} else {
			$custom_time_form_settings = array_merge(get_option('coderockz_woo_delivery_time_slot_settings'),$custom_time_form_settings);
			update_option('coderockz_woo_delivery_time_slot_settings', $custom_time_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_delete_custom_time_slot() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		$time_slot = [];
		$custom_time_form_settings = [];
		
		$custom_time_slot_starts_hour = (isset($_POST['startHour']) && $_POST['startHour'] !="") ? sanitize_text_field($_POST['startHour']) : "0";
		
		$custom_time_slot_starts_min = (isset($_POST['startMin']) && $_POST['startMin'] !="") ? sanitize_text_field($_POST['startMin']) : "0";

		$custom_time_slot_starts_format = sanitize_text_field($_POST['startFormat']);
		if($custom_time_slot_starts_format == "am") {
			$custom_time_slot_starts_hour = ($custom_time_slot_starts_hour == "12") ? "0" : $custom_time_slot_starts_hour;
			$custom_time_slot_starts = ((int)$custom_time_slot_starts_hour * 60) + (int)$custom_time_slot_starts_min;
		} else {
			$custom_time_slot_starts_hour = ($custom_time_slot_starts_hour == "12") ? "0" : $custom_time_slot_starts_hour;
			$custom_time_slot_starts = (((int)$custom_time_slot_starts_hour + 12)*60) + (int)$custom_time_slot_starts_min;
		}

		$custom_time_slot_ends_hour = (isset($_POST['endHour']) && $_POST['endHour'] !="") ? sanitize_text_field($_POST['endHour']) : "0";
		
		$custom_time_slot_ends_min = (isset($_POST['endMin']) && $_POST['endMin'] !="") ? sanitize_text_field($_POST['endMin']) : "0"; 

		$custom_time_slot_ends_format = sanitize_text_field($_POST['endFormat']);

		if($custom_time_slot_ends_format == "am") {
			$custom_time_slot_ends_hour_12 = ($custom_time_slot_ends_hour == "12") ? "0" : $custom_time_slot_ends_hour;
			$custom_time_slot_ends = ((int)$custom_time_slot_ends_hour_12 * 60) + (int)$custom_time_slot_ends_min;
		} else {
			$custom_time_slot_ends_hour = ($custom_time_slot_ends_hour == "12") ? "0" : $custom_time_slot_ends_hour;
			$custom_time_slot_ends = (((int)$custom_time_slot_ends_hour + 12)*60) + (int)$custom_time_slot_ends_min;
		}

		if($custom_time_slot_ends_format == "am" && $custom_time_slot_ends_hour == "12" && ($custom_time_slot_ends_min =="0"||$custom_time_slot_ends_min =="00")) {
			$custom_time_slot_ends = 1440;
		}

		$db_custom_time_slot = get_option('coderockz_woo_delivery_time_slot_settings')['time_slot'];

		if(!is_null($db_custom_time_slot)) {
		
			if(array_key_exists($custom_time_slot_starts.'-'.$custom_time_slot_ends,$db_custom_time_slot)) {
				unset($db_custom_time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]);
				$custom_time_form_settings['time_slot'] = $db_custom_time_slot;
			}

		}

		$custom_time_form_settings = array_merge(get_option('coderockz_woo_delivery_time_slot_settings'),$custom_time_form_settings);
		update_option('coderockz_woo_delivery_time_slot_settings', $custom_time_form_settings);
		
		wp_send_json_success();
    }

	public function coderockz_woo_delivery_add_enable_custom_time_slot() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		$time_slot = [];
		$custom_time_form_settings = [];		

		$old_custom_time_slot_starts_hour = (isset($_POST['oldStartHour']) && $_POST['oldStartHour'] !="") ? sanitize_text_field($_POST['oldStartHour']) : "0";
		
		$old_custom_time_slot_starts_min = (isset($_POST['oldStartMin']) && $_POST['oldStartMin'] !="") ? sanitize_text_field($_POST['oldStartMin']) : "0";

		$old_custom_time_slot_starts_format = (isset($_POST['oldStartFormat']) && $_POST['oldStartFormat'] !="") ? sanitize_text_field($_POST['oldStartFormat']) : "";

		if($old_custom_time_slot_starts_format == "am") {
			$old_custom_time_slot_starts_hour = ($old_custom_time_slot_starts_hour == "12") ? "0" : $old_custom_time_slot_starts_hour;
			$old_custom_time_slot_starts = ((int)$old_custom_time_slot_starts_hour * 60) + (int)$old_custom_time_slot_starts_min;
		} else {
			$old_custom_time_slot_starts_hour = ($old_custom_time_slot_starts_hour == "12") ? "0" : $old_custom_time_slot_starts_hour;
			$old_custom_time_slot_starts = (((int)$old_custom_time_slot_starts_hour + 12)*60) + (int)$old_custom_time_slot_starts_min;
		}
		
		$old_custom_time_slot_ends_hour = (isset($_POST['oldEndHour']) && $_POST['oldEndHour'] !="") ? sanitize_text_field($_POST['oldEndHour']) : "0";
		
		$old_custom_time_slot_ends_min = (isset($_POST['oldEndMin']) && $_POST['oldEndMin'] !="") ? sanitize_text_field($_POST['oldEndMin']) : "0"; 

		$old_custom_time_slot_ends_format = (isset($_POST['oldEndFormat']) && $_POST['oldEndFormat'] !="") ? sanitize_text_field($_POST['oldEndFormat']) : "";

		if($old_custom_time_slot_ends_format == "am") {
			$old_custom_time_slot_ends_hour = ($old_custom_time_slot_ends_hour == "12") ? "0" : $old_custom_time_slot_ends_hour;
			$old_custom_time_slot_ends = ((int)$old_custom_time_slot_ends_hour * 60) + (int)$old_custom_time_slot_ends_min;
		} else {
			$old_custom_time_slot_ends_hour = ($old_custom_time_slot_ends_hour == "12") ? "0" : $old_custom_time_slot_ends_hour;
			$old_custom_time_slot_ends = (((int)$old_custom_time_slot_ends_hour + 12)*60) + (int)$old_custom_time_slot_ends_min;
		} 

		$custom_time_slot_starts_hour = (isset($_POST['startHour']) && $_POST['startHour'] !="") ? sanitize_text_field($_POST['startHour']) : "0";
		
		$custom_time_slot_starts_min = (isset($_POST['startMin']) && $_POST['startMin'] !="") ? sanitize_text_field($_POST['startMin']) : "0";

		$custom_time_slot_starts_format = sanitize_text_field($_POST['startFormat']);

		if($custom_time_slot_starts_format == "am") {
			$custom_time_slot_starts_hour = ($custom_time_slot_starts_hour == "12") ? "0" : $custom_time_slot_starts_hour;
			$custom_time_slot_starts = ((int)$custom_time_slot_starts_hour * 60) + (int)$custom_time_slot_starts_min;
		} else {
			$custom_time_slot_starts_hour = ($custom_time_slot_starts_hour == "12") ? "0" : $custom_time_slot_starts_hour;
			$custom_time_slot_starts = (((int)$custom_time_slot_starts_hour + 12)*60) + (int)$custom_time_slot_starts_min;
		}

		$custom_time_slot_ends_hour = (isset($_POST['endHour']) && $_POST['endHour'] !="") ? sanitize_text_field($_POST['endHour']) : "0";
		
		$custom_time_slot_ends_min = (isset($_POST['endMin']) && $_POST['endMin'] !="") ? sanitize_text_field($_POST['endMin']) : "0"; 

		$custom_time_slot_ends_format = sanitize_text_field($_POST['endFormat']);

		if($custom_time_slot_ends_format == "am") {
			$custom_time_slot_ends_hour_12 = ($custom_time_slot_ends_hour == "12") ? "0" : $custom_time_slot_ends_hour;
			$custom_time_slot_ends = ((int)$custom_time_slot_ends_hour_12 * 60) + (int)$custom_time_slot_ends_min;
		} else {
			$custom_time_slot_ends_hour = ($custom_time_slot_ends_hour == "12") ? "0" : $custom_time_slot_ends_hour;
			$custom_time_slot_ends = (((int)$custom_time_slot_ends_hour + 12)*60) + (int)$custom_time_slot_ends_min;
		}

		if($custom_time_slot_ends_format == "am" && $custom_time_slot_ends_hour == "12" && ($custom_time_slot_ends_min =="0"||$custom_time_slot_ends_min =="00")) {
			$custom_time_slot_ends = 1440;
		}

		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['start'] = (string)$custom_time_slot_starts;
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['end'] = (string)$custom_time_slot_ends;

		$enable_custom_time_split = !isset($_POST['enableCustomTimeSplit']) || $_POST['enableCustomTimeSplit'] == "unchecked" ? false : true;
		$split_time_slot_duration_time = (isset($_POST['splitDurationTime']) && $_POST['splitDurationTime'] !="") ? sanitize_text_field($_POST['splitDurationTime']) : "0";
		$split_time_slot_duration_format = (isset($_POST['splitDurationFormat']) && $_POST['splitDurationFormat'] !="") ? sanitize_text_field($_POST['splitDurationFormat']) : "min";

		if($split_time_slot_duration_format == "hour") {
			$each_split_time_slot = (int)$split_time_slot_duration_time * 60;
			$each_split_time_slot = $each_split_time_slot != 0 ? $each_split_time_slot : "";
		} else {
			$each_split_time_slot = (int)$split_time_slot_duration_time;
			$each_split_time_slot = $each_split_time_slot != 0 ? $each_split_time_slot : "";
		}
		$enable_custom_splited_time_single = !isset($_POST['enableCustomSplitedTimeSingle']) || $_POST['enableCustomSplitedTimeSingle'] == "unchecked" ? false : true;
		$enable_custom_time_single = !isset($_POST['enableCustomTimeSingle']) || $_POST['enableCustomTimeSingle'] == "unchecked" ? false : true;
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['enable_split'] = $enable_custom_time_split;
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['split_slot_duration'] = $each_split_time_slot;
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['enable_single_splited_slot'] = $enable_custom_splited_time_single;
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['enable_single_slot'] = $enable_custom_time_single;

		$hide_custom_time_current_date = !isset($_POST['hideCustomTimeCurrentDate']) || $_POST['hideCustomTimeCurrentDate'] == "unchecked" ? false : true;
		$hide_custom_time_next_day = !isset($_POST['hideCustomTimeNextDay']) || $_POST['hideCustomTimeNextDay'] == "unchecked" ? false : true;

		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['hide_time_slot_current_date'] = $hide_custom_time_current_date;
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['hide_time_slot_next_day'] = $hide_custom_time_next_day;

		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['timeslot_closing_time'] = $_POST['timeslotHidetime'];

		$custom_time_maximum_order = sanitize_text_field($_POST['maxOrder']);
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['max_order'] = $custom_time_maximum_order;

		$custom_time_slot_open_specific_date = sanitize_text_field(str_replace(', ', ',', $_POST['openSpecificDate']));
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['only_specific_date'] = $custom_time_slot_open_specific_date;

		$custom_time_slot_close_specific_date = sanitize_text_field(str_replace(', ', ',', $_POST['closeSpecificDate']));
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['only_specific_date_close'] = $custom_time_slot_close_specific_date;

		$custom_time_slot_fee = sanitize_text_field($_POST['slotFee']);
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['fee'] = $custom_time_slot_fee;

		$disable_for = (isset($_POST['disableFor']) && !empty($_POST['disableFor'])) ? $_POST['disableFor'] : array();
		
		$state_or_zip_selection = sanitize_text_field($_POST['stateOrZipSelection']);

		if($state_or_zip_selection == 'zone') {
			$region_zone_code = (isset($_POST['regionZoneCode']) && !empty($_POST['regionZoneCode'])) ? $_POST['regionZoneCode'] : [];
			$zone_state_code = [];
			$zone_post_code = [];
			$region_shipping_method = [];
			foreach($region_zone_code as $zone_code) {
				
				$zone = new WC_Shipping_Zone($zone_code);

				$zone_locations = $zone->get_zone_locations();
				$zone_locations = $this->helper->objectToArray($zone_locations);
				foreach($zone_locations as $zone_location) {
					if($zone_location['type'] == "state") {
						$position = strpos($zone_location['code'],':');
						$zone_state_code[] = substr($zone_location['code'],($position+1));
					} else if($zone_location['type'] == "postcode") {
						$zone_post_code[] = $zone_location['code'];
					} else if($zone_location['type'] == "country") {
						$zone_state_code[] = $zone_location['code'];
					}
				}
			}

			$region_state_code = $zone_state_code;
			$region_post_code = $zone_post_code;


		} elseif($state_or_zip_selection == 'state') {
			$region_state_code = (isset($_POST['regionStateCode']) && !empty($_POST['regionStateCode'])) ? $_POST['regionStateCode'] : [];
			$region_zone_code = [];
			$region_post_code = [];
			$region_shipping_method = [];

		} elseif($state_or_zip_selection == 'postcode') {
			$region_post_code = (isset($_POST['regionPostCode']) && !empty($_POST['regionPostCode'])) ? $_POST['regionPostCode'] : [];
			$region_zone_code = [];
			$region_state_code = [];
			$region_shipping_method = [];
		} elseif($state_or_zip_selection == 'shipping_method') {
			$region_shipping_method = (isset($_POST['regionShippingMethod']) && !empty($_POST['regionShippingMethod'])) ? $_POST['regionShippingMethod'] : [];
			$region_zone_code = [];
			$region_state_code = [];
			$region_post_code = [];
		} else {
			$region_zone_code = [];
			$region_state_code = [];
			$region_post_code = [];
			$region_shipping_method = [];
		}		

		$custom_time_slot_disable = $this->helper->coderockz_woo_delivery_array_sanitize($disable_for);
		$custom_time_slot_disable_zone_code = $this->helper->coderockz_woo_delivery_array_sanitize($region_zone_code);
		$custom_time_slot_disable_state_code = $this->helper->coderockz_woo_delivery_array_sanitize($region_state_code);
		$custom_time_slot_disable_post_code = $this->helper->coderockz_woo_delivery_array_sanitize($region_post_code);
		$custom_time_slot_disable_shipping_method = $this->helper->coderockz_woo_delivery_array_sanitize($region_shipping_method);

		$hide_categories = (isset($_POST['hideCategoriesArray']) && !empty($_POST['hideCategoriesArray'])) ? $_POST['hideCategoriesArray'] : array();
		$hide_products = (isset($_POST['hideProductsArray']) && !empty($_POST['hideProductsArray'])) ? $_POST['hideProductsArray'] : array();

		$hide_categories_array = $this->helper->coderockz_woo_delivery_array_sanitize($hide_categories);
		$hide_products_array = $this->helper->coderockz_woo_delivery_array_sanitize($hide_products);

		$time_slot_shown_other_categories_products = !isset($_POST['enableShownOtherCategoriesProducts']) || $_POST['enableShownOtherCategoriesProducts'] == "unchecked" ? false : true;

		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['hide_categories'] = $hide_categories_array;
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['hide_products'] = $hide_products_array;
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['time_slot_shown_other_categories_products'] = $time_slot_shown_other_categories_products;

		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['disable_for'] = $custom_time_slot_disable;
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['more_settings'] = $state_or_zip_selection;
		
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['disable_zone'] = $custom_time_slot_disable_zone_code;
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['disable_state'] = $custom_time_slot_disable_state_code;
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['disable_postcode'] = $custom_time_slot_disable_post_code;
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['disable_shipping_method'] = $custom_time_slot_disable_shipping_method;

		$enable_added_custom_time = !isset($_POST['enableAddedCustomTime']) || $_POST['enableAddedCustomTime'] == "unchecked" ? false : true;
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['enable'] = $enable_added_custom_time;

		$custom_timeslot_name = sanitize_text_field($_POST['customTimeslotName']);
		$time_slot[$custom_time_slot_starts.'-'.$custom_time_slot_ends]['name'] = $custom_timeslot_name;
		
		if(get_option('coderockz_woo_delivery_time_slot_settings') == false) {
			$temp_time_slot = [];
			$temp_time_slot['time_slot'] = $time_slot;
			update_option('coderockz_woo_delivery_time_slot_settings', $temp_time_slot);
		} else {

			if(isset(get_option('coderockz_woo_delivery_time_slot_settings')['time_slot']) && count(get_option('coderockz_woo_delivery_time_slot_settings')['time_slot'])>0) {

				$db_custom_time_slot = get_option('coderockz_woo_delivery_time_slot_settings')['time_slot'];
				if($old_custom_time_slot_starts != $custom_time_slot_starts || $old_custom_time_slot_ends != $custom_time_slot_ends) {

					
					if(array_key_exists($old_custom_time_slot_starts.'-'.$old_custom_time_slot_ends,$db_custom_time_slot)) {
						
						unset($db_custom_time_slot[$old_custom_time_slot_starts.'-'.$old_custom_time_slot_ends]);
						
					}
				}

				$time_slot = array_merge($db_custom_time_slot,$time_slot);
				$custom_time_form_settings['time_slot'] = $time_slot;
			} else {
				$custom_time_form_settings['time_slot'] = $time_slot;
			}

			$custom_time_form_settings = array_merge(get_option('coderockz_woo_delivery_time_slot_settings'),$custom_time_form_settings);
			update_option('coderockz_woo_delivery_time_slot_settings', $custom_time_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_process_custom_pickup_slot_settings() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $custom_time_form_data );
		$custom_pickup_form_settings = [];
		$enable_custom_pickup = !isset($custom_time_form_data['coderockz_woo_delivery_enable_custom_pickup_slot']) ? false : true;
		
		$custom_pickup_form_settings['enable_custom_pickup_slot'] = $enable_custom_pickup;
		
		if(get_option('coderockz_woo_delivery_pickup_slot_settings') == false) {
			update_option('coderockz_woo_delivery_pickup_slot_settings', $custom_pickup_form_settings);
		} else {
			$custom_pickup_form_settings = array_merge(get_option('coderockz_woo_delivery_pickup_slot_settings'),$custom_pickup_form_settings);
			update_option('coderockz_woo_delivery_pickup_slot_settings', $custom_pickup_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_delete_custom_pickup_slot() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		$pickup_slot = [];
		$custom_pickup_form_settings = [];
		
		$custom_pickup_slot_starts_hour = (isset($_POST['pickupStartHour']) && $_POST['pickupStartHour'] !="") ? sanitize_text_field($_POST['pickupStartHour']) : "0";
		
		$custom_pickup_slot_starts_min = (isset($_POST['pickupStartMin']) && $_POST['pickupStartMin'] !="") ? sanitize_text_field($_POST['pickupStartMin']) : "0";

		$custom_pickup_slot_starts_format = (isset($_POST['pickupStartFormat']) && $_POST['pickupStartFormat'] !="") ? sanitize_text_field($_POST['pickupStartFormat']) : "";

		if($custom_pickup_slot_starts_format == "am") {
			$custom_pickup_slot_starts_hour = ($custom_pickup_slot_starts_hour == "12") ? "0" : $custom_pickup_slot_starts_hour;
			$custom_pickup_slot_starts = ((int)$custom_pickup_slot_starts_hour * 60) + (int)$custom_pickup_slot_starts_min;
		} elseif($custom_pickup_slot_starts_format == "pm") {
			$custom_pickup_slot_starts_hour = ($custom_pickup_slot_starts_hour == "12") ? "0" : $custom_pickup_slot_starts_hour;
			$custom_pickup_slot_starts = (((int)$custom_pickup_slot_starts_hour + 12)*60) + (int)$custom_pickup_slot_starts_min;
		}

		$custom_pickup_slot_ends_hour = (isset($_POST['pickupEndHour']) && $_POST['pickupEndHour'] !="") ? sanitize_text_field($_POST['pickupEndHour']) : "0";
		
		$custom_pickup_slot_ends_min = (isset($_POST['pickupEndMin']) && $_POST['pickupEndMin'] !="") ? sanitize_text_field($_POST['pickupEndMin']) : "0"; 

		$custom_pickup_slot_ends_format = (isset($_POST['pickupEndFormat']) && $_POST['pickupEndFormat'] !="") ? sanitize_text_field($_POST['pickupEndFormat']) : "";

		if($custom_pickup_slot_ends_format == "am") {
			$custom_pickup_slot_ends_hour_12 = ($custom_pickup_slot_ends_hour == "12") ? "0" : $custom_pickup_slot_ends_hour;
			$custom_pickup_slot_ends = ((int)$custom_pickup_slot_ends_hour_12 * 60) + (int)$custom_pickup_slot_ends_min;
		} elseif(($custom_pickup_slot_ends_format == "pm")) {
			$custom_pickup_slot_ends_hour = ($custom_pickup_slot_ends_hour == "12") ? "0" : $custom_pickup_slot_ends_hour;
			$custom_pickup_slot_ends = (((int)$custom_pickup_slot_ends_hour + 12)*60) + (int)$custom_pickup_slot_ends_min;
		}

		if($custom_pickup_slot_ends_format == "am" && $custom_pickup_slot_ends_hour == "12" && ($custom_pickup_slot_ends_min =="0"||$custom_pickup_slot_ends_min =="00")) {
			$custom_pickup_slot_ends = 1440;
		}
	
		$db_custom_pickup_slot = get_option('coderockz_woo_delivery_pickup_slot_settings')['time_slot'];
		if(!is_null($db_custom_pickup_slot)) {
			if(array_key_exists($custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends,$db_custom_pickup_slot)) {
				unset($db_custom_pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]);
				$custom_pickup_form_settings['time_slot'] = $db_custom_pickup_slot;
			}
		}

		$custom_pickup_form_settings = array_merge(get_option('coderockz_woo_delivery_pickup_slot_settings'),$custom_pickup_form_settings);
		update_option('coderockz_woo_delivery_pickup_slot_settings', $custom_pickup_form_settings);

		wp_send_json_success();
    }

	public function coderockz_woo_delivery_add_enable_custom_pickup_slot() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		$pickup_slot = [];
		$custom_pickup_form_settings = [];		

		$old_custom_pickup_slot_starts_hour = (isset($_POST['pickupOldStartHour']) && $_POST['pickupOldStartHour'] !="") ? sanitize_text_field($_POST['pickupOldStartHour']) : "0";
		
		$old_custom_pickup_slot_starts_min = (isset($_POST['pickupOldStartMin']) && $_POST['pickupOldStartMin'] !="") ? sanitize_text_field($_POST['pickupOldStartMin']) : "0";

		$old_custom_pickup_slot_starts_format = (isset($_POST['pickupOldStartFormat']) && $_POST['pickupOldStartFormat'] !="") ? sanitize_text_field($_POST['pickupOldStartFormat']) : "";

		if($old_custom_pickup_slot_starts_format == "am") {
			$old_custom_pickup_slot_starts_hour = ($old_custom_pickup_slot_starts_hour == "12") ? "0" : $old_custom_pickup_slot_starts_hour;
			$old_custom_pickup_slot_starts = ((int)$old_custom_pickup_slot_starts_hour * 60) + (int)$old_custom_pickup_slot_starts_min;
		} else {
			$old_custom_pickup_slot_starts_hour = ($old_custom_pickup_slot_starts_hour == "12") ? "0" : $old_custom_pickup_slot_starts_hour;
			$old_custom_pickup_slot_starts = (((int)$old_custom_pickup_slot_starts_hour + 12)*60) + (int)$old_custom_pickup_slot_starts_min;
		}
		
		$old_custom_pickup_slot_ends_hour = (isset($_POST['pickupOldEndHour']) && $_POST['pickupOldEndHour'] !="") ? sanitize_text_field($_POST['pickupOldEndHour']) : "0";
		
		$old_custom_pickup_slot_ends_min = (isset($_POST['pickupOldEndMin']) && $_POST['pickupOldEndMin'] !="") ? sanitize_text_field($_POST['pickupOldEndMin']) : "0"; 

		$old_custom_pickup_slot_ends_format = (isset($_POST['pickupOldEndFormat']) && $_POST['pickupOldEndFormat'] !="") ? sanitize_text_field($_POST['pickupOldEndFormat']) : "";

		if($old_custom_pickup_slot_ends_format == "am") {
			$old_custom_pickup_slot_ends_hour = ($old_custom_pickup_slot_ends_hour == "12") ? "0" : $old_custom_pickup_slot_ends_hour;
			$old_custom_pickup_slot_ends = ((int)$old_custom_pickup_slot_ends_hour * 60) + (int)$old_custom_pickup_slot_ends_min;
		} else {
			$old_custom_pickup_slot_ends_hour = ($old_custom_pickup_slot_ends_hour == "12") ? "0" : $old_custom_pickup_slot_ends_hour;
			$old_custom_pickup_slot_ends = (((int)$old_custom_pickup_slot_ends_hour + 12)*60) + (int)$old_custom_pickup_slot_ends_min;
		} 

		$custom_pickup_slot_starts_hour = (isset($_POST['pickupStartHour']) && $_POST['pickupStartHour'] !="") ? sanitize_text_field($_POST['pickupStartHour']) : "0";
		
		$custom_pickup_slot_starts_min = (isset($_POST['pickupStartMin']) && $_POST['pickupStartMin'] !="") ? sanitize_text_field($_POST['pickupStartMin']) : "0";

		$custom_pickup_slot_starts_format = sanitize_text_field($_POST['pickupStartFormat']);

		if($custom_pickup_slot_starts_format == "am") {
			$custom_pickup_slot_starts_hour = ($custom_pickup_slot_starts_hour == "12") ? "0" : $custom_pickup_slot_starts_hour;
			$custom_pickup_slot_starts = ((int)$custom_pickup_slot_starts_hour * 60) + (int)$custom_pickup_slot_starts_min;
		} else {
			$custom_pickup_slot_starts_hour = ($custom_pickup_slot_starts_hour == "12") ? "0" : $custom_pickup_slot_starts_hour;
			$custom_pickup_slot_starts = (((int)$custom_pickup_slot_starts_hour + 12)*60) + (int)$custom_pickup_slot_starts_min;
		}

		$custom_pickup_slot_ends_hour = (isset($_POST['pickupEndHour']) && $_POST['pickupEndHour'] !="") ? sanitize_text_field($_POST['pickupEndHour']) : "0";
		
		$custom_pickup_slot_ends_min = (isset($_POST['pickupEndMin']) && $_POST['pickupEndMin'] !="") ? sanitize_text_field($_POST['pickupEndMin']) : "0"; 

		$custom_pickup_slot_ends_format = sanitize_text_field($_POST['pickupEndFormat']);

		if($custom_pickup_slot_ends_format == "am") {
			$custom_pickup_slot_ends_hour_12 = ($custom_pickup_slot_ends_hour == "12") ? "0" : $custom_pickup_slot_ends_hour;
			$custom_pickup_slot_ends = ((int)$custom_pickup_slot_ends_hour_12 * 60) + (int)$custom_pickup_slot_ends_min;
		} else {
			$custom_pickup_slot_ends_hour = ($custom_pickup_slot_ends_hour == "12") ? "0" : $custom_pickup_slot_ends_hour;
			$custom_pickup_slot_ends = (((int)$custom_pickup_slot_ends_hour + 12)*60) + (int)$custom_pickup_slot_ends_min;
		}

		if($custom_pickup_slot_ends_format == "am" && $custom_pickup_slot_ends_hour == "12" && ($custom_pickup_slot_ends_min =="0"||$custom_pickup_slot_ends_min =="00")) {
			$custom_pickup_slot_ends = 1440;
		}

		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['start'] = (string)$custom_pickup_slot_starts;
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['end'] = (string)$custom_pickup_slot_ends;

		$enable_custom_pickup_split = !isset($_POST['pickupEnableCustomTimeSplit']) || $_POST['pickupEnableCustomTimeSplit'] == "unchecked" ? false : true;
		$split_pickup_slot_duration_time = (isset($_POST['pickupSplitDurationTime']) && $_POST['pickupSplitDurationTime'] !="") ? sanitize_text_field($_POST['pickupSplitDurationTime']) : "0";
		$split_pickup_slot_duration_format = (isset($_POST['pickupSplitDurationFormat']) && $_POST['pickupSplitDurationFormat'] !="") ? sanitize_text_field($_POST['pickupSplitDurationFormat']) : "min";

		if($split_pickup_slot_duration_format == "hour") {
			$each_split_pickup_slot = (int)$split_pickup_slot_duration_time * 60;
			$each_split_pickup_slot = $each_split_pickup_slot != 0 ? $each_split_pickup_slot : "";
		} else {
			$each_split_pickup_slot = (int)$split_pickup_slot_duration_time;
			$each_split_pickup_slot = $each_split_pickup_slot != 0 ? $each_split_pickup_slot : "";
		}
		$enable_custom_splited_pickup_single = !isset($_POST['pickupEnableCustomSplitedTimeSingle']) || $_POST['pickupEnableCustomSplitedTimeSingle'] == "unchecked" ? false : true;
		$enable_custom_pickup_single = !isset($_POST['pickupEnableCustomTimeSingle']) || $_POST['pickupEnableCustomTimeSingle'] == "unchecked" ? false : true;
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['enable_split'] = $enable_custom_pickup_split;
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['split_slot_duration'] = $each_split_pickup_slot;
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['enable_single_splited_slot'] = $enable_custom_splited_pickup_single;
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['enable_single_slot'] = $enable_custom_pickup_single;

		$hide_slot_current_date = !isset($_POST['pickupHideSlotCurrentDate']) || $_POST['pickupHideSlotCurrentDate'] == "unchecked" ? false : true;
		$hide_slot_next_day = !isset($_POST['pickupHideSlotNextDay']) || $_POST['pickupHideSlotNextDay'] == "unchecked" ? false : true;
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['hide_time_slot_current_date'] = $hide_slot_current_date;
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['hide_time_slot_next_day'] = $hide_slot_next_day;

		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['timeslot_closing_time'] = $_POST['pickupslotHidetime'];

		$custom_pickup_maximum_order = sanitize_text_field($_POST['pickupMaxOrder']);
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['max_order'] = $custom_pickup_maximum_order;

		$custom_pickup_slot_fee = sanitize_text_field($_POST['pickupSlotFee']);
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['fee'] = $custom_pickup_slot_fee;

		$custom_pickup_open_specific_date = sanitize_text_field(str_replace(', ', ',', $_POST['pickupOpenSpecificDate']));
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['only_specific_date'] = $custom_pickup_open_specific_date;

		$custom_pickup_close_specific_date = sanitize_text_field(str_replace(', ', ',', $_POST['pickupCloseSpecificDate']));
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['only_specific_date_close'] = $custom_pickup_close_specific_date;

		$disable_for = (isset($_POST['pickupDisableFor']) && !empty($_POST['pickupDisableFor'])) ? $_POST['pickupDisableFor'] : array();
		
		$state_or_zip_selection = sanitize_text_field($_POST['pickupStateOrZipSelection']);

		if($state_or_zip_selection == 'zone') {
			$region_zone_code = (isset($_POST['pickupRegionZoneCode']) && !empty($_POST['pickupRegionZoneCode'])) ? $_POST['pickupRegionZoneCode'] : [];
			$zone_state_code = [];
			$zone_post_code = [];
			$region_shipping_method = [];
			foreach($region_zone_code as $zone_code) {
				
				$zone = new WC_Shipping_Zone($zone_code);

				$zone_locations = $zone->get_zone_locations();
				$zone_locations = $this->helper->objectToArray($zone_locations);
				foreach($zone_locations as $zone_location) {
					if($zone_location['type'] == "state") {
						$position = strpos($zone_location['code'],':');
						$zone_state_code[] = substr($zone_location['code'],($position+1));
					} else if($zone_location['type'] == "postcode") {
						$zone_post_code[] = $zone_location['code'];
					} else if($zone_location['type'] == "country") {
						$zone_state_code[] = $zone_location['code'];
					}
				}
			}

			$region_state_code = $zone_state_code;
			$region_post_code = $zone_post_code;


		} elseif($state_or_zip_selection == 'state') {
			$region_state_code = (isset($_POST['pickupRegionStateCode']) && !empty($_POST['pickupRegionStateCode'])) ? $_POST['pickupRegionStateCode'] : [];
			$region_zone_code = [];
			$region_post_code = [];
			$region_shipping_method = [];

		} elseif($state_or_zip_selection == 'postcode') {
			$region_post_code = (isset($_POST['pickupRegionPostCode']) && !empty($_POST['pickupRegionPostCode'])) ? $_POST['pickupRegionPostCode'] : [];
			$region_zone_code = [];
			$region_state_code = [];
			$region_shipping_method = [];
		} elseif($state_or_zip_selection == 'shipping_method') {
			$region_shipping_method = (isset($_POST['pickupRegionShippingMethod']) && !empty($_POST['pickupRegionShippingMethod'])) ? $_POST['pickupRegionShippingMethod'] : [];
			$region_zone_code = [];
			$region_state_code = [];
			$region_post_code = [];
		} else {
			$region_zone_code = [];
			$region_state_code = [];
			$region_post_code = [];
			$region_shipping_method = [];
		}		

		$custom_pickup_slot_disable = $this->helper->coderockz_woo_delivery_array_sanitize($disable_for);
		$custom_pickup_slot_disable_zone_code = $this->helper->coderockz_woo_delivery_array_sanitize($region_zone_code);
		$custom_pickup_slot_disable_state_code = $this->helper->coderockz_woo_delivery_array_sanitize($region_state_code);
		$custom_pickup_slot_disable_post_code = $this->helper->coderockz_woo_delivery_array_sanitize($region_post_code);
		$custom_pickup_slot_disable_shipping_method = $this->helper->coderockz_woo_delivery_array_sanitize($region_shipping_method);

		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['disable_for'] = $custom_pickup_slot_disable;
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['more_settings'] = $state_or_zip_selection;
		
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['disable_zone'] = $custom_pickup_slot_disable_zone_code;
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['disable_state'] = $custom_pickup_slot_disable_state_code;
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['disable_postcode'] = $custom_pickup_slot_disable_post_code;
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['disable_shipping_method'] = $custom_pickup_slot_disable_shipping_method;

		$hide_location = (isset($_POST['hidePickupLocationArray']) && !empty($_POST['hidePickupLocationArray'])) ? $_POST['hidePickupLocationArray'] : array();
		$hide_categories = (isset($_POST['hidePickupCategoriesArray']) && !empty($_POST['hidePickupCategoriesArray'])) ? $_POST['hidePickupCategoriesArray'] : array();
		$hide_products = (isset($_POST['hidePickupProductsArray']) && !empty($_POST['hidePickupProductsArray'])) ? $_POST['hidePickupProductsArray'] : array();

		$hide_location_array = $this->helper->coderockz_woo_delivery_array_sanitize($hide_location);
		$hide_categories_array = $this->helper->coderockz_woo_delivery_array_sanitize($hide_categories);
		$hide_products_array = $this->helper->coderockz_woo_delivery_array_sanitize($hide_products);

		$time_slot_shown_other_categories_products = !isset($_POST['enablePickupShownOtherCategoriesProducts']) || $_POST['enablePickupShownOtherCategoriesProducts'] == "unchecked" ? false : true;

		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['hide_for_pickup_location'] = $hide_location_array;
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['hide_categories'] = $hide_categories_array;
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['hide_products'] = $hide_products_array;
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['time_slot_shown_other_categories_products'] = $time_slot_shown_other_categories_products;

		$enable_added_custom_pickup = !isset($_POST['pickupEnableAddedCustomTime']) || $_POST['pickupEnableAddedCustomTime'] == "unchecked" ? false : true;
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['enable'] = $enable_added_custom_pickup;

		$custom_pickupslot_name = sanitize_text_field($_POST['pickupSlotName']);
		$pickup_slot[$custom_pickup_slot_starts.'-'.$custom_pickup_slot_ends]['name'] = $custom_pickupslot_name;
		
		if(get_option('coderockz_woo_delivery_pickup_slot_settings') == false) {
			$temp_pickup_slot = [];
			$temp_pickup_slot['time_slot'] = $pickup_slot;
			update_option('coderockz_woo_delivery_pickup_slot_settings', $temp_pickup_slot);
		} else {

			if(isset(get_option('coderockz_woo_delivery_pickup_slot_settings')['time_slot']) && count(get_option('coderockz_woo_delivery_pickup_slot_settings')['time_slot'])>0) {

				$db_custom_pickup_slot = get_option('coderockz_woo_delivery_pickup_slot_settings')['time_slot'];
				if($old_custom_pickup_slot_starts != $custom_pickup_slot_starts || $old_custom_pickup_slot_ends != $custom_pickup_slot_ends) {
					
					if(array_key_exists($old_custom_pickup_slot_starts.'-'.$old_custom_pickup_slot_ends,$db_custom_pickup_slot)) {
						
						unset($db_custom_pickup_slot[$old_custom_pickup_slot_starts.'-'.$old_custom_pickup_slot_ends]);
						
					}
				}

				$pickup_slot = array_merge($db_custom_pickup_slot,$pickup_slot);
				$custom_pickup_form_settings['time_slot'] = $pickup_slot;
			} else {
				$custom_pickup_form_settings['time_slot'] = $pickup_slot;
			}

			$custom_pickup_form_settings = array_merge(get_option('coderockz_woo_delivery_pickup_slot_settings'),$custom_pickup_form_settings);
			update_option('coderockz_woo_delivery_pickup_slot_settings', $custom_pickup_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_process_pickup_location_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $date_form_data );
		$pickup_location_form_settings = [];
		$enable_pickup_location = !isset($date_form_data['coderockz_enable_pickup_location']) ? false : true;
		$pickup_location_mandatory = !isset($date_form_data['coderockz_pickup_location_mandatory']) ? false : true;
		$pickup_location_field_label = sanitize_text_field($date_form_data['coderockz_pickup_location_field_label']);
		$pickup_location_field_placeholder = sanitize_text_field($date_form_data['coderockz_pickup_location_field_placeholder']);

		$pickup_location_popup = !isset($date_form_data['coderockz_woo_delivery_pickup_location_popup']) ? false : true;
		$hide_searchbox_location = !isset($date_form_data['coderockz_hide_searchbox_location_field_dropdown']) ? false : true;

		$pickup_location_popup_heading = sanitize_text_field($date_form_data['coderockz_woo_delivery_pickup_location_popup_heading']);

		$pickup_location_form_settings['enable_pickup_location'] = $enable_pickup_location;
		$pickup_location_form_settings['pickup_location_mandatory'] = $pickup_location_mandatory;
		$pickup_location_form_settings['field_label'] = $pickup_location_field_label;
		$pickup_location_form_settings['field_placeholder'] = $pickup_location_field_placeholder;
		$pickup_location_form_settings['pickup_location_popup'] = $pickup_location_popup;
		$pickup_location_form_settings['pickup_location_popup_heading'] = $pickup_location_popup_heading;
		$pickup_location_form_settings['hide_searchbox'] = $hide_searchbox_location;

		if(get_option('coderockz_woo_delivery_pickup_location_settings') == false) {
			update_option('coderockz_woo_delivery_pickup_location_settings', $pickup_location_form_settings);
		} else {
			$pickup_location_form_settings = array_merge(get_option('coderockz_woo_delivery_pickup_location_settings'),$pickup_location_form_settings);
			update_option('coderockz_woo_delivery_pickup_location_settings', $pickup_location_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_delete_pickup_location() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		$pickup_location_settings = [];
		
		$pickup_location = (isset($_POST['pickupLocation']) && $_POST['pickupLocation'] !="") ? sanitize_text_field($_POST['pickupLocation']) : "";
	
		$db_pickup_location = isset(get_option('coderockz_woo_delivery_pickup_location_settings')['pickup_location']) && !empty(get_option('coderockz_woo_delivery_pickup_location_settings')['pickup_location']) ? get_option('coderockz_woo_delivery_pickup_location_settings')['pickup_location'] : array();

		if(array_key_exists($pickup_location,$db_pickup_location)) {
			unset($db_pickup_location[$pickup_location]);
			$pickup_location_settings['pickup_location'] = $db_pickup_location;
		}

		$pickup_location_settings = array_merge(get_option('coderockz_woo_delivery_pickup_location_settings'),$pickup_location_settings);
		update_option('coderockz_woo_delivery_pickup_location_settings', $pickup_location_settings);
		
		wp_send_json_success();
    }


    public function coderockz_woo_delivery_enable_and_save_pickup_location() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		$pickup_location = [];
		$pickup_location_settings = [];		

		$old_pickup_location = (isset($_POST['pickupOldLocation']) && $_POST['pickupOldLocation'] !="") ? sanitize_text_field($_POST['pickupOldLocation']) : "";
		
		$pickup_location_name = (isset($_POST['pickupLocationName']) && $_POST['pickupLocationName'] !="") ? sanitize_text_field($_POST['pickupLocationName']) : "";
		
		$pickup_location_url = (isset($_POST['pickupLocationUrl']) && $_POST['pickupLocationUrl'] !="") ? sanitize_text_field($_POST['pickupLocationUrl']) : "";
		$pickup_location_email = (isset($_POST['pickupLocationEmail']) && $_POST['pickupLocationEmail'] !="") ? sanitize_text_field($_POST['pickupLocationEmail']) : "";

		$pickup_location_maximum_order = (isset($_POST['pickupLocationMaxOrder']) && $_POST['pickupLocationMaxOrder'] !="") ? sanitize_text_field($_POST['pickupLocationMaxOrder']) : "";
		$pickup_location_fee = (isset($_POST['pickupLocationFee']) && $_POST['pickupLocationFee'] !="") ? sanitize_text_field($_POST['pickupLocationFee']) : "";

		$only_specific_date_close = (isset($_POST['closeSpecificDate']) && $_POST['closeSpecificDate'] !="") ? sanitize_text_field(str_replace(', ', ',', $_POST['closeSpecificDate'])) : "";

		$only_specific_date_show = (isset($_POST['showSpecificDate']) && $_POST['showSpecificDate'] !="") ? sanitize_text_field(str_replace(', ', ',', $_POST['showSpecificDate'])) : "";
		
		$disable_for = (isset($_POST['pickupLocationDisableFor']) && !empty($_POST['pickupLocationDisableFor'])) ? $_POST['pickupLocationDisableFor'] : array();
		$hide_categories = (isset($_POST['hidePickupLocationCategoriesArray']) && !empty($_POST['hidePickupLocationCategoriesArray'])) ? $_POST['hidePickupLocationCategoriesArray'] : array();
		$hide_products = (isset($_POST['hidePickupLocationProductsArray']) && !empty($_POST['hidePickupLocationProductsArray'])) ? $_POST['hidePickupLocationProductsArray'] : array();
		
		$disable_zone = (isset($_POST['regionZoneCode']) && !empty($_POST['regionZoneCode'])) ? $_POST['regionZoneCode'] : array();

		$location_shown_other_categories_products = !isset($_POST['enablePickupLocationShownOtherCategoriesProducts']) || $_POST['enablePickupLocationShownOtherCategoriesProducts'] == "unchecked" ? false : true;
		$enable_pickup_location = !isset($_POST['pickupEnableAddedLocation']) || $_POST['pickupEnableAddedLocation'] == "unchecked" ? false : true;

		$pickup_location_disable = $this->helper->coderockz_woo_delivery_array_sanitize($disable_for);
		$hide_categories_array = $this->helper->coderockz_woo_delivery_array_sanitize($hide_categories);
		$hide_products_array = $this->helper->coderockz_woo_delivery_array_sanitize($hide_products);
		$disable_zone_array = $this->helper->coderockz_woo_delivery_array_sanitize($disable_zone);

		$pickup_location[$pickup_location_name]['location_name'] = $pickup_location_name;
		$pickup_location[$pickup_location_name]['map_url'] = $pickup_location_url;
		$pickup_location[$pickup_location_name]['location_email'] = $pickup_location_email;
		$pickup_location[$pickup_location_name]['max_order'] = $pickup_location_maximum_order;
		$pickup_location[$pickup_location_name]['fee'] = $pickup_location_fee;
		$pickup_location[$pickup_location_name]['only_specific_date_close'] = $only_specific_date_close;
		$pickup_location[$pickup_location_name]['only_specific_date_show'] = $only_specific_date_show;
		$pickup_location[$pickup_location_name]['disable_for'] = $pickup_location_disable;
		$pickup_location[$pickup_location_name]['hide_categories'] = $hide_categories_array;
		$pickup_location[$pickup_location_name]['hide_products'] = $hide_products_array;
		$pickup_location[$pickup_location_name]['location_shown_other_categories_products'] = $location_shown_other_categories_products;
		$pickup_location[$pickup_location_name]['disable_zone'] = $disable_zone_array;
		$pickup_location[$pickup_location_name]['enable'] = $enable_pickup_location;
		
		if(get_option('coderockz_woo_delivery_pickup_location_settings') == false) {
			$temp_pickup_location = [];
			$temp_pickup_location['pickup_location'] = $pickup_location;
			update_option('coderockz_woo_delivery_pickup_location_settings', $temp_pickup_location);
		} else {

			if(isset(get_option('coderockz_woo_delivery_pickup_location_settings')['pickup_location']) && count(get_option('coderockz_woo_delivery_pickup_location_settings')['pickup_location'])>0) {

				$db_pickup_location = get_option('coderockz_woo_delivery_pickup_location_settings')['pickup_location'];
				if($old_pickup_location != $pickup_location_name) {
					
					if(array_key_exists($old_pickup_location,$db_pickup_location)) {
						
						unset($db_pickup_location[$old_pickup_location]);
						
					}
				}

				$pickup_location = array_merge($db_pickup_location,$pickup_location);
				$pickup_location_settings['pickup_location'] = $pickup_location;
			} else {
				$pickup_location_settings['pickup_location'] = $pickup_location;
			}

			$pickup_location_settings = array_merge(get_option('coderockz_woo_delivery_pickup_location_settings'),$pickup_location_settings);
			update_option('coderockz_woo_delivery_pickup_location_settings', $pickup_location_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_tips_settings_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $delivery_tips_form_data );
		
		$delivery_tips_form_settings = [];

		$enable_delivery_tips = !isset($delivery_tips_form_data['coderockz_woo_delivery_enable_delivery_tips']) ? false : true;
		$delivery_tips_form_settings['enable_delivery_tips'] = $enable_delivery_tips;

		$delivery_tips_required = !isset($delivery_tips_form_data['coderockz_woo_delivery_delivery_tips_required']) ? false : true;
		$delivery_tips_form_settings['delivery_tips_required'] = $delivery_tips_required;

		$delivery_tips_field_label = (isset($delivery_tips_form_data['coderockz_woo_delivery_tips_field_label']) && $delivery_tips_form_data['coderockz_woo_delivery_tips_field_label'] !="") ? sanitize_text_field($delivery_tips_form_data['coderockz_woo_delivery_tips_field_label']) : "";
		$delivery_tips_form_settings['delivery_tips_field_label'] = $delivery_tips_field_label;

		$enable_delivery_tips_dropdown = !isset($delivery_tips_form_data['coderockz_woo_delivery_enable_delivery_tips_dropdown']) ? false : true;
		$delivery_tips_form_settings['enable_delivery_tips_dropdown'] = $enable_delivery_tips_dropdown;

		if($enable_delivery_tips_dropdown) {
			$delivery_tips_dropdown_value = isset($delivery_tips_form_data['coderockz_woo_delivery_tips_dropdown_value']) && $delivery_tips_form_data['coderockz_woo_delivery_tips_dropdown_value'] != "" ? $delivery_tips_form_data['coderockz_woo_delivery_tips_dropdown_value'] : "";
			$delivery_tips_dropdown_value = str_replace(', ', ',', $delivery_tips_dropdown_value);
			$delivery_tips_dropdown_value = explode(",",$delivery_tips_dropdown_value);
			$delivery_tips_dropdown_value = $this->helper->coderockz_woo_delivery_array_sanitize($delivery_tips_dropdown_value);

			$precentage_rounding = sanitize_text_field($delivery_tips_form_data['coderockz_woo_delivery_delivery_tips_precentage_rounding']);
			$percentage_calculating_include_fees = !isset($delivery_tips_form_data['coderockz_woo_delivery_tips_percentage_fees']) ? false : true;
			$percentage_calculating_include_shipping_cost = !isset($delivery_tips_form_data['coderockz_woo_delivery_tips_percentage_calculate_shipping']) ? false : true;
			$percentage_calculating_include_tax = !isset($delivery_tips_form_data['coderockz_woo_delivery_tips_percentage_calculate_tax']) ? false : true;
			$percentage_calculating_include_discount = !isset($delivery_tips_form_data['coderockz_woo_delivery_tips_percentage_calculate_discount']) ? false : true;
			$enable_input_field_dropdown = !isset($delivery_tips_form_data['coderockz_woo_delivery_enable_input_field_dropdown']) ? false : true;

		} else {
			$delivery_tips_dropdown_value = [];
			$precentage_rounding = "no_round";
			$percentage_calculating_include_fees = false;
			$percentage_calculating_include_shipping_cost = false;
			$percentage_calculating_include_tax = false;
			$percentage_calculating_include_discount = false;
			$enable_input_field_dropdown = false;

		}

		$delivery_tips_form_settings['delivery_tips_dropdown_value'] = $delivery_tips_dropdown_value;
		$delivery_tips_form_settings['precentage_rounding'] = $precentage_rounding;
		$delivery_tips_form_settings['percentage_calculating_include_fees'] = $percentage_calculating_include_fees;
		$delivery_tips_form_settings['percentage_calculating_include_shipping_cost'] = $percentage_calculating_include_shipping_cost;
		$delivery_tips_form_settings['percentage_calculating_include_tax'] = $percentage_calculating_include_tax;
		$delivery_tips_form_settings['percentage_calculating_include_discount'] = $percentage_calculating_include_discount;
		$delivery_tips_form_settings['enable_input_field_dropdown'] = $enable_input_field_dropdown;

		$add_tax = !isset($delivery_tips_form_data['coderockz_woo_delivery_tips_add_tax']) ? false : true;
		$delivery_tips_form_settings['add_tax'] = $add_tax;

		if(get_option('coderockz_woo_delivery_delivery_tips_settings') == false) {
			update_option('coderockz_woo_delivery_delivery_tips_settings', $delivery_tips_form_settings);
		} else {
			$delivery_tips_form_settings = array_merge(get_option('coderockz_woo_delivery_delivery_tips_settings'),$delivery_tips_form_settings);
			update_option('coderockz_woo_delivery_delivery_tips_settings', $delivery_tips_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_overall_processing_days_settings_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $processing_days_form_data );
		$processing_days_form_settings = [];
		$overall_processing_days = sanitize_text_field($processing_days_form_data['coderockz_delivery_overall_processing_days']);
		$processing_days_form_settings['overall_processing_days'] = $overall_processing_days;

		$overall_processing_days_pickup = sanitize_text_field($processing_days_form_data['coderockz_delivery_overall_processing_days_pickup']);
		$processing_days_form_settings['overall_processing_days_pickup'] = $overall_processing_days_pickup;

		$backorder_processing_days = sanitize_text_field($processing_days_form_data['coderockz_delivery_backorder_processing_days']);
		$processing_days_form_settings['backorder_processing_days'] = $backorder_processing_days;

		$exclude_categories = (isset($processing_days_form_data['coderockz_woo_delivery_exclude_categories_processing_days']) && !empty($processing_days_form_data['coderockz_woo_delivery_exclude_categories_processing_days'])) ? $processing_days_form_data['coderockz_woo_delivery_exclude_categories_processing_days'] : array();
		$exclude_categories = $this->helper->coderockz_woo_delivery_array_sanitize($exclude_categories);

		if(get_option('coderockz_woo_delivery_large_product_list') == false) {
			$exclude_product = (isset($processing_days_form_data['coderockz_woo_delivery_exclude_product_processing_days']) && !empty($processing_days_form_data['coderockz_woo_delivery_exclude_product_processing_days'])) ? $processing_days_form_data['coderockz_woo_delivery_exclude_product_processing_days'] : array();
		$exclude_product = $this->helper->coderockz_woo_delivery_array_sanitize($exclude_product);
		} else {
			$exclude_product = isset($processing_days_form_data['coderockz_woo_delivery_exclude_product_input_processing_days']) && $processing_days_form_data['coderockz_woo_delivery_exclude_product_input_processing_days'] != "" ? $processing_days_form_data['coderockz_woo_delivery_exclude_product_input_processing_days'] : "";
			$exclude_product = explode(",",$exclude_product);
			$exclude_product = $this->helper->coderockz_woo_delivery_array_sanitize($exclude_product);
		}

		$processing_days_form_settings['exclude_categories'] = $exclude_categories;
		$processing_days_form_settings['exclude_products'] = $exclude_product;


		if(get_option('coderockz_woo_delivery_processing_days_settings') == false) {
			update_option('coderockz_woo_delivery_processing_days_settings', $processing_days_form_settings);
		} else {
			$processing_days_form_settings = array_merge(get_option('coderockz_woo_delivery_processing_days_settings'),$processing_days_form_settings);
			update_option('coderockz_woo_delivery_processing_days_settings', $processing_days_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_processing_days_settings_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $processing_days_form_data );

		$consider_off_days = !isset($processing_days_form_data['coderockz_delivery_date_processing_days_off_days']) ? false : true;
		$consider_weekend = !isset($processing_days_form_data['coderockz_delivery_date_processing_days_weekend_days']) ? false : true;
		$consider_current_day = !isset($processing_days_form_data['coderockz_delivery_date_processing_days_current_day']) ? false : true;
		$processing_days_form_settings['processing_days_consider_off_days'] = $consider_off_days;
		$processing_days_form_settings['processing_days_consider_weekends'] = $consider_weekend;
		$processing_days_form_settings['processing_days_consider_current_day'] = $consider_current_day;

		if(get_option('coderockz_woo_delivery_processing_days_settings') == false) {
			update_option('coderockz_woo_delivery_processing_days_settings', $processing_days_form_settings);
		} else {
			$processing_days_form_settings = array_merge(get_option('coderockz_woo_delivery_processing_days_settings'),$processing_days_form_settings);
			update_option('coderockz_woo_delivery_processing_days_settings', $processing_days_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_category_processing_days_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $processing_days_form_data );
		$processing_days = [];
		$processing_days_categories = $this->helper->coderockz_woo_delivery_array_sanitize($processing_days_form_data['coderockz_delivery_processing_days_categories']);
		foreach($processing_days_categories as $processing_days_category) {
			$category = str_replace("c-w-d"," ", $processing_days_category);
			
			if(isset($processing_days_form_data['coderockz-woo-delivery-processing-days-'.$processing_days_category]) && !empty($processing_days_form_data['coderockz-woo-delivery-processing-days-'.$processing_days_category]) && $category != "") {

			$processing_days[$category] = sanitize_text_field($processing_days_form_data['coderockz-woo-delivery-processing-days-'.$processing_days_category]);

			}
		}
		$enable_category_wise_processing_days = !isset($processing_days_form_data['coderockz_woo_delivery_category_wise_processing_days']) ? false : true;

		$processing_days_form_settings['enable_category_wise_processing_days'] = $enable_category_wise_processing_days;
		$processing_days_form_settings['category_processing_days'] = $processing_days;

		if(get_option('coderockz_woo_delivery_processing_days_settings') == false) {
			update_option('coderockz_woo_delivery_processing_days_settings', $processing_days_form_settings);
		} else {
			$processing_days_form_settings = array_merge(get_option('coderockz_woo_delivery_processing_days_settings'),$processing_days_form_settings);
			update_option('coderockz_woo_delivery_processing_days_settings', $processing_days_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_product_processing_days_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $processing_days_form_data );
		$processing_days = [];
		if(get_option('coderockz_woo_delivery_large_product_list') == false) {
			$processing_days_products = $this->helper->coderockz_woo_delivery_array_sanitize($processing_days_form_data['coderockz_delivery_processing_days_products']);
		} else {

			$processing_days_products = $this->helper->coderockz_woo_delivery_array_sanitize($_POST['processingdaysProduct']);
		}
		
		foreach($processing_days_products as $processing_days_product) {


			if(isset($processing_days_form_data['coderockz-woo-delivery-product-processing-days-'.$processing_days_product]) && !empty($processing_days_form_data['coderockz-woo-delivery-product-processing-days-'.$processing_days_product]) && $processing_days_product != "") {

			$processing_days[$processing_days_product] = sanitize_text_field($processing_days_form_data['coderockz-woo-delivery-product-processing-days-'.$processing_days_product]);

			}
		}

		$enable_product_wise_processing_days = !isset($processing_days_form_data['coderockz_woo_delivery_product_wise_processing_days']) ? false : true;
		$enable_product_processing_day_quantity = !isset($processing_days_form_data['coderockz_woo_delivery_product_processing_day_quantity']) ? false : true;

		$processing_days_form_settings['enable_product_wise_processing_days'] = $enable_product_wise_processing_days;
		$processing_days_form_settings['enable_product_processing_day_quantity'] = $enable_product_processing_day_quantity;
		$processing_days_form_settings['product_processing_days'] = $processing_days;

		if(get_option('coderockz_woo_delivery_processing_days_settings') == false) {
			update_option('coderockz_woo_delivery_processing_days_settings', $processing_days_form_settings);
		} else {
			$processing_days_form_settings = array_merge(get_option('coderockz_woo_delivery_processing_days_settings'),$processing_days_form_settings);
			update_option('coderockz_woo_delivery_processing_days_settings', $processing_days_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_process_weekday_wise_processing_days() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $date_form_data );
		
		$weekday_processing_days_settings = [];
		$weekday_delivery_weekday_processing_days_settings = [];

		$enable_weekday_wise_processing_days = !isset($date_form_data['coderockz_woo_delivery_enable_weekday_wise_processing_days']) ? false : true;
		$weekday_wise_processing_days = [];

		$weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
        foreach ($weekday as $key => $value) { 
	
			$weekday_wise_processing_days_[$key] = (isset($date_form_data['coderockz_woo_delivery_weekday_wise_processing_days_'.$key]) && $date_form_data['coderockz_woo_delivery_weekday_wise_processing_days_'.$key] !="") ? sanitize_text_field($date_form_data['coderockz_woo_delivery_weekday_wise_processing_days_'.$key]) : ""; 

			$weekday_wise_processing_days[$key] = $weekday_wise_processing_days_[$key];
        }

        $weekday_processing_days_settings['enable_weekday_wise_processing_days'] = $enable_weekday_wise_processing_days;
		$weekday_processing_days_settings['weekday_wise_processing_days'] = $weekday_wise_processing_days;

		if(get_option('coderockz_woo_delivery_processing_days_settings') == false) {
			update_option('coderockz_woo_delivery_processing_days_settings', $weekday_processing_days_settings);
		} else {
			$weekday_processing_days_settings = array_merge(get_option('coderockz_woo_delivery_processing_days_settings'),$weekday_processing_days_settings);
			update_option('coderockz_woo_delivery_processing_days_settings', $weekday_processing_days_settings);
		}

		wp_send_json_success();
		
	}

	public function coderockz_woo_delivery_zone_wise_processingdays_form() {

		check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $processing_days_form_data );
		$processing_days = [];
		$processing_days_zone = $this->helper->coderockz_woo_delivery_array_sanitize($processing_days_form_data['coderockz_delivery_zone_wise_processingdays_zone']);
		foreach($processing_days_zone as $zone) {

			if(isset($processing_days_form_data['coderockz-delivery-zone-wise-processingdays-zone-day-'.$zone]) && !empty($processing_days_form_data['coderockz-delivery-zone-wise-processingdays-zone-day-'.$zone]) && $zone != "" && $zone != 0) {

			$processing_days[$zone] = sanitize_text_field($processing_days_form_data['coderockz-delivery-zone-wise-processingdays-zone-day-'.$zone]);

			}
		}

		$processing_days_form_settings['zone_wise_processing_days'] = $processing_days;

		if(get_option('coderockz_woo_delivery_processing_days_settings') == false) {
			update_option('coderockz_woo_delivery_processing_days_settings', $processing_days_form_settings);
		} else {
			$processing_days_form_settings = array_merge(get_option('coderockz_woo_delivery_processing_days_settings'),$processing_days_form_settings);
			update_option('coderockz_woo_delivery_processing_days_settings', $processing_days_form_settings);
		}

		wp_send_json_success();

	}

	public function coderockz_woo_delivery_zone_wise_processingtime_form() {

		check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $processing_time_form_data );
		$processing_time = [];
		$processing_time_zone = $this->helper->coderockz_woo_delivery_array_sanitize($processing_time_form_data['coderockz_delivery_zone_wise_processingtime_zone']);
		foreach($processing_time_zone as $zone) {

			if(isset($processing_time_form_data['coderockz-delivery-zone-wise-processingtime-zone-day-'.$zone]) && !empty($processing_time_form_data['coderockz-delivery-zone-wise-processingtime-zone-day-'.$zone]) && $zone != "" && $zone != 0) {

			$processing_time[$zone] = sanitize_text_field($processing_time_form_data['coderockz-delivery-zone-wise-processingtime-zone-day-'.$zone]);

			}
		}

		$processing_time_form_settings['zone_wise_processing_time'] = $processing_time;

		if(get_option('coderockz_woo_delivery_processing_time_settings') == false) {
			update_option('coderockz_woo_delivery_processing_time_settings', $processing_time_form_settings);
		} else {
			$processing_time_form_settings = array_merge(get_option('coderockz_woo_delivery_processing_time_settings'),$processing_time_form_settings);
			update_option('coderockz_woo_delivery_processing_time_settings', $processing_time_form_settings);
		}

		wp_send_json_success();

	}

	public function coderockz_woo_delivery_shippingmethod_wise_processingdays_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $shippingmethod_processingdays_form_data );

		$shippingmethod_processingdays['delivery'] = [];
		$shippingmethod_processingdays['pickup'] = [];
		$shippingmethod_processingdays_methods = $this->helper->coderockz_woo_delivery_array_sanitize($shippingmethod_processingdays_form_data['coderockz_delivery_shippingmethod_wise_processingdays_shippingmethod']);

		foreach($shippingmethod_processingdays_methods as $shippingmethod_processingdays_method) {

			$method_code = $shippingmethod_processingdays_method;

			if(!isset($delivery_completed[$shippingmethod_processingdays_method]) && isset($shippingmethod_processingdays_form_data['coderockz-delivery-shippingmethod-wise-processingdays-shippingmethod-day-'.$shippingmethod_processingdays_method.'-delivery'])) {

				$day_delivery = sanitize_text_field($shippingmethod_processingdays_form_data['coderockz-delivery-shippingmethod-wise-processingdays-shippingmethod-day-'.$shippingmethod_processingdays_method.'-delivery']);

				if(!empty($day_delivery) && $method_code != "") {

					$shippingmethod_processingdays['delivery'][$method_code] = $day_delivery;

					$delivery_completed[$shippingmethod_processingdays_method] = 'completed';

				}
			}

			if(!isset($pickup_completed[$shippingmethod_processingdays_method]) && isset($shippingmethod_processingdays_form_data['coderockz-delivery-shippingmethod-wise-processingdays-shippingmethod-day-'.$shippingmethod_processingdays_method.'-pickup'])) {
				
				$day_pickup = sanitize_text_field($shippingmethod_processingdays_form_data['coderockz-delivery-shippingmethod-wise-processingdays-shippingmethod-day-'.$shippingmethod_processingdays_method.'-pickup']);

				if(!empty($day_pickup) && $method_code != "") {

					$shippingmethod_processingdays['pickup'][$method_code] = $day_pickup;

					$pickup_completed[$shippingmethod_processingdays_method] = 'completed';

				}
			}

		}


		$shippingmethod_processingdays_form_settings['shippingmethod_wise_processingdays'] = $shippingmethod_processingdays;

		if(get_option('coderockz_woo_delivery_processing_days_settings') == false) {
			update_option('coderockz_woo_delivery_processing_days_settings', $shippingmethod_processingdays_form_settings);
		} else {
			$shippingmethod_processingdays_form_settings = array_merge(get_option('coderockz_woo_delivery_processing_days_settings'),$shippingmethod_processingdays_form_settings);
			update_option('coderockz_woo_delivery_processing_days_settings', $shippingmethod_processingdays_form_settings);
		}

		wp_send_json_success();
	}


	public function coderockz_woo_delivery_specific_date_coupon_form() {

		check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $specific_date_coupon_form_data );
		$specific_date_coupon = [];
		$specific_date_coupon_coupon = $this->helper->coderockz_woo_delivery_array_sanitize($specific_date_coupon_form_data['coderockz_delivery_date_wise_coupon_date']);

		foreach($specific_date_coupon_coupon as $coupon) {

			if(isset($specific_date_coupon_form_data['coderockz-delivery-date-wise-coupon-date-day-'.$coupon]) && !empty($specific_date_coupon_form_data['coderockz-delivery-date-wise-coupon-date-day-'.$coupon]) && $coupon != "") {

				if(strpos($specific_date_coupon_form_data['coderockz-delivery-date-wise-coupon-date-day-'.$coupon], '...') !== false) {

					$temporary_dates = explode(',', str_replace(' ', '', sanitize_text_field($specific_date_coupon_form_data['coderockz-delivery-date-wise-coupon-date-day-'.$coupon])));
					$dates = [];
					foreach($temporary_dates as $temporary_date) {
						if(strpos($temporary_date, '...') !== false) {
							$filtered_dates = explode('...', $temporary_date);
						    $period = $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1]);
							$dates = array_merge($dates,$period);
						} else {
							$dates[] = $temporary_date;
						}
					}
					
				    $specific_date_coupon[$coupon]['type'] = 'range';
				    $specific_date_coupon[$coupon]['range_short'] = str_replace(' ', '', sanitize_text_field($specific_date_coupon_form_data['coderockz-delivery-date-wise-coupon-date-day-'.$coupon]));
				    $specific_date_coupon[$coupon]['range_value'] = $dates;

				} else {
					$dates = explode(',', str_replace(' ', '', sanitize_text_field($specific_date_coupon_form_data['coderockz-delivery-date-wise-coupon-date-day-'.$coupon])));
					$specific_date_coupon[$coupon]['range_value'] = $dates;
				}

			}
		}

		$specific_date_coupon_form_settings['specific_date_coupon'] = $specific_date_coupon;

		if(get_option('coderockz_woo_delivery_fee_settings') == false) {
			update_option('coderockz_woo_delivery_fee_settings', $specific_date_coupon_form_settings);
		} else {
			$specific_date_coupon_form_settings = array_merge(get_option('coderockz_woo_delivery_fee_settings'),$specific_date_coupon_form_settings);
			update_option('coderockz_woo_delivery_fee_settings', $specific_date_coupon_form_settings);
		}

		wp_send_json_success();

	}


	public function coderockz_woo_delivery_processing_time_settings_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'processingTimeFormData' ], $processing_time_form_data );
		$processing_time_form_settings = [];
		$disable_timeslot_with_processing_time = !isset($processing_time_form_data['coderockz_woo_delivery_processing_time_disable_timeslot_with_processing_time']) ? false : true;
		$overall_processing_time = (isset($processing_time_form_data['coderockz_woo_delivery_overall_processing_time']) && $processing_time_form_data['coderockz_woo_delivery_overall_processing_time'] !="") ? sanitize_text_field($processing_time_form_data['coderockz_woo_delivery_overall_processing_time']) : "0";
		$overall_processing_time_format = sanitize_text_field($processing_time_form_data['coderockz_woo_delivery_overall_processing_time_format']);

		if($overall_processing_time_format == "hour") {
			$processing_time_minutes = (int)$overall_processing_time * 60;
			$processing_time_minutes = $processing_time_minutes != 0 ? $processing_time_minutes : "";
		} else {
			$processing_time_minutes = (int)$overall_processing_time;
			$processing_time_minutes = $processing_time_minutes != 0 ? $processing_time_minutes : "";
		}

		$overall_processing_time_pickup = (isset($processing_time_form_data['coderockz_woo_delivery_overall_processing_time_pickup']) && $processing_time_form_data['coderockz_woo_delivery_overall_processing_time_pickup'] !="") ? sanitize_text_field($processing_time_form_data['coderockz_woo_delivery_overall_processing_time_pickup']) : "0";
		$overall_processing_time_pickup_format = sanitize_text_field($processing_time_form_data['coderockz_woo_delivery_overall_processing_time_pickup_format']);

		if($overall_processing_time_pickup_format == "hour") {
			$processing_time_minutes_pickup = (int)$overall_processing_time_pickup * 60;
			$processing_time_minutes_pickup = $processing_time_minutes_pickup != 0 ? $processing_time_minutes_pickup : "";
		} else {
			$processing_time_minutes_pickup = (int)$overall_processing_time_pickup;
			$processing_time_minutes_pickup = $processing_time_minutes_pickup != 0 ? $processing_time_minutes_pickup : "";
		}		
		
		$exclude_categories = (isset($processing_time_form_data['coderockz_woo_delivery_exclude_categories_processing_time']) && !empty($processing_time_form_data['coderockz_woo_delivery_exclude_categories_processing_time'])) ? $processing_time_form_data['coderockz_woo_delivery_exclude_categories_processing_time'] : array();
		$exclude_categories = $this->helper->coderockz_woo_delivery_array_sanitize($exclude_categories);
		
		if(get_option('coderockz_woo_delivery_large_product_list') == false) {
			$exclude_product = (isset($processing_time_form_data['coderockz_woo_delivery_exclude_product_processing_time']) && !empty($processing_time_form_data['coderockz_woo_delivery_exclude_product_processing_time'])) ? $processing_time_form_data['coderockz_woo_delivery_exclude_product_processing_time'] : array();
		$exclude_product = $this->helper->coderockz_woo_delivery_array_sanitize($exclude_product);
		} else {
			$exclude_product = isset($processing_time_form_data['coderockz_woo_delivery_exclude_product_input_processing_time']) && $processing_time_form_data['coderockz_woo_delivery_exclude_product_input_processing_time'] != "" ? $processing_time_form_data['coderockz_woo_delivery_exclude_product_input_processing_time'] : "";
			$exclude_product = explode(",",$exclude_product);
			$exclude_product = $this->helper->coderockz_woo_delivery_array_sanitize($exclude_product);
		}

		$processing_time_form_settings['exclude_categories'] = $exclude_categories;
		$processing_time_form_settings['exclude_products'] = $exclude_product;

		$processing_time_form_settings['disable_timeslot_with_processing_time'] = $disable_timeslot_with_processing_time;
		$processing_time_form_settings['overall_processing_time'] = (string)$processing_time_minutes;
		$processing_time_form_settings['overall_processing_time_pickup'] = (string)$processing_time_minutes_pickup;

		if(get_option('coderockz_woo_delivery_processing_time_settings') == false) {
			update_option('coderockz_woo_delivery_processing_time_settings', $processing_time_form_settings);
		} else {
			$processing_time_form_settings = array_merge(get_option('coderockz_woo_delivery_processing_time_settings'),$processing_time_form_settings);
			update_option('coderockz_woo_delivery_processing_time_settings', $processing_time_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_category_processing_time_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'categoryProcessingTimeFormData' ], $category_processing_time_form_data );
		$processing_time = [];
		$processing_time_categories = $this->helper->coderockz_woo_delivery_array_sanitize($category_processing_time_form_data['coderockz_delivery_processing_time_categories']);
		foreach($processing_time_categories as $processing_time_category) {
			$category = str_replace("c-w-d"," ", $processing_time_category);

			$processing_time_duration = (isset($category_processing_time_form_data['coderockz_woo_delivery_category_processing_time-'.$processing_time_category]) && $category_processing_time_form_data['coderockz_woo_delivery_category_processing_time-'.$processing_time_category] !="") ? sanitize_text_field($category_processing_time_form_data['coderockz_woo_delivery_category_processing_time-'.$processing_time_category]) : "0";
			$processing_time_format = (isset($category_processing_time_form_data['coderockz_woo_delivery_category_processing_time_format-'.$processing_time_category]) && $category_processing_time_form_data['coderockz_woo_delivery_category_processing_time_format-'.$processing_time_category] !="") ? sanitize_text_field($category_processing_time_form_data['coderockz_woo_delivery_category_processing_time_format-'.$processing_time_category]) : "";

			if($processing_time_format == "hour") {
				$processing_time_minutes = (int)$processing_time_duration * 60;
				$processing_time_minutes = $processing_time_minutes != 0 ? $processing_time_minutes : "";
			} elseif($processing_time_format == "min") {
				$processing_time_minutes = (int)$processing_time_duration;
				$processing_time_minutes = $processing_time_minutes != 0 ? $processing_time_minutes : "";
			} else {
				$processing_time_minutes = "";
			}

			if($processing_time_minutes != "" && $category != "") {
				$processing_time[$category] = (string)$processing_time_minutes;
			}
			
		}
		$enable_category_wise_processing_time = !isset($category_processing_time_form_data['coderockz_woo_delivery_category_wise_processing_time']) ? false : true;

		$processing_time_form_settings['enable_category_wise_processing_time'] = $enable_category_wise_processing_time;
		$processing_time_form_settings['category_processing_time'] = $processing_time;

		if(get_option('coderockz_woo_delivery_processing_time_settings') == false) {
			update_option('coderockz_woo_delivery_processing_time_settings', $processing_time_form_settings);
		} else {
			$processing_time_form_settings = array_merge(get_option('coderockz_woo_delivery_processing_time_settings'),$processing_time_form_settings);
			update_option('coderockz_woo_delivery_processing_time_settings', $processing_time_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_product_processing_time_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'productProcessingTimeFormData' ], $product_processing_time_form_data );
		$processing_time = [];

		if(get_option('coderockz_woo_delivery_large_product_list') == false) {
			$processing_time_products = $this->helper->coderockz_woo_delivery_array_sanitize($product_processing_time_form_data['coderockz_delivery_processing_time_products']);
		} else {
			$processing_time_products = $this->helper->coderockz_woo_delivery_array_sanitize($_POST['processingtimeProduct']);
		}

		foreach($processing_time_products as $processing_time_product) {

			$processing_time_duration = (isset($product_processing_time_form_data['coderockz_woo_delivery_product_processing_time-'.$processing_time_product]) && $product_processing_time_form_data['coderockz_woo_delivery_product_processing_time-'.$processing_time_product] !="") ? sanitize_text_field($product_processing_time_form_data['coderockz_woo_delivery_product_processing_time-'.$processing_time_product]) : "0";
			$processing_time_format = sanitize_text_field($product_processing_time_form_data['coderockz_woo_delivery_product_processing_time_format-'.$processing_time_product]);

			if($processing_time_format == "hour") {
				$processing_time_minutes = (int)$processing_time_duration * 60;
				$processing_time_minutes = $processing_time_minutes != 0 ? $processing_time_minutes : "";
			} else {
				$processing_time_minutes = (int)$processing_time_duration;
				$processing_time_minutes = $processing_time_minutes != 0 ? $processing_time_minutes : "";
			}

			if($processing_time_minutes != "" && $processing_time_product != "") {

				$processing_time[$processing_time_product] = (string)$processing_time_minutes;

			}
		}

		$enable_product_wise_processing_time = !isset($product_processing_time_form_data['coderockz_woo_delivery_product_wise_processing_time']) ? false : true;
		$enable_product_processing_time_quantity = !isset($product_processing_time_form_data['coderockz_woo_delivery_product_processing_time_quantity']) ? false : true;

		$processing_time_form_settings['enable_product_wise_processing_time'] = $enable_product_wise_processing_time;
		$processing_time_form_settings['enable_product_processing_time_quantity'] = $enable_product_processing_time_quantity;
		$processing_time_form_settings['product_processing_time'] = $processing_time;

		if(get_option('coderockz_woo_delivery_processing_time_settings') == false) {
			update_option('coderockz_woo_delivery_processing_time_settings', $processing_time_form_settings);
		} else {
			$processing_time_form_settings = array_merge(get_option('coderockz_woo_delivery_processing_time_settings'),$processing_time_form_settings);
			update_option('coderockz_woo_delivery_processing_time_settings', $processing_time_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_weekday_processing_time_settings() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'formData' ], $date_form_data );
		
		$weekday_processing_time_settings = [];
		$weekday_delivery_weekday_processing_time_settings = [];

		$enable_weekday_wise_processing_time = !isset($date_form_data['coderockz_woo_delivery_enable_weekday_wise_processing_time']) ? false : true;
		$weekday_wise_processing_time = [];

		$weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
        foreach ($weekday as $key => $value) { 
	
			$weekday_wise_processing_time_[$key] = (isset($date_form_data['coderockz_woo_delivery_weekday_wise_processing_time_'.$key]) && $date_form_data['coderockz_woo_delivery_weekday_wise_processing_time_'.$key] !="") ? sanitize_text_field($date_form_data['coderockz_woo_delivery_weekday_wise_processing_time_'.$key]) : ""; 

			$weekday_wise_processing_time[$key] = $weekday_wise_processing_time_[$key];
        }

        $weekday_processing_time_settings['enable_weekday_wise_processing_time'] = $enable_weekday_wise_processing_time;
		$weekday_processing_time_settings['weekday_wise_processing_time'] = $weekday_wise_processing_time;

		if(get_option('coderockz_woo_delivery_processing_time_settings') == false) {
			update_option('coderockz_woo_delivery_processing_time_settings', $weekday_processing_time_settings);
		} else {
			$weekday_processing_time_settings = array_merge(get_option('coderockz_woo_delivery_processing_time_settings'),$weekday_processing_time_settings);
			update_option('coderockz_woo_delivery_processing_time_settings', $weekday_processing_time_settings);
		}

		wp_send_json_success();
		
	}

	public function coderockz_woo_delivery_process_time_slot_fee() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $date_form_data );
		$delivery_time_slot_fee_settings = [];
		$time_slot_fees=[];
		$enable_delivery_time_slot_fee = !isset($date_form_data['coderockz_delivery_date_enable_time_slot_fee']) ? false : true;

		$delivery_time_slot_fees = $this->helper->coderockz_woo_delivery_array_sanitize($date_form_data['coderockz_delivery_time_slot']);
	
		foreach($delivery_time_slot_fees as $delivery_time_slot_fee) {
			if($delivery_time_slot_fee != "") {
				$time_slot_fees[$delivery_time_slot_fee] = sanitize_text_field($date_form_data['coderockz-woo-delivery-time-slot-fee-'.$delivery_time_slot_fee]);
			}
			
		}
		$delivery_time_slot_fee_settings['time_slot_fee'] = $time_slot_fees;
		$delivery_time_slot_fee_settings['enable_time_slot_fee'] = $enable_delivery_time_slot_fee;
		
		if(get_option('coderockz_woo_delivery_fee_settings') == false) {
			update_option('coderockz_woo_delivery_fee_settings', $delivery_time_slot_fee_settings);
		} else {
			$delivery_time_slot_fee_settings = array_merge(get_option('coderockz_woo_delivery_fee_settings'),$delivery_time_slot_fee_settings);
			update_option('coderockz_woo_delivery_fee_settings', $delivery_time_slot_fee_settings);
		}

		wp_send_json_success();
		
	}

	public function coderockz_woo_delivery_process_pickup_slot_fee() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $date_form_data );
		$delivery_pickup_slot_fee_settings = [];
		$pickup_slot_fees=[];
		$enable_delivery_pickup_slot_fee = !isset($date_form_data['coderockz_delivery_date_enable_pickup_slot_fee']) ? false : true;

		$delivery_pickup_slot_fees = $this->helper->coderockz_woo_delivery_array_sanitize($date_form_data['coderockz_delivery_pickup_slot']);
		
		foreach($delivery_pickup_slot_fees as $delivery_pickup_slot_fee) {
			if($delivery_pickup_slot_fee != "") {
				$pickup_slot_fees[$delivery_pickup_slot_fee] = sanitize_text_field($date_form_data['coderockz-woo-delivery-pickup-slot-fee-'.$delivery_pickup_slot_fee]);
			}
		}

		$delivery_pickup_slot_fee_settings['pickup_slot_fee'] = $pickup_slot_fees;

		$delivery_pickup_slot_fee_settings['enable_pickup_slot_fee'] = $enable_delivery_pickup_slot_fee;
		
		if(get_option('coderockz_woo_delivery_fee_settings') == false) {
			update_option('coderockz_woo_delivery_fee_settings', $delivery_pickup_slot_fee_settings);
		} else {
			$delivery_pickup_slot_fee_settings = array_merge(get_option('coderockz_woo_delivery_fee_settings'),$delivery_pickup_slot_fee_settings);
			update_option('coderockz_woo_delivery_fee_settings', $delivery_pickup_slot_fee_settings);
		}

		wp_send_json_success();
		
	}

	public function coderockz_woo_delivery_conditional_delivery_fee_settings_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'formData' ], $conditional_delivery_fee_data );

		$conditional_delivery_fee_settings = [];
		$enable_conditional_delivery_fee = !isset($conditional_delivery_fee_data['coderockz_delivery_enable_conditional_delivery_fee']) ? false : true;
		$conditional_delivery_fee = (isset($conditional_delivery_fee_data['coderockz_delivery_conditional_delivery_fee']) && $conditional_delivery_fee_data['coderockz_delivery_conditional_delivery_fee'] !="") ? sanitize_text_field($conditional_delivery_fee_data['coderockz_delivery_conditional_delivery_fee']) : "";
		$conditional_delivery_shipping_method = (isset($conditional_delivery_fee_data['coderockz_woo_delivery_conditional_delivery_shipping_method']) && $conditional_delivery_fee_data['coderockz_woo_delivery_conditional_delivery_shipping_method'] !="") ? sanitize_text_field($conditional_delivery_fee_data['coderockz_woo_delivery_conditional_delivery_shipping_method']) : "";
		$conditional_delivery_fee_time = (isset($conditional_delivery_fee_data['coderockz_woo_delivery_conditional_delivery_fee_time']) && $conditional_delivery_fee_data['coderockz_woo_delivery_conditional_delivery_fee_time'] !="") ? sanitize_text_field($conditional_delivery_fee_data['coderockz_woo_delivery_conditional_delivery_fee_time']) : "0";
		$conditional_delivery_fee_time_format = sanitize_text_field($conditional_delivery_fee_data['coderockz_woo_delivery_conditional_delivery_fee_format']);

		if($conditional_delivery_fee_time_format == "hour") {
			$conditional_delivery_fee_time = (int)$conditional_delivery_fee_time * 60;
			$conditional_delivery_fee_time = $conditional_delivery_fee_time != 0 ? $conditional_delivery_fee_time : "";
		} else {
			$conditional_delivery_fee_time = (int)$conditional_delivery_fee_time;
			$conditional_delivery_fee_time = $conditional_delivery_fee_time != 0 ? $conditional_delivery_fee_time : "";
		}

		$conditional_delivery_text = (isset($conditional_delivery_fee_data['coderockz_woo_delivery_conditional_delivery_text']) && $conditional_delivery_fee_data['coderockz_woo_delivery_conditional_delivery_text'] !="") ? sanitize_text_field($conditional_delivery_fee_data['coderockz_woo_delivery_conditional_delivery_text']) : "";		

		if(isset($conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_starts_hour']) && $conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_starts_hour'] !="") {

			$conditional_delivery_time_slot_starts_hour = (isset($conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_starts_hour']) && $conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_starts_hour'] !="") ? sanitize_text_field($conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_starts_hour']) : "0";
			
			$conditional_delivery_time_slot_starts_min = (isset($conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_starts_min']) && $conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_starts_min'] !="") ? sanitize_text_field($conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_starts_min']) : "0"; 

			$conditional_delivery_time_slot_starts_format = sanitize_text_field($conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_starts_format']);
			if($conditional_delivery_time_slot_starts_format == "am") {
				$conditional_delivery_time_slot_starts_hour = ($conditional_delivery_time_slot_starts_hour == "12") ? "0" : $conditional_delivery_time_slot_starts_hour;
				$conditional_delivery_time_slot_starts = ((int)$conditional_delivery_time_slot_starts_hour * 60) + (int)$conditional_delivery_time_slot_starts_min;
			} else {
				$conditional_delivery_time_slot_starts_hour = ($conditional_delivery_time_slot_starts_hour == "12") ? "0" : $conditional_delivery_time_slot_starts_hour;
				$conditional_delivery_time_slot_starts = (((int)$conditional_delivery_time_slot_starts_hour + 12)*60) + (int)$conditional_delivery_time_slot_starts_min;
			}

		} else {
			$conditional_delivery_time_slot_starts = "";
		}

		if(isset($conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_ends_hour']) && $conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_ends_hour'] !="") {

			$conditional_delivery_time_slot_ends_hour = (isset($conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_ends_hour']) && $conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_ends_hour'] !="") ? sanitize_text_field($conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_ends_hour']) : "0";
			
			$conditional_delivery_time_slot_ends_min = (isset($conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_ends_min']) && $conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_ends_min'] !="") ? sanitize_text_field($conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_ends_min']) : "0"; 

			$conditional_delivery_time_slot_ends_format = sanitize_text_field($conditional_delivery_fee_data['coderockz_conditional_delivery_time_slot_ends_format']);

			if($conditional_delivery_time_slot_ends_format == "am") {
				$conditional_delivery_time_slot_ends_hour_12 = ($conditional_delivery_time_slot_ends_hour == "12") ? "0" : $conditional_delivery_time_slot_ends_hour;
				$conditional_delivery_time_slot_ends = ((int)$conditional_delivery_time_slot_ends_hour_12 * 60) + (int)$conditional_delivery_time_slot_ends_min;
			} else {
				$conditional_delivery_time_slot_ends_hour = ($conditional_delivery_time_slot_ends_hour == "12") ? "0" : $conditional_delivery_time_slot_ends_hour;
				$conditional_delivery_time_slot_ends = (((int)$conditional_delivery_time_slot_ends_hour + 12)*60) + (int)$conditional_delivery_time_slot_ends_min;
			}

			if($conditional_delivery_time_slot_ends_format == "am" && $conditional_delivery_time_slot_ends_hour == "12" && ($conditional_delivery_time_slot_ends_min =="0"||$conditional_delivery_time_slot_ends_min =="00")) {
					$conditional_delivery_time_slot_ends = 1440;
			}

		} else {
			$conditional_delivery_time_slot_ends = "";
		}

		$disable_inter_timeslot_conditional = !isset($conditional_delivery_fee_data['coderockz_delivery_disable_inter_timeslot_conditional']) ? false : true;

		$conditional_delivery_fee_settings['enable_conditional_delivery_fee'] = $enable_conditional_delivery_fee;
		$conditional_delivery_fee_settings['conditional_delivery_fee'] = $conditional_delivery_fee;
		$conditional_delivery_fee_settings['conditional_delivery_shipping_method'] = $conditional_delivery_shipping_method;
		$conditional_delivery_fee_settings['conditional_delivery_fee_duration'] = (string)$conditional_delivery_fee_time;
		$conditional_delivery_fee_settings['conditional_delivery_dropdown_text'] = $conditional_delivery_text;

		$conditional_delivery_fee_settings['conditional_delivery_time_starts'] = $conditional_delivery_time_slot_starts;
		$conditional_delivery_fee_settings['conditional_delivery_time_ends'] = $conditional_delivery_time_slot_ends;
		$conditional_delivery_fee_settings['disable_inter_timeslot_conditional'] = $disable_inter_timeslot_conditional;

		if(get_option('coderockz_woo_delivery_fee_settings') == false) {
			update_option('coderockz_woo_delivery_fee_settings', $conditional_delivery_fee_settings);
		} else {
			$conditional_delivery_fee_settings = array_merge(get_option('coderockz_woo_delivery_fee_settings'),$conditional_delivery_fee_settings);
			update_option('coderockz_woo_delivery_fee_settings', $conditional_delivery_fee_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_process_delivery_date_fee() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $date_form_data );
		$delivery_date_fee_settings = [];
		$enable_delivery_date_fee = !isset($date_form_data['coderockz_delivery_date_enable_delivery_date_fee']) ? false : true;
		$same_day_fee = sanitize_text_field($date_form_data['coderockz_delivery_date_same_day_fee']);
		$next_day_fee = sanitize_text_field($date_form_data['coderockz_delivery_date_next_day_fee']);
		$day_after_tomorrow_fee = sanitize_text_field($date_form_data['coderockz_delivery_date_day_after_tomorrow_fee']);
		$other_day_fee = sanitize_text_field($date_form_data['coderockz_delivery_date_other_days_fee']);

		$conditional_delivery_day_shipping_method = sanitize_text_field($date_form_data['coderockz_woo_delivery_conditional_delivery_day_shipping_method']);
		$conditional_delivery_day_shipping_method_total_day = sanitize_text_field($date_form_data['coderockz_woo_delivery_conditional_delivery_day_shipping_method_total_day']);

		$delivery_date_fee_settings['enable_delivery_date_fee'] = $enable_delivery_date_fee;
		$delivery_date_fee_settings['same_day_fee'] = $same_day_fee;
		$delivery_date_fee_settings['next_day_fee'] = $next_day_fee;
		$delivery_date_fee_settings['day_after_tomorrow_fee'] = $day_after_tomorrow_fee;
		$delivery_date_fee_settings['other_days_fee'] = $other_day_fee;
		$delivery_date_fee_settings['conditional_delivery_day_shipping_method'] = $conditional_delivery_day_shipping_method;
		$delivery_date_fee_settings['conditional_delivery_day_shipping_method_total_day'] = $conditional_delivery_day_shipping_method_total_day;

		if(get_option('coderockz_woo_delivery_fee_settings') == false) {
			update_option('coderockz_woo_delivery_fee_settings', $delivery_date_fee_settings);
		} else {
			$delivery_date_fee_settings = array_merge(get_option('coderockz_woo_delivery_fee_settings'),$delivery_date_fee_settings);
			update_option('coderockz_woo_delivery_fee_settings', $delivery_date_fee_settings);
		}

		wp_send_json_success();
		
	}

	public function coderockz_woo_delivery_process_weekday_wise_fee() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $date_form_data );
		
		$fee_settings = [];
		$weekday_delivery_fee_settings = [];

		$enable_weekday_wise_delivery_fee = !isset($date_form_data['coderockz_woo_delivery_enable_weekday_wise_delivery_fee']) ? false : true;
		$weekday_wise_delivery_fee = [];

		$weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
        foreach ($weekday as $key => $value) { 
	
			$weekday_wise_delivery_fee_[$key] = (isset($date_form_data['coderockz_woo_delivery_weekday_wise_fee_'.$key]) && $date_form_data['coderockz_woo_delivery_weekday_wise_fee_'.$key] !="") ? sanitize_text_field($date_form_data['coderockz_woo_delivery_weekday_wise_fee_'.$key]) : ""; 

			$weekday_wise_delivery_fee[$key] = $weekday_wise_delivery_fee_[$key];
        }

        $fee_settings['enable_weekday_wise_delivery_fee'] = $enable_weekday_wise_delivery_fee;
		$fee_settings['weekday_wise_delivery_fee'] = $weekday_wise_delivery_fee;

		if(get_option('coderockz_woo_delivery_fee_settings') == false) {
			update_option('coderockz_woo_delivery_fee_settings', $fee_settings);
		} else {
			$fee_settings = array_merge(get_option('coderockz_woo_delivery_fee_settings'),$fee_settings);
			update_option('coderockz_woo_delivery_fee_settings', $fee_settings);
		}

		wp_send_json_success();
		
	}

	public function coderockz_woo_delivery_process_specific_date_fee() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $date_form_data );
		$specific_date_fee_settings = [];
		$specific_date_fee=[];

		$delivery_specific_date_fees = $this->helper->coderockz_woo_delivery_array_sanitize($date_form_data['coderockz_woo_delivery_specific_date']);
		foreach($delivery_specific_date_fees as $delivery_specific_date_fee) {
			if($delivery_specific_date_fee != "" && strlen($delivery_specific_date_fee) == 10) {
				if(sanitize_text_field($date_form_data['coderockz-woo-delivery-specific-date-fee-'.$delivery_specific_date_fee]) != "") {
					$specific_date_fee[$delivery_specific_date_fee] = sanitize_text_field($date_form_data['coderockz-woo-delivery-specific-date-fee-'.$delivery_specific_date_fee]);
				}
			}
			
		}

		$specific_date_fee_settings['specific_date_fee'] = $specific_date_fee;

		if(get_option('coderockz_woo_delivery_fee_settings') == false) {
			update_option('coderockz_woo_delivery_fee_settings', $specific_date_fee_settings);
		} else {
			$specific_date_fee_settings = array_merge(get_option('coderockz_woo_delivery_fee_settings'),$specific_date_fee_settings);
			update_option('coderockz_woo_delivery_fee_settings', $specific_date_fee_settings);
		}

		wp_send_json_success();
		
	}

	public function coderockz_woo_delivery_process_common_email_settings() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $date_form_data );
		$notify_email_form_settings = [];
		$notify_email_different_name_email = !isset($date_form_data['coderockz_woo_delivery_notify_email_different_name_email']) ? false : true;
		$send_email_from_email = sanitize_text_field($date_form_data['coderockz_woo_delivery_send_email_from_email']);
		$send_email_from_name = sanitize_text_field($date_form_data['coderockz_woo_delivery_send_email_from_name']);
		$notify_email_billing_address_heading = sanitize_text_field($date_form_data['coderockz_woo_delivery_notify_email_billing_address_heading']);
		$notify_email_shipping_address_heading = sanitize_text_field($date_form_data['coderockz_woo_delivery_notify_email_shipping_address_heading']);
		$notify_email_product_text = sanitize_text_field($date_form_data['coderockz_woo_delivery_notify_email_product_text']);
		$notify_email_order_text = sanitize_text_field($date_form_data['coderockz_woo_delivery_notify_email_order_text']);
		$notify_email_quantity_text = sanitize_text_field($date_form_data['coderockz_woo_delivery_notify_email_quantity_text']);
		$notify_email_price_text = sanitize_text_field($date_form_data['coderockz_woo_delivery_notify_email_total_text']);
		$notify_email_tax_text = sanitize_text_field($date_form_data['coderockz_woo_delivery_notify_email_tax_text']);
		$notify_email_shipping_text = sanitize_text_field($date_form_data['coderockz_woo_delivery_notify_email_shipping_text']);
		$notify_email_payment_text = sanitize_text_field($date_form_data['coderockz_woo_delivery_notify_email_payment_text']);
		$notify_email_total_text = sanitize_text_field($date_form_data['coderockz_woo_delivery_notify_email_price_text']);
		$notify_email_logo_id = isset($date_form_data['coderockz-woo-delivery-notify-email-logo-upload-id']) ? sanitize_text_field($date_form_data['coderockz-woo-delivery-notify-email-logo-upload-id']) : "";
		$notify_email_heading_color = sanitize_text_field($date_form_data['coderockz_woo_delivery_notify_email_heading_color']);		
		$notify_email_logo_width = sanitize_text_field($date_form_data['coderockz_woo_delivery_notify_email_logo_width']);		
		$notify_email_form_settings['notify-email-logo-id'] = $notify_email_logo_id;
		$notify_email_form_settings['notify_email_heading_color'] = $notify_email_heading_color;
		$notify_email_form_settings['notify_email_logo_width'] = $notify_email_logo_width;
		$notify_email_form_settings['notify_email_different_name_email'] = $notify_email_different_name_email;
		$notify_email_form_settings['send_email_from_email'] = $send_email_from_email;
		$notify_email_form_settings['send_email_from_name'] = $send_email_from_name;
		$notify_email_form_settings['notify_email_billing_address_heading'] = $notify_email_billing_address_heading;
		$notify_email_form_settings['notify_email_shipping_address_heading'] = $notify_email_shipping_address_heading;
		$notify_email_form_settings['notify_email_product_text'] = $notify_email_product_text;
		$notify_email_form_settings['notify_email_order_text'] = $notify_email_order_text;
		$notify_email_form_settings['notify_email_quantity_text'] = $notify_email_quantity_text;
		$notify_email_form_settings['notify_email_price_text'] = $notify_email_price_text;
		$notify_email_form_settings['notify_email_tax_text'] = $notify_email_tax_text;
		$notify_email_form_settings['notify_email_shipping_text'] = $notify_email_shipping_text;
		$notify_email_form_settings['notify_email_payment_text'] = $notify_email_payment_text;
		$notify_email_form_settings['notify_email_total_text'] = $notify_email_total_text;

		if(get_option('coderockz_woo_delivery_notify_email_settings') == false) {
			update_option('coderockz_woo_delivery_notify_email_settings', $notify_email_form_settings);
		} else {
			$notify_email_form_settings = array_merge(get_option('coderockz_woo_delivery_notify_email_settings'),$notify_email_form_settings);
			update_option('coderockz_woo_delivery_notify_email_settings', $notify_email_form_settings);
		}

		wp_send_json_success();
		
	}

	public function coderockz_woo_delivery_process_reminder_email() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $date_form_data );
		$reminder_email_form_settings = [];

		$enable_reminder_email = !isset($date_form_data['coderockz_woo_delivery_enable_reminder_email']) ? false : true;

		if($enable_reminder_email) {

			if ( !wp_next_scheduled( 'coderockz_woo_delivery_reminder_email_schedule' ) ) {
				wp_schedule_event(current_time( 'timestamp', 1 ) - ((wp_date("G")*60)+wp_date("i"))*60, 'daily', 'coderockz_woo_delivery_reminder_email_schedule');
			}
		} else {
			//find out when the last event was scheduled
			$timestamp = wp_next_scheduled('coderockz_woo_delivery_reminder_email_schedule');
			//unschedule previous event if any
			wp_unschedule_event($timestamp,'coderockz_woo_delivery_reminder_email_schedule');
			wp_clear_scheduled_hook('coderockz_woo_delivery_reminder_email_schedule');

		}

		$reminder_delivery_email_subject = sanitize_text_field($date_form_data['coderockz_woo_delivery_reminder_delivery_email_subject']);
		$reminder_pickup_email_subject = sanitize_text_field($date_form_data['coderockz_woo_delivery_reminder_pickup_email_subject']);

		$delivery_reminder_email_heading = sanitize_text_field($date_form_data['coderockz_woo_delivery_delivery_reminder_email_heading']);
		$pickup_reminder_email_heading = sanitize_text_field($date_form_data['coderockz_woo_delivery_pickup_reminder_email_heading']);		
		
		$reminder_email_form_settings['enable_reminder_email'] = $enable_reminder_email;
		$reminder_email_form_settings['reminder_delivery_email_subject'] = $reminder_delivery_email_subject;
		$reminder_email_form_settings['reminder_pickup_email_subject'] = $reminder_pickup_email_subject;
		$reminder_email_form_settings['delivery_reminder_email_heading'] = $delivery_reminder_email_heading;
		$reminder_email_form_settings['pickup_reminder_email_heading'] = $pickup_reminder_email_heading;

		if(get_option('coderockz_woo_delivery_notify_email_settings') == false) {
			update_option('coderockz_woo_delivery_notify_email_settings', $reminder_email_form_settings);
		} else {
			$reminder_email_form_settings = array_merge(get_option('coderockz_woo_delivery_notify_email_settings'),$reminder_email_form_settings);
			update_option('coderockz_woo_delivery_notify_email_settings', $reminder_email_form_settings);
		}

		wp_send_json_success();
		
	}

	public function coderockz_woo_delivery_process_notify_email() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $date_form_data );
		$notify_email_form_settings = [];

		$notify_delivery_email_subject = sanitize_text_field($date_form_data['coderockz_woo_delivery_notify_delivery_email_subject']);
		$notify_pickup_email_subject = sanitize_text_field($date_form_data['coderockz_woo_delivery_notify_pickup_email_subject']);

		$delivery_notify_email_heading = sanitize_text_field($date_form_data['coderockz_woo_delivery_delivery_notify_email_heading']);
		$pickup_notify_email_heading = sanitize_text_field($date_form_data['coderockz_woo_delivery_pickup_notify_email_heading']);		
		
		$notify_email_form_settings['notify_delivery_email_subject'] = $notify_delivery_email_subject;
		$notify_email_form_settings['notify_pickup_email_subject'] = $notify_pickup_email_subject;
		$notify_email_form_settings['delivery_notify_email_heading'] = $delivery_notify_email_heading;
		$notify_email_form_settings['pickup_notify_email_heading'] = $pickup_notify_email_heading;

		if(get_option('coderockz_woo_delivery_notify_email_settings') == false) {
			update_option('coderockz_woo_delivery_notify_email_settings', $notify_email_form_settings);
		} else {
			$notify_email_form_settings = array_merge(get_option('coderockz_woo_delivery_notify_email_settings'),$notify_email_form_settings);
			update_option('coderockz_woo_delivery_notify_email_settings', $notify_email_form_settings);
		}

		wp_send_json_success();
		
	}

	public function coderockz_woo_delivery_process_additional_field() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		
		$additional_field_form_settings = [];

		parse_str( $_POST[ 'dateFormData' ], $date_form_data );

		$enable_additional_field = !isset($date_form_data['coderockz_enable_additional_field']) ? false : true;
		
		$additional_field_mandatory = !isset($date_form_data['coderockz_additional_field_mandatory']) ? false : true;
		
		$additional_field_field_label = sanitize_text_field($date_form_data['coderockz_additional_field_label']);
		$additional_field_field_placeholder = sanitize_text_field($date_form_data['coderockz_additional_field_placeholder']);

		$character_remaining_text = sanitize_text_field($date_form_data['coderockz_woo_delivery_additional_field_character_remaining_text']);
		
		$additional_field_ch_limit = sanitize_text_field($date_form_data['coderockz_additional_field_ch_limit']);

		$disable_order_notes = !isset($date_form_data['coderockz_woo_delivery_disable_order_notes']) ? false : true;

		$hide_additional_field_for = (isset($date_form_data['coderockz_woo_delivery_hide_additional_field_for']) && !empty($date_form_data['coderockz_woo_delivery_hide_additional_field_for'])) ? $date_form_data['coderockz_woo_delivery_hide_additional_field_for'] : array();

		$additional_field_form_settings['enable_additional_field'] = $enable_additional_field;
		$additional_field_form_settings['additional_field_mandatory'] = $additional_field_mandatory;
		$additional_field_form_settings['field_label'] = $additional_field_field_label;
		$additional_field_form_settings['field_placeholder'] = $additional_field_field_placeholder;
		$additional_field_form_settings['character_limit'] = $additional_field_ch_limit;
		$additional_field_form_settings['character_remaining_text'] = $character_remaining_text;
		$additional_field_form_settings['hide_additional_field_for'] = $hide_additional_field_for;
		$additional_field_form_settings['disable_order_notes'] = $disable_order_notes;
		
		if(get_option('coderockz_woo_delivery_additional_field_settings') == false) {
			update_option('coderockz_woo_delivery_additional_field_settings', $additional_field_form_settings);
		} else {
			$additional_field_form_settings = array_merge(get_option('coderockz_woo_delivery_additional_field_settings'),$additional_field_form_settings);
			update_option('coderockz_woo_delivery_additional_field_settings', $additional_field_form_settings);
		}
		wp_send_json_success();
		
    }

    public function coderockz_woo_delivery_process_delivery_option_settings() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		
		$delivery_option_settings_form_settings = [];

		parse_str( $_POST[ 'formData' ], $form_data );

		$coderockz_enable_option_time_pickup = !isset($form_data['coderockz_enable_option_time_pickup']) ? false : true;

		$delivery_option_field_label = sanitize_text_field($form_data['coderockz_woo_delivery_delivery_option_label']);

		$delivery_field_label = sanitize_text_field($form_data['coderockz_woo_delivery_option_delivery_label']);

		$pickup_field_label = sanitize_text_field($form_data['coderockz_woo_delivery_option_pickup_label']);
		$pre_selected_order_type = sanitize_text_field($form_data['coderockz_woo_delivery_pre_selected_order_type']);
		$no_result_notice = sanitize_text_field($form_data['coderockz_woo_delivery_option_no_result_notice']);

		$maximum_delivery_pickup_per_day = sanitize_text_field($form_data['coderockz_woo_delivery_maximum_delivery_pickup_per_day']);
		$maximum_product_delivery_pickup_per_day = sanitize_text_field($form_data['coderockz_woo_delivery_maximum_product_delivery_pickup_per_day']);

		$enable_dynamic_order_type = !isset($form_data['coderockz_woo_delivery_enable_dynamic_order_type']) ? false : true;

		$dynamic_order_type_no_delivery = sanitize_text_field($form_data['coderockz_woo_delivery_dynamic_order_type_no_delivery']);

		$dynamic_order_type_no_pickup = sanitize_text_field($form_data['coderockz_woo_delivery_dynamic_order_type_no_pickup']);
		$dynamic_order_type_no_delivery_pickup = sanitize_text_field($form_data['coderockz_woo_delivery_dynamic_order_type_no_delivery_pickup']);

		$delivery_option_settings_form_settings['enable_option_time_pickup'] = $coderockz_enable_option_time_pickup;
		$delivery_option_settings_form_settings['delivery_option_label'] = $delivery_option_field_label;
		$delivery_option_settings_form_settings['delivery_label'] = $delivery_field_label;
		$delivery_option_settings_form_settings['pickup_label'] = $pickup_field_label;
		$delivery_option_settings_form_settings['pre_selected_order_type'] = $pre_selected_order_type;
		$delivery_option_settings_form_settings['no_result_notice'] = $no_result_notice;
		$delivery_option_settings_form_settings['maximum_delivery_pickup_per_day'] = $maximum_delivery_pickup_per_day;
		$delivery_option_settings_form_settings['maximum_product_delivery_pickup_per_day'] = $maximum_product_delivery_pickup_per_day;
		$delivery_option_settings_form_settings['enable_dynamic_order_type'] = $enable_dynamic_order_type;
		$delivery_option_settings_form_settings['dynamic_order_type_no_delivery'] = $dynamic_order_type_no_delivery;
		$delivery_option_settings_form_settings['dynamic_order_type_no_pickup'] = $dynamic_order_type_no_pickup;
		$delivery_option_settings_form_settings['dynamic_order_type_no_delivery_pickup'] = $dynamic_order_type_no_delivery_pickup;

		if(get_option('coderockz_woo_delivery_option_delivery_settings') == false) {
			update_option('coderockz_woo_delivery_option_delivery_settings', $delivery_option_settings_form_settings);
		} else {
			$delivery_option_settings_form_settings = array_merge(get_option('coderockz_woo_delivery_option_delivery_settings'),$delivery_option_settings_form_settings);
			update_option('coderockz_woo_delivery_option_delivery_settings', $delivery_option_settings_form_settings);
		}
		wp_send_json_success();
		
    }

    public function coderockz_woo_delivery_delivery_restriction_settings() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		
		$delivery_option_settings_form_settings = [];

		parse_str( $_POST[ 'formData' ], $form_data );

		$enable_delivery_restriction = !isset($form_data['coderockz_woo_delivery_enable_delivery_restriction']) ? false : true;

		$minimum_amount_cart_restriction = sanitize_text_field($form_data['coderockz_woo_delivery_minimum_amount_cart_restriction']);

		$calculating_include_tax = !isset($form_data['coderockz_woo_delivery_calculating_include_tax']) ? false : true;
		$calculating_include_discount = !isset($form_data['coderockz_woo_delivery_calculating_include_discount']) ? false : true;

		$delivery_restriction_notice = sanitize_text_field($form_data['coderockz_woo_delivery_delivery_restriction_notice']);

		$delivery_option_settings_form_settings['enable_delivery_restriction'] = $enable_delivery_restriction;
		$delivery_option_settings_form_settings['minimum_amount_cart_restriction'] = $minimum_amount_cart_restriction;
		$delivery_option_settings_form_settings['calculating_include_tax'] = $calculating_include_tax;
		$delivery_option_settings_form_settings['calculating_include_discount'] = $calculating_include_discount;
		$delivery_option_settings_form_settings['delivery_restriction_notice'] = $delivery_restriction_notice;
		
		if(get_option('coderockz_woo_delivery_option_delivery_settings') == false) {
			update_option('coderockz_woo_delivery_option_delivery_settings', $delivery_option_settings_form_settings);
		} else {
			$delivery_option_settings_form_settings = array_merge(get_option('coderockz_woo_delivery_option_delivery_settings'),$delivery_option_settings_form_settings);
			update_option('coderockz_woo_delivery_option_delivery_settings', $delivery_option_settings_form_settings);
		}
		wp_send_json_success();
		
    }

    public function coderockz_woo_delivery_pickup_restriction_settings() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		
		$delivery_option_settings_form_settings = [];

		parse_str( $_POST[ 'formData' ], $form_data );

		$enable_pickup_restriction = !isset($form_data['coderockz_woo_delivery_enable_pickup_restriction']) ? false : true;

		$minimum_amount_cart_restriction_pickup = sanitize_text_field($form_data['coderockz_woo_delivery_minimum_amount_cart_restriction_pickup']);

		$calculating_include_tax_pickup = !isset($form_data['coderockz_woo_delivery_calculating_include_tax_pickup']) ? false : true;

		$calculating_include_discount_pickup = !isset($form_data['coderockz_woo_delivery_calculating_include_discount_pickup']) ? false : true;

		$pickup_restriction_notice = sanitize_text_field($form_data['coderockz_woo_delivery_pickup_restriction_notice']);

		$delivery_option_settings_form_settings['enable_pickup_restriction'] = $enable_pickup_restriction;
		$delivery_option_settings_form_settings['minimum_amount_cart_restriction_pickup'] = $minimum_amount_cart_restriction_pickup;
		$delivery_option_settings_form_settings['calculating_include_tax_pickup'] = $calculating_include_tax_pickup;
		$delivery_option_settings_form_settings['calculating_include_discount_pickup'] = $calculating_include_discount_pickup;
		$delivery_option_settings_form_settings['pickup_restriction_notice'] = $pickup_restriction_notice;
		
		if(get_option('coderockz_woo_delivery_option_delivery_settings') == false) {
			update_option('coderockz_woo_delivery_option_delivery_settings', $delivery_option_settings_form_settings);
		} else {
			$delivery_option_settings_form_settings = array_merge(get_option('coderockz_woo_delivery_option_delivery_settings'),$delivery_option_settings_form_settings);
			update_option('coderockz_woo_delivery_option_delivery_settings', $delivery_option_settings_form_settings);
		}
		wp_send_json_success();
		
    }

    public function coderockz_woo_delivery_category_product_delivery_restriction_settings_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');

    	$category_product_delivery_restriction = [];

		parse_str( $_POST[ 'formData' ], $form_data );

		$delivery_restriction_categories = (isset($form_data['coderockz_woo_delivery_restrict_delivery_categories']) && !empty($form_data['coderockz_woo_delivery_restrict_delivery_categories'])) ? $form_data['coderockz_woo_delivery_restrict_delivery_categories'] : array();
		$delivery_restriction_categories = $this->helper->coderockz_woo_delivery_array_sanitize($delivery_restriction_categories);

		$category_product_delivery_restriction['restrict_delivery_categories'] = $delivery_restriction_categories;
		
		if(get_option('coderockz_woo_delivery_large_product_list') == false) {
			$restrict_delivery_product = (isset($form_data['coderockz_woo_delivery_restrict_delivery_individual_product']) && !empty($form_data['coderockz_woo_delivery_restrict_delivery_individual_product'])) ? $form_data['coderockz_woo_delivery_restrict_delivery_individual_product'] : array();
			$restrict_delivery_product = $this->helper->coderockz_woo_delivery_array_sanitize($restrict_delivery_product);
		} else {
			$restrict_delivery_product = isset($form_data['coderockz_woo_delivery_restrict_delivery_individual_product_input']) && $form_data['coderockz_woo_delivery_restrict_delivery_individual_product_input'] != "" ? $form_data['coderockz_woo_delivery_restrict_delivery_individual_product_input'] : "";
			$restrict_delivery_product = explode(",",$restrict_delivery_product);
			$restrict_delivery_product = $this->helper->coderockz_woo_delivery_array_sanitize($restrict_delivery_product);
		}

		$category_product_delivery_restriction['restrict_delivery_products'] = $restrict_delivery_product;

		$reverse_current_condition = !isset($form_data['coderockz_delivery_restrict_delivery_reverse_current_condition']) ? false : true;

		$category_product_delivery_restriction['restrict_delivery_reverse_current_condition'] = $reverse_current_condition;
		
		if(get_option('coderockz_woo_delivery_option_delivery_settings') == false) {
			update_option('coderockz_woo_delivery_option_delivery_settings', $category_product_delivery_restriction);
		} else {
			$category_product_delivery_restriction = array_merge(get_option('coderockz_woo_delivery_option_delivery_settings'),$category_product_delivery_restriction);
			update_option('coderockz_woo_delivery_option_delivery_settings', $category_product_delivery_restriction);
		}

		wp_send_json_success();
	}


	public function coderockz_woo_delivery_category_product_pickup_restriction_settings_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');

    	$category_product_pickup_restriction = [];

		parse_str( $_POST[ 'formData' ], $form_data );

		$pickup_restriction_categories = (isset($form_data['coderockz_woo_delivery_restrict_pickup_categories']) && !empty($form_data['coderockz_woo_delivery_restrict_pickup_categories'])) ? $form_data['coderockz_woo_delivery_restrict_pickup_categories'] : array();
		$pickup_restriction_categories = $this->helper->coderockz_woo_delivery_array_sanitize($pickup_restriction_categories);

		$category_product_pickup_restriction['restrict_pickup_categories'] = $pickup_restriction_categories;
				
		if(get_option('coderockz_woo_delivery_large_product_list') == false) {
			$restrict_pickup_product = (isset($form_data['coderockz_woo_delivery_restrict_pickup_individual_product']) && !empty($form_data['coderockz_woo_delivery_restrict_pickup_individual_product'])) ? $form_data['coderockz_woo_delivery_restrict_pickup_individual_product'] : array();
			$restrict_pickup_product = $this->helper->coderockz_woo_delivery_array_sanitize($restrict_pickup_product);
		} else {
			$restrict_pickup_product = isset($form_data['coderockz_woo_delivery_restrict_pickup_individual_product_input']) && $form_data['coderockz_woo_delivery_restrict_pickup_individual_product_input'] != "" ? $form_data['coderockz_woo_delivery_restrict_pickup_individual_product_input'] : "";
			$restrict_pickup_product = explode(",",$restrict_pickup_product);
			$restrict_pickup_product = $this->helper->coderockz_woo_delivery_array_sanitize($restrict_pickup_product);
		}

		$category_product_pickup_restriction['restrict_pickup_products'] = $restrict_pickup_product;

		$reverse_current_condition = !isset($form_data['coderockz_delivery_restrict_pickup_reverse_current_condition']) ? false : true;

		$category_product_pickup_restriction['restrict_pickup_reverse_current_condition'] = $reverse_current_condition;
		
		if(get_option('coderockz_woo_delivery_option_delivery_settings') == false) {
			update_option('coderockz_woo_delivery_option_delivery_settings', $category_product_pickup_restriction);
		} else {
			$category_product_pickup_restriction = array_merge(get_option('coderockz_woo_delivery_option_delivery_settings'),$category_product_pickup_restriction);
			update_option('coderockz_woo_delivery_option_delivery_settings', $category_product_pickup_restriction);
		}

		wp_send_json_success();
	}

    public function coderockz_woo_delivery_free_shipping_restriction_settings() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		
		$delivery_option_settings_form_settings = [];

		parse_str( $_POST[ 'formData' ], $form_data );

		$enable_free_shipping_restriction = !isset($form_data['coderockz_woo_delivery_enable_free_shipping_restriction']) ? false : true;
		$enable_hide_other_shipping_method = !isset($form_data['coderockz_woo_delivery_enable_hide_other_shipping_method']) ? false : true;

		$minimum_amount_shipping_restriction = sanitize_text_field($form_data['coderockz_woo_delivery_minimum_amount_shipping_restriction']);

		$calculating_include_tax_free_shipping = !isset($form_data['coderockz_woo_delivery_calculating_include_tax_free_shipping']) ? false : true;
		$calculating_include_discount_free_shipping = !isset($form_data['coderockz_woo_delivery_calculating_include_discount_free_shipping']) ? false : true;

		$free_shipping_restriction_notice = sanitize_text_field($form_data['coderockz_woo_delivery_free_shipping_restriction_notice']);

		$enable_free_shipping_current_day = !isset($form_data['coderockz_woo_delivery_enable_free_shipping_current_day']) ? false : true;
		$disable_free_shipping_current_day = !isset($form_data['coderockz_woo_delivery_disable_free_shipping_current_day']) ? false : true;

		$hide_free_shipping_weekday = (isset($form_data['coderockz_woo_delivery_hide_free_shipping_weekday']) && !empty($form_data['coderockz_woo_delivery_hide_free_shipping_weekday'])) ? $form_data['coderockz_woo_delivery_hide_free_shipping_weekday'] : array();

		$show_free_shipping_only_at = isset($form_data['coderockz_woo_delivery_show_free_shipping_only_at']) && $form_data['coderockz_woo_delivery_show_free_shipping_only_at'] != "" ? $form_data['coderockz_woo_delivery_show_free_shipping_only_at'] : "";
		if($show_free_shipping_only_at != "") {

			if(strpos($show_free_shipping_only_at, '...') !== false) {

				$temporary_dates = explode(',', str_replace(' ', '', $show_free_shipping_only_at));
				$specific_date_array = [];
				foreach($temporary_dates as $temporary_date) {
					if(strpos($temporary_date, '...') !== false) {
						$filtered_dates = explode('...', $temporary_date);
					    $period = $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1]);
						$specific_date_array = array_merge($specific_date_array,$period);
					} else {
						$specific_date_array[] = $temporary_date;
					}
				}
				
			    $show_free_shipping_only_at_string = str_replace(' ', '', $show_free_shipping_only_at);
			    $show_free_shipping_only_at = $specific_date_array;

			} else {
				$show_free_shipping_only_at_string = str_replace(' ', '', $show_free_shipping_only_at);
				$specific_date_array = explode(',', str_replace(' ', '', $show_free_shipping_only_at));
				$show_free_shipping_only_at = $specific_date_array;
			}

			$show_free_shipping_only_at = $this->helper->coderockz_woo_delivery_array_sanitize($show_free_shipping_only_at);
		} else {

			$show_free_shipping_only_at_string = "";
			$show_free_shipping_only_at = [];
		}
		
		$hide_free_shipping_at = isset($form_data['coderockz_woo_delivery_hide_free_shipping_at']) && $form_data['coderockz_woo_delivery_hide_free_shipping_at'] != "" ? $form_data['coderockz_woo_delivery_hide_free_shipping_at'] : "";
		if($hide_free_shipping_at != "") {

			if(strpos($hide_free_shipping_at, '...') !== false) {

				$temporary_dates = explode(',', str_replace(' ', '', $hide_free_shipping_at));
				$specific_date_array = [];
				foreach($temporary_dates as $temporary_date) {
					if(strpos($temporary_date, '...') !== false) {
						$filtered_dates = explode('...', $temporary_date);
					    $period = $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1]);
						$specific_date_array = array_merge($specific_date_array,$period);
					} else {
						$specific_date_array[] = $temporary_date;
					}
				}
				
			    $hide_free_shipping_at_string = str_replace(' ', '', $hide_free_shipping_at);
			    $hide_free_shipping_at = $specific_date_array;

			} else {
				$hide_free_shipping_at_string = str_replace(' ', '', $hide_free_shipping_at);
				$specific_date_array = explode(',', str_replace(' ', '', $hide_free_shipping_at));
				$hide_free_shipping_at = $specific_date_array;
			}

			$hide_free_shipping_at = $this->helper->coderockz_woo_delivery_array_sanitize($hide_free_shipping_at);
		} else {

			$hide_free_shipping_at_string = "";
			$hide_free_shipping_at = [];
		}		

		$delivery_option_settings_form_settings['enable_free_shipping_restriction'] = $enable_free_shipping_restriction;
		$delivery_option_settings_form_settings['enable_hide_other_shipping_method'] = $enable_hide_other_shipping_method;
		$delivery_option_settings_form_settings['minimum_amount_shipping_restriction'] = $minimum_amount_shipping_restriction;
		$delivery_option_settings_form_settings['calculating_include_tax_free_shipping'] = $calculating_include_tax_free_shipping;
		$delivery_option_settings_form_settings['calculating_include_discount_free_shipping'] = $calculating_include_discount_free_shipping;
		$delivery_option_settings_form_settings['free_shipping_restriction_notice'] = $free_shipping_restriction_notice;
		$delivery_option_settings_form_settings['enable_free_shipping_current_day'] = $enable_free_shipping_current_day;
		$delivery_option_settings_form_settings['disable_free_shipping_current_day'] = $disable_free_shipping_current_day;
		$delivery_option_settings_form_settings['hide_free_shipping_weekday'] = $hide_free_shipping_weekday;
		$delivery_option_settings_form_settings['hide_free_shipping_at'] = $hide_free_shipping_at;
		$delivery_option_settings_form_settings['hide_free_shipping_at_string'] = $hide_free_shipping_at_string;
		$delivery_option_settings_form_settings['show_free_shipping_only_at'] = $show_free_shipping_only_at;
		$delivery_option_settings_form_settings['show_free_shipping_only_at_string'] = $show_free_shipping_only_at_string;
		
		if(get_option('coderockz_woo_delivery_option_delivery_settings') == false) {
			update_option('coderockz_woo_delivery_option_delivery_settings', $delivery_option_settings_form_settings);
		} else {
			$delivery_option_settings_form_settings = array_merge(get_option('coderockz_woo_delivery_option_delivery_settings'),$delivery_option_settings_form_settings);
			update_option('coderockz_woo_delivery_option_delivery_settings', $delivery_option_settings_form_settings);
		}
		wp_send_json_success();
		
    }

    public function coderockz_woo_delivery_disable_delivery_facility_days() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		
		$delivery_option_settings_form_settings = [];

		parse_str( $_POST[ 'formData' ], $form_data );

		$disable_delivery_facility_days = (isset($form_data['coderockz_woo_delivery_disable_delivery_facility_days']) && !empty($form_data['coderockz_woo_delivery_disable_delivery_facility_days'])) ? $form_data['coderockz_woo_delivery_disable_delivery_facility_days'] : array();
		$disable_delivery_facility_days = $this->helper->coderockz_woo_delivery_array_sanitize($disable_delivery_facility_days);

		$delivery_option_settings_form_settings['disable_delivery_facility'] = $disable_delivery_facility_days;

		$disable_delivery_facility_dates = isset($form_data['coderockz_woo_delivery_disable_delivery_facility_dates']) && $form_data['coderockz_woo_delivery_disable_delivery_facility_dates'] != "" ? $form_data['coderockz_woo_delivery_disable_delivery_facility_dates'] : "";
		if($disable_delivery_facility_dates != "") {

			if(strpos($disable_delivery_facility_dates, '...') !== false) {

				$temporary_dates = explode(',', str_replace(' ', '', $disable_delivery_facility_dates));
				$specific_date_array = [];
				foreach($temporary_dates as $temporary_date) {
					if(strpos($temporary_date, '...') !== false) {
						$filtered_dates = explode('...', $temporary_date);
					    $period = $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1]);
						$specific_date_array = array_merge($specific_date_array,$period);
					} else {
						$specific_date_array[] = $temporary_date;
					}
				}
				
			    $disable_delivery_facility_dates_string = str_replace(' ', '', $disable_delivery_facility_dates);
			    $disable_delivery_facility_dates = $specific_date_array;

			} else {
				$disable_delivery_facility_dates_string = str_replace(' ', '', $disable_delivery_facility_dates);
				$specific_date_array = explode(',', str_replace(' ', '', $disable_delivery_facility_dates));
				$disable_delivery_facility_dates = $specific_date_array;
			}

			$disable_delivery_facility_dates = $this->helper->coderockz_woo_delivery_array_sanitize($disable_delivery_facility_dates);
		} else {

			$disable_delivery_facility_dates_string = "";
			$disable_delivery_facility_dates = [];
		}
		
		$delivery_option_settings_form_settings['disable_delivery_facility_dates'] = $disable_delivery_facility_dates;
		$delivery_option_settings_form_settings['disable_delivery_facility_dates_string'] = $disable_delivery_facility_dates_string;
		
		if(get_option('coderockz_woo_delivery_option_delivery_settings') == false) {
			update_option('coderockz_woo_delivery_option_delivery_settings', $delivery_option_settings_form_settings);
		} else {
			$delivery_option_settings_form_settings = array_merge(get_option('coderockz_woo_delivery_option_delivery_settings'),$delivery_option_settings_form_settings);
			update_option('coderockz_woo_delivery_option_delivery_settings', $delivery_option_settings_form_settings);
		}
		wp_send_json_success();
		
    }

    public function coderockz_woo_delivery_disable_pickup_facility_days() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		
		$pickup_option_settings_form_settings = [];

		parse_str( $_POST[ 'formData' ], $form_data );

		$disable_pickup_facility_days = (isset($form_data['coderockz_woo_delivery_disable_pickup_facility_days']) && !empty($form_data['coderockz_woo_delivery_disable_pickup_facility_days'])) ? $form_data['coderockz_woo_delivery_disable_pickup_facility_days'] : array();
		$disable_pickup_facility_days = $this->helper->coderockz_woo_delivery_array_sanitize($disable_pickup_facility_days);

		$pickup_option_settings_form_settings['disable_pickup_facility'] = $disable_pickup_facility_days;

		$disable_pickup_facility_dates = isset($form_data['coderockz_woo_delivery_disable_pickup_facility_dates']) && $form_data['coderockz_woo_delivery_disable_pickup_facility_dates'] != "" ? $form_data['coderockz_woo_delivery_disable_pickup_facility_dates'] : "";

		if($disable_pickup_facility_dates != "") {

			if(strpos($disable_pickup_facility_dates, '...') !== false) {

				$temporary_dates = explode(',', str_replace(' ', '', $disable_pickup_facility_dates));
				$specific_date_array = [];
				foreach($temporary_dates as $temporary_date) {
					if(strpos($temporary_date, '...') !== false) {
						$filtered_dates = explode('...', $temporary_date);
					    $period = $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1]);
						$specific_date_array = array_merge($specific_date_array,$period);
					} else {
						$specific_date_array[] = $temporary_date;
					}
				}
				
			    $disable_pickup_facility_dates_string = str_replace(' ', '', $disable_pickup_facility_dates);
			    $disable_pickup_facility_dates = $specific_date_array;

			} else {
				$disable_pickup_facility_dates_string = str_replace(' ', '', $disable_pickup_facility_dates);
				$specific_date_array = explode(',', str_replace(' ', '', $disable_pickup_facility_dates));
				$disable_pickup_facility_dates = $specific_date_array;
			}

			$disable_pickup_facility_dates = $this->helper->coderockz_woo_delivery_array_sanitize($disable_pickup_facility_dates);
		} else {

			$disable_pickup_facility_dates_string = "";
			$disable_pickup_facility_dates = [];
		}
		
		$pickup_option_settings_form_settings['disable_pickup_facility_dates'] = $disable_pickup_facility_dates;
		$pickup_option_settings_form_settings['disable_pickup_facility_dates_string'] = $disable_pickup_facility_dates_string;

		
		if(get_option('coderockz_woo_delivery_option_delivery_settings') == false) {
			update_option('coderockz_woo_delivery_option_delivery_settings', $pickup_option_settings_form_settings);
		} else {
			$pickup_option_settings_form_settings = array_merge(get_option('coderockz_woo_delivery_option_delivery_settings'),$pickup_option_settings_form_settings);
			update_option('coderockz_woo_delivery_option_delivery_settings', $pickup_option_settings_form_settings);
		}
		wp_send_json_success();
		
    }

    public function coderockz_woo_delivery_process_google_calendar_settings() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		
		$google_calendar_form_settings = [];

		parse_str( $_POST[ 'dateFormData' ], $date_form_data );

		$google_calendar_sync = !isset($date_form_data['coderockz_woo_delivery_enable_google_calendar_sync']) ? false : true;
		$google_calendar_form_settings['google_calendar_sync'] = $google_calendar_sync;

		$google_calendar_id = isset($date_form_data['coderockz_woo_delivery_google_calendar_id']) ? sanitize_text_field($date_form_data['coderockz_woo_delivery_google_calendar_id']) : "";
		$google_calendar_form_settings['google_calendar_id'] = $google_calendar_id;

		$google_calendar_client_id = isset($date_form_data['coderockz_woo_delivery_google_calendar_client_id']) ? sanitize_text_field($date_form_data['coderockz_woo_delivery_google_calendar_client_id']) : "";
		$google_calendar_form_settings['google_calendar_client_id'] = $google_calendar_client_id;

		$google_calendar_client_secret = isset($date_form_data['coderockz_woo_delivery_google_calendar_client_secret']) ? sanitize_text_field($date_form_data['coderockz_woo_delivery_google_calendar_client_secret']) : "";
		$google_calendar_form_settings['google_calendar_client_secret'] = $google_calendar_client_secret;

		$google_calendar_customer_sync = !isset($date_form_data['coderockz_woo_delivery_google_calendar_customer_sync']) ? false : true;
		$google_calendar_form_settings['google_calendar_customer_sync'] = $google_calendar_customer_sync;

		$google_calendar_order_received_page_btn_txt = isset($date_form_data['coderockz_woo_delivery_order_received_page_btn_txt']) ? sanitize_text_field($date_form_data['coderockz_woo_delivery_order_received_page_btn_txt']) : "";
		$google_calendar_form_settings['google_calendar_order_received_page_btn_txt'] = $google_calendar_order_received_page_btn_txt;

		$google_calendar_order_added_page_btn_txt = isset($date_form_data['coderockz_woo_delivery_order_added_page_btn_txt']) ? sanitize_text_field($date_form_data['coderockz_woo_delivery_order_added_page_btn_txt']) : "";
		$google_calendar_form_settings['google_calendar_order_added_page_btn_txt'] = $google_calendar_order_added_page_btn_txt;

		$order_status_sync = (isset($date_form_data['coderockz_woo_delivery_order_status_sync']) && !empty($date_form_data['coderockz_woo_delivery_order_status_sync'])) ? $date_form_data['coderockz_woo_delivery_order_status_sync'] : array();
		$order_status_sync = $this->helper->coderockz_woo_delivery_array_sanitize($order_status_sync);

		$google_calendar_form_settings['order_status_sync'] = $order_status_sync;

		$sync_custom_field_name = isset($date_form_data['coderockz_woo_delivery_sync_custom_field_name']) && $date_form_data['coderockz_woo_delivery_sync_custom_field_name'] != "" ? $date_form_data['coderockz_woo_delivery_sync_custom_field_name'] : "";

		while (strpos($sync_custom_field_name, ', ') !== FALSE) {
		    $sync_custom_field_name = str_replace(', ', ',', $sync_custom_field_name);
		}

		$sync_custom_field_name = explode(",",$sync_custom_field_name);
		$sync_custom_field_name = $this->helper->coderockz_woo_delivery_array_sanitize($sync_custom_field_name);

		$google_calendar_form_settings['sync_custom_field_name'] = $sync_custom_field_name;
		
		if(get_option('coderockz_woo_delivery_google_calendar_settings') == false) {
			update_option('coderockz_woo_delivery_google_calendar_settings', $google_calendar_form_settings);
		} else {
			$google_calendar_form_settings = array_merge(get_option('coderockz_woo_delivery_google_calendar_settings'),$google_calendar_form_settings);
			update_option('coderockz_woo_delivery_google_calendar_settings', $google_calendar_form_settings);
		}
		wp_send_json_success();
		
    }

    public function coderockz_woo_delivery_process_other_settings() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		
		$other_settings_form_settings = [];

		parse_str( $_POST[ 'dateFormData' ], $date_form_data );
		
		$field_position = sanitize_text_field($date_form_data['coderockz_woo_delivery_field_position']);
		$spinner_id = isset($date_form_data['coderockz-woo-delivery-spinner-upload-id']) ? sanitize_text_field($date_form_data['coderockz-woo-delivery-spinner-upload-id']) : "";
		$spinner_background = sanitize_text_field($date_form_data['coderockz_woo_delivery_spinner_animation_background']);
		$other_settings_form_settings['field_position'] = $field_position;
		$other_settings_form_settings['spinner-animation-id'] = $spinner_id;
		$other_settings_form_settings['spinner_animation_background'] = $spinner_background;

		$hide_heading_delivery_section = !isset($date_form_data['coderockz_woo_delivery_hide_heading_delivery_section']) ? false : true;
		$other_settings_form_settings['hide_heading_delivery_section'] = $hide_heading_delivery_section;

		$add_tax_delivery_pickup_fee = !isset($date_form_data['coderockz_woo_delivery_add_tax_delivery_pickup_fee']) ? false : true;
		$other_settings_form_settings['add_tax_delivery_pickup_fee'] = $add_tax_delivery_pickup_fee;

		$shipping_tax_class = sanitize_text_field($date_form_data['coderockz_delivery_shipping_tax_class']);
		$other_settings_form_settings['shipping_tax_class'] = $shipping_tax_class;

		$coderockz_disable_fields_for_downloadable_products = !isset($date_form_data['coderockz_disable_fields_for_downloadable_products']) ? false : true;
		$other_settings_form_settings['disable_fields_for_downloadable_products'] = $coderockz_disable_fields_for_downloadable_products;
		
		$coderockz_disable_fields_for_downloadable_regular_products = !isset($date_form_data['coderockz_disable_fields_for_downloadable_regular_products']) ? false : true;
		$other_settings_form_settings['disable_fields_for_downloadable_regular_products'] = $coderockz_disable_fields_for_downloadable_regular_products;

		$access_shop_manager = !isset($date_form_data['coderockz_woo_delivery_access_shop_manager']) ? false : true;
				
		$other_settings_form_settings['access_shop_manager'] = $access_shop_manager;

		$calendar_access_shop_manager = !isset($date_form_data['coderockz_woo_delivery_calendar_access_shop_manager']) ? false : true;

		$other_settings_form_settings['calendar_access_shop_manager'] = $calendar_access_shop_manager;

		$add_delivery_info_order_note = !isset($date_form_data['coderockz_woo_delivery_add_delivery_info_order_note']) ? false : true;
		$other_settings_form_settings['add_delivery_info_order_note'] = $add_delivery_info_order_note;

		$remove_delivery_status_column = !isset($date_form_data['coderockz_woo_delivery_remove_delivery_status_column']) ? false : true;
		$other_settings_form_settings['remove_delivery_status_column'] = $remove_delivery_status_column;

		$disable_dynamic_shipping = !isset($date_form_data['coderockz_woo_delivery_disable_dynamic_shipping_methods']) ? false : true;
		$other_settings_form_settings['disable_dynamic_shipping'] = $disable_dynamic_shipping;

		$hide_shipping_address = !isset($date_form_data['coderockz_woo_delivery_hide_shipping_address']) ? false : true;
		$other_settings_form_settings['hide_shipping_address'] = $hide_shipping_address;

		$mark_delivery_completed_with_order_completed = !isset($date_form_data['coderockz_woo_delivery_mark_delivery_completed_with_order_completed']) ? false : true;

		$other_settings_form_settings['mark_delivery_completed_with_order_completed'] = $mark_delivery_completed_with_order_completed;

		$hide_module_cart_total_zero = !isset($date_form_data['coderockz_woo_delivery_hide_module_cart_total_zero']) ? false : true;
		$other_settings_form_settings['hide_module_cart_total_zero'] = $hide_module_cart_total_zero;

		$hide_disabled_timeslot = !isset($date_form_data['coderockz_woo_delivery_hide_disabled_timeslot']) ? false : true;
		$other_settings_form_settings['hide_disabled_timeslot'] = $hide_disabled_timeslot;

		$hide_metadata_reports_calendar = !isset($date_form_data['coderockz_woo_delivery_hide_metadata_reports_calendar']) ? false : true;
		$other_settings_form_settings['hide_metadata_reports_calendar'] = $hide_metadata_reports_calendar;

		$additional_message = isset($date_form_data['coderockz_woo_delivery_additional_message']) ? sanitize_text_field(htmlentities($date_form_data['coderockz_woo_delivery_additional_message'])) : "";
		$other_settings_form_settings['additional_message'] = $additional_message;

		$hide_additional_message_for = (isset($date_form_data['coderockz_woo_delivery_hide_additional_message_for']) && !empty($date_form_data['coderockz_woo_delivery_hide_additional_message_for'])) ? $date_form_data['coderockz_woo_delivery_hide_additional_message_for'] : array();

		$other_settings_form_settings['hide_additional_message_for'] = $hide_additional_message_for;

		$custom_css = isset($date_form_data['coderockz_woo_delivery_code_editor_css']) ? sanitize_textarea_field(htmlentities($date_form_data['coderockz_woo_delivery_code_editor_css'])) : "";
		$other_settings_form_settings['custom_css'] = $custom_css;
		
		if(get_option('coderockz_woo_delivery_other_settings') == false) {
			update_option('coderockz_woo_delivery_other_settings', $other_settings_form_settings);
		} else {
			$other_settings_form_settings = array_merge(get_option('coderockz_woo_delivery_other_settings'),$other_settings_form_settings);
			update_option('coderockz_woo_delivery_other_settings', $other_settings_form_settings);
		}
		wp_send_json_success();
		
    }

    public function coderockz_woo_delivery_next_month_off_settings_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');

    	$next_month_off_form_settings = [];

		parse_str( $_POST[ 'formData' ], $form_data );

		$next_month_off_categories = (isset($form_data['coderockz_woo_delivery_next_month_off_categories']) && !empty($form_data['coderockz_woo_delivery_next_month_off_categories'])) ? $form_data['coderockz_woo_delivery_next_month_off_categories'] : array();
		$next_month_off_categories = $this->helper->coderockz_woo_delivery_array_sanitize($next_month_off_categories);

		$next_month_off_form_settings['next_month_off_categories'] = $next_month_off_categories;
		
		if(get_option('coderockz_woo_delivery_off_days_settings') == false) {
			update_option('coderockz_woo_delivery_off_days_settings', $next_month_off_form_settings);
		} else {
			$next_month_off_form_settings = array_merge(get_option('coderockz_woo_delivery_off_days_settings'),$next_month_off_form_settings);
			update_option('coderockz_woo_delivery_off_days_settings', $next_month_off_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_next_week_off_settings_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');

    	$next_week_off_form_settings = [];

		parse_str( $_POST[ 'formData' ], $form_data );

		$next_week_off_categories = (isset($form_data['coderockz_woo_delivery_next_week_off_categories']) && !empty($form_data['coderockz_woo_delivery_next_week_off_categories'])) ? $form_data['coderockz_woo_delivery_next_week_off_categories'] : array();
		$next_week_off_categories = $this->helper->coderockz_woo_delivery_array_sanitize($next_week_off_categories);

		$next_week_off_form_settings['next_week_off_categories'] = $next_week_off_categories;
		
		if(get_option('coderockz_woo_delivery_off_days_settings') == false) {
			update_option('coderockz_woo_delivery_off_days_settings', $next_week_off_form_settings);
		} else {
			$next_week_off_form_settings = array_merge(get_option('coderockz_woo_delivery_off_days_settings'),$next_week_off_form_settings);
			update_option('coderockz_woo_delivery_off_days_settings', $next_week_off_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_current_week_off_settings_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');

    	$current_week_off_form_settings = [];

		parse_str( $_POST[ 'formData' ], $form_data );

		$current_week_off_categories = (isset($form_data['coderockz_woo_delivery_current_week_off_categories']) && !empty($form_data['coderockz_woo_delivery_current_week_off_categories'])) ? $form_data['coderockz_woo_delivery_current_week_off_categories'] : array();
		$current_week_off_categories = $this->helper->coderockz_woo_delivery_array_sanitize($current_week_off_categories);

		$current_week_off_form_settings['current_week_off_categories'] = $current_week_off_categories;
		
		if(get_option('coderockz_woo_delivery_off_days_settings') == false) {
			update_option('coderockz_woo_delivery_off_days_settings', $current_week_off_form_settings);
		} else {
			$current_week_off_form_settings = array_merge(get_option('coderockz_woo_delivery_off_days_settings'),$current_week_off_form_settings);
			update_option('coderockz_woo_delivery_off_days_settings', $current_week_off_form_settings);
		}

		wp_send_json_success();
	}

    public function coderockz_woo_delivery_exclusion_settings_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');

    	$exclusion_form_settings = [];

		parse_str( $_POST[ 'formData' ], $form_data );

		$exclude_categories = (isset($form_data['coderockz_woo_delivery_exclude_product_categories']) && !empty($form_data['coderockz_woo_delivery_exclude_product_categories'])) ? $form_data['coderockz_woo_delivery_exclude_product_categories'] : array();
		$exclude_categories = $this->helper->coderockz_woo_delivery_array_sanitize($exclude_categories);

		$exclusion_form_settings['exclude_categories'] = $exclude_categories;
		
		if(get_option('coderockz_woo_delivery_large_product_list') == false) {
			$exclude_product = (isset($form_data['coderockz_woo_delivery_exclude_individual_product']) && !empty($form_data['coderockz_woo_delivery_exclude_individual_product'])) ? $form_data['coderockz_woo_delivery_exclude_individual_product'] : array();
		$exclude_product = $this->helper->coderockz_woo_delivery_array_sanitize($exclude_product);
		} else {
			$exclude_product = isset($form_data['coderockz_woo_delivery_exclude_individual_product_input']) && $form_data['coderockz_woo_delivery_exclude_individual_product_input'] != "" ? $form_data['coderockz_woo_delivery_exclude_individual_product_input'] : "";
			$exclude_product = explode(",",$exclude_product);
			$exclude_product = $this->helper->coderockz_woo_delivery_array_sanitize($exclude_product);
		}

		$exclusion_form_settings['exclude_products'] = $exclude_product;

		$reverse_current_condition = !isset($form_data['coderockz_delivery_exclusion_reverse_current_condition']) ? false : true;

		$exclusion_form_settings['reverse_current_condition'] = $reverse_current_condition;

		$exclude_shipping_methods = (isset($form_data['coderockz_woo_delivery_exclude_shipping_methods']) && !empty($form_data['coderockz_woo_delivery_exclude_shipping_methods'])) ? $form_data['coderockz_woo_delivery_exclude_shipping_methods'] : array();
		$exclude_shipping_methods = $this->helper->coderockz_woo_delivery_array_sanitize($exclude_shipping_methods);

		$exclusion_form_settings['exclude_shipping_methods'] = $exclude_shipping_methods;

		$exclude_user_roles = (isset($form_data['coderockz_woo_delivery_exclude_user_roles']) && !empty($form_data['coderockz_woo_delivery_exclude_user_roles'])) ? $form_data['coderockz_woo_delivery_exclude_user_roles'] : array();
		$exclude_user_roles = $this->helper->coderockz_woo_delivery_array_sanitize($exclude_user_roles);

		$exclusion_form_settings['exclude_user_roles'] = $exclude_user_roles;

		$exclusion_non_logged_in_user = !isset($form_data['coderockz_delivery_exclusion_non_logged_in_user']) ? false : true;

		$exclusion_form_settings['exclusion_non_logged_in_user'] = $exclusion_non_logged_in_user;

		$exclude_shipping_method_title = isset($form_data['coderockz_woo_delivery_exclude_shipping_method_title']) && $form_data['coderockz_woo_delivery_exclude_shipping_method_title'] != "" ? $form_data['coderockz_woo_delivery_exclude_shipping_method_title'] : "";

		if($exclude_shipping_method_title != "") {
			while (strpos($exclude_shipping_method_title, ', ') !== FALSE) {
			    $exclude_shipping_method_title = str_replace(', ', ',', $exclude_shipping_method_title);
			}

			$exclude_shipping_method_title = explode(",",$exclude_shipping_method_title);
			$exclude_shipping_method_title = $this->helper->coderockz_woo_delivery_array_sanitize($exclude_shipping_method_title);
		} else {
			$exclude_shipping_method_title = [];
		}
		
		$exclusion_form_settings['exclude_shipping_method_title'] = $exclude_shipping_method_title;

		$minimum_amount_hide_plugin = sanitize_text_field($form_data['coderockz_woo_delivery_minimum_amount_hide_plugin']);

		$calculating_include_discount_hide_plugin = !isset($form_data['coderockz_woo_delivery_calculating_include_discount_hide_plugin']) ? false : true;
		$calculating_include_tax_hide_plugin = !isset($form_data['coderockz_woo_delivery_calculating_include_tax_hide_plugin']) ? false : true;

		$hide_plugin_amount_restriction_notice = sanitize_text_field($form_data['coderockz_woo_delivery_hide_plugin_amount_restriction_notice']);

		$exclusion_form_settings['minimum_amount_hide_plugin'] = $minimum_amount_hide_plugin;
		$exclusion_form_settings['calculating_include_discount_hide_plugin'] = $calculating_include_discount_hide_plugin;
		$exclusion_form_settings['calculating_include_tax_hide_plugin'] = $calculating_include_tax_hide_plugin;
		$exclusion_form_settings['hide_plugin_amount_restriction_notice'] = $hide_plugin_amount_restriction_notice;
		
		if(get_option('coderockz_woo_delivery_exclude_settings') == false) {
			update_option('coderockz_woo_delivery_exclude_settings', $exclusion_form_settings);
		} else {
			$exclusion_form_settings = array_merge(get_option('coderockz_woo_delivery_exclude_settings'),$exclusion_form_settings);
			update_option('coderockz_woo_delivery_exclude_settings', $exclusion_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_overall_laundry_store_settings_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $laundry_store_form_data );
		$laundry_store_form_settings = [];
		
		$enable_laundry_store_settings = !isset($laundry_store_form_data['coderockz_woo_delivery_enable_laundry_store_settings']) ? false : true;

		$laundry_store_form_settings['enable_laundry_store_settings'] = $enable_laundry_store_settings;

		$overall_after_pickup_dates = sanitize_text_field($laundry_store_form_data['coderockz_delivery_overall_after_pickup_dates']);
		$laundry_store_form_settings['overall_after_pickup_dates'] = $overall_after_pickup_dates;

		$delivery_date_consider_disabled_days = !isset($laundry_store_form_data['coderockz_delivery_date_consider_disabled_days']) ? false : true;

		$laundry_store_form_settings['delivery_date_consider_disabled_days'] = $delivery_date_consider_disabled_days;

		$delivery_date_consider_selected_pickup_date = !isset($laundry_store_form_data['coderockz_delivery_date_consider_selected_pickup_date']) ? false : true;

		$laundry_store_form_settings['delivery_date_consider_selected_pickup_date'] = $delivery_date_consider_selected_pickup_date;

		$overall_after_pickup_time = sanitize_text_field($laundry_store_form_data['coderockz_delivery_overall_after_pickup_time']);
		$laundry_store_form_settings['overall_after_pickup_time'] = $overall_after_pickup_time;

		if(get_option('coderockz_woo_delivery_laundry_store_settings') == false) {
			update_option('coderockz_woo_delivery_laundry_store_settings', $laundry_store_form_settings);
		} else {
			$laundry_store_form_settings = array_merge(get_option('coderockz_woo_delivery_laundry_store_settings'),$laundry_store_form_settings);
			update_option('coderockz_woo_delivery_laundry_store_settings', $laundry_store_form_settings);
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_category_after_pickup_dates_form() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		parse_str( $_POST[ 'dateFormData' ], $category_after_pickup_dates_form_data );
		$category_after_pickup_dates = [];
		$category_after_pickup_dates_categories = $this->helper->coderockz_woo_delivery_array_sanitize($category_after_pickup_dates_form_data['coderockz_delivery_after_pickup_days_categories']);
		foreach($category_after_pickup_dates_categories as $category_after_pickup_dates_category) {
			$category = str_replace("c-w-d"," ", $category_after_pickup_dates_category);
			
			if(!empty($category_after_pickup_dates_form_data['coderockz-woo-delivery-after-pickup-days-'.$category_after_pickup_dates_category]) && $category != "") {

			$category_after_pickup_dates[$category] = sanitize_text_field($category_after_pickup_dates_form_data['coderockz-woo-delivery-after-pickup-days-'.$category_after_pickup_dates_category]);

			}
		}
		$enable_category_after_pickup_dates = !isset($category_after_pickup_dates_form_data['coderockz_woo_delivery_enable_category_after_pickup_dates']) ? false : true;

		$category_after_pickup_dates_form_settings['enable_category_after_pickup_dates'] = $enable_category_after_pickup_dates;
		$category_after_pickup_dates_form_settings['category_after_pickup_dates'] = $category_after_pickup_dates;

		if(get_option('coderockz_woo_delivery_laundry_store_settings') == false) {
			update_option('coderockz_woo_delivery_laundry_store_settings', $category_after_pickup_dates_form_settings);
		} else {
			$category_after_pickup_dates_form_settings = array_merge(get_option('coderockz_woo_delivery_laundry_store_settings'),$category_after_pickup_dates_form_settings);
			update_option('coderockz_woo_delivery_laundry_store_settings', $category_after_pickup_dates_form_settings);
		}

		wp_send_json_success();
	}
  
    public function coderockz_woo_delivery_process_localization_settings() { 
    	check_ajax_referer('coderockz_woo_delivery_nonce');
		
		$localization_settings_form_settings = [];

		parse_str( $_POST[ 'formData' ], $form_data );
		
		$order_limit_notice = sanitize_text_field($form_data['coderockz_woo_delivery_order_limit_notice']);
		$pickup_limit_notice = sanitize_text_field($form_data['coderockz_woo_delivery_pickup_limit_notice']);
		$pickup_location_limit_notice = sanitize_text_field($form_data['coderockz_woo_delivery_pickup_location_limit_notice']);
		$delivery_heading_checkout = sanitize_text_field($form_data['coderockz_woo_delivery_delivery_heading_checkout']);
		$pickup_heading_checkout = sanitize_text_field($form_data['coderockz_woo_delivery_pickup_heading_checkout']);
		$delivery_pickup_heading_checkout = sanitize_text_field($form_data['coderockz_woo_delivery_delivery_pickup_heading_checkout']);
		$no_timeslot_available = sanitize_text_field($form_data['coderockz_woo_delivery_no_timeslot_available_notice']);
		$select_delivery_date_notice = sanitize_text_field($form_data['coderockz_woo_delivery_select_delivery_date_notice']);
		$select_pickup_date_notice = sanitize_text_field($form_data['coderockz_woo_delivery_select_pickup_date_notice']);
		$select_pickup_date_location_notice = sanitize_text_field($form_data['coderockz_woo_delivery_select_pickup_date_location_notice']);
		$select_pickup_location_notice = sanitize_text_field($form_data['coderockz_woo_delivery_select_pickup_location_notice']);

		$select_pickup_time_notice = sanitize_text_field($form_data['coderockz_woo_delivery_select_pickup_time_notice']);

		$select_pickup_time_delivery_date_notice = sanitize_text_field($form_data['coderockz_woo_delivery_select_pickup_time_delivery_date_notice']);		
		$delivery_details_text = sanitize_text_field($form_data['coderockz_woo_delivery_delivery_details_text']);
		$delivery_status_text = sanitize_text_field($form_data['coderockz_woo_delivery_delivery_status_text']);
		$delivery_status_not_delivered_text = sanitize_text_field($form_data['coderockz_woo_delivery_delivery_status_not_delivered_text']);
		$delivery_status_delivered_text = sanitize_text_field($form_data['coderockz_woo_delivery_delivery_status_delivered_text']);
		$pickup_status_not_picked_text = sanitize_text_field($form_data['coderockz_woo_delivery_pickup_status_not_picked_text']);
		$pickup_status_picked_text = sanitize_text_field($form_data['coderockz_woo_delivery_pickup_status_picked_text']);
		$order_metabox_heading = sanitize_text_field($form_data['coderockz_woo_delivery_order_metabox_heading']);
		$checkout_delivery_option_notice = sanitize_text_field($form_data['coderockz_woo_delivery_checkout_delivery_option_notice']);
		$checkout_date_notice = sanitize_text_field($form_data['coderockz_woo_delivery_checkout_date_notice']);
		$checkout_pickup_date_notice = sanitize_text_field($form_data['coderockz_woo_delivery_checkout_pickup_date_notice']);
		$checkout_time_notice = sanitize_text_field($form_data['coderockz_woo_delivery_checkout_time_notice']);
		$checkout_tips_notice = sanitize_text_field($form_data['coderockz_woo_delivery_checkout_tips_notice']);
		$checkout_pickup_time_notice = sanitize_text_field($form_data['coderockz_woo_delivery_checkout_pickup_time_notice']);
		$checkout_pickup_notice = sanitize_text_field($form_data['coderockz_woo_delivery_checkout_pickup_notice']);
		$location_map_click_here = sanitize_text_field($form_data['coderockz_woo_delivery_location_map_click_here']);
		$to_see_map_location = sanitize_text_field($form_data['coderockz_woo_delivery_to_see_map_location']);
		$pickup_location_fee_text = sanitize_text_field($form_data['coderockz_woo_delivery_pickup_location_fee_text']);
		$checkout_additional_field_notice = sanitize_text_field($form_data['coderockz_woo_delivery_checkout_additional_field_notice']);
		$delivery_fee_text = sanitize_text_field($form_data['coderockz_woo_delivery_checkout_delivery_fee_text']);
		$as_soon_as_possible_fee_text = sanitize_text_field($form_data['coderockz_woo_delivery_checkout_as_soon_as_possible_fee_text']);
		$pickup_fee_text = sanitize_text_field($form_data['coderockz_woo_delivery_checkout_pickup_fee_text']);
		$conditional_fee_text = sanitize_text_field($form_data['coderockz_woo_delivery_conditional_fee_text']);
		$sameday_fee_text = sanitize_text_field($form_data['coderockz_woo_delivery_same_day_delivery_fee_text']);
		$nextday_fee_text = sanitize_text_field($form_data['coderockz_woo_delivery_next_day_delivery_fee_text']);
		$day_after_tomorrow_fee_text = sanitize_text_field($form_data['coderockz_woo_delivery_day_after_tomorrow_delivery_fee_text']);
		$other_fee_text = sanitize_text_field($form_data['coderockz_woo_delivery_other_day_delivery_fee_text']);
		$weekday_fee_text = sanitize_text_field($form_data['coderockz_woo_delivery_weekday_fee_text']);
		$specific_date_fee_text = sanitize_text_field($form_data['coderockz_woo_delivery_specific_date_fee_text']);
		$only_available_for_today_text = sanitize_text_field($form_data['coderockz_woo_delivery_only_available_for_today_text']);
		$free_shipping_other_day_text = sanitize_text_field($form_data['coderockz_woo_delivery_free_shipping_other_day_text']);
		$only_available_for_text = sanitize_text_field($form_data['coderockz_woo_delivery_only_available_for_text']);
		$urgent_delivery_fee_text = sanitize_text_field($form_data['coderockz_woo_delivery_urgent_delivery_fee_text']);
		$need_to_select_text = sanitize_text_field($form_data['coderockz_woo_delivery_need_to_select_text']);
		$if_available_text = sanitize_text_field($form_data['coderockz_woo_delivery_if_available_text']);
		$purchase_products_separately_delivery = sanitize_text_field($form_data['coderockz_woo_delivery_purchase_products_separately_delivery_text']);
		$purchase_products_separately_pickup = sanitize_text_field($form_data['coderockz_woo_delivery_purchase_products_separately_pickup_text']);
		$metabox_order_creation = sanitize_text_field($form_data['coderockz_woo_delivery_metabox_order_creation_text']);
		$localization_settings_form_settings['order_limit_notice'] = $order_limit_notice;
		$localization_settings_form_settings['pickup_limit_notice'] = $pickup_limit_notice;
		$localization_settings_form_settings['pickup_location_limit_notice'] = $pickup_location_limit_notice;
		$localization_settings_form_settings['delivery_status_text'] = $delivery_status_text;
		$localization_settings_form_settings['delivery_heading_checkout'] = $delivery_heading_checkout;
		$localization_settings_form_settings['pickup_heading_checkout'] = $pickup_heading_checkout;
		$localization_settings_form_settings['delivery_pickup_heading_checkout'] = $delivery_pickup_heading_checkout;
		$localization_settings_form_settings['no_timeslot_available'] = $no_timeslot_available;
		$localization_settings_form_settings['delivery_details_text'] = $delivery_details_text;
		$localization_settings_form_settings['select_delivery_date_notice'] = $select_delivery_date_notice;
		$localization_settings_form_settings['select_pickup_date_notice'] = $select_pickup_date_notice;
		$localization_settings_form_settings['select_pickup_date_location_notice'] = $select_pickup_date_location_notice;
		$localization_settings_form_settings['select_pickup_location_notice'] = $select_pickup_location_notice;
		$localization_settings_form_settings['select_pickup_time_notice'] = $select_pickup_time_notice;
		$localization_settings_form_settings['select_pickup_time_delivery_date_notice'] = $select_pickup_time_delivery_date_notice;
		$localization_settings_form_settings['delivery_status_not_delivered_text'] = $delivery_status_not_delivered_text;
		$localization_settings_form_settings['delivery_status_delivered_text'] = $delivery_status_delivered_text;
		$localization_settings_form_settings['pickup_status_not_picked_text'] = $pickup_status_not_picked_text;
		$localization_settings_form_settings['pickup_status_picked_text'] = $pickup_status_picked_text;
		$localization_settings_form_settings['order_metabox_heading'] = $order_metabox_heading;
		$localization_settings_form_settings['checkout_delivery_option_notice'] = $checkout_delivery_option_notice;
		$localization_settings_form_settings['checkout_date_notice'] = $checkout_date_notice;
		$localization_settings_form_settings['checkout_pickup_date_notice'] = $checkout_pickup_date_notice;
		$localization_settings_form_settings['checkout_pickup_time_notice'] = $checkout_pickup_time_notice;
		$localization_settings_form_settings['checkout_time_notice'] = $checkout_time_notice;
		$localization_settings_form_settings['checkout_tips_notice'] = $checkout_tips_notice;
		$localization_settings_form_settings['checkout_pickup_notice'] = $checkout_pickup_notice;
		$localization_settings_form_settings['location_map_click_here'] = $location_map_click_here;
		$localization_settings_form_settings['to_see_map_location'] = $to_see_map_location;
		$localization_settings_form_settings['pickup_location_fee_text'] = $pickup_location_fee_text;
		$localization_settings_form_settings['checkout_additional_field_notice'] = $checkout_additional_field_notice;
		$localization_settings_form_settings['delivery_fee_text'] = $delivery_fee_text;
		$localization_settings_form_settings['as_soon_as_possible_fee_text'] = $as_soon_as_possible_fee_text;
		$localization_settings_form_settings['pickup_fee_text'] = $pickup_fee_text;
		$localization_settings_form_settings['conditional_fee_text'] = $conditional_fee_text;
		$localization_settings_form_settings['sameday_fee_text'] = $sameday_fee_text;
		$localization_settings_form_settings['nextday_fee_text'] = $nextday_fee_text;
		$localization_settings_form_settings['day_after_tomorrow_fee_text'] = $day_after_tomorrow_fee_text;
		$localization_settings_form_settings['other_fee_text'] = $other_fee_text;
		$localization_settings_form_settings['weekday_fee_text'] = $weekday_fee_text;
		$localization_settings_form_settings['specific_date_fee_text'] = $specific_date_fee_text;
		$localization_settings_form_settings['only_available_for_today_text'] = $only_available_for_today_text;
		$localization_settings_form_settings['free_shipping_other_day_text'] = $free_shipping_other_day_text;
		$localization_settings_form_settings['only_available_for_text'] = $only_available_for_text;
		$localization_settings_form_settings['urgent_delivery_fee_text'] = $urgent_delivery_fee_text;
		$localization_settings_form_settings['need_to_select_text'] = $need_to_select_text;
		$localization_settings_form_settings['if_available_text'] = $if_available_text;
		$localization_settings_form_settings['purchase_products_separately_delivery'] = $purchase_products_separately_delivery;
		$localization_settings_form_settings['purchase_products_separately_pickup'] = $purchase_products_separately_pickup;
		$localization_settings_form_settings['metabox_order_creation'] = $metabox_order_creation;
		
		if(get_option('coderockz_woo_delivery_localization_settings') == false) {
			update_option('coderockz_woo_delivery_localization_settings', $localization_settings_form_settings);
		} else {
			$localization_settings_form_settings = array_merge(get_option('coderockz_woo_delivery_localization_settings'),$localization_settings_form_settings);
			update_option('coderockz_woo_delivery_localization_settings', $localization_settings_form_settings);
		}
		wp_send_json_success();
		
    }

    /**
	 * Add custom column in orders page in admin panel
	*/
	public function coderockz_woo_delivery_add_custom_fields_orders_list($columns) {
		$delivery_details_text = (isset(get_option('coderockz_woo_delivery_localization_settings')['delivery_details_text']) && !empty(get_option('coderockz_woo_delivery_localization_settings')['delivery_details_text'])) ? stripslashes(get_option('coderockz_woo_delivery_localization_settings')['delivery_details_text']) : __("Delivery Details","coderockz-woo-delivery");

		$delivery_status_text = (isset(get_option('coderockz_woo_delivery_localization_settings')['delivery_status_text']) && !empty(get_option('coderockz_woo_delivery_localization_settings')['delivery_status_text'])) ? stripslashes(get_option('coderockz_woo_delivery_localization_settings')['delivery_status_text']) : __("Delivery Status","coderockz-woo-delivery");

		$new_columns = [];

		$other_settings = get_option('coderockz_woo_delivery_other_settings');
		$remove_delivery_status_column = (isset($other_settings['remove_delivery_status_column']) && !empty($other_settings['remove_delivery_status_column'])) ? $other_settings['remove_delivery_status_column'] : false;

		foreach($columns as $name => $value)
		{
			$new_columns[$name] = $value;

			if($name == 'order_status') {
				$new_columns['order_delivery_details'] = $delivery_details_text;
				if(!$remove_delivery_status_column) {
					$new_columns['order_delivery_status'] = $delivery_status_text;
				}
				
			}
		}
		return $new_columns;

	}

	public function coderockz_woo_delivery_deliverywise_order_sort( $columns ) {

	    $meta_key = 'order_delivery_details';
    	return wp_parse_args( array('order_delivery_details' => $meta_key), $columns );
	}

	public function coderockz_woo_delivery_meta_field_sortable_orderby( $query ) {
	    global $pagenow;

	    if ( !is_admin() ) { 
	    	return; 
	    }

	    if ( 'edit.php' === $pagenow && isset($_GET['post_type']) && 'shop_order' === $_GET['post_type'] ){

	        $orderby  = $query->get( 'orderby');

	        if ('order_delivery_details' === $orderby){
	          $query->set('meta_key', 'delivery_details_timestamp');
	          $query->set('orderby', 'meta_value');
	          $query->set('order', $_GET['order']);
	        }
	    }

	    return $query;
	}


	public function coderockz_woo_delivery_meta_field_sortable_orderby_hpos( $clauses ) {	
			
		if (  isset( $_GET['page'] ) && 'wc-orders' === $_GET['page'] && isset( $_GET[ 'orderby' ] ) && 'order_delivery_details' === $_GET[ 'orderby' ] && isset($_GET['order'])) {
								
			global $wpdb;

			$clauses['join'] .= ' LEFT JOIN ' . $wpdb->prefix . "wc_orders_meta wom ON ( wom.order_id = " . $wpdb->prefix . "wc_orders.id AND wom.meta_key = 'delivery_details_timestamp' )
			LEFT JOIN " . $wpdb->prefix . "wc_orders_meta wom2 ON ( wom2.order_id = " . $wpdb->prefix . "wc_orders.id AND wom2.meta_key = 'delivery_details_timestamp' )";

			$orderby = ( ! isset( $_GET['order'] ) || 'desc' === $_GET['order'] ) ? 'DESC' : 'ASC';
			$orderby = " COALESCE( wom2.meta_value, wom.meta_value ) " . $orderby ." " ;

			$clauses['orderby'] =  ! empty( $clauses['orderby'] ) ? $orderby . ', ' . $clauses['orderby'] : $orderby;


		}

		return $clauses;
	
	}
	

	public function coderockz_woo_delivery_deliverywise_searchable_field( $meta_keys ){
	    $meta_keys[] = 'order_delivery_details';
	    return $meta_keys;
	}

	public function coderockz_woo_delivery_deliverywise_searchable_field_hpos( $meta_keys ){
	    $meta_keys[] = 'delivery_type';
	    $meta_keys[] = 'delivery_date';
	    $meta_keys[] = 'delivery_time';
	    $meta_keys[] = 'pickup_date';
	    $meta_keys[] = 'pickup_time';
	    $meta_keys[] = 'pickup_location';
	    $meta_keys[] = 'additional_note';
	    return $meta_keys;
	}

	public function coderockz_woo_delivery_show_custom_fields_data_orders_list($column) {
		global $post;

		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');			
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');			
		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
		$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
		$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');
		$localization_settings = get_option('coderockz_woo_delivery_localization_settings');

		$delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : __("Delivery Date","coderockz-woo-delivery");
		$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : __("Pickup Date","coderockz-woo-delivery");
		$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : __("Delivery Time","coderockz-woo-delivery");
		$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : __("Pickup Time","coderockz-woo-delivery");
		$pickup_location_field_label = (isset($delivery_pickup_settings['field_label']) && !empty($delivery_pickup_settings['field_label'])) ? stripslashes($delivery_pickup_settings['field_label']) : __("Pickup Location","coderockz-woo-delivery");
		$additional_field_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? stripslashes($additional_field_settings['field_label']) : __("Special Note About Delivery","coderockz-woo-delivery");

		$localization_settings = get_option('coderockz_woo_delivery_localization_settings');
		$delivery_status_not_delivered_text = (isset($localization_settings['delivery_status_not_delivered_text']) && !empty($localization_settings['delivery_status_not_delivered_text'])) ? stripslashes($localization_settings['delivery_status_not_delivered_text']) :  __("Not Delivered","coderockz-woo-delivery");
		$delivery_status_delivered_text = (isset($localization_settings['delivery_status_delivered_text']) && !empty($localization_settings['delivery_status_delivered_text'])) ? stripslashes($localization_settings['delivery_status_delivered_text']) :  __("Delivery Completed","coderockz-woo-delivery");
		$pickup_status_not_picked_text = (isset($localization_settings['pickup_status_not_picked_text']) && !empty($localization_settings['pickup_status_not_picked_text'])) ? stripslashes($localization_settings['pickup_status_not_picked_text']) :  __("Not Picked","coderockz-woo-delivery");
		$pickup_status_picked_text = (isset($localization_settings['pickup_status_picked_text']) && !empty($localization_settings['pickup_status_picked_text'])) ? stripslashes($localization_settings['pickup_status_picked_text']) :  __("Pickup Completed","coderockz-woo-delivery");

		if($column == 'order_delivery_details')
		{
			if(metadata_exists('post', $post->ID, 'pickup_date') && get_post_meta($post->ID, 'pickup_date', true) !="")
			{
		    	$pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";

				$pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

				if($pickup_add_weekday_name) {
					$pickup_date_format = "l ".$pickup_date_format;
				}

		    	$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime(get_post_meta( $post->ID, 'pickup_date', true ))),"pickup"),"pickup");

		    	echo $pickup_date_field_label.": " . $pickup_date; 	
			}

			if(metadata_exists('post', $post->ID, 'pickup_time') && get_post_meta($post->ID, 'pickup_time', true) !="")
			{
				$pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
				if($pickup_time_format == 12) {
					$pickup_time_format = "h:i A";
				} elseif ($pickup_time_format == 24) {
					$pickup_time_format = "H:i";
				}

				echo " <br > ";
				$pickup_times = get_post_meta($post->ID,"pickup_time",true);
				$pickup_minutes = explode(' - ', $pickup_times);

	    		if(!isset($pickup_minutes[1])) {
	    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0]));
	    		} else {

	    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1]));
	    		}

				echo $pickup_time_field_label.": " . $pickup_time_value;
			}

			if(metadata_exists('post', $post->ID, 'pickup_location') && get_post_meta($post->ID, 'pickup_location', true) !="")
			{
				echo "<br >";
				echo $pickup_location_field_label.": " . stripslashes(html_entity_decode(get_post_meta($post->ID, 'pickup_location', true), ENT_QUOTES));
			}
			
			if(metadata_exists('post', $post->ID, 'delivery_date') && get_post_meta($post->ID, 'delivery_date', true) !="")
			{
				if(metadata_exists('post', $post->ID, 'pickup_date') && get_post_meta($post->ID, 'pickup_date', true) !="")
				{
					echo " <br > ";
				}

				$delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";
				$add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

				if($add_weekday_name) {
					$delivery_date_format = "l ".$delivery_date_format;
				}

				$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime(get_post_meta( $post->ID, 'delivery_date', true ))),"delivery"),"delivery");
		    	
		    	echo $delivery_date_field_label.": " . $delivery_date;	
			}

			if(metadata_exists('post', $post->ID, 'delivery_time') && get_post_meta($post->ID, 'delivery_time', true) !="")
			{
				$time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
				if($time_format == 12) {
					$time_format = "h:i A";
				} elseif ($time_format == 24) {
					$time_format = "H:i";
				}

				echo " <br > ";
				if(get_post_meta($post->ID,"delivery_time",true) == "as-soon-as-possible") {
					$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : "As Soon As Possible";
					$time_value = $as_soon_as_possible_text;
				} else {
					$times = get_post_meta($post->ID,"delivery_time",true);
					$minutes = explode(' - ', $times);

		    		if(!isset($minutes[1])) {
		    			$time_value = date($time_format, strtotime($minutes[0]));
		    		} else {
		    			$time_value = date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1]));
		    		}

	    		}

				echo $delivery_time_field_label.": " . $time_value;

			}

			$delivery_tips_settings = get_option('coderockz_woo_delivery_delivery_tips_settings');
			$delivery_tips_field_label = (isset($delivery_tips_settings['delivery_tips_field_label']) && !empty($delivery_tips_settings['delivery_tips_field_label'])) ? stripslashes($delivery_tips_settings['delivery_tips_field_label']) : __( "Tips to Delivery Person", 'coderockz-woo-delivery' );
			$tips_fee = 0;
			$order = wc_get_order($post->ID);
			foreach( $order->get_items('fee') as $item_id => $item_fee ){
			    if( $item_fee['name'] == $delivery_tips_field_label ) {
			    	$tips_fee = $item_fee['total'] + $item_fee['total_tax'];
			    	break;
			    }
			}

			if($tips_fee != 0) {

				if(class_exists('WOOCS_STARTER')){
					global $WOOCS;
                	$currencies=$WOOCS->get_currencies();
                	$currency_symbol = $currencies[$WOOCS->current_currency]['symbol'];
					$fee = $this->helper->postion_currency_symbol($currency_symbol,apply_filters('woocs_exchange_value', $tips_fee));
				} else {
					$currency_symbol = get_woocommerce_currency_symbol();
					$fee = $this->helper->postion_currency_symbol($currency_symbol,$tips_fee);
				}

				echo " <br > ";
				echo $delivery_tips_field_label.": " . $fee;
			}

		}

		if($column == 'order_delivery_status' && metadata_exists('post', $post->ID, 'delivery_type') && get_post_meta($post->ID, 'delivery_type', true) !="")
		{

			if(metadata_exists('post', $post->ID, 'delivery_status') && get_post_meta($post->ID, 'delivery_status', true) !="" && get_post_meta($post->ID, 'delivery_status', true) == "delivered")
			{

				if(metadata_exists('post', $post->ID, 'delivery_type') && get_post_meta($post->ID, 'delivery_type', true) !="" && get_post_meta($post->ID, 'delivery_type', true) =="pickup") {
					$delivery_status = '<span class="coderockz_woo_delivery_delivered_text">'.$pickup_status_picked_text.'</span>';
				} else {
					$delivery_status = '<span class="coderockz_woo_delivery_delivered_text">'.$delivery_status_delivered_text.'</span>';
				}

				echo $delivery_status;
			} else {

				if(metadata_exists('post', $post->ID, 'delivery_type') && get_post_meta($post->ID, 'delivery_type', true) !="" && get_post_meta($post->ID, 'delivery_type', true) =="pickup") {
					$delivery_status = '<span class="coderockz_woo_delivery_not_delivered_text">'.$pickup_status_not_picked_text.'</span>';
				} else {
					$delivery_status = '<span class="coderockz_woo_delivery_not_delivered_text">'.$delivery_status_not_delivered_text.'</span>';
				}

				echo $delivery_status;
			}

		}

	}


	public function coderockz_woo_delivery_show_custom_fields_data_orders_list_hpos($column, $order) {
		
		if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
		    $order_id = $order->get_id();
		} else {
		    $order_id = $order->id;
		}

		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');			
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');			
		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
		$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
		$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');
		$localization_settings = get_option('coderockz_woo_delivery_localization_settings');

		$delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : __("Delivery Date","coderockz-woo-delivery");
		$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : __("Pickup Date","coderockz-woo-delivery");
		$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : __("Delivery Time","coderockz-woo-delivery");
		$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : __("Pickup Time","coderockz-woo-delivery");
		$pickup_location_field_label = (isset($delivery_pickup_settings['field_label']) && !empty($delivery_pickup_settings['field_label'])) ? stripslashes($delivery_pickup_settings['field_label']) : __("Pickup Location","coderockz-woo-delivery");
		$additional_field_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? stripslashes($additional_field_settings['field_label']) : __("Special Note About Delivery","coderockz-woo-delivery");

		$localization_settings = get_option('coderockz_woo_delivery_localization_settings');
		$delivery_status_not_delivered_text = (isset($localization_settings['delivery_status_not_delivered_text']) && !empty($localization_settings['delivery_status_not_delivered_text'])) ? stripslashes($localization_settings['delivery_status_not_delivered_text']) :  __("Not Delivered","coderockz-woo-delivery");
		$delivery_status_delivered_text = (isset($localization_settings['delivery_status_delivered_text']) && !empty($localization_settings['delivery_status_delivered_text'])) ? stripslashes($localization_settings['delivery_status_delivered_text']) :  __("Delivery Completed","coderockz-woo-delivery");
		$pickup_status_not_picked_text = (isset($localization_settings['pickup_status_not_picked_text']) && !empty($localization_settings['pickup_status_not_picked_text'])) ? stripslashes($localization_settings['pickup_status_not_picked_text']) :  __("Not Picked","coderockz-woo-delivery");
		$pickup_status_picked_text = (isset($localization_settings['pickup_status_picked_text']) && !empty($localization_settings['pickup_status_picked_text'])) ? stripslashes($localization_settings['pickup_status_picked_text']) :  __("Pickup Completed","coderockz-woo-delivery");

		if($column == 'order_delivery_details')
		{
			if($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true )!= "")
			{
		    	$pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";

				$pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

				if($pickup_add_weekday_name) {
					$pickup_date_format = "l ".$pickup_date_format;
				}

		    	$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime($order->get_meta( 'pickup_date', true ))),"pickup"),"pickup");

		    	echo $pickup_date_field_label.": " . $pickup_date; 	
			}

			if($order->meta_exists('pickup_time') && $order->get_meta( 'pickup_time', true )!= "")
			{
				$pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
				if($pickup_time_format == 12) {
					$pickup_time_format = "h:i A";
				} elseif ($pickup_time_format == 24) {
					$pickup_time_format = "H:i";
				}

				echo " <br > ";
				$pickup_times = $order->get_meta( 'pickup_time', true );
				$pickup_minutes = explode(' - ', $pickup_times);

	    		if(!isset($pickup_minutes[1])) {
	    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0]));
	    		} else {

	    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1]));
	    		}

				echo $pickup_time_field_label.": " . $pickup_time_value;

			}

			if($order->meta_exists('pickup_location') && $order->get_meta( 'pickup_location', true )!= "")
			{
				echo "<br >";
				echo $pickup_location_field_label.": " . stripslashes(html_entity_decode($order->get_meta( 'pickup_location', true ), ENT_QUOTES));
			}
			
			if($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true )!= "")
			{
				if($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true )!= "")
				{
					echo " <br > ";
				}

				$delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";
				$add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

				if($add_weekday_name) {
					$delivery_date_format = "l ".$delivery_date_format;
				}

				$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime($order->get_meta( 'delivery_date', true ))),"delivery"),"delivery");
		    	
		    	echo $delivery_date_field_label.": " . $delivery_date;	
			}

			if($order->meta_exists('delivery_time') && $order->get_meta( 'delivery_time', true )!= "")
			{
				$time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
				if($time_format == 12) {
					$time_format = "h:i A";
				} elseif ($time_format == 24) {
					$time_format = "H:i";
				}

				echo " <br > ";
				if($order->get_meta( 'delivery_time', true ) == "as-soon-as-possible") {
					$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : "As Soon As Possible";
					$time_value = $as_soon_as_possible_text;
				} else {
					$times = $order->get_meta( 'delivery_time', true );
					$minutes = explode(' - ', $times);

		    		if(!isset($minutes[1])) {
		    			$time_value = date($time_format, strtotime($minutes[0]));
		    		} else {
		    			$time_value = date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1]));
		    		}

	    		}

				echo $delivery_time_field_label.": " . $time_value;

			}

			$delivery_tips_settings = get_option('coderockz_woo_delivery_delivery_tips_settings');
			$delivery_tips_field_label = (isset($delivery_tips_settings['delivery_tips_field_label']) && !empty($delivery_tips_settings['delivery_tips_field_label'])) ? stripslashes($delivery_tips_settings['delivery_tips_field_label']) : __( "Tips to Delivery Person", 'coderockz-woo-delivery' );
			$tips_fee = 0;
			$order = wc_get_order($order_id);
			foreach( $order->get_items('fee') as $item_id => $item_fee ){
			    if( $item_fee['name'] == $delivery_tips_field_label ) {
			    	$tips_fee = $item_fee['total'] + $item_fee['total_tax'];
			    	break;
			    }
			}

			if($tips_fee != 0) {

				if(class_exists('WOOCS_STARTER')){
					global $WOOCS;
                	$currencies=$WOOCS->get_currencies();
                	$currency_symbol = $currencies[$WOOCS->current_currency]['symbol'];
					$fee = $this->helper->postion_currency_symbol($currency_symbol,apply_filters('woocs_exchange_value', $tips_fee));
				} else {
					$currency_symbol = get_woocommerce_currency_symbol();
					$fee = $this->helper->postion_currency_symbol($currency_symbol,$tips_fee);
				}

				echo " <br > ";
				echo $delivery_tips_field_label.": " . $fee;
			}

		}

		if($column == 'order_delivery_status' && $order->meta_exists('delivery_type') && $order->get_meta( 'delivery_type', true )!= "")
		{

			if($order->meta_exists('delivery_status') && $order->get_meta( 'delivery_status', true )!= "" && $order->get_meta( 'delivery_status', true ) == "delivered")
			{

				if($order->meta_exists('delivery_type') && $order->get_meta( 'delivery_type', true )!= "" && $order->get_meta( 'delivery_type', true ) == "pickup") {
					$delivery_status = '<span class="coderockz_woo_delivery_delivered_text">'.$pickup_status_picked_text.'</span>';
				} else {
					$delivery_status = '<span class="coderockz_woo_delivery_delivered_text">'.$delivery_status_delivered_text.'</span>';
				}

				echo $delivery_status;
			} else {

				if($order->meta_exists('delivery_type') && $order->get_meta( 'delivery_type', true )!= "" && $order->get_meta( 'delivery_type', true ) == "pickup") {
					$delivery_status = '<span class="coderockz_woo_delivery_not_delivered_text">'.$pickup_status_not_picked_text.'</span>';
				} else {
					$delivery_status = '<span class="coderockz_woo_delivery_not_delivered_text">'.$delivery_status_not_delivered_text.'</span>';
				}

				echo $delivery_status;
			}

		}

	}

	public function coderockz_woo_delivery_show_custom_fields_data_subscription_list($column) {
		global $post;

		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');			
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');			
		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
		$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
		$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');
		$localization_settings = get_option('coderockz_woo_delivery_localization_settings');

		$delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : __("Delivery Date","coderockz-woo-delivery");
		$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : __("Pickup Date","coderockz-woo-delivery");
		$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : __("Delivery Time","coderockz-woo-delivery");
		$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : __("Pickup Time","coderockz-woo-delivery");
		$pickup_location_field_label = (isset($delivery_pickup_settings['field_label']) && !empty($delivery_pickup_settings['field_label'])) ? stripslashes($delivery_pickup_settings['field_label']) : __("Pickup Location","coderockz-woo-delivery");
		$additional_field_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? stripslashes($additional_field_settings['field_label']) : __("Special Note About Delivery","coderockz-woo-delivery");

		$localization_settings = get_option('coderockz_woo_delivery_localization_settings');
		$delivery_status_not_delivered_text = (isset($localization_settings['delivery_status_not_delivered_text']) && !empty($localization_settings['delivery_status_not_delivered_text'])) ? stripslashes($localization_settings['delivery_status_not_delivered_text']) :  __("Not Delivered","coderockz-woo-delivery");
		$delivery_status_delivered_text = (isset($localization_settings['delivery_status_delivered_text']) && !empty($localization_settings['delivery_status_delivered_text'])) ? stripslashes($localization_settings['delivery_status_delivered_text']) :  __("Delivery Completed","coderockz-woo-delivery");
		$pickup_status_not_picked_text = (isset($localization_settings['pickup_status_not_picked_text']) && !empty($localization_settings['pickup_status_not_picked_text'])) ? stripslashes($localization_settings['pickup_status_not_picked_text']) :  __("Not Picked","coderockz-woo-delivery");
		$pickup_status_picked_text = (isset($localization_settings['pickup_status_picked_text']) && !empty($localization_settings['pickup_status_picked_text'])) ? stripslashes($localization_settings['pickup_status_picked_text']) :  __("Pickup Completed","coderockz-woo-delivery");

		if($column == 'order_delivery_subscription_details')
		{
			
			if(metadata_exists('post', $post->ID, 'delivery_date') && get_post_meta($post->ID, 'delivery_date', true) !="")
			{
				$delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";
				$add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

				if($add_weekday_name) {
					$delivery_date_format = "l ".$delivery_date_format;
				}

				$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime(get_post_meta( $post->ID, 'delivery_date', true ))),"delivery"),"delivery");
		    	
		    	echo $delivery_date_field_label.": " . $delivery_date;	
			}

			if(metadata_exists('post', $post->ID, 'pickup_date') && get_post_meta($post->ID, 'pickup_date', true) !="")
			{
		    	$pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";

				$pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

				if($pickup_add_weekday_name) {
					$pickup_date_format = "l ".$pickup_date_format;
				}

		    	$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime(get_post_meta( $post->ID, 'pickup_date', true ))),"pickup"),"pickup");

		    	echo $pickup_date_field_label.": " . $pickup_date; 	
			}

			if(metadata_exists('post', $post->ID, 'delivery_time') && get_post_meta($post->ID, 'delivery_time', true) !="")
			{
				$time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
				if($time_format == 12) {
					$time_format = "h:i A";
				} elseif ($time_format == 24) {
					$time_format = "H:i";
				}

				echo " <br > ";
				if(get_post_meta($post->ID,"delivery_time",true) == "as-soon-as-possible") {
					$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : "As Soon As Possible";
					$time_value = $as_soon_as_possible_text;
				} else {
					$times = get_post_meta($post->ID,"delivery_time",true);
					$minutes = explode(' - ', $times);

		    		if(!isset($minutes[1])) {
		    			$time_value = date($time_format, strtotime($minutes[0]));
		    		} else {
		    			$time_value = date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1]));
		    		}

	    		}

				echo $delivery_time_field_label.": " . $time_value;

			}

			if(metadata_exists('post', $post->ID, 'pickup_time') && get_post_meta($post->ID, 'pickup_time', true) !="")
			{
				$pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
				if($pickup_time_format == 12) {
					$pickup_time_format = "h:i A";
				} elseif ($pickup_time_format == 24) {
					$pickup_time_format = "H:i";
				}

				echo " <br > ";
				$pickup_times = get_post_meta($post->ID,"pickup_time",true);
				$pickup_minutes = explode(' - ', $pickup_times);

	    		if(!isset($pickup_minutes[1])) {
	    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0]));
	    		} else {

	    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1]));
	    		}

				echo $pickup_time_field_label.": " . $pickup_time_value;

			}

			if(metadata_exists('post', $post->ID, 'pickup_location') && get_post_meta($post->ID, 'pickup_location', true) !="")
			{
				echo "<br >";
				echo $pickup_location_field_label.": " . get_post_meta($post->ID, 'pickup_location', true);
			}
		}

		if($column == 'order_delivery_subscription_status')
		{

			if(metadata_exists('post', $post->ID, 'delivery_status') && get_post_meta($post->ID, 'delivery_status', true) !="" && get_post_meta($post->ID, 'delivery_status', true) == "delivered")
			{

				if(metadata_exists('post', $post->ID, 'delivery_type') && get_post_meta($post->ID, 'delivery_type', true) !="" && get_post_meta($post->ID, 'delivery_type', true) =="pickup") {
					$delivery_status = '<span class="coderockz_woo_delivery_delivered_text">'.$pickup_status_picked_text.'</span>';
				} else {
					$delivery_status = '<span class="coderockz_woo_delivery_delivered_text">'.$delivery_status_delivered_text.'</span>';
				}

				echo $delivery_status;
			} else {

				if(metadata_exists('post', $post->ID, 'delivery_type') && get_post_meta($post->ID, 'delivery_type', true) !="" && get_post_meta($post->ID, 'delivery_type', true) =="pickup") {
					$delivery_status = '<span class="coderockz_woo_delivery_not_delivered_text">'.$pickup_status_not_picked_text.'</span>';
				} else {
					$delivery_status = '<span class="coderockz_woo_delivery_not_delivered_text">'.$delivery_status_not_delivered_text.'</span>';
				}

				echo $delivery_status;
			}

		}

	}

	public function coderockz_woo_delivery_information_after_shipping_address($order){
	    
		$order_items = $order->get_items();

		$exclude_condition = $this->helper->order_detect_exclude_condition($order_items);

	    $other_settings = get_option('coderockz_woo_delivery_other_settings');
		$exclude_settings = get_option('coderockz_woo_delivery_exclude_settings');

		$cart_total_hide_plugin = 0;
		$enable_including_tax_hide_module = (isset($exclude_settings['calculating_include_tax_hide_plugin']) && !empty($exclude_settings['calculating_include_tax_hide_plugin'])) ? $exclude_settings['calculating_include_tax_hide_plugin'] : false;
		foreach ( $order->get_items() as $item_id => $item ) {
			$cart_total_hide_plugin = $cart_total_hide_plugin+$item->get_subtotal();
			if($enable_including_tax_hide_module) {
				$cart_total_hide_plugin = $cart_total_hide_plugin+$item->get_subtotal_tax();
			}
		}

		$exclude_user_roles_condition = $this->helper->detect_exclude_user_roles_condition();

		$minimum_amount_hide_plugin = (isset($exclude_settings['minimum_amount_hide_plugin']) && $exclude_settings['minimum_amount_hide_plugin'] != "") ? (float)$exclude_settings['minimum_amount_hide_plugin'] : "";

		
		$enable_including_discount_hide_module = (isset($exclude_settings['calculating_include_discount_hide_plugin']) && !empty($exclude_settings['calculating_include_discount_hide_plugin'])) ? $exclude_settings['calculating_include_discount_hide_plugin'] : false;

		if($enable_including_discount_hide_module) {
			$cart_total_hide_plugin = $cart_total_hide_plugin + $order->get_total_discount() + (float)$order->get_discount_tax();
		}

		if( $minimum_amount_hide_plugin != "" && $cart_total_hide_plugin < $minimum_amount_hide_plugin){
	    	$hide_plugin = true;
	    } else {
	    	$hide_plugin = false;
	    }

		$exclude_shipping_methods = (isset($exclude_settings['exclude_shipping_methods']) && !empty($exclude_settings['exclude_shipping_methods'])) ? array_map('stripslashes', $exclude_settings['exclude_shipping_methods']) : array();

		$exclude_shipping_method_title = (isset($exclude_settings['exclude_shipping_method_title']) && !empty($exclude_settings['exclude_shipping_method_title'])) ? array_map('stripslashes', $exclude_settings['exclude_shipping_method_title']) : array();

		$exclude_shipping_methods = array_filter(array_merge($exclude_shipping_methods, $exclude_shipping_method_title), 'strlen');

		if(!$exclude_user_roles_condition && !$exclude_condition && !in_array($order->get_shipping_method(), $exclude_shipping_methods) && !$hide_plugin) {

	    $delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');			
	    $pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');			
		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
		$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
		$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');

		$delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : __("Delivery Date","coderockz-woo-delivery");
		$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : __("Pickup Date","coderockz-woo-delivery");
		$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : __("Delivery Time","coderockz-woo-delivery");
		$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : __("Pickup Time","coderockz-woo-delivery");
		$pickup_location_field_label = (isset($delivery_pickup_settings['field_label']) && !empty($delivery_pickup_settings['field_label'])) ? stripslashes($delivery_pickup_settings['field_label']) : __("Pickup Location","coderockz-woo-delivery");
		$additional_field_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? stripslashes($additional_field_settings['field_label']) : __("Special Note About Delivery","coderockz-woo-delivery");

		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
	    $order_type_field_label = (isset($delivery_option_settings['delivery_option_label']) && !empty($delivery_option_settings['delivery_option_label'])) ? stripslashes($delivery_option_settings['delivery_option_label']) : __("Order Type","coderockz-woo-delivery");
	    $delivery_field_label = (isset($delivery_option_settings['delivery_label']) && !empty($delivery_option_settings['delivery_label'])) ? stripslashes($delivery_option_settings['delivery_label']) : __("Delivery","coderockz-woo-delivery");
		$pickup_field_label = (isset($delivery_option_settings['pickup_label']) && !empty($delivery_option_settings['pickup_label'])) ? stripslashes($delivery_option_settings['pickup_label']) : __("Pickup","coderockz-woo-delivery");

		$localization_settings = get_option('coderockz_woo_delivery_localization_settings');
		$delivery_status_not_delivered_text = (isset($localization_settings['delivery_status_not_delivered_text']) && !empty($localization_settings['delivery_status_not_delivered_text'])) ? stripslashes($localization_settings['delivery_status_not_delivered_text']) : __("Not Delivered","coderockz-woo-delivery");
		$delivery_status_delivered_text = (isset($localization_settings['delivery_status_delivered_text']) && !empty($localization_settings['delivery_status_delivered_text'])) ? stripslashes($localization_settings['delivery_status_delivered_text']) : __("Delivery Completed","coderockz-woo-delivery");
		$pickup_status_not_picked_text = (isset($localization_settings['pickup_status_not_picked_text']) && !empty($localization_settings['pickup_status_not_picked_text'])) ? stripslashes($localization_settings['pickup_status_not_picked_text']) : __("Not Picked","coderockz-woo-delivery");
		$pickup_status_picked_text = (isset($localization_settings['pickup_status_picked_text']) && !empty($localization_settings['pickup_status_picked_text'])) ? stripslashes($localization_settings['pickup_status_picked_text']) : __("Pickup Completed","coderockz-woo-delivery");

		$other_settings = get_option('coderockz_woo_delivery_other_settings');
		$remove_delivery_status_column = (isset($other_settings['remove_delivery_status_column']) && !empty($other_settings['remove_delivery_status_column'])) ? $other_settings['remove_delivery_status_column'] : false;

		if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
	        $order_id = $order->get_id();
	    } else {
	        $order_id = $order->id;
	    }

	    if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {

	    	$delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";
			$add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

			if($add_weekday_name) {
				$delivery_date_format = "l ".$delivery_date_format;
			}

	    	if($this->hpos) {
				$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime($order->get_meta( 'delivery_date', true ))),"delivery"),"delivery");
			} else {
				$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime(get_post_meta( $order_id, 'delivery_date', true ))),"delivery"),"delivery");
			}

	    	echo '<p><strong>'.$delivery_date_field_label.':</strong> ' . $delivery_date . '</p>';
	    	
	    }

	    if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {

	    	$pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";

			$pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

			if($pickup_add_weekday_name) {
				$pickup_date_format = "l ".$pickup_date_format;
			}

	    	if($this->hpos) {
				$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime($order->get_meta( 'pickup_date', true ))),"pickup"),"pickup");
			} else {
				$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime(get_post_meta( $order_id, 'pickup_date', true ))),"pickup"),"pickup");
			}
	    	echo '<p><strong>'.$pickup_date_field_label.':</strong> ' . $pickup_date . '</p>'; 
	    	
	    }

	    if((metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id,"delivery_time",true) != "") || ($order->meta_exists('delivery_time') && $order->get_meta( 'delivery_time', true ) != "")) {

	    	$time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
			if($time_format == 12) {
				$time_format = "h:i A";
			} elseif ($time_format == 24) {
				$time_format = "H:i";
			}

	    	if(get_post_meta($order_id,"delivery_time",true) == "as-soon-as-possible" || $order->get_meta( 'delivery_time', true ) == "as-soon-as-possible") {
	    		$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : "As Soon As Possible";
	    		echo '<p><strong>'.$delivery_time_field_label.':</strong> ' . $as_soon_as_possible_text . '</p>';
	    	} else {
		    	if($this->hpos) {
					$minutes = $order->get_meta( 'delivery_time', true );
				} else {
					$minutes = get_post_meta($order_id,"delivery_time",true);
				}

		    	$minutes = explode(' - ', $minutes);

	    		if(!isset($minutes[1])) {
	    			echo '<p><strong>'.$delivery_time_field_label.':</strong> ' . date($time_format, strtotime($minutes[0])) . '</p>';
	    		} else {
	    			echo '<p><strong>'.$delivery_time_field_label.':</strong> ' . date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1])) . '</p>';    			
	    		}

    		}
	    	
	    }

	    if((metadata_exists('post', $order_id, 'pickup_time') && get_post_meta($order_id,"pickup_time",true) != "") || ($order->meta_exists('pickup_time') && $order->get_meta( 'pickup_time', true ) != "")) {

	    	$pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
			if($pickup_time_format == 12) {
				$pickup_time_format = "h:i A";
			} elseif ($pickup_time_format == 24) {
				$pickup_time_format = "H:i";
			}
			
	    	if($this->hpos) {
				$pickup_minutes = $order->get_meta( 'pickup_time', true );
			} else {
				$pickup_minutes = get_post_meta($order_id,"pickup_time",true);
			}
	    	$pickup_minutes = explode(' - ', $pickup_minutes);

    		if(!isset($pickup_minutes[1])) {
    			echo '<p><strong>'.$pickup_time_field_label.':</strong> ' . date($pickup_time_format, strtotime($pickup_minutes[0])) . '</p>';
    		} else {

    			echo '<p><strong>'.$pickup_time_field_label.':</strong> ' . date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1])) . '</p>';			
    		}
	    	
	    }

	    if((metadata_exists('post', $order_id, 'pickup_location') && get_post_meta($order_id, 'pickup_location', true) !="") || ($order->meta_exists('pickup_location') && $order->get_meta( 'pickup_location', true ) != "")) {
	    	if($this->hpos) {
				$pickup_location = $order->get_meta( 'pickup_location', true );
			} else {
				$pickup_location = get_post_meta($order_id,"pickup_location",true);
			}
			echo '<p><strong>'.$pickup_location_field_label.':</strong> ' . stripslashes(html_entity_decode($pickup_location, ENT_QUOTES)) . '</p>';
		}

		if((metadata_exists('post', $order_id, 'additional_note') && get_post_meta($order_id, 'additional_note', true) !="") || ($order->meta_exists('additional_note') && $order->get_meta( 'additional_note', true )!= "")) {
			if($this->hpos) {
				$additional_note = $order->get_meta( 'additional_note', true );
			} else {
				$additional_note = get_post_meta($order_id, 'additional_note', true);
			}
			echo '<p><strong>'.$additional_field_field_label.':</strong> ' . stripslashes(html_entity_decode($additional_note, ENT_QUOTES)) . '</p>';
		}
		if(!$remove_delivery_status_column) {
			if( (metadata_exists('post', $order_id, 'delivery_type') && get_post_meta($order_id, 'delivery_type', true) !="") || ($order->meta_exists('delivery_type') && $order->get_meta( 'delivery_type', true )!= "")) {
				if((metadata_exists('post', $order_id, 'delivery_status') && get_post_meta($order_id, 'delivery_status', true) !="" && get_post_meta($order_id, 'delivery_status', true) =="delivered") || ($order->meta_exists('delivery_status') && $order->get_meta( 'delivery_status', true )!= "" && $order->get_meta( 'delivery_status', true ) =="delivered")) {
					if((metadata_exists('post', $order_id, 'delivery_type') && get_post_meta($order_id, 'delivery_type', true) !="" && get_post_meta($order_id, 'delivery_type', true) =="pickup") || ($order->meta_exists('delivery_type') && $order->get_meta( 'delivery_type', true )!= "" && $order->get_meta( 'delivery_type', true ) =="pickup")) {
						echo '<span class="coderockz_woo_delivery_delivered_text">'.$pickup_status_picked_text.'</span>';
					} else {
						echo '<span class="coderockz_woo_delivery_delivered_text">'.$delivery_status_delivered_text.'</span>';
					}
					
				} else {

					if((metadata_exists('post', $order_id, 'delivery_type') && get_post_meta($order_id, 'delivery_type', true) !="" && get_post_meta($order_id, 'delivery_type', true) =="pickup") || ($order->meta_exists('delivery_type') && $order->get_meta( 'delivery_type', true )!= "" && $order->get_meta( 'delivery_type', true ) =="pickup")) {
						echo '<span class="coderockz_woo_delivery_not_delivered_text">'.$pickup_status_not_picked_text.'</span>';
					} else {
						echo '<span class="coderockz_woo_delivery_not_delivered_text">'.$delivery_status_not_delivered_text.'</span>';
					}
				}
			}

		}
			
		} else {
			echo '<span class="coderockz_woo_delivery_not_delivered_text">'.__('Order Has One Of The Excluded Conditions', 'coderockz-woo-delivery').'</span>';
		}	
	    
	}

	public function coderockz_woo_delivery_review_notice() {
		if(!$this->helper->detect_plugin_settings_page()) {
		    $options = get_option('coderockz_woo_delivery_review_notice');

		    $activation_time = get_option('coderockz-woo-delivery-pro-activation-time');

		    $notice = '<div class="coderockz-woo-delivery-review-notice notice notice-success is-dismissible">';
	        $notice .= '<img class="coderockz-woo-delivery-review-notice-left" src="'.CODEROCKZ_WOO_DELIVERY_URL.'admin/images/woo-delivery-logo.png" alt="coderockz-woo-delivery">';
	        $notice .= '<div class="coderockz-woo-delivery-review-notice-right">';
	        $notice .= '<p><b>'.__("We have worked relentlessly to develop the plugin and it would really appreciate us if you dropped a short review about the plugin. Your review means a lot to us and we are working to make the plugin more awesome. Thanks for using WooCommerce Delivery & Pickup Date Time.","coderockz-woo-delivery").'</b></p>';
	        $notice .= '<ul>';
	        $notice .= '<li><a val="later" href="#">'.__("Remind me later","coderockz-woo-delivery").'</a></li>';
	        $notice .= '<li><a class="coderockz-woo-delivery-review-request-btn" style="font-weight:bold;" val="given" href="#" target="_blank">'.__("Review Here","coderockz-woo-delivery").'</a></li>';
			$notice .= '<li><a val="never" href="#">'.__("I would not","coderockz-woo-delivery").'</a></li>';	        
	        $notice .= '</ul>';
	        $notice .= '</div>';
	        $notice .= '</div>';
	        
		    if(!$options && time()>= $activation_time + (60*60*24*15)){
		        echo $notice;
		    } else if(is_array($options)) {
		        if((!array_key_exists('review_notice',$options)) || ($options['review_notice'] =='later' && time()>=($options['updated_at'] + (60*60*24*30) ))){
		            echo $notice;
		        }
		    }
		}
	}

	public function coderockz_woo_delivery_save_review_notice() {
	    $notice = sanitize_text_field($_POST['notice']);
	    $value = array();
	    $value['review_notice'] = $notice;
	    $value['updated_at'] = time();

	    update_option('coderockz_woo_delivery_review_notice',$value);
	    wp_send_json_success($value);
	}

	public function coderockz_woo_delivery_get_deactivate_reasons() {

		$reasons = array(
			array(
				'id'          => 'could-not-understand',
				'text'        => __('I couldn\'t understand how to make it work','coderockz-woo-delivery'),
				'type'        => 'textarea',
				'placeholder' => __('Would you like us to assist you?','coderockz-woo-delivery')
			),
			array(
				'id'          => 'found-better-plugin',
				'text'        => __('I found a better plugin','coderockz-woo-delivery'),
				'type'        => 'text',
				'placeholder' => __('Which plugin?','coderockz-woo-delivery')
			),
			array(
				'id'          => 'not-have-that-feature',
				'text'        => __('I need specific feature that you don\'t support','coderockz-woo-delivery'),
				'type'        => 'textarea',
				'placeholder' => __('Could you tell us more about that feature?','coderockz-woo-delivery')
			),
			array(
				'id'          => 'is-not-working',
				'text'        => __('The plugin is not working','coderockz-woo-delivery'),
				'type'        => 'textarea',
				'placeholder' => __('Could you tell us a bit more whats not working?','coderockz-woo-delivery')
			),
			array(
				'id'          => 'temporary-deactivation',
				'text'        => __('It\'s a temporary deactivation','coderockz-woo-delivery'),
				'type'        => '',
				'placeholder' => ''
			),
			array(
				'id'          => 'other',
				'text'        => __('Other','coderockz-woo-delivery'),
				'type'        => 'textarea',
				'placeholder' => __('Could you tell us a bit more?','coderockz-woo-delivery')
			),
		);

		return $reasons;
	}

	public function coderockz_woo_delivery_deactivate_reason_submission(){
		check_ajax_referer('coderockz_woo_delivery_nonce');
		global $wpdb;

		if ( ! isset( $_POST['reason_id'] ) ) { // WPCS: CSRF ok, Input var ok.
			wp_send_json_error();
		}

		$current_user = new WP_User(get_current_user_id());

		$data = array(
			'reason_id'     => sanitize_text_field( $_POST['reason_id'] ), // WPCS: CSRF ok, Input var ok.
			'plugin'        => "Woo Delivery Pro",
			'url'           => home_url(),
			'user_email'    => $current_user->data->user_email,
			'user_name'     => $current_user->data->display_name,
			'reason_info'   => isset( $_REQUEST['reason_info'] ) ? trim( stripslashes( $_REQUEST['reason_info'] ) ) : '',
			'software'      => $_SERVER['SERVER_SOFTWARE'],
			'date'          => time(),
			'php_version'   => phpversion(),
			'mysql_version' => $wpdb->db_version(),
			'wp_version'    => get_bloginfo( 'version' )
		);


		$this->coderockz_woo_delivery_deactivate_send_request( $data);
		wp_send_json_success();

	}

	public function coderockz_woo_delivery_deactivate_send_request( $params) {
		$api_url = "https://coderockz.com/wp-json/coderockz-api/v1/deactivation-reason";
		return  wp_remote_post($api_url, array(
				'method'      => 'POST',
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => false,
				'headers'     => array( 'user-agent' => 'WooDelivery/' . md5( esc_url( home_url() ) ) . ';' ),
				'body'        => $params,
				'cookies'     => array()
			)
		);
	}

	public function coderockz_woo_delivery_deactivate_scripts() {

		global $pagenow;

		if ( 'plugins.php' != $pagenow ) {
			return;
		}

		$reasons = $this->coderockz_woo_delivery_get_deactivate_reasons();
		?>
		<!--pop up modal-->
		<div class="coderockz_woo_delivery_deactive_plugin-modal" id="coderockz_woo_delivery_deactive_plugin-modal">
			<div class="coderockz_woo_delivery_deactive_plugin-modal-wrap">
				<div class="coderockz_woo_delivery_deactive_plugin-modal-header">
					<h2 style="margin:0;"><span class="dashicons dashicons-testimonial"></span><?php _e( ' QUICK FEEDBACK' ); ?></h2>
				</div>

				<div class="coderockz_woo_delivery_deactive_plugin-modal-body">
					<p style="font-size:15px;font-weight:bold"><?php _e( 'If you have a moment, please share why you are deactivating Our plugin', 'coderockz-woo-delivery' ); ?></p>
					<ul class="reasons">
						<?php foreach ($reasons as $reason) { ?>
							<li data-type="<?php echo esc_attr( $reason['type'] ); ?>" data-placeholder="<?php echo esc_attr( $reason['placeholder'] ); ?>">
								<label><input type="radio" name="selected-reason" value="<?php echo $reason['id']; ?>"> <?php echo $reason['text']; ?></label>
							</li>
						<?php } ?>
					</ul>
				</div>

				<div class="coderockz_woo_delivery_deactive_plugin-modal-footer">
					<a href="#" class="coderockz-woo-delivery-skip-deactivate"><?php _e( 'Skip & Deactivate', 'coderockz-woo-delivery' ); ?></a>
					<div style="float:left">
					<button class="coderockz-woo-delivery-deactivate-button button-primary"><?php _e( 'Submit & Deactivate', 'coderockz-woo-delivery' ); ?></button>
					<button class="coderockz-woo-delivery-cancel-button button-secondary"><?php _e( 'Cancel', 'coderockz-woo-delivery' ); ?></button>
					</div>
				</div>
			</div>
		</div>

		<?php
	}

	public function coderockz_woo_delivery_review_submission(){
	    check_ajax_referer('coderockz_woo_delivery_nonce');
	    global $wpdb;

	    if ( ! isset( $_POST['review'] ) ) { // WPCS: CSRF ok, Input var ok.
	        wp_send_json_error();
	    }

	    $current_user = new WP_User(get_current_user_id());

	    $data = array(
	        'review'     	=> sanitize_textarea_field( $_POST['review'] ), // WPCS: CSRF ok, Input var ok.
	        'rating'        => sanitize_text_field( $_POST['rating'] ),
	        'plugin'        => 'Woo Delivery Pro',
	        'url'           => home_url(),
	        'user_email'    => $current_user->data->user_email,
	        'user_name'     => $current_user->data->display_name,
	        'user_gravator' => get_avatar_url(get_current_user_id()),
	        'date'          => time(),
	    );


	    $notice = sanitize_text_field($_POST['notice']);
	    $value = array();
	    $value['review_notice'] = $notice;
	    $value['updated_at'] = time();
	    update_option('coderockz_woo_delivery_review_notice',$value);


	    $this->coderockz_woo_delivery_review_send_request( $data );
	    wp_send_json_success();

	}

	public function coderockz_woo_delivery_review_send_request( $params ) {
	    $api_url = "https://coderockz.com/wp-json/coderockz-api/v1/customer-review";
	    return  wp_remote_post($api_url, array(
	            'method'      => 'POST',
	            'timeout'     => 45,
	            'redirection' => 5,
	            'httpversion' => '1.0',
	            'blocking'    => false,
	            'headers'     => array( 'user-agent' => 'WooDelivery/' . md5( esc_url( home_url() ) ) . ';' ),
	            'body'        => $params,
	            'cookies'     => array()
	        )
	    );

	}

	public function coderockz_woo_delivery_review_scripts() {

	    if ( !current_user_can( 'manage_options' ) ) {
	        return;
	    }

	    $my_current_screen = get_current_screen();
		if(isset( $my_current_screen->base ) && 'dashboard' === $my_current_screen->base) {
	    
	    ?>
	    <!--pop up modal-->
	    <div class="coderockz_woo_delivery_customer_review_plugin-modal" id="coderockz_woo_delivery_customer_review_plugin-modal">
	        <div class="coderockz_woo_delivery_customer_review_plugin-modal-wrap">
	        	<div class="coderockz_woo_delivery_customer_review_plugin-modal-header">
					<h2 style="margin:0;"><span class="dashicons dashicons-format-chat"></span><?php _e( ' QUICK REVIEW' ); ?></h2>
				</div>
	            <div class="coderockz_woo_delivery_customer_review_plugin-modal-body">
	                <p style="font-size:15px;font-weight:bold"><?php _e( 'Your review really appreciate us. Thank\'s for reviewing WooCommerce Delivery & Pickup Date Time.', 'coderockz-woo-delivery' ); ?></p>
	                <div class="coderockz-woo-delivery-customer-rating-review">
	                    <div class="form-group" id="coderockz-woo-delivery-customer-rating">
	                    </div>
	                    <textarea id="coderockz-woo-delivery-customer-review" placeholder=""></textarea>
	                </div>
	                <p class="coderockz-woo-delivery-customer-review-required">Please write something.</p>
	            </div>

	            <div class="coderockz_woo_delivery_customer_review_plugin-modal-footer">
	            	<div style="float:right;">
	                <button class="coderockz-woo-delivery-submit-review-button button-primary"><?php _e( 'Submit Review', 'coderockz-woo-delivery' ); ?></button>
	                <button class="coderockz-woo-delivery-cancel-button button-secondary"><?php _e( 'Cancel', 'coderockz-woo-delivery' ); ?></button>
	            	</div>
	            </div>
	        </div>
	    </div>
	    <?php
		}
	}


	public function coderockz_woo_delivery_custom_meta_box() {
		
		$order_metabox_heading = (isset(get_option('coderockz_woo_delivery_localization_settings')['order_metabox_heading']) && !empty(get_option('coderockz_woo_delivery_localization_settings')['order_metabox_heading'])) ? __(stripslashes(get_option('coderockz_woo_delivery_localization_settings')['order_metabox_heading']),'coderockz-woo-delivery') : __("Delivery/Pickup Date & Time",'coderockz-woo-delivery'); 
		
		$screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';
		add_meta_box( 'coderockz_woo_delivery_meta_box', $order_metabox_heading, array($this,"coderockz_woo_delivery_meta_box_markup"), $screen, 'normal', 'high', null );

	}

	public function coderockz_woo_delivery_meta_box_markup($post_or_order_object) {

		global $pagenow;

		if(((get_post_type() == 'shop_order' || (function_exists( 'wc_get_page_screen_id' ) && wc_get_page_screen_id( 'shop-order' ) === 'woocommerce_page_wc-orders')) && ($pagenow === 'post-new.php' || (isset($_GET['action'])  && $_GET['action'] === 'new')))) {
			$localization_settings = get_option('coderockz_woo_delivery_localization_settings');
			$metabox_order_creation_text = (isset($localization_settings['metabox_order_creation']) && !empty($localization_settings['metabox_order_creation'])) ? stripslashes($localization_settings['metabox_order_creation']) : __("Please create the order first","coderockz-woo-delivery");
			$meta_box = '<p style="font-size: 20px;font-weight: 700;text-align: center;font-style: italic;margin-bottom: 12px !important;">'.$metabox_order_creation_text.' :) </p>';

			echo $meta_box;
		} else {

		$order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;

		if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
		    $order_id = $order->get_id();
		} else {
		    $order_id = $order->id;
		}
		
		$order_items = $order->get_items();

		$exclude_condition = $this->helper->order_detect_exclude_condition($order_items);

	    $other_settings = get_option('coderockz_woo_delivery_other_settings');
		$exclude_settings = get_option('coderockz_woo_delivery_exclude_settings');

		$cart_total_hide_plugin = 0;
		$enable_including_tax_hide_module = (isset($exclude_settings['calculating_include_tax_hide_plugin']) && !empty($exclude_settings['calculating_include_tax_hide_plugin'])) ? $exclude_settings['calculating_include_tax_hide_plugin'] : false;
		foreach ( $order->get_items() as $item_id => $item ) {
			$cart_total_hide_plugin = $cart_total_hide_plugin+$item->get_subtotal();
			if($enable_including_tax_hide_module) {
				$cart_total_hide_plugin = $cart_total_hide_plugin+$item->get_subtotal_tax();
			}
		}

		$exclude_user_roles_condition = $this->helper->detect_exclude_user_roles_condition();

		$minimum_amount_hide_plugin = (isset($exclude_settings['minimum_amount_hide_plugin']) && $exclude_settings['minimum_amount_hide_plugin'] != "") ? (float)$exclude_settings['minimum_amount_hide_plugin'] : "";

		
		$enable_including_discount_hide_module = (isset($exclude_settings['calculating_include_discount_hide_plugin']) && !empty($exclude_settings['calculating_include_discount_hide_plugin'])) ? $exclude_settings['calculating_include_discount_hide_plugin'] : false;

		if($enable_including_discount_hide_module) {
			$cart_total_hide_plugin = $cart_total_hide_plugin + $order->get_total_discount() + (float)$order->get_discount_tax();
		}

		if( $minimum_amount_hide_plugin != "" && $cart_total_hide_plugin < $minimum_amount_hide_plugin){
	    	$hide_plugin = true;
	    } else {
	    	$hide_plugin = false;
	    }

		$exclude_shipping_methods = (isset($exclude_settings['exclude_shipping_methods']) && !empty($exclude_settings['exclude_shipping_methods'])) ? array_map('stripslashes', $exclude_settings['exclude_shipping_methods']) : array();

		$exclude_shipping_method_title = (isset($exclude_settings['exclude_shipping_method_title']) && !empty($exclude_settings['exclude_shipping_method_title'])) ? array_map('stripslashes', $exclude_settings['exclude_shipping_method_title']) : array();

		$exclude_shipping_methods = array_filter(array_merge($exclude_shipping_methods, $exclude_shipping_method_title), 'strlen');

		if($exclude_user_roles_condition || $exclude_condition || in_array($order->get_shipping_method(), $exclude_shipping_methods) || $hide_plugin) {

			$meta_box = '<p style= "text-align:center;" class="coderockz_woo_delivery_not_delivered_text">'. __('Order Has One Of The Excluded Conditions', 'coderockz-woo-delivery').'</p>';
			echo $meta_box;

			return;
		}

		$today = wp_date('Y-m-d',current_time( 'timestamp', 1 ));
		 $tomorrow = wp_date("Y-m-d", current_time( 'timestamp', 1 )+86400);

		$order_total = $this->helper->order_cart_total($order_id);
		$laundry_store_settings = get_option('coderockz_woo_delivery_laundry_store_settings');
		$delivery_tips_settings = get_option('coderockz_woo_delivery_delivery_tips_settings');
		$delivery_tips_field_label = isset($delivery_tips_settings['delivery_tips_field_label']) && $delivery_tips_settings['delivery_tips_field_label'] != "" ? $delivery_tips_settings['delivery_tips_field_label'] : __( "Tips to Delivery Person", 'coderockz-woo-delivery' );
		$delivery_tips = "";
		foreach( $order->get_items('fee') as $item_id => $item_fee ){
			if($item_fee['name'] == $delivery_tips_field_label) {
				$delivery_tips = $order->get_items('fee')[$item_id]->get_total();
			}
		}

		if(metadata_exists('post', $order_id, 'delivery_date') || $order->meta_exists('delivery_date')) {
			if($this->hpos) {
				$delivery_date_free_shipping_restriction = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date("Y-m-d", strtotime($order->get_meta( 'delivery_date', true ))),"delivery"),"delivery");
			} else {
				$delivery_date_free_shipping_restriction = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date("Y-m-d", strtotime(get_post_meta( $order_id, 'delivery_date', true ))),"delivery"),"delivery");
			}
	    	
	    } else {
	    	$delivery_date_free_shipping_restriction="";
	    }

		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings'); 

		$enable_dynamic_order_type = (isset($delivery_option_settings['enable_dynamic_order_type']) && !empty($delivery_option_settings['enable_dynamic_order_type'])) ? $delivery_option_settings['enable_dynamic_order_type'] : false;

		$enable_free_shipping_restriction = (isset($delivery_option_settings['enable_free_shipping_restriction']) && !empty($delivery_option_settings['enable_free_shipping_restriction'])) ? $delivery_option_settings['enable_free_shipping_restriction'] : false;
		$minimum_amount = (isset($delivery_option_settings['minimum_amount_shipping_restriction']) && $delivery_option_settings['minimum_amount_shipping_restriction'] != "") ? (float)$delivery_option_settings['minimum_amount_shipping_restriction'] : "";


		foreach( $order->get_items( 'shipping' ) as $item_id => $item ) {

		    $shipping_zone = WC_Shipping_Zones::get_zone_by( 'instance_id', $item->get_instance_id() );

		    $shipping_methods = $shipping_zone->get_shipping_methods();

		    $has_local_pickup_method = [];
		    $has_other_method = [];
		    $has_conditional_delivery_shipping_method = false;
		    $fees_settings = get_option('coderockz_woo_delivery_fee_settings');
		    $enable_conditional_delivery_fee = (isset($fees_settings['enable_conditional_delivery_fee']) && !empty($fees_settings['enable_conditional_delivery_fee'])) ? $fees_settings['enable_conditional_delivery_fee'] : false;

		    foreach ( $shipping_methods as $instance_id => $shipping_method ) {
		    	
		    	if($shipping_method->is_enabled()) {
			    	if($shipping_method->id == 'local_pickup') {
	    				$has_local_pickup_method[] = $shipping_method->get_title();	        	
		        	} 

	        		if($shipping_method->id != 'local_pickup') {
		        		if($shipping_method->id == 'free_shipping') {
		        			if(($order->get_total() >= $shipping_method->min_amount/* && $shipping_method->min_amount != 0*/ ) || ($enable_free_shipping_restriction && $minimum_amount != "" && $order_total['delivery_free_shipping'] >= $minimum_amount)/* || ($enable_free_shipping_current_day && $delivery_date_free_shipping_restriction == $today) || ($disable_free_shipping_current_day && $delivery_date_free_shipping_restriction != $today)*/ ) {
		        				$has_other_method[] = $shipping_method->get_title();
		        			}

		        		} else {		        			 

		        			if(!$enable_conditional_delivery_fee || !isset($fees_settings['conditional_delivery_shipping_method']) || empty($fees_settings['conditional_delivery_shipping_method']) || $shipping_method->get_title() != $fees_settings['conditional_delivery_shipping_method']) { 
		        				$has_other_method[] = $shipping_method->get_title();
		        			}

		        			if($enable_conditional_delivery_fee && isset($fees_settings['conditional_delivery_shipping_method']) && $fees_settings['conditional_delivery_shipping_method'] != "" && $shipping_method->get_title() == $fees_settings['conditional_delivery_shipping_method']) {
		        				$has_conditional_delivery_shipping_method = true;
		        			}
		        			
		        		}

	        		}
		        	
	        	}

		    }
		}

		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
		$other_settings = get_option('coderockz_woo_delivery_other_settings');
		$remove_delivery_status_column = (isset($other_settings['remove_delivery_status_column']) && !empty($other_settings['remove_delivery_status_column'])) ? $other_settings['remove_delivery_status_column'] : false;
		$enable_delivery_option = (isset($delivery_option_settings['enable_option_time_pickup']) && !empty($delivery_option_settings['enable_option_time_pickup'])) ? $delivery_option_settings['enable_option_time_pickup'] : false;
		
		$processing_days_settings = get_option('coderockz_woo_delivery_processing_days_settings');
		$processing_time_settings = get_option('coderockz_woo_delivery_processing_time_settings');
		$offdays_settings = get_option('coderockz_woo_delivery_off_days_settings');
		$opendays_settings = get_option('coderockz_woo_delivery_open_days_settings');
		$enable_delivery_date = (isset($delivery_date_settings['enable_delivery_date']) && !empty($delivery_date_settings['enable_delivery_date'])) ? $delivery_date_settings['enable_delivery_date'] : false;
		$enable_pickup_date = (isset($pickup_date_settings['enable_pickup_date']) && !empty($pickup_date_settings['enable_pickup_date'])) ? $pickup_date_settings['enable_pickup_date'] : false;
				
		$consider_off_days = (isset($processing_days_settings['processing_days_consider_off_days']) && !empty($processing_days_settings['processing_days_consider_off_days'])) ? $processing_days_settings['processing_days_consider_off_days'] : false;
		$consider_weekends = (isset($processing_days_settings['processing_days_consider_weekends']) && !empty($processing_days_settings['processing_days_consider_weekends'])) ? $processing_days_settings['processing_days_consider_weekends'] : false;
		$consider_current_day = (isset($processing_days_settings['processing_days_consider_current_day']) && !empty($processing_days_settings['processing_days_consider_current_day'])) ? $processing_days_settings['processing_days_consider_current_day'] : false;
				
		$enable_category_processing_days = (isset($processing_days_settings['enable_category_wise_processing_days']) && !empty($processing_days_settings['enable_category_wise_processing_days'])) ? $processing_days_settings['enable_category_wise_processing_days'] : false;
		$category_processing_days = (isset($processing_days_settings['category_processing_days']) && !empty($processing_days_settings['category_processing_days'])) ? $processing_days_settings['category_processing_days'] : array();

		$category_after_pickup_dates = (isset($laundry_store_settings['category_after_pickup_dates']) && !empty($laundry_store_settings['category_after_pickup_dates'])) ? $laundry_store_settings['category_after_pickup_dates'] : array();

		$enable_category_processing_time = (isset($processing_time_settings['enable_category_wise_processing_time']) && !empty($processing_time_settings['enable_category_wise_processing_time'])) ? $processing_time_settings['enable_category_wise_processing_time'] : false;
		$category_processing_time = (isset($processing_time_settings['category_processing_time']) && !empty($processing_time_settings['category_processing_time'])) ? $processing_time_settings['category_processing_time'] : array();

		$category_wise_offdays = (isset($offdays_settings['category_wise_offdays']) && !empty($offdays_settings['category_wise_offdays'])) ? $offdays_settings['category_wise_offdays'] : array();

		$opendays_categories = (isset($opendays_settings['category_open_days']) && !empty($opendays_settings['category_open_days'])) ? $opendays_settings['category_open_days'] : array();	
		$opendays_pickup_categories = (isset($opendays_settings['category_open_days_pickup']) && !empty($opendays_settings['category_open_days_pickup'])) ? $opendays_settings['category_open_days_pickup'] : array();

		$disable_opendays_regular_product = (isset($opendays_settings['disable_opendays_regular_product']) && !empty($opendays_settings['disable_opendays_regular_product'])) ? $opendays_settings['disable_opendays_regular_product'] : false;

		$disable_opendays_pickup_regular_product = (isset($opendays_settings['disable_opendays_pickup_regular_product']) && !empty($opendays_settings['disable_opendays_pickup_regular_product'])) ? $opendays_settings['disable_opendays_pickup_regular_product'] : false;	

		$cutoff_categories = (isset($delivery_time_settings['category_wise_cutoff']) && !empty($delivery_time_settings['category_wise_cutoff'])) ? $delivery_time_settings['category_wise_cutoff'] : array();
		$disable_category_wise_cutoff_regular_category = (isset($delivery_time_settings['disable_category_wise_cutoff_regular_category']) && !empty($delivery_time_settings['disable_category_wise_cutoff_regular_category'])) ? $delivery_time_settings['disable_category_wise_cutoff_regular_category'] : false;
		
		$consider_multiple_cutoff_category_condition = (isset($delivery_time_settings['consider_multiple_cutoff_category_condition']) && !empty($delivery_time_settings['consider_multiple_cutoff_category_condition'])) ? $delivery_time_settings['consider_multiple_cutoff_category_condition'] : 'first';

		$next_month_off_categories = (isset($offdays_settings['next_month_off_categories']) && !empty($offdays_settings['next_month_off_categories'])) ? $offdays_settings['next_month_off_categories'] : array();
		$next_week_off_categories = (isset($offdays_settings['next_week_off_categories']) && !empty($offdays_settings['next_week_off_categories'])) ? $offdays_settings['next_week_off_categories'] : array();
		$current_week_off_categories = (isset($offdays_settings['current_week_off_categories']) && !empty($offdays_settings['current_week_off_categories'])) ? $offdays_settings['current_week_off_categories'] : array();

		$exclude_category_processing_time = (isset($processing_time_settings['exclude_categories']) && !empty($processing_time_settings['exclude_categories'])) ? $processing_time_settings['exclude_categories'] : array();
		$exclude_product_processing_time = (isset($processing_time_settings['exclude_products']) && !empty($processing_time_settings['exclude_products'])) ? $processing_time_settings['exclude_products'] : array();

		$exclude_category_processing_days = (isset($processing_days_settings['exclude_categories']) && !empty($processing_days_settings['exclude_categories'])) ? $processing_days_settings['exclude_categories'] : array();
		$exclude_product_processing_days = (isset($processing_days_settings['exclude_products']) && !empty($processing_days_settings['exclude_products'])) ? $processing_days_settings['exclude_products'] : array();

		if(($enable_category_processing_days && !empty($category_processing_days)) || ($enable_category_processing_time && !empty($category_processing_time)) || (!empty($category_wise_offdays)) || (!empty($cutoff_categories)) || (!empty($next_month_off_categories)) || (!empty($next_week_off_categories)) || (!empty($current_week_off_categories)) || (!empty($exclude_product_processing_time) || !empty($exclude_category_processing_time)) || (!empty($exclude_product_processing_days) || !empty($exclude_category_processing_days)) || !empty($category_after_pickup_dates)) {
			$order_product_categories = $this->helper->order_product_categories($order_items);
		}

		$enable_product_processing_days = (isset($processing_days_settings['enable_product_wise_processing_days']) && !empty($processing_days_settings['enable_product_wise_processing_days'])) ? $processing_days_settings['enable_product_wise_processing_days'] : false;
		$product_processing_days = (isset($processing_days_settings['product_processing_days']) && !empty($processing_days_settings['product_processing_days'])) ? $processing_days_settings['product_processing_days'] : array();

		$enable_product_processing_time = (isset($processing_time_settings['enable_product_wise_processing_time']) && !empty($processing_time_settings['enable_product_wise_processing_time'])) ? $processing_time_settings['enable_product_wise_processing_time'] : false;

		$product_processing_time = (isset($processing_time_settings['product_processing_time']) && !empty($processing_time_settings['product_processing_time'])) ? $processing_time_settings['product_processing_time'] : array();

		$product_wise_offdays = (isset($offdays_settings['product_wise_offdays']) && !empty($offdays_settings['product_wise_offdays'])) ? $offdays_settings['product_wise_offdays'] : array();

		if(($enable_product_processing_days && !empty($product_processing_days)) || ($enable_product_processing_time && !empty($product_processing_time)) || (!empty($product_wise_offdays)) || (!empty($exclude_product_processing_time) || !empty($exclude_category_processing_time)) || (!empty($exclude_product_processing_days) || !empty($exclude_category_processing_days)) || (!empty($opendays_categories)) || (!empty($cutoff_categories)) || (!empty($opendays_pickup_categories))) {
			$product_id = $this->helper->order_product_id($order_items);
		}

		$disable_dates = [];
		$pickup_disable_dates = [];

		$max_processing_days_array = [];
		$max_processing_days_array_pickup = [];

		$overall_processing_days = (isset($processing_days_settings['overall_processing_days']) && !empty($processing_days_settings['overall_processing_days'])) ? $processing_days_settings['overall_processing_days'] : "0";
		$overall_processing_days_pickup = (isset($processing_days_settings['overall_processing_days_pickup']) && $processing_days_settings['overall_processing_days_pickup'] != "") ? $processing_days_settings['overall_processing_days_pickup'] : "0";

		$enable_weekday_wise_processing_days = (isset($processing_days_settings['enable_weekday_wise_processing_days']) && !empty($processing_days_settings['enable_weekday_wise_processing_days'])) ? $processing_days_settings['enable_weekday_wise_processing_days'] : false;

		$weekday_wise_processing_days = (isset($processing_days_settings['weekday_wise_processing_days']) && !empty($processing_days_settings['weekday_wise_processing_days'])) ? $processing_days_settings['weekday_wise_processing_days'] : array();

		$current_week_day = (int)wp_date("w");

		if($enable_weekday_wise_processing_days && !empty($weekday_wise_processing_days)) {

			foreach ($weekday_wise_processing_days as $key => $value)
			{
				if($key === $current_week_day)
				{
					array_push($max_processing_days_array,(int)$value);
					array_push($max_processing_days_array_pickup,(int)$value);
				}
			}
		}

		if($enable_category_processing_days && !empty($category_processing_days)) {						
			foreach ($category_processing_days as $key => $value)
			{
				if(in_array(stripslashes(strtolower($key)), $order_product_categories))
				{
					array_push($max_processing_days_array,(int)$value);
					array_push($max_processing_days_array_pickup,(int)$value);
				}
			}
		}

		$enable_product_processing_day_quantity = (isset($processing_days_settings['enable_product_processing_day_quantity']) && !empty($processing_days_settings['enable_product_processing_day_quantity'])) ? $processing_days_settings['enable_product_processing_day_quantity'] : false;
		
		if($enable_product_processing_days && !empty($product_processing_days)) {
					
			foreach ($product_processing_days as $key => $value)
			{
				if(in_array($key, $product_id))
				{
					if($enable_product_processing_day_quantity) {
						foreach ( $order_items as $item ) {

						    if( $item->get_variation_id() ) {
						        if($item->get_variation_id() == $key ){
							        $qty =  $item->get_quantity();
							        break;
							    }			        
						    } else {
								if($item->get_product_id() == $key ){
							        $qty =  $item->get_quantity();
							        break;
							    }
							}
						}
						array_push($max_processing_days_array,(int)$value * $qty * count(array_keys($product_id, $key)));
						array_push($max_processing_days_array_pickup,(int)$value * $qty * count(array_keys($product_id, $key)));
					} else {
						array_push($max_processing_days_array,(int)$value);
						array_push($max_processing_days_array_pickup,(int)$value);
					}
				}
			}
		}

		$backorder_item = false;

	    foreach( $order_items as $item ) {
	    	$product = $item->get_product();
	        if(gettype($product) != "boolean") {
	        	if( $product->is_on_backorder() ) {
		            $backorder_item = true;
		            break;
		        }
	        }
	        
	    }

	    if($backorder_item) {
	    	$backorder_processing_days = (isset($processing_days_settings['backorder_processing_days']) && $processing_days_settings['backorder_processing_days'] != "") ? $processing_days_settings['backorder_processing_days'] : "0";
	    }

    	if(empty($exclude_category_processing_days) && empty($exclude_product_processing_days)) {
    		if($overall_processing_days !== "0") {
    			array_push($max_processing_days_array,(int)$overall_processing_days);
    		}

    		if($overall_processing_days_pickup !== "0") {
    			array_push($max_processing_days_array_pickup,(int)$overall_processing_days_pickup);
    		}

    		if(isset($backorder_processing_days) && $backorder_processing_days !== "0") {
				array_push($max_processing_days_array,(int)$backorder_processing_days);
				array_push($max_processing_days_array_pickup,(int)$backorder_processing_days);
			}

		} elseif(!empty($exclude_category_processing_days) || !empty($exclude_product_processing_days)) {
			$exclude_category_processing_days_array = [];
			foreach ($exclude_category_processing_days as $key => $value) {
				$exclude_category_processing_days_array [] = stripslashes(strtolower($value));	
			}

			$exclude_category_processing_days_condition = (count(array_intersect($order_product_categories, $exclude_category_processing_days_array)) <= count($order_product_categories)) && count(array_intersect($order_product_categories, $exclude_category_processing_days_array))>0;

			$exclude_product_processing_days_condition = (count(array_intersect($product_id, $exclude_product_processing_days)) <= count($product_id)) && count(array_intersect($product_id, $exclude_product_processing_days))>0;

			if($exclude_category_processing_days_condition && !$exclude_product_processing_days_condition) {
				$exclude_condition_processing_days = true;

	  			$pre_product = 0;
				foreach($product_id as $product) {
					$parent_id  = wp_get_post_parent_id( $product );
					$product = $parent_id > 0 ? $parent_id : $product;
					$term_lists = wp_get_post_terms($product,'product_cat',array('fields'=>'ids'));

					foreach($term_lists as $cat_id) {
						$term = get_term_by( 'id', $cat_id, 'product_cat' );
						if(in_array(stripslashes(strtolower(htmlspecialchars_decode($term->name))), $exclude_category_processing_days_array)) {
							$exclude_condition_processing_days = true;
							$pre_product = $product;
							break;
						} elseif(!in_array(stripslashes(strtolower(htmlspecialchars_decode($term->name))), $exclude_category_processing_days_array)) {
							if($pre_product != $product) {
								$exclude_condition_processing_days = false;
								if(count($product_id)>1)
									break 2;
							}

						}

					}
					
				}
	  			
			} elseif($exclude_product_processing_days_condition && !$exclude_category_processing_days_condition) {
				$exclude_condition_processing_days = true;
				if(count($product_id) > 1 && count(array_diff($product_id, $exclude_product_processing_days))>0) {
	  				$exclude_condition_processing_days = !$exclude_condition_processing_days;
	  			}
			} elseif($exclude_product_processing_days_condition && $exclude_category_processing_days_condition) {
				$exclude_condition_processing_days = true;					
				$pre_product = 0;
				foreach($product_id as $product) {
					
					if(!in_array($product, $exclude_product_processing_days)) {
						$parent_id  = wp_get_post_parent_id( $product );
						$product = $parent_id > 0 ? $parent_id : $product;
						$term_lists = wp_get_post_terms($product,'product_cat',array('fields'=>'ids'));
						
						foreach($term_lists as $cat_id) {
							$term = get_term_by( 'id', $cat_id, 'product_cat' );
							if(in_array(stripslashes(strtolower(htmlspecialchars_decode($term->name))), $exclude_category_processing_days_array)) {
								$exclude_condition_processing_days = true;
								$pre_product = $product;
								break;
							} elseif(!in_array(stripslashes(strtolower(htmlspecialchars_decode($term->name))), $exclude_category_processing_days_array)) {
								if($pre_product != $product) {
									$exclude_condition_processing_days = false;
									if(count($product_id)>1)
										break 2;
								}
								
							}
							
						}
					}
				}
			} else {
				$exclude_condition_processing_days = false;
			}

			if(!$exclude_condition_processing_days) {
				array_push($max_processing_days_array,(int)$overall_processing_days);
				array_push($max_processing_days_array_pickup,(int)$overall_processing_days_pickup);
				if(isset($backorder_processing_days) && $backorder_processing_days !== "0") {
					array_push($max_processing_days_array,(int)$backorder_processing_days);
					array_push($max_processing_days_array_pickup,(int)$backorder_processing_days);
				}
			}
		}

		$max_processing_days = count($max_processing_days_array) > 0 ? max($max_processing_days_array) : 0;
		$max_processing_days_pickup = count($max_processing_days_array_pickup) > 0 ? max($max_processing_days_array_pickup) : 0;
		
		$temp_max_processing_days = $max_processing_days;
		$temp_max_processing_days_pickup = $max_processing_days_pickup;

		$max_processing_time_array = [];
		$max_processing_time_array_pickup = [];

		$overall_processing_time = isset($processing_time_settings['overall_processing_time']) && $processing_time_settings['overall_processing_time'] != "" ? $processing_time_settings['overall_processing_time'] : 0;

		$overall_processing_time_pickup = isset($processing_time_settings['overall_processing_time_pickup']) && $processing_time_settings['overall_processing_time_pickup'] != "" ? $processing_time_settings['overall_processing_time_pickup'] : 0;

		$enable_weekday_wise_processing_time = (isset($processing_time_settings['enable_weekday_wise_processing_time']) && !empty($processing_time_settings['enable_weekday_wise_processing_time'])) ? $processing_time_settings['enable_weekday_wise_processing_time'] : false;

		$weekday_wise_processing_time = (isset($processing_time_settings['weekday_wise_processing_time']) && !empty($processing_time_settings['weekday_wise_processing_time'])) ? $processing_time_settings['weekday_wise_processing_time'] : array();

		$current_week_day = (int)wp_date("w");

		if($enable_weekday_wise_processing_time && !empty($weekday_wise_processing_time)) {

			foreach ($weekday_wise_processing_time as $key => $value)
			{
				if($key === $current_week_day)
				{
					array_push($max_processing_time_array,(int)$value);
					array_push($max_processing_time_array_pickup,(int)$value);
				}
			}
		}
		
		if($enable_category_processing_time && !empty($category_processing_time)) {

			foreach ($category_processing_time as $key => $value)
			{
				if(in_array(stripslashes(strtolower($key)), $order_product_categories))
				{
					array_push($max_processing_time_array,(int)$value);
					array_push($max_processing_time_array_pickup,(int)$value);
				}
			}
		}

		$enable_product_processing_time_quantity = (isset($processing_time_settings['enable_product_processing_time_quantity']) && !empty($processing_time_settings['enable_product_processing_time_quantity'])) ? $processing_time_settings['enable_product_processing_time_quantity'] : false;
		
		if($enable_product_processing_time && !empty($product_processing_time)) {

			foreach ($product_processing_time as $key => $value)
			{
				if(in_array($key, $product_id))
				{					
					if($enable_product_processing_time_quantity) {
						foreach ( $order_items as $item ) {
							if( $item->get_variation_id() ) {
						        if($item->get_variation_id() == $key ){
							        $qty =  $item->get_quantity();
							        break;
							    }			        
						    } else {
								if($item->get_product_id() == $key ){
							        $qty =  $item->get_quantity();
							        break;
							    }
							}
						    
						}
						array_push($max_processing_time_array,(int)$value * $qty * count(array_keys($product_id, $key)));
						array_push($max_processing_time_array_pickup,(int)$value * $qty * count(array_keys($product_id, $key)));
					} else {
						array_push($max_processing_time_array,(int)$value);
						array_push($max_processing_time_array_pickup,(int)$value);
					}
				}
			}
		} 

		if (empty($exclude_category_processing_time) && empty($exclude_product_processing_time)) {
			if($overall_processing_time !== 0) {
				array_push($max_processing_time_array,(int)$processing_time_settings['overall_processing_time']);
			}

			if($overall_processing_time_pickup !== 0) {
				array_push($max_processing_time_array_pickup,(int)$processing_time_settings['overall_processing_time_pickup']);
			}

		} elseif(!empty($exclude_category_processing_time) || !empty($exclude_product_processing_time)) {
			$exclude_category_processing_time_array = [];
			foreach ($exclude_category_processing_time as $key => $value) {
				$exclude_category_processing_time_array [] = stripslashes(strtolower($value));	
			}

			$exclude_category_processing_time_condition = (count(array_intersect($order_product_categories, $exclude_category_processing_time_array)) <= count($order_product_categories)) && count(array_intersect($order_product_categories, $exclude_category_processing_time_array))>0;

			$exclude_product_processing_time_condition = (count(array_intersect($product_id, $exclude_product_processing_time)) <= count($product_id)) && count(array_intersect($product_id, $exclude_product_processing_time))>0;

			if($exclude_category_processing_time_condition && !$exclude_product_processing_time_condition) {
				$exclude_condition_processing_time = true;
	  			$pre_product = 0;
				foreach($product_id as $product) {
					$parent_id  = wp_get_post_parent_id( $product );
					$product = $parent_id > 0 ? $parent_id : $product;
					$term_lists = wp_get_post_terms($product,'product_cat',array('fields'=>'ids'));

					foreach($term_lists as $cat_id) {
						$term = get_term_by( 'id', $cat_id, 'product_cat' );
						if(in_array(stripslashes(strtolower(htmlspecialchars_decode($term->name))), $exclude_category_processing_time_array)) {
							$exclude_condition_processing_time = true;
							$pre_product = $product;
							break;
						} elseif(!in_array(stripslashes(strtolower(htmlspecialchars_decode($term->name))), $exclude_category_processing_time_array)) {
							if($pre_product != $product) {
								$exclude_condition_processing_time = false;
								if(count($product_id)>1)
									break 2;
							}

						}

					}
					
				}
			} elseif($exclude_product_processing_time_condition && !$exclude_category_processing_time_condition) {
				$exclude_condition_processing_time = true;
				if(count($product_id) > 1 && count(array_diff($product_id, $exclude_product_processing_time))>0) {
	  				$exclude_condition_processing_time = !$exclude_condition_processing_time;
	  			}
			} elseif($exclude_product_processing_time_condition && $exclude_category_processing_time_condition) {
				$exclude_condition_processing_time = true;
				$pre_product = 0;					
				foreach($product_id as $product) {
					
					if(!in_array($product, $exclude_product_processing_time)) {
						$parent_id  = wp_get_post_parent_id( $product );
						$product = $parent_id > 0 ? $parent_id : $product;
						$term_lists = wp_get_post_terms($product,'product_cat',array('fields'=>'ids'));
						
						foreach($term_lists as $cat_id) {
							$term = get_term_by( 'id', $cat_id, 'product_cat' );
							if(in_array(stripslashes(strtolower(htmlspecialchars_decode($term->name))), $exclude_category_processing_time_array)) {
								$exclude_condition_processing_time = true;
								$pre_product = $product;
								break;
							} elseif(!in_array(stripslashes(strtolower(htmlspecialchars_decode($term->name))), $exclude_category_processing_time_array)) {
								if($pre_product != $product) {
									$exclude_condition_processing_time = false;
									if(count($product_id)>1)
										break 2;
								}
								
							}
							
						}
					}
				}
			} else {
				$exclude_condition_processing_time = false;
			}

			if(!$exclude_condition_processing_time) {
				if($overall_processing_time !== 0) {
					array_push($max_processing_time_array,(int)$processing_time_settings['overall_processing_time']);
				}

				if($overall_processing_time_pickup !== 0) {
					array_push($max_processing_time_array_pickup,(int)$processing_time_settings['overall_processing_time_pickup']);
				}
			}
		}

		$max_processing_time = count($max_processing_time_array) > 0 ? max($max_processing_time_array) : 0;
		$max_processing_time_pickup = count($max_processing_time_array_pickup) > 0 ? max($max_processing_time_array_pickup) : 0;

		$disable_week_days_category = [];
		$pickup_disable_week_days_category = [];
		
		if(isset($category_wise_offdays) && !empty($category_wise_offdays)) {
			
			if(isset($category_wise_offdays['both']) && !empty($category_wise_offdays['both'])) {
				
				foreach ($category_wise_offdays['both'] as $key => $value)
				{

					if(in_array(stripslashes(strtolower($key)), $order_product_categories))
					{
						if(isset($value['weekday_offdays']) && !empty($value['weekday_offdays'])) {
							foreach($value['weekday_offdays'] as $off_day) {
								$disable_week_days_category[] = $off_day;
								$pickup_disable_week_days_category[] = $off_day;
							}
						}
						

						if(isset($value['specific_date_offdays']) && !empty($value['specific_date_offdays'])) {
							foreach($value['specific_date_offdays'] as $date_off_day) {
								$disable_dates[] = trim($date_off_day);
								$pickup_disable_dates[] = trim($date_off_day);
							}
						}
					}
				}

			}

			if(isset($category_wise_offdays['delivery']) && !empty($category_wise_offdays['delivery'])) {
				
				foreach ($category_wise_offdays['delivery'] as $key => $value)
				{

					if(in_array(stripslashes(strtolower($key)), $order_product_categories))
					{
						if(isset($value['weekday_offdays']) && !empty($value['weekday_offdays'])) {
							foreach($value['weekday_offdays'] as $off_day) {
								$disable_week_days_category[] = $off_day;
							}
						}
						

						if(isset($value['specific_date_offdays']) && !empty($value['specific_date_offdays'])) {
							foreach($value['specific_date_offdays'] as $date_off_day) {
								$disable_dates[] = trim($date_off_day);
							}
						}
					}
				}

			}

			if(isset($category_wise_offdays['pickup']) && !empty($category_wise_offdays['pickup'])) {
				
				foreach ($category_wise_offdays['pickup'] as $key => $value)
				{

					if(in_array(stripslashes(strtolower($key)), $order_product_categories))
					{
						if(isset($value['weekday_offdays']) && !empty($value['weekday_offdays'])) {
							foreach($value['weekday_offdays'] as $off_day) {
								$pickup_disable_week_days_category[] = $off_day;
							}
						}
						

						if(isset($value['specific_date_offdays']) && !empty($value['specific_date_offdays'])) {
							foreach($value['specific_date_offdays'] as $date_off_day) {
								$pickup_disable_dates[] = trim($date_off_day);
							}
						}
					}
				}

			}
		}

		$disable_week_days_category = array_unique($disable_week_days_category, false);
		$disable_week_days_category = array_values($disable_week_days_category);
		$disable_week_days_category = implode(",",$disable_week_days_category);

		$pickup_disable_week_days_category = array_unique($pickup_disable_week_days_category, false);
		$pickup_disable_week_days_category = array_values($pickup_disable_week_days_category);
		$pickup_disable_week_days_category = implode(",",$pickup_disable_week_days_category);

		$disable_week_days_product = [];
		
		if(!empty($product_wise_offdays)) {

			foreach ($product_wise_offdays as $key => $value)
			{
				if(in_array($key, $product_id))
				{
					if(isset($value['weekday_offdays']) && !empty($value['weekday_offdays'])) {
						foreach($value['weekday_offdays'] as $off_day) {
							$disable_week_days_product[] = $off_day;
						}
					}

					if(isset($value['specific_date_offdays']) && !empty($value['specific_date_offdays'])) {
						foreach($value['specific_date_offdays'] as $date_off_day) {
							$disable_dates[] = trim($date_off_day);
							$pickup_disable_dates[] = trim($date_off_day);
						}
					}
				}
			}
		}

		$disable_week_days_product = array_unique($disable_week_days_product, false);
		$disable_week_days_product = array_values($disable_week_days_product);

		$disable_week_days_product = implode(",",$disable_week_days_product);

		$special_open_days_categories = [];

		$special_open_days_pickup_categories = [];

		$off_dates_for_off_before_pickup = [];
		$off_dates_for_off_before = [];

		if(!empty($opendays_categories)) {

			$order_product_categories = $this->helper->order_product_categories_opendays_exclusion_condition($order_items, array_map('strtolower', array_map('stripslashes', array_keys($opendays_categories))));

			foreach($opendays_categories as $category_name => $open_dates_array) {
				if(in_array(stripslashes(strtolower($category_name)), $order_product_categories)) {
					$open_dates = $open_dates_array['specific_date_open'];
					$off_before = isset($open_dates_array['off_before']) && $open_dates_array['off_before'] != "" ? $open_dates_array['off_before'] : 0;
					$open_dates = array_filter($open_dates,function($date) use ($off_before){
					    return $this->helper->wp_strtotime($date) >= $this->helper->wp_strtotime('today') + (86400 * (int)$off_before);
					});

					if($off_before > 0 && (isset($open_dates) && !empty($open_dates))) {
						$today_date_for_off_before = wp_date('Y-m-d');
					
						$off_dates_for_off_before[] = $today_date_for_off_before;
						for($i =1; $i < $off_before; $i++){
							$today_date_for_off_before = wp_date('Y-m-d', $this->helper->wp_strtotime('+1 day', $this->helper->wp_strtotime($today_date_for_off_before)));
							$off_dates_for_off_before[] = wp_date('Y-m-d', $this->helper->wp_strtotime($today_date_for_off_before));
						}
					}

					$special_open_days_categories = array_values(array_unique(array_merge($special_open_days_categories,$open_dates)));
				}
			}

			$opendays_categories_key = array_map('strtolower', array_map('stripslashes', array_keys($opendays_categories)));
			
			$disable_categories_opendays_condition = (count(array_intersect($order_product_categories, $opendays_categories_key)) <= count($order_product_categories)) && count(array_intersect($order_product_categories, $opendays_categories_key))>0 && count($order_product_categories) > 1 && count($product_id) > 1 && count(array_diff($order_product_categories, $opendays_categories_key))>0;
			
			if($disable_opendays_regular_product && $disable_categories_opendays_condition) {
	  			$special_open_days_categories = [];
	  		}

	  		if(!empty($special_open_days_categories)) {
		  		$special_category_name_common_date = [];
		  		$all_date_special_category = [];
		  		$all_date_special_category_before = [];
		  		$common_date_special_category = [];
		  		if (count(array_intersect($order_product_categories, $opendays_categories_key)) <= count($order_product_categories) && count(array_intersect($order_product_categories, $opendays_categories_key)) > 1 && count($order_product_categories) > 1 && count($product_id) > 1 ) {
					$special_category_name_common_date = array_intersect($order_product_categories, $opendays_categories_key);
					$opendays_categories_with_case_insensitive = array_change_key_case($opendays_categories);
					
					foreach($special_category_name_common_date as $special_category_name) {
						$all_date_special_category = array_merge($all_date_special_category, $opendays_categories_with_case_insensitive[$special_category_name]['specific_date_open']);
						if(isset($opendays_categories_with_case_insensitive[$special_category_name]['off_before']) && $opendays_categories_with_case_insensitive[$special_category_name]['off_before'] != 0){
							$all_date_special_category_before [] =  $opendays_categories_with_case_insensitive[$special_category_name]['off_before'];
						}
					}
					
					foreach(array_count_values($all_date_special_category) as $val => $c)
					    if($c == count($special_category_name_common_date)) $common_date_special_category[] = $val;

					if(!empty($all_date_special_category_before) && max($all_date_special_category_before) > 0) {

						$max_all_date_special_category_before = max($all_date_special_category_before);

						$common_date_special_category = array_filter($common_date_special_category,function($date) use ($max_all_date_special_category_before){

						    return $this->helper->wp_strtotime($date) >= $this->helper->wp_strtotime('today') + (86400 * (int)$max_all_date_special_category_before);
						});

					}

					$special_open_days_categories = array_values(array_unique($common_date_special_category));				

				}

			}
		}

		$special_open_days_categories = implode("::",$special_open_days_categories);
		
		if(!empty($opendays_pickup_categories)) {

			$order_product_categories = $this->helper->order_product_categories_opendays_exclusion_condition($order_items, array_map('strtolower', array_map('stripslashes', array_keys($opendays_pickup_categories))));

			foreach($opendays_pickup_categories as $category_name => $open_dates_array) {
				if(in_array(stripslashes(strtolower($category_name)), $order_product_categories)) {
					$open_dates = $open_dates_array['specific_date_open'];
					$off_before = isset($open_dates_array['off_before']) && $open_dates_array['off_before'] != "" ? $open_dates_array['off_before'] : 0;
					$open_dates = array_filter($open_dates,function($date) use ($off_before){
					    return $this->helper->wp_strtotime($date) >= $this->helper->wp_strtotime('today') + (86400 * (int)$off_before);
					});
					if($off_before > 0 && (isset($open_dates) && !empty($open_dates))) {
						$today_date_for_off_before_pickup = wp_date('Y-m-d');
						
						$off_dates_for_off_before_pickup[] = $today_date_for_off_before_pickup;
						for($i =1; $i < $off_before; $i++){
							$today_date_for_off_before_pickup = wp_date('Y-m-d', $this->helper->wp_strtotime('+1 day', $this->helper->wp_strtotime($today_date_for_off_before_pickup)));
							$off_dates_for_off_before_pickup[] = wp_date('Y-m-d', $this->helper->wp_strtotime($today_date_for_off_before_pickup));
						}
					}
					$special_open_days_pickup_categories = array_values(array_unique(array_merge($special_open_days_pickup_categories,$open_dates)));
				}
			}

			$opendays_pickup_categories_key = array_map('strtolower', array_map('stripslashes', array_keys($opendays_pickup_categories)));
			
			$disable_categories_opendays_pickup_condition = (count(array_intersect($order_product_categories, $opendays_pickup_categories_key)) <= count($order_product_categories)) && count(array_intersect($order_product_categories, $opendays_pickup_categories_key))>0 && count($order_product_categories) > 1 && count($product_id) > 1 && count(array_diff($order_product_categories, $opendays_pickup_categories_key))>0;
			
			if($disable_opendays_pickup_regular_product && $disable_categories_opendays_pickup_condition) {
	  			$special_open_days_pickup_categories = [];
	  		}

	  		//$opendays_pickup_categories_key = array_map('strtolower', array_map('stripslashes', array_keys($opendays_pickup_categories)));
	  		if(!empty($special_open_days_pickup_categories)) {
		  		$special_category_name_common_date_pickup = [];
		  		$all_date_special_category_pickup = [];
		  		$all_date_special_category_pickup_before = [];
		  		$common_date_special_category_pickup = [];
		  		if (count(array_intersect($order_product_categories, $opendays_pickup_categories_key)) <= count($order_product_categories) && count(array_intersect($order_product_categories, $opendays_pickup_categories_key)) > 1 && count($order_product_categories) > 1 && count($product_id) > 1 ) {
					$special_category_name_common_date_pickup = array_intersect($order_product_categories, $opendays_pickup_categories_key);
					$opendays_pickup_categories_with_case_insensitive = array_change_key_case($opendays_pickup_categories);
					
					foreach($special_category_name_common_date_pickup as $special_category_name) {
						$all_date_special_category_pickup = array_merge($all_date_special_category_pickup, $opendays_pickup_categories_with_case_insensitive[$special_category_name]['specific_date_open']);
						if(isset($opendays_pickup_categories_with_case_insensitive[$special_category_name]['off_before']) && $opendays_pickup_categories_with_case_insensitive[$special_category_name]['off_before'] != 0){
							$all_date_special_category_pickup_before [] = $opendays_pickup_categories_with_case_insensitive[$special_category_name]['off_before'];
						}
					}
					
					foreach(array_count_values($all_date_special_category_pickup) as $val => $c)
					    if($c == count($special_category_name_common_date_pickup)) $common_date_special_category_pickup[] = $val;

					if(!empty($all_date_special_category_pickup_before) && max($all_date_special_category_pickup_before) > 0) {

						$max_all_date_special_category_pickup_before = max($all_date_special_category_pickup_before);

						$common_date_special_category_pickup = array_filter($common_date_special_category_pickup,function($date) use ($max_all_date_special_category_pickup_before){

						    return $this->helper->wp_strtotime($date) >= $this->helper->wp_strtotime('today') + (86400 * (int)$max_all_date_special_category_pickup_before);
						});

					}

					$special_open_days_pickup_categories = array_values(array_unique($common_date_special_category_pickup));				

				}

			}
		}

		$special_open_days_pickup_categories = implode("::",$special_open_days_pickup_categories);

		$cutoff_categories_array = [];

		if(!empty($cutoff_categories)) {
			
			foreach($cutoff_categories as $category_name => $cutoff) {
				if(in_array(stripslashes(strtolower($category_name)), $order_product_categories)) {
					$cutoff_categories_array[] = $cutoff;
				}
			}

			$cutoff_categories_key = array_map('strtolower', array_map('stripslashes', array_keys($cutoff_categories)));
			
			$disable_categories_cutoff_condition = (count(array_intersect($order_product_categories, $cutoff_categories_key)) <= count($order_product_categories)) && count(array_intersect($order_product_categories, $cutoff_categories_key))>0 && count($order_product_categories) > 1 && count($product_id) > 1 && count(array_diff($order_product_categories, $cutoff_categories_key))>0;
			
			if($disable_category_wise_cutoff_regular_category && $disable_categories_cutoff_condition) {
				$cutoff_categories_array = [];
			}
		}

		$category_cutoff_value = "";

		if(!empty($cutoff_categories_array)){
			if($consider_multiple_cutoff_category_condition == "last") {
				$category_cutoff_value = max( $cutoff_categories_array );
			} else {
				$category_cutoff_value = min( $cutoff_categories_array );
			}
		}

		if($category_cutoff_value != "" && (wp_date("G")*60)+wp_date("i") >= $category_cutoff_value) {
			$disable_dates[] = $today;
			$pickup_disable_dates[] = $today;
		}

		$current_time = (wp_date("G")*60)+wp_date("i");

		$last_processing_time_date = "";

		if($max_processing_time>0){
			$max_processing_time_with_current = $current_time+$max_processing_time;
			if($max_processing_time_with_current>=1440) {
				$x = 1440;
				$date = $today;
				$days_from_processing_time =0;
				while($max_processing_time_with_current>=$x) {
					$second_time = $max_processing_time_with_current - $x;
					$formated = wp_date('Y-m-d H:i:s', $this->helper->wp_strtotime($date));
					$formated_obj = current_datetime($formated);
					$processing_time_date = $formated_obj->modify("+".$days_from_processing_time." day")->format("Y-m-d");
					$last_processing_time_date = $processing_time_date;
					$disable_dates[] = $processing_time_date;
					$max_processing_time_with_current = $second_time;
					$max_processing_time = $second_time;
					$days_from_processing_time = $days_from_processing_time+1;
				}

				$formated_last_processing = wp_date('Y-m-d H:i:s', $this->helper->wp_strtotime($last_processing_time_date));
				$formated_obj_last_processing = current_datetime($formated_last_processing);
				$last_processing_time_date = $formated_obj_last_processing->modify("+1 day")->format("Y-m-d");
			} else {
				$last_processing_time_date = $today;
			}
		}

		$current_time = (wp_date("G")*60)+wp_date("i");
		$last_processing_time_date_pickup = "";

		if($max_processing_time_pickup>0){
			$max_processing_time_pickup_with_current = $current_time+$max_processing_time_pickup;
			if($max_processing_time_pickup_with_current>=1440) {
				$x = 1440;
				$date = $today;
				$days_from_processing_time_pickup =0;
				while($max_processing_time_pickup_with_current>=$x) {
					$second_time = $max_processing_time_pickup_with_current - $x;
					$formated = wp_date('Y-m-d H:i:s', $this->helper->wp_strtotime($date));
					$formated_obj = current_datetime($formated);
					$processing_time_date_pickup = $formated_obj->modify("+".$days_from_processing_time_pickup." day")->format("Y-m-d");
					$last_processing_time_date_pickup = $processing_time_date_pickup;
					$pickup_disable_dates[] = $processing_time_date_pickup;
					$max_processing_time_pickup_with_current = $second_time;
					$max_processing_time_pickup = $second_time;
					$days_from_processing_time_pickup = $days_from_processing_time_pickup+1;
				}

				$formated_last_processing_pickup = wp_date('Y-m-d H:i:s', $this->helper->wp_strtotime($last_processing_time_date_pickup));
				$formated_obj_last_processing_pickup = current_datetime($formated_last_processing_pickup);
				$last_processing_time_date_pickup = $formated_obj_last_processing_pickup->modify("+1 day")->format("Y-m-d");
			} else {
				$last_processing_time_date_pickup = $today;
			}
		}

		$disable_timeslot_with_processing_time = (isset($processing_time_settings['disable_timeslot_with_processing_time']) && !empty($processing_time_settings['disable_timeslot_with_processing_time'])) ? $processing_time_settings['disable_timeslot_with_processing_time'] : false;

		$enable_closing_time = (isset($delivery_time_settings['enable_closing_time']) && !empty($delivery_time_settings['enable_closing_time'])) ? $delivery_time_settings['enable_closing_time'] : false;

		$enable_closing_time_pickup = (isset($pickup_time_settings['enable_closing_time']) && !empty($pickup_time_settings['enable_closing_time'])) ? $pickup_time_settings['enable_closing_time'] : false;

		$enable_different_closing_time = (isset($delivery_time_settings['enable_different_closing_time']) && !empty($delivery_time_settings['enable_different_closing_time'])) ? $delivery_time_settings['enable_different_closing_time'] : false;

		$extended_closing_time_delivery = 0;
		$extended_closing_time_pickup = 0;
		$last_closing_time_date_delivery = $tomorrow;
		$last_closing_time_date_pickup = $tomorrow;
		$detect_next_month_off_category = false;
		$current_month_remaining_date = [];		
		if(!empty($next_month_off_categories)) {

			$month_last_date = current_datetime($today)->modify('last day of this month')->format('Y-m-d');

			$current_month_remaining_date =  $this->helper->get_date_from_range($today, $month_last_date);
			
			foreach ($next_month_off_categories as $key => $value)
			{
				if(in_array(stripslashes(strtolower($value)), $order_product_categories))
				{
					$detect_next_month_off_category = true;
					break;
				}
			}
		}

		$current_month_remaining_date = implode("::",$current_month_remaining_date);
		
		$detect_next_week_off_category = false;
		$current_week_remaining_date = [];
		$detect_next_week_off_category_pickup = false;
		$current_week_remaining_date_pickup = [];
		$detect_current_week_off_category = false;
		$detect_current_week_off_category_pickup = false;
		$week_starts_from = (isset($delivery_date_settings['week_starts_from']) && !empty($delivery_date_settings['week_starts_from'])) ? $delivery_date_settings['week_starts_from']:"0";
		$pickup_week_starts_from = (isset($pickup_date_settings['week_starts_from']) && !empty($pickup_date_settings['week_starts_from'])) ? $pickup_date_settings['week_starts_from']:"0";		
		if(!empty($next_week_off_categories) || !empty($current_week_off_categories)) {

			foreach ($next_week_off_categories as $key => $value)
			{
				if(in_array(stripslashes(strtolower($value)), $order_product_categories))
				{
					$detect_next_week_off_category = true;
					$detect_next_week_off_category_pickup = true;
					break;
				}
			}

			foreach ($current_week_off_categories as $key => $value)
			{
				if(in_array(stripslashes(strtolower($value)), $order_product_categories))
				{
					$detect_current_week_off_category = true;
					$detect_current_week_off_category_pickup = true;
					break;
				}
			}

			if($detect_next_week_off_category || $detect_next_week_off_category_pickup || $detect_current_week_off_category || $detect_current_week_off_category_pickup) {

				$week_last_date = current_datetime($today)->modify($this->helper->week_last_date($week_starts_from).' this week')->format('Y-m-d');
				$week_last_date_pickup = current_datetime($today)->modify($this->helper->week_last_date($pickup_week_starts_from).' this week')->format('Y-m-d');

				$current_week_remaining_date =  $this->helper->get_date_from_range($today, $week_last_date);
				$current_week_remaining_date_pickup =  $this->helper->get_date_from_range($today, $week_last_date_pickup);

			}
		}

		$current_week_remaining_date = implode("::",$current_week_remaining_date);
		$current_week_remaining_date_pickup = implode("::",$current_week_remaining_date_pickup);


		if(metadata_exists('post', $order_id, 'delivery_type') || $order->meta_exists('delivery_type')) {
			if($this->hpos) {
				$delivery_type = $order->get_meta( 'delivery_type', true );
			} else {
				$delivery_type = get_post_meta(  $order_id, 'delivery_type', true );
			}
	    	
	    } else {
	    	$delivery_type="delivery";
	    }

		$enable_delivery_time = (isset($delivery_time_settings['enable_delivery_time']) && !empty($delivery_time_settings['enable_delivery_time'])) ? $delivery_time_settings['enable_delivery_time'] : false;
		
		$enable_pickup_time = (isset($pickup_time_settings['enable_pickup_time']) && !empty($pickup_time_settings['enable_pickup_time'])) ? $pickup_time_settings['enable_pickup_time'] : false;

	    $delivery_date_format = (isset($delivery_date_settings['date_format']) && $delivery_date_settings['date_format'] != "") ? $delivery_date_settings['date_format'] : "F j, Y";
	    $add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

		if($add_weekday_name) {
			$delivery_date_format = "l ".$delivery_date_format;
		}

	    if(metadata_exists('post', $order_id, 'delivery_date') || $order->meta_exists('delivery_date')) {
	    	if($this->hpos) {
				$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime($order->get_meta( 'delivery_date', true ))),"delivery"),"delivery");
			} else {
				$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime(get_post_meta( $order_id, 'delivery_date', true ))),"delivery"),"delivery");
			}
	    } else {
	    	$delivery_date="";
	    }

	    $pickup_date_format = (isset($pickup_date_settings['date_format']) && $pickup_date_settings['date_format'] != "") ? $pickup_date_settings['date_format'] : "F j, Y";
	    $pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

		if($pickup_add_weekday_name) {
			$pickup_date_format = "l ".$pickup_date_format;
		}
	    if(metadata_exists('post', $order_id, 'pickup_date') || $order->meta_exists('pickup_date')) {
	    	if($this->hpos) {
				$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime($order->get_meta( 'pickup_date', true ))),"pickup"),"pickup");
			} else {
				$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime(get_post_meta( $order_id, 'pickup_date', true ))),"pickup"),"pickup");
			}
	    } else {
	    	$pickup_date="";
	    }

	    $time_options = Coderockz_Woo_Delivery_Time_Option::delivery_time_option($delivery_time_settings,"meta_box", $order_id);
	    $pickup_options = Coderockz_Woo_Delivery_Pickup_Option::pickup_time_option($pickup_time_settings,"meta_box", $order_id);
	    $delivery_options = Coderockz_Woo_Delivery_Delivery_Option::delivery_option($delivery_option_settings,"meta_box", $order_id);

	    if(metadata_exists('post', $order_id, 'delivery_time') || $order->meta_exists('delivery_time')) {
	    	if($this->hpos) {
				$time = $order->get_meta( 'delivery_time', true );
			} else {
				$time = get_post_meta($order_id,"delivery_time",true);
			}

	    } else {
	    	$time="";
	    }

	    if(metadata_exists('post', $order_id, 'pickup_time') || $order->meta_exists('pickup_time')) {
	    	if($this->hpos) {
				$pickup_time = $order->get_meta( 'pickup_time', true );
			} else {
				$pickup_time = get_post_meta($order_id,"pickup_time",true);
			}
	    } else {
	    	$pickup_time="";
	    }

	    $pickup_location_settings = get_option('coderockz_woo_delivery_pickup_location_settings');

		$enable_pickup_location = (isset($pickup_location_settings['enable_pickup_location']) && !empty($pickup_location_settings['enable_pickup_location'])) ? $pickup_location_settings['enable_pickup_location'] : false;
		$pickup_location_options = Coderockz_Woo_Delivery_Pickup_Location_Option::pickup_location_option($pickup_location_settings,"meta_box", $order_id);
		if(metadata_exists('post', $order_id, 'pickup_location') || $order->meta_exists('pickup_location')) {
			if($this->hpos) {
				$location = stripslashes($order->get_meta( 'pickup_location', true ));
			} else {
				$location = stripslashes(get_post_meta($order_id, 'pickup_location', true));
			} 
		} else {
			$location="";
		}

		$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');

		$enable_additional_field = (isset($additional_field_settings['enable_additional_field']) && !empty($additional_field_settings['enable_additional_field'])) ? $additional_field_settings['enable_additional_field'] : false;

		$hide_additional_field_for = (isset($additional_field_settings['hide_additional_field_for']) && !empty($additional_field_settings['hide_additional_field_for'])) ? $additional_field_settings['hide_additional_field_for'] : array();

		if($enable_additional_field && count($hide_additional_field_for) > 0) {
			$hide_additional_field_for = $hide_additional_field_for;
		} else {
			$hide_additional_field_for = array();
		}

		$hide_additional_field_for = implode("::",$hide_additional_field_for);

		$additional_field_character_limit = (isset($additional_field_settings['character_limit']) && !empty($additional_field_settings['character_limit'])) ? (int)$additional_field_settings['character_limit'] : "";
		$character_remaining_text = (isset($additional_field_settings['character_remaining_text']) && !empty($additional_field_settings['character_remaining_text'])) ? $additional_field_settings['character_remaining_text'] : __("characters remaining","coderockz-woo-delivery");
		$additional_field_label = isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label']) ? stripslashes(esc_attr($additional_field_settings['field_label'])) : __("Special Note for Delivery","coderockz-woo-delivery");

		if(metadata_exists('post', $order_id, 'additional_note') || $order->meta_exists('additional_note')) {
			if($this->hpos) {
				$special_note = $order->get_meta( 'additional_note', true );
			} else {
				$special_note = get_post_meta($order_id, 'additional_note', true);
			}

			$special_note = stripslashes(html_entity_decode($special_note, ENT_QUOTES));
		} else {
			$special_note = "";
		}

		if(metadata_exists('post', $order_id, 'delivery_type') || $order->meta_exists('delivery_type')) {
			if(get_post_meta($order_id, 'delivery_type', true) == 'delivery' || $order->get_meta( 'delivery_type', true ) == "delivery") {
				$delivery_complete_btn_text = __("Delivery","coderockz-woo-delivery");
			} elseif(get_post_meta($order_id, 'delivery_type', true) == 'pickup' || $order->get_meta( 'delivery_type', true ) == "pickup") {
				$delivery_complete_btn_text = __("Pickup","coderockz-woo-delivery");
			}
		} else {
			$delivery_complete_btn_text = __("Delivery","coderockz-woo-delivery");
		}

		$localization_settings = get_option('coderockz_woo_delivery_localization_settings');
		$order_limit_notice = (isset($localization_settings['order_limit_notice']) && !empty($localization_settings['order_limit_notice'])) ? "(".stripslashes($localization_settings['order_limit_notice']).")" : __("(Maximum Delivery Limit Exceed)","coderockz-woo-delivery");
		$pickup_limit_notice = (isset($localization_settings['pickup_limit_notice']) && !empty($localization_settings['pickup_limit_notice'])) ? "(".stripslashes($localization_settings['pickup_limit_notice']).")" : __("(Maximum Pickup Limit Exceed)","coderockz-woo-delivery");
		$pickup_location_limit_notice = (isset($localization_settings['pickup_location_limit_notice']) && !empty($localization_settings['pickup_location_limit_notice'])) ? "(".stripslashes($localization_settings['pickup_location_limit_notice']).")" : __("(Maximum Pickup Limit Exceed For This Location)","coderockz-woo-delivery");

		$delivery_field_label = (isset($delivery_option_settings['delivery_label']) && !empty($delivery_option_settings['delivery_label'])) ? $delivery_option_settings['delivery_label'] : __("Delivery","coderockz-woo-delivery");
		$pickup_field_label = (isset($delivery_option_settings['pickup_label']) && !empty($delivery_option_settings['pickup_label'])) ? $delivery_option_settings['pickup_label'] : __("Pickup","coderockz-woo-delivery");

		$enable_laundry_store_settings = (isset($laundry_store_settings['enable_laundry_store_settings']) && $laundry_store_settings['enable_laundry_store_settings'] != "") ? $laundry_store_settings['enable_laundry_store_settings'] : false;

		$hide_disabled_timeslot = (isset($other_settings['hide_disabled_timeslot']) && !empty($other_settings['hide_disabled_timeslot'])) ? $other_settings['hide_disabled_timeslot'] : false;

		$shipping_zone_wise_processing_time = false;
		if(isset($processing_time_settings['zone_wise_processing_time']) && !empty($processing_time_settings['zone_wise_processing_time'])) {
			$shipping_zone_wise_processing_time = true;
		}
		
		$meta_box = '<div data-shipping_zone_wise_processing_time="'.$shipping_zone_wise_processing_time.'" data-enable_laundry_store_settings="'.$enable_laundry_store_settings.'" data-hide_disabled_timeslot="'.$hide_disabled_timeslot.'" data-tomorrow_date="'.$tomorrow.'" data-today_date="'.$today.'" data-hide_additional_field_for="'.$hide_additional_field_for.'" id="coderockz_woo_delivery_admin_setting_wrapper">';
        $meta_box .= '<input type="hidden" id="coderockz_woo_delivery_meta_box_order_id" value="' . $order_id . '" readonly>';
        if($enable_delivery_option) {

        $meta_box .= '<select style="width:100%;margin:5px auto;display:none" name ="coderockz_woo_delivery_meta_box_delivery_selection_field" id="coderockz_woo_delivery_meta_box_delivery_selection_field">';
    		foreach($delivery_options as $key => $value) {
    			$selected = ($key == $delivery_type) ? "selected" : "";

    			if($enable_dynamic_order_type && $key == "delivery" && isset($has_other_method)) {
    				if(!empty($has_other_method)) {
    					$meta_box .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
    				} elseif($has_conditional_delivery_shipping_method) {
    					$meta_box .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
    				} else {
    					$meta_box .= '<option value="'.$key.'" '.$selected.' disabled="disabled">'.$value.'</option>';
    				}
    			} elseif($key == "delivery") {
    				$meta_box .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
    			}

    			if($enable_dynamic_order_type && $key == "pickup" && isset($has_local_pickup_method)) {
    				if(!empty($has_local_pickup_method)) {
    					$meta_box .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
    				} else {
    					$meta_box .= '<option value="'.$key.'" '.$selected.' disabled="disabled">'.$value.'</option>';
    				}
    				
    			} elseif($key == "pickup") {
    				$meta_box .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
    			}
    			
    		}
    		$meta_box .= '</select>';
        }

        if($enable_delivery_date) {

        	$delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : __("Delivery Date","coderockz-woo-delivery");
			

        	$delivery_days = isset($delivery_date_settings['delivery_days']) && $delivery_date_settings['delivery_days'] != "" ? $delivery_date_settings['delivery_days'] : "6,0,1,2,3,4,5";

        	$delivery_date_calendar_locale = (isset($delivery_date_settings['calendar_locale']) && !empty($delivery_date_settings['calendar_locale'])) ? $delivery_date_settings['calendar_locale'] : "default";
			$week_starts_from = (isset($delivery_date_settings['week_starts_from']) && !empty($delivery_date_settings['week_starts_from']))?$delivery_date_settings['week_starts_from']:"0";
		
			$selectable_date = (isset($delivery_date_settings['selectable_date']) && !empty($delivery_date_settings['selectable_date']))?$delivery_date_settings['selectable_date']:365;
			$selectable_date_until = (isset($delivery_date_settings['selectable_date_until']) && !empty($delivery_date_settings['selectable_date_until'])) ? $delivery_date_settings['selectable_date_until'] : "no_date";

			$same_day_delivery = (isset($delivery_date_settings['disable_same_day_delivery']) && !empty($delivery_date_settings['disable_same_day_delivery'])) ? $delivery_date_settings['disable_same_day_delivery'] : false;

			$delivery_days = explode(',', $delivery_days);

			$week_days = ['0', '1', '2', '3', '4', '5', '6'];
			$disable_week_days = array_values(array_diff($week_days, $delivery_days));

			$off_days = (isset($delivery_date_settings['off_days']) && !empty($delivery_date_settings['off_days'])) ? $delivery_date_settings['off_days'] : array();
			$off_day_dates = [];
			$selectable_start_date = wp_date('Y-m-d H:i:s', current_time( 'timestamp', 1 ));
			$start_date = current_datetime($selectable_start_date);
			if(count($off_days)) {
				$date = $start_date;
				foreach ($off_days as $year => $months) {
					foreach($months as $month =>$days){
						$month_num = date_parse($month)['month'];
						if(strlen($month_num) == 1) {
							$month_num_final = "0".$month_num;
						} else {
							$month_num_final = $month_num;
						}
						$days = explode(',', $days);
						foreach($days as $day){
							$disable_dates[] = $year . "-" . $month_num_final . "-" .$day;
							$off_day_dates[] = $year . "-" . $month_num_final . "-" .$day;
						}
					}
				}
			}

			$selectable_start_date = wp_date('Y-m-d H:i:s', current_time( 'timestamp', 1 ));
			$start_date = current_datetime($selectable_start_date);
			$max_processing_days = $temp_max_processing_days;
			
			if($max_processing_days > 0) {
				if($consider_current_day && $max_processing_days > 0) {
					if(($consider_weekends && in_array($start_date->format("w"), $disable_week_days)) || ($consider_off_days && in_array($start_date->format("Y-m-d"), $off_day_dates))) {

					} else {
						$disable_dates[] = $start_date->format("Y-m-d");
						$max_processing_days = $max_processing_days - 1;
						$start_date = $start_date->modify("+1 day");
					}
				} else {
					if(($consider_weekends && in_array($start_date->format("w"), $disable_week_days)) || ($consider_off_days && in_array($start_date->format("Y-m-d"), $off_day_dates))) {

					} else {
						$disable_dates[] = $start_date->format("Y-m-d");
						$start_date = $start_date->modify("+1 day");
					}
				}

				while($max_processing_days > 0) {
					$date = $start_date;
					if($consider_weekends) {

						$disable_dates[] = $date->format("Y-m-d");
						$max_processing_days = $max_processing_days - 1;
						$start_date = $start_date->modify("+1 day");
					} else {
						if (!in_array($date->format("w"), $disable_week_days)) {
							$disable_dates[] = $date->format("Y-m-d");
							$max_processing_days = $max_processing_days - 1;
							$start_date = $start_date->modify("+1 day");
						} else {
							$disable_dates[] = $date->format("Y-m-d");
							$start_date = $start_date->modify("+1 day");

						}

					}

				}

			}

			$selectable_start_date_sec = wp_date('Y-m-d H:i:s', current_time( 'timestamp', 1 ));
			$start_date_sec = current_datetime($selectable_start_date_sec);
			$max_processing_days_sec = $temp_max_processing_days;

			if($max_processing_days_sec > 0) {

				if($consider_current_day && $max_processing_days_sec > 0) {
					if(($consider_weekends && in_array($start_date_sec->format("w"), $disable_week_days)) || ($consider_off_days && in_array($start_date_sec->format("Y-m-d"), $off_day_dates))) {

					} else {
						$disable_dates[] = $start_date_sec->format("Y-m-d");
						$max_processing_days_sec = $max_processing_days_sec - 1;
						$start_date_sec = $start_date_sec->modify("+1 day");
					}
				} else {
					if(($consider_weekends && in_array($start_date_sec->format("w"), $disable_week_days)) || ($consider_off_days && in_array($start_date_sec->format("Y-m-d"), $off_day_dates))) {

					} else {
						$disable_dates[] = $start_date_sec->format("Y-m-d");
						$start_date_sec = $start_date_sec->modify("+1 day");
					}
				}

				while($max_processing_days_sec > 0) {
					$date = $start_date_sec;
					if($consider_off_days) {
						
						$disable_dates[] = $date->format("Y-m-d");
						$max_processing_days_sec = $max_processing_days_sec - 1;
						$start_date_sec = $start_date_sec->modify("+1 day");

					} else {
						if (!in_array($date->format("Y-m-d"), $off_day_dates)) {
							$disable_dates[] = $date->format("Y-m-d");
							$max_processing_days_sec = $max_processing_days_sec - 1;
							$start_date_sec = $start_date_sec->modify("+1 day");
						} else {

							$disable_dates[] = $date->format("Y-m-d");
							$start_date_sec = $start_date_sec->modify("+1 day");

						}

					}

				}
	
			}

			$enable_closing_time = (isset($delivery_time_settings['enable_closing_time']) && !empty($delivery_time_settings['enable_closing_time'])) ? $delivery_time_settings['enable_closing_time'] : false;

			$enable_different_closing_time = (isset($delivery_time_settings['enable_different_closing_time']) && !empty($delivery_time_settings['enable_different_closing_time'])) ? $delivery_time_settings['enable_different_closing_time'] : false;

			if($enable_closing_time) {
				$store_closing_time = isset($delivery_time_settings['store_closing_time']) ? (int)$delivery_time_settings['store_closing_time'] : "";

				$current_time = (wp_date("G")*60)+wp_date("i");

				$extended_closing_days = (isset($delivery_time_settings['extended_closing_days']) && !empty($delivery_time_settings['extended_closing_days'])) ? (int)$delivery_time_settings['extended_closing_days'] : 0;

				if(($store_closing_time != "" || $store_closing_time === 0) && ($current_time >= $store_closing_time)) {
					$extended_closing_time_delivery = (isset($delivery_time_settings['extended_closing_time']) && !empty($delivery_time_settings['extended_closing_time'])) ? (int)$delivery_time_settings['extended_closing_time'] : 0;

					if($extended_closing_time_delivery>=1440) {
						$x = 1440;
						$date = $today;
						$days_from_closing_time =1;
						while($extended_closing_time_delivery>=$x) {
							$second_time = $extended_closing_time_delivery - $x;
							$formated = wp_date('Y-m-d H:i:s', $this->helper->wp_strtotime($date));
							$formated_obj = current_datetime($formated);
							$closing_time_date = $formated_obj->modify("+".$days_from_closing_time." day")->format("Y-m-d");
							$last_closing_time_date_delivery = $closing_time_date;
							$disable_dates[] = $closing_time_date;
							$extended_closing_time_delivery = $second_time;
							$days_from_closing_time = $days_from_closing_time+1;
						}

						$formated_last_closing = wp_date('Y-m-d H:i:s', $this->helper->wp_strtotime($last_closing_time_date_delivery));
						$formated_obj_last_closing = current_datetime($formated_last_closing);
						$last_closing_time_date_delivery = $formated_obj_last_closing->modify("+1 day")->format("Y-m-d");
					}

					$extended_closing_last_date = current_datetime($today)->modify("+".$extended_closing_days." day")->format('Y-m-d');
					$extended_closing_dates =  $this->helper->get_date_from_range($today, $extended_closing_last_date);
					$disable_dates = array_merge($disable_dates, $extended_closing_dates);
				}
			} elseif($enable_different_closing_time) {
				$current_week_day = date("w"); 				
				$store_closing_time = isset($delivery_time_settings['different_store_closing_time'][$current_week_day]) ? (int)$delivery_time_settings['different_store_closing_time'][$current_week_day] : "";
				$current_time = (date("G")*60)+date("i");

				$extended_closing_days = (isset($delivery_time_settings['different_extended_closing_day'][$current_week_day]) && $delivery_time_settings['different_extended_closing_day'][$current_week_day] != "") ? (int)$delivery_time_settings['different_extended_closing_day'][$current_week_day] : 0;

				if(($store_closing_time != "" || $store_closing_time === 0) && ($current_time >= $store_closing_time)) {
					$extended_closing_time_delivery = (isset($delivery_time_settings['different_extended_closing_time'][$current_week_day]) && $delivery_time_settings['different_extended_closing_time'][$current_week_day] != "") ? (int)$delivery_time_settings['different_extended_closing_time'][$current_week_day] : 0;
					
					$extended_closing_time_delivery = (isset($delivery_time_settings['extended_closing_time']) && !empty($delivery_time_settings['extended_closing_time'])) ? (int)$delivery_time_settings['extended_closing_time'] : 0;					
					if($extended_closing_time_delivery>=1440) {
						$x = 1440;
						$date = $today;
						$days_from_closing_time =1;
						while($extended_closing_time_delivery>=$x) {
							$second_time = $extended_closing_time_delivery - $x;
							$formated = wp_date('Y-m-d H:i:s', $this->helper->wp_strtotime($date));
							$formated_obj = current_datetime($formated);
							$closing_time_date = $formated_obj->modify("+".$days_from_closing_time." day")->format("Y-m-d");
							$last_closing_time_date_delivery = $closing_time_date;
							$disable_dates[] = $closing_time_date;
							$extended_closing_time_delivery = $second_time;
							$days_from_closing_time = $days_from_closing_time+1;
						}

						$formated_last_closing = wp_date('Y-m-d H:i:s', $this->helper->wp_strtotime($last_closing_time_date_delivery));
						$formated_obj_last_closing = current_datetime($formated_last_closing);
						$last_closing_time_date_delivery = $formated_obj_last_closing->modify("+1 day")->format("Y-m-d");
					}

					$extended_closing_last_date = current_datetime($today)->modify("+".$extended_closing_days." day")->format('Y-m-d');
					$extended_closing_dates =  $this->helper->get_date_from_range($today, $extended_closing_last_date);
					$disable_dates = array_merge($disable_dates, $extended_closing_dates);
				}

			}

			if($same_day_delivery) {
				$disable_dates[] = $today;
			}

			$disable_dates = array_unique($disable_dates, false);
			$disable_dates = array_values($disable_dates);

			$disable_week_days = implode(",",$disable_week_days);
		    $disable_dates = implode("::",$disable_dates);

		    $delivery_open_days = (isset($delivery_date_settings['open_days']) && !empty($delivery_date_settings['open_days'])) ? $delivery_date_settings['open_days'] : array();
			$special_open_days_dates = [];
			$selectable_start_date = wp_date('Y-m-d H:i:s', current_time( 'timestamp', 1 ));
			$start_date = current_datetime($selectable_start_date);
			if(count($delivery_open_days)) {
				$date = $start_date;
				foreach ($delivery_open_days as $year => $months) {
					foreach($months as $month => $days){
						$month_num = date_parse($month)['month'];
						if(strlen($month_num) == 1) {
							$month_num_final = "0".$month_num;
						} else {
							$month_num_final = $month_num;
						}
						$days = explode(',', $days);
						foreach($days as $day){
							if($this->helper->wp_strtotime($year . "-" . $month_num_final . "-" .$day) + 86400 >= time())	
								$special_open_days_dates[] = $year . "-" . $month_num_final . "-" .$day;
						}
					}
				}
			}

			$overall_off_before = isset($delivery_date_settings['overall_off_before']) && $delivery_date_settings['overall_off_before'] != "" ? $delivery_date_settings['overall_off_before'] : 0;

			$special_open_days_dates = array_filter($special_open_days_dates,function($date) use ($overall_off_before){
			    return $this->helper->wp_strtotime($date) >= $this->helper->wp_strtotime('today') + (86400 * (int)$overall_off_before);
			});

			if($overall_off_before > 0 && (isset($special_open_days_dates) && !empty($special_open_days_dates))) {
				$today_date_for_off_before = wp_date('Y-m-d');
			
				$off_dates_for_off_before[] = $today_date_for_off_before;
				for($i =1; $i < $overall_off_before; $i++){
					$today_date_for_off_before = wp_date('Y-m-d', $this->helper->wp_strtotime('+1 day', $this->helper->wp_strtotime($today_date_for_off_before)));
					$off_dates_for_off_before[] = wp_date('Y-m-d', $this->helper->wp_strtotime($today_date_for_off_before));
				}
			}
			
			$special_open_days_dates = array_diff($special_open_days_dates,$off_dates_for_off_before);

			$off_dates_for_off_before = implode("::",$off_dates_for_off_before);

			$special_open_days_dates = implode("::",$special_open_days_dates);

			$off_day_dates = implode("::",$off_day_dates);

			$laundry_delivery_date_consider_disabled_days = (isset($laundry_store_settings['delivery_date_consider_disabled_days']) && !empty($laundry_store_settings['delivery_date_consider_disabled_days'])) ? $laundry_store_settings['delivery_date_consider_disabled_days'] : false;
			$laundry_delivery_date_consider_selected_pickup_date = (isset($laundry_store_settings['delivery_date_consider_selected_pickup_date']) && !empty($laundry_store_settings['delivery_date_consider_selected_pickup_date'])) ? $laundry_store_settings['delivery_date_consider_selected_pickup_date'] : false;

			$after_pickup_dates_array = [];
			if($enable_laundry_store_settings) {
				$overall_after_pickup_dates = (isset($laundry_store_settings['overall_after_pickup_dates']) && $laundry_store_settings['overall_after_pickup_dates'] != "") ? array_push($after_pickup_dates_array,(int)$laundry_store_settings['overall_after_pickup_dates']) : 0;

				$enable_category_after_pickup_dates = (isset($laundry_store_settings['enable_category_after_pickup_dates']) && !empty($laundry_store_settings['enable_category_after_pickup_dates'])) ? $laundry_store_settings['enable_category_after_pickup_dates'] : false;

				if($enable_category_after_pickup_dates && !empty($category_after_pickup_dates)) {

					foreach ($category_after_pickup_dates as $key => $value)
					{
						if(in_array(stripslashes(strtolower($key)), $order_product_categories))
						{
							array_push($after_pickup_dates_array,(int)$value);
						}
					}
				}
			}

			$after_pickup_dates = count($after_pickup_dates_array) > 0 ? max($after_pickup_dates_array) : 0;

	        $meta_box .= '<input style="width:100%;margin:5px auto;display:none" type="text" id="coderockz_woo_delivery_meta_box_datepicker" placeholder="'.$delivery_date_field_label.'" name="coderockz_woo_delivery_meta_box_datepicker" data-off_day_dates_delivery="'.$off_day_dates.'" data-selectable_dates_until="'.$selectable_date_until.'" data-special_open_days_dates="'.$special_open_days_dates.'" data-same_day_delivery="'.$same_day_delivery.'" data-calendar_locale="'.$delivery_date_calendar_locale.'" data-disable_dates="'.$disable_dates.'" data-selectable_dates="'.$selectable_date.'" data-disable_week_days="'.$disable_week_days.'" data-disable_week_days_category="'.$disable_week_days_category.'" data-special_open_days_categories="'.$special_open_days_categories.'" data-off_dates_for_off_before="'.$off_dates_for_off_before.'" data-current_month_remaining_date="'.$current_month_remaining_date.'" data-detect_next_month_off_category="'.$detect_next_month_off_category.'" data-current_week_remaining_date="'.$current_week_remaining_date.'" data-detect_next_week_off_category="'.$detect_next_week_off_category.'" data-detect_current_week_off_category="'.$detect_current_week_off_category.'" data-disable_week_days_product="'.$disable_week_days_product.'" data-week_starts_from="'.$week_starts_from.'" data-date_format="'.$delivery_date_format.'" data-laundry_delivery_date_consider_disabled_days="'.$laundry_delivery_date_consider_disabled_days.'" data-laundry_delivery_date_consider_selected_pickup_date="'.$laundry_delivery_date_consider_selected_pickup_date.'" data-after_pickup_dates="'.$after_pickup_dates.'" value="' . $delivery_date . '">';
    	}

    	if($enable_pickup_date) {

    		$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : __("Pickup Date","coderockz-woo-delivery");

    		$pickup_days = isset($pickup_date_settings['pickup_days']) && $pickup_date_settings['pickup_days'] != "" ? $pickup_date_settings['pickup_days'] : "6,0,1,2,3,4,5";

    		$pickup_date_calendar_locale = (isset($pickup_date_settings['calendar_locale']) && !empty($pickup_date_settings['calendar_locale'])) ? $pickup_date_settings['calendar_locale'] : "default";
			$pickup_week_starts_from = (isset($pickup_date_settings['week_starts_from']) && !empty($pickup_date_settings['week_starts_from']))?$pickup_date_settings['week_starts_from']:"0";
		
			$pickup_selectable_date = (isset($pickup_date_settings['selectable_date']) && !empty($pickup_date_settings['selectable_date']))?$pickup_date_settings['selectable_date']:365;

			$pickup_selectable_date_until = (isset($pickup_date_settings['selectable_date_until']) && !empty($pickup_date_settings['selectable_date_until']))?$pickup_date_settings['selectable_date_until']:"no_date";

			$same_day_pickup = (isset($pickup_date_settings['disable_same_day_pickup']) && !empty($pickup_date_settings['disable_same_day_pickup'])) ? $pickup_date_settings['disable_same_day_pickup'] : false;

			$pickup_days = explode(',', $pickup_days);

			$week_days = ['0', '1', '2', '3', '4', '5', '6'];
			$pickup_disable_week_days = array_values(array_diff($week_days, $pickup_days));

			$off_days = (isset($pickup_date_settings['pickup_off_days']) && !empty($pickup_date_settings['pickup_off_days'])) ? $pickup_date_settings['pickup_off_days'] : array();
			$off_day_dates = [];
			$selectable_start_date = wp_date('Y-m-d H:i:s', current_time( 'timestamp', 1 ));
			$start_date = current_datetime($selectable_start_date);
			if(count($off_days)) {
				$date = $start_date;
				foreach ($off_days as $year => $months) {
					foreach($months as $month =>$days){
						$month_num = date_parse($month)['month'];
						if(strlen($month_num) == 1) {
							$month_num_final = "0".$month_num;
						} else {
							$month_num_final = $month_num;
						}
						$days = explode(',', $days);
						foreach($days as $day){
							$pickup_disable_dates[] = $year . "-" . $month_num_final . "-" .$day;
							$off_day_dates[] = $year . "-" . $month_num_final . "-" .$day;
						}
					}
				}
			}

			$selectable_start_date = wp_date('Y-m-d H:i:s', current_time( 'timestamp', 1 ));
			$start_date = current_datetime($selectable_start_date);
			$max_processing_days = $temp_max_processing_days_pickup;
			
			if($max_processing_days > 0) {
				if($consider_current_day && $max_processing_days > 0) {
					if(($consider_weekends && in_array($start_date->format("w"), $pickup_disable_week_days)) || ($consider_off_days && in_array($start_date->format("Y-m-d"), $off_day_dates))) {

					} else {
						$pickup_disable_dates[] = $start_date->format("Y-m-d");
						$max_processing_days = $max_processing_days - 1;
						$start_date = $start_date->modify("+1 day");
					}
				} else {
					if(($consider_weekends && in_array($start_date->format("w"), $pickup_disable_week_days)) || ($consider_off_days && in_array($start_date->format("Y-m-d"), $off_day_dates))) {

					} else {
						$pickup_disable_dates[] = $start_date->format("Y-m-d");
						$start_date = $start_date->modify("+1 day");
					}
				}

				while($max_processing_days > 0) {
					$date = $start_date;
					if($consider_weekends) {

						$pickup_disable_dates[] = $date->format("Y-m-d");
						$max_processing_days = $max_processing_days - 1;
						$start_date = $start_date->modify("+1 day");
					} else {
						if (!in_array($date->format("w"), $pickup_disable_week_days)) {
							$pickup_disable_dates[] = $date->format("Y-m-d");
							$max_processing_days = $max_processing_days - 1;
							$start_date = $start_date->modify("+1 day");
						} else {

							$pickup_disable_dates[] = $date->format("Y-m-d");
							$start_date = $start_date->modify("+1 day");

						}

					}

				}

			}

			$selectable_start_date_sec = wp_date('Y-m-d H:i:s', current_time( 'timestamp', 1 ));
			$start_date_sec = current_datetime($selectable_start_date_sec);
			$max_processing_days_sec = $temp_max_processing_days_pickup;
			
			if($max_processing_days_sec > 0) {

				if($consider_current_day && $max_processing_days_sec > 0) {
					if(($consider_weekends && in_array($start_date_sec->format("w"), $pickup_disable_week_days)) || ($consider_off_days && in_array($start_date_sec->format("Y-m-d"), $off_day_dates))) {

					} else {
						$pickup_disable_dates[] = $start_date_sec->format("Y-m-d");
						$max_processing_days_sec = $max_processing_days_sec - 1;
						$start_date_sec = $start_date_sec->modify("+1 day");
					}
				} else {
					if(($consider_weekends && in_array($start_date_sec->format("w"), $pickup_disable_week_days)) || ($consider_off_days && in_array($start_date_sec->format("Y-m-d"), $off_day_dates))) {

					} else {
						$pickup_disable_dates[] = $start_date_sec->format("Y-m-d");
						$start_date_sec = $start_date_sec->modify("+1 day");
					}
				}

				while($max_processing_days_sec > 0) {
					$date = $start_date_sec;
					if($consider_off_days) {
						
						$pickup_disable_dates[] = $date->format("Y-m-d");
						$max_processing_days_sec = $max_processing_days_sec - 1;
						$start_date_sec = $start_date_sec->modify("+1 day");

					} else {
						if (!in_array($date->format("Y-m-d"), $off_day_dates)) {
							$pickup_disable_dates[] = $date->format("Y-m-d");
							$max_processing_days_sec = $max_processing_days_sec - 1;
							$start_date_sec = $start_date_sec->modify("+1 day");
						} else {

							$pickup_disable_dates[] = $date->format("Y-m-d");
							$start_date_sec = $start_date_sec->modify("+1 day");

						}

					}

				}	

			}

			$enable_closing_time_pickup = (isset($pickup_time_settings['enable_closing_time']) && !empty($pickup_time_settings['enable_closing_time'])) ? $pickup_time_settings['enable_closing_time'] : false;

			$enable_different_closing_time = (isset($delivery_time_settings['enable_different_closing_time']) && !empty($delivery_time_settings['enable_different_closing_time'])) ? $delivery_time_settings['enable_different_closing_time'] : false;

			if($enable_closing_time_pickup) {
				$store_closing_time = isset($pickup_time_settings['store_closing_time']) ? (int)$pickup_time_settings['store_closing_time'] : "";

				$current_time = (wp_date("G")*60)+wp_date("i");

				$extended_closing_days = (isset($pickup_time_settings['extended_closing_days']) && !empty($pickup_time_settings['extended_closing_days'])) ? (int)$pickup_time_settings['extended_closing_days'] : 0;

				if(($store_closing_time != "" || $store_closing_time === 0) && ($current_time >= $store_closing_time)) {
					$extended_closing_time_pickup = (isset($pickup_time_settings['extended_closing_time']) && !empty($pickup_time_settings['extended_closing_time'])) ? (int)$pickup_time_settings['extended_closing_time'] : 0;
					
					if($extended_closing_time_pickup>=1440) {
						$x = 1440;
						$date = $today;
						$days_from_closing_time_pickup =1;
						while($extended_closing_time_pickup>=$x) {
							$second_time = $extended_closing_time_pickup - $x;
							$formated = wp_date('Y-m-d H:i:s', $this->helper->wp_strtotime($date));
							$formated_obj = current_datetime($formated);
							$closing_time_date_pickup = $formated_obj->modify("+".$days_from_closing_time_pickup." day")->format("Y-m-d");
							$last_closing_time_date_pickup = $closing_time_date_pickup;
							$pickup_disable_dates[] = $closing_time_date_pickup;
							$extended_closing_time_pickup = $second_time;
							$days_from_closing_time_pickup = $days_from_closing_time_pickup+1;
						}

						$formated_last_closing_pickup = wp_date('Y-m-d H:i:s', $this->helper->wp_strtotime($last_closing_time_date_pickup));
						$formated_obj_last_closing_pickup = current_datetime($formated_last_closing_pickup);
						$last_closing_time_date_pickup = $formated_obj_last_closing_pickup->modify("+1 day")->format("Y-m-d");
					}

					$extended_closing_last_date = current_datetime($today)->modify("+".$extended_closing_days." day")->format('Y-m-d');
					$extended_closing_dates =  $this->helper->get_date_from_range($today, $extended_closing_last_date);
					$pickup_disable_dates = array_merge($pickup_disable_dates, $extended_closing_dates);
				}
			} elseif($enable_different_closing_time) {
				$current_week_day = date("w"); 				
				$store_closing_time = isset($delivery_time_settings['different_store_closing_time'][$current_week_day]) ? (int)$delivery_time_settings['different_store_closing_time'][$current_week_day] : "";
				$current_time = (date("G")*60)+date("i");

				$extended_closing_days = (isset($delivery_time_settings['different_extended_closing_day'][$current_week_day]) && $delivery_time_settings['different_extended_closing_day'][$current_week_day] != "") ? (int)$delivery_time_settings['different_extended_closing_day'][$current_week_day] : 0;

				if(($store_closing_time != "" || $store_closing_time === 0) && ($current_time >= $store_closing_time)) {
					$extended_closing_time_pickup = (isset($delivery_time_settings['different_extended_closing_time'][$current_week_day]) && $delivery_time_settings['different_extended_closing_time'][$current_week_day] != "") ? (int)$delivery_time_settings['different_extended_closing_time'][$current_week_day] : 0;
					
					if($extended_closing_time_pickup>=1440) {
						$x = 1440;
						$date = $today;
						$days_from_closing_time_pickup =1;
						while($extended_closing_time_pickup>=$x) {
							$second_time = $extended_closing_time_pickup - $x;
							$formated = wp_date('Y-m-d H:i:s', $this->helper->wp_strtotime($date));
							$formated_obj = current_datetime($formated);
							$closing_time_date_pickup = $formated_obj->modify("+".$days_from_closing_time_pickup." day")->format("Y-m-d");
							$last_closing_time_date_pickup = $closing_time_date_pickup;
							$pickup_disable_dates[] = $closing_time_date_pickup;
							$extended_closing_time_pickup = $second_time;
							$days_from_closing_time_pickup = $days_from_closing_time_pickup+1;
						}

						$formated_last_closing_pickup = wp_date('Y-m-d H:i:s', $this->helper->wp_strtotime($last_closing_time_date_pickup));
						$formated_obj_last_closing_pickup = current_datetime($formated_last_closing_pickup);
						$last_closing_time_date_pickup = $formated_obj_last_closing_pickup->modify("+1 day")->format("Y-m-d");
					}

					$extended_closing_last_date = current_datetime($today)->modify("+".$extended_closing_days." day")->format('Y-m-d');
					$extended_closing_dates =  $this->helper->get_date_from_range($today, $extended_closing_last_date);
					$pickup_disable_dates = array_merge($pickup_disable_dates, $extended_closing_dates);
				}

			}

			if($same_day_pickup) {
				$pickup_disable_dates[] = $today;
			}

			$pickup_disable_dates = array_unique($pickup_disable_dates, false);
			$pickup_disable_dates = array_values($pickup_disable_dates);

			$pickup_disable_week_days = implode(",",$pickup_disable_week_days);
		    $pickup_disable_dates = implode("::",$pickup_disable_dates);

		    $pickup_open_days = (isset($pickup_date_settings['open_days']) && !empty($pickup_date_settings['open_days'])) ? $pickup_date_settings['open_days'] : array();
			$special_open_days_dates_pickup = [];
			$selectable_start_date = wp_date('Y-m-d H:i:s', current_time( 'timestamp', 1 ));
			$start_date = current_datetime($selectable_start_date);
			if(count($pickup_open_days)) {
				$date = $start_date;
				foreach ($pickup_open_days as $year => $months) {
					foreach($months as $month => $days){
						$month_num = date_parse($month)['month'];
						if(strlen($month_num) == 1) {
							$month_num_final = "0".$month_num;
						} else {
							$month_num_final = $month_num;
						}
						$days = explode(',', $days);
						foreach($days as $day){
							if($this->helper->wp_strtotime($year . "-" . $month_num_final . "-" .$day) + 86400 >= time())		
								$special_open_days_dates_pickup[] = $year . "-" . $month_num_final . "-" .$day;
						}
					}
				}
			}

			$overall_off_before_pickup = isset($pickup_date_settings['overall_off_before']) && $pickup_date_settings['overall_off_before'] != "" ? $pickup_date_settings['overall_off_before'] : 0;

			$special_open_days_dates_pickup = array_filter($special_open_days_dates_pickup,function($date) use ($overall_off_before_pickup){
			    return $this->helper->wp_strtotime($date) >= $this->helper->wp_strtotime('today') + (86400 * (int)$overall_off_before_pickup);
			});

			if($overall_off_before_pickup > 0 && (isset($special_open_days_dates_pickup) && !empty($special_open_days_dates_pickup))) {
				$today_date_for_off_before = wp_date('Y-m-d');
			
				$off_dates_for_off_before_pickup[] = $today_date_for_off_before;
				for($i =1; $i < $overall_off_before_pickup; $i++){
					$today_date_for_off_before = wp_date('Y-m-d', $this->helper->wp_strtotime('+1 day', $this->helper->wp_strtotime($today_date_for_off_before)));
					$off_dates_for_off_before_pickup[] = wp_date('Y-m-d', $this->helper->wp_strtotime($today_date_for_off_before));
				}
			}
		
			$special_open_days_dates_pickup = array_diff($special_open_days_dates_pickup,$off_dates_for_off_before_pickup);

			$special_open_days_dates_pickup = implode("::",$special_open_days_dates_pickup);
			$off_dates_for_off_before_pickup = implode("::",$off_dates_for_off_before_pickup);
			$off_day_dates_pickup = implode("::",$off_day_dates);

	        $meta_box .= '<input style="width:100%;margin:5px auto;display:none" type="text" id="coderockz_woo_delivery_meta_box_pickup_datepicker" placeholder="'.$pickup_date_field_label.'" name="coderockz_woo_delivery_meta_box_pickup_datepicker" data-pickup_selectable_dates_until="'.$pickup_selectable_date_until.'" data-special_open_days_dates_pickup="'.$special_open_days_dates_pickup.'" data-off_dates_for_off_before_pickup="'.$off_dates_for_off_before_pickup.'" data-same_day_pickup="'.$same_day_pickup.'" data-pickup_calendar_locale="'.$pickup_date_calendar_locale.'" data-pickup_disable_dates="'.$pickup_disable_dates.'" data-pickup_selectable_dates="'.$pickup_selectable_date.'" data-pickup_disable_week_days="'.$pickup_disable_week_days.'" data-off_day_dates_pickup="'.$off_day_dates_pickup.'" data-pickup_disable_week_days_category="'.$pickup_disable_week_days_category.'" data-special_open_days_categories="'.$special_open_days_pickup_categories.'" data-current_month_remaining_date="'.$current_month_remaining_date.'" data-pickup_detect_next_month_off_category="'.$detect_next_month_off_category.'" data-current_week_remaining_date_pickup="'.$current_week_remaining_date_pickup.'" data-detect_next_week_off_category_pickup="'.$detect_next_week_off_category_pickup.'" data-detect_current_week_off_category_pickup="'.$detect_current_week_off_category_pickup.'" data-pickup_disable_week_days_product="'.$disable_week_days_product.'" data-pickup_week_starts_from="'.$pickup_week_starts_from.'" data-pickup_date_format="'.$pickup_date_format.'" value="' . $pickup_date . '">';
    	}

    	if($enable_delivery_time) {
 
    		$overall_after_pickup_time = (isset($laundry_store_settings['overall_after_pickup_time']) && $laundry_store_settings['overall_after_pickup_time'] != "") ? (int)$laundry_store_settings['overall_after_pickup_time'] : 0;
    		$meta_box .= '<select style="width:100%;margin:5px auto;display:none" name ="coderockz_woo_delivery_meta_box_time_field" id="coderockz_woo_delivery_meta_box_time_field" data-overall_after_pickup_time="'.$overall_after_pickup_time.'" data-extended_closing_time="'.$extended_closing_time_delivery.'" data-last_closing_time_date_delivery="'.$last_closing_time_date_delivery.'" data-last_processing_time_date="'.$last_processing_time_date.'" data-order_limit_notice="'.$order_limit_notice.'" data-max_processing_time="'.$max_processing_time.'" data-disable_timeslot_with_processing_time="'.$disable_timeslot_with_processing_time.'">';
    		if($time == ""){
    			$meta_box .= '<option value="" selected>'.__("Select Delivery Time Slot","coderockz-woo-delivery").'</option>';
    		} else {
    			$meta_box .= '<option value="" disabled="disabled" selected>'.__("Select Delivery Time Slot","coderockz-woo-delivery").'</option>';
    		}
    		
    		foreach($time_options as $key => $value) {
    			$selected = ($key == $time) ? "selected" : "";
    			$meta_box .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
    		}
    		$meta_box .= '</select>';
    	}

    	$enable_delivery_tips = (isset($delivery_tips_settings['enable_delivery_tips']) && !empty($delivery_tips_settings['enable_delivery_tips'])) ? $delivery_tips_settings['enable_delivery_tips'] : false;

		if($enable_delivery_tips) {

			$delivery_tips_field_label = (isset($delivery_tips_settings['delivery_tips_field_label']) && !empty($delivery_tips_settings['delivery_tips_field_label'])) ? stripslashes($delivery_tips_settings['delivery_tips_field_label']) : __( "Tips to Delivery Person", 'coderockz-woo-delivery' );

			$enable_delivery_tips_dropdown = (isset($delivery_tips_settings['enable_delivery_tips_dropdown']) && !empty($delivery_tips_settings['enable_delivery_tips_dropdown'])) ? $delivery_tips_settings['enable_delivery_tips_dropdown'] : false;

			$currency_symbol = get_woocommerce_currency_symbol($order->get_currency());

			if($enable_delivery_tips_dropdown) {

				$delivery_tips_value = (isset($delivery_tips_settings['delivery_tips_dropdown_value']) && !empty($delivery_tips_settings['delivery_tips_dropdown_value'])) ? $delivery_tips_settings['delivery_tips_dropdown_value'] : [];

				$meta_box .= '<select style="width:100%;margin:5px auto;display:none" name ="coderockz_woo_delivery_meta_box_tips_field" id="coderockz_woo_delivery_meta_box_tips_field">';
	    		$meta_box .= '<option value="" selected>'.__($delivery_tips_field_label, 'coderockz-woo-delivery').'</option>';
	    		if($delivery_tips > 0) {
	    			$meta_box .= '<option value="'.$delivery_tips.'" selected>'.$currency_symbol.$delivery_tips.'</option>';
	    		}
	    		foreach($delivery_tips_value as $tips) {
	    				    			
	    			if(strpos($tips, '%') !== false) {

	    				$meta_box .= '<option value="'.$tips.'">'.$tips.'</option>';
						
					} else {

						$selected = ($tips == $delivery_tips) ? "selected" : "";

		    			if($selected == "") {
		    				$meta_box .= '<option value="'.$tips.'">'.$currency_symbol.$tips.'</option>';
		    			} elseif($selected != "") {
		    				$meta_box .= '<option value="'.$tips.'" '.$selected.'>'.$currency_symbol.$tips.'</option>';
		    			}
					}
	    			
    				
	    		}
	    		$meta_box .= '</select>';

			} else {
				$meta_box .= '<input value="'.$delivery_tips.'" style="width:100%;margin:5px auto;display:none" type="text" id="coderockz_woo_delivery_meta_box_tips_field" placeholder="'.__($delivery_tips_field_label, 'coderockz-woo-delivery').'" name="coderockz_woo_delivery_meta_box_tips_field">';
			}

		}

    	if($enable_pickup_time) {
    		$meta_box .= '<select style="width:100%;margin:5px auto;display:none" name ="coderockz_woo_delivery_meta_box_pickup_field" id="coderockz_woo_delivery_meta_box_pickup_field" data-extended_closing_time="'.$extended_closing_time_pickup.'" data-last_closing_time_date_pickup="'.$last_closing_time_date_pickup.'" data-last_processing_time_date="'.$last_processing_time_date_pickup.'" data-pickup_limit_notice="'.$pickup_limit_notice.'" data-max_processing_time="'.$max_processing_time_pickup.'" data-disable_timeslot_with_processing_time="'.$disable_timeslot_with_processing_time.'">';
    		if($pickup_time == ""){
    			$meta_box .= '<option value="" selected>'.__("Select Pickup Time Slot","coderockz-woo-delivery").'</option>';
    		} else {
    			$meta_box .= '<option value="" disabled="disabled" selected>'.__("Select Pickup Time Slot","coderockz-woo-delivery").'</option>';
    		}

    		foreach($pickup_options as $key => $value) {
    			$selected = ($key == $pickup_time) ? "selected" : "";
    			$meta_box .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
    		}
    		$meta_box .= '</select>';
    	}

    	if($enable_pickup_location) {
    		$meta_box .= '<select style="width:100%;margin:5px auto;display:none" name="coderockz_woo_delivery_pickup_location_field" id="coderockz_woo_delivery_pickup_location_field" data-pickup_location_limit_notice="'.$pickup_location_limit_notice.'">';

    		$meta_box .= '<option value="" disabled="disabled" selected>'.__("Select Pickup Location","coderockz-woo-delivery").'</option>';

    		foreach($pickup_location_options as $key => $value) {
    			$selected = (remove_accents($key) == remove_accents($location)) ? "selected" : "";
    			$meta_box .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
    		}
    		$meta_box .= '</select>';
    	}
    	if($enable_additional_field) {
    		$meta_box .= '<div name="coderockz-woo-delivery-metabox-additional-field" id="coderockz-woo-delivery-metabox-additional-field" style="display:none"><textarea style="width:100%;margin:5px auto;" maxlength="'.$additional_field_character_limit.'" data-character_limit="'.$additional_field_character_limit.'" data-character_remaining_text="'.$character_remaining_text.'" id="coderockz_woo_delivery_meta_box_additional_field_field" placeholder="'.$additional_field_label.'">'.$special_note.'</textarea></div>';
    	}

    	if(isset($has_other_method) && count($has_other_method) > 1) {
    		$meta_box .= '<select style="width:100%;margin:5px auto;display:none" name ="coderockz_woo_delivery_meta_box_shipping_method" id="coderockz_woo_delivery_meta_box_shipping_method">';
    		$meta_box .= '<option value="">Select Shipping Method</option>';
    		foreach($has_other_method as $method) {
    			if($method == $order->get_shipping_method()) {
    				$meta_box .= '<option value="'.$method.'" selected="selected">'.$method.'</option>';
    			} else {
    				$meta_box .= '<option value="'.$method.'">'.$method.'</option>';
    			}
    		}

    		$meta_box .= '</select>';

		} 

		if(isset($has_local_pickup_method) && count($has_local_pickup_method) > 1) {
    		$meta_box .= '<select style="width:100%;margin:5px auto;display:none" name ="coderockz_woo_delivery_meta_box_shipping_method_pickup" id="coderockz_woo_delivery_meta_box_shipping_method_pickup">';
    		$meta_box .= '<option value="">Select Shipping Method</option>';
    		foreach($has_local_pickup_method as $method) {
    			if($method == $order->get_shipping_method()) {
    				$meta_box .= '<option value="'.$method.'" selected="selected">'.$method.'</option>';
    			} else {
    				$meta_box .= '<option value="'.$method.'">'.$method.'</option>';
    			}
    		}

    		$meta_box .= '</select>';

		}

    	$meta_box .= '</div>';

    	$meta_box .= '<div class="coderockz-woo-delivery-metabox-update-section" data-plugin-url="'.CODEROCKZ_WOO_DELIVERY_URL.'">';
        $meta_box .= '<a class="coderockz-woo-delivery-metabox-update-btn" href="#" style="margin-right:10px"><button type="button" class="button button-primary">'.__("Update","coderockz-woo-delivery").'</button></a>';
        $meta_box .= '<a class="coderockz-woo-delivery-metabox-update-btn" data-notify="yes" href="#" style="margin-right:10px"><button type="button" class="button button-primary">'.__("Update & Notify","coderockz-woo-delivery").'</button></a>';
        if(!$remove_delivery_status_column) {

        if(metadata_exists('post', $order_id, 'delivery_type') || $order->meta_exists('delivery_type')) {
			if(get_post_meta($order_id, 'delivery_status', true) == "delivered" || $order->get_meta( 'delivery_status', true ) == "delivered") {
				$meta_box .= '<a class="coderockz-woo-delivery-metabox-delivery-complete-btn" href="#"><button type="button" class="button" disabled>'.$delivery_complete_btn_text.' '.__('Completed','coderockz-woo-delivery').'</button></a>';
			} else {
				$meta_box .= '<a class="coderockz-woo-delivery-metabox-delivery-complete-btn" href="#"><button type="button" class="button button-secondary">'.__('Mark','coderockz-woo-delivery').' '.$delivery_complete_btn_text.' '.__('As Completed','coderockz-woo-delivery').'</button></a>';
			}
		} else {
			if(get_post_meta($order_id, 'delivery_status', true) == "delivered" || $order->get_meta( 'delivery_status', true ) == "delivered") {
				$meta_box .= '<a class="coderockz-woo-delivery-metabox-delivery-complete-btn" href="#"><button type="button" class="button" disabled>'.$delivery_complete_btn_text.' '.__('Completed','coderockz-woo-delivery').'</button></a>';
			} else {
				$meta_box .= '<a class="coderockz-woo-delivery-metabox-delivery-complete-btn" href="#"><button type="button" class="button button-secondary">'.__('Mark','coderockz-woo-delivery').' '.$delivery_complete_btn_text.' '.__('As Completed','coderockz-woo-delivery').'</button></a>';
			}
		}

		}
        
        $meta_box .= '</div>';
        echo $meta_box;

        }
        
	}

	public function coderockz_woo_delivery_save_delivery_pickup_details( $order_id ){

		global $pagenow;
		if(((get_post_type() == 'shop_order' || (function_exists( 'wc_get_page_screen_id' ) && wc_get_page_screen_id( 'shop-order' ) === 'woocommerce_page_wc-orders')) && isset($_GET['action'])  && $_GET['action'] === 'edit' ) || ((get_post_type() == 'shop_order' || (function_exists( 'wc_get_page_screen_id' ) && wc_get_page_screen_id( 'shop-order' ) === 'woocommerce_page_wc-orders')) && ($pagenow === 'post-new.php' || (isset($_GET['action'])  && $_GET['action'] === 'new')))) {
	
			$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');

			$order = wc_get_order( $order_id );

			$date_timestamp = null;
			$time_timestamp_start = null;
			$time_timestamp_end = null;
			$for_time_timestamp = null;

			if(isset($_POST['coderockz_woo_delivery_meta_box_delivery_selection_field']) && $_POST['coderockz_woo_delivery_meta_box_delivery_selection_field'] == "delivery") {
				
				if($this->hpos) {
					$order->delete_meta_data( 'pickup_date');
					$order->delete_meta_data( 'pickup_time' );
					$order->delete_meta_data( 'pickup_location' );
					$order->update_meta_data( 'delivery_type', 'delivery' );
				} else {
					delete_post_meta($order_id, 'pickup_date');
					delete_post_meta($order_id, 'pickup_time');
					delete_post_meta($order_id, 'pickup_location');
					update_post_meta( $order_id, 'delivery_type', 'delivery' );
				}

				if(isset($_POST['coderockz_woo_delivery_meta_box_tips_field'])) {
					$selected_delivery_tips = $_POST['coderockz_woo_delivery_meta_box_tips_field'];
				} else {
					$selected_delivery_tips = "";
				}
				
				if(isset($_POST['coderockz_woo_delivery_meta_box_datepicker'])) {
					$en_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['coderockz_woo_delivery_meta_box_datepicker']),"delivery"),"delivery");
					
					if($this->hpos) {
						$order->update_meta_data( 'delivery_date', date("Y-m-d", strtotime($en_date)) );
					} else {
						update_post_meta( $order_id, 'delivery_date', date("Y-m-d", strtotime($en_date)) );
					}
					$date_timestamp = strtotime($en_date);
				} else {
					if($this->hpos) {
						$order->delete_meta_data( 'delivery_date' );
					} else {
						delete_post_meta($order_id, 'delivery_date');
					}
				}

				if(isset($_POST['coderockz_woo_delivery_meta_box_time_field'])) {
					$time = sanitize_text_field($_POST['coderockz_woo_delivery_meta_box_time_field']);
					if($time == "conditional-delivery") {

						$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
						$conditional_time = date("H:i", mktime(0, (int)((wp_date("G")*60)+wp_date("i")))) . " - ".date("H:i", mktime(0, (int)((wp_date("G")*60)+wp_date("i") + $delivery_fee_settings['conditional_delivery_fee_duration'])));
						if($this->hpos) {
							$order->update_meta_data( 'delivery_time', $conditional_time );
						} else {
							update_post_meta( $order_id, 'delivery_time', $conditional_time );
						}

						$for_time_timestamp = $conditional_time;
					} else {
						if($this->hpos) {
							$order->update_meta_data( 'delivery_time', $time );
						} else {
							update_post_meta( $order_id, 'delivery_time', $time );
						}
						
						$for_time_timestamp = $time;
					}
										
				} else {
					
					if($this->hpos) {
						$order->delete_meta_data( 'delivery_time' );
					} else {
						delete_post_meta($order_id, 'delivery_time');
					}
				}

				if(isset($_POST['coderockz_woo_delivery_meta_box_time_field']) && $_POST['coderockz_woo_delivery_meta_box_time_field'] !="as-soon-as-possible" && $_POST['coderockz_woo_delivery_meta_box_time_field'] != "" && !is_null($for_time_timestamp)) {
				$minutes = $for_time_timestamp;

		    	$slot_key = explode(' - ', $minutes);
				$slot_key_one = explode(':', $slot_key[0]);
				$time_timestamp_start = ((int)$slot_key_one[0]*60*60+(int)$slot_key_one[1]*60);

		    	if(!isset($slot_key[1])) {
		    		$time_timestamp_end = 0;
		    	} else {
		    		$slot_key_two = explode(':', $slot_key[1]);
		    		$time_timestamp_end = ((int)$slot_key_two[0]*60*60+(int)$slot_key_two[1]*60);
		    	}
			}

			} elseif(isset($_POST['coderockz_woo_delivery_meta_box_delivery_selection_field']) && $_POST['coderockz_woo_delivery_meta_box_delivery_selection_field'] == "pickup") {
				
				if($this->hpos) {
					$order->delete_meta_data( 'delivery_date');
					$order->delete_meta_data( 'delivery_time' );
					$order->update_meta_data( 'delivery_type', 'pickup' );
				} else {
					delete_post_meta($order_id, 'delivery_date');
					delete_post_meta($order_id, 'delivery_time');
					update_post_meta( $order_id, 'delivery_type', 'pickup' );
				}

				if(isset($_POST['coderockz_woo_delivery_meta_box_pickup_datepicker'])) {
					$en_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['coderockz_woo_delivery_meta_box_pickup_datepicker']),"pickup"),"pickup");
					if($this->hpos) {
						$order->update_meta_data( 'pickup_date', date("Y-m-d", strtotime($en_date)) );
					} else {
						update_post_meta( $order_id, 'pickup_date', date("Y-m-d", strtotime($en_date)) );
					}

					$date_timestamp = strtotime($en_date);
				} else {
					
					if($this->hpos) {
						$order->delete_meta_data( 'pickup_date' );
					} else {
						delete_post_meta($order_id, 'pickup_date');
					}
				}

				if(isset($_POST['coderockz_woo_delivery_meta_box_pickup_field'])) {
					$pickup_time = sanitize_text_field($_POST['coderockz_woo_delivery_meta_box_pickup_field']);
					if($this->hpos) {
						$order->update_meta_data( 'pickup_time', $pickup_time );
					} else {
						update_post_meta( $order_id, 'pickup_time', $pickup_time );
					}
					
					$for_time_timestamp = $_POST['coderockz_woo_delivery_meta_box_pickup_field'];
					
				} else {
					if($this->hpos) {
						$order->delete_meta_data( 'pickup_time' );
					} else {
						delete_post_meta($order_id, 'pickup_time');
					}
				}
				if(isset($_POST['coderockz_woo_delivery_pickup_location_field'])) {
					$pickup = sanitize_text_field($_POST['coderockz_woo_delivery_pickup_location_field']);
					if($this->hpos) {
						$order->update_meta_data( 'pickup_location', $pickup );
					} else {
						update_post_meta( $order_id, 'pickup_location', $pickup );
					}
					
				} else {
					if($this->hpos) {
						$order->delete_meta_data( 'pickup_location' );
					} else {
						delete_post_meta($order_id, 'pickup_location');
					}
				}

				if(isset($_POST['coderockz_woo_delivery_meta_box_pickup_field']) && $_POST['coderockz_woo_delivery_meta_box_pickup_field'] != "" && !is_null($for_time_timestamp)) {
					$minutes = $for_time_timestamp;

			    	$slot_key = explode(' - ', $minutes);
					$slot_key_one = explode(':', $slot_key[0]);
					$time_timestamp_start = ((int)$slot_key_one[0]*60*60+(int)$slot_key_one[1]*60);

			    	if(!isset($slot_key[1])) {
			    		$time_timestamp_end = 0;
			    	} else {
			    		$slot_key_two = explode(':', $slot_key[1]);
			    		$time_timestamp_end = ((int)$slot_key_two[0]*60*60+(int)$slot_key_two[1]*60);
			    	}
				}

			} else {
				
				if((isset($_POST['coderockz_woo_delivery_meta_box_datepicker']) || isset($_POST['coderockz_woo_delivery_meta_box_time_field'])) && (!isset($_POST['coderockz_woo_delivery_meta_box_pickup_datepicker']) && !isset($_POST['coderockz_woo_delivery_meta_box_pickup_field']))) {
					
					if($this->hpos) {
						$order->update_meta_data( 'delivery_type', 'delivery' );
					} else {
						update_post_meta( $order_id, 'delivery_type', 'delivery' );
					}
				} elseif((isset($_POST['coderockz_woo_delivery_meta_box_pickup_datepicker']) || isset($_POST['coderockz_woo_delivery_meta_box_pickup_field'])) && (!isset($_POST['coderockz_woo_delivery_meta_box_datepicker']) && !isset($_POST['coderockz_woo_delivery_meta_box_time_field']))) {
					
					if($this->hpos) {
						$order->update_meta_data( 'delivery_type', 'pickup' );
					} else {
						update_post_meta( $order_id, 'delivery_type', 'pickup' );
					}
				} else {
					
					if($this->hpos) {
						$order->update_meta_data( 'delivery_type', 'delivery' );
					} else {
						update_post_meta( $order_id, 'delivery_type', 'delivery' );
					}
				}
				if(isset($_POST['coderockz_woo_delivery_meta_box_datepicker']) && !isset($_POST['coderockz_woo_delivery_meta_box_pickup_datepicker'])) {
					$en_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['coderockz_woo_delivery_meta_box_datepicker']),"delivery"),"delivery");
					
					if($this->hpos) {
						$order->update_meta_data( 'delivery_date', date("Y-m-d", strtotime($en_date)) );
					} else {
						update_post_meta( $order_id, 'delivery_date', date("Y-m-d", strtotime($en_date)) );
					}
					$date_timestamp = strtotime($en_date);
				} elseif(isset($_POST['coderockz_woo_delivery_meta_box_datepicker']) && $_POST['coderockz_woo_delivery_meta_box_datepicker'] != "") {
					$en_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['coderockz_woo_delivery_meta_box_datepicker']),"delivery"),"delivery");
					
					if($this->hpos) {
						$order->update_meta_data( 'delivery_date', date("Y-m-d", strtotime($en_date)) );
					} else {
						update_post_meta( $order_id, 'delivery_date', date("Y-m-d", strtotime($en_date)) );
					}
					$date_timestamp = strtotime($en_date);
				} else {
					if($this->hpos) {
						$order->delete_meta_data( 'delivery_date' );
					} else {
						delete_post_meta($order_id, 'delivery_date');
					}
				}

				if(isset($_POST['coderockz_woo_delivery_meta_box_time_field']) && !isset($_POST['coderockz_woo_delivery_meta_box_pickup_field'])) {
					$time = sanitize_text_field($_POST['coderockz_woo_delivery_meta_box_time_field']);
					
					if($this->hpos) {
						$order->update_meta_data( 'delivery_time', $time );
					} else {
						update_post_meta( $order_id, 'delivery_time', $time );
					}
					$for_time_timestamp = $_POST['coderockz_woo_delivery_meta_box_time_field'];
				} elseif(isset($_POST['coderockz_woo_delivery_meta_box_time_field'])) {
					$time = sanitize_text_field($_POST['coderockz_woo_delivery_meta_box_time_field']);
					
					if($this->hpos) {
						$order->update_meta_data( 'delivery_time', $time );
					} else {
						update_post_meta( $order_id, 'delivery_time', $time );
					}
					$for_time_timestamp = $_POST['coderockz_woo_delivery_meta_box_time_field'];
				} else {
					if($this->hpos) {
						$order->delete_meta_data( 'delivery_time' );
					} else {
						delete_post_meta($order_id, 'delivery_time');
					}
				}

				if(isset($_POST['coderockz_woo_delivery_meta_box_pickup_datepicker']) && !isset($_POST['coderockz_woo_delivery_meta_box_datepicker'])) {
					$en_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['coderockz_woo_delivery_meta_box_pickup_datepicker']),"pickup"),"pickup");
					
					if($this->hpos) {
						$order->update_meta_data( 'pickup_date', date("Y-m-d", strtotime($en_date)) );
					} else {
						update_post_meta( $order_id, 'pickup_date', date("Y-m-d", strtotime($en_date)) );
					}
					$date_timestamp = strtotime($en_date);
				} elseif(isset($_POST['coderockz_woo_delivery_meta_box_pickup_datepicker']) && $_POST['coderockz_woo_delivery_meta_box_pickup_datepicker'] != "") {
					$en_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['coderockz_woo_delivery_meta_box_pickup_datepicker']),"pickup"),"pickup");
					
					if($this->hpos) {
						$order->update_meta_data( 'pickup_date', date("Y-m-d", strtotime($en_date)) );
					} else {
						update_post_meta( $order_id, 'pickup_date', date("Y-m-d", strtotime($en_date)) );
					}
				} else {
					if($this->hpos) {
						$order->delete_meta_data( 'pickup_date' );
					} else {
						delete_post_meta($order_id, 'pickup_date');
					}
				}

				if(isset($_POST['coderockz_woo_delivery_meta_box_pickup_field']) && !isset($_POST['coderockz_woo_delivery_meta_box_time_field'])) {
					$pickup_time = sanitize_text_field($_POST['coderockz_woo_delivery_meta_box_pickup_field']);
					
					if($this->hpos) {
						$order->update_meta_data( 'pickup_time', $pickup_time );
					} else {
						update_post_meta( $order_id, 'pickup_time', $pickup_time );
					}
					$for_time_timestamp = $_POST['coderockz_woo_delivery_meta_box_pickup_field'];

				} elseif(isset($_POST['coderockz_woo_delivery_meta_box_pickup_field'])) {
					$pickup_time = sanitize_text_field($_POST['coderockz_woo_delivery_meta_box_pickup_field']);
					
					if($this->hpos) {
						$order->update_meta_data( 'pickup_time', $pickup_time );
					} else {
						update_post_meta( $order_id, 'pickup_time', $pickup_time );
					}
				} else {
					if($this->hpos) {
						$order->delete_meta_data( 'pickup_time' );
					} else {
						delete_post_meta($order_id, 'pickup_time');
					}
				}

				if(isset($_POST['coderockz_woo_delivery_pickup_location_field'])) {
					$pickup = sanitize_text_field($_POST['coderockz_woo_delivery_pickup_location_field']);
					
					if($this->hpos) {
						$order->update_meta_data( 'pickup_location', $pickup );
					} else {
						update_post_meta( $order_id, 'pickup_location', $pickup );
					}
				} else {
					if($this->hpos) {
						$order->delete_meta_data( 'pickup_location' );
					} else {
						delete_post_meta($order_id, 'pickup_location');
					}
				}

				if(!is_null($for_time_timestamp)) {
					$minutes = $for_time_timestamp;

			    	$slot_key = explode(' - ', $minutes);
					$slot_key_one = explode(':', $slot_key[0]);
					$time_timestamp_start = ((int)$slot_key_one[0]*60*60+(int)$slot_key_one[1]*60);

			    	if(!isset($slot_key[1])) {
			    		$time_timestamp_end = 0;
			    	} else {
			    		$slot_key_two = explode(':', $slot_key[1]);
			    		$time_timestamp_end = ((int)$slot_key_two[0]*60*60+(int)$slot_key_two[1]*60);
			    	}
				} 
			}
			
			if(isset($_POST['coderockz-woo-delivery-metabox-additional-field'])) {
				$additional = sanitize_textarea_field($_POST['coderockz-woo-delivery-metabox-additional-field']);
				if($this->hpos) {
					$order->update_meta_data( 'additional_note', $additional );
				} else {
					update_post_meta( $order_id, 'additional_note', $additional );
				}
			} else {
				if($this->hpos) {
					$order->delete_meta_data( 'additional_note' );
				} else {
					delete_post_meta($order_id, 'additional_note');
				}
			}

			$delivery_details_in_timestamp = 0;
			if(!is_null($date_timestamp)) {
				$delivery_details_in_timestamp = $delivery_details_in_timestamp+(int)$date_timestamp;
			}
			if(!is_null($time_timestamp_start)) {
				$delivery_details_in_timestamp = $delivery_details_in_timestamp+(int)$time_timestamp_start;
			}

			if(!is_null($time_timestamp_end)) {
				$delivery_details_in_timestamp = $delivery_details_in_timestamp+(int)$time_timestamp_end;
			}

			if($delivery_details_in_timestamp != 0) {
				
				if($this->hpos) {
					$order->update_meta_data( 'delivery_details_timestamp', $delivery_details_in_timestamp);
				} else {
					update_post_meta($order_id, 'delivery_details_timestamp', $delivery_details_in_timestamp);
				}
			}
			

			if($this->hpos) {
		  		$order->save();
			}

		}
		
	}

	public function coderockz_woo_delivery_save_meta_box_information() {
		check_ajax_referer('coderockz_woo_delivery_nonce');
		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');

		$order_id = sanitize_text_field($_POST['orderId']);
		$order = wc_get_order( $order_id );

		if((metadata_exists('post', $order_id, 'delivery_type') && get_post_meta( $order_id, 'delivery_type', true ) != "") || ($order->meta_exists('delivery_type') && $order->get_meta( 'delivery_type', true ) != "")) {
            if($this->hpos) {
				$old_delivery_type = $order->get_meta( 'delivery_type', true );
			} else {
				$old_delivery_type = get_post_meta( $order_id, 'delivery_type', true );
			}
        }

        if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {
            if($this->hpos) {
				$old_delivery_date = $order->get_meta( 'delivery_date', true );
			} else {
				$old_delivery_date = get_post_meta( $order_id, 'delivery_date', true );
			}
        } else {
        	$old_delivery_date = "";
        }

        if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {
            if($this->hpos) {
				$old_pickup_date = $order->get_meta( 'pickup_date', true );
			} else {
				$old_pickup_date = get_post_meta( $order_id, 'pickup_date', true );
			}
        } else {
        	$old_pickup_date = "";
        }

		$date_timestamp = null;
		$time_timestamp_start = null;
		$time_timestamp_end = null;
		$for_time_timestamp = null;

		if(isset($_POST['deliveryOption']) && $_POST['deliveryOption'] == "delivery") {
			
			if($this->hpos) {
				$order->delete_meta_data( 'pickup_date');
				$order->delete_meta_data( 'pickup_time' );
				$order->delete_meta_data( 'pickup_location' );
				$order->update_meta_data( 'delivery_type', 'delivery' );
			} else {
				delete_post_meta($order_id, 'pickup_date');
				delete_post_meta($order_id, 'pickup_time');
				delete_post_meta($order_id, 'pickup_location');
				update_post_meta( $order_id, 'delivery_type', 'delivery' );
			}

			if(isset($_POST['tips'])) {
				$selected_delivery_tips = $_POST['tips'];
			} else {
				$selected_delivery_tips = "";
			}
			
			if(isset($_POST['date'])) {
				$en_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['date']),"delivery"),"delivery");
				
				if($this->hpos) {
					$order->update_meta_data( 'delivery_date', date("Y-m-d", strtotime($en_date)) );
				} else {
					update_post_meta( $order_id, 'delivery_date', date("Y-m-d", strtotime($en_date)) );
				}
				if(!in_array($order->get_status(), ['cancelled','failed','refunded'])) {
					
					if(get_option('coderockz_woo_delivery_max_per_day_count') !== false) {
						$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');
						if(((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && date("Y-m-d", strtotime($en_date)) != $old_delivery_date) {
							if(isset($max_per_day_count['delivery']['order']) && array_key_exists(date("Y-m-d", strtotime($en_date)), $max_per_day_count['delivery']['order'])) {
						    	if(isset($max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))]) && ($max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))]!= '' || $max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))]>=0)) {
						    		$max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))] = $max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))] + 1;
						    	} else {
						    		$max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))] = 1;
						    	}
						    } else {
						    	$max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))] = 1;
						    }

						}

					    if(((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && $old_delivery_type != $_POST['deliveryOption'] && $old_pickup_date != "") {
							if(isset($max_per_day_count['pickup']['order']) && array_key_exists($old_pickup_date, $max_per_day_count['pickup']['order'])) {
						    	if(isset($max_per_day_count['pickup']['order'][$old_pickup_date]) && ($max_per_day_count['pickup']['order'][$old_pickup_date]!= '' || $max_per_day_count['pickup']['order'][$old_pickup_date]>0)) {
						    		$max_per_day_count['pickup']['order'][$old_pickup_date] = $max_per_day_count['pickup']['order'][$old_pickup_date] - 1;
						    	}
						    }
						} elseif(((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && $old_delivery_type == $_POST['deliveryOption'] && $old_delivery_date != "" && date("Y-m-d", strtotime($en_date)) != $old_delivery_date) {
							if(isset($max_per_day_count['delivery']['order']) && array_key_exists($old_delivery_date, $max_per_day_count['delivery']['order'])) {
						    	if(isset($max_per_day_count['delivery']['order'][$old_delivery_date]) && ($max_per_day_count['delivery']['order'][$old_delivery_date]!= '' || $max_per_day_count['delivery']['order'][$old_delivery_date]>0)) {
						    		$max_per_day_count['delivery']['order'][$old_delivery_date] = $max_per_day_count['delivery']['order'][$old_delivery_date] - 1;
						    	}
						    }
						}

					    $max_per_day_count = array_merge(get_option('coderockz_woo_delivery_max_per_day_count'),$max_per_day_count);
						update_option('coderockz_woo_delivery_max_per_day_count', $max_per_day_count);
					}
				}

				$date_timestamp = strtotime($en_date);
			} else {
				if($this->hpos) {
					$order->delete_meta_data( 'delivery_date' );
				} else {
					delete_post_meta($order_id, 'delivery_date');
				}
			}

			if(isset($_POST['time'])) {
				$time = sanitize_text_field($_POST['time']);
				if($time == "conditional-delivery") {

					$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
					$conditional_time = date("H:i", mktime(0, (int)((wp_date("G")*60)+wp_date("i")))) . " - ".date("H:i", mktime(0, (int)((wp_date("G")*60)+wp_date("i") + $delivery_fee_settings['conditional_delivery_fee_duration'])));
					if($this->hpos) {
						$order->update_meta_data( 'delivery_time', $conditional_time );
					} else {
						update_post_meta( $order_id, 'delivery_time', $conditional_time );
					}

					$for_time_timestamp = $conditional_time;
				} else {
					if($this->hpos) {
						$order->update_meta_data( 'delivery_time', $time );
					} else {
						update_post_meta( $order_id, 'delivery_time', $time );
					}
					
					$for_time_timestamp = $time;
				}				
				
			} else {
				
				if($this->hpos) {
					$order->delete_meta_data( 'delivery_time' );
				} else {
					delete_post_meta($order_id, 'delivery_time');
				}
			}

			if(isset($_POST['time']) && $_POST['time'] !="as-soon-as-possible" && $_POST['time'] != "" && !is_null($for_time_timestamp)) {
			$minutes = $for_time_timestamp;

		    	$slot_key = explode(' - ', $minutes);
				$slot_key_one = explode(':', $slot_key[0]);
				$time_timestamp_start = ((int)$slot_key_one[0]*60*60+(int)$slot_key_one[1]*60);

		    	if(!isset($slot_key[1])) {
		    		$time_timestamp_end = 0;
		    	} else {
		    		$slot_key_two = explode(':', $slot_key[1]);
		    		$time_timestamp_end = ((int)$slot_key_two[0]*60*60+(int)$slot_key_two[1]*60);
		    	}
			}

		} elseif(isset($_POST['deliveryOption']) && $_POST['deliveryOption'] == "pickup") {
			
			if($this->hpos) {
				$order->delete_meta_data( 'delivery_date');
				$order->delete_meta_data( 'delivery_time' );
				$order->update_meta_data( 'delivery_type', 'pickup' );
			} else {
				delete_post_meta($order_id, 'delivery_date');
				delete_post_meta($order_id, 'delivery_time');
				update_post_meta( $order_id, 'delivery_type', 'pickup' );
			}

			if(isset($_POST['pickupDate'])) {
				$en_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['pickupDate']),"pickup"),"pickup");
				if($this->hpos) {
					$order->update_meta_data( 'pickup_date', date("Y-m-d", strtotime($en_date)) );
				} else {
					update_post_meta( $order_id, 'pickup_date', date("Y-m-d", strtotime($en_date)) );
				}

				if(!in_array($order->get_status(), ['cancelled','failed','refunded'])) {
					
					if(get_option('coderockz_woo_delivery_max_per_day_count') !== false) {
						$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');

						if(((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && date("Y-m-d", strtotime($en_date)) != $old_pickup_date) {
							if(isset($max_per_day_count['pickup']['order']) && array_key_exists(date("Y-m-d", strtotime($en_date)), $max_per_day_count['pickup']['order'])) {
						    	if(isset($max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))]) && ($max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))]!= '' || $max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))] >=0)) {
						    		$max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))] = $max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))] + 1;
						    	} else {
						    		$max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))] = 1;
						    	}
						    } else {
						    	$max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))] = 1;
						    }

						}

						if(((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && $old_delivery_type != $_POST['deliveryOption'] && $old_delivery_date != "") {
							if(isset($max_per_day_count['delivery']['order']) && array_key_exists($old_delivery_date, $max_per_day_count['delivery']['order'])) {
						    	if(isset($max_per_day_count['delivery']['order'][$old_delivery_date]) && ($max_per_day_count['delivery']['order'][$old_delivery_date]!= '' || $max_per_day_count['delivery']['order'][$old_delivery_date]>0)) {
						    		$max_per_day_count['delivery']['order'][$old_delivery_date] = $max_per_day_count['delivery']['order'][$old_delivery_date] - 1;
						    	}
						    }
						} elseif(((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && $old_delivery_type == $_POST['deliveryOption'] && $old_pickup_date != "" && date("Y-m-d", strtotime($en_date)) != $old_pickup_date) {
							if(isset($max_per_day_count['pickup']['order']) && array_key_exists($old_pickup_date, $max_per_day_count['pickup']['order'])) {
						    	if(isset($max_per_day_count['pickup']['order'][$old_pickup_date]) && ($max_per_day_count['pickup']['order'][$old_pickup_date]!= '' || $max_per_day_count['pickup']['order'][$old_pickup_date]>0)) {
						    		$max_per_day_count['pickup']['order'][$old_pickup_date] = $max_per_day_count['pickup']['order'][$old_pickup_date] - 1;
						    	}
						    }
						}

					    $max_per_day_count = array_merge(get_option('coderockz_woo_delivery_max_per_day_count'),$max_per_day_count);
						update_option('coderockz_woo_delivery_max_per_day_count', $max_per_day_count);
					}
				}

				$date_timestamp = strtotime($en_date);
			} else {
				
				if($this->hpos) {
					$order->delete_meta_data( 'pickup_date' );
				} else {
					delete_post_meta($order_id, 'pickup_date');
				}
			}

			if(isset($_POST['pickupTime'])) {
				$pickup_time = sanitize_text_field($_POST['pickupTime']);
				if($this->hpos) {
					$order->update_meta_data( 'pickup_time', $pickup_time );
				} else {
					update_post_meta( $order_id, 'pickup_time', $pickup_time );
				}
				
				$for_time_timestamp = $_POST['pickupTime'];
				
			} else {
				if($this->hpos) {
					$order->delete_meta_data( 'pickup_time' );
				} else {
					delete_post_meta($order_id, 'pickup_time');
				}
			}
			if(isset($_POST['pickup'])) {
				$pickup = sanitize_text_field($_POST['pickup']);
				if($this->hpos) {
					$order->update_meta_data( 'pickup_location', $pickup );
				} else {
					update_post_meta( $order_id, 'pickup_location', $pickup );
				}
				
			} else {
				if($this->hpos) {
					$order->delete_meta_data( 'pickup_location' );
				} else {
					delete_post_meta($order_id, 'pickup_location');
				}
			}

			if(isset($_POST['pickupTime']) && $_POST['pickupTime'] != "" && !is_null($for_time_timestamp)) {
				$minutes = $for_time_timestamp;

		    	$slot_key = explode(' - ', $minutes);
				$slot_key_one = explode(':', $slot_key[0]);
				$time_timestamp_start = ((int)$slot_key_one[0]*60*60+(int)$slot_key_one[1]*60);

		    	if(!isset($slot_key[1])) {
		    		$time_timestamp_end = 0;
		    	} else {
		    		$slot_key_two = explode(':', $slot_key[1]);
		    		$time_timestamp_end = ((int)$slot_key_two[0]*60*60+(int)$slot_key_two[1]*60);
		    	}
			}

		} else {
			
			if((isset($_POST['date']) || isset($_POST['time'])) && (!isset($_POST['pickupDate']) && !isset($_POST['pickupTime']))) {
				
				if($this->hpos) {
					$order->update_meta_data( 'delivery_type', 'delivery' );
				} else {
					update_post_meta( $order_id, 'delivery_type', 'delivery' );
				}
			} elseif((isset($_POST['pickupDate']) || isset($_POST['pickupTime'])) && (!isset($_POST['date']) && !isset($_POST['time']))) {
				
				if($this->hpos) {
					$order->update_meta_data( 'delivery_type', 'pickup' );
				} else {
					update_post_meta( $order_id, 'delivery_type', 'pickup' );
				}
			} else {
				
				if($this->hpos) {
					$order->update_meta_data( 'delivery_type', 'delivery' );
				} else {
					update_post_meta( $order_id, 'delivery_type', 'delivery' );
				}
			}
			if(isset($_POST['date']) && !isset($_POST['pickupDate'])) {
				$en_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['date']),"delivery"),"delivery");
				
				if($this->hpos) {
					$order->update_meta_data( 'delivery_date', date("Y-m-d", strtotime($en_date)) );
				} else {
					update_post_meta( $order_id, 'delivery_date', date("Y-m-d", strtotime($en_date)) );
				}

				if(!in_array($order->get_status(), ['cancelled','failed','refunded'])) {
					
					if(get_option('coderockz_woo_delivery_max_per_day_count') !== false) {
						$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');
						if(((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && date("Y-m-d", strtotime($en_date)) != $old_delivery_date) {
							if(isset($max_per_day_count['delivery']['order']) && array_key_exists(date("Y-m-d", strtotime($en_date)), $max_per_day_count['delivery']['order'])) {
						    	if(isset($max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))]) && ($max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))]!= '' || $max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))]>=0)) {
						    		$max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))] = $max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))] + 1;
						    	} else {
						    		$max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))] = 1;
						    	}
						    } else {
						    	$max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))] = 1;
						    }

						}

					    if(((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && $old_delivery_type != $_POST['deliveryOption'] && $old_pickup_date != "") {
							if(isset($max_per_day_count['pickup']['order']) && array_key_exists($old_pickup_date, $max_per_day_count['pickup']['order'])) {
						    	if(isset($max_per_day_count['pickup']['order'][$old_pickup_date]) && ($max_per_day_count['pickup']['order'][$old_pickup_date]!= '' || $max_per_day_count['pickup']['order'][$old_pickup_date]>0)) {
						    		$max_per_day_count['pickup']['order'][$old_pickup_date] = $max_per_day_count['pickup']['order'][$old_pickup_date] - 1;
						    	}
						    }
						} elseif(((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && $old_delivery_type == $_POST['deliveryOption'] && $old_delivery_date != "" && date("Y-m-d", strtotime($en_date)) != $old_delivery_date) {
							if(isset($max_per_day_count['delivery']['order']) && array_key_exists($old_delivery_date, $max_per_day_count['delivery']['order'])) {
						    	if(isset($max_per_day_count['delivery']['order'][$old_delivery_date]) && ($max_per_day_count['delivery']['order'][$old_delivery_date]!= '' || $max_per_day_count['delivery']['order'][$old_delivery_date]>0)) {
						    		$max_per_day_count['delivery']['order'][$old_delivery_date] = $max_per_day_count['delivery']['order'][$old_delivery_date] - 1;
						    	}
						    }
						}

					    $max_per_day_count = array_merge(get_option('coderockz_woo_delivery_max_per_day_count'),$max_per_day_count);
						update_option('coderockz_woo_delivery_max_per_day_count', $max_per_day_count);
					}
				}
				$date_timestamp = strtotime($en_date);
			} elseif(isset($_POST['date']) && $_POST['date'] != "") {
				$en_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['date']),"delivery"),"delivery");
				
				if($this->hpos) {
					$order->update_meta_data( 'delivery_date', date("Y-m-d", strtotime($en_date)) );
				} else {
					update_post_meta( $order_id, 'delivery_date', date("Y-m-d", strtotime($en_date)) );
				}

				if(!in_array($order->get_status(), ['cancelled','failed','refunded'])) {
					
					if(get_option('coderockz_woo_delivery_max_per_day_count') !== false) {
						$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');
						if(((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && date("Y-m-d", strtotime($en_date)) != $old_delivery_date) {
							if(isset($max_per_day_count['delivery']['order']) && array_key_exists(date("Y-m-d", strtotime($en_date)), $max_per_day_count['delivery']['order'])) {
						    	if(isset($max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))]) && ($max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))]!= '' || $max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))]>=0)) {
						    		$max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))] = $max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))] + 1;
						    	} else {
						    		$max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))] = 1;
						    	}
						    } else {
						    	$max_per_day_count['delivery']['order'][date("Y-m-d", strtotime($en_date))] = 1;
						    }

						}


					    if(((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && $old_delivery_type != $_POST['deliveryOption'] && $old_pickup_date != "") {
							if(isset($max_per_day_count['pickup']['order']) && array_key_exists($old_pickup_date, $max_per_day_count['pickup']['order'])) {
						    	if(isset($max_per_day_count['pickup']['order'][$old_pickup_date]) && ($max_per_day_count['pickup']['order'][$old_pickup_date]!= '' || $max_per_day_count['pickup']['order'][$old_pickup_date]>0)) {
						    		$max_per_day_count['pickup']['order'][$old_pickup_date] = $max_per_day_count['pickup']['order'][$old_pickup_date] - 1;
						    	}
						    }
						} elseif(((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && $old_delivery_type == $_POST['deliveryOption'] && $old_delivery_date != "" && date("Y-m-d", strtotime($en_date)) != $old_delivery_date) {
							if(isset($max_per_day_count['delivery']['order']) && array_key_exists($old_delivery_date, $max_per_day_count['delivery']['order'])) {
						    	if(isset($max_per_day_count['delivery']['order'][$old_delivery_date]) && ($max_per_day_count['delivery']['order'][$old_delivery_date]!= '' || $max_per_day_count['delivery']['order'][$old_delivery_date]>0)) {
						    		$max_per_day_count['delivery']['order'][$old_delivery_date] = $max_per_day_count['delivery']['order'][$old_delivery_date] - 1;
						    	}
						    }
						}

					    $max_per_day_count = array_merge(get_option('coderockz_woo_delivery_max_per_day_count'),$max_per_day_count);
						update_option('coderockz_woo_delivery_max_per_day_count', $max_per_day_count);
					}
				}
				$date_timestamp = strtotime($en_date);
			} else {
				if($this->hpos) {
					$order->delete_meta_data( 'delivery_date' );
				} else {
					delete_post_meta($order_id, 'delivery_date');
				}
			}

			if(isset($_POST['time']) && !isset($_POST['pickupTime'])) {
				$time = sanitize_text_field($_POST['time']);
				
				if($this->hpos) {
					$order->update_meta_data( 'delivery_time', $time );
				} else {
					update_post_meta( $order_id, 'delivery_time', $time );
				}
				$for_time_timestamp = $_POST['time'];
			} elseif(isset($_POST['time'])) {
				$time = sanitize_text_field($_POST['time']);
				
				if($this->hpos) {
					$order->update_meta_data( 'delivery_time', $time );
				} else {
					update_post_meta( $order_id, 'delivery_time', $time );
				}
				$for_time_timestamp = $_POST['time'];
			} else {
				if($this->hpos) {
					$order->delete_meta_data( 'delivery_time' );
				} else {
					delete_post_meta($order_id, 'delivery_time');
				}
			}

			if(isset($_POST['pickupDate']) && !isset($_POST['date'])) {
				$en_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['pickupDate']),"pickup"),"pickup");
				
				if($this->hpos) {
					$order->update_meta_data( 'pickup_date', date("Y-m-d", strtotime($en_date)) );
				} else {
					update_post_meta( $order_id, 'pickup_date', date("Y-m-d", strtotime($en_date)) );
				}
				if(!in_array($order->get_status(), ['cancelled','failed','refunded'])) {
					
					if(get_option('coderockz_woo_delivery_max_per_day_count') !== false) {
						$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');
						if(((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && date("Y-m-d", strtotime($en_date)) != $old_pickup_date) {
							if(isset($max_per_day_count['pickup']['order']) && array_key_exists(date("Y-m-d", strtotime($en_date)), $max_per_day_count['pickup']['order'])) {
						    	if(isset($max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))]) && ($max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))]!= '' || $max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))] >=0)) {
						    		$max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))] = $max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))] + 1;
						    	} else {
						    		$max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))] = 1;
						    	}
						    } else {
						    	$max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))] = 1;
						    }

						}

						if(((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && $old_delivery_type != $_POST['deliveryOption'] && $old_delivery_date != "") {
							if(isset($max_per_day_count['delivery']['order']) && array_key_exists($old_delivery_date, $max_per_day_count['delivery']['order'])) {
						    	if(isset($max_per_day_count['delivery']['order'][$old_delivery_date]) && ($max_per_day_count['delivery']['order'][$old_delivery_date]!= '' || $max_per_day_count['delivery']['order'][$old_delivery_date]>0)) {
						    		$max_per_day_count['delivery']['order'][$old_delivery_date] = $max_per_day_count['delivery']['order'][$old_delivery_date] - 1;
						    	}
						    }
						} elseif(((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && $old_delivery_type == $_POST['deliveryOption'] && $old_pickup_date != "" && date("Y-m-d", strtotime($en_date)) != $old_pickup_date) {
							if(isset($max_per_day_count['pickup']['order']) && array_key_exists($old_pickup_date, $max_per_day_count['pickup']['order'])) {
						    	if(isset($max_per_day_count['pickup']['order'][$old_pickup_date]) && ($max_per_day_count['pickup']['order'][$old_pickup_date]!= '' || $max_per_day_count['pickup']['order'][$old_pickup_date]>0)) {
						    		$max_per_day_count['pickup']['order'][$old_pickup_date] = $max_per_day_count['pickup']['order'][$old_pickup_date] - 1;
						    	}
						    }
						}

					    $max_per_day_count = array_merge(get_option('coderockz_woo_delivery_max_per_day_count'),$max_per_day_count);
						update_option('coderockz_woo_delivery_max_per_day_count', $max_per_day_count);
					}
				}
				$date_timestamp = strtotime($en_date);
			} elseif(isset($_POST['pickupDate']) && $_POST['pickupDate'] != "") {
				$en_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['pickupDate']),"pickup"),"pickup");
				
				if($this->hpos) {
					$order->update_meta_data( 'pickup_date', date("Y-m-d", strtotime($en_date)) );
				} else {
					update_post_meta( $order_id, 'pickup_date', date("Y-m-d", strtotime($en_date)) );
				}
				if(!in_array($order->get_status(), ['cancelled','failed','refunded'])) {
					
					if(get_option('coderockz_woo_delivery_max_per_day_count') !== false) {
						$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');
						if(((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && date("Y-m-d", strtotime($en_date)) != $old_pickup_date) {
							if(isset($max_per_day_count['pickup']['order']) && array_key_exists(date("Y-m-d", strtotime($en_date)), $max_per_day_count['pickup']['order'])) {
						    	if(isset($max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))]) && ($max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))]!= '' || $max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))] >=0)) {
						    		$max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))] = $max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))] + 1;
						    	} else {
						    		$max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))] = 1;
						    	}
						    } else {
						    	$max_per_day_count['pickup']['order'][date("Y-m-d", strtotime($en_date))] = 1;
						    }

						}

						if(((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && $old_delivery_type != $_POST['deliveryOption'] && $old_delivery_date != "") {
							if(isset($max_per_day_count['delivery']['order']) && array_key_exists($old_delivery_date, $max_per_day_count['delivery']['order'])) {
						    	if(isset($max_per_day_count['delivery']['order'][$old_delivery_date]) && ($max_per_day_count['delivery']['order'][$old_delivery_date]!= '' || $max_per_day_count['delivery']['order'][$old_delivery_date]>0)) {
						    		$max_per_day_count['delivery']['order'][$old_delivery_date] = $max_per_day_count['delivery']['order'][$old_delivery_date] - 1;
						    	}
						    }
						} elseif(((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && $old_delivery_type == $_POST['deliveryOption'] && $old_pickup_date != "" && date("Y-m-d", strtotime($en_date)) != $old_pickup_date) {
							if(isset($max_per_day_count['pickup']['order']) && array_key_exists($old_pickup_date, $max_per_day_count['pickup']['order'])) {
						    	if(isset($max_per_day_count['pickup']['order'][$old_pickup_date]) && ($max_per_day_count['pickup']['order'][$old_pickup_date]!= '' || $max_per_day_count['pickup']['order'][$old_pickup_date]>0)) {
						    		$max_per_day_count['pickup']['order'][$old_pickup_date] = $max_per_day_count['pickup']['order'][$old_pickup_date] - 1;
						    	}
						    }
						}

					    $max_per_day_count = array_merge(get_option('coderockz_woo_delivery_max_per_day_count'),$max_per_day_count);
						update_option('coderockz_woo_delivery_max_per_day_count', $max_per_day_count);
					}
				}
				$date_timestamp = strtotime($en_date);
			} else {
				if($this->hpos) {
					$order->delete_meta_data( 'pickup_date' );
				} else {
					delete_post_meta($order_id, 'pickup_date');
				}
			}

			if(isset($_POST['pickupTime']) && !isset($_POST['time'])) {
				$pickup_time = sanitize_text_field($_POST['pickupTime']);
				
				if($this->hpos) {
					$order->update_meta_data( 'pickup_time', $pickup_time );
				} else {
					update_post_meta( $order_id, 'pickup_time', $pickup_time );
				}
				$for_time_timestamp = $_POST['pickupTime'];

			} elseif(isset($_POST['pickupTime'])) {
				$pickup_time = sanitize_text_field($_POST['pickupTime']);
				
				if($this->hpos) {
					$order->update_meta_data( 'pickup_time', $pickup_time );
				} else {
					update_post_meta( $order_id, 'pickup_time', $pickup_time );
				}
			} else {
				if($this->hpos) {
					$order->delete_meta_data( 'pickup_time' );
				} else {
					delete_post_meta($order_id, 'pickup_time');
				}
			}

			if(isset($_POST['pickup'])) {
				$pickup = sanitize_text_field($_POST['pickup']);
				
				if($this->hpos) {
					$order->update_meta_data( 'pickup_location', $pickup );
				} else {
					update_post_meta( $order_id, 'pickup_location', $pickup );
				}
			} else {
				if($this->hpos) {
					$order->delete_meta_data( 'pickup_location' );
				} else {
					delete_post_meta($order_id, 'pickup_location');
				}
			}

			if(!is_null($for_time_timestamp)) {
				$minutes = $for_time_timestamp;

		    	$slot_key = explode(' - ', $minutes);
				$slot_key_one = explode(':', $slot_key[0]);
				$time_timestamp_start = ((int)$slot_key_one[0]*60*60+(int)$slot_key_one[1]*60);

		    	if(!isset($slot_key[1])) {
		    		$time_timestamp_end = 0;
		    	} else {
		    		$slot_key_two = explode(':', $slot_key[1]);
		    		$time_timestamp_end = ((int)$slot_key_two[0]*60*60+(int)$slot_key_two[1]*60);
		    	}
			} 
		}
		
		if(isset($_POST['additional'])) {
			$additional = sanitize_textarea_field($_POST['additional']);
			if($this->hpos) {
				$order->update_meta_data( 'additional_note', $additional );
			} else {
				update_post_meta( $order_id, 'additional_note', $additional );
			}
		} else {
			if($this->hpos) {
				$order->delete_meta_data( 'additional_note' );
			} else {
				delete_post_meta($order_id, 'additional_note');
			}
		}

		$delivery_details_in_timestamp = 0;
		if(!is_null($date_timestamp)) {
			$delivery_details_in_timestamp = $delivery_details_in_timestamp+(int)$date_timestamp;
		}
		if(!is_null($time_timestamp_start)) {
			$delivery_details_in_timestamp = $delivery_details_in_timestamp+(int)$time_timestamp_start;
		}

		if(!is_null($time_timestamp_end)) {
			$delivery_details_in_timestamp = $delivery_details_in_timestamp+(int)$time_timestamp_end;
		}

		if($delivery_details_in_timestamp != 0) {
			
			if($this->hpos) {
				$order->update_meta_data( 'delivery_details_timestamp', $delivery_details_in_timestamp);
			} else {
				update_post_meta($order_id, 'delivery_details_timestamp', $delivery_details_in_timestamp);
			}
		}

		if($this->hpos) {
	  		$order->save();
		}

		$localization_settings = get_option('coderockz_woo_delivery_localization_settings');
		$delivery_fee_text = (isset($localization_settings['delivery_fee_text']) && !empty($localization_settings['delivery_fee_text'])) ? stripslashes($localization_settings['delivery_fee_text']) : __("Delivery Time Slot Fee","coderockz-woo-delivery");
		$pickup_fee_text = (isset($localization_settings['pickup_fee_text']) && !empty($localization_settings['pickup_fee_text'])) ? stripslashes($localization_settings['pickup_fee_text']) : __("Pickup Slot Fee","coderockz-woo-delivery");
		$sameday_fee_text = (isset($localization_settings['sameday_fee_text']) && !empty($localization_settings['sameday_fee_text'])) ? stripslashes($localization_settings['sameday_fee_text']) : __("Same Day Delivery Fee","coderockz-woo-delivery");
		$nextday_fee_text = (isset($localization_settings['nextday_fee_text']) && !empty($localization_settings['nextday_fee_text'])) ? stripslashes($localization_settings['nextday_fee_text']) : __("Next Day Delivery Fee","coderockz-woo-delivery");
		$day_after_tomorrow_fee_text = (isset($localization_settings['day_after_tomorrow_fee_text']) && !empty($localization_settings['day_after_tomorrow_fee_text'])) ? stripslashes($localization_settings['day_after_tomorrow_fee_text']) : __("Day After Tomorrow Delivery Fee","coderockz-woo-delivery");
		$other_fee_text = (isset($localization_settings['other_fee_text']) && !empty($localization_settings['other_fee_text'])) ? stripslashes($localization_settings['other_fee_text']) : __("Other Day Delivery Fee","coderockz-woo-delivery");

		$weekday_fee_text = (isset($localization_settings['weekday_fee_text']) && !empty($localization_settings['weekday_fee_text'])) ? stripslashes($localization_settings['weekday_fee_text']) : __("Weekday Delivery Fee","coderockz-woo-delivery");

		$specific_date_fee_text = (isset($localization_settings['specific_date_fee_text']) && !empty($localization_settings['specific_date_fee_text'])) ? stripslashes($localization_settings['specific_date_fee_text']) : __( "Delivery Date Fee", 'coderockz-woo-delivery' );

		$conditional_fee_text = (isset($localization_settings['conditional_fee_text']) && !empty($localization_settings['conditional_fee_text'])) ? stripslashes($localization_settings['conditional_fee_text']) : __("Conditional Delivery Fee","coderockz-woo-delivery");
		
		$as_soon_as_possible_fee_text = (isset($localization_settings['as_soon_as_possible_fee_text']) && !empty($localization_settings['as_soon_as_possible_fee_text'])) ? stripslashes($localization_settings['as_soon_as_possible_fee_text']) : __("As Soon As Possible Delivery Fee","coderockz-woo-delivery");

		$other_settings = get_option('coderockz_woo_delivery_other_settings');
		$add_tax_delivery_pickup_fee = (isset($other_settings['add_tax_delivery_pickup_fee']) && !empty($other_settings['add_tax_delivery_pickup_fee'])) ? $other_settings['add_tax_delivery_pickup_fee'] : false;
		$shipping_tax_class = (isset($other_settings['shipping_tax_class']) && !empty($other_settings['shipping_tax_class'])) ? $other_settings['shipping_tax_class'] : "";
		$delivery_tips_settings = get_option('coderockz_woo_delivery_delivery_tips_settings');
		$delivery_tips_field_label = (isset($delivery_tips_settings['delivery_tips_field_label']) && !empty($delivery_tips_settings['delivery_tips_field_label'])) ? stripslashes($delivery_tips_settings['delivery_tips_field_label']) : __( "Tips to Delivery Person", 'coderockz-woo-delivery' );

		$pickup_location_fee_text = (isset($localization_settings['pickup_location_fee_text']) && !empty($localization_settings['pickup_location_fee_text'])) ? stripslashes($localization_settings['pickup_location_fee_text']) : __( "Pickup Location Fee", 'coderockz-woo-delivery' );


		// Delete the previous time slot fee and delivery date fee

		$product_quantity = 0;
		foreach( $order->get_items( array( 'line_item' ) ) as $item_id => $item ) {
			$product_quantity = $product_quantity + $item->get_quantity();
		}

		foreach( $order->get_items('fee') as $item_id => $item_fee ){
		    if( $item_fee['name'] == $delivery_fee_text || $item_fee['name'] == $pickup_fee_text || $item_fee['name'] == $sameday_fee_text || $item_fee['name'] == $nextday_fee_text || $item_fee['name'] == $day_after_tomorrow_fee_text || $item_fee['name'] == $other_fee_text || $item_fee['name'] == $weekday_fee_text || $item_fee['name'] == $specific_date_fee_text || $item_fee['name'] == $conditional_fee_text || $item_fee['name'] == $as_soon_as_possible_fee_text || $item_fee['name'] == $delivery_tips_field_label || $item_fee['name'] == $pickup_location_fee_text) {
		    	$order->get_items('fee')[$item_id]->delete();
		    }
		}

		$order = wc_get_order( $order_id );
		$order->calculate_totals();

		// Add new time slot fee if any
	    $fees_settings = get_option('coderockz_woo_delivery_fee_settings');

		$custom_time_slot_settings = get_option('coderockz_woo_delivery_time_slot_settings');
		$enable_custom_time_slot = (isset($custom_time_slot_settings['enable_custom_time_slot']) && !empty($custom_time_slot_settings['enable_custom_time_slot'])) ? $custom_time_slot_settings['enable_custom_time_slot'] : false;

		$custom_pickup_slot_settings = get_option('coderockz_woo_delivery_pickup_slot_settings');
		$enable_custom_pickup_slot = (isset($custom_pickup_slot_settings['enable_custom_pickup_slot']) && !empty($custom_pickup_slot_settings['enable_custom_pickup_slot'])) ? $custom_pickup_slot_settings['enable_custom_pickup_slot'] : false;

		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings'); 

		$enable_free_shipping_restriction = (isset($delivery_option_settings['enable_free_shipping_restriction']) && !empty($delivery_option_settings['enable_free_shipping_restriction'])) ? $delivery_option_settings['enable_free_shipping_restriction'] : false;
		$minimum_amount = (isset($delivery_option_settings['minimum_amount_shipping_restriction']) && $delivery_option_settings['minimum_amount_shipping_restriction'] != "") ? (float)$delivery_option_settings['minimum_amount_shipping_restriction'] : "";

		foreach( $order->get_items( 'shipping' ) as $item_id => $item ) {

		    $shipping_zone = WC_Shipping_Zones::get_zone_by( 'instance_id', $item->get_instance_id() );

		    $shipping_methods = $shipping_zone->get_shipping_methods();

		    foreach ( $shipping_methods as $instance_id => $shipping_method ) {

		    	if($shipping_method->is_enabled() && $shipping_method->id == 'local_pickup') {
    				$has_local_pickup_method[] = $shipping_method->get_title();	        	
	        	} 
		    	
		    	$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
		    	if($shipping_method->is_enabled() && ($shipping_method->id != 'local_pickup')) {

	        		if($shipping_method->id == 'free_shipping') {
	        			if(($order->get_total() >= $shipping_method->min_amount) || ($enable_free_shipping_restriction && $minimum_amount != "" && $order->get_total() >= $minimum_amount)) {
	        				$has_other_method[] = $shipping_method->get_title();
	        			}

	        		} else {

	        			if(!isset($fees_settings['conditional_delivery_shipping_method']) || empty($fees_settings['conditional_delivery_shipping_method']) || $shipping_method->get_title() != $fees_settings['conditional_delivery_shipping_method']) { 
	        				$has_other_method[] = $shipping_method->get_title();
	        			}
	        			
	        		}

	        	}

		    }
		}

		if(isset($_POST['deliveryOption']) && $_POST['deliveryOption'] == "delivery") {

			if(isset($has_other_method) && count($has_other_method) > 1 && sanitize_text_field($_POST['shippingMethod']) != null && sanitize_text_field($_POST['shippingMethod']) != "" && isset($_POST['shippingMethod'])) {

				$new_method_title = sanitize_text_field($_POST['shippingMethod']);

				// Array for tax calculations
				$calculate_tax_for = array(
				    'country'  => ! empty($order->get_shipping_country()) ? $order->get_shipping_country() : $order->get_billing_country(),
            		'state'    => ! empty($order->get_shipping_state()) ? $order->get_shipping_state() : $order->get_billing_state(),
            		'postcode' => ! empty($order->get_shipping_postcode()) ? $order->get_shipping_postcode() : $order->get_billing_postcode(),
            		'city'     => ! empty($order->get_shipping_city()) ? $order->get_shipping_city() : $order->get_billing_city(),
				);

				$changed = false; // Initializing

				// Loop through order shipping items
				foreach( $order->get_items( 'shipping' ) as $item_id => $item ){

				    // Retrieve the customer shipping zone
				    $shipping_zone = WC_Shipping_Zones::get_zone_by( 'instance_id', $item->get_instance_id() );

				    // Get an array of available shipping methods for the current shipping zone
				    $shipping_methods = $shipping_zone->get_shipping_methods();

				    // Loop through available shipping methods
				    foreach ( $shipping_methods as $instance_id => $shipping_method ) {

				        // Targeting specific shipping method
				        if( $shipping_method->is_enabled() && $shipping_method->get_title() === $new_method_title ) {

				            // Set an existing shipping method for customer zone
				            $item->set_method_title( $shipping_method->get_title() );
				            $item->set_method_id( $shipping_method->get_rate_id() ); // set an existing Shipping method rate ID
				            if($shipping_method->get_rate_id() != "free_shipping") {
				            	if(isset($shipping_method->cost)) {
					            	if(strpos($shipping_method->cost,"[qty]") !== false) {
					            		
					            		$shipping_cost_string = str_replace("[qty]",$product_quantity,$shipping_method->cost);
					            		$item->set_total( $this->field_calculate->calculate($shipping_cost_string) );
					            	
					            	}/* elseif(strpos($shipping_method->cost,"fee percent") !== false) {

					            	
						            	if(strpos($shipping_method->cost,"min_fee") !== false) {
						            		$item->set_total( $shipping_method->cost );
						            	
						            	} elseif(strpos($shipping_method->cost,"max_fee") !== false) {
						            		$item->set_total( $shipping_method->cost );
						            	
						            	} 

					            	} */else {
					            		$item->set_total( (float)$shipping_method->cost );
					            	}
				            	}
				            }
				            
				            $item->calculate_taxes( $calculate_tax_for );
				            $item->save();


				            $changed = true;
				            break; // stop the loop
				        }
				    }
				}

				if ( $changed ) {
				    // Calculate totals and save
				    $order->calculate_totals(); // the save() method is included
				}

			} elseif(isset($has_other_method) && count($has_other_method) == 1 && isset($_POST['shippingMethod'])) {
				$new_method_title = $has_other_method[0];

				// Array for tax calculations
				$calculate_tax_for = array(
				    'country'  => ! empty($order->get_shipping_country()) ? $order->get_shipping_country() : $order->get_billing_country(),
            		'state'    => ! empty($order->get_shipping_state()) ? $order->get_shipping_state() : $order->get_billing_state(),
            		'postcode' => ! empty($order->get_shipping_postcode()) ? $order->get_shipping_postcode() : $order->get_billing_postcode(),
            		'city'     => ! empty($order->get_shipping_city()) ? $order->get_shipping_city() : $order->get_billing_city(),
				);

				$changed = false; // Initializing

				// Loop through order shipping items
				foreach( $order->get_items( 'shipping' ) as $item_id => $item ){

				    // Retrieve the customer shipping zone
				    $shipping_zone = WC_Shipping_Zones::get_zone_by( 'instance_id', $item->get_instance_id() );

				    // Get an array of available shipping methods for the current shipping zone
				    $shipping_methods = $shipping_zone->get_shipping_methods();

				    // Loop through available shipping methods
				    foreach ( $shipping_methods as $instance_id => $shipping_method ) {

				        // Targeting specific shipping method
				        if( $shipping_method->is_enabled() && $shipping_method->get_title() === $new_method_title/* && $item->get_instance_id() === $instance_id*/ ) {

				            // Set an existing shipping method for customer zone
				            $item->set_method_title( $shipping_method->get_title() );
				            $item->set_method_id( $shipping_method->get_rate_id() ); // set an existing Shipping method rate ID
				            if($shipping_method->get_rate_id() != "free_shipping") {
				            	if(strpos($shipping_method->cost,"[qty]") !== false) {
				            		
				            		$shipping_cost_string = str_replace("[qty]",$product_quantity,$shipping_method->cost);
				            		$item->set_total( $this->field_calculate->calculate($shipping_cost_string) );
				            	
				            	}/* elseif(strpos($shipping_method->cost,"fee percent") !== false) {

				            	
					            	if(strpos($shipping_method->cost,"min_fee") !== false) {
					            		$item->set_total( $shipping_method->cost );
					            	
					            	} elseif(strpos($shipping_method->cost,"max_fee") !== false) {
					            		$item->set_total( $shipping_method->cost );
					            	
					            	} 

				            	} */else {
				            		$item->set_total( (float)$shipping_method->cost );
				            	}
				        	}

				            $item->calculate_taxes( $calculate_tax_for );
				            $item->save();

				            

				            $changed = true;
				            break; // stop the loop
				        }
				    }
				}

				if ( $changed ) {
				    // Calculate totals and save
				    $order->calculate_totals(); // the save() method is included
				}
			}

			if(isset($time)) {
				if($time != "" && $time != "as-soon-as-possible" && $time != "conditional-delivery") {
		        	if(strpos($time, ' - ') !== false) {
		        		$time = explode(' - ', $time);
						$inserted_data_key_array_one = explode(':', $time[0]);
						$inserted_data_key_array_two = explode(':', $time[1]);
						$time = ((int)$inserted_data_key_array_one[0]*60+(int)$inserted_data_key_array_one[1]).' - '.((int)( $inserted_data_key_array_two[0] == "00" || $inserted_data_key_array_two[0] == "0" ? 24 : $inserted_data_key_array_two[0] )*60+(int)$inserted_data_key_array_two[1]);
						$inserted_data_key_array = explode(" - ",$time);
		        	} else {
		        		$inserted_data_key_array = [];
		        		$inserted_data_key_array_one = explode(':', $time);
		        		$time = ((int)$inserted_data_key_array_one[0]*60+(int)$inserted_data_key_array_one[1]);
		        		$inserted_data_key_array[] = $time;
		        	}

		        	if($enable_custom_time_slot) {
						if(isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0){


					  		foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {
					  			if($individual_time_slot['enable']) {
						  			$key = preg_replace('/-/', ' - ', $key);
						  			$key_array = explode(" - ",$key);

							  		if(isset($inserted_data_key_array[1])) {


							  			if(!empty($time) && $time == $key) {

								    		if($individual_time_slot["fee"] !="") {
									    		$fee = array('name' => $delivery_fee_text, 'amount' => $individual_time_slot["fee"], 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
												$item = new WC_Order_Item_Fee();
												$item->set_props( array(
												  'name'      => $fee['name'],
												  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
												  'total'     => $fee['amount'],
												  'total_tax' => $fee['tax'],
												  'taxes'     => array(
												    'total' => $fee['tax_data'],
												  ),
												  'order_id'  => $order_id,
												) );

												$item->save();
												$order->add_item( $item );
								    		}
								    	} elseif(!empty($time) && $individual_time_slot['enable_split'] && $individual_time_slot['split_slot_duration']!="" && $inserted_data_key_array[0]>= $key_array[0] && $inserted_data_key_array[1] <= $key_array[1]) {
								    		$temp_first_slot = $key_array[0] + $individual_time_slot['split_slot_duration'];
								    		$individual_time_slot_duration = $individual_time_slot['split_slot_duration'];
								    		while($temp_first_slot<=$key_array[1]) {

								    			if($temp_first_slot == $inserted_data_key_array[1] && $temp_first_slot - $individual_time_slot_duration == $inserted_data_key_array[0]) {

								    				if($individual_time_slot["fee"] !="") {
											    		$fee = array('name' => $delivery_fee_text, 'amount' => $individual_time_slot["fee"], 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
														$item = new WC_Order_Item_Fee();
														$item->set_props( array(
														  'name'      => $fee['name'],
														  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
														  'total'     => $fee['amount'],
														  'total_tax' => $fee['tax'],
														  'taxes'     => array(
														    'total' => $fee['tax_data'],
														  ),
														  'order_id'  => $order_id,
														) );

														$item->save();
														$order->add_item( $item );
										    		}

										    		break;
								    			}

								    			$individual_time_slot_duration = $temp_first_slot + $individual_time_slot_duration > $key_array[1] ? $key_array[1] - $temp_first_slot : $individual_time_slot['split_slot_duration'];

								    			$temp_first_slot = $temp_first_slot + $individual_time_slot_duration;
								    		}
								    	}


							  		} elseif(!isset($inserted_data_key_array[1])) {

							  			if($time != "" && $time == $key_array[0] && $inserted_data_key_array[0] < $key_array[1]) {
							    		
								    		if($individual_time_slot["fee"] !="") {
								    			
								    			$fee = array('name' => $delivery_fee_text, 'amount' => $individual_time_slot["fee"], 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
												$item = new WC_Order_Item_Fee();
												$item->set_props( array(
												  'name'      => $fee['name'],
												  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
												  'total'     => $fee['amount'],
												  'total_tax' => $fee['tax'],
												  'taxes'     => array(
												    'total' => $fee['tax_data'],
												  ),
												  'order_id'  => $order_id,
												) );

												$item->save();
												$order->add_item( $item );
								    		}
								    	} elseif($time != "" && $individual_time_slot['enable_split'] && $individual_time_slot['split_slot_duration']!="") {
								    		$temp_first_slot = $key_array[0];
								    		while(($temp_first_slot + $individual_time_slot['split_slot_duration'])<=$key_array[1]) {
								    			if($temp_first_slot + $individual_time_slot['split_slot_duration'] == $inserted_data_key_array[0]) {
								    				if($individual_time_slot["fee"] !="") {
								    			
										    			$fee = array('name' => $delivery_fee_text, 'amount' => $individual_time_slot["fee"], 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
														$item = new WC_Order_Item_Fee();
														$item->set_props( array(
														  'name'      => $fee['name'],
														  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
														  'total'     => $fee['amount'],
														  'total_tax' => $fee['tax'],
														  'taxes'     => array(
														    'total' => $fee['tax_data'],
														  ),
														  'order_id'  => $order_id,
														) );

														$item->save();
														$order->add_item( $item );
										    		}

										    		break;
								    			}

								    			$temp_first_slot = $temp_first_slot + $individual_time_slot['split_slot_duration'];
								    		}
								    	}

							  		}
								}
					  		}
					  	}
					} else {
						if(isset($fees_settings['enable_time_slot_fee']) && $fees_settings['enable_time_slot_fee'] && isset($time))
						{
					    	foreach($fees_settings['time_slot_fee'] as $key => $slot_fee)
					    	{
					    		$key = preg_replace('/-/', ' - ', $key);
						    	if($time == $key) {
							    	$fee = array('name' => $delivery_fee_text, 'amount' => $slot_fee, 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
									$item = new WC_Order_Item_Fee();
									$item->set_props( array(
									  'name'      => $fee['name'],
									  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
									  'total'     => $fee['amount'],
									  'total_tax' => $fee['tax'],
									  'taxes'     => array(
									    'total' => $fee['tax_data'],
									  ),
									  'order_id'  => $order_id,
									) );
									
									$item->save();
									$order->add_item( $item );
							    }
					    	}
						}
					}
		    		
				} elseif($time == "conditional-delivery") {


					if(isset($fees_settings['conditional_delivery_shipping_method']) && !empty($fees_settings['conditional_delivery_shipping_method'])) {
						$new_method_title =$fees_settings['conditional_delivery_shipping_method'];

						// Get the the WC_Order Object from an order ID (optional)
						$order = wc_get_order( $order_id );

						// Array for tax calculations
						$calculate_tax_for = array(
						    'country'  => $order->get_shipping_country(),
						    'state'    => $order->get_shipping_state(), // (optional value)
						    'postcode' => $order->get_shipping_postcode(), // (optional value)
						    'city'     => $order->get_shipping_city(), // (optional value)
						);

						$changed = false; // Initializing

						// Loop through order shipping items
						foreach( $order->get_items( 'shipping' ) as $item_id => $item ){

						    // Retrieve the customer shipping zone
						    $shipping_zone = WC_Shipping_Zones::get_zone_by( 'instance_id', $item->get_instance_id() );

						    // Get an array of available shipping methods for the current shipping zone
						    $shipping_methods = $shipping_zone->get_shipping_methods();

						    // Loop through available shipping methods
						    foreach ( $shipping_methods as $instance_id => $shipping_method ) {

						        // Targeting specific shipping method
						        if( $shipping_method->is_enabled() && $shipping_method->get_title() === $new_method_title ) {

						            // Set an existing shipping method for customer zone
						            $item->set_method_title( $shipping_method->get_title() );
						            $item->set_method_id( $shipping_method->get_rate_id() ); // set an existing Shipping method rate ID
						            if($shipping_method->get_rate_id() != "free_shipping") {
						            	$item->set_total( (float)$shipping_method->cost );
						        	}

						            $item->calculate_taxes( $calculate_tax_for );
						            $item->save();

						            $changed = true;
						            break; // stop the loop
						        }
						    }
						}

						if ( $changed ) {
						    // Calculate totals and save
						    $order->calculate_totals(); // the save() method is included
						}
					} elseif(isset($fees_settings['conditional_delivery_fee']) && !empty($fees_settings['conditional_delivery_fee'])) {
						$today = wp_date('Y-m-d',current_time( 'timestamp', 1 ));
						$selected_en_date = isset($en_date) ? $en_date : $today;
						if(date("Y-m-d", strtotime($selected_en_date)) == $today) {
							$conditional_delivery_fee = isset($fees_settings['conditional_delivery_fee']) && !empty($fees_settings['conditional_delivery_fee']) ? esc_attr($fees_settings['conditional_delivery_fee']) : 0;
							$conditional_fee_text = (isset($localization_settings['conditional_fee_text']) && !empty($localization_settings['conditional_fee_text'])) ? stripslashes($localization_settings['conditional_fee_text']) : __( "Conditional Delivery Fee", 'coderockz-woo-delivery' );
							if(isset($conditional_delivery_fee) && $conditional_delivery_fee != 0) {

								$fee = array('name' => $conditional_fee_text, 'amount' => $conditional_delivery_fee, 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
								$item = new WC_Order_Item_Fee();
								$item->set_props( array(
								  'name'      => $fee['name'],
								  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
								  'total'     => $fee['amount'],
								  'total_tax' => $fee['tax'],
								  'taxes'     => array(
								    'total' => $fee['tax_data'],
								  ),
								  'order_id'  => $order_id,
								) );

								$item->save();
								$order->add_item( $item );
							}
						}
					}

				} elseif($time == "as-soon-as-possible") {

					if(isset($delivery_time_settings['as_soon_as_possible_fee']) && !empty($delivery_time_settings['as_soon_as_possible_fee'])) {
						$as_soon_as_possible_fee = isset($delivery_time_settings['as_soon_as_possible_fee']) && !empty($delivery_time_settings['as_soon_as_possible_fee']) ? esc_attr($delivery_time_settings['as_soon_as_possible_fee']) : 0;
						$as_soon_as_possible_fee_text = (isset($localization_settings['as_soon_as_possible_fee_text']) && !empty($localization_settings['as_soon_as_possible_fee_text'])) ? stripslashes($localization_settings['as_soon_as_possible_fee_text']) : __( "As Soon As Possible Delivery Fee", 'coderockz-woo-delivery' );
						if(isset($as_soon_as_possible_fee) && $as_soon_as_possible_fee != 0) {

							$fee = array('name' => $as_soon_as_possible_fee_text, 'amount' => $as_soon_as_possible_fee, 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
							$item = new WC_Order_Item_Fee();
							$item->set_props( array(
							  'name'      => $fee['name'],
							  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
							  'total'     => $fee['amount'],
							  'total_tax' => $fee['tax'],
							  'taxes'     => array(
							    'total' => $fee['tax_data'],
							  ),
							  'order_id'  => $order_id,
							) );

							$item->save();
							$order->add_item( $item );
						}
					}

				}

			}

			if (isset($fees_settings['enable_delivery_date_fee']) && $fees_settings['enable_delivery_date_fee'] && isset($en_date) && !empty($en_date))
			{
				$today = wp_date('Y-m-d',current_time( 'timestamp', 1 ));
				$today_dt = current_datetime($today);
				$tomorrow = $today_dt->modify('+1 day')->format("Y-m-d");
				$today_dt = current_datetime($today);
				$day_after_tomorrow = $today_dt->modify('+2 day')->format("Y-m-d");

				if(date("Y-m-d", strtotime($en_date)) == $today)
				{
					if(isset($fees_settings['same_day_fee']))
					{
		    			$fee = $fees_settings['same_day_fee'];
		    			$day = $sameday_fee_text;
					}
				}
				elseif(date('Y-m-d', strtotime($en_date)) == $tomorrow)
				{
					if(isset($fees_settings['next_day_fee']))
					{
		    			$fee = $fees_settings['next_day_fee'];
		    			$day = $nextday_fee_text;
					}
				}
				elseif(date('Y-m-d', strtotime($en_date)) == $day_after_tomorrow)
				{
					if(isset($fees_settings['day_after_tomorrow_fee']))
					{
		    			$fee = $fees_settings['day_after_tomorrow_fee'];
		    			$day = $day_after_tomorrow_fee_text;
					}
				}
				else
				{
					if(isset($fees_settings['other_days_fee']))
					{
		    			$fee = $fees_settings['other_days_fee'];
		    			$day = $other_fee_text;
					}
				}
				if(isset($fee) && $fee != 0)
				{

			    	$fee = array('name' => $day, 'amount' => $fee, 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
					$item = new WC_Order_Item_Fee();
					$item->set_props( array(
					  'name'      => $fee['name'],
					  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
					  'total'     => $fee['amount'],
					  'total_tax' => $fee['tax'],
					  'taxes'     => array(
					    'total' => $fee['tax_data'],
					  ),
					  'order_id'  => $order_id,
					) );
					
					$item->save();
					$order->add_item( $item );
				}
			}

			if (isset($fees_settings['enable_weekday_wise_delivery_fee']) && $fees_settings['enable_weekday_wise_delivery_fee'] && isset($en_date) && !empty($en_date))
			{
				$current_week_day = date("w",strtotime($en_date));

				$week_day_fee = (isset($fees_settings['weekday_wise_delivery_fee'][$current_week_day]) && $fees_settings['weekday_wise_delivery_fee'][$current_week_day] != "") ? (float)$fees_settings['weekday_wise_delivery_fee'][$current_week_day] : "";

				if( $week_day_fee != "" && $week_day_fee != 0 )
				{
			    	$fee = array('name' => $weekday_fee_text, 'amount' => $week_day_fee, 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
					$item = new WC_Order_Item_Fee();
					$item->set_props( array(
					  'name'      => $fee['name'],
					  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
					  'total'     => $fee['amount'],
					  'total_tax' => $fee['tax'],
					  'taxes'     => array(
					    'total' => $fee['tax_data'],
					  ),
					  'order_id'  => $order_id,
					) );
					
					$item->save();
					$order->add_item( $item ); 
				}
			}

			if (isset($fees_settings['specific_date_fee']) && !empty($fees_settings['specific_date_fee']) && isset($en_date) && !empty($en_date))
			{	
				$specific_date = date("Y-m-d", strtotime($en_date));			
				

				$specific_date_fee = (isset($fees_settings['specific_date_fee'][$specific_date]) && $fees_settings['specific_date_fee'][$specific_date] != "") ? (float)$fees_settings['specific_date_fee'][$specific_date] : "";


				if( $specific_date_fee != "" && $specific_date_fee != 0 )
				{
			    	$fee = array('name' => $specific_date_fee_text, 'amount' => $specific_date_fee, 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
					$item = new WC_Order_Item_Fee();
					$item->set_props( array(
					  'name'      => $fee['name'],
					  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
					  'total'     => $fee['amount'],
					  'total_tax' => $fee['tax'],
					  'taxes'     => array(
					    'total' => $fee['tax_data'],
					  ),
					  'order_id'  => $order_id,
					) );
					
					$item->save();
					$order->add_item( $item ); 
				}
			}

			if ( isset($en_date) && !empty($en_date) && isset($fees_settings['specific_date_coupon']) && !empty($fees_settings['specific_date_coupon'])) {
				foreach($fees_settings['specific_date_coupon'] as $coupon => $value) {
					$order->remove_coupon( $coupon );
				}
			}

			$coupon_code = '';
			$applied_coupons  = $order->get_coupon_codes();
			if ( isset($en_date) && !empty($en_date) && isset($fees_settings['specific_date_coupon']) && !empty($fees_settings['specific_date_coupon']))
			{
				foreach($fees_settings['specific_date_coupon'] as $coupon => $value) {
					if(isset($value['range_value']) && in_array(date("Y-m-d", strtotime($en_date)), $value['range_value'])) {
						$coupon_code = $coupon;
						break;
					}
				}
			}

			if( isset($en_date) && !empty($en_date) && $coupon_code != "") {

			    if( ! in_array($coupon_code, $applied_coupons)) {
					$order->apply_coupon( $coupon_code );
				}

			}

			$enable_delivery_tips = (isset($delivery_tips_settings['enable_delivery_tips']) && !empty($delivery_tips_settings['enable_delivery_tips'])) ? $delivery_tips_settings['enable_delivery_tips'] : false;

			if ($enable_delivery_tips && $selected_delivery_tips != "") {

				$enable_including_discount = (isset($delivery_tips_settings['percentage_calculating_include_discount']) && !empty($delivery_tips_settings['percentage_calculating_include_discount'])) ? $delivery_tips_settings['percentage_calculating_include_discount'] : false;
				$enable_including_tax = (isset($delivery_tips_settings['percentage_calculating_include_tax']) && !empty($delivery_tips_settings['percentage_calculating_include_tax'])) ? $delivery_tips_settings['percentage_calculating_include_tax'] : false;
				$enable_including_shipping_cost = (isset($delivery_tips_settings['percentage_calculating_include_shipping_cost']) && !empty($delivery_tips_settings['percentage_calculating_include_shipping_cost'])) ? $delivery_tips_settings['percentage_calculating_include_shipping_cost'] : false;
				$enable_including_fees = (isset($delivery_tips_settings['percentage_calculating_include_fees']) && !empty($delivery_tips_settings['percentage_calculating_include_fees'])) ? $delivery_tips_settings['percentage_calculating_include_fees'] : false;
	    		$cart_total_for_percentage = $this->helper->order_total_tips($order, $enable_including_discount, $enable_including_tax, $enable_including_shipping_cost, $enable_including_fees);

				if(strpos($selected_delivery_tips, '%') !== false) {

					$delivery_tips = ($cart_total_for_percentage * (float)str_replace("%","",$selected_delivery_tips))/100;
				} else {
					$delivery_tips = (float)$selected_delivery_tips;
				}

				if($delivery_tips > 0) {
					if(class_exists('WOOCS_STARTER')){
						$delivery_tips = apply_filters('woocs_exchange_value', $delivery_tips);
					}

					$precentage_rounding = isset($delivery_tips_settings['precentage_rounding']) && $delivery_tips_settings['precentage_rounding']!= "" ? $delivery_tips_settings['precentage_rounding'] : "no_round";

					if($precentage_rounding == "up") {
						$delivery_tips = ceil($delivery_tips);
					} elseif($precentage_rounding == "down") {
						$delivery_tips = floor($delivery_tips);
					}

					$add_tax = isset($delivery_tips_settings['add_tax']) && $delivery_tips_settings['add_tax']!= "" ? $delivery_tips_settings['add_tax'] : false;

					$delivery_tips_field_label = isset($delivery_tips_settings['delivery_tips_field_label']) && $delivery_tips_settings['delivery_tips_field_label'] != "" ? $delivery_tips_settings['delivery_tips_field_label'] : __( "Tips to Delivery Person", 'coderockz-woo-delivery' );

	  				$fee = array('name' => $delivery_tips_field_label, 'amount' => $delivery_tips, 'taxable' => $add_tax, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
					$item = new WC_Order_Item_Fee();
					$item->set_props( array(
					  'name'      => $fee['name'],
					  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
					  'total'     => $fee['amount'],
					  'total_tax' => $fee['tax'],
					  'taxes'     => array(
					    'total' => $fee['tax_data'],
					  ),
					  'order_id'  => $order_id,
					) );
					
					$item->save();
					$order->add_item( $item );
	  			}
				
			}

		} 

		if (isset($_POST['deliveryOption']) && $_POST['deliveryOption'] == "pickup") {

			if(isset($has_local_pickup_method) && count($has_local_pickup_method) > 1 && sanitize_text_field($_POST['shippingMethod']) != null && sanitize_text_field($_POST['shippingMethod']) != "" && isset($_POST['shippingMethod'])) {

				$new_method_title = sanitize_text_field($_POST['shippingMethod']);

				// Array for tax calculations
				$calculate_tax_for = array(
				    'country'  => $order->get_shipping_country(),
				    'state'    => $order->get_shipping_state(), // (optional value)
				    'postcode' => $order->get_shipping_postcode(), // (optional value)
				    'city'     => $order->get_shipping_city(), // (optional value)
				);

				$changed = false; // Initializing

				// Loop through order shipping items
				foreach( $order->get_items( 'shipping' ) as $item_id => $item ){

				    // Retrieve the customer shipping zone
				    $shipping_zone = WC_Shipping_Zones::get_zone_by( 'instance_id', $item->get_instance_id() );

				    // Get an array of available shipping methods for the current shipping zone
				    $shipping_methods = $shipping_zone->get_shipping_methods();

				    // Loop through available shipping methods
				    foreach ( $shipping_methods as $instance_id => $shipping_method ) {

				        // Targeting specific shipping method
				        if( $shipping_method->is_enabled() && $shipping_method->get_title() === $new_method_title ) {

				            // Set an existing shipping method for customer zone
				            $item->set_method_title( $shipping_method->get_title() );
				            $item->set_method_id( $shipping_method->get_rate_id() ); // set an existing Shipping method rate ID
				            if($shipping_method->get_rate_id() != "free_shipping") {
				            	$item->set_total( (float)$shipping_method->cost );
				        	}

				            $item->calculate_taxes( $calculate_tax_for );
				            $item->save();

				            $changed = true;
				            break; // stop the loop
				        }
				    }
				}

				if ( $changed ) {
				    // Calculate totals and save
				    $order->calculate_totals(); // the save() method is included
				}

			} elseif(isset($has_local_pickup_method) && count($has_local_pickup_method) == 1 && isset($_POST['shippingMethod'])) {
				$new_method_title = $has_local_pickup_method[0];

				// Array for tax calculations
				$calculate_tax_for = array(
				    'country'  => $order->get_shipping_country(),
				    'state'    => $order->get_shipping_state(), // (optional value)
				    'postcode' => $order->get_shipping_postcode(), // (optional value)
				    'city'     => $order->get_shipping_city(), // (optional value)
				);

				$changed = false; // Initializing

				// Loop through order shipping items
				foreach( $order->get_items( 'shipping' ) as $item_id => $item ){

				    // Retrieve the customer shipping zone
				    $shipping_zone = WC_Shipping_Zones::get_zone_by( 'instance_id', $item->get_instance_id() );

				    // Get an array of available shipping methods for the current shipping zone
				    $shipping_methods = $shipping_zone->get_shipping_methods();

				    // Loop through available shipping methods
				    foreach ( $shipping_methods as $instance_id => $shipping_method ) {

				        // Targeting specific shipping method
				        if( $shipping_method->is_enabled() && $shipping_method->get_title() === $new_method_title ) {

				            // Set an existing shipping method for customer zone
				            $item->set_method_title( $shipping_method->get_title() );
				            $item->set_method_id( $shipping_method->get_rate_id() ); // set an existing Shipping method rate ID
				            if($shipping_method->get_rate_id() != "free_shipping") {
				            	$item->set_total( (float)$shipping_method->cost );
				        	}
				            $item->calculate_taxes( $calculate_tax_for );
				            $item->save();

				            $changed = true;
				            break; // stop the loop
				        }
				    }
				}

				if ( $changed ) {
				    // Calculate totals and save
				    $order->calculate_totals(); // the save() method is included
				}
			}

			if(isset($pickup_time)) {
				if($pickup_time != "") {
		        	if(strpos($pickup_time, ' - ') !== false) {
		        		$pickup_time = explode(' - ', $pickup_time);
						$inserted_data_key_array_one = explode(':', $pickup_time[0]);
						$inserted_data_key_array_two = explode(':', $pickup_time[1]);
						$pickup_time = ((int)$inserted_data_key_array_one[0]*60+(int)$inserted_data_key_array_one[1]).' - '.((int)( $inserted_data_key_array_two[0] == "00" || $inserted_data_key_array_two[0] == "0" ? 24 : $inserted_data_key_array_two[0] )*60+(int)$inserted_data_key_array_two[1]);
						$inserted_data_key_array = explode(" - ",$pickup_time);
		        	} else {
		        		$inserted_data_key_array = [];
		        		$inserted_data_key_array_one = explode(':', $pickup_time);
		        		$pickup_time = ((int)$inserted_data_key_array_one[0]*60+(int)$inserted_data_key_array_one[1]);
		        		$inserted_data_key_array[] = $pickup_time;
		        	}
		    		
				}

				if($enable_custom_pickup_slot) {
					if(isset($custom_pickup_slot_settings['time_slot']) && count($custom_pickup_slot_settings['time_slot'])>0){

						
				  		foreach($custom_pickup_slot_settings['time_slot'] as $key => $individual_pickup_slot) {
				  			if($individual_pickup_slot['enable']) {
					  			$key = preg_replace('/-/', ' - ', $key);
					  			
					  			$key_array = explode(" - ",$key);

					  			if(isset($inserted_data_key_array[1])) {


						  			if(!empty($pickup_time) && $pickup_time == $key) {

							    		if($individual_pickup_slot["fee"] !="") {
								    		$fee = array('name' => $pickup_fee_text, 'amount' => $individual_pickup_slot["fee"], 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
											$item = new WC_Order_Item_Fee();
											$item->set_props( array(
											  'name'      => $fee['name'],
											  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
											  'total'     => $fee['amount'],
											  'total_tax' => $fee['tax'],
											  'taxes'     => array(
											    'total' => $fee['tax_data'],
											  ),
											  'order_id'  => $order_id,
											) );

											$item->save();
											$order->add_item( $item );
							    		}
							    	} elseif(!empty($pickup_time) && $individual_pickup_slot['enable_split'] && $individual_pickup_slot['split_slot_duration']!="" && $inserted_data_key_array[0]>= $key_array[0] && $inserted_data_key_array[1] <= $key_array[1]) {
							    		$temp_first_slot = $key_array[0] + $individual_pickup_slot['split_slot_duration'];
							    		$individual_time_slot_duration = $individual_pickup_slot['split_slot_duration'];
							    		while($temp_first_slot<=$key_array[1]) {

							    			if($temp_first_slot == $inserted_data_key_array[1] && $temp_first_slot - $individual_time_slot_duration == $inserted_data_key_array[0]) {

							    				if($individual_pickup_slot["fee"] !="") {
										    		$fee = array('name' => $pickup_fee_text, 'amount' => $individual_pickup_slot["fee"], 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
													$item = new WC_Order_Item_Fee();
													$item->set_props( array(
													  'name'      => $fee['name'],
													  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
													  'total'     => $fee['amount'],
													  'total_tax' => $fee['tax'],
													  'taxes'     => array(
													    'total' => $fee['tax_data'],
													  ),
													  'order_id'  => $order_id,
													) );

													$item->save();
													$order->add_item( $item );
									    		}

									    		break;
							    			}

							    			$individual_time_slot_duration = $temp_first_slot + $individual_time_slot_duration > $key_array[1] ? $key_array[1] - $temp_first_slot : $individual_pickup_slot['split_slot_duration'];

							    			$temp_first_slot = $temp_first_slot + $individual_time_slot_duration;
							    		}
							    	}

						  		} elseif(!isset($inserted_data_key_array[1])) {

						  			if($pickup_time != "" && $pickup_time == $key_array[0] && $inserted_data_key_array[0] < $key_array[1]) {
							    		
						  				if($individual_pickup_slot["fee"] !="") {

							    			$fee = array('name' => $pickup_fee_text, 'amount' => $individual_pickup_slot["fee"], 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
											$item = new WC_Order_Item_Fee();
											$item->set_props( array(
											  'name'      => $fee['name'],
											  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
											  'total'     => $fee['amount'],
											  'total_tax' => $fee['tax'],
											  'taxes'     => array(
											    'total' => $fee['tax_data'],
											  ),
											  'order_id'  => $order_id,
											) );

											$item->save();
											$order->add_item( $item );
							    		}

							    	} elseif($pickup_time != "" && $individual_pickup_slot['enable_split'] && $individual_pickup_slot['split_slot_duration']!="") {
							    		$temp_first_slot = $key_array[0];
							    		while(($temp_first_slot + $individual_pickup_slot['split_slot_duration'])<=$key_array[1]) {
							    			if($temp_first_slot + $individual_pickup_slot['split_slot_duration'] == $inserted_data_key_array[0]) {
							    				
							    				if($individual_pickup_slot["fee"] !="") {

									    			$fee = array('name' => $pickup_fee_text, 'amount' => $individual_pickup_slot["fee"], 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
													$item = new WC_Order_Item_Fee();
													$item->set_props( array(
													  'name'      => $fee['name'],
													  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
													  'total'     => $fee['amount'],
													  'total_tax' => $fee['tax'],
													  'taxes'     => array(
													    'total' => $fee['tax_data'],
													  ),
													  'order_id'  => $order_id,
													) );

													$item->save();
													$order->add_item( $item );
									    		}

									    		break;
							    			}

							    			$temp_first_slot = $temp_first_slot + $individual_pickup_slot['split_slot_duration'];
							    		}
							    	}
						  		}
							}
				  		}
				  	}
				} else {
					if(isset($fees_settings['enable_pickup_slot_fee']) && $fees_settings['enable_pickup_slot_fee'] && isset($pickup_time))
					{
				    	foreach($fees_settings['pickup_slot_fee'] as $key => $slot_fee)
				    	{
				    		$key = preg_replace('/-/', ' - ', $key);
					    	if($pickup_time == $key) {
						    	$fee = array('name' => $pickup_fee_text, 'amount' => $slot_fee, 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
									$item = new WC_Order_Item_Fee();
									$item->set_props( array(
									  'name'      => $fee['name'],
									  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
									  'total'     => $fee['amount'],
									  'total_tax' => $fee['tax'],
									  'taxes'     => array(
									    'total' => $fee['tax_data'],
									  ),
									  'order_id'  => $order_id,
									) );
									
									$item->save();
									$order->add_item( $item );
						    }
				    	}
					}
				}

			}

			if(isset($pickup) && $pickup != "") {

				$pickup_location_settings = get_option('coderockz_woo_delivery_pickup_location_settings');

				$pickup_locations = (isset($pickup_location_settings['pickup_location']) && !empty($pickup_location_settings['pickup_location'])) ? $pickup_location_settings['pickup_location'] : [];

				$pickup_location_fee = isset($pickup_locations[$pickup]['fee']) && $pickup_locations[$pickup]['fee'] != "" ? $pickup_locations[$pickup]['fee'] : "";

				if($pickup_location_fee != "") {

		    		$fee = array('name' => $pickup_location_fee_text, 'amount' => $pickup_location_fee, 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
					$item = new WC_Order_Item_Fee();
					$item->set_props( array(
					  'name'      => $fee['name'],
					  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
					  'total'     => $fee['amount'],
					  'total_tax' => $fee['tax'],
					  'taxes'     => array(
					    'total' => $fee['tax_data'],
					  ),
					  'order_id'  => $order_id,
					) );

					$item->save();
					$order->add_item( $item );
		    		
			    }
			}
		}

		if(!isset($_POST['deliveryOption'])) {

			if(isset($time)) {
				if($time != "") {
		        	if(strpos($time, ' - ') !== false) {
		        		$time = explode(' - ', $time);
						$inserted_data_key_array_one = explode(':', $time[0]);
						$inserted_data_key_array_two = explode(':', $time[1]);
						$time = ((int)$inserted_data_key_array_one[0]*60+(int)$inserted_data_key_array_one[1]).' - '.((int)( $inserted_data_key_array_two[0] == "00" || $inserted_data_key_array_two[0] == "0" ? 24 : $inserted_data_key_array_two[0] )*60+(int)$inserted_data_key_array_two[1]);
						$inserted_data_key_array = explode(" - ",$time);
		        	} else {
		        		$inserted_data_key_array = [];
		        		$inserted_data_key_array_one = explode(':', $time);
		        		$time = ((int)$inserted_data_key_array_one[0]*60+(int)$inserted_data_key_array_one[1]);
		        		$inserted_data_key_array[] = $time;
		        	}
		    		
				}

				if($enable_custom_time_slot) {
					if(isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0){


				  		foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {
				  			if($individual_time_slot['enable']) {
					  			$key = preg_replace('/-/', ' - ', $key);
					  			$key_array = explode(" - ",$key);

					  			if(isset($inserted_data_key_array[1])) {

					  				if(!empty($time) && $time == $key) {

							    		if($individual_time_slot["fee"] !="") {
								    		$fee = array('name' => $delivery_fee_text, 'amount' => $individual_time_slot["fee"], 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
											$item = new WC_Order_Item_Fee();
											$item->set_props( array(
											  'name'      => $fee['name'],
											  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
											  'total'     => $fee['amount'],
											  'total_tax' => $fee['tax'],
											  'taxes'     => array(
											    'total' => $fee['tax_data'],
											  ),
											  'order_id'  => $order_id,
											) );

											$item->save();
											$order->add_item( $item );
							    		}
							    	} elseif(!empty($time) && $individual_time_slot['enable_split'] && $individual_time_slot['split_slot_duration']!="" && $inserted_data_key_array[0]>= $key_array[0] && $inserted_data_key_array[1] <= $key_array[1]) {
							    		$temp_first_slot = $key_array[0] + $individual_time_slot['split_slot_duration'];
							    		$individual_time_slot_duration = $individual_time_slot['split_slot_duration'];
							    		while($temp_first_slot<=$key_array[1]) {

							    			if($temp_first_slot == $inserted_data_key_array[1] && $temp_first_slot - $individual_time_slot_duration == $inserted_data_key_array[0]) {

							    				if($individual_time_slot["fee"] !="") {
										    		$fee = array('name' => $delivery_fee_text, 'amount' => $individual_time_slot["fee"], 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
													$item = new WC_Order_Item_Fee();
													$item->set_props( array(
													  'name'      => $fee['name'],
													  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
													  'total'     => $fee['amount'],
													  'total_tax' => $fee['tax'],
													  'taxes'     => array(
													    'total' => $fee['tax_data'],
													  ),
													  'order_id'  => $order_id,
													) );

													$item->save();
													$order->add_item( $item );
									    		}

									    		break;
							    			}

							    			$individual_time_slot_duration = $temp_first_slot + $individual_time_slot_duration > $key_array[1] ? $key_array[1] - $temp_first_slot : $individual_time_slot['split_slot_duration'];

							    			$temp_first_slot = $temp_first_slot + $individual_time_slot_duration;
							    		}
							    	}

						  		} elseif(!isset($inserted_data_key_array[1])) {


						  			if($time != "" && ($time == $key_array[0] && $inserted_data_key_array[0] < $key_array[1])) {
							    		

							    		if($individual_time_slot["fee"] !="") {
							    			
							    			$fee = array('name' => $delivery_fee_text, 'amount' => $individual_time_slot["fee"], 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
											$item = new WC_Order_Item_Fee();
											$item->set_props( array(
											  'name'      => $fee['name'],
											  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
											  'total'     => $fee['amount'],
											  'total_tax' => $fee['tax'],
											  'taxes'     => array(
											    'total' => $fee['tax_data'],
											  ),
											  'order_id'  => $order_id,
											) );

											$item->save();
											$order->add_item( $item );
							    		}
							    	} elseif($time != "" && $individual_time_slot['enable_split'] && $individual_time_slot['split_slot_duration']!="") {
							    		$temp_first_slot = $key_array[0];
							    		while(($temp_first_slot + $individual_time_slot['split_slot_duration'])<=$key_array[1]) {
							    			if($temp_first_slot + $individual_time_slot['split_slot_duration'] == $inserted_data_key_array[0]) {
							    				if($individual_time_slot["fee"] !="") {
							    			
									    			$fee = array('name' => $delivery_fee_text, 'amount' => $individual_time_slot["fee"], 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
													$item = new WC_Order_Item_Fee();
													$item->set_props( array(
													  'name'      => $fee['name'],
													  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
													  'total'     => $fee['amount'],
													  'total_tax' => $fee['tax'],
													  'taxes'     => array(
													    'total' => $fee['tax_data'],
													  ),
													  'order_id'  => $order_id,
													) );

													$item->save();
													$order->add_item( $item );
									    		}

									    		break;
							    			}

							    			$temp_first_slot = $temp_first_slot + $individual_time_slot['split_slot_duration'];
							    		}
							    	}

						  		}

							}
				  		}
				  	}
				} else {
					if(isset($fees_settings['enable_time_slot_fee']) && $fees_settings['enable_time_slot_fee'] && isset($time))
					{
				    	foreach($fees_settings['time_slot_fee'] as $key => $slot_fee)
				    	{
				    		$key = preg_replace('/-/', ' - ', $key);
					    	if($time == $key) {
						    	$fee = array('name' => $delivery_fee_text, 'amount' => $slot_fee, 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
									$item = new WC_Order_Item_Fee();
									$item->set_props( array(
									  'name'      => $fee['name'],
									  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
									  'total'     => $fee['amount'],
									  'total_tax' => $fee['tax'],
									  'taxes'     => array(
									    'total' => $fee['tax_data'],
									  ),
									  'order_id'  => $order_id,
									) );
									
									$item->save();
									$order->add_item( $item );
						    }
				    	}
					}
				}

			}

			if (isset($fees_settings['enable_delivery_date_fee']) && $fees_settings['enable_delivery_date_fee'] && isset($en_date) && !empty($en_date))
			{
				$today = wp_date('Y-m-d',current_time( 'timestamp', 1 ));
				$today_dt = current_datetime($today);
				$tomorrow = $today_dt->modify('+1 day')->format("Y-m-d");
				$today_dt = current_datetime($today);
				$day_after_tomorrow = $today_dt->modify('+2 day')->format("Y-m-d");

				if(date("Y-m-d", strtotime($en_date)) == $today)
				{
					if(isset($fees_settings['same_day_fee']))
					{
		    			$fee = $fees_settings['same_day_fee'];
		    			$day = $sameday_fee_text;
					}
				}
				elseif(date('Y-m-d', strtotime($en_date)) == $tomorrow)
				{
					if(isset($fees_settings['next_day_fee']))
					{
		    			$fee = $fees_settings['next_day_fee'];
		    			$day = $nextday_fee_text;
					}
				}
				elseif(date('Y-m-d', strtotime($en_date)) == $day_after_tomorrow)
				{
					if(isset($fees_settings['day_after_tomorrow_fee']))
					{
		    			$fee = $fees_settings['day_after_tomorrow_fee'];
		    			$day = $day_after_tomorrow_fee_text;
					}
				}
				else
				{
					if(isset($fees_settings['other_days_fee']))
					{
		    			$fee = $fees_settings['other_days_fee'];
		    			$day = $other_fee_text;
					}
				}
				if(isset($fee) && $fee != 0)
				{

			    	$fee = array('name' => $day, 'amount' => $fee, 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
					$item = new WC_Order_Item_Fee();
					$item->set_props( array(
					  'name'      => $fee['name'],
					  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
					  'total'     => $fee['amount'],
					  'total_tax' => $fee['tax'],
					  'taxes'     => array(
					    'total' => $fee['tax_data'],
					  ),
					  'order_id'  => $order_id,
					) );
					
					$item->save();
					$order->add_item( $item );
				}
			}

			if (isset($fees_settings['enable_weekday_wise_delivery_fee']) && $fees_settings['enable_weekday_wise_delivery_fee'] && isset($en_date) && !empty($en_date))
			{
				$current_week_day = date("w",strtotime($en_date));

				$week_day_fee = (isset($fees_settings['weekday_wise_delivery_fee'][$current_week_day]) && $fees_settings['weekday_wise_delivery_fee'][$current_week_day] != "") ? (float)$fees_settings['weekday_wise_delivery_fee'][$current_week_day] : "";

				if( $week_day_fee != "" && $week_day_fee != 0 )
				{
			    	$fee = array('name' => $weekday_fee_text, 'amount' => $week_day_fee, 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
					$item = new WC_Order_Item_Fee();
					$item->set_props( array(
					  'name'      => $fee['name'],
					  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
					  'total'     => $fee['amount'],
					  'total_tax' => $fee['tax'],
					  'taxes'     => array(
					    'total' => $fee['tax_data'],
					  ),
					  'order_id'  => $order_id,
					) );
					
					$item->save();
					$order->add_item( $item ); 
				}
			}

			if(isset($pickup_time)) {

				if($pickup_time != "") {
		        	if(strpos($pickup_time, ' - ') !== false) {
		        		$pickup_time = explode(' - ', $pickup_time);
						$inserted_data_key_array_one = explode(':', $pickup_time[0]);
						$inserted_data_key_array_two = explode(':', $pickup_time[1]);
						$pickup_time = ((int)$inserted_data_key_array_one[0]*60+(int)$inserted_data_key_array_one[1]).' - '.((int)( $inserted_data_key_array_two[0] == "00" || $inserted_data_key_array_two[0] == "0" ? 24 : $inserted_data_key_array_two[0] )*60+(int)$inserted_data_key_array_two[1]);
						$inserted_data_key_array = explode(" - ",$pickup_time);
		        	} else {
		        		$inserted_data_key_array = [];
		        		$inserted_data_key_array_one = explode(':', $pickup_time);
		        		$pickup_time = ((int)$inserted_data_key_array_one[0]*60+(int)$inserted_data_key_array_one[1]);
		        		$inserted_data_key_array[] = $pickup_time;
		        	}
		    		
				}

				if($enable_custom_pickup_slot) {
					if(isset($custom_pickup_slot_settings['time_slot']) && count($custom_pickup_slot_settings['time_slot'])>0){

						
				  		foreach($custom_pickup_slot_settings['time_slot'] as $key => $individual_pickup_slot) {
				  			if($individual_pickup_slot['enable']) {
					  			$key = preg_replace('/-/', ' - ', $key);
					  			
					  			$key_array = explode(" - ",$key);

						    	if(isset($inserted_data_key_array[1])) {

						    		if(!empty($pickup_time) && $pickup_time == $key) {

							    		if($individual_pickup_slot["fee"] !="") {
								    		$fee = array('name' => $pickup_fee_text, 'amount' => $individual_pickup_slot["fee"], 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
											$item = new WC_Order_Item_Fee();
											$item->set_props( array(
											  'name'      => $fee['name'],
											  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
											  'total'     => $fee['amount'],
											  'total_tax' => $fee['tax'],
											  'taxes'     => array(
											    'total' => $fee['tax_data'],
											  ),
											  'order_id'  => $order_id,
											) );

											$item->save();
											$order->add_item( $item );
							    		}
							    	} elseif(!empty($pickup_time) && $individual_pickup_slot['enable_split'] && $individual_pickup_slot['split_slot_duration']!="" && $inserted_data_key_array[0]>= $key_array[0] && $inserted_data_key_array[1] <= $key_array[1]) {
							    		$temp_first_slot = $key_array[0] + $individual_pickup_slot['split_slot_duration'];
							    		$individual_time_slot_duration = $individual_pickup_slot['split_slot_duration'];
							    		while($temp_first_slot<=$key_array[1]) {

							    			if($temp_first_slot == $inserted_data_key_array[1] && $temp_first_slot - $individual_time_slot_duration == $inserted_data_key_array[0]) {

							    				if($individual_pickup_slot["fee"] !="") {
										    		$fee = array('name' => $pickup_fee_text, 'amount' => $individual_pickup_slot["fee"], 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
													$item = new WC_Order_Item_Fee();
													$item->set_props( array(
													  'name'      => $fee['name'],
													  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
													  'total'     => $fee['amount'],
													  'total_tax' => $fee['tax'],
													  'taxes'     => array(
													    'total' => $fee['tax_data'],
													  ),
													  'order_id'  => $order_id,
													) );

													$item->save();
													$order->add_item( $item );
									    		}

									    		break;
							    			}

							    			$individual_time_slot_duration = $temp_first_slot + $individual_time_slot_duration > $key_array[1] ? $key_array[1] - $temp_first_slot : $individual_pickup_slot['split_slot_duration'];

							    			$temp_first_slot = $temp_first_slot + $individual_time_slot_duration;
							    		}
							    	}

						  		} elseif(!isset($inserted_data_key_array[1])) {

						  			if($pickup_time != "" && $pickup_time == $key_array[0] && $inserted_data_key_array[0] < $key_array[1]) {
							    		
						  				if($individual_pickup_slot["fee"] !="") {

							    			$fee = array('name' => $pickup_fee_text, 'amount' => $individual_pickup_slot["fee"], 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
											$item = new WC_Order_Item_Fee();
											$item->set_props( array(
											  'name'      => $fee['name'],
											  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
											  'total'     => $fee['amount'],
											  'total_tax' => $fee['tax'],
											  'taxes'     => array(
											    'total' => $fee['tax_data'],
											  ),
											  'order_id'  => $order_id,
											) );

											$item->save();
											$order->add_item( $item );
							    		}

							    	} elseif($pickup_time != "" && $individual_pickup_slot['enable_split'] && $individual_pickup_slot['split_slot_duration']!="") {
							    		$temp_first_slot = $key_array[0];
							    		while(($temp_first_slot + $individual_pickup_slot['split_slot_duration'])<=$key_array[1]) {
							    			if($temp_first_slot + $individual_pickup_slot['split_slot_duration'] == $inserted_data_key_array[0]) {
							    				
							    				if($individual_pickup_slot["fee"] !="") {

									    			$fee = array('name' => $pickup_fee_text, 'amount' => $individual_pickup_slot["fee"], 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
													$item = new WC_Order_Item_Fee();
													$item->set_props( array(
													  'name'      => $fee['name'],
													  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
													  'total'     => $fee['amount'],
													  'total_tax' => $fee['tax'],
													  'taxes'     => array(
													    'total' => $fee['tax_data'],
													  ),
													  'order_id'  => $order_id,
													) );

													$item->save();
													$order->add_item( $item );
									    		}

									    		break;
							    			}

							    			$temp_first_slot = $temp_first_slot + $individual_time_slot['split_slot_duration'];
							    		}
							    	}
						  		}
							}
				  		}
				  	}
				} else {
					if(isset($fees_settings['enable_pickup_slot_fee']) && $fees_settings['enable_pickup_slot_fee'] && isset($pickup_time))
					{
				    	foreach($fees_settings['pickup_slot_fee'] as $key => $slot_fee)
				    	{
				    		$key = preg_replace('/-/', ' - ', $key);
					    	if($pickup_time == $key) {
						    	$fee = array('name' => $pickup_fee_text, 'amount' => $slot_fee, 'taxable' => $add_tax_delivery_pickup_fee, 'tax_class' => $shipping_tax_class, 'tax' => 0, 'tax_data' => array());
									$item = new WC_Order_Item_Fee();
									$item->set_props( array(
									  'name'      => $fee['name'],
									  'tax_class' => $fee['taxable'] ? $fee['tax_class'] : 0,
									  'total'     => $fee['amount'],
									  'total_tax' => $fee['tax'],
									  'taxes'     => array(
									    'total' => $fee['tax_data'],
									  ),
									  'order_id'  => $order_id,
									) );
									
									$item->save();
									$order->add_item( $item );
						    }
				    	}
					}
				}

			}

		}

		$order = wc_get_order( $order_id );
		$order->calculate_totals();
		
		if(sanitize_textarea_field($_POST['notify']) == "yes") {
			$order = wc_get_order( $order_id );
        	
        	$order_email = $order->get_billing_email();

        	if((metadata_exists('post', $order_id, '_wcj_order_number') && get_post_meta($order_id, '_wcj_order_number', true) !="") || ($order->meta_exists('_wcj_order_number') && $order->get_meta( '_wcj_order_number', true ) != "")) {
				if($this->hpos) {
					$order_id_with_custom = '#'.$order->get_meta( '_wcj_order_number', true );
				} else {
					$order_id_with_custom = '#'.get_post_meta($order_id, '_wcj_order_number', true);
				}
			} elseif(is_plugin_active('wt-woocommerce-sequential-order-numbers-pro/wt-advanced-order-number-pro.php') || is_plugin_active('wt-woocommerce-sequential-order-numbers/wt-advanced-order-number.php') || is_plugin_active('custom-order-numbers-for-woocommerce/custom-order-numbers-for-woocommerce.php') || is_plugin_active('yith-woocommerce-sequential-order-number-premium/init.php')) {
				$order_id_with_custom = $order->get_order_number();
			} else {
				$order_id_with_custom = $order->get_id();
			}

	        $shipping_address = $order->get_formatted_shipping_address();
	        $billing_address = $order->get_formatted_billing_address();
	        $payment_method = $order->get_payment_method_title();
	        $shipping_method = $order->get_shipping_method() != null && $order->get_shipping_method() != "" ? $order->get_shipping_method() : "";
	        $shipping_method_amount = $order->get_shipping_total() != null && $order->get_shipping_total() != "" && $order->get_shipping_total() != 0 ? " - ".wc_price($order->get_shipping_total()): "";

	        $order_tax = $order->get_total_tax();

	        $delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
	        $pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
        	$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
        	$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
        	$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
        	$pickup_location_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
        	$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');

        	$delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : __("Delivery Date","coderockz-woo-delivery");
        	$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : __("Pickup Date","coderockz-woo-delivery");
        	$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : __("Delivery Time","coderockz-woo-delivery");
        	$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : __("Pickup Time","coderockz-woo-delivery");
        	$pickup_location_field_label = (isset($pickup_location_settings['field_label']) && !empty($pickup_location_settings['field_label'])) ? stripslashes($pickup_location_settings['field_label']) : __("Pickup Location","coderockz-woo-delivery");
        	$additional_field_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? stripslashes($additional_field_settings['field_label']) : __("Special Note About Delivery","coderockz-woo-delivery");

	        $delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";
	        $add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

			if($add_weekday_name) {
				$delivery_date_format = "l ".$delivery_date_format;
			}

	        $pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";

	        $pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

			if($pickup_add_weekday_name) {
				$pickup_date_format = "l ".$pickup_date_format;
			}

	        $time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
	        if($time_format == 12) {
	            $time_format = "h:i A";
	        } elseif ($time_format == 24) {
	            $time_format = "H:i";
	        }

	        $pickup_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
	        if($pickup_format == 12) {
	            $pickup_format = "h:i A";
	        } elseif ($pickup_format == 24) {
	            $pickup_format = "H:i";
	        }

	        $notify_email_settings = get_option('coderockz_woo_delivery_notify_email_settings');
	        $delivery_subject = (isset($notify_email_settings['notify_delivery_email_subject']) && $notify_email_settings['notify_delivery_email_subject'] !="") ? stripslashes($notify_email_settings['notify_delivery_email_subject']) : "[".htmlspecialchars_decode(get_bloginfo('name'))."] ".__("Your delivery information is changed!", 'coderockz-woo-delivery');
        	$pickup_subject = (isset($notify_email_settings['notify_pickup_email_subject']) && $notify_email_settings['notify_pickup_email_subject'] !="") ? stripslashes($notify_email_settings['notify_pickup_email_subject']) : "[".htmlspecialchars_decode(get_bloginfo('name'))."] ".__("Your pickup information is changed!", 'coderockz-woo-delivery');
	        $pickup_notify_email_heading = (isset($notify_email_settings['pickup_notify_email_heading']) && $notify_email_settings['pickup_notify_email_heading'] !="") ? stripslashes($notify_email_settings['pickup_notify_email_heading']) : __("Your pickup information is changed!", 'coderockz-woo-delivery');
	        $delivery_notify_email_heading = (isset($notify_email_settings['delivery_notify_email_heading']) && $notify_email_settings['delivery_notify_email_heading'] !="") ? stripslashes($notify_email_settings['delivery_notify_email_heading']) : __("Your delivery information is changed!", 'coderockz-woo-delivery');

	        $notify_email_product_text = (isset($notify_email_settings['notify_email_product_text']) && $notify_email_settings['notify_email_product_text'] !="") ? stripslashes($notify_email_settings['notify_email_product_text']) : __("Product","coderockz-woo-delivery");
	        $notify_email_order_text = (isset($notify_email_settings['notify_email_order_text']) && $notify_email_settings['notify_email_order_text'] !="") ? stripslashes($notify_email_settings['notify_email_order_text']) : __("Order","coderockz-woo-delivery");
	        $notify_email_quantity_text = (isset($notify_email_settings['notify_email_quantity_text']) && $notify_email_settings['notify_email_quantity_text'] !="") ? stripslashes($notify_email_settings['notify_email_quantity_text']) : __("Quantity","coderockz-woo-delivery");
	        $notify_email_price_text = (isset($notify_email_settings['notify_email_price_text']) && $notify_email_settings['notify_email_price_text'] !="") ? stripslashes($notify_email_settings['notify_email_price_text']) : __("Price","coderockz-woo-delivery");
	        $notify_email_shipping_text = (isset($notify_email_settings['notify_email_shipping_text']) && $notify_email_settings['notify_email_shipping_text'] !="") ? stripslashes($notify_email_settings['notify_email_shipping_text']) : __("Shipping Method","coderockz-woo-delivery");
	        $notify_email_payment_text = (isset($notify_email_settings['notify_email_payment_text']) && $notify_email_settings['notify_email_payment_text'] !="") ? stripslashes($notify_email_settings['notify_email_payment_text']) : __("Payment Method","coderockz-woo-delivery");
	        $notify_email_total_text = (isset($notify_email_settings['notify_email_total_text']) && $notify_email_settings['notify_email_total_text'] !="") ? stripslashes($notify_email_settings['notify_email_total_text']) : __("Total","coderockz-woo-delivery");
	        $notify_email_tax_text = (isset($notify_email_settings['notify_email_tax_text']) && $notify_email_settings['notify_email_tax_text'] !="") ? stripslashes($notify_email_settings['notify_email_tax_text']) : __("Tax","coderockz-woo-delivery");
	        $notify_email_billing_address_heading = (isset($notify_email_settings['notify_email_billing_address_heading']) && $notify_email_settings['notify_email_billing_address_heading'] !="") ? stripslashes($notify_email_settings['notify_email_billing_address_heading']) : __("Billing Address","coderockz-woo-delivery");
	        $notify_email_shipping_address_heading = (isset($notify_email_settings['notify_email_shipping_address_heading']) && $notify_email_settings['notify_email_shipping_address_heading'] !="") ? stripslashes($notify_email_settings['notify_email_shipping_address_heading']) : __("Shipping Address","coderockz-woo-delivery");
	        
	        if($this->hpos) {
				$delivery_type = $order->meta_exists('delivery_type') ? $order->get_meta( 'delivery_type', true ) : "delivery";
			} else {
				$delivery_type = metadata_exists('post', $order_id, 'delivery_type') ? get_post_meta($order_id, 'delivery_type', true) : "delivery";
			}
	        
	        if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {
	            if($this->hpos) {
					$date = $order->get_meta( 'delivery_date', true );
				} else {
					$date = get_post_meta($order_id,"delivery_date",true);
				}

	            $delivery_date = date($delivery_date_format, strtotime($date));
	        }

	        if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {
	            if($this->hpos) {
					$pickup_date = $order->get_meta( 'pickup_date', true );
				} else {
					$pickup_date = get_post_meta($order_id,"pickup_date",true);
				}

	            $pickup_date = date($pickup_date_format, strtotime($pickup_date));
	        }

	        if((metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id,"delivery_time",true) != "") || ($order->meta_exists('delivery_time') && $order->get_meta( 'delivery_time', true ) != "")) {
	            
	        	if(get_post_meta($order_id,"delivery_time",true) != "as-soon-as-possible" && $order->get_meta( 'delivery_time', true ) != "as-soon-as-possible") {
	        		if($this->hpos) {
						$minutes = $order->get_meta( 'delivery_time', true );
					} else {
						$minutes = get_post_meta($order_id,"delivery_time",true);
					}
		            $minutes = explode(' - ', $minutes);

		    		if(!isset($minutes[1])) {
		    			$delivery_time = date($time_format, strtotime($minutes[0]));
		    		} else {

		    			$delivery_time = date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1]));
		    		}
	        	} else {
	        		$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : __("As Soon As Possible","coderockz-woo-delivery");
	        		$delivery_time = $as_soon_as_possible_text;
	        	}	            

	        }

	        if((metadata_exists('post', $order_id, 'pickup_time') && get_post_meta($order_id,"pickup_time",true) != "") || ($order->meta_exists('pickup_time') && $order->get_meta( 'pickup_time', true ) != "")) {
	            if($this->hpos) {
					$pickup_minutes = $order->get_meta( 'pickup_time', true );
				} else {
					$pickup_minutes = get_post_meta($order_id,"pickup_time",true);
				}
	            $pickup_minutes = explode(' - ', $pickup_minutes);

	    		if(!isset($pickup_minutes[1])) {
	    			$pickup_time = date($pickup_format, strtotime($pickup_minutes[0]));
	    		} else {
	    			$pickup_time = date($pickup_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_format, strtotime($pickup_minutes[1]));
	    		}

	        }

	        if((metadata_exists('post', $order_id, 'pickup_location') && get_post_meta($order_id, 'pickup_location', true) !="") || ($order->meta_exists('pickup_location') && $order->get_meta( 'pickup_location', true ) != "")) {
	            if($this->hpos) {
					$pickup_location = stripslashes(html_entity_decode($order->get_meta( 'pickup_location', true ), ENT_QUOTES));
				} else {
					$pickup_location = stripslashes(html_entity_decode(get_post_meta($order_id,"pickup_location",true), ENT_QUOTES));
				}
	        }

	        if((metadata_exists('post', $order_id, 'additional_note') && get_post_meta($order_id, 'additional_note', true) !="") || ($order->meta_exists('additional_note') && $order->get_meta( 'additional_note', true )!= "")) {
	            if($this->hpos) {
					$additional_note = $order->get_meta( 'additional_note', true );
				} else {
					$additional_note = get_post_meta($order_id, 'additional_note', true);
				}
	        }
	        

	        if($delivery_type == "pickup") {
	            $subject = $pickup_subject;
	            $email_heading = $pickup_notify_email_heading;
	        } else {
	            $subject = $delivery_subject;
	            $email_heading = $delivery_notify_email_heading;
	             
	        }

	        $currency_symbol = get_woocommerce_currency_symbol();

	        $notify_logo_id = (isset($notify_email_settings['notify-email-logo-id']) && !empty($notify_email_settings['notify-email-logo-id'])) ? $notify_email_settings['notify-email-logo-id'] : "";

			if($notify_logo_id != "") {

				$notify_logo_url = wp_get_attachment_image_src($notify_logo_id,'full', true);
				$notify_logo_path = $notify_logo_url[0];
			} else {
				$notify_logo_path = "";
			}

			$email_heading_color = (isset($notify_email_settings['notify_email_heading_color']) && !empty($notify_email_settings['notify_email_heading_color'])) ? $notify_email_settings['notify_email_heading_color'] : "#96588a";
			
			$notify_email_logo_width = (isset($notify_email_settings['notify_email_logo_width']) && !empty($notify_email_settings['notify_email_logo_width'])) ? $notify_email_settings['notify_email_logo_width']."px" : "120px";

			$email = Coderockz_Woo_Delivery_Email::init()
	        ->to($order_email)
	        ->subject($subject)
	        ->template(CODEROCKZ_WOO_DELIVERY_DIR .'admin/includes/notify_email_template.php', [
	            'order_id' => $order_id,
	            'order_id_with_custom' => $order_id_with_custom,
	            'notify_email_order_text' => $notify_email_order_text,
	            'delivery_type' => $delivery_type,
	            'notify_logo_path' => $notify_logo_path,
	            'email_heading_color' => $email_heading_color,
	            'notify_email_logo_width' => $notify_email_logo_width,
	            'email_heading' => $email_heading,
            	'notify_email_product_text' => $notify_email_product_text,
            	'notify_email_quantity_text' => $notify_email_quantity_text,
            	'notify_email_price_text' => $notify_email_price_text,
            	'notify_email_shipping_text' => $notify_email_shipping_text,
            	'notify_email_payment_text' => $notify_email_payment_text,
            	'notify_email_tax_text' => $notify_email_tax_text,
            	'notify_email_total_text' => $notify_email_total_text,
            	'notify_email_billing_address_heading' => $notify_email_billing_address_heading,
            	'notify_email_shipping_address_heading' => $notify_email_shipping_address_heading,
            	'delivery_date_field_label' =>$delivery_date_field_label,
            	'pickup_date_field_label' =>$pickup_date_field_label,
            	'delivery_time_field_label' =>$delivery_time_field_label,
            	'pickup_time_field_label' =>$pickup_time_field_label,
            	'pickup_location_field_label' =>$pickup_location_field_label,
            	'additional_field_field_label' =>$additional_field_field_label,
	            'delivery_date' => isset($delivery_date)?$delivery_date:"",
	            'delivery_time' => isset($delivery_time)?$delivery_time:"",
	            'pickup_date' => isset($pickup_date)?$pickup_date:"",
	            'pickup_time' => isset($pickup_time)?$pickup_time:"",
	            'pickup_location' => isset($pickup_location)?$pickup_location:"",
	            'additional_note' => isset($additional_note)?$additional_note:"",
	            'order_total' => $order->get_formatted_order_total(),
	            'billing_address' => $billing_address,
	            'shipping_address' => $shipping_address,
	            'shipping_method' => $shipping_method,
	            'shipping_method_amount' => $shipping_method_amount,
	            'order_tax' => $order_tax,
	            'payment_method' => $payment_method,
	            'order' => $order,
                'currency_symbol' => $currency_symbol,
                'order_email' => $order_email
	        ])
	        ->send();
		}

		$other_settings = get_option('coderockz_woo_delivery_other_settings');
		$add_delivery_info_order_note = (isset($other_settings['add_delivery_info_order_note']) && !empty($other_settings['add_delivery_info_order_note'])) ? $other_settings['add_delivery_info_order_note'] : false;

		if($add_delivery_info_order_note) {

			$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');			
			$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');			
			$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
			$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
			$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
			$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');

			$delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : __("Delivery Date","coderockz-woo-delivery");
			$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : __("Pickup Date","coderockz-woo-delivery");
			$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : __("Delivery Time","coderockz-woo-delivery");
			$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : __("Pickup Time","coderockz-woo-delivery");
			$pickup_location_field_label = (isset($delivery_pickup_settings['field_label']) && !empty($delivery_pickup_settings['field_label'])) ? stripslashes($delivery_pickup_settings['field_label']) : __("Pickup Location","coderockz-woo-delivery");
			$additional_field_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? stripslashes($additional_field_settings['field_label']) : __("Special Note About Delivery","coderockz-woo-delivery");
			
			$delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";
	        $add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

			if($add_weekday_name) {
				$delivery_date_format = "l ".$delivery_date_format;
			}

	        $pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";

	        $pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

			if($pickup_add_weekday_name) {
				$pickup_date_format = "l ".$pickup_date_format;
			}


			$time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
			if($time_format == 12) {
				$time_format = "h:i A";
			} elseif ($time_format == 24) {
				$time_format = "H:i";
			}

			$pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
			if($pickup_time_format == 12) {
				$pickup_time_format = "h:i A";
			} elseif ($pickup_time_format == 24) {
				$pickup_time_format = "H:i";
			}

		    $delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
		    $order_type_field_label = (isset($delivery_option_settings['delivery_option_label']) && !empty($delivery_option_settings['delivery_option_label'])) ? stripslashes($delivery_option_settings['delivery_option_label']) : __("Order Type","coderockz-woo-delivery");
		    $delivery_field_label = (isset($delivery_option_settings['delivery_label']) && !empty($delivery_option_settings['delivery_label'])) ? stripslashes($delivery_option_settings['delivery_label']) : __("Delivery","coderockz-woo-delivery");
			$pickup_field_label = (isset($delivery_option_settings['pickup_label']) && !empty($delivery_option_settings['pickup_label'])) ? stripslashes($delivery_option_settings['pickup_label']) : __("Pickup","coderockz-woo-delivery");
		    
		    $order_note = "";

		    $order = wc_get_order($order_id);

		    if((metadata_exists('post', $order_id, 'delivery_type') && get_post_meta( $order_id, 'delivery_type', true ) != "") || ($order->meta_exists('delivery_type') && $order->get_meta( 'delivery_type', true ) != "")) {

				if(get_post_meta($order_id, 'delivery_type', true) == "delivery" || $order->get_meta( 'delivery_type', true ) == "delivery") {

					if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {
						if($this->hpos) {
							$order_note .= "<br>".$delivery_date_field_label .': '. date($delivery_date_format, strtotime(sanitize_text_field($order->get_meta( 'delivery_date', true ))));
						} else {
							$order_note .= "<br>".$delivery_date_field_label .': '. date($delivery_date_format, strtotime(sanitize_text_field(get_post_meta($order_id, 'delivery_date', true))));
						}
				    }
					

				    if((metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id,"delivery_time",true) != "") || ($order->meta_exists('delivery_time') && $order->get_meta( 'delivery_time', true ) != "")) {
						if(get_post_meta($order_id,"delivery_time",true) == "as-soon-as-possible" || $order->get_meta( 'delivery_time', true ) == "as-soon-as-possible") {
							$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : __("As Soon As Possible","coderockz-woo-delivery");
							$time_value = $as_soon_as_possible_text;
						} else {
							if($this->hpos) {
								$minutes = sanitize_text_field($order->get_meta( 'delivery_time', true ));
							} else {
								$minutes = sanitize_text_field(get_post_meta($order_id, 'delivery_time', true));
							}
											
							$minutes = explode(' - ', $minutes);

				    		if(!isset($minutes[1])) {
				    			$time_value = date($time_format, strtotime($minutes[0]));
				    		} else {

				    			$time_value = date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1]));

				    		}

			    		}

						$order_note .= "<br>".$delivery_time_field_label .': '.$time_value;
					}
				} elseif(get_post_meta($order_id, 'delivery_type', true) == "pickup" || $order->get_meta( 'delivery_type', true ) == "pickup") {

					if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {
						if($this->hpos) {

							$order_note .= "<br>".$pickup_date_field_label .': '.date($pickup_date_format, strtotime(sanitize_text_field($order->get_meta( 'pickup_date', true ))));
						} else {
							$order_note .= "<br>".$pickup_date_field_label .': '.date($pickup_date_format, strtotime(sanitize_text_field(get_post_meta($order_id, 'pickup_date', true))));
						}
						
				    }


					if((metadata_exists('post', $order_id, 'pickup_time') && get_post_meta($order_id,"pickup_time",true) != "") || ($order->meta_exists('pickup_time') && $order->get_meta( 'pickup_time', true ) != "")) {
						if($this->hpos) {
							$pickup_minutes = sanitize_text_field($order->get_meta( 'pickup_time', true ));
						} else {
							$pickup_minutes = sanitize_text_field(get_post_meta($order_id, 'pickup_time', true));
						}

						$pickup_minutes = explode(' - ', $pickup_minutes);

			    		if(!isset($pickup_minutes[1])) {
			    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0]));
			    		} else {

			    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1]));
			    		}

						$order_note .= "<br>".$pickup_time_field_label .': '.$pickup_time_value;
					}


					if((metadata_exists('post', $order_id, 'pickup_location') && get_post_meta($order_id, 'pickup_location', true) !="") || ($order->meta_exists('pickup_location') && $order->get_meta( 'pickup_location', true ) != "")) {
						if($this->hpos) {
							$order_note .= "<br>".$pickup_location_field_label .': '.stripslashes(html_entity_decode(sanitize_text_field($order->get_meta( 'pickup_location', true )), ENT_QUOTES));
						} else {
							$order_note .= "<br>".$pickup_location_field_label .': '.stripslashes(html_entity_decode(sanitize_text_field(get_post_meta($order_id, 'pickup_location', true)), ENT_QUOTES));
						}
						
					}
				}
		    } else {

		    	if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {
		    		if($this->hpos) {
						$order_note .= "<br>".$pickup_date_field_label .': '.date($pickup_date_format, strtotime(sanitize_text_field($order->get_meta( 'pickup_date', true ))));
					} else {
						$order_note .= "<br>".$pickup_date_field_label .': '.date($pickup_date_format, strtotime(sanitize_text_field(get_post_meta($order_id, 'pickup_date', true))));
					}
					
			    }

				if((metadata_exists('post', $order_id, 'pickup_time') && get_post_meta($order_id,"pickup_time",true) != "") || ($order->meta_exists('pickup_time') && $order->get_meta( 'pickup_time', true ) != "")) {
					if($this->hpos) {
						$pickup_minutes = sanitize_text_field($order->get_meta( 'pickup_time', true ));
					} else {
						$pickup_minutes = sanitize_text_field(get_post_meta($order_id, 'pickup_time', true));
					}
					$pickup_minutes = explode(' - ', $pickup_minutes);

		    		if(!isset($pickup_minutes[1])) {
		    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0]));
		    		} else {

		    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1]));
		    		}

					$order_note .= "<br>".$pickup_time_field_label .': '.$pickup_time_value;
				}

				if((metadata_exists('post', $order_id, 'pickup_location') && get_post_meta($order_id, 'pickup_location', true) !="") || ($order->meta_exists('pickup_location') && $order->get_meta( 'pickup_location', true ) != "")) {
					if($this->hpos) {
						$order_note .= "<br>".$pickup_location_field_label .': '.stripslashes(html_entity_decode(sanitize_text_field($order->get_meta( 'pickup_location', true )), ENT_QUOTES));
					} else {
						$order_note .= "<br>".$pickup_location_field_label .': '.stripslashes(html_entity_decode(sanitize_text_field(get_post_meta($order_id, 'pickup_location', true)), ENT_QUOTES));
					}
					
				}
				
		    	if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {
		    		if($this->hpos) {
						$order_note .= "<br>".$delivery_date_field_label .': '.date($delivery_date_format, strtotime(sanitize_text_field($order->get_meta( 'delivery_date', true ))));
					} else {
						$order_note .= "<br>".$delivery_date_field_label .': '.date($delivery_date_format, strtotime(sanitize_text_field(get_post_meta($order_id, 'delivery_date', true))));
					}
					
			    }
				
			    if((metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id,"delivery_time",true) != "") || ($order->meta_exists('delivery_time') && $order->get_meta( 'delivery_time', true ) != "")) {
					$minutes = sanitize_text_field(get_post_meta($order_id, 'delivery_time', true));
					if(get_post_meta($order_id,"delivery_time",true) == "as-soon-as-possible" || $order->get_meta( 'delivery_time', true ) == "as-soon-as-possible") {
						$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : __("As Soon As Possible","coderockz-woo-delivery");
						$time_value = $as_soon_as_possible_text;
					} else {

						if($this->hpos) {
							$minutes = sanitize_text_field($order->get_meta( 'delivery_time', true ));
						} else {
							$minutes = sanitize_text_field(get_post_meta($order_id, 'delivery_time', true));
						}
						$minutes = explode(' - ', $minutes);

			    		if(!isset($minutes[1])) {
			    			$time_value = date($time_format, strtotime($minutes[0]));
			    		} else {

			    			$time_value = date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1]));

			    		}

		    		}

					$order_note .= "<br>".$delivery_time_field_label .': '.$time_value;
				}

				
		    }

			if((metadata_exists('post', $order_id, 'additional_note') && get_post_meta($order_id, 'additional_note', true) !="") || ($order->meta_exists('additional_note') && $order->get_meta( 'additional_note', true )!= "")) {
				if($this->hpos) {
					$additional_note = $order->get_meta( 'additional_note', true );
				} else {
					$additional_note = get_post_meta($order_id, 'additional_note', true);
				}
				$order_note .= "<br>".$additional_field_field_label .': '. stripslashes(html_entity_decode($additional_note, ENT_QUOTES));
			}
	
			$order = new WC_Order( $order_id );
			if($order_note != "") {
				$order->add_order_note( $order_note );
			}
		}

		wp_send_json_success();
	}

	public function coderockz_woo_delivery_meta_box_get_orders() {

		check_ajax_referer('coderockz_woo_delivery_nonce');
		
		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$today = wp_date('Y-m-d',current_time( 'timestamp', 1 ));
		$order_status_keys = array_keys(wc_get_order_statuses());
		$order_status = ['partially-paid'];
		foreach($order_status_keys as $order_status_key) {
			$order_status[] = substr($order_status_key,3);
		}
		$order_status = array_diff($order_status,['cancelled','failed','refunded']);
		
		$disabled_current_time_slot = (isset($delivery_time_settings['disabled_current_time_slot']) && !empty($delivery_time_settings['disabled_current_time_slot'])) ? $delivery_time_settings['disabled_current_time_slot'] : false;

		$custom_time_slot_settings = get_option('coderockz_woo_delivery_time_slot_settings');
		$enable_custom_time_slot = (isset($custom_time_slot_settings['enable_custom_time_slot']) && !empty($custom_time_slot_settings['enable_custom_time_slot'])) ? $custom_time_slot_settings['enable_custom_time_slot'] : false;

		if(isset($_POST['onlyDeliveryTime']) && $_POST['onlyDeliveryTime']) {
			$order_date = date("Y-m-d", strtotime(sanitize_text_field($_POST['date']))); 
			
		    if($this->hpos) {
		    	$args = array(
			        'limit' => -1,
					'type' => array( 'shop_order' ),
					'date_created' => $order_date,
					'status' => $order_status,
					'meta_query' => array(
			            array(
			                'key'     => 'delivery_type',
			                'value'   => 'delivery',
			                'compare' => '==',
			            ),
			        ),
			        'return' => 'ids'
			    );
		    } else {
		    	$args = array(
			        'limit' => -1,
			        'date_created' => $order_date,
			        'delivery_type' => 'delivery',
			        'status' => $order_status,
			        'return' => 'ids'
			    );
		    }

		} else {
			$delivery_date = date("Y-m-d", strtotime(sanitize_text_field($this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['date']),"delivery"),"delivery"))));
			
		    if($this->hpos) {
		    	$args = array(
			        'limit' => -1,
					'type' => array( 'shop_order' ),
					'status' => $order_status,
					'meta_query' => array(
			            array(
			                'key'     => 'delivery_date',
			                'value'   => $delivery_date,
			                'compare' => '==',
			            ),
			        ),
			        'return' => 'ids'
			    );
		    } else {
		    	$args = array(
			        'limit' => -1,
			        'delivery_date' => $delivery_date,
			        'status' => $order_status,
			        'return' => 'ids'
			    );
		    }
		}
		
	    $order_ids = wc_get_orders( $args );
	    $orderid = isset($_POST['orderId']) && $_POST['orderId'] != "" ? sanitize_text_field($_POST['orderId']) : "";
	    $current_order_for_state_zip = wc_get_order($orderid);
	    $selected_shipping_method = "";
	    if(!is_null($current_order_for_state_zip) && $current_order_for_state_zip != false) {
	    	$current_state = $current_order_for_state_zip->get_shipping_state();
	    	$current_country = $current_order_for_state_zip->get_shipping_country();
			$current_postcode = $current_order_for_state_zip->get_shipping_postcode();
			foreach( $current_order_for_state_zip->get_items( 'shipping' ) as $item_id => $item ){
			    $selected_shipping_method = $item->get_method_id().':'.$item->get_instance_id();
			}
	    }
	    
		$response_delivery = [];

		$delivery_times = [];
		$max_order_per_slot = [];
		$slot_disable_for_sameday = [];
		$slot_disable_for_nextday = [];
		$slot_disable_for_excceed = [];
		$slot_open_specific_date = [];
		$slot_close_specific_date = [];
		$slot_disable_for = [];
		$disable_timeslot['state'] = [];
		$disable_timeslot['postcode'] = [];
		$disable_timeslot['shipping_method'] = [];
		$state_zip_disable_timeslot_all['state'] = [];
		$state_zip_disable_timeslot_all['postcode'] = [];
		$state_zip_disable_timeslot_all['shipping_method'] = [];
		$no_state_zip_disable_timeslot_all['nostatezip'] = [];
		
		if($enable_custom_time_slot && isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0){
	  		foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {

	  			if($individual_time_slot['enable']) {
		  			if($individual_time_slot['enable_split']) {
						$times = explode('-', $key);
						$x = $times[0];
						while($times[1]>$x) {
							$second_time = $x+$individual_time_slot['split_slot_duration'];
							if($second_time > $times[1]) {
								$second_time = $times[1];
							}
							$disable = $individual_time_slot['disable_for'];
							if($individual_time_slot['enable_single_splited_slot']) {
								$slot_disable_for[date("H:i", mktime(0, (int)$x))] = $disable;
							} else {
								$slot_disable_for[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))] = $disable;
							}
							
							$x = $second_time;
						}
					} else {
						$times = explode('-', $key);
						$disable = $individual_time_slot['disable_for'];
						if($individual_time_slot['enable_single_slot']) {
							$slot_disable_for[date("H:i", mktime(0, (int)$times[0]))] = $disable;
						} else {
							$slot_disable_for[date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]))] = $disable;
						}
					}


					if(isset($individual_time_slot['hide_time_slot_current_date']) && $individual_time_slot['hide_time_slot_current_date']) {
						
		  				$times = explode('-', $key);
			  			if($individual_time_slot['enable_split']) {
							$times = explode('-', $key);
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								if($individual_time_slot['enable_single_splited_slot']) {
									$slot_disable_for_sameday[] = date("H:i", mktime(0, (int)$x));
								} else {
									$slot_disable_for_sameday[] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));
								}
								
								$x = $second_time;
							}
						} else {
							$times = explode('-', $key);
							if($individual_time_slot['enable_single_slot']) {
								$slot_disable_for_sameday[] = date("H:i", mktime(0, (int)$times[0]));
							} else {
								$slot_disable_for_sameday[] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));
							}
							
						}

					}

					if(isset($individual_time_slot['hide_time_slot_next_day']) && $individual_time_slot['hide_time_slot_next_day'] != "") {
						
		  				$times = explode('-', $key);
			  			if($individual_time_slot['enable_split']) {
							$times = explode('-', $key);
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								if($individual_time_slot['enable_single_splited_slot']) {
									$slot_disable_for_nextday[] = date("H:i", mktime(0, (int)$x));
								} else {
									$slot_disable_for_nextday[] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));
								}
								
								$x = $second_time;
							}
						} else {
							$times = explode('-', $key);
							if($individual_time_slot['enable_single_slot']) {
								$slot_disable_for_nextday[] = date("H:i", mktime(0, (int)$times[0]));
							} else {
								$slot_disable_for_nextday[] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));
							}
							
						}

					}

					$current_time = (wp_date("G")*60)+wp_date("i");

					if(isset($individual_time_slot['timeslot_closing_time']) && $individual_time_slot['timeslot_closing_time'] != "") {

						if($current_time >= $individual_time_slot['timeslot_closing_time']) {
						
			  				$times = explode('-', $key);
				  			if($individual_time_slot['enable_split']) {
								$times = explode('-', $key);
								$x = $times[0];
								while($times[1]>$x) {
									$second_time = $x+$individual_time_slot['split_slot_duration'];
									if($second_time > $times[1]) {
										$second_time = $times[1];
									}
									if($individual_time_slot['enable_single_splited_slot']) {
										$slot_disable_for_excceed[] = date("H:i", mktime(0, (int)$x));
									} else {
										$slot_disable_for_excceed[] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));
									}
									
									$x = $second_time;
								}
							} else {
								$times = explode('-', $key);
								if($individual_time_slot['enable_single_slot']) {
									$slot_disable_for_excceed[] = date("H:i", mktime(0, (int)$times[0]));
								} else {
									$slot_disable_for_excceed[] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));
								}
								
							}

						}

					}

					if(isset($individual_time_slot['only_specific_date']) && $individual_time_slot['only_specific_date'] != "") {
						
		  				$times = explode('-', $key);
			  			if($individual_time_slot['enable_split']) {
							$times = explode('-', $key);
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								if($individual_time_slot['enable_single_splited_slot']) {
									$slot_open_specific_date[date("H:i", mktime(0, (int)$x))][] = explode(",",$individual_time_slot['only_specific_date']);
								} else {
									$slot_open_specific_date[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))][] = explode(",",$individual_time_slot['only_specific_date']);
								}
								
								$x = $second_time;
							}
						} else {
							$times = explode('-', $key);
							if($individual_time_slot['enable_single_slot']) {
								$slot_open_specific_date[date("H:i", mktime(0, (int)$times[0]))][] = explode(",",$individual_time_slot['only_specific_date']);
							} else {
								$slot_open_specific_date[date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]))][] = explode(",",$individual_time_slot['only_specific_date']);
							}
							
						}

					}

					if(isset($individual_time_slot['only_specific_date_close']) && $individual_time_slot['only_specific_date_close'] != "") {
						
		  				$times = explode('-', $key);
			  			if($individual_time_slot['enable_split']) {
							$times = explode('-', $key);
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								if($individual_time_slot['enable_single_splited_slot']) {
									$slot_close_specific_date[date("H:i", mktime(0, (int)$x))][] = explode(",",$individual_time_slot['only_specific_date_close']);
								} else {
									$slot_close_specific_date[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))][] = explode(",",$individual_time_slot['only_specific_date_close']);
								}
								
								$x = $second_time;
							}
						} else {
							$times = explode('-', $key);
							if($individual_time_slot['enable_single_slot']) {
								$slot_close_specific_date[date("H:i", mktime(0, (int)$times[0]))][] = explode(",",$individual_time_slot['only_specific_date_close']);
							} else {
								$slot_close_specific_date[date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]))][] = explode(",",$individual_time_slot['only_specific_date_close']);
							}
							
						}

					}


					if(isset($individual_time_slot['more_settings']) && !empty($individual_time_slot['more_settings']) && $individual_time_slot['more_settings'] == 'zone') {


						if(isset($individual_time_slot['disable_zone']) && !empty($individual_time_slot['disable_zone'])) {

							global $woocommerce;
							$current_zone_id = '';

							$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
							if(isset($chosen_methods)){
								$shipping_id = $chosen_methods[0];
								$packages    = $woocommerce->cart->get_shipping_packages();
							    foreach ( $packages as $i => $package ) {
							        if ( isset( $package['rates'] ) && isset( $package['rates'][ $shipping_id ] ) ) {
							            $package = $package;
							            break;
							        }
							    }
							    $shipping_zone = WC_Shipping_Zones::get_zone_matching_package( $package );
							    $current_zone_id = (int)$shipping_zone->get_id();
							}


							foreach($individual_time_slot['disable_zone'] as $zone_key => $zone_id) {

								if($zone_id == $current_zone_id) {
									$times = explode('-', $key);
									if($individual_time_slot['enable_split']) {
										$x = $times[0];
										while($times[1]>$x) {
											$second_time = $x+$individual_time_slot['split_slot_duration'];
											if($second_time > $times[1]) {
												$second_time = $times[1];
											}
											$disable = $individual_time_slot['disable_for'];
											if($individual_time_slot['enable_single_splited_slot']) {
												$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x));								
											} else {
												$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
											}
											
											$x = $second_time;
										}
									} else {
										if($individual_time_slot['enable_single_slot']) {
											$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
										} else {
											$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));						
										}						
									}

									break;
								}								

							}

						}

					} else {


						if((isset($individual_time_slot['disable_shipping_method']) && !empty($individual_time_slot['disable_shipping_method']) && in_array($selected_shipping_method,$individual_time_slot['disable_shipping_method']))){
			  				$times = explode('-', $key);

							if($individual_time_slot['enable_split']) {
								
								$x = $times[0];
								while($times[1]>$x) {
									$second_time = $x+$individual_time_slot['split_slot_duration'];
									if($second_time > $times[1]) {
										$second_time = $times[1];
									}
									$disable = $individual_time_slot['disable_for'];
									if($individual_time_slot['enable_single_splited_slot']) {
										$disable_timeslot['shipping_method'][] = date("H:i", mktime(0, (int)$x));								
									} else {
										$disable_timeslot['shipping_method'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
									}
									
									$x = $second_time;
								}
							} else {

								if($individual_time_slot['enable_single_slot']) {
									$disable_timeslot['shipping_method'][] = date("H:i", mktime(0, (int)$times[0]));								
								} else {
									$disable_timeslot['shipping_method'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
								}
								
							}		  				 
			  			} 

			  			if(isset($individual_time_slot['disable_state']) && !empty($individual_time_slot['disable_state']) && (in_array($current_state,$individual_time_slot['disable_state']) || in_array($current_country,$individual_time_slot['disable_state']))){
			  				$times = explode('-', $key);

							if($individual_time_slot['enable_split']) {
								
								$x = $times[0];
								while($times[1]>$x) {
									$second_time = $x+$individual_time_slot['split_slot_duration'];
									if($second_time > $times[1]) {
										$second_time = $times[1];
									}
									$disable = $individual_time_slot['disable_for'];
									if($individual_time_slot['enable_single_splited_slot']) {
										$disable_timeslot['state'][] = date("H:i", mktime(0, (int)$x));								
									} else {
										$disable_timeslot['state'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
									}
									
									$x = $second_time;
								}
							} else {

								if($individual_time_slot['enable_single_slot']) {
									$disable_timeslot['state'][] = date("H:i", mktime(0, (int)$times[0]));								
								} else {
									$disable_timeslot['state'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
								}
								
							}		  				 
			  			} 

			  			if(isset($individual_time_slot['disable_postcode']) && !empty($individual_time_slot['disable_postcode'])){

			  				foreach($individual_time_slot['disable_postcode'] as $postcode_value) {
								$multistep_postal_code = false;
								$between_postal_code = false;
							    if (stripos($postcode_value,'...') !== false) {
							    	$range = explode('...', $postcode_value);
							    	if(stripos($current_postcode,'-') !== false && stripos($range[0],'-') !== false && stripos($range[1],'-') !== false) {
						
										$sub_range_one = (int)str_replace("-", "", $range[0]);
										$sub_range_two = (int)str_replace("-", "", $range[1]);

										$current_zip_range = (int)str_replace("-", "", $current_postcode);
										
										if($this->helper->number_between($current_zip_range, $sub_range_two, $sub_range_one)) {
											$multistep_postal_code = true;
										}
										
									} elseif(stripos($range[0],'*') !== false && stripos($range[1],'*') !== false) {
						
										$sub_range_one = (int)str_replace("*", "", $range[0]);
										$sub_range_two = (int)str_replace("*", "", $range[1]);
										
										if($this->helper->number_between($this->helper->starts_with_starting_numeric($current_postcode), $sub_range_two, $sub_range_one)) {
											$multistep_postal_code = true;
										}
										
									} elseif(stripos($current_postcode,'-') === false && stripos($range[0],'-') === false && stripos($range[1],'-') === false) {
										$alphabet_code = preg_replace("/[^a-zA-Z]+/", "", $range[0]);
										$range[0] = preg_replace("/[^0-9]+/", "", $range[0]);
										$range[1] = preg_replace("/[^0-9]+/", "", $range[1]);
										if($alphabet_code != "" && $this->helper->starts_with(strtolower($current_postcode), strtolower($alphabet_code)) && $this->helper->number_between(preg_replace("/[^0-9]/", "", $current_postcode ), $range[1], $range[0])) {
											$between_postal_code = true;
										} elseif($alphabet_code == "" && $this->helper->number_between($current_postcode, $range[1], $range[0])) {
											$between_postal_code = true;
										}
									}
							    }
							    if (substr($postcode_value, -1) == '*' && stripos($postcode_value,'...') == "") {
							    	if($this->helper->starts_with($current_postcode,substr($postcode_value, 0, -1)) || $this->helper->starts_with(strtolower($current_postcode),substr(strtolower($postcode_value), 0, -1)) || $this->helper->starts_with(strtoupper($current_postcode),substr(strtoupper($postcode_value), 0, -1))) {
							    		$times = explode('-', $key);
										if($individual_time_slot['enable_split']) {
											$x = $times[0];
											while($times[1]>$x) {
												$second_time = $x+$individual_time_slot['split_slot_duration'];
												if($second_time > $times[1]) {
													$second_time = $times[1];
												}
												$disable = $individual_time_slot['disable_for'];
												if($individual_time_slot['enable_single_splited_slot']) {
													$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x));								
												} else {
													$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
												}
												
												$x = $second_time;
											}
										} else {
											if($individual_time_slot['enable_single_slot']) {
												$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
											} else {
												$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));						
											}						
										}
							    	}
							    } elseif($multistep_postal_code || $between_postal_code || ($postcode_value == $current_postcode || str_replace(" ","",$postcode_value) == $current_postcode || strtolower($postcode_value) == strtolower($current_postcode) || str_replace(" ","",strtolower($postcode_value)) == strtolower($current_postcode) )) {
							    	$times = explode('-', $key);
									if($individual_time_slot['enable_split']) {
										$x = $times[0];
										while($times[1]>$x) {
											$second_time = $x+$individual_time_slot['split_slot_duration'];
											if($second_time > $times[1]) {
												$second_time = $times[1];
											}
											$disable = $individual_time_slot['disable_for'];
											if($individual_time_slot['enable_single_splited_slot']) {
												$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x));								
											} else {
												$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
											}
											
											$x = $second_time;
										}
									} else {
										if($individual_time_slot['enable_single_slot']) {
											$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
										} else {
											$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));						
										}						
									}
							    }
							}		  				 
			  			}

		  			}

		  			if((isset($individual_time_slot['disable_state']) && !empty($individual_time_slot['disable_state']))){
		  				$times = explode('-', $key);
						if($individual_time_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_time_slot['disable_for'];
								if($individual_time_slot['enable_single_splited_slot']) {
									$state_zip_disable_timeslot_all['state'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$state_zip_disable_timeslot_all['state'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}
						} else {
							if($individual_time_slot['enable_single_slot']) {
								$state_zip_disable_timeslot_all['state'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$state_zip_disable_timeslot_all['state'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}
							
						}		  				 
		  			} else {
		  				$times = explode('-', $key);
						if($individual_time_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_time_slot['disable_for'];
								if($individual_time_slot['enable_single_splited_slot']) {
									$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}

						} else {
							if($individual_time_slot['enable_single_slot']) {
								$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}

						}		  				 
		  			}


		  			if((isset($individual_time_slot['disable_shipping_method']) && !empty($individual_time_slot['disable_shipping_method']))){
		  				$times = explode('-', $key);
						if($individual_time_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_time_slot['disable_for'];
								if($individual_time_slot['enable_single_splited_slot']) {
									$state_zip_disable_timeslot_all['shipping_method'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$state_zip_disable_timeslot_all['shipping_method'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}
						} else {
							if($individual_time_slot['enable_single_slot']) {
								$state_zip_disable_timeslot_all['shipping_method'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$state_zip_disable_timeslot_all['shipping_method'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}
							
						}		  				 
		  			} else { 
		  				$times = explode('-', $key);
						if($individual_time_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_time_slot['disable_for'];
								if($individual_time_slot['enable_single_splited_slot']) {
									$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}

						} else {
							if($individual_time_slot['enable_single_slot']) {
								$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}

						}		  				 
		  			} 

		  			if((isset($individual_time_slot['disable_postcode']) && !empty($individual_time_slot['disable_postcode']))){
		  				$times = explode('-', $key);
						if($individual_time_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_time_slot['disable_for'];
								if($individual_time_slot['enable_single_splited_slot']) {
									$state_zip_disable_timeslot_all['postcode'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$state_zip_disable_timeslot_all['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}
						} else {
							if($individual_time_slot['enable_single_slot']) {
								$state_zip_disable_timeslot_all['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$state_zip_disable_timeslot_all['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}						
						}		  				 
		  			} else {
		  				$times = explode('-', $key);
						if($individual_time_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_time_slot['disable_for'];
								if($individual_time_slot['enable_single_splited_slot']) {
									$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}

						} else {
							if($individual_time_slot['enable_single_slot']) {
								$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}

						}		  				 
		  			}
	  			}
	  		}
	  	} else {
	  		$time_settings = get_option('coderockz_woo_delivery_time_settings');
	  		$enable_delivery_time = (isset($time_settings['enable_delivery_time']) && !empty($time_settings['enable_delivery_time'])) ? $time_settings['enable_delivery_time'] : false;
	  		if($enable_delivery_time) {
		  		$x = (int)$time_settings['delivery_time_starts'];
		  		$each_time_slot = (isset($time_settings['each_time_slot']) && !empty($time_settings['each_time_slot'])) ? (int)$time_settings['each_time_slot'] : (int)$time_settings['delivery_time_ends']-(int)$time_settings['delivery_time_starts'];
				while((int)$time_settings['delivery_time_ends']>$x) {
					$second_time = $x+$each_time_slot;
					if($second_time > (int)$time_settings['delivery_time_ends']) {
						$second_time = (int)$time_settings['delivery_time_ends'];
					}
					$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));		
					$x = $second_time;
				}
			}	  		
	  	}

	  	$free_up_slot_for_delivery_completed = (isset($delivery_time_settings['free_up_slot_for_delivery_completed']) && !empty($delivery_time_settings['free_up_slot_for_delivery_completed'])) ? $delivery_time_settings['free_up_slot_for_delivery_completed'] : false;
	  	
	  	foreach ($order_ids as $order) {
	  		$order_ref = wc_get_order($order);
	  		if($this->hpos) {	  			
				$date = $order_ref->get_meta( 'delivery_date', true );
				$time = $order_ref->get_meta( 'delivery_time', true );
			} else {
				$date = get_post_meta($order,"delivery_date",true);
				$time = get_post_meta($order,"delivery_time",true);
			}

			if((isset($date) && isset($time)) || isset($time)) {
				if($time !="as-soon-as-possible") {
					if(!$free_up_slot_for_delivery_completed) {
						$delivery_times[] = $time;
					} else {
						if($this->hpos) {	  			
							if(!$order_ref->meta_exists('delivery_status')) {
								$delivery_times[] = $time;
							}
						} else {
							if(!metadata_exists('post', $order, 'delivery_status')) {
								$delivery_times[] = $time;
							}
						}
					}
				}
				
			}

		}

		$unique_delivery_times = array_unique($delivery_times, false);
		$unique_delivery_times = array_values($unique_delivery_times);

		if($enable_custom_time_slot) {
			if(isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0){

				foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {

		  			if($individual_time_slot['enable']) {
			  			$key = preg_replace('/-/', ',', $key);

			  			$key_array = explode(",",$key);

					    $max_order = (isset($individual_time_slot['max_order']) && $individual_time_slot['max_order'] !="") ? sanitize_text_field($individual_time_slot['max_order']) : "";
					
					    if($individual_time_slot['enable_split']) {
							$x = $key_array[0];
							while($key_array[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $key_array[1]) {
									$second_time = $key_array[1];
								}
								if($individual_time_slot['enable_single_splited_slot']) {
									if(in_array(date("H:i", mktime(0, (int)$x)),$unique_delivery_times)) {
										$max_order_per_slot[date("H:i", mktime(0, (int)$x))] = (int)$max_order;
									}
									
								} else {
									if(in_array(date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time)),$unique_delivery_times)) {
									$max_order_per_slot[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))] = (int)$max_order;
									}						
								}
								
								$x = $second_time;
							}

						} else {
							if($individual_time_slot['enable_single_slot']) {
					
								if(in_array(date("H:i", mktime(0, (int)$key_array[0])),$unique_delivery_times)) {
									$max_order_per_slot[date("H:i", mktime(0, (int)$key_array[0]))] = (int)$max_order;
								}
							} else {
								if(in_array(date("H:i", mktime(0, (int)$key_array[0])) . ' - ' . date("H:i", mktime(0, (int)$key_array[1])),$unique_delivery_times)) {
									$max_order_per_slot[date("H:i", mktime(0, (int)$key_array[0])) . ' - ' . date("H:i", mktime(0, (int)$key_array[1]))] = (int)$max_order;	
								}						
							}

						}

					}
				}
			}
		} else {

		    $time_settings = get_option('coderockz_woo_delivery_time_settings');
		    $enable_delivery_time = (isset($time_settings['enable_delivery_time']) && !empty($time_settings['enable_delivery_time'])) ? $time_settings['enable_delivery_time'] : false;
		    if($enable_delivery_time) {
			    $max_order = (isset($time_settings['max_order_per_slot']) && $time_settings['max_order_per_slot'] !="") ? sanitize_text_field($time_settings['max_order_per_slot']) : "";
		  		$x = (int)$time_settings['delivery_time_starts'];
		  		$each_delivery_slot = (isset($time_settings['each_time_slot']) && !empty($time_settings['each_time_slot'])) ? (int)$time_settings['each_time_slot'] : (int)$time_settings['delivery_time_ends']-(int)$time_settings['delivery_time_starts'];
				while((int)$time_settings['delivery_time_ends']>$x) {
					$second_time = $x+$each_delivery_slot;
					if($second_time > (int)$time_settings['delivery_time_ends']) {
						$second_time = (int)$time_settings['delivery_time_ends'];
					}
					if(in_array(date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time)),$unique_delivery_times)) {
						$max_order_per_slot[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))] = (int)$max_order;
					}		
					$x = $second_time;
				}
			}
		}

		$response_delivery = [
			"delivery_times" => $delivery_times,
			"max_order_per_slot" => $max_order_per_slot,
			"slot_disable_for" => $slot_disable_for,
			'disabled_current_time_slot' => $disabled_current_time_slot,
			'disable_timeslot' => $disable_timeslot,
			'state_zip_disable_timeslot_all' => $state_zip_disable_timeslot_all,
			'no_state_zip_disable_timeslot_all' => $no_state_zip_disable_timeslot_all,
			'slot_disable_for_sameday' => $slot_disable_for_sameday,
			'slot_disable_for_nextday' => $slot_disable_for_nextday,
			'slot_disable_for_excceed' => $slot_disable_for_excceed,
			'slot_open_specific_date' => $slot_open_specific_date,
			'slot_close_specific_date' => $slot_close_specific_date
		];


		$formated_date = date('Y-m-d H:i:s', strtotime($this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['date']),"delivery"),"delivery")));
		$formated_date_obj = new DateTime($formated_date);
		$formated_date = $formated_date_obj->format("w");
		$formated_delivery_date_selected = $formated_date_obj->format("Y-m-d");

		$current_time = (wp_date("G")*60)+wp_date("i");

		$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');

		$enable_conditional_delivery_fee = isset($delivery_fee_settings['enable_conditional_delivery_fee']) && !empty($delivery_fee_settings['enable_conditional_delivery_fee']) ? $delivery_fee_settings['enable_conditional_delivery_fee'] : false;
		$have_conditional_delivery = false;
		$conditional_delivery_fee_duration= "";
		$disable_inter_timeslot_conditional = false;
		$only_conditional_delivery_for_fee = false;
		$find_conditional_shipping_method = false;
		$hide_free_shipping_method = false;
		$free_shipping_method_title = "No Free Shipping Method";
		if($enable_conditional_delivery_fee && (isset($delivery_fee_settings['conditional_delivery_time_starts']) && ($delivery_fee_settings['conditional_delivery_time_starts'] !='' || $delivery_fee_settings['conditional_delivery_time_starts'] == 0)) && (isset($delivery_fee_settings['conditional_delivery_time_ends']) && $delivery_fee_settings['conditional_delivery_time_ends'] !='') && ($current_time >= (int)$delivery_fee_settings['conditional_delivery_time_starts'] && (int)$delivery_fee_settings['conditional_delivery_time_ends'] >= $current_time) && (isset($delivery_fee_settings['conditional_delivery_fee_duration']) && !empty($delivery_fee_settings['conditional_delivery_fee_duration'])) && ((isset($delivery_fee_settings['conditional_delivery_fee']) && !empty($delivery_fee_settings['conditional_delivery_fee'])) || (isset($delivery_fee_settings['conditional_delivery_shipping_method']) && !empty($delivery_fee_settings['conditional_delivery_shipping_method'])))) {
			$have_conditional_delivery = true;
			$conditional_delivery_fee_duration = (int)$delivery_fee_settings['conditional_delivery_fee_duration'];
			$disable_inter_timeslot_conditional = (isset($delivery_fee_settings['disable_inter_timeslot_conditional']) && !empty($delivery_fee_settings['disable_inter_timeslot_conditional'])) ? $delivery_fee_settings['disable_inter_timeslot_conditional'] : false;

		}

		if($enable_conditional_delivery_fee && (isset($delivery_fee_settings['conditional_delivery_time_starts']) && ($delivery_fee_settings['conditional_delivery_time_starts'] !='' || $delivery_fee_settings['conditional_delivery_time_starts'] == 0)) && (isset($delivery_fee_settings['conditional_delivery_time_ends']) && $delivery_fee_settings['conditional_delivery_time_ends'] !='') && ($current_time >= (int)$delivery_fee_settings['conditional_delivery_time_starts'] && (int)$delivery_fee_settings['conditional_delivery_time_ends'] >= $current_time) && (isset($delivery_fee_settings['conditional_delivery_fee_duration']) && !empty($delivery_fee_settings['conditional_delivery_fee_duration'])) && (isset($delivery_fee_settings['conditional_delivery_fee']) && !empty($delivery_fee_settings['conditional_delivery_fee'])) && $formated_delivery_date_selected == wp_date('Y-m-d',current_time( 'timestamp', 1 )) ) {
			$only_conditional_delivery_for_fee = true;

		}

		$orderid = isset($_POST['orderId']) && $_POST['orderId'] != "" ? sanitize_text_field($_POST['orderId']) : "";

		$order = wc_get_order( $orderid );

		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');

		$enable_free_shipping_current_day = (isset($delivery_option_settings['enable_free_shipping_current_day']) && !empty($delivery_option_settings['enable_free_shipping_current_day'])) ? $delivery_option_settings['enable_free_shipping_current_day'] : false;

		$disable_free_shipping_current_day = (isset($delivery_option_settings['disable_free_shipping_current_day']) && !empty($delivery_option_settings['disable_free_shipping_current_day'])) ? $delivery_option_settings['disable_free_shipping_current_day'] : false;

		$hide_free_shipping_weekday = (isset($delivery_option_settings['hide_free_shipping_weekday']) && !empty($delivery_option_settings['hide_free_shipping_weekday'])) ? $delivery_option_settings['hide_free_shipping_weekday'] : array();

		$show_free_shipping_only_at = (isset($delivery_option_settings['show_free_shipping_only_at']) && !empty($delivery_option_settings['show_free_shipping_only_at'])) ? $delivery_option_settings['show_free_shipping_only_at'] : array();

	    $hide_free_shipping_at = (isset($delivery_option_settings['hide_free_shipping_at']) && !empty($delivery_option_settings['hide_free_shipping_at'])) ? $delivery_option_settings['hide_free_shipping_at'] : array();

		foreach( $order->get_items( 'shipping' ) as $item_id => $item ) {

		    $shipping_zone = WC_Shipping_Zones::get_zone_by( 'instance_id', $item->get_instance_id() );

		    $shipping_methods = $shipping_zone->get_shipping_methods();

		    foreach ( $shipping_methods as $instance_id => $shipping_method ) {
		    	
		    	$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');

		    	if($shipping_method->is_enabled()) {
			    	
		    		if($shipping_method->id != 'local_pickup') {
		        		if($shipping_method->id == 'free_shipping') {

		        			if( ($enable_free_shipping_current_day && $formated_delivery_date_selected != $today) || ($disable_free_shipping_current_day && $formated_delivery_date_selected == $today) || in_array($formated_date, $hide_free_shipping_weekday) || (!empty($show_free_shipping_only_at) && !in_array($formated_delivery_date_selected, $show_free_shipping_only_at)) || (!empty($hide_free_shipping_at) && in_array($formated_delivery_date_selected, $hide_free_shipping_at))) {
		        				$hide_free_shipping_method = true;
		        			}

		        			$free_shipping_method_title = $shipping_method->get_title();

		        		} 

		        	}

		        	$conditional_delivery_shipping_method_name = (isset($delivery_fee_settings['conditional_delivery_shipping_method']) && $delivery_fee_settings['conditional_delivery_shipping_method'] != "") ? $delivery_fee_settings['conditional_delivery_shipping_method'] : "";

			    	if($shipping_method->get_title() == $conditional_delivery_shipping_method_name && wp_date('Y-m-d',current_time( 'timestamp', 1 )) == $formated_delivery_date_selected) {
	    				$find_conditional_shipping_method = true;
	    				/*break;*/
		        	}
	        	}

		    }
		}

		$response_for_all = [
			"formated_date" => $formated_date,
			"current_time" => $current_time,
			"formated_delivery_date_selected" => $formated_delivery_date_selected,
			"have_conditional_delivery" => $have_conditional_delivery,
			"conditional_delivery_fee_duration" => $conditional_delivery_fee_duration,
			"disable_inter_timeslot_conditional" => $disable_inter_timeslot_conditional,
			"find_conditional_shipping_method" => $find_conditional_shipping_method,
			"only_conditional_delivery_for_fee" => $only_conditional_delivery_for_fee,
			"hide_free_shipping_method" => $hide_free_shipping_method,
			"free_shipping_method_title" => $free_shipping_method_title
		];

		$response = array_merge($response_delivery, $response_for_all);
		$response = json_encode($response);
		wp_send_json_success($response);

	}

	public function coderockz_woo_delivery_meta_box_get_orders_pickup() {

		check_ajax_referer('coderockz_woo_delivery_nonce');
		
		$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
		$order_status_keys = array_keys(wc_get_order_statuses());
		$order_status = ['partially-paid'];
		foreach($order_status_keys as $order_status_key) {
			$order_status[] = substr($order_status_key,3);
		}
		$order_status = array_diff($order_status,['cancelled','failed','refunded']);

		$given_location = (isset($_POST['givenLocation']) && $_POST['givenLocation'] !="") ? sanitize_text_field($_POST['givenLocation']) : "";

		$pickup_disabled_current_time_slot = (isset($delivery_pickup_settings['disabled_current_pickup_time_slot']) && !empty($delivery_pickup_settings['disabled_current_pickup_time_slot'])) ? $delivery_pickup_settings['disabled_current_pickup_time_slot'] : false;

		$max_pickup_consider_location = (isset($delivery_pickup_settings['max_pickup_consider_location']) && !empty($delivery_pickup_settings['max_pickup_consider_location'])) ? $delivery_pickup_settings['max_pickup_consider_location'] : false;

		$pickup_location_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
		$enable_pickup_location = (isset($pickup_location_settings['enable_pickup_location']) && !empty($pickup_location_settings['enable_pickup_location'])) ? $pickup_location_settings['enable_pickup_location'] : false;	

		$custom_pickup_slot_settings = get_option('coderockz_woo_delivery_pickup_slot_settings');
		$enable_custom_pickup_slot = (isset($custom_pickup_slot_settings['enable_custom_pickup_slot']) && !empty($custom_pickup_slot_settings['enable_custom_pickup_slot'])) ? $custom_pickup_slot_settings['enable_custom_pickup_slot'] : false;
		
		$max_pickup_slot_individual_location = false;

		if($enable_pickup_location && $max_pickup_consider_location) {

			$max_pickup_slot_individual_location = true;

			if(isset($_POST['onlyPickupTime']) && $_POST['onlyPickupTime']) {
				$order_date = date("Y-m-d", strtotime(sanitize_text_field($_POST['date']))); 
				
			    if($this->hpos) {
			    	$args = array(
				        'limit' => -1,
						'type' => array( 'shop_order' ),
						'date_created' => $order_date,
						'status' => $order_status,
						'meta_query' => array(
				            array(
				                'key'     => 'delivery_type',
				                'value'   => 'pickup',
				                'compare' => '==',
				            ),
				            array(
				                'key'     => 'pickup_location',
				                'value'   => $given_location,
				                'compare' => '==',
				            ),
				        ),
				        'return' => 'ids'
				    );
			    } else {
			    	$args = array(
				        'limit' => -1,
				        'date_created' => $order_date,
				        'delivery_type' => 'pickup',
				        'pickup_location' => $given_location,
				        'status' => $order_status,
				        'return' => 'ids'
				    );
			    }

			} else {
				$pickup_date = date("Y-m-d", strtotime(sanitize_text_field($this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['date']),"pickup"),"pickup"))));

			    if($this->hpos) {
			    	$args = array(
				        'limit' => -1,
						'type' => array( 'shop_order' ),
						'status' => $order_status,
						'meta_query' => array(
				            array(
				                'key'     => 'pickup_date',
				                'value'   => $pickup_date,
				                'compare' => '==',
				            ),
				            array(
				                'key'     => 'pickup_location',
				                'value'   => $given_location,
				                'compare' => '==',
				            ),
				        ),
				        'return' => 'ids'
				    );
			    } else {
			    	$args = array(
				        'limit' => -1,
				        'pickup_date' => $pickup_date,
				        'pickup_location' => $given_location,
				        'status' => $order_status,
				        'return' => 'ids'
				    );
			    }
			}

		} else {
			if(isset($_POST['onlyPickupTime']) && $_POST['onlyPickupTime']) {
				$order_date = date("Y-m-d", strtotime(sanitize_text_field($_POST['date']))); 
				
			    if($this->hpos) {
			    	$args = array(
				        'limit' => -1,
						'type' => array( 'shop_order' ),
						'date_created' => $order_date,
						'status' => $order_status,
						'meta_query' => array(
				            array(
				                'key'     => 'delivery_type',
				                'value'   => 'pickup',
				                'compare' => '==',
				            ),
				        ),
				        'return' => 'ids'
				    );
			    } else {
			    	$args = array(
				        'limit' => -1,
				        'date_created' => $order_date,
				        'delivery_type' => 'pickup',
				        'status' => $order_status,
				        'return' => 'ids'
				    );
			    }

			} else {
				$pickup_date = date("Y-m-d", strtotime(sanitize_text_field($this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['date']),"pickup"),"pickup"))));
				
			    if($this->hpos) {
			    	$args = array(
				        'limit' => -1,
						'type' => array( 'shop_order' ),
						'status' => $order_status,
						'meta_query' => array(
				            array(
				                'key'     => 'pickup_date',
				                'value'   => $pickup_date,
				                'compare' => '==',
				            ),
				        ),
				        'return' => 'ids'
				    );
			    } else {
			    	$args = array(
				        'limit' => -1,
				        'pickup_date' => $pickup_date,
				        'status' => $order_status,
				        'return' => 'ids'
				    );
			    }
			}
		}
		
	    $order_ids = wc_get_orders( $args );

	    $pickup_location_disable_for = [];
		$pickup_delivery_locations = [];
		$pickup_max_order_per_location = [];
		$pickup_location_only_specific_date_close = [];
		$pickup_location_only_specific_date_show = [];

	    $orderid = isset($_POST['orderId']) && $_POST['orderId'] != "" ? sanitize_text_field($_POST['orderId']) : "";
	    $current_order_for_state_zip = wc_get_order($orderid);
	    $selected_shipping_method = "";
	    if(!is_null($current_order_for_state_zip) && $current_order_for_state_zip != false) {
	    	$current_state = $current_order_for_state_zip->get_shipping_state();
	    	$current_country = $current_order_for_state_zip->get_shipping_country();
			$current_postcode = $current_order_for_state_zip->get_shipping_postcode();
			foreach( $current_order_for_state_zip->get_items( 'shipping' ) as $item_id => $item ){
			    $selected_shipping_method = $item->get_method_id().':'.$item->get_instance_id();
			}

	    }

		$response_pickup = [];
		$pickup_delivery_times = [];
		$pickup_max_order_per_slot = [];
		$pickup_slot_disable_for = [];
		$pickup_slot_disable_for_sameday = [];
		$pickup_slot_disable_for_nextday = [];
		$pickup_slot_disable_for_excceed = [];
		$pickup_slot_open_specific_date = [];
		$pickup_slot_close_specific_date = [];
		$pickup_disable_timeslot['state'] = [];
		$pickup_disable_timeslot['postcode'] = [];
		$pickup_disable_timeslot['shipping_method'] = [];
		$pickup_state_zip_disable_timeslot_all['state'] = [];
		$pickup_state_zip_disable_timeslot_all['postcode'] = [];
		$pickup_state_zip_disable_timeslot_all['shipping_method'] = [];
		$pickup_no_state_zip_disable_timeslot_all['nostatezip'] = [];
		$pickup_disable_timeslot_location[] = [];
		$pickup_location_disable_timeslot_all = [];
		$pickup_no_location_disable_timeslot_all[] = [];
		$detect_pickup_location_hide = false;
		
		if($enable_custom_pickup_slot && isset($custom_pickup_slot_settings['time_slot']) && count($custom_pickup_slot_settings['time_slot'])>0){
	  		foreach($custom_pickup_slot_settings['time_slot'] as $key => $individual_pickup_slot) {

	  			if($individual_pickup_slot['enable']) {
		  			if($individual_pickup_slot['enable_split']) {
						$times = explode('-', $key);
						$x = $times[0];
						while($times[1]>$x) {
							$second_time = $x+$individual_pickup_slot['split_slot_duration'];
							if($second_time > $times[1]) {
								$second_time = $times[1];
							}
							$disable = $individual_pickup_slot['disable_for'];
							if($individual_pickup_slot['enable_single_splited_slot']) {
								$pickup_slot_disable_for[date("H:i", mktime(0, (int)$x))] = $disable;
							} else {
								$pickup_slot_disable_for[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))] = $disable;
							}
							
							$x = $second_time;
						}
					} else {
						$times = explode('-', $key);
						$disable = $individual_pickup_slot['disable_for'];
						if($individual_pickup_slot['enable_single_slot']) {
							$pickup_slot_disable_for[date("H:i", mktime(0, (int)$times[0]))] = $disable;
						} else {
							$pickup_slot_disable_for[date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]))] = $disable;
						}
					}

					if(isset($individual_pickup_slot['hide_time_slot_current_date']) && $individual_pickup_slot['hide_time_slot_current_date']) {
						
		  				$times = explode('-', $key);
			  			if($individual_pickup_slot['enable_split']) {
							$times = explode('-', $key);
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								if($individual_pickup_slot['enable_single_splited_slot']) {
									$pickup_slot_disable_for_sameday[] = date("H:i", mktime(0, (int)$x));
								} else {
									$pickup_slot_disable_for_sameday[] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));
								}
								
								$x = $second_time;
							}
						} else {
							$times = explode('-', $key);
							if($individual_pickup_slot['enable_single_slot']) {
								$pickup_slot_disable_for_sameday[] = date("H:i", mktime(0, (int)$times[0]));
							} else {
								$pickup_slot_disable_for_sameday[] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));
							}
							
						}

					}

					if(isset($individual_pickup_slot['hide_time_slot_next_day']) && $individual_pickup_slot['hide_time_slot_next_day']) {
						
		  				$times = explode('-', $key);
			  			if($individual_pickup_slot['enable_split']) {
							$times = explode('-', $key);
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								if($individual_pickup_slot['enable_single_splited_slot']) {
									$pickup_slot_disable_for_nextday[] = date("H:i", mktime(0, (int)$x));
								} else {
									$pickup_slot_disable_for_nextday[] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));
								}
								
								$x = $second_time;
							}
						} else {
							$times = explode('-', $key);
							if($individual_pickup_slot['enable_single_slot']) {
								$pickup_slot_disable_for_nextday[] = date("H:i", mktime(0, (int)$times[0]));
							} else {
								$pickup_slot_disable_for_nextday[] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));
							}
							
						}

					}

					$current_time = (date("G")*60)+date("i");

					if(isset($individual_pickup_slot['timeslot_closing_time']) && $individual_pickup_slot['timeslot_closing_time'] != "") {

						if($current_time >= $individual_pickup_slot['timeslot_closing_time']) {
						
			  				$times = explode('-', $key);
				  			if($individual_pickup_slot['enable_split']) {
								$times = explode('-', $key);
								$x = $times[0];
								while($times[1]>$x) {
									$second_time = $x+$individual_pickup_slot['split_slot_duration'];
									if($second_time > $times[1]) {
										$second_time = $times[1];
									}
									if($individual_pickup_slot['enable_single_splited_slot']) {
										$pickup_slot_disable_for_excceed[] = date("H:i", mktime(0, (int)$x));
									} else {
										$pickup_slot_disable_for_excceed[] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));
									}
									
									$x = $second_time;
								}
							} else {
								$times = explode('-', $key);
								if($individual_pickup_slot['enable_single_slot']) {
									$pickup_slot_disable_for_excceed[] = date("H:i", mktime(0, (int)$times[0]));
								} else {
									$pickup_slot_disable_for_excceed[] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));
								}
								
							}

						}

					}

					if(isset($individual_pickup_slot['only_specific_date']) && $individual_pickup_slot['only_specific_date'] != "") {
						
		  				$times = explode('-', $key);
			  			if($individual_pickup_slot['enable_split']) {
							$times = explode('-', $key);
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								if($individual_pickup_slot['enable_single_splited_slot']) {
									$pickup_slot_open_specific_date[date("H:i", mktime(0, (int)$x))][] = explode(",",$individual_pickup_slot['only_specific_date']);
								} else {
									$pickup_slot_open_specific_date[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))][] = explode(",",$individual_pickup_slot['only_specific_date']);
								}
								
								$x = $second_time;
							}
						} else {
							$times = explode('-', $key);
							if($individual_pickup_slot['enable_single_slot']) {
								$pickup_slot_open_specific_date[date("H:i", mktime(0, (int)$times[0]))][] = explode(",",$individual_pickup_slot['only_specific_date']);
							} else {
								$pickup_slot_open_specific_date[date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]))][] = explode(",",$individual_pickup_slot['only_specific_date']);
							}
							
						}

					}

					if(isset($individual_pickup_slot['only_specific_date_close']) && $individual_pickup_slot['only_specific_date_close'] != "") {
						
		  				$times = explode('-', $key);
			  			if($individual_pickup_slot['enable_split']) {
							$times = explode('-', $key);
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								if($individual_pickup_slot['enable_single_splited_slot']) {
									$pickup_slot_close_specific_date[date("H:i", mktime(0, (int)$x))][] = explode(",",$individual_pickup_slot['only_specific_date_close']);
								} else {
									$pickup_slot_close_specific_date[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))][] = explode(",",$individual_pickup_slot['only_specific_date_close']);
								}
								
								$x = $second_time;
							}
						} else {
							$times = explode('-', $key);
							if($individual_pickup_slot['enable_single_slot']) {
								$pickup_slot_close_specific_date[date("H:i", mktime(0, (int)$times[0]))][] = explode(",",$individual_pickup_slot['only_specific_date_close']);
							} else {
								$pickup_slot_close_specific_date[date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]))][] = explode(",",$individual_pickup_slot['only_specific_date_close']);
							}
							
						}

					}

					if(isset($individual_pickup_slot['more_settings']) && !empty($individual_pickup_slot['more_settings']) && $individual_pickup_slot['more_settings'] == 'zone') {


						if(isset($individual_pickup_slot['disable_zone']) && !empty($individual_pickup_slot['disable_zone'])) {

							global $woocommerce;
							$current_zone_id = '';

							$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
							if(isset($chosen_methods)){
								$shipping_id = $chosen_methods[0];
								$packages    = $woocommerce->cart->get_shipping_packages();
							    foreach ( $packages as $i => $package ) {
							        if ( isset( $package['rates'] ) && isset( $package['rates'][ $shipping_id ] ) ) {
							            $package = $package;
							            break;
							        }
							    }
							    $shipping_zone = WC_Shipping_Zones::get_zone_matching_package( $package );
							    $current_zone_id = (int)$shipping_zone->get_id();
							}

							foreach($individual_pickup_slot['disable_zone'] as $zone_key => $zone_id) {

								if($zone_id == $current_zone_id) {
									$times = explode('-', $key);
									if($individual_pickup_slot['enable_split']) {
										$x = $times[0];
										while($times[1]>$x) {
											$second_time = $x+$individual_pickup_slot['split_slot_duration'];
											if($second_time > $times[1]) {
												$second_time = $times[1];
											}
											$disable = $individual_pickup_slot['disable_for'];
											if($individual_pickup_slot['enable_single_splited_slot']) {
												$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x));								
											} else {
												$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
											}
											
											$x = $second_time;
										}
									} else {
										if($individual_pickup_slot['enable_single_slot']) {
											$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
										} else {
											$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));						
										}						
									}

									break;
								}								

							}

						}

					} else {

						if(isset($individual_pickup_slot['disable_shipping_method']) && !empty($individual_pickup_slot['disable_shipping_method']) && in_array($selected_shipping_method,$individual_pickup_slot['disable_shipping_method'])){
			  				$times = explode('-', $key);

							if($individual_pickup_slot['enable_split']) {
								
								$x = $times[0];
								while($times[1]>$x) {
									$second_time = $x+$individual_pickup_slot['split_slot_duration'];
									if($second_time > $times[1]) {
										$second_time = $times[1];
									}
									$disable = $individual_pickup_slot['disable_for'];
									if($individual_pickup_slot['enable_single_splited_slot']) {
										$pickup_disable_timeslot['shipping_method'][] = date("H:i", mktime(0, (int)$x));								
									} else {
										$pickup_disable_timeslot['shipping_method'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
									}
									
									$x = $second_time;
								}
							} else {

								if($individual_pickup_slot['enable_single_slot']) {
									$pickup_disable_timeslot['shipping_method'][] = date("H:i", mktime(0, (int)$times[0]));								
								} else {
									$pickup_disable_timeslot['shipping_method'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
								}
								
							}		  				 
			  			} 

			  			if(isset($individual_pickup_slot['disable_state']) && !empty($individual_pickup_slot['disable_state']) && (in_array($current_state,$individual_pickup_slot['disable_state']) || in_array($current_country,$individual_pickup_slot['disable_state']))){
			  				$times = explode('-', $key);

							if($individual_pickup_slot['enable_split']) {
								
								$x = $times[0];
								while($times[1]>$x) {
									$second_time = $x+$individual_pickup_slot['split_slot_duration'];
									if($second_time > $times[1]) {
										$second_time = $times[1];
									}
									$disable = $individual_pickup_slot['disable_for'];
									if($individual_pickup_slot['enable_single_splited_slot']) {
										$pickup_disable_timeslot['state'][] = date("H:i", mktime(0, (int)$x));								
									} else {
										$pickup_disable_timeslot['state'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
									}
									
									$x = $second_time;
								}
							} else {

								if($individual_pickup_slot['enable_single_slot']) {
									$pickup_disable_timeslot['state'][] = date("H:i", mktime(0, (int)$times[0]));								
								} else {
									$pickup_disable_timeslot['state'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
								}
								
							}		  				 
			  			} 

			  			if(isset($individual_pickup_slot['disable_postcode']) && !empty($individual_pickup_slot['disable_postcode'])){

			  				foreach($individual_pickup_slot['disable_postcode'] as $postcode_value) {
								$multistep_postal_code = false;
								$between_postal_code = false;
								/*$postcode_range = [];*/
							    if (stripos($postcode_value,'...') !== false) {
							    	$range = explode('...', $postcode_value);
							    	if(stripos($current_postcode,'-') !== false && stripos($range[0],'-') !== false && stripos($range[1],'-') !== false) {
						
										$sub_range_one = (int)str_replace("-", "", $range[0]);
										$sub_range_two = (int)str_replace("-", "", $range[1]);

										$current_zip_range = (int)str_replace("-", "", $current_postcode);
										
										if($this->helper->number_between($current_zip_range, $sub_range_two, $sub_range_one)) {
											$multistep_postal_code = true;
										}
										
									} elseif(stripos($range[0],'*') !== false && stripos($range[1],'*') !== false) {
						
										$sub_range_one = (int)str_replace("*", "", $range[0]);
										$sub_range_two = (int)str_replace("*", "", $range[1]);
										
										if($this->helper->number_between($this->helper->starts_with_starting_numeric($current_postcode), $sub_range_two, $sub_range_one)) {
											$multistep_postal_code = true;
										}
										
									} elseif(stripos($current_postcode,'-') === false && stripos($range[0],'-') === false && stripos($range[1],'-') === false) {
										$alphabet_code = preg_replace("/[^a-zA-Z]+/", "", $range[0]);
										$range[0] = preg_replace("/[^0-9]+/", "", $range[0]);
										$range[1] = preg_replace("/[^0-9]+/", "", $range[1]);
										if($alphabet_code != "" && $this->helper->starts_with(strtolower($current_postcode), strtolower($alphabet_code)) && $this->helper->number_between(preg_replace("/[^0-9]/", "", $current_postcode ), $range[1], $range[0])) {
											$between_postal_code = true;
										} elseif($alphabet_code == "" /*&& is_numeric($current_postcode)*/ && $this->helper->number_between($current_postcode, $range[1], $range[0])) {
											$between_postal_code = true;
										}
									}
							    }
							    if (substr($postcode_value, -1) == '*' && stripos($postcode_value,'...') == "") {
							    	if($this->helper->starts_with($current_postcode,substr($postcode_value, 0, -1)) || $this->helper->starts_with(strtolower($current_postcode),substr(strtolower($postcode_value), 0, -1)) || $this->helper->starts_with(strtoupper($current_postcode),substr(strtoupper($postcode_value), 0, -1))) {
							    		$times = explode('-', $key);
										if($individual_pickup_slot['enable_split']) {
											$x = $times[0];
											while($times[1]>$x) {
												$second_time = $x+$individual_pickup_slot['split_slot_duration'];
												if($second_time > $times[1]) {
													$second_time = $times[1];
												}
												$disable = $individual_pickup_slot['disable_for'];
												if($individual_pickup_slot['enable_single_splited_slot']) {
													$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x));								
												} else {
													$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
												}
												
												$x = $second_time;
											}
										} else {
											if($individual_pickup_slot['enable_single_slot']) {
												$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
											} else {
												$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));						
											}						
										}
							    	}
							    } elseif($multistep_postal_code || $between_postal_code || ($postcode_value == $current_postcode || str_replace(" ","",$postcode_value) == $current_postcode || strtolower($postcode_value) == strtolower($current_postcode) || str_replace(" ","",strtolower($postcode_value)) == strtolower($current_postcode) )) {
							    	$times = explode('-', $key);
									if($individual_pickup_slot['enable_split']) {
										$x = $times[0];
										while($times[1]>$x) {
											$second_time = $x+$individual_pickup_slot['split_slot_duration'];
											if($second_time > $times[1]) {
												$second_time = $times[1];
											}
											$disable = $individual_pickup_slot['disable_for'];
											if($individual_pickup_slot['enable_single_splited_slot']) {
												$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x));								
											} else {
												$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
											}
											
											$x = $second_time;
										}
									} else {
										if($individual_pickup_slot['enable_single_slot']) {
											$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
										} else {
											$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));						
										}						
									}
							    }
							}		  				 
			  			}

		  			}

		  			if((isset($individual_pickup_slot['disable_shipping_method']) && !empty($individual_pickup_slot['disable_shipping_method']))){
		  				$times = explode('-', $key);
						if($individual_pickup_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_pickup_slot['disable_for'];
								if($individual_pickup_slot['enable_single_splited_slot']) {
									$pickup_state_zip_disable_timeslot_all['shipping_method'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$pickup_state_zip_disable_timeslot_all['shipping_method'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}
						} else {
							if($individual_pickup_slot['enable_single_slot']) {
								$pickup_state_zip_disable_timeslot_all['shipping_method'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$pickup_state_zip_disable_timeslot_all['shipping_method'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}
							
						}		  				 
		  			} else {
		  				$times = explode('-', $key);
						if($individual_pickup_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_pickup_slot['disable_for'];
								if($individual_pickup_slot['enable_single_splited_slot']) {
									$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}

						} else {
							if($individual_pickup_slot['enable_single_slot']) {
								$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}

						}
		  			}

		  			if((isset($individual_pickup_slot['disable_state']) && !empty($individual_pickup_slot['disable_state']))){
		  				$times = explode('-', $key);
						if($individual_pickup_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_pickup_slot['disable_for'];
								if($individual_pickup_slot['enable_single_splited_slot']) {
									$pickup_state_zip_disable_timeslot_all['state'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$pickup_state_zip_disable_timeslot_all['state'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}
						} else {
							if($individual_pickup_slot['enable_single_slot']) {
								$pickup_state_zip_disable_timeslot_all['state'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$pickup_state_zip_disable_timeslot_all['state'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}
							
						}		  				 
		  			} else {
		  				$times = explode('-', $key);
						if($individual_pickup_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_pickup_slot['disable_for'];
								if($individual_pickup_slot['enable_single_splited_slot']) {
									$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}

						} else {
							if($individual_pickup_slot['enable_single_slot']) {
								$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}

						}

		  			}

		  			if((isset($individual_pickup_slot['disable_postcode']) && !empty($individual_pickup_slot['disable_postcode']))){
		  				$times = explode('-', $key);
						if($individual_pickup_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_pickup_slot['disable_for'];
								if($individual_pickup_slot['enable_single_splited_slot']) {
									$pickup_state_zip_disable_timeslot_all['postcode'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$pickup_state_zip_disable_timeslot_all['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}
						} else {
							if($individual_pickup_slot['enable_single_slot']) {
								$pickup_state_zip_disable_timeslot_all['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$pickup_state_zip_disable_timeslot_all['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}						
						}		  				 
		  			} else {
		  				$times = explode('-', $key);
						if($individual_pickup_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_pickup_slot['disable_for'];
								if($individual_pickup_slot['enable_single_splited_slot']) {
									$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}

						} else {
							if($individual_pickup_slot['enable_single_slot']) {
								$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}

						}

		  			}

		  			if(isset($individual_pickup_slot['hide_for_pickup_location']) && !empty($individual_pickup_slot['hide_for_pickup_location']) && $enable_pickup_location) {
		  				$detect_pickup_location_hide = true;
		  				if((isset($individual_pickup_slot['hide_for_pickup_location']) && !empty($individual_pickup_slot['hide_for_pickup_location']) && in_array($given_location,$individual_pickup_slot['hide_for_pickup_location']))){
			  				$times = explode('-', $key);

							if($individual_pickup_slot['enable_split']) {
								
								$x = $times[0];
								while($times[1]>$x) {
									$second_time = $x+$individual_pickup_slot['split_slot_duration'];
									if($second_time > $times[1]) {
										$second_time = $times[1];
									}
									if($individual_pickup_slot['enable_single_splited_slot']) {
										$pickup_disable_timeslot_location['location'][] = date("H:i", mktime(0, (int)$x));								
									} else {
										$pickup_disable_timeslot_location['location'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
									}
									
									$x = $second_time;
								}
							} else {

								if($individual_pickup_slot['enable_single_slot']) {
									$pickup_disable_timeslot_location['location'][] = date("H:i", mktime(0, (int)$times[0]));								
								} else {
									$pickup_disable_timeslot_location['location'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
								}
								
							}		  				 
			  			}

			  			if((isset($individual_pickup_slot['hide_for_pickup_location']) && !empty($individual_pickup_slot['hide_for_pickup_location']))){
		  					$times = explode('-', $key);
							if($individual_pickup_slot['enable_split']) {
								$x = $times[0];
								while($times[1]>$x) {
									$second_time = $x+$individual_pickup_slot['split_slot_duration'];
									if($second_time > $times[1]) {
										$second_time = $times[1];
									}
									if($individual_pickup_slot['enable_single_splited_slot']) {
										$pickup_location_disable_timeslot_all['location'][] = date("H:i", mktime(0, (int)$x));								
									} else {
										$pickup_location_disable_timeslot_all['location'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
									}
									
									$x = $second_time;
								}
							} else {
								if($individual_pickup_slot['enable_single_slot']) {
									$pickup_location_disable_timeslot_all['location'][] = date("H:i", mktime(0, (int)$times[0]));								
								} else {
									$pickup_location_disable_timeslot_all['location'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
								}
								
							}		  				 
		  				} else {
			  				$times = explode('-', $key);
							if($individual_pickup_slot['enable_split']) {
								$x = $times[0];
								while($times[1]>$x) {
									$second_time = $x+$individual_pickup_slot['split_slot_duration'];
									if($second_time > $times[1]) {
										$second_time = $times[1];
									}
									if($individual_pickup_slot['enable_single_splited_slot']) {
										$pickup_no_location_disable_timeslot_all['nolocation'][] = date("H:i", mktime(0, (int)$x));								
									} else {
										$pickup_no_location_disable_timeslot_all['nolocation'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
									}
									
									$x = $second_time;
								}

							} else {
								if($individual_pickup_slot['enable_single_slot']) {
									$pickup_no_location_disable_timeslot_all['nolocation'][] = date("H:i", mktime(0, (int)$times[0]));								
								} else {
									$pickup_no_location_disable_timeslot_all['nolocation'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
								}

							}		  				 
			  			}
		  			}

	  			}
	  		}
	  	} else {
	  		$pickup_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
	  		$enable_pickup_time = (isset($pickup_settings['enable_pickup_time']) && !empty($pickup_settings['enable_pickup_time'])) ? $pickup_settings['enable_pickup_time'] : false;
		    if($enable_pickup_time) {
		  		$x = (int)$pickup_settings['pickup_time_starts'];
		  		$each_pickup_slot = (isset($pickup_settings['each_time_slot']) && !empty($pickup_settings['each_time_slot'])) ? (int)$pickup_settings['each_time_slot'] : (int)$pickup_settings['pickup_time_ends']-(int)$pickup_settings['pickup_time_starts'];
				while((int)$pickup_settings['pickup_time_ends']>$x) {
					$second_time = $x+$each_pickup_slot;
					if($second_time > (int)$pickup_settings['pickup_time_ends']) {
						$second_time = (int)$pickup_settings['pickup_time_ends'];
					}
					$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));		
					$x = $second_time;
				}

			}	  		
	  	}

	  	$free_up_slot_for_pickup_completed = (isset($delivery_pickup_settings['free_up_slot_for_pickup_completed']) && !empty($delivery_pickup_settings['free_up_slot_for_pickup_completed'])) ? $delivery_pickup_settings['free_up_slot_for_pickup_completed'] : false;

	  	$other_settings = get_option('coderockz_woo_delivery_other_settings');
		$remove_delivery_status_column = (isset($other_settings['remove_delivery_status_column']) && !empty($other_settings['remove_delivery_status_column'])) ? $other_settings['remove_delivery_status_column'] : false;
		if($remove_delivery_status_column){
			$free_up_slot_for_pickup_completed = false;
		}
	  	
	  	foreach ($order_ids as $order) {
	  		$order_ref = wc_get_order($order);
	  		if($this->hpos) {
	  			
				$date = $order_ref->get_meta( 'pickup_date', true );
				$time = $order_ref->get_meta( 'pickup_time', true );
				$location = $order_ref->get_meta( 'pickup_location', true );
			} else {
				$date = get_post_meta($order,"pickup_date",true);
				$time = get_post_meta($order,"pickup_time",true);
				$location = get_post_meta($order,"pickup_location",true);
			}
			
			
			if((isset($date) && isset($time)) || isset($time)) {
				if(!$free_up_slot_for_pickup_completed) {
					$pickup_delivery_times[] = $time;
				} else {
					
					if($this->hpos) {	  			
						if(!$order_ref->meta_exists('delivery_status')) {
							$pickup_delivery_times[] = $time;
						}
					} else {
						if(!metadata_exists('post', $order, 'delivery_status')) {
							$pickup_delivery_times[] = $time;
						}
					}
				}
			}

			if((isset($date) && isset($location)) || isset($location)) {
				$pickup_delivery_locations[] = $location;
			}

		}

		$unique_pickup_times = array_unique($pickup_delivery_times, false);
		$unique_pickup_times = array_values($unique_pickup_times);

		$unique_pickup_locations = array_unique($pickup_delivery_locations, false);
		$unique_pickup_locations = array_values($unique_pickup_locations);

		if($enable_pickup_location && isset($pickup_location_settings['pickup_location']) && count($pickup_location_settings['pickup_location'])>0){
	  		foreach($pickup_location_settings['pickup_location'] as $name => $settings) {

	  			if($settings['enable']) {
		  			
					$disable = $settings['disable_for'];

					if(isset($settings['only_specific_date_close']) && $settings['only_specific_date_close'] != "") {
						$pickup_location_only_specific_date_close[stripslashes($name)] = explode(',', $settings['only_specific_date_close']);
					}

					if(isset($settings['only_specific_date_show']) && $settings['only_specific_date_show'] != "") {
						$pickup_location_only_specific_date_show[stripslashes($name)] = explode(',', $settings['only_specific_date_show']);
					}

					$max_order_pickup_location = (isset($settings['max_order']) && $settings['max_order'] !="") ? sanitize_text_field($settings['max_order']) : "";

					$pickup_location_disable_for[stripslashes($name)] = $disable;

					if(in_array(stripslashes($name),$unique_pickup_locations)) {
						$pickup_max_order_per_location[stripslashes($name)] = (int)$max_order_pickup_location;
					}

				}

			}
		}

		if($enable_custom_pickup_slot) {
			if(isset($custom_pickup_slot_settings['time_slot']) && count($custom_pickup_slot_settings['time_slot'])>0){

				foreach($custom_pickup_slot_settings['time_slot'] as $key => $individual_pickup_slot) {

		  			if($individual_pickup_slot['enable']) {
			  			$key = preg_replace('/-/', ',', $key);

			  			$key_array = explode(",",$key);

					    $max_order = (isset($individual_pickup_slot['max_order']) && $individual_pickup_slot['max_order'] !="") ? sanitize_text_field($individual_pickup_slot['max_order']) : "";
					
					    if($individual_pickup_slot['enable_split']) {
							$x = $key_array[0];
							while($key_array[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $key_array[1]) {
									$second_time = $key_array[1];
								}
								if($individual_pickup_slot['enable_single_splited_slot']) {
									if(in_array(date("H:i", mktime(0, (int)$x)),$unique_pickup_times)) {
										$pickup_max_order_per_slot[date("H:i", mktime(0, (int)$x))] = (int)$max_order;
									}
									
								} else {
									if(in_array(date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time)),$unique_pickup_times)) {
									$pickup_max_order_per_slot[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))] = (int)$max_order;
									}						
								}
								
								$x = $second_time;
							}

						} else {
							if($individual_pickup_slot['enable_single_slot']) {
					
								if(in_array(date("H:i", mktime(0, (int)$key_array[0])),$unique_pickup_times)) {
									$pickup_max_order_per_slot[date("H:i", mktime(0, (int)$key_array[0]))] = (int)$max_order;
								}
							} else {
								if(in_array(date("H:i", mktime(0, (int)$key_array[0])) . ' - ' . date("H:i", mktime(0, (int)$key_array[1])),$unique_pickup_times)) {
									$pickup_max_order_per_slot[date("H:i", mktime(0, (int)$key_array[0])) . ' - ' . date("H:i", mktime(0, (int)$key_array[1]))] = (int)$max_order;	
								}						
							}

						}

					}
				}
			}
		} else {

		    $pickup_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
		    $enable_pickup_time = (isset($pickup_settings['enable_pickup_time']) && !empty($pickup_settings['enable_pickup_time'])) ? $pickup_settings['enable_pickup_time'] : false;
		    if($enable_pickup_time) {
			    $max_order = (isset($pickup_settings['max_pickup_per_slot']) && $pickup_settings['max_pickup_per_slot'] !="") ? sanitize_text_field($pickup_settings['max_pickup_per_slot']) : "";
		  		$x = (int)$pickup_settings['pickup_time_starts'];
		  		$each_pickup_slot = (isset($pickup_settings['each_time_slot']) && !empty($pickup_settings['each_time_slot'])) ? (int)$pickup_settings['each_time_slot'] : (int)$pickup_settings['pickup_time_ends']-(int)$pickup_settings['pickup_time_starts'];
				while((int)$pickup_settings['pickup_time_ends']>$x) {
					$second_time = $x+$each_pickup_slot;
					if($second_time > (int)$pickup_settings['pickup_time_ends']) {
						$second_time = (int)$pickup_settings['pickup_time_ends'];
					}
					if(in_array(date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time)),$unique_pickup_times)) {
						$pickup_max_order_per_slot[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))] = (int)$max_order;
					}		
					$x = $second_time;
				}
			}
		}

		$response_pickup = [
			"pickup_delivery_times" => $pickup_delivery_times,
			"pickup_max_order_per_slot" => $pickup_max_order_per_slot,
			"pickup_slot_disable_for" => $pickup_slot_disable_for,
			'pickup_disabled_current_time_slot' => $pickup_disabled_current_time_slot,
			'pickup_disable_timeslot' => $pickup_disable_timeslot,
			'pickup_state_zip_disable_timeslot_all' => $pickup_state_zip_disable_timeslot_all,
			'pickup_no_state_zip_disable_timeslot_all' => $pickup_no_state_zip_disable_timeslot_all,
			'pickup_disable_timeslot_location' => $pickup_disable_timeslot_location,
			'pickup_location_disable_timeslot_all' => $pickup_location_disable_timeslot_all,
			'pickup_no_location_disable_timeslot_all' => $pickup_no_location_disable_timeslot_all,
			'detect_pickup_location_hide' => $detect_pickup_location_hide,
			'pickup_location_disable_for' => $pickup_location_disable_for,
			'pickup_delivery_locations' => $pickup_delivery_locations,
			'pickup_max_order_per_location' => $pickup_max_order_per_location,
			'max_pickup_slot_individual_location' => $max_pickup_slot_individual_location,
			'pickup_slot_disable_for_sameday' => $pickup_slot_disable_for_sameday,
			'pickup_slot_disable_for_nextday' => $pickup_slot_disable_for_nextday,
			'pickup_slot_disable_for_excceed' => $pickup_slot_disable_for_excceed,
			'pickup_slot_open_specific_date' => $pickup_slot_open_specific_date,
			'pickup_slot_close_specific_date' => $pickup_slot_close_specific_date,
			'pickup_location_only_specific_date_close' => $pickup_location_only_specific_date_close,
			'pickup_location_only_specific_date_show' => $pickup_location_only_specific_date_show,
		];

		$formated_date = date('Y-m-d H:i:s', strtotime($this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['date']),"pickup"),"pickup")));
		$formated_date_obj = new DateTime($formated_date);
		$formated_date = $formated_date_obj->format("w");
		$formated_pickup_date_selected = $formated_date_obj->format("Y-m-d");

		$current_time = (wp_date("G")*60)+wp_date("i");

		$response_for_all = [
			"formated_date" => $formated_date,
			"current_time" => $current_time,
			"formated_pickup_date_selected" => $formated_pickup_date_selected,
		];

		$response = array_merge($response_pickup, $response_for_all);
		$response = json_encode($response);
		wp_send_json_success($response);

	}

	public function coderockz_woo_delivery_get_state_zip_disable_weekday() {
		$current_order_for_state_zip = wc_get_order($_POST['orderId']);
		$given_shippingmethod = "";
		if(!is_null($current_order_for_state_zip) && $current_order_for_state_zip != false) {
	    	$current_state = $current_order_for_state_zip->get_shipping_state();
	    	$current_country = $current_order_for_state_zip->get_shipping_country();
			$current_postcode = $current_order_for_state_zip->get_shipping_postcode();

			foreach( $current_order_for_state_zip->get_items( 'shipping' ) as $item_id => $item ){
			    $given_shippingmethod = $item->get_method_id().':'.$item->get_instance_id();
			}
	    }

		$offdays_settings = get_option('coderockz_woo_delivery_off_days_settings');
		$current_state_offdays_delivery = [];
		$current_postcode_offdays_delivery = [];
		$current_state_offdays_pickup = [];
		$current_postcode_offdays_pickup = [];
		$current_state_offdays_specific_delivery = [];
		$current_postcode_offdays_specific_delivery = [];
		$current_state_offdays_specific_pickup = [];
		$current_postcode_offdays_specific_pickup = [];
		$given_shippingmethod_offdays_delivery = [];
		$given_shippingmethod_offdays_pickup = [];
		$zone_wise_processing_days_off = [];
		$zone_wise_pickup_location_off = [];

		$current_zone_id = '';

		foreach( $current_order_for_state_zip->get_items( 'shipping' ) as $item_id => $item ) {

		    $current_zone = WC_Shipping_Zones::get_zone_by( 'instance_id', $item->get_instance_id() );
		    $current_zone_id = $current_zone->get_id();
		}

		$disable_week_days = isset($_POST['disableWeekDaysZoneProcessing']) && !empty($_POST['disableWeekDaysZoneProcessing']) ? $this->helper->coderockz_woo_delivery_array_sanitize($_POST['disableWeekDaysZoneProcessing']) : [];
		$off_day_dates = isset($_POST['disableOffdaysZoneProcessing']) && !empty($_POST['disableOffdaysZoneProcessing']) ? $this->helper->coderockz_woo_delivery_array_sanitize($_POST['disableOffdaysZoneProcessing']) : [];
		
		$temp_max_processing_days = [];
		$zone_wise_max_processing_time = 0;
		$processing_days_settings = get_option('coderockz_woo_delivery_processing_days_settings');
		$processing_time_settings = get_option('coderockz_woo_delivery_processing_time_settings');

		$consider_off_days = (isset($processing_days_settings['processing_days_consider_off_days']) && !empty($processing_days_settings['processing_days_consider_off_days'])) ? $processing_days_settings['processing_days_consider_off_days'] : false;

		$consider_weekends = (isset($processing_days_settings['processing_days_consider_weekends']) && !empty($processing_days_settings['processing_days_consider_weekends'])) ? $processing_days_settings['processing_days_consider_weekends'] : false;

		$consider_current_day = (isset($processing_days_settings['processing_days_consider_current_day']) && !empty($processing_days_settings['processing_days_consider_current_day'])) ? $processing_days_settings['processing_days_consider_current_day'] : false;

		$zone_wise_processing_days = (isset($processing_days_settings['zone_wise_processing_days']) && !empty($processing_days_settings['zone_wise_processing_days'])) ? $processing_days_settings['zone_wise_processing_days'] : array();

		if(!empty($zone_wise_processing_days)) {

			foreach ($zone_wise_processing_days as $key => $value)
			{
				if($key === $current_zone_id)
				{
					$temp_max_processing_days[] = (int)$value;
					break;
				}
			}

		}

		if($given_shippingmethod != "") {

			if(isset($processing_days_settings['shippingmethod_wise_processingdays']['delivery']) && !empty($processing_days_settings['shippingmethod_wise_processingdays']['delivery']) && isset($processing_days_settings['shippingmethod_wise_processingdays']['delivery'][$given_shippingmethod]) && !empty($processing_days_settings['shippingmethod_wise_processingdays']['delivery'][$given_shippingmethod]) ) {
				$temp_max_processing_days[] = (int)$processing_days_settings['shippingmethod_wise_processingdays']['delivery'][$given_shippingmethod];
			} elseif(isset($processing_days_settings['shippingmethod_wise_processingdays']['pickup']) && !empty($processing_days_settings['shippingmethod_wise_processingdays']['pickup']) && isset($processing_days_settings['shippingmethod_wise_processingdays']['pickup'][$given_shippingmethod]) && !empty($processing_days_settings['shippingmethod_wise_processingdays']['pickup'][$given_shippingmethod]) ) {
				$temp_max_processing_days[] = (int)$processing_days_settings['shippingmethod_wise_processingdays']['pickup'][$given_shippingmethod];
			}
		}

		$temp_max_processing_days = count($temp_max_processing_days) > 0 ? max($temp_max_processing_days) : 0;

		$selectable_start_date = wp_date('Y-m-d H:i:s',current_time( 'timestamp', 1 ));;
		$start_date = current_datetime($selectable_start_date);
		$max_processing_days = $temp_max_processing_days;

		if($max_processing_days > 0) {

			if($consider_current_day && $max_processing_days > 0) {
				if(($consider_weekends && in_array($start_date->format("w"), $disable_week_days)) || ($consider_off_days && in_array($start_date->format("Y-m-d"), $off_day_dates))) {

				} else {
					$zone_wise_processing_days_off[] = $start_date->format("Y-m-d");
					$max_processing_days = $max_processing_days - 1;
					$start_date = $start_date->modify("+1 day");
				}
			} else {
				if(($consider_weekends && in_array($start_date->format("w"), $disable_week_days)) || ($consider_off_days && in_array($start_date->format("Y-m-d"), $off_day_dates))) {

				} else {
					$zone_wise_processing_days_off[] = $start_date->format("Y-m-d");
					$start_date = $start_date->modify("+1 day");
				}
			}

			while($max_processing_days > 0) {
				$date = $start_date;
				if($consider_weekends) {

					$zone_wise_processing_days_off[] = $date->format("Y-m-d");
					$max_processing_days = $max_processing_days - 1;
					$start_date = $start_date->modify("+1 day");
				} else {
					if (!in_array($date->format("w"), $disable_week_days)) {
						$zone_wise_processing_days_off[] = $date->format("Y-m-d");
						$max_processing_days = $max_processing_days - 1;
						$start_date = $start_date->modify("+1 day");
					} else {
						$zone_wise_processing_days_off[] = $date->format("Y-m-d");
						$start_date = $start_date->modify("+1 day");

					}

				}

			}

		}

		$selectable_start_date_sec = wp_date('Y-m-d H:i:s', current_time( 'timestamp', 1 ));
		$start_date_sec = current_datetime($selectable_start_date_sec);
		$max_processing_days_sec = $temp_max_processing_days;

		if($max_processing_days_sec > 0) {

			if($consider_current_day && $max_processing_days_sec > 0) {
				if(($consider_weekends && in_array($start_date_sec->format("w"), $disable_week_days)) || ($consider_off_days && in_array($start_date_sec->format("Y-m-d"), $off_day_dates))) {

				} else {
					$zone_wise_processing_days_off[] = $start_date_sec->format("Y-m-d");
					$max_processing_days_sec = $max_processing_days_sec - 1;
					$start_date_sec = $start_date_sec->modify("+1 day");
				}
			} else {
				if(($consider_weekends && in_array($start_date_sec->format("w"), $disable_week_days)) || ($consider_off_days && in_array($start_date_sec->format("Y-m-d"), $off_day_dates))) {

				} else {
					$zone_wise_processing_days_off[] = $start_date_sec->format("Y-m-d");
					$start_date_sec = $start_date_sec->modify("+1 day");
				}
			}

			while($max_processing_days_sec > 0) {
				$date = $start_date_sec;
				if($consider_off_days) {
					
					$zone_wise_processing_days_off[] = $date->format("Y-m-d");
					$max_processing_days_sec = $max_processing_days_sec - 1;
					$start_date_sec = $start_date_sec->modify("+1 day");

				} else {
					if (!in_array($date->format("Y-m-d"), $off_day_dates)) {
						$zone_wise_processing_days_off[] = $date->format("Y-m-d");
						$max_processing_days_sec = $max_processing_days_sec - 1;
						$start_date_sec = $start_date_sec->modify("+1 day");
					} else {

						$zone_wise_processing_days_off[] = $date->format("Y-m-d");
						$start_date_sec = $start_date_sec->modify("+1 day");

					}

				}

			}
		}


		$zone_processing_time_check = (isset($_POST['zoneProcessingTimeCheck']) && $_POST['zoneProcessingTimeCheck'] !="") ? (bool)sanitize_text_field($_POST['zoneProcessingTimeCheck']) : false;

		if($zone_processing_time_check) {

			$zone_wise_processing_time = (isset($processing_time_settings['zone_wise_processing_time']) && !empty($processing_time_settings['zone_wise_processing_time'])) ? $processing_time_settings['zone_wise_processing_time'] : array();

			if(!empty($zone_wise_processing_time)) {

				foreach ($zone_wise_processing_time as $key => $value)
				{
					if($key === $current_zone_id)
					{
						$zone_wise_max_processing_time = (int)$value;
						break;
					}
				}
			}
		}

		$current_time = (wp_date("G")*60)+wp_date("i");

		$zone_wise_last_processing_time_date = "";
		$today = wp_date('Y-m-d',current_time( 'timestamp', 1 ));

		if($zone_wise_max_processing_time>0){
			$max_processing_time_with_current = $current_time+$zone_wise_max_processing_time;
			if($max_processing_time_with_current>=1440) {
				$x = 1440;
				$date = $today;
				$days_from_processing_time =0;
				while($max_processing_time_with_current>=$x) {
					$second_time = $max_processing_time_with_current - $x;
					$formated = wp_date('Y-m-d H:i:s', $this->helper->wp_strtotime($date));
					$formated_obj = current_datetime($formated);
					$processing_time_date = $formated_obj->modify("+".$days_from_processing_time." day")->format("Y-m-d");
					$zone_wise_last_processing_time_date = $processing_time_date;
					$zone_wise_processing_days_off[] = $processing_time_date;
					$max_processing_time_with_current = $second_time;
					$zone_wise_max_processing_time = $second_time;
					$days_from_processing_time = $days_from_processing_time+1;
				}

				$formated_last_processing = wp_date('Y-m-d H:i:s', $this->helper->wp_strtotime($zone_wise_last_processing_time_date));
				$formated_obj_last_processing = current_datetime($formated_last_processing);
				$zone_wise_last_processing_time_date = $formated_obj_last_processing->modify("+1 day")->format("Y-m-d");
			} else {
				$zone_wise_last_processing_time_date = $today;
			}
		}

		$pickup_location_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
		$pickup_locations = (isset($pickup_location_settings['pickup_location']) && !empty($pickup_location_settings['pickup_location'])) ? $pickup_location_settings['pickup_location'] : array();

		foreach ($pickup_locations as $name => $location_settings) {
			if(isset($location_settings['disable_zone']) && !empty($location_settings['disable_zone'])){
				if(in_array($current_zone_id, $location_settings['disable_zone'])) {
					$zone_wise_pickup_location_off[] = stripslashes($name);
				}
			}
		}

		if(isset($offdays_settings['state_wise_offdays']) && !empty($offdays_settings['state_wise_offdays']) && isset($offdays_settings['state_wise_offdays'][$current_state]) && !empty($offdays_settings['state_wise_offdays'][$current_state])) { 
			$current_state_offdays_delivery = $offdays_settings['state_wise_offdays'][$current_state];
			$current_state_offdays_pickup = $offdays_settings['state_wise_offdays'][$current_state];
		}

		
		if(isset($offdays_settings['postcode_wise_offdays']) && !empty($offdays_settings['postcode_wise_offdays'])) {
			foreach($offdays_settings['postcode_wise_offdays'] as $key => $off_days) {
				$multistep_postal_code = false;
				$between_postal_code = false;
			    if (stripos($key,'...') !== false) {
			    	$range = explode('...', $key);
			    	if(stripos($current_postcode,'-') !== false && stripos($range[0],'-') !== false && stripos($range[1],'-') !== false) {
						
						$sub_range_one = (int)str_replace("-", "", $range[0]);
						$sub_range_two = (int)str_replace("-", "", $range[1]);

						$current_zip_range = (int)str_replace("-", "", $current_postcode);
						
						if($this->helper->number_between($current_zip_range, $sub_range_two, $sub_range_one)) {
							$multistep_postal_code = true;
						}
						
					} elseif(stripos($range[0],'*') !== false && stripos($range[1],'*') !== false) {
						
						$sub_range_one = (int)str_replace("*", "", $range[0]);
						$sub_range_two = (int)str_replace("*", "", $range[1]);
						
						if($this->helper->number_between($this->helper->starts_with_starting_numeric($current_postcode), $sub_range_two, $sub_range_one)) {
							$multistep_postal_code = true;
						}
						
					} elseif(stripos($current_postcode,'-') === false && stripos($range[0],'-') === false && stripos($range[1],'-') === false) {
						$alphabet_code = preg_replace("/[^a-zA-Z]+/", "", $range[0]);
						$range[0] = preg_replace("/[^0-9]+/", "", $range[0]);
						$range[1] = preg_replace("/[^0-9]+/", "", $range[1]);
						if($alphabet_code != "" && $this->helper->starts_with(strtolower($current_postcode), strtolower($alphabet_code)) && $this->helper->number_between(preg_replace("/[^0-9]/", "", $current_postcode ), $range[1], $range[0])) {
							$between_postal_code = true;
						} elseif($alphabet_code == "" /*&& is_numeric($current_postcode)*/ && $this->helper->number_between($current_postcode, $range[1], $range[0])) {
							$between_postal_code = true;
						}
					}
			    }
			    if (substr($key, -1) == '*' && stripos($key,'...') == "") {
			    	if($this->helper->starts_with($current_postcode,substr($key, 0, -1)) || $this->helper->starts_with(strtolower($current_postcode),substr(strtolower($key), 0, -1)) || $this->helper->starts_with(strtoupper($current_postcode),substr(strtoupper($key), 0, -1))) {
			    		$current_postcode_offdays_delivery = [];
			    		$current_postcode_offdays_pickup = [];
						foreach($off_days as $off_day) {
							$current_postcode_offdays_delivery[] = $off_day;
							$current_postcode_offdays_pickup[] = $off_day;
						}
			    	}
			    } elseif($multistep_postal_code || $between_postal_code || ($key == $current_postcode || str_replace(" ","",$key) == $current_postcode || strtolower($key) == strtolower($current_postcode) || str_replace(" ","",strtolower($key)) == strtolower($current_postcode) )) {
					foreach($off_days as $off_day) {
						$current_postcode_offdays_delivery[] = $off_day;
						$current_postcode_offdays_pickup[] = $off_day;
					}
			    }
			}			
		}

		if(isset($offdays_settings['zone_wise_offdays']) && !empty($offdays_settings['zone_wise_offdays'])) {

			if(isset($offdays_settings['zone_wise_offdays']['both']) && !empty($offdays_settings['zone_wise_offdays']['both'])) {
				foreach($offdays_settings['zone_wise_offdays']['both'] as $zone_id => $zone) {

					if($zone_id == $current_zone_id) {

						if($zone['off_days'] != "") {
							$off_days = explode(",",$zone['off_days']);
							foreach($off_days as $off_day) {

								$current_postcode_offdays_delivery[] = $off_day;
								$current_postcode_offdays_pickup[] = $off_day;
								
							}

						}

						if(isset($zone['specific_date_offdays']) && !empty($zone['specific_date_offdays'])) {
							$current_postcode_offdays_specific_delivery = $zone['specific_date_offdays'];
							$current_postcode_offdays_specific_pickup = $zone['specific_date_offdays'];
						}

						break;
					}
				}
			}


			if(isset($offdays_settings['zone_wise_offdays']['delivery']) && !empty($offdays_settings['zone_wise_offdays']['delivery'])) {
				foreach($offdays_settings['zone_wise_offdays']['delivery'] as $zone_id => $zone) {
					if($zone_id == $current_zone_id) {
						if($zone['off_days'] != "") {
							$off_days = explode(",",$zone['off_days']);
							foreach($off_days as $off_day) {
								$current_postcode_offdays_delivery[] = $off_day;
							}

						}

						if(isset($zone['specific_date_offdays']) && !empty($zone['specific_date_offdays'])) {
							$current_postcode_offdays_specific_delivery = $zone['specific_date_offdays'];
						}

						break;
					}
				}
			}

			if(isset($offdays_settings['zone_wise_offdays']['pickup']) && !empty($offdays_settings['zone_wise_offdays']['pickup'])) {
				foreach($offdays_settings['zone_wise_offdays']['pickup'] as $zone_id => $zone) {
					if($zone_id == $current_zone_id) {
						if($zone['off_days'] != "") {
							$off_days = explode(",",$zone['off_days']);
							foreach($off_days as $off_day) {
								$current_postcode_offdays_pickup[] = $off_day;
								
							}
						}

						if(isset($zone['specific_date_offdays']) && !empty($zone['specific_date_offdays'])) {
							$current_postcode_offdays_specific_pickup = $zone['specific_date_offdays'];
						}

						break;
					}
				}
			}

			
		}

		if($given_shippingmethod != "") {

			if(isset($offdays_settings['shippingmethod_wise_offdays']['delivery']) && !empty($offdays_settings['shippingmethod_wise_offdays']['delivery']) && isset($offdays_settings['shippingmethod_wise_offdays']['delivery'][$given_shippingmethod]) && !empty($offdays_settings['shippingmethod_wise_offdays']['delivery'][$given_shippingmethod]) ) {
				$given_shippingmethod_offdays_delivery = $offdays_settings['shippingmethod_wise_offdays']['delivery'][$given_shippingmethod];
			} elseif(isset($offdays_settings['shippingmethod_wise_offdays']['pickup']) && !empty($offdays_settings['shippingmethod_wise_offdays']['pickup']) && isset($offdays_settings['shippingmethod_wise_offdays']['pickup'][$given_shippingmethod]) && !empty($offdays_settings['shippingmethod_wise_offdays']['pickup'][$given_shippingmethod]) ) {
				$given_shippingmethod_offdays_pickup = $offdays_settings['shippingmethod_wise_offdays']['pickup'][$given_shippingmethod];
			}

			if(isset($offdays_settings['shippingmethod_wise_offdays']['delivery']) && !empty($offdays_settings['shippingmethod_wise_offdays']['delivery']) && !empty($this->helper->keyexistinstring($offdays_settings['shippingmethod_wise_offdays']['delivery'], $given_shippingmethod)) ) { 
				$given_shippingmethod_offdays_delivery = $this->helper->keyexistinstring($offdays_settings['shippingmethod_wise_offdays']['delivery'], $given_shippingmethod);
			} elseif(isset($offdays_settings['shippingmethod_wise_offdays']['pickup']) && !empty($offdays_settings['shippingmethod_wise_offdays']['pickup']) && !empty($this->helper->keyexistinstring($offdays_settings['shippingmethod_wise_offdays']['pickup'], $given_shippingmethod)) ) { 
				$given_shippingmethod_offdays_pickup = $this->helper->keyexistinstring($offdays_settings['shippingmethod_wise_offdays']['pickup'], $given_shippingmethod);
			}

		}

		$current_postcode_offdays_delivery = array_values(array_unique($current_postcode_offdays_delivery, false));
		$current_state_offdays_delivery = array_values(array_unique($current_state_offdays_delivery, false));

		$current_postcode_offdays_pickup = array_values(array_unique($current_postcode_offdays_pickup, false));
		$current_state_offdays_pickup = array_values(array_unique($current_state_offdays_pickup, false));


		$current_state_zip_offdays_delivery = array_merge($current_state_offdays_delivery,$current_postcode_offdays_delivery);
		$current_state_zip_offdays_pickup = array_merge($current_state_offdays_pickup,$current_postcode_offdays_pickup);

		$current_state_zip_offdays_specific_delivery = array_merge($current_state_offdays_specific_delivery,$current_postcode_offdays_specific_delivery);
		$current_state_zip_offdays_specific_pickup = array_merge($current_state_offdays_specific_pickup,$current_postcode_offdays_specific_pickup);

		$zone_wise_processing_days_off = array_values(array_unique($zone_wise_processing_days_off, false));

		
		$given_shippingmethod_offdays_delivery = array_values(array_unique($given_shippingmethod_offdays_delivery, false));
		$given_shippingmethod_offdays_pickup = array_values(array_unique($given_shippingmethod_offdays_pickup, false));

		$response = [
			"given_shippingmethod_offdays_delivery" => $given_shippingmethod_offdays_delivery,
			"given_shippingmethod_offdays_pickup" => $given_shippingmethod_offdays_pickup,
			"current_state_zip_offdays_delivery" => $current_state_zip_offdays_delivery,
			"current_state_zip_offdays_pickup" => $current_state_zip_offdays_pickup,
			"current_state_zip_offdays_specific_delivery" => $current_state_zip_offdays_specific_delivery,
			"current_state_zip_offdays_specific_pickup" => $current_state_zip_offdays_specific_pickup,
			"zone_wise_processing_days_off" => $zone_wise_processing_days_off,
			"zone_wise_pickup_location_off" =>$zone_wise_pickup_location_off,
			"zone_wise_max_processing_time" => $zone_wise_max_processing_time,
			"zone_wise_last_processing_time_date" => $zone_wise_last_processing_time_date
		];
		$response = json_encode($response);
		wp_send_json_success($response);
	}

    public function coderockz_woo_delivery_admin_option_delivery_time_pickup() {
    	$delivery_selection = isset($_POST['deliverySelector']) ? sanitize_text_field($_POST['deliverySelector']) : "";
		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
		$enable_delivery_date = (isset($delivery_date_settings['enable_delivery_date']) && !empty($delivery_date_settings['enable_delivery_date'])) ? $delivery_date_settings['enable_delivery_date'] : false;
		$enable_pickup_date = (isset($pickup_date_settings['enable_pickup_date']) && !empty($pickup_date_settings['enable_pickup_date'])) ? $pickup_date_settings['enable_pickup_date'] : false;

		$order_status_keys = array_keys(wc_get_order_statuses());
		$order_status = ['partially-paid'];
		foreach($order_status_keys as $order_status_key) {
			$order_status[] = substr($order_status_key,3);
		}
		$order_status = array_diff($order_status,['cancelled','failed','refunded']);

		$disable_for_max_delivery_dates = [];
		$disable_for_max_pickup_dates = [];
		$delivery_days_delivery = [];
		$need_checking_max_order = true;
		$pickup_days_delivery = [];
		$pickup_need_checking_max_order = true;

		$today = wp_date('Y-m-d',current_time( 'timestamp', 1 ));
		$range_first_date = wp_date('Y-m-d', current_time( 'timestamp', 1 ));
		$formated_obj = current_datetime($range_first_date);

		$second_range_first_date = wp_date('Y-m-d', current_time( 'timestamp', 1 ));
		$second_formated_obj = current_datetime($second_range_first_date);
		$second_range_last_date = $second_formated_obj->modify("+10 day")->format("Y-m-d");

		$second_period = $this->helper->get_date_from_range($second_range_first_date, $second_range_last_date, "Y-m-d");
	    
		if($delivery_selection == "delivery") {

			if((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "")) {

				$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');
				$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
				$period = $this->helper->get_date_from_range($range_first_date, $range_last_date, "Y-m-d");
				if(isset($max_per_day_count['delivery']['order']) && !empty($max_per_day_count['delivery']['order'])) {
					$period = array_intersect($period, array_keys($max_per_day_count['delivery']['order']));
				}

				$max_order_per_day = (isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") ? (int)$delivery_date_settings['maximum_order_per_day'] : 10000000000000;
				foreach ($period as $date) { 

					if($max_per_day_count !== false) {
						if(isset($max_per_day_count['delivery']['order']) && array_key_exists($date, $max_per_day_count['delivery']['order'])) {
					    	if(isset($max_per_day_count['delivery']['order'][$date]) && $max_per_day_count['delivery']['order'][$date]!= '') {
					    		if((int)$max_per_day_count['delivery']['order'][$date] >= $max_order_per_day) {
									$disable_for_max_delivery_dates[] = $date;
							    }
					    		
					    	}
					    }
					}

				}

			}

			if(isset($delivery_date_settings['maximum_order_product_per_day']) && $delivery_date_settings['maximum_order_product_per_day'] != "") {

				$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
				$filtered_date = $range_first_date . ',' . $range_last_date;
				$filtered_dates = explode(',', $filtered_date);
				$period = new DatePeriod(new DateTime($filtered_dates[0]), new DateInterval('P1D'), new DateTime($filtered_dates[1].' +1 day'));

				$maximum_order_product_per_day = (isset($delivery_date_settings['maximum_order_product_per_day']) && $delivery_date_settings['maximum_order_product_per_day'] != "") ? (int)$delivery_date_settings['maximum_order_product_per_day'] : 10000000000000;
				foreach ($period as $date) { 
				    if($this->hpos) {
				    	$args = array(
					        'limit' => -1,
							'type' => array( 'shop_order' ),
							'status' => $order_status,
							'meta_query' => array(
					            array(
					                'key'     => 'delivery_type',
					                'value'   => "delivery",
					                'compare' => '==',
					            ),
					            array(
					                'key'     => 'delivery_date',
					                'value'   => date('Y-m-d', strtotime($date->format("Y-m-d"))),
					                'compare' => '==',
					            ),
					        ),
					        'return' => 'ids'
					    );
				    } else {
				    	$args = array(
					        'limit' => -1,
					        'delivery_date' => date('Y-m-d', strtotime($date->format("Y-m-d"))),
					        'delivery_type' => "delivery",
					        'status' => $order_status,
					        'return' => 'ids'
					    );
				    }
				    $orders_array = wc_get_orders( $args );

				    $total_quantity = 0;
					foreach($orders_array as $order_id) {
						
						$order = wc_get_order($order_id);
						
						foreach ( $order->get_items() as $item_id => $item ) {
							$total_quantity += $item->get_quantity();
						}
						
					}


				    if($total_quantity > $maximum_order_product_per_day) {
						$disable_for_max_delivery_dates[] = date('Y-m-d', strtotime($date->format("Y-m-d")));
				    }
				}

			}

			$custom_time_slot_settings = get_option('coderockz_woo_delivery_time_slot_settings');
			$enable_custom_time_slot = (isset($custom_time_slot_settings['enable_custom_time_slot']) && !empty($custom_time_slot_settings['enable_custom_time_slot'])) ? $custom_time_slot_settings['enable_custom_time_slot'] : false;
			if($enable_custom_time_slot) {
				if(isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0 && count($custom_time_slot_settings['time_slot'])<=4){
					$max_order = true;
					foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {
						if((isset($individual_time_slot['max_order']) && $individual_time_slot['max_order'] !="")) {

						} else {
							$max_order = false;
							break;
						}
					}

					if($max_order) {

						foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {

				  			if($individual_time_slot['enable']) {
				  				
				  				if((isset($individual_time_slot['max_order']) && $individual_time_slot['max_order'] !="")) {

					  				$hide_date_timeslot = isset($individual_time_slot['only_specific_date_close']) && $individual_time_slot['only_specific_date_close'] !="" ? sanitize_text_field($individual_time_slot['only_specific_date_close']) : "";
					  				$hide_date_timeslot = explode(",",$hide_date_timeslot);
					  				$available_date_timeslot = isset($individual_time_slot['only_specific_date']) && $individual_time_slot['only_specific_date'] !="" ? sanitize_text_field($individual_time_slot['only_specific_date']) : "";
					  				$available_date_timeslot = explode(",",$available_date_timeslot);
					  				foreach($second_period as $delivery_day) {
					  					if(isset($individual_time_slot['only_specific_date']) && $individual_time_slot['only_specific_date'] !="" && !in_array($delivery_day,$available_date_timeslot)) {
					  						continue;
					  					}
					  					if((!empty($available_date_timeslot) && in_array($delivery_day,$available_date_timeslot)) || (!in_array($delivery_day,$hide_date_timeslot) && !in_array(date('w', strtotime($delivery_day)),$individual_time_slot['disable_for']))) {
					  						
					  						$max_order = sanitize_text_field($individual_time_slot['max_order']);

								  			if($individual_time_slot['enable_split']) {
												$times = explode('-', $key);
												if(isset($delivery_days_delivery[$delivery_day])) {
													$delivery_days_delivery[$delivery_day] = (int)$delivery_days_delivery[$delivery_day] + floor(((((int)$times[1] - (int)$times[0])/(int)$individual_time_slot['split_slot_duration'])*(int)$max_order));
												} else {
													$delivery_days_delivery[$delivery_day] = floor(((((int)$times[1] - (int)$times[0])/(int)$individual_time_slot['split_slot_duration'])*(int)$max_order));
												}
											} else {
												if(isset($delivery_days_delivery[$delivery_day])) {
													$delivery_days_delivery[$delivery_day] = (int)$delivery_days_delivery[$delivery_day] + (int)$max_order;
												} else {
													$delivery_days_delivery[$delivery_day] = (int)$max_order;
												}
												
											}

					  					}
					  				}
				  				}
				  			}
				  		}
			  		} else {
  						$need_checking_max_order = false;
  					}
			  	}
			} else {

				$time_settings = get_option('coderockz_woo_delivery_time_settings');
			    $enable_delivery_time = (isset($time_settings['enable_delivery_time']) && !empty($time_settings['enable_delivery_time'])) ? $time_settings['enable_delivery_time'] : false;
			    $max_order = (isset($time_settings['max_order_per_slot']) && $time_settings['max_order_per_slot'] !="") ? sanitize_text_field($time_settings['max_order_per_slot']) : "";
			    if($enable_delivery_time && $max_order != ""/* && (int)$max_order <= 15*/) {

			    	$each_delivery_slot = (isset($time_settings['each_time_slot']) && !empty($time_settings['each_time_slot'])) ? (int)$time_settings['each_time_slot'] : (int)$time_settings['delivery_time_ends']-(int)$time_settings['delivery_time_starts'];
			    	$day_max_order = ((((int)$time_settings['delivery_time_ends'] - (int)$time_settings['delivery_time_starts'])/(int)$each_delivery_slot)*(int)$max_order);
			    	
			    	foreach($second_period as $delivery_day) {
			    		if(isset($delivery_days_delivery[$delivery_day])) {
							$delivery_days_delivery[$delivery_day] = (int)$day_max_order;
						} else {
							$delivery_days_delivery[$delivery_day] = (int)$day_max_order;
						}
			    	}

				} else {
					$need_checking_max_order = false;
				}
 		
		  	}

			if($need_checking_max_order) {
				foreach($second_period as $delivery_day) {
					if(isset($delivery_days_delivery[$delivery_day])) {
						
					    if($this->hpos) {
					    	$checking_max_order_args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $order_status,
								'meta_query' => array(
						            array(
						                'key'     => 'delivery_type',
						                'value'   => "delivery",
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'delivery_date',
						                'value'   => $delivery_day,
						                'compare' => '==',
						            ),
						        ),
						        'return' => 'ids'
						    );
					    } else {
					    	$checking_max_order_args = array(
						        'limit' => -1,
						        'delivery_date' => $delivery_day,
						        'delivery_type' => "delivery",
						        'status' => $order_status,
						        'return' => 'ids'
						    );
					    }
					    $orders_array = wc_get_orders( $checking_max_order_args );
					    if(count($orders_array) >= (int)$delivery_days_delivery[$delivery_day]) {
							$disable_for_max_delivery_dates[] = $delivery_day;
					    }
					}
				}
			}

			$disable_for_max_delivery_dates = array_unique($disable_for_max_delivery_dates, false);
			$disable_for_max_delivery_dates = array_values($disable_for_max_delivery_dates);

		} elseif($delivery_selection == "pickup") {
			
			if((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "")) {

				$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');
				$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
				$period = $this->helper->get_date_from_range($range_first_date, $range_last_date, "Y-m-d");
				if(isset($max_per_day_count['pickup']['order']) && !empty($max_per_day_count['pickup']['order'])) {
					$period = array_intersect($period, array_keys($max_per_day_count['pickup']['order']));
				}

				$max_pickup_per_day = (isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") ? (int)$pickup_date_settings['maximum_pickup_per_day'] : 10000000000000;
				foreach ($period as $date) {
					
				    if($max_per_day_count !== false) {
						if(isset($max_per_day_count['pickup']['order']) && array_key_exists($date, $max_per_day_count['pickup']['order'])) {
					    	if(isset($max_per_day_count['pickup']['order'][$date]) && $max_per_day_count['pickup']['order'][$date]!= '') {
					    		if((int)$max_per_day_count['pickup']['order'][$date] >= $max_pickup_per_day) {
									$disable_for_max_pickup_dates[] = $date;
							    }
					    		
					    	}
					    }
					}
				}

			}

			if(isset($pickup_date_settings['maximum_pickup_product_per_day']) && $pickup_date_settings['maximum_pickup_product_per_day'] != "") {

				$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
				$filtered_date = $range_first_date . ',' . $range_last_date;
				$filtered_dates = explode(',', $filtered_date);
				$period = new DatePeriod(new DateTime($filtered_dates[0]), new DateInterval('P1D'), new DateTime($filtered_dates[1].' +1 day'));

				$maximum_pickup_product_per_day = (isset($pickup_date_settings['maximum_pickup_product_per_day']) && $pickup_date_settings['maximum_pickup_product_per_day'] != "") ? (int)$pickup_date_settings['maximum_pickup_product_per_day'] : 10000000000000;
				foreach ($period as $date) {
				    if($this->hpos) {
				    	$args = array(
					        'limit' => -1,
							'type' => array( 'shop_order' ),
							'status' => $order_status,
							'meta_query' => array(
					            array(
					                'key'     => 'delivery_type',
					                'value'   => "pickup",
					                'compare' => '==',
					            ),
					            array(
					                'key'     => 'pickup_date',
					                'value'   => date('Y-m-d', strtotime($date->format("Y-m-d"))),
					                'compare' => '==',
					            ),
					        ),
					        'return' => 'ids'
					    );
				    } else {
				    	$args = array(
					        'limit' => -1,
					        'pickup_date' => date('Y-m-d', strtotime($date->format("Y-m-d"))),
					        'delivery_type' => "pickup",
					        'status' => $order_status,
					        'return' => 'ids'
					    );
				    }
				    $orders_array = wc_get_orders( $args );

				    $total_quantity = 0;
					foreach($orders_array as $order_id) {
						
						$order = wc_get_order($order_id);
						
						foreach ( $order->get_items() as $item_id => $item ) {
							$total_quantity += $item->get_quantity();
						}
						
					}
					
				    if($total_quantity > $maximum_pickup_product_per_day) {
						$disable_for_max_pickup_dates[] = date('Y-m-d', strtotime($date->format("Y-m-d")));
				    }
				}

			}

			$custom_pickup_slot_settings = get_option('coderockz_woo_delivery_pickup_slot_settings');
			$enable_custom_pickup_slot = (isset($custom_pickup_slot_settings['enable_custom_pickup_slot']) && !empty($custom_pickup_slot_settings['enable_custom_pickup_slot'])) ? $custom_pickup_slot_settings['enable_custom_pickup_slot'] : false;
			if($enable_custom_pickup_slot) {
				if(isset($custom_pickup_slot_settings['time_slot']) && count($custom_pickup_slot_settings['time_slot'])>0 && count($custom_pickup_slot_settings['time_slot'])<=4){
					$pickup_max_order = true;
					foreach($custom_pickup_slot_settings['time_slot'] as $key => $individual_pickup_slot) {
						if((isset($individual_pickup_slot['max_order']) && $individual_pickup_slot['max_order'] !="")) {

						} else {
							$pickup_max_order = false;
							break;
						}
					}

					if($pickup_max_order) {

						foreach($custom_pickup_slot_settings['time_slot'] as $key => $individual_pickup_slot) {

				  			if($individual_pickup_slot['enable']) {
				  				
				  				if((isset($individual_pickup_slot['max_order']) && $individual_pickup_slot['max_order'] !="")) {

					  				$pickup_hide_date_timeslot = isset($individual_pickup_slot['only_specific_date_close']) && $individual_pickup_slot['only_specific_date_close'] !="" ? sanitize_text_field($individual_pickup_slot['only_specific_date_close']) : "";
					  				$pickup_hide_date_timeslot = explode(",",$pickup_hide_date_timeslot);
					  				$pickup_available_date_timeslot = isset($individual_pickup_slot['only_specific_date']) && $individual_pickup_slot['only_specific_date'] !="" ? sanitize_text_field($individual_pickup_slot['only_specific_date']) : "";
					  				$pickup_available_date_timeslot = explode(",",$pickup_available_date_timeslot);
					  				foreach($second_period as $pickup_day) {
					  					if(isset($individual_pickup_slot['only_specific_date']) && $individual_pickup_slot['only_specific_date'] !="" && !in_array($pickup_day,$pickup_available_date_timeslot)) {
					  						continue;
					  					}
					  					if((!empty($pickup_available_date_timeslot) && in_array($pickup_day,$pickup_available_date_timeslot)) || (!in_array($pickup_day,$pickup_hide_date_timeslot) && !in_array(date('w', strtotime($pickup_day)),$individual_pickup_slot['disable_for']))) {
					  						
					  						$max_order = sanitize_text_field($individual_pickup_slot['max_order']);

								  			if($individual_pickup_slot['enable_split']) {
												$times = explode('-', $key);
												if(isset($pickup_days_delivery[$pickup_day])) {
													$pickup_days_delivery[$pickup_day] = (int)$pickup_days_delivery[$pickup_day] + floor(((((int)$times[1] - (int)$times[0])/(int)$individual_pickup_slot['split_slot_duration'])*(int)$max_order));
												} else {
													$pickup_days_delivery[$pickup_day] = floor(((((int)$times[1] - (int)$times[0])/(int)$individual_pickup_slot['split_slot_duration'])*(int)$max_order));
												}
											} else {
												if(isset($pickup_days_delivery[$pickup_day])) {
													$pickup_days_delivery[$pickup_day] = (int)$pickup_days_delivery[$pickup_day] + (int)$max_order;
												} else {
													$pickup_days_delivery[$pickup_day] = (int)$max_order;
												}
												
											}

					  					}
					  				}
				  				}
				  			}
				  		}
			  		} else {
  						$pickup_need_checking_max_order = false;
  					}
			  	}
			} else {

				$pickup_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
			    $enable_pickup_time = (isset($pickup_settings['enable_pickup_time']) && !empty($pickup_settings['enable_pickup_time'])) ? $pickup_settings['enable_pickup_time'] : false;
			    $max_order = (isset($pickup_settings['max_pickup_per_slot']) && $pickup_settings['max_pickup_per_slot'] !="") ? sanitize_text_field($pickup_settings['max_pickup_per_slot']) : "";
			    if($enable_pickup_time && $max_order != ""/* && (int)$max_order <= 15*/) {

			    	$each_pickup_slot = (isset($pickup_settings['each_time_slot']) && !empty($pickup_settings['each_time_slot'])) ? (int)$pickup_settings['each_time_slot'] : (int)$pickup_settings['pickup_time_ends']-(int)$pickup_settings['pickup_time_starts'];
			    	$day_max_order = ((((int)$pickup_settings['pickup_time_ends'] - (int)$pickup_settings['pickup_time_starts'])/(int)$each_pickup_slot)*(int)$max_order);
			    	
			    	foreach($second_period as $pickup_day) {
			    		if(isset($pickup_days_delivery[$pickup_day])) {
							$pickup_days_delivery[$pickup_day] = (int)$day_max_order;
						} else {
							$pickup_days_delivery[$pickup_day] = (int)$day_max_order;
						}
			    	}

				} else {
					$pickup_need_checking_max_order = false;
				}
 		
		  	}

			if($pickup_need_checking_max_order) {
				foreach($second_period as $pickup_day) {
					if(isset($pickup_days_delivery[$pickup_day])) {
						
					    if($this->hpos) {
					    	$pickup_checking_max_order_args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $order_status,
								'meta_query' => array(
						            array(
						                'key'     => 'delivery_type',
						                'value'   => "pickup",
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'pickup_date',
						                'value'   => $pickup_day,
						                'compare' => '==',
						            ),
						        ),
						        'return' => 'ids'
						    );
					    } else {
					    	$pickup_checking_max_order_args = array(
						        'limit' => -1,
						        'pickup_date' => $pickup_day,
						        'delivery_type' => "pickup",
						        'status' => $order_status,
						        'return' => 'ids'
						    );
					    }
					    $orders_array = wc_get_orders( $pickup_checking_max_order_args );
					    if(count($orders_array) >= (int)$pickup_days_delivery[$pickup_day]) {
							$disable_for_max_pickup_dates[] = $pickup_day;
					    }
					}
				}
			}

			$disable_for_max_pickup_dates = array_unique($disable_for_max_pickup_dates, false);
			$disable_for_max_pickup_dates = array_values($disable_for_max_pickup_dates);
		
		} else {

			if((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "")) {
				$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');
				$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
				$period = $this->helper->get_date_from_range($range_first_date, $range_last_date, "Y-m-d");
				if(isset($max_per_day_count['delivery']['order']) && !empty($max_per_day_count['delivery']['order'])) {
					$period = array_intersect($period, array_keys($max_per_day_count['delivery']['order']));
				}


				$max_order_per_day = (isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") ? (int)$delivery_date_settings['maximum_order_per_day'] : 10000000000000;
				foreach ($period as $date) { 
					
				    if($max_per_day_count !== false) {
						if(isset($max_per_day_count['delivery']['order']) && array_key_exists($date, $max_per_day_count['delivery']['order'])) {
					    	if(isset($max_per_day_count['delivery']['order'][$date]) && $max_per_day_count['delivery']['order'][$date]!= '') {
					    		if((int)$max_per_day_count['delivery']['order'][$date] >= $max_order_per_day) {
									$disable_for_max_delivery_dates[] = $date;
							    }
					    		
					    	}
					    }
					}
				}

			}

			if(isset($delivery_date_settings['maximum_order_product_per_day']) && $delivery_date_settings['maximum_order_product_per_day'] != "") {

				$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
				$filtered_date = $range_first_date . ',' . $range_last_date;
				$filtered_dates = explode(',', $filtered_date);
				$period = new DatePeriod(new DateTime($filtered_dates[0]), new DateInterval('P1D'), new DateTime($filtered_dates[1].' +1 day'));


				$maximum_order_product_per_day = (isset($delivery_date_settings['maximum_order_product_per_day']) && $delivery_date_settings['maximum_order_product_per_day'] != "") ? (int)$delivery_date_settings['maximum_order_product_per_day'] : 10000000000000;
				foreach ($period as $date) { 
				    if($this->hpos) {
				    	$args = array(
					        'limit' => -1,
							'type' => array( 'shop_order' ),
							'status' => $order_status,
							'meta_query' => array(
					            array(
					                'key'     => 'delivery_type',
					                'value'   => "delivery",
					                'compare' => '==',
					            ),
					            array(
					                'key'     => 'delivery_date',
					                'value'   => date('Y-m-d', strtotime($date->format("Y-m-d"))),
					                'compare' => '==',
					            ),
					        ),
					        'return' => 'ids'
					    );
				    } else {
				    	$args = array(
					        'limit' => -1,
					        'delivery_date' => date('Y-m-d', strtotime($date->format("Y-m-d"))),
					        'delivery_type' => "delivery",
					        'status' => $order_status,
					        'return' => 'ids'
					    );
				    }
				    $orders_array = wc_get_orders( $args );

				    $total_quantity = 0;
					foreach($orders_array as $order_id) {
						
						$order = wc_get_order($order_id);
						
						foreach ( $order->get_items() as $item_id => $item ) {
							$total_quantity += $item->get_quantity();
						}
						
					}

				    if($total_quantity > $maximum_order_product_per_day) {
						$disable_for_max_delivery_dates[] = date('Y-m-d', strtotime($date->format("Y-m-d")));
				    }
				}

			}

			$custom_time_slot_settings = get_option('coderockz_woo_delivery_time_slot_settings');
			$enable_custom_time_slot = (isset($custom_time_slot_settings['enable_custom_time_slot']) && !empty($custom_time_slot_settings['enable_custom_time_slot'])) ? $custom_time_slot_settings['enable_custom_time_slot'] : false;
			if($enable_custom_time_slot) {
				if(isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0 && count($custom_time_slot_settings['time_slot'])<=4){
					$max_order = true;
					foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {
						if((isset($individual_time_slot['max_order']) && $individual_time_slot['max_order'] !="")) {

						} else {
							$max_order = false;
							break;
						}
					}

					if($max_order) {

						foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {

				  			if($individual_time_slot['enable']) {
				  				
				  				if((isset($individual_time_slot['max_order']) && $individual_time_slot['max_order'] !="")) {

					  				$hide_date_timeslot = isset($individual_time_slot['only_specific_date_close']) && $individual_time_slot['only_specific_date_close'] !="" ? sanitize_text_field($individual_time_slot['only_specific_date_close']) : "";
					  				$hide_date_timeslot = explode(",",$hide_date_timeslot);
					  				$available_date_timeslot = isset($individual_time_slot['only_specific_date']) && $individual_time_slot['only_specific_date'] !="" ? sanitize_text_field($individual_time_slot['only_specific_date']) : "";
					  				$available_date_timeslot = explode(",",$available_date_timeslot);
					  				foreach($second_period as $delivery_day) {
					  					$delivery_day = date("Y-m-d", strtotime($delivery_day->format("Y-m-d")));
					  					if(isset($individual_time_slot['only_specific_date']) && $individual_time_slot['only_specific_date'] !="" && !in_array($delivery_day,$available_date_timeslot)) {
					  						continue;
					  					}
					  					if((!empty($available_date_timeslot) && in_array($delivery_day,$available_date_timeslot)) || (!in_array($delivery_day,$hide_date_timeslot) && !in_array(date('w', strtotime($delivery_day)),$individual_time_slot['disable_for']))) {
					  						
					  						$max_order = sanitize_text_field($individual_time_slot['max_order']);

								  			if($individual_time_slot['enable_split']) {
												$times = explode('-', $key);
												$delivery_days_delivery[$delivery_day] = ((((int)$times[1] - (int)$times[0])/(int)$individual_time_slot['split_slot_duration'])*(int)$max_order);
											} else {
												if(isset($delivery_days_delivery[$delivery_day])) {
													$delivery_days_delivery[$delivery_day] = (int)$delivery_days_delivery[$delivery_day] + (int)$max_order;
												} else {
													$delivery_days_delivery[$delivery_day] = (int)$max_order;
												}
												
											}

					  					}
					  				}
				  				}
				  			}
				  		}
			  		} else {
  						$need_checking_max_order = false;
  					}
			  	}
			} else {

				$time_settings = get_option('coderockz_woo_delivery_time_settings');
			    $enable_delivery_time = (isset($time_settings['enable_delivery_time']) && !empty($time_settings['enable_delivery_time'])) ? $time_settings['enable_delivery_time'] : false;
			    $max_order = (isset($time_settings['max_order_per_slot']) && $time_settings['max_order_per_slot'] !="") ? sanitize_text_field($time_settings['max_order_per_slot']) : "";
			    if($enable_delivery_time && $max_order != ""/* && (int)$max_order <= 15*/) {

			    	$each_delivery_slot = (isset($time_settings['each_time_slot']) && !empty($time_settings['each_time_slot'])) ? (int)$time_settings['each_time_slot'] : (int)$time_settings['delivery_time_ends']-(int)$time_settings['delivery_time_starts'];
			    	$day_max_order = ((((int)$time_settings['delivery_time_ends'] - (int)$time_settings['delivery_time_starts'])/(int)$each_delivery_slot)*(int)$max_order);
			    	
			    	foreach($second_period as $delivery_day) {
			    		if(isset($delivery_days_delivery[$delivery_day])) {
							$delivery_days_delivery[$delivery_day] = (int)$day_max_order;
						} else {
							$delivery_days_delivery[$delivery_day] = (int)$day_max_order;
						}
			    	}

				} else {
					$need_checking_max_order = false;
				}
 		
		  	}

			if($need_checking_max_order) {
				foreach($second_period as $delivery_day) {
					if(isset($delivery_days_delivery[$delivery_day])) {
						
					    if($this->hpos) {
					    	$checking_max_order_args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $order_status,
								'meta_query' => array(
						            array(
						                'key'     => 'delivery_type',
						                'value'   => "delivery",
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'delivery_date',
						                'value'   => $delivery_day,
						                'compare' => '==',
						            ),
						        ),
						        'return' => 'ids'
						    );
					    } else {
					    	$checking_max_order_args = array(
						        'limit' => -1,
						        'delivery_date' => $delivery_day,
						        'delivery_type' => "delivery",
						        'status' => $order_status,
						        'return' => 'ids'
						    );
					    }
					    $orders_array = wc_get_orders( $checking_max_order_args );
					    if(count($orders_array) >= (int)$delivery_days_delivery[$delivery_day]) {
							$disable_for_max_delivery_dates[] = $delivery_day;
					    }
					}
				}
			}

			$disable_for_max_delivery_dates = array_unique($disable_for_max_delivery_dates, false);
			$disable_for_max_delivery_dates = array_values($disable_for_max_delivery_dates);

			if((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "")) {

				$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');
				$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
				$period = $this->helper->get_date_from_range($range_first_date, $range_last_date, "Y-m-d");
				if(isset($max_per_day_count['pickup']['order']) && !empty($max_per_day_count['pickup']['order'])) {
					$period = array_intersect($period, array_keys($max_per_day_count['pickup']['order']));
				}

				$max_pickup_per_day = (isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") ? (int)$pickup_date_settings['maximum_pickup_per_day'] : 10000000000000;
				foreach ($period as $date) {

				    if($max_per_day_count !== false) {
						if(isset($max_per_day_count['pickup']['order']) && array_key_exists($date, $max_per_day_count['pickup']['order'])) {
					    	if(isset($max_per_day_count['pickup']['order'][$date]) && $max_per_day_count['pickup']['order'][$date]!= '') {
					    		if((int)$max_per_day_count['pickup']['order'][$date] >= $max_pickup_per_day) {
									$disable_for_max_pickup_dates[] = $date;
							    }
					    		
					    	}
					    }
					}
				}

			}

			if(isset($pickup_date_settings['maximum_pickup_product_per_day']) && $pickup_date_settings['maximum_pickup_product_per_day'] != "") {

				$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
				$filtered_date = $range_first_date . ',' . $range_last_date;
				$filtered_dates = explode(',', $filtered_date);
				$period = new DatePeriod(new DateTime($filtered_dates[0]), new DateInterval('P1D'), new DateTime($filtered_dates[1].' +1 day'));

				$maximum_pickup_product_per_day = (isset($pickup_date_settings['maximum_pickup_product_per_day']) && $pickup_date_settings['maximum_pickup_product_per_day'] != "") ? (int)$pickup_date_settings['maximum_pickup_product_per_day'] : 10000000000000;
				foreach ($period as $date) {

				    if($this->hpos) {
				    	$args = array(
					        'limit' => -1,
							'type' => array( 'shop_order' ),
							'status' => $order_status,
							'meta_query' => array(
					            array(
					                'key'     => 'delivery_type',
					                'value'   => "pickup",
					                'compare' => '==',
					            ),
					            array(
					                'key'     => 'pickup_date',
					                'value'   => date('Y-m-d', strtotime($date->format("Y-m-d"))),
					                'compare' => '==',
					            ),
					        ),
					        'return' => 'ids'
					    );
				    } else {
				    	$args = array(
					        'limit' => -1,
					        'pickup_date' => date('Y-m-d', strtotime($date->format("Y-m-d"))),
					        'delivery_type' => "pickup",
					        'status' => $order_status,
					        'return' => 'ids'
					    );
				    }
				    $orders_array = wc_get_orders( $args );

				    $total_quantity = 0;
					foreach($orders_array as $order_id) {
						
						$order = wc_get_order($order_id);
						
						foreach ( $order->get_items() as $item_id => $item ) {
							$total_quantity += $item->get_quantity();
						}
						
					}
					
				    if($total_quantity > $maximum_pickup_product_per_day) {
						$disable_for_max_pickup_dates[] = date('Y-m-d', strtotime($date->format("Y-m-d")));
				    }
				}

			}

			$custom_pickup_slot_settings = get_option('coderockz_woo_delivery_pickup_slot_settings');
			$enable_custom_pickup_slot = (isset($custom_pickup_slot_settings['enable_custom_pickup_slot']) && !empty($custom_pickup_slot_settings['enable_custom_pickup_slot'])) ? $custom_pickup_slot_settings['enable_custom_pickup_slot'] : false;
			if($enable_custom_pickup_slot) {
				if(isset($custom_pickup_slot_settings['time_slot']) && count($custom_pickup_slot_settings['time_slot'])>0 && count($custom_pickup_slot_settings['time_slot'])<=4){
					$pickup_max_order = true;
					foreach($custom_pickup_slot_settings['time_slot'] as $key => $individual_pickup_slot) {
						if((isset($individual_pickup_slot['max_order']) && $individual_pickup_slot['max_order'] !="")) {

						} else {
							$pickup_max_order = false;
							break;
						}
					}

					if($pickup_max_order) {

						foreach($custom_pickup_slot_settings['time_slot'] as $key => $individual_pickup_slot) {

				  			if($individual_pickup_slot['enable']) {
				  				
				  				if((isset($individual_pickup_slot['max_order']) && $individual_pickup_slot['max_order'] !="")) {

					  				$pickup_hide_date_timeslot = isset($individual_pickup_slot['only_specific_date_close']) && $individual_pickup_slot['only_specific_date_close'] !="" ? sanitize_text_field($individual_pickup_slot['only_specific_date_close']) : "";
					  				$pickup_hide_date_timeslot = explode(",",$pickup_hide_date_timeslot);
					  				$pickup_available_date_timeslot = isset($individual_pickup_slot['only_specific_date']) && $individual_pickup_slot['only_specific_date'] !="" ? sanitize_text_field($individual_pickup_slot['only_specific_date']) : "";
					  				$pickup_available_date_timeslot = explode(",",$pickup_available_date_timeslot);
					  				foreach($second_period as $pickup_day) {
					  					$pickup_day = date("Y-m-d", strtotime($pickup_day->format("Y-m-d")));
					  					if(isset($individual_pickup_slot['only_specific_date']) && $individual_pickup_slot['only_specific_date'] !="" && !in_array($pickup_day,$pickup_available_date_timeslot)) {
					  						continue;
					  					}
					  					if((!empty($pickup_available_date_timeslot) && in_array($pickup_day,$pickup_available_date_timeslot)) || (!in_array($pickup_day,$pickup_hide_date_timeslot) && !in_array(date('w', strtotime($pickup_day)),$individual_pickup_slot['disable_for']))) {
					  						
					  						$max_order = sanitize_text_field($individual_pickup_slot['max_order']);

								  			if($individual_pickup_slot['enable_split']) {
												$times = explode('-', $key);
												$pickup_days_delivery[$pickup_day] = ((((int)$times[1] - (int)$times[0])/(int)$individual_pickup_slot['split_slot_duration'])*(int)$max_order);
											} else {
												if(isset($pickup_days_delivery[$pickup_day])) {
													$pickup_days_delivery[$pickup_day] = (int)$pickup_days_delivery[$pickup_day] + (int)$max_order;
												} else {
													$pickup_days_delivery[$pickup_day] = (int)$max_order;
												}
												
											}

					  					}
					  				}
				  				}
				  			}
				  		}
			  		} else {
  						$pickup_need_checking_max_order = false;
  					}
			  	}
			} else {

				$pickup_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
			    $enable_pickup_time = (isset($pickup_settings['enable_pickup_time']) && !empty($pickup_settings['enable_pickup_time'])) ? $pickup_settings['enable_pickup_time'] : false;
			    $max_order = (isset($pickup_settings['max_pickup_per_slot']) && $pickup_settings['max_pickup_per_slot'] !="") ? sanitize_text_field($pickup_settings['max_pickup_per_slot']) : "";
			    if($enable_pickup_time && $max_order != ""/* && (int)$max_order <= 15*/) {

			    	$each_pickup_slot = (isset($pickup_settings['each_time_slot']) && !empty($pickup_settings['each_time_slot'])) ? (int)$pickup_settings['each_time_slot'] : (int)$pickup_settings['pickup_time_ends']-(int)$pickup_settings['pickup_time_starts'];
			    	$day_max_order = ((((int)$pickup_settings['pickup_time_ends'] - (int)$pickup_settings['pickup_time_starts'])/(int)$each_pickup_slot)*(int)$max_order);
			    	
			    	foreach($second_period as $pickup_day) {
			    		if(isset($pickup_days_delivery[$pickup_day])) {
							$pickup_days_delivery[$pickup_day] = (int)$day_max_order;
						} else {
							$pickup_days_delivery[$pickup_day] = (int)$day_max_order;
						}
			    	}
				    
				} else {
					$pickup_need_checking_max_order = false;
				}
 		
		  	}

			if($pickup_need_checking_max_order) {
				foreach($second_period as $pickup_day) {
					if(isset($pickup_days_delivery[$pickup_day])) {
						
					    if($this->hpos) {
					    	$pickup_checking_max_order_args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $order_status,
								'meta_query' => array(
						            array(
						                'key'     => 'delivery_type',
						                'value'   => "pickup",
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'pickup_date',
						                'value'   => $pickup_day,
						                'compare' => '==',
						            ),
						        ),
						        'return' => 'ids'
						    );
					    } else {
					    	$pickup_checking_max_order_args = array(
						        'limit' => -1,
						        'pickup_date' => $pickup_day,
						        'delivery_type' => "pickup",
						        'status' => $order_status,
						        'return' => 'ids'
						    );
					    }
					    $orders_array = wc_get_orders( $pickup_checking_max_order_args );
					    if(count($orders_array) >= (int)$pickup_days_delivery[$pickup_day]) {
							$disable_for_max_pickup_dates[] = $pickup_day;
					    }
					}
				}
			}

			$disable_for_max_pickup_dates = array_unique($disable_for_max_pickup_dates, false);
			$disable_for_max_pickup_dates = array_values($disable_for_max_pickup_dates);
		} 

		if (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "") {

			$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');
			$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
			$period = $this->helper->get_date_from_range($range_first_date, $range_last_date, "Y-m-d");
			
			if(isset($max_per_day_count['delivery']['order']) && !empty($max_per_day_count['delivery']['order'])) {
				$delivery_keys = array_keys($max_per_day_count['delivery']['order']);	
			} else {
				$delivery_keys = [];
			}

			if(isset($max_per_day_count['pickup']['order']) && !empty($max_per_day_count['pickup']['order'])) {
				$pickup_keys = array_keys($max_per_day_count['pickup']['order']);	
			} else {
				$pickup_keys = [];
			}

			$period = array_intersect($period, array_values(array_unique(array_merge($delivery_keys,$pickup_keys))));

			$maximum_delivery_pickup_per_day = (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "") ? (int)$delivery_option_settings['maximum_delivery_pickup_per_day'] : 10000000000000;
			foreach ($period as $date) { 
				
			    $total_orders = 0;

			    if($max_per_day_count !== false) {
					if(isset($max_per_day_count['delivery']['order']) && array_key_exists($date, $max_per_day_count['delivery']['order'])) {
				    	if(isset($max_per_day_count['delivery']['order'][$date]) && $max_per_day_count['delivery']['order'][$date]!= '') {

				    		$total_orders = $total_orders + (int)$max_per_day_count['delivery']['order'][$date];
				    		
				    	}
				    }

				    if(isset($max_per_day_count['pickup']['order']) && array_key_exists($date, $max_per_day_count['pickup']['order'])) {
				    	if(isset($max_per_day_count['pickup']['order'][$date]) && $max_per_day_count['pickup']['order'][$date]!= '') {
				    		$total_orders = $total_orders + (int)$max_per_day_count['pickup']['order'][$date];				    		
				    	}
				    }
				}

				if($total_orders >= $maximum_delivery_pickup_per_day) {
					$disable_for_max_delivery_dates[] = $date;
					$disable_for_max_pickup_dates[] = $date;
			    }
			}

			$disable_for_max_delivery_dates = array_unique($disable_for_max_delivery_dates, false);
			$disable_for_max_delivery_dates = array_values($disable_for_max_delivery_dates);
			$disable_for_max_pickup_dates = array_unique($disable_for_max_pickup_dates, false);
			$disable_for_max_pickup_dates = array_values($disable_for_max_pickup_dates);

		}

		if (isset($delivery_option_settings['maximum_product_delivery_pickup_per_day']) && $delivery_option_settings['maximum_product_delivery_pickup_per_day'] != "") {

			$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
			$filtered_date = $range_first_date . ',' . $range_last_date;
			$filtered_dates = explode(',', $filtered_date);
			$period = new DatePeriod(new DateTime($filtered_dates[0]), new DateInterval('P1D'), new DateTime($filtered_dates[1].' +1 day'));


			$maximum_product_delivery_pickup_per_day = (isset($delivery_option_settings['maximum_product_delivery_pickup_per_day']) && $delivery_option_settings['maximum_product_delivery_pickup_per_day'] != "") ? (int)$delivery_option_settings['maximum_product_delivery_pickup_per_day'] : 10000000000000;

			foreach ($period as $date) { 
				
			    if($this->hpos) {
			    	$args = array(
				        'limit' => -1,
						'type' => array( 'shop_order' ),
						'status' => $order_status,
						'meta_query' => array(
				            array(
				                'key'     => 'delivery_type',
				                'value'   => "delivery",
				                'compare' => '==',
				            ),
				            array(
				                'key'     => 'delivery_date',
				                'value'   => date('Y-m-d', strtotime($date->format("Y-m-d"))),
				                'compare' => '==',
				            ),
				        ),
				        'return' => 'ids'
				    );
			    } else {
			    	$args = array(
				        'limit' => -1,
				        'delivery_date' => date("Y-m-d", strtotime($date->format("Y-m-d"))),
				        'delivery_type' => "delivery",
				        'status' => $order_status,
				        'return' => 'ids'
				    );
			    }
			    $delivery_array = wc_get_orders( $args );

			    if($this->hpos) {
			    	$pickup_args = array(
				        'limit' => -1,
						'type' => array( 'shop_order' ),
						'status' => $order_status,
						'meta_query' => array(
				            array(
				                'key'     => 'delivery_type',
				                'value'   => "pickup",
				                'compare' => '==',
				            ),
				            array(
				                'key'     => 'pickup_date',
				                'value'   => date('Y-m-d', strtotime($date->format("Y-m-d"))),
				                'compare' => '==',
				            ),
				        ),
				        'return' => 'ids'
				    );
			    } else {
			    	$pickup_args = array(
				        'limit' => -1,
				        'pickup_date' => date("Y-m-d", strtotime($date->format("Y-m-d"))),
				        'delivery_type' => "pickup",
				        'status' => $order_status,
				        'return' => 'ids'
				    );
			    }

			    $pickup_array = wc_get_orders( $pickup_args );

			    $delivery_pickup_order_array = array_unique(array_merge($delivery_array, $pickup_array));

			    $total_quantity = 0;
				foreach($delivery_pickup_order_array as $order_id) {
					
					$order = wc_get_order($order_id);
					
					foreach ( $order->get_items() as $item_id => $item ) {
						$total_quantity += $item->get_quantity();
					}
					
				}

			    if($total_quantity > $maximum_product_delivery_pickup_per_day) {
					$disable_for_max_delivery_dates[] = date('Y-m-d', strtotime($date->format("Y-m-d")));
					$disable_for_max_pickup_dates[] = date('Y-m-d', strtotime($date->format("Y-m-d")));
			    }
			}

			$disable_for_max_delivery_dates = array_unique($disable_for_max_delivery_dates, false);
			$disable_for_max_delivery_dates = array_values($disable_for_max_delivery_dates);
			$disable_for_max_pickup_dates = array_unique($disable_for_max_pickup_dates, false);
			$disable_for_max_pickup_dates = array_values($disable_for_max_pickup_dates);

		}

		$disable_delivery_date_passed_time = [];
		$disable_pickup_date_passed_time = [];

		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_time_settings');

		$enable_delivery_time = (isset($delivery_time_settings['enable_delivery_time']) && !empty($delivery_time_settings['enable_delivery_time'])) ? $delivery_time_settings['enable_delivery_time'] : false;

		$disabled_current_time_slot = (isset($delivery_time_settings['disabled_current_time_slot']) && !empty($delivery_time_settings['disabled_current_time_slot'])) ? $delivery_time_settings['disabled_current_time_slot'] : false;
	  	
		$enable_pickup_time = (isset($pickup_time_settings['enable_pickup_time']) && !empty($pickup_time_settings['enable_pickup_time'])) ? $pickup_time_settings['enable_pickup_time'] : false;

		$disabled_current_pickup_time_slot = (isset($pickup_time_settings['disabled_current_pickup_time_slot']) && !empty($pickup_time_settings['disabled_current_pickup_time_slot'])) ? $pickup_time_settings['disabled_current_pickup_time_slot'] : false;
		
		
		if($enable_delivery_time && $delivery_selection == "delivery") {

			$time_slot_end = [0];
			$custom_time_slot_settings = get_option('coderockz_woo_delivery_time_slot_settings');
			$enable_custom_time_slot = (isset($custom_time_slot_settings['enable_custom_time_slot']) && !empty($custom_time_slot_settings['enable_custom_time_slot'])) ? $custom_time_slot_settings['enable_custom_time_slot'] : false;
			if($enable_custom_time_slot) {
				if(isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0){				
					foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {

			  			if($individual_time_slot['enable']) {

			  				$hide_date_timeslot = isset($individual_time_slot['only_specific_date_close']) && $individual_time_slot['only_specific_date_close'] !="" ? sanitize_text_field($individual_time_slot['only_specific_date_close']) : "";
					  		$hide_date_timeslot = explode(",",$hide_date_timeslot);
					  		$available_date_timeslot = isset($individual_time_slot['only_specific_date']) && $individual_time_slot['only_specific_date'] !="" ? sanitize_text_field($individual_time_slot['only_specific_date']) : "";
					  		$available_date_timeslot = explode(",",$available_date_timeslot);

					  		$delivery_day = wp_date('Y-m-d',current_time( 'timestamp', 1 ));

			  				if((!empty($available_date_timeslot) && in_array($delivery_day,$available_date_timeslot)) || (!in_array($delivery_day,$hide_date_timeslot) && !in_array(date('w', strtotime($delivery_day)),$individual_time_slot['disable_for']))) {
				  				
				  				$key = preg_replace('/-/', ',', $key);

				  				$key_array = explode(",",$key);

						
							    if($individual_time_slot['enable_split']) {
							    	$x = $key_array[0];
									while($key_array[1]>$x) {
										$second_time = $x+$individual_time_slot['split_slot_duration'];
										if($second_time > $key_array[1]) {
											$second_time = $key_array[1];
										}
										if($individual_time_slot['enable_single_splited_slot']) {
											$time_slot_end[] = (int)$x;
										} else {
											if($disabled_current_time_slot) {
												$time_slot_end[] = (int)$second_time - (int)$individual_time_slot['split_slot_duration'];
											} else {
												$time_slot_end[] = (int)$second_time;
											}
										}
																		
										$x = $second_time;
									}
							    } else {
							    	if($individual_time_slot['enable_single_slot']) {
										$time_slot_end[] = (int)$individual_time_slot['start'];
									} else {
										$time_slot_end[] = (int)$individual_time_slot['end'];
									}
							    }

							}

						}
					}
				}
			} else {

				$time_settings = get_option('coderockz_woo_delivery_time_settings');
				if($disabled_current_time_slot && isset($time_settings['each_time_slot']) && $time_settings['each_time_slot'] != "") {
					$time_slot_end[] = (int)$time_settings['delivery_time_ends'] - (int)$time_settings['each_time_slot'];
				} else {
					$time_slot_end[] = (int)$time_settings['delivery_time_ends'];
				}											
			}

			$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');

			$enable_conditional_delivery_fee = isset($delivery_fee_settings['enable_conditional_delivery_fee']) && !empty($delivery_fee_settings['enable_conditional_delivery_fee']) ? $delivery_fee_settings['enable_conditional_delivery_fee'] : false;

			if($enable_conditional_delivery_fee && isset($delivery_fee_settings['conditional_delivery_time_ends']) && $delivery_fee_settings['conditional_delivery_time_ends'] !='') {
				$time_slot_end[] = (int)$delivery_fee_settings['conditional_delivery_time_ends'];
			}

			$highest_timeslot_end = max($time_slot_end);

			$current_time = (wp_date("G")*60)+wp_date("i");

			if($current_time>$highest_timeslot_end) {
				$disable_delivery_date_passed_time[] = wp_date('Y-m-d',current_time( 'timestamp', 1 ));
			}

		}

		if($enable_pickup_time && $delivery_selection == "pickup") {

			$pickup_slot_end = [0];
			$custom_pickup_slot_settings = get_option('coderockz_woo_delivery_pickup_slot_settings');
			$enable_custom_pickup_slot = (isset($custom_pickup_slot_settings['enable_custom_pickup_slot']) && !empty($custom_pickup_slot_settings['enable_custom_pickup_slot'])) ? $custom_pickup_slot_settings['enable_custom_pickup_slot'] : false;
			if($enable_custom_pickup_slot) {
				if(isset($custom_pickup_slot_settings['time_slot']) && count($custom_pickup_slot_settings['time_slot'])>0){

					foreach($custom_pickup_slot_settings['time_slot'] as $pickup_key => $individual_pickup_slot) {

			  			if($individual_pickup_slot['enable']) {

			  				$pickup_hide_date_timeslot = isset($individual_pickup_slot['only_specific_date_close']) && $individual_pickup_slot['only_specific_date_close'] !="" ? sanitize_text_field($individual_pickup_slot['only_specific_date_close']) : "";
					  		$pickup_hide_date_timeslot = explode(",",$pickup_hide_date_timeslot);
					  		$pickup_available_date_timeslot = isset($individual_pickup_slot['only_specific_date']) && $individual_pickup_slot['only_specific_date'] !="" ? sanitize_text_field($individual_pickup_slot['only_specific_date']) : "";
					  		$pickup_available_date_timeslot = explode(",",$pickup_available_date_timeslot);
					  		$pickup_day = wp_date('Y-m-d',current_time( 'timestamp', 1 ));
					  		if((!empty($pickup_available_date_timeslot) && in_array($pickup_day,$pickup_available_date_timeslot)) || (!in_array($pickup_day,$pickup_hide_date_timeslot) && !in_array(date('w', strtotime($pickup_day)),$individual_pickup_slot['disable_for']))) {

					  			$pickup_key = preg_replace('/-/', ',', $pickup_key);

					  			$pickup_key_array = explode(",",$pickup_key);
							
							    if($individual_pickup_slot['enable_split']) {
							    	$pickup_x = $pickup_key_array[0];
									while($pickup_key_array[1]>$pickup_x) {
										$pickup_second_time = $pickup_x+$individual_pickup_slot['split_slot_duration'];
										if($pickup_second_time > $pickup_key_array[1]) {
											$pickup_second_time = $pickup_key_array[1];
										}
										if($individual_pickup_slot['enable_single_splited_slot']) {
											$pickup_slot_end[] = (int)$pickup_x;
										} else {
											if($disabled_current_time_slot) {
												$pickup_slot_end[] = (int)$pickup_second_time - (int)$individual_pickup_slot['split_slot_duration'];
											} else {
												$pickup_slot_end[] = (int)$pickup_second_time;
											}
										}

										$pickup_x = $pickup_second_time;
									}
							    } else {
							    	if($individual_pickup_slot['enable_single_slot']) {
										$pickup_slot_end[] = (int)$individual_pickup_slot['start'];
									} else {
										$pickup_slot_end[] = (int)$individual_pickup_slot['end'];
									}
							    }

							}
						}
					}
				}
			} else {

		    	$pickup_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
				if($disabled_current_time_slot && isset($pickup_settings['each_time_slot']) && $pickup_settings['each_time_slot'] != "") {
					$pickup_slot_end[] = (int)$pickup_settings['pickup_time_ends'] - (int)$pickup_settings['each_time_slot'];
				} else {
					$pickup_slot_end[] = (int)$pickup_settings['pickup_time_ends'];
				}
			}

			$highest_pickupslot_end = max($pickup_slot_end);

			$current_time = (wp_date("G")*60)+wp_date("i");

			if($current_time>$highest_pickupslot_end) {
				$disable_pickup_date_passed_time[] = wp_date('Y-m-d',current_time( 'timestamp', 1 ));
			}

		}

		$response=[
			"disable_for_max_delivery_dates" => $disable_for_max_delivery_dates,
			"disable_for_max_pickup_dates" => $disable_for_max_pickup_dates,
			"disable_delivery_date_passed_time" => $disable_delivery_date_passed_time,
			"disable_pickup_date_passed_time" => $disable_pickup_date_passed_time,
		];
		$response = json_encode($response);
		wp_send_json_success($response);
		
	}

	public function override_post_meta_box_order( $order ) {

	    return array(
	        'normal' => join( 
	            ",", 
	            array(       // vvv  Arrange here as you desire
	                'order_data',
	                'coderockz_woo_delivery_meta_box',
	                'woocommerce-order-items',
	            )
	        ),
	    );
	}

	// Function to change email address
 
	public function coderockz_woo_delivery_sender_email( $original_email_address ) {
	    $notify_email_settings = get_option('coderockz_woo_delivery_notify_email_settings');
	    $send_email_from_email = isset($notify_email_settings['send_email_from_email']) && $notify_email_settings['send_email_from_email'] != "" ? $notify_email_settings['send_email_from_email'] : get_option( 'admin_email' );

	    return $send_email_from_email;
	}
	 
	// Function to change sender name
	public function coderockz_woo_delivery_sender_name( $original_email_from ) {
	    $notify_email_settings = get_option('coderockz_woo_delivery_notify_email_settings');
	    $send_email_from_name = isset($notify_email_settings['send_email_from_name']) && $notify_email_settings['send_email_from_name'] != "" ? stripslashes($notify_email_settings['send_email_from_name']) : get_bloginfo( 'name' );

	    return $send_email_from_name;
	}

	public function coderockz_woo_delivery_filter_orders_by_delivery() {

		global $typenow;

		if ( 'shop_order' === $typenow || (function_exists( 'wc_get_page_screen_id' ) && wc_get_page_screen_id( 'shop-order' ) === 'woocommerce_page_wc-orders')) {

		$today_placeholder = __('Today',"coderockz-woo-delivery");
		$tomorrow_placeholder = __('Tomorrow',"coderockz-woo-delivery");
		$this_week_placeholder = __('This Week',"coderockz-woo-delivery");
		$this_month_placeholder = __('This Month',"coderockz-woo-delivery");
		$custom_placeholder = __('Custom',"coderockz-woo-delivery");
		$delivery_placeholder = __('Delivery',"coderockz-woo-delivery");
		$pickup_placeholder = __('Pickup',"coderockz-woo-delivery");

		$delivery_date_filters = [];
		$delivery_date_filters[$today_placeholder] = 'today';
		$delivery_date_filters[$tomorrow_placeholder] = 'tomorrow';
		$delivery_date_filters[$this_week_placeholder] = 'week';
		$delivery_date_filters[$this_month_placeholder] = 'month';
		$delivery_date_filters[$custom_placeholder] = 'custom';

		$delivery_types = [];
		$delivery_types[$delivery_placeholder] = 'delivery';
		$delivery_types[$pickup_placeholder] = 'pickup';

		?>

		<select data-delivery_type_filter_text="<?php _e('Filter by Delivery Type', 'coderockz-woo-delivery'); ?>" name="_delivery_type" id="coderockz_woo_delivery_delivery_type_filter">
			<option value=""></option>
			<?php foreach ( $delivery_types as $label => $delivery_type ) : ?>
				<option value="<?php echo esc_attr( $delivery_type ); ?>" <?php echo esc_attr( isset( $_GET['_delivery_type'] ) ? selected( $delivery_type, $_GET['_delivery_type'], false ) : '' ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<select data-date_filter_text="<?php _e('Filter by Delivery/Pickup Date', 'coderockz-woo-delivery'); ?>" name="_date_filter" id="coderockz_woo_delivery_delivery_date_filter">
			<option value=""></option>
			<?php foreach ( $delivery_date_filters as $label => $date_filter ) : ?>
				<option value="<?php echo esc_attr( $date_filter ); ?>" <?php echo esc_attr( isset( $_GET['_date_filter'] ) ? selected( $date_filter, $_GET['_date_filter'], false ) : '' ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<input style="width:110px;float:left;display:none" id="coderockz_woo_delivery_custom_start_date_filter" name="coderockz_woo_delivery_custom_start_date_filter" type="text" class="regular-text coderockz_woo_delivery_custom_start_date_filter" value="<?php echo (isset($_GET['coderockz_woo_delivery_custom_start_date_filter']) && $_GET['coderockz_woo_delivery_custom_start_date_filter'] != "") ? stripslashes($_GET['coderockz_woo_delivery_custom_start_date_filter']) : "" ?>" placeholder="YYYY-MM-DD"/>

		<input style="width:110px;float:left;display:none" id="coderockz_woo_delivery_custom_end_date_filter" name="coderockz_woo_delivery_custom_end_date_filter" type="text" class="regular-text coderockz_woo_delivery_custom_end_date_filter" value="<?php echo (isset($_GET['coderockz_woo_delivery_custom_end_date_filter']) && $_GET['coderockz_woo_delivery_custom_end_date_filter'] != "") ? stripslashes($_GET['coderockz_woo_delivery_custom_end_date_filter']) : "" ?>" placeholder="YYYY-MM-DD"/>

		<?php
		}

	}

	/**
	 * Modify SQL JOIN for filtering the orders by any coupons used
	 *
 	 * @since 1.0.0
	 *
	 * @param string $join JOIN part of the sql query
	 * @return string $join modified JOIN part of sql query
	 */
	public function coderockz_woo_delivery_add_order_items_join($join) {

		global $typenow, $wpdb;

		if ( 'shop_order' === $typenow && isset( $_GET['_date_filter'] ) && !empty( $_GET['_date_filter'] ) && $_GET['_delivery_type'] == "" ) {

			$join .= "LEFT JOIN {$wpdb->prefix}postmeta wpm ON {$wpdb->posts}.ID = wpm.post_id";
		}

		if ( 'shop_order' === $typenow && isset( $_GET['_delivery_type'] ) && !empty( $_GET['_delivery_type'] ) && $_GET['_date_filter'] == "" ) {

			$join .= "LEFT JOIN {$wpdb->prefix}postmeta wpm ON {$wpdb->posts}.ID = wpm.post_id";
		}

		if ( 'shop_order' === $typenow && ( isset( $_GET['_delivery_type'] ) && !empty( $_GET['_delivery_type'] ) ) && ( isset( $_GET['_date_filter'] ) && !empty( $_GET['_date_filter'] ) ) ) {

			$join .= "LEFT JOIN {$wpdb->prefix}postmeta wpm ON {$wpdb->posts}.ID = wpm.post_id";
		}		

		return $join;
	}

	public static function coderockz_woo_delivery_type_date_filter_order_page_hpos( $clauses ) {

		global $wpdb;

		if ( (isset($_GET['page']) && 'wc-orders' === $_GET['page']) && isset( $_GET['_date_filter'] ) && !empty( $_GET['_date_filter'] ) && $_GET['_delivery_type'] == "" ) {

			$clauses['join'] .= " LEFT JOIN {$wpdb->prefix}wc_orders_meta wpm ON {$wpdb->prefix}wc_orders.id = wpm.order_id";
		}

		if ( (isset($_GET['page']) && 'wc-orders' === $_GET['page']) && isset( $_GET['_delivery_type'] ) && !empty( $_GET['_delivery_type'] ) && $_GET['_date_filter'] == "") {

			$clauses['join'] .= " LEFT JOIN {$wpdb->prefix}wc_orders_meta wpm ON {$wpdb->prefix}wc_orders.id = wpm.order_id";
		}

		if ( (isset($_GET['page']) && 'wc-orders' === $_GET['page']) && ( isset( $_GET['_delivery_type'] ) && !empty( $_GET['_delivery_type'] ) ) && ( isset( $_GET['_date_filter'] ) && !empty( $_GET['_date_filter'] ) ) ) {

			$clauses['join'] .= " LEFT JOIN {$wpdb->prefix}wc_orders_meta wpm ON {$wpdb->prefix}wc_orders.id = wpm.order_id";
		}

		if ( isset($_GET['page']) && 'wc-orders' === $_GET['page'] && isset( $_GET['_delivery_type'] ) && $_GET['_delivery_type'] !="" ) {

			if($_GET['_delivery_type'] == "delivery" && isset( $_GET['_date_filter'] ) && $_GET['_date_filter'] != "") {

				$clauses['where'] .= $wpdb->prepare( " AND wpm.meta_key='%s'",'delivery_date' );
			}

			if($_GET['_delivery_type'] == "pickup" && isset( $_GET['_date_filter'] ) && $_GET['_date_filter'] != "") {

				$clauses['where'] .= $wpdb->prepare( " AND wpm.meta_key='%s'",'pickup_date' );
			}

			if($_GET['_delivery_type'] == "delivery" && $_GET['_date_filter'] == "" && $_GET['coderockz_woo_delivery_custom_start_date_filter'] == "" && $_GET['coderockz_woo_delivery_custom_end_date_filter'] == "") {

				$clauses['where'] .= $wpdb->prepare( " AND wpm.meta_key='%s'",'delivery_type' );
				$clauses['where'] .= $wpdb->prepare( " AND wpm.meta_value='%s'",'delivery' );
			}

			if($_GET['_delivery_type'] == "pickup" && $_GET['_date_filter'] == "" && $_GET['coderockz_woo_delivery_custom_start_date_filter'] == "" && $_GET['coderockz_woo_delivery_custom_end_date_filter'] == "") {

				$clauses['where'] .= $wpdb->prepare( " AND wpm.meta_key='%s'",'delivery_type' );
				$clauses['where'] .= $wpdb->prepare( " AND wpm.meta_value='%s'",'pickup' );
			}

		}

		if ( isset($_GET['page']) && 'wc-orders' === $_GET['page'] && isset( $_GET['_date_filter'] ) && $_GET['_date_filter'] != "" ) {

			$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');

			if($_GET['_date_filter'] == "week") {
				$week_starts_from = (isset($delivery_date_settings['week_starts_from']) && !empty($delivery_date_settings['week_starts_from'])) ? $delivery_date_settings['week_starts_from']:"0";

				switch ($week_starts_from) {
				    case "0":
				        $week_day_start = "sunday";
				        break;
				    case "1":
				        $week_day_start = "monday";
				        break;
				    case "2":
				        $week_day_start = "tuesday";
				        break;
				    case "3":
				        $week_day_start = "wednesday";
				        break;
				    case "4":
				        $week_day_start = "thursday";
				        break;
				    case "5":
				        $week_day_start = "friday";
				        break;
				    case "6":
				        $week_day_start = "saturday";
				        break;
				}

				$week_start = strtotime( wp_date( 'Y-m-d H:i:s', strtotime( "last ".$week_day_start) ) );
				$week_start = wp_date('w', $week_start)==wp_date('w') ? $week_start+7*86400 : $week_start;

				$week_end = strtotime( wp_date( 'Y-m-d H:i:s', strtotime(wp_date("Y-m-d",$week_start)." +6 days") ) );

				$this_week_start = wp_date("Y-m-d",$week_start);
				$this_week_end = wp_date("Y-m-d",$week_end);

				$get_date_filter = $this_week_start." - ".$this_week_end;
			}	
			

			if($_GET['_date_filter'] == "month") {

			    $day_today = wp_date('Y-m-d',current_time( 'timestamp', 1 ));
			    $this_month_first_day = current_datetime($day_today )->modify('first day of this month')->format('Y-m-d');
			    $this_month_last_day = current_datetime($day_today )->modify('last day of this month')->format('Y-m-d');

			    $get_date_filter = $this_month_first_day." - ".$this_month_last_day;


			}

			if($_GET['_date_filter'] == "today") {
				$get_date_filter = wp_date('Y-m-d',current_time( 'timestamp', 1 ));
			}

			if($_GET['_date_filter'] == "tomorrow") {
				$get_date_filter = wp_date("Y-m-d", current_time( 'timestamp', 1 )+86400);
			}
			
			if($_GET['_date_filter'] == "custom") {
				if((isset($_GET['coderockz_woo_delivery_custom_start_date_filter']) && $_GET['coderockz_woo_delivery_custom_start_date_filter'] !="") && (isset($_GET['coderockz_woo_delivery_custom_end_date_filter']) && $_GET['coderockz_woo_delivery_custom_end_date_filter'] !="")) {
					$get_date_filter = $_GET['coderockz_woo_delivery_custom_start_date_filter'].' - '.$_GET['coderockz_woo_delivery_custom_end_date_filter'];
				}

			}

			// Main WHERE query part
			if(strpos($get_date_filter, ' - ') !== false) {

				$filtered_dates = explode(' - ', $get_date_filter);
				$period = new DatePeriod(new DateTime($filtered_dates[0]), new DateInterval('P1D'), new DateTime($filtered_dates[1].' +1 day'));
				//$period = $this->helper->get_date_from_range($filtered_dates[0], $filtered_dates[1], "Y-m-d");
			    $query = '';
			    $dates = [];
			    foreach ($period as $date) {
			    	$dates [] = $date->format("Y-m-d");
			    	$query .= "wpm.meta_value='%s' OR ";	   	
			    }

			    $final_query = substr($query, 0, -4);

			    $clauses['where'] .= $wpdb->prepare( " AND (".$final_query.")",$dates);
			    

			} else {
				$clauses['where'] .= $wpdb->prepare( " AND wpm.meta_value='%s'", $get_date_filter );
			}
			
		}

		return $clauses;
	}


	/**
	 * Modify SQL WHERE for filtering the orders by any coupons used
	 *
	 * @since 1.0.0
	 *
	 * @param string $where WHERE part of the sql query
	 * @return string $where modified WHERE part of sql query
	 */
	public function coderockz_woo_delivery_add_filterable_where( $where ) {
		global $typenow, $wpdb;

		if ( 'shop_order' === $typenow && isset( $_GET['_delivery_type'] ) && $_GET['_delivery_type'] !="" ) {

			if($_GET['_delivery_type'] == "delivery" && isset( $_GET['_date_filter'] ) && $_GET['_date_filter'] != "") {

				$where .= $wpdb->prepare( " AND wpm.meta_key='%s'",'delivery_date' );
			}

			if($_GET['_delivery_type'] == "pickup" && isset( $_GET['_date_filter'] ) && $_GET['_date_filter'] != "") {

				$where .= $wpdb->prepare( " AND wpm.meta_key='%s'",'pickup_date' );
			}

			if($_GET['_delivery_type'] == "delivery" && $_GET['_date_filter'] == "" && $_GET['coderockz_woo_delivery_custom_start_date_filter'] == "" && $_GET['coderockz_woo_delivery_custom_end_date_filter'] == "") {

				$where .= $wpdb->prepare( " AND wpm.meta_key='%s'",'delivery_type' );
				$where .= $wpdb->prepare( " AND wpm.meta_value='%s'",'delivery' );
			}

			if($_GET['_delivery_type'] == "pickup" && $_GET['_date_filter'] == "" && $_GET['coderockz_woo_delivery_custom_start_date_filter'] == "" && $_GET['coderockz_woo_delivery_custom_end_date_filter'] == "") {

				$where .= $wpdb->prepare( " AND wpm.meta_key='%s'",'delivery_type' );
				$where .= $wpdb->prepare( " AND wpm.meta_value='%s'",'pickup' );
			}

		}

		if ( 'shop_order' === $typenow && isset( $_GET['_date_filter'] ) && $_GET['_date_filter'] != "" ) {

			$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');

			if($_GET['_date_filter'] == "week") {
				$week_starts_from = (isset($delivery_date_settings['week_starts_from']) && !empty($delivery_date_settings['week_starts_from'])) ? $delivery_date_settings['week_starts_from']:"0";
				$this_week_array = get_weekstartend( wp_date('Y-m-d',current_time( 'timestamp', 1 )), $week_starts_from );
				$get_date_filter = wp_date('Y-m-d', $this_week_array['start'])." - ".wp_date('Y-m-d', $this_week_array['end']-86400);

			}	
			

			if($_GET['_date_filter'] == "month") {
				$day_today = wp_date('Y-m-d',current_time( 'timestamp', 1 ));
			    $this_month_first_day = current_datetime($day_today )->modify('first day of this month')->format('Y-m-d');
			    $this_month_last_day = current_datetime($day_today )->modify('last day of this month')->format('Y-m-d');

			    $get_date_filter = $this_month_first_day." - ".$this_month_last_day;

			}

			if($_GET['_date_filter'] == "today") {
				$get_date_filter = wp_date('Y-m-d',current_time( 'timestamp', 1 ));
			}

			if($_GET['_date_filter'] == "tomorrow") {
				$get_date_filter = wp_date("Y-m-d", current_time( 'timestamp', 1 )+86400);
			}

			
			if($_GET['_date_filter'] == "custom") {
				if((isset($_GET['coderockz_woo_delivery_custom_start_date_filter']) && $_GET['coderockz_woo_delivery_custom_start_date_filter'] !="") && (isset($_GET['coderockz_woo_delivery_custom_end_date_filter']) && $_GET['coderockz_woo_delivery_custom_end_date_filter'] !="")) {
					$get_date_filter = $_GET['coderockz_woo_delivery_custom_start_date_filter'].' - '.$_GET['coderockz_woo_delivery_custom_end_date_filter'];
				}

			}

			// Main WHERE query part
			if(strpos($get_date_filter, ' - ') !== false) {

				$filtered_dates = explode(' - ', $get_date_filter);
				$period = new DatePeriod(new DateTime($filtered_dates[0]), new DateInterval('P1D'), new DateTime($filtered_dates[1].' +1 day'));
			    $query = '';
			    $dates = [];
			    foreach ($period as $date) {
			    	$dates [] = $date->format("Y-m-d");
			    	$query .= "wpm.meta_value='%s' OR ";	   	
			    }

			    $final_query = substr($query, 0, -4);

			    $where .= $wpdb->prepare( " AND (".$final_query.")",$dates);
			    

			} else {
				$where .= $wpdb->prepare( " AND wpm.meta_value='%s'", $get_date_filter );
			}
			
		}

		return $where;
	}

	public function coderockz_woo_delivery_get_order_details_for_delivery_calender() {

		$timezone = $this->helper->get_the_timezone();

		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_time_settings');

		$enable_delivery_time = (isset($delivery_time_settings['enable_delivery_time']) && !empty($delivery_time_settings['enable_delivery_time'])) ? $delivery_time_settings['enable_delivery_time'] : false;
	  	
		$enable_pickup_time = (isset($pickup_time_settings['enable_pickup_time']) && !empty($pickup_time_settings['enable_pickup_time'])) ? $pickup_time_settings['enable_pickup_time'] : false;

		if($enable_delivery_time && (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))) {
			if($delivery_time_settings['time_format'] == 12) {
				$time_format = true;
			} elseif ($delivery_time_settings['time_format'] == 24) {
				$time_format = false;
			} 
		} elseif($enable_pickup_time && (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))) {
			if($pickup_time_settings['time_format'] == 12) {
				$time_format = true;
			} elseif ($pickup_time_settings['time_format'] == 24) {
				$time_format = false;
			}
		} else {
			$time_format = false;
		}

		$filtered_delivery_type = sanitize_text_field($_POST[ 'filteredDeliveryType' ]);
		$filtered_filter_type = sanitize_text_field($_POST[ 'filteredFilterType' ]);
		$filtered_status_type = $this->helper->coderockz_woo_delivery_array_sanitize($_POST[ 'filteredStatusType' ]);
		if(isset($_POST[ 'filteredLocationType' ])) {

			$filtered_pickup_location = [];
    		$filtered_pickup_location_before = $this->helper->coderockz_woo_delivery_array_sanitize($_POST[ 'filteredLocationType' ]);
    		foreach($filtered_pickup_location_before as $location) {
    			$filtered_pickup_location[] = stripslashes($location);
    		}
    		$filtered_pickup_location = array_filter($filtered_pickup_location, 'strlen');

		} else {
			$filtered_pickup_location = [];
		}


		
		if(sanitize_text_field($_POST[ 'counter' ]) == 0) {
			$day_today = strtotime (date('Y-m-d', time()));
			$this_month_first_day = date ('Y-m-d', strtotime ('first day of this month', $day_today));
			
			$formated = date('Y-m-d H:i:s', strtotime($this_month_first_day));
			$formated_obj = new DateTime($formated);
			$this_month_last_day = $formated_obj->modify("+15 day")->format("Y-m-d");

		} elseif(sanitize_text_field($_POST[ 'counter' ]) == 1) {
			$day_today = strtotime (date('Y-m-d', time()));
			$this_month_first_day = date ('Y-m-d', strtotime ('first day of this month', $day_today));
			$this_month_last_day = date ('Y-m-d', strtotime ('last day of this month', $day_today));
			
			$formated = date('Y-m-d H:i:s', strtotime($this_month_first_day));
			$formated_obj = new DateTime($formated);
			$partial_month_first_day = $formated_obj->modify("+16 day")->format("Y-m-d");
			$this_month_first_day = $partial_month_first_day;
		} elseif(sanitize_text_field($_POST[ 'counter' ]) == 2) {

			$this_month_first_day = date('Y-m-d', strtotime('first day of +1 month'));

			$formated = date('Y-m-d H:i:s', strtotime($this_month_first_day));
			$formated_obj = new DateTime($formated);
			$this_month_last_day = $formated_obj->modify("+15 day")->format("Y-m-d");

		} elseif(sanitize_text_field($_POST[ 'counter' ]) == 3) {

			$this_month_first_day = date('Y-m-d', strtotime('first day of +1 month'));
			$this_month_last_day = date ('Y-m-d', strtotime ('last day of +1 month'));
			
			$formated = date('Y-m-d H:i:s', strtotime($this_month_first_day));
			$formated_obj = new DateTime($formated);
			$partial_month_first_day = $formated_obj->modify("+16 day")->format("Y-m-d");
			$this_month_first_day = $partial_month_first_day;
		} 

	    $this_month = $this_month_first_day." - ".$this_month_last_day;

	    $filtered_dates = explode(' - ', $this_month);
		$orders = [];
		$delivery_orders = [];
		$pickup_orders = [];
		$period = new DatePeriod(new DateTime($filtered_dates[0]), new DateInterval('P1D'), new DateTime($filtered_dates[1].' +1 day'));
	    foreach ($period as $date) {
	        $dates[] = $date->format("Y-m-d");
	    }
	    
		if($filtered_filter_type == 'product') {
			$response_orders_by_quantity = [];
			foreach ($dates as $date) {
				
		    	if($filtered_delivery_type == "delivery"){
		    		$product_name = [];
					$product_quantity = [];
					$product_sku = [];

					if($this->hpos) {
						$args = array(
					        'limit' => -1,
							'type' => array( 'shop_order' ),
							'status' => $filtered_status_type,
							'meta_query' => array(
					            array(
					                'key'     => 'delivery_date',
					                'value'   => date("Y-m-d", strtotime($date)),
					                'compare' => '==',
					            ),
					            array(
					                'key'     => 'delivery_type',
					                'value'   => 'delivery',
					                'compare' => '==',
					            ),
					        ),
					    );
					} else {
						$args = array(
					        'limit' => -1,
					        'delivery_date' => date("Y-m-d", strtotime($date)),
					        'delivery_type' => "delivery",
					        'status' => $filtered_status_type
					    );
					}
		    		
				    $orders_array = wc_get_orders( $args );

				    $other_settings = get_option('coderockz_woo_delivery_other_settings');
				    $hide_metadata_reports_calendar = (isset($other_settings['hide_metadata_reports_calendar']) && !empty($other_settings['hide_metadata_reports_calendar'])) ? $other_settings['hide_metadata_reports_calendar'] : false;
				    foreach ($orders_array as $order) {

					    foreach ( $order->get_items() as $item_id => $item ) {
						   if($item->get_variation_id() == 0) {
						   		
						   	   if($hide_metadata_reports_calendar) {
						   	   		if(array_key_exists($item->get_product_id(),$product_quantity)) {
							   		   $product_quantity[$item->get_product_id()] = $product_quantity[$item->get_product_id()]+$item->get_quantity();
								   } else {
								   	   $product_quantity[$item->get_product_id()] = $item->get_quantity();
								   }
								   if(!array_key_exists($item->get_product_id(),$product_name)) {
								   	   $product_name[$item->get_product_id()] = $item->get_name();
								   }
								   if(!array_key_exists($item->get_product_id(),$product_sku)) {
								   	   $product_sku[$item->get_product_id()] = get_post_meta( $item->get_product_id(), '_sku', true );
								   }
						   	   } else {
						   	   		$item_name = $item->get_name();
									$item_meta_data = $item->get_formatted_meta_data();
									if(!empty($item_meta_data)) {
										foreach ( $item_meta_data as $meta_id => $meta ) {
											$item_name .= ', '.wp_kses_post( strip_tags($meta->value) );
										}
									}
							   		
							   	   if(array_key_exists($item_name,$product_quantity)) {
								   		$product_quantity[$item_name] = $product_quantity[$item_name]+$item->get_quantity();
								   } else {
								   		$product_quantity[$item_name] = $item->get_quantity();
								   }
								   if(!array_key_exists($item_name,$product_name)) {
								   		$product_name[$item_name] = $item_name;
								   }

								   if(!array_key_exists($item_name,$product_sku)) {
								   		$product_sku[$item_name] = get_post_meta( $item->get_product_id(), '_sku', true );
								   }
						   	   }

						   } else {
						   		
						   	   $variation = new WC_Product_Variation($item->get_variation_id());
							   $item_meta_data = $item->get_formatted_meta_data();
							   $item_name_with_meta = $variation->get_title();
							   
							   if(array_filter($variation->get_variation_attributes())) {
									$item_name_with_meta .= " - ".strip_tags(implode(", ", array_filter($variation->get_variation_attributes(), 'strlen')));   	
								}

								if(!empty($item_meta_data) && !$hide_metadata_reports_calendar) {
							        foreach ( $item_meta_data as $meta_id => $meta ) {
							        	if (!array_key_exists("attribute_".$meta->key,$variation->get_variation_attributes()) || (array_key_exists("attribute_".$meta->key,$variation->get_variation_attributes()) && $variation->get_variation_attributes()["attribute_".$meta->key] == "") )
							            	$item_name_with_meta .= ', '.wp_kses_post( strip_tags($meta->display_value) );

							        }
							    }

							    if(!array_key_exists($item_name_with_meta,$product_name)) {
							   		$product_name[$item_name_with_meta] = $item_name_with_meta;
							    }

							    if(array_key_exists($item_name_with_meta,$product_quantity)) {
							   		$product_quantity[$item_name_with_meta] = $product_quantity[$item_name_with_meta]+$item->get_quantity();
							   } else {
							   		$product_quantity[$item_name_with_meta] = $item->get_quantity();
							   }
							}

						}

				    }

				    foreach($product_name as $id => $name) {
				    	$title = '';
				    	$temp_orders_quantity = [];
				        $title .= $name;
				        if($product_sku[$id] != "") {
				        	$title .= '('.$product_sku[$id].')';
				        }
				        $title .= ' x '.$product_quantity[$id];
				        $temp_orders_quantity['start'] = $date;
						$temp_orders_quantity['end'] = $date;
						$temp_orders_quantity['title'] = $title;
						$response_orders_by_quantity[] = $temp_orders_quantity;
					}
				    

		    	} elseif($filtered_delivery_type == "pickup") {
		    		$product_name = [];
					$product_quantity = [];
					$product_sku = [];

					$orders_array = [];

					if(!empty($filtered_pickup_location)) {
						foreach($filtered_pickup_location as $location) {
							if($this->hpos) {
								$args = array(
							        'limit' => -1,
									'type' => array( 'shop_order' ),
									'status' => $filtered_status_type,
									'meta_query' => array(
							            array(
							                'key'     => 'pickup_date',
							                'value'   => date("Y-m-d", strtotime($date)),
							                'compare' => '==',
							            ),
							            array(
							                'key'     => 'pickup_location',
							                'value'   => $location,
							                'compare' => '==',
							            ),
							            array(
							                'key'     => 'delivery_type',
							                'value'   => "pickup",
							                'compare' => '==',
							            ),
							        ),
							    );
							} else {
								$args = array(
							        'limit' => -1,
							        'pickup_date' => date("Y-m-d", strtotime($date)),
							        'delivery_type' => "pickup",
							        'status' => $filtered_status_type,
							        'pickup_location' => $location
							    );
							}

						    $orders = wc_get_orders( $args );
						    foreach ($orders as $order) {
						    	$orders_array[] = $order;
						    }
						}
					} else {
						if($this->hpos) {
							$args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $filtered_status_type,
								'meta_query' => array(
						            array(
						                'key'     => 'pickup_date',
						                'value'   => date("Y-m-d", strtotime($date)),
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'delivery_type',
						                'value'   => "pickup",
						                'compare' => '==',
						            ),
						        ),
						    );
						} else {
							$args = array(
						        'limit' => -1,
						        'pickup_date' => date("Y-m-d", strtotime($date)),
						        'delivery_type' => "pickup",
						        'status' => $filtered_status_type
						    );
						}
					    $orders = wc_get_orders( $args );
					    foreach ($orders as $order) {
					    	$orders_array[] = $order;
					    }
					}

					$other_settings = get_option('coderockz_woo_delivery_other_settings');
					$hide_metadata_reports_calendar = (isset($other_settings['hide_metadata_reports_calendar']) && !empty($other_settings['hide_metadata_reports_calendar'])) ? $other_settings['hide_metadata_reports_calendar'] : false;

				    foreach ($orders_array as $order) {
				    	foreach ( $order->get_items() as $item_id => $item ) {
						   if($item->get_variation_id() == 0) {

						   	    if($hide_metadata_reports_calendar) {
									if(array_key_exists($item->get_product_id(),$product_quantity)) {
									   $product_quantity[$item->get_product_id()] = $product_quantity[$item->get_product_id()]+$item->get_quantity();
									} else {
										   $product_quantity[$item->get_product_id()] = $item->get_quantity();
									}
									if(!array_key_exists($item->get_product_id(),$product_name)) {
										   $product_name[$item->get_product_id()] = $item->get_name();
									}
									if(!array_key_exists($item->get_product_id(),$product_sku)) {
										   $product_sku[$item->get_product_id()] = get_post_meta( $item->get_product_id(), '_sku', true );
									}
								} else {

							   	    $item_name = $item->get_name();
									$item_meta_data = $item->get_formatted_meta_data();
									if(!empty($item_meta_data)) {
										foreach ( $item_meta_data as $meta_id => $meta ) {
											$item_name .= ', '.wp_kses_post( strip_tags($meta->value) );
										}
									}
							   	   if(array_key_exists($item_name,$product_quantity)) {
								   		$product_quantity[$item_name] = $product_quantity[$item_name]+$item->get_quantity();
								   } else {
								   		$product_quantity[$item_name] = $item->get_quantity();
								   }
								   if(!array_key_exists($item_name,$product_name)) {
								   		$product_name[$item_name] = $item_name;
								   }
								   if(!array_key_exists($item_name,$product_sku)) {
								   		$product_sku[$item_name] = get_post_meta( $item->get_product_id(), '_sku', true );
								   }
								}
						   } else {
						   	   
						   	   $variation = new WC_Product_Variation($item->get_variation_id());
							   $item_meta_data = $item->get_formatted_meta_data();
							   $item_name_with_meta = $variation->get_title();
							   
							   if(array_filter($variation->get_variation_attributes())) {
									$item_name_with_meta .= " - ".strip_tags(implode(", ", array_filter($variation->get_variation_attributes(), 'strlen')));   	
								}

								if(!empty($item_meta_data) && !$hide_metadata_reports_calendar) {
							        foreach ( $item_meta_data as $meta_id => $meta ) {
							        	if (!array_key_exists("attribute_".$meta->key,$variation->get_variation_attributes()) || (array_key_exists("attribute_".$meta->key,$variation->get_variation_attributes()) && $variation->get_variation_attributes()["attribute_".$meta->key] == "") )
							            	$item_name_with_meta .= ', '.wp_kses_post( strip_tags($meta->display_value) );

							        }
							    }


							    if(!array_key_exists($item_name_with_meta,$product_name)) {
							   		$product_name[$item_name_with_meta] = $item_name_with_meta;
							    }

							    if(array_key_exists($item_name_with_meta,$product_quantity)) {
							   		$product_quantity[$item_name_with_meta] = $product_quantity[$item_name_with_meta]+$item->get_quantity();
							   } else {
							   		$product_quantity[$item_name_with_meta] = $item->get_quantity();
							   }
						   }

						}
				    }

				    foreach($product_name as $id => $name) {
				    	$title = '';
				    	$temp_orders_quantity = [];
				        $title .= $name;
				        if($product_sku[$id] != "") {
				        	$title .= '('.$product_sku[$id].')';
				        }
				        $title .= ' x '.$product_quantity[$id];
				        $temp_orders_quantity['start'] = $date;
						$temp_orders_quantity['end'] = $date;
						$temp_orders_quantity['title'] = $title;
						$response_orders_by_quantity[] = $temp_orders_quantity;
					}
		    	} else {
		    		$product_name = [];
					$product_quantity = [];
					$product_sku = [];
					$delivery_orders = [];
					$pickup_orders = [];

				    if($this->hpos) {
				    	$args = array(
					        'limit' => -1,
							'type' => array( 'shop_order' ),
							'status' => $filtered_status_type,
							'meta_query' => array(
					            array(
					                'key'     => 'delivery_date',
					                'value'   => date("Y-m-d", strtotime($date)),
					                'compare' => '==',
					            ),
					        ),
					    );
				    } else {
				    	$args = array(
					        'limit' => -1,
					        'delivery_date' => date("Y-m-d", strtotime($date)),
					        'status' => $filtered_status_type
					    );
				    }

				    $orders_array = wc_get_orders( $args );
				    foreach ($orders_array as $order) {
				    	$delivery_orders[] = $order;
				    }

					if(!empty($filtered_pickup_location)) {

						foreach($filtered_pickup_location as $location) {

							if($this->hpos) {
								$args = array(
							        'limit' => -1,
									'type' => array( 'shop_order' ),
									'status' => $filtered_status_type,
									'meta_query' => array(
							            array(
							                'key'     => 'pickup_date',
							                'value'   => date("Y-m-d", strtotime($date)),
							                'compare' => '==',
							            ),
							            array(
							                'key'     => 'pickup_location',
							                'value'   => $location,
							                'compare' => '==',
							            ),
							        ),
							    );
							} else {
								$args = array(
							        'limit' => -1,
							        'pickup_date' => date("Y-m-d", strtotime($date)),
							        'status' => $filtered_status_type,
							        'pickup_location' => $location
							    );
							}
						    $orders_array = wc_get_orders( $args );
						    foreach ($orders_array as $order) {
						    	$pickup_orders[] = $order;
						    }
						}
					} else {
						if($this->hpos) {
							$args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $filtered_status_type,
								'meta_query' => array(
						            array(
						                'key'     => 'pickup_date',
						                'value'   => date("Y-m-d", strtotime($date)),
						                'compare' => '==',
						            ),
						        ),
						    );
						} else {
							$args = array(
						        'limit' => -1,
						        'pickup_date' => date("Y-m-d", strtotime($date)),
						        'status' => $filtered_status_type
						    );
						}
						
					    $orders_array = wc_get_orders( $args );
					    foreach ($orders_array as $order) {
					    	$pickup_orders[] = $order;
					    }
					}

				    $orders = array_merge($delivery_orders, $pickup_orders);
				    $other_settings = get_option('coderockz_woo_delivery_other_settings');
					$hide_metadata_reports_calendar = (isset($other_settings['hide_metadata_reports_calendar']) && !empty($other_settings['hide_metadata_reports_calendar'])) ? $other_settings['hide_metadata_reports_calendar'] : false;
		    	
				    foreach ($orders as $order) {

				    	foreach ( $order->get_items() as $item_id => $item ) {
						   if($item->get_variation_id() == 0) {

							   	if($hide_metadata_reports_calendar) {
									if(array_key_exists($item->get_product_id(),$product_quantity)) {
									   $product_quantity[$item->get_product_id()] = $product_quantity[$item->get_product_id()]+$item->get_quantity();
									} else {
										   $product_quantity[$item->get_product_id()] = $item->get_quantity();
									}
									if(!array_key_exists($item->get_product_id(),$product_name)) {
										   $product_name[$item->get_product_id()] = $item->get_name();
									}
									if(!array_key_exists($item->get_product_id(),$product_sku)) {
										   $product_sku[$item->get_product_id()] = get_post_meta( $item->get_product_id(), '_sku', true );
									}
								} else {

							   		$item_name = $item->get_name();
									$item_meta_data = $item->get_formatted_meta_data();
									if(!empty($item_meta_data)) {
										foreach ( $item_meta_data as $meta_id => $meta ) {
											$item_name .= ', '.wp_kses_post( strip_tags($meta->value) );
										}
									}
							   	   if(array_key_exists($item_name,$product_quantity)) {
								   		$product_quantity[$item_name] = $product_quantity[$item_name]+$item->get_quantity();
								   } else {
								   		$product_quantity[$item_name] = $item->get_quantity();
								   }
								   if(!array_key_exists($item_name,$product_name)) {
								   		$product_name[$item_name] = $item_name;
								   }
								   if(!array_key_exists($item_name,$product_sku)) {
								   		$product_sku[$item_name] = get_post_meta( $item->get_product_id(), '_sku', true );
								   }
								}
						   } else {
						   	   $variation = new WC_Product_Variation($item->get_variation_id());
							   $item_meta_data = $item->get_formatted_meta_data();
							   $item_name_with_meta = $variation->get_title();
							   
							   if(array_filter($variation->get_variation_attributes())) {
									$item_name_with_meta .= " - ".strip_tags(implode(", ", array_filter($variation->get_variation_attributes(), 'strlen')));   	
								}

								if(!empty($item_meta_data) && !$hide_metadata_reports_calendar) {
							        foreach ( $item_meta_data as $meta_id => $meta ) {
							        	if (!array_key_exists("attribute_".$meta->key,$variation->get_variation_attributes()) || (array_key_exists("attribute_".$meta->key,$variation->get_variation_attributes()) && $variation->get_variation_attributes()["attribute_".$meta->key] == "") )
							            	$item_name_with_meta .= ', '.wp_kses_post( strip_tags($meta->display_value) );

							        }
							    }


							    if(!array_key_exists($item_name_with_meta,$product_name)) {
							   		$product_name[$item_name_with_meta] = $item_name_with_meta;
							    }

							    if(array_key_exists($item_name_with_meta,$product_quantity)) {
							   		$product_quantity[$item_name_with_meta] = $product_quantity[$item_name_with_meta]+$item->get_quantity();
							   } else {
							   		$product_quantity[$item_name_with_meta] = $item->get_quantity();
							   }
						   }

						}
				    }

				    foreach($product_name as $id => $name) {
				    	$title = '';
				    	$temp_orders_quantity = [];
				        $title .= $name;
				        if($product_sku[$id] != "") {
				        	$title .= '('.$product_sku[$id].')';
				        }
				        $title .= ' x '.$product_quantity[$id];
				        $temp_orders_quantity['start'] = $date;
						$temp_orders_quantity['end'] = $date;
						$temp_orders_quantity['title'] = $title;
						$response_orders_by_quantity[] = $temp_orders_quantity;
					}

		    	}
			    
		    }

			$final_response = $response_orders_by_quantity;
		
		} else {

			$response_orders = [];

			foreach ($dates as $date) {
		    	if($filtered_delivery_type == "delivery"){

				    if($this->hpos) {
				    	$args = array(
					        'limit' => -1,
							'type' => array( 'shop_order' ),
							'status' => $filtered_status_type,
							'meta_query' => array(
					            array(
					                'key'     => 'delivery_date',
					                'value'   => date("Y-m-d", strtotime($date)),
					                'compare' => '==',
					            ),
					            array(
					                'key'     => 'delivery_type',
					                'value'   => 'delivery',
					                'compare' => '==',
					            ),
					        ),
					    );
				    } else {
				    	$args = array(
					        'limit' => -1,
					        'delivery_date' => date("Y-m-d", strtotime($date)),
					        'delivery_type' => "delivery",
					        'status' => $filtered_status_type
					    );
				    }
				    $orders_array = wc_get_orders( $args );
				    foreach ($orders_array as $order) {
				    	$orders[] = $order;
				    }
		    	} elseif($filtered_delivery_type == "pickup") {

		    		if(!empty($filtered_pickup_location)) {
						foreach($filtered_pickup_location as $location) {
							if($this->hpos) {
								$args = array(
							        'limit' => -1,
									'type' => array( 'shop_order' ),
									'status' => $filtered_status_type,
									'meta_query' => array(
							            array(
							                'key'     => 'pickup_date',
							                'value'   => date("Y-m-d", strtotime($date)),
							                'compare' => '==',
							            ),
							            array(
							                'key'     => 'pickup_location',
							                'value'   => $location,
							                'compare' => '==',
							            ),
							            array(
							                'key'     => 'delivery_type',
							                'value'   => "pickup",
							                'compare' => '==',
							            ),
							        ),
							    );
							} else {
								$args = array(
							        'limit' => -1,
							        'pickup_date' => date("Y-m-d", strtotime($date)),
							        'delivery_type' => "pickup",
							        'status' => $filtered_status_type,
							        'pickup_location' => $location
							    );
							}

						    $orders_array = wc_get_orders( $args );
						    foreach ($orders_array as $order) {
						    	$orders[] = $order;
						    }
						}
					} else {
						if($this->hpos) {
							$args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $filtered_status_type,
								'meta_query' => array(
						            array(
						                'key'     => 'pickup_date',
						                'value'   => date("Y-m-d", strtotime($date)),
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'delivery_type',
						                'value'   => "pickup",
						                'compare' => '==',
						            ),
						        ),
						    );
						} else {
							$args = array(
						        'limit' => -1,
						        'pickup_date' => date("Y-m-d", strtotime($date)),
						        'delivery_type' => "pickup",
						        'status' => $filtered_status_type
						    );
						}
					    $orders_array = wc_get_orders( $args );
					    foreach ($orders_array as $order) {
					    	$orders[] = $order;
					    }
					}

		    		
		    	} else {

				    if($this->hpos) {
				    	$args = array(
					        'limit' => -1,
							'type' => array( 'shop_order' ),
							'status' => $filtered_status_type,
							'meta_query' => array(
					            array(
					                'key'     => 'delivery_date',
					                'value'   => date("Y-m-d", strtotime($date)),
					                'compare' => '==',
					            ),
					        ),
					    );
				    } else {
				    	$args = array(
					        'limit' => -1,
					        'delivery_date' => date("Y-m-d", strtotime($date)),
					        'status' => $filtered_status_type
					    );
				    }

				    $orders_array = wc_get_orders( $args );

				    foreach ($orders_array as $order) {
				    	$delivery_orders[] = $order;
				    }

				    if(!empty($filtered_pickup_location)) {

						foreach($filtered_pickup_location as $location) {

							if($this->hpos) {
								$args = array(
							        'limit' => -1,
									'type' => array( 'shop_order' ),
									'status' => $filtered_status_type,
									'meta_query' => array(
							            array(
							                'key'     => 'pickup_date',
							                'value'   => date("Y-m-d", strtotime($date)),
							                'compare' => '==',
							            ),
							            array(
							                'key'     => 'pickup_location',
							                'value'   => $location,
							                'compare' => '==',
							            ),
							        ),
							    );
							} else {
								$args = array(
							        'limit' => -1,
							        'pickup_date' => date("Y-m-d", strtotime($date)),
							        'status' => $filtered_status_type,
							        'pickup_location' => $location
							    );
							}
						    $orders_array = wc_get_orders( $args );
						    foreach ($orders_array as $order) {
						    	$pickup_orders[] = $order;
						    }
						}
					} else {
						if($this->hpos) {
							$args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $filtered_status_type,
								'meta_query' => array(
						            array(
						                'key'     => 'pickup_date',
						                'value'   => date("Y-m-d", strtotime($date)),
						                'compare' => '==',
						            ),
						        ),
						    );
						} else {
							$args = array(
						        'limit' => -1,
						        'pickup_date' => date("Y-m-d", strtotime($date)),
						        'status' => $filtered_status_type
						    );
						}
						
					    $orders_array = wc_get_orders( $args );
					    foreach ($orders_array as $order) {
					    	$pickup_orders[] = $order;
					    }
					}

				    $orders = array_merge($delivery_orders, $pickup_orders);
		    	}
			    
		    }

			foreach($orders as $order) {

				if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
			        $order_id = $order->get_id();
			    } else {
			        $order_id = $order->id;
			    }
		    	
		    	$temp_orders = [];
		    	
		    	$date = "";
		    	$time_start="";
		    	$time_end="";

		    	include_once(ABSPATH.'wp-admin/includes/plugin.php'); 

		    	if((metadata_exists('post', $order_id, '_wcj_order_number') && get_post_meta($order_id, '_wcj_order_number', true) !="") || ($order->meta_exists('_wcj_order_number') && $order->get_meta( '_wcj_order_number', true ) != "")) {
					if($this->hpos) {
						$order_id_with_custom = '#'.$order->get_meta( '_wcj_order_number', true );
					} else {
						$order_id_with_custom = '#'.get_post_meta($order_id, '_wcj_order_number', true);
					}
				} elseif(is_plugin_active('wt-woocommerce-sequential-order-numbers-pro/wt-advanced-order-number-pro.php') || is_plugin_active('wt-woocommerce-sequential-order-numbers/wt-advanced-order-number.php') || is_plugin_active('custom-order-numbers-for-woocommerce/custom-order-numbers-for-woocommerce.php') || is_plugin_active('yith-woocommerce-sequential-order-number-premium/init.php')) {
					$order_id_with_custom = '#'.$order->get_order_number();
				} else {
					$order_id_with_custom = '#'.$order->get_id();
				}
		    	$temp_orders ['title'] = 'Order '.$order_id_with_custom; 
		    	
		    	if($this->hpos) {
			    	$temp_orders ['url'] = get_site_url().'/wp-admin/admin.php?page=wc-orders&action=edit&id='.$order_id;
			    } else {
			    	$temp_orders ['url'] = get_site_url().'/wp-admin/post.php?post='.$order_id.'&action=edit';
			    }

		    	if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {
		    		if($this->hpos) {
						$date = $order->get_meta( 'delivery_date', true );
					} else {
						$date = get_post_meta( $order_id, 'delivery_date', true );
					}
			    	
			    }

			    if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {

			    	if($this->hpos) {
						$date = $order->get_meta( 'pickup_date', true );
					} else {
						$date = get_post_meta( $order_id, 'pickup_date', true );
					} 

			    }

			    if((metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id,"delivery_time",true) != "") || ($order->meta_exists('delivery_time') && $order->get_meta( 'delivery_time', true ) != "")) {

			    	if(get_post_meta($order_id,"delivery_time",true) != "as-soon-as-possible" && $order->get_meta( 'delivery_time', true ) != "as-soon-as-possible") {
				    	if($this->hpos) {
							$minutes = $order->get_meta( 'delivery_time', true );
						} else {
							$minutes = get_post_meta($order_id,"delivery_time",true);
						}

				    	$minutes = explode(' - ', $minutes);

			    		if(!isset($minutes[1])) {
			    			$time_start = "T".date("H:i", strtotime($minutes[0])).':00';
			    		} else {

			    			$time_start = "T".date("H:i", strtotime($minutes[0])).':00';
			    			$time_end = "T".date("H:i", strtotime($minutes[1])).':00'; 			
			    		}
		    		} else {
		    			$temp_orders ['title'] = 'Order #'.$order->get_id()." (As Soon As Possible)"; 
		    		}
			    	
			    }

			    if((metadata_exists('post', $order_id, 'pickup_time') && get_post_meta($order_id,"pickup_time",true) != "") || ($order->meta_exists('pickup_time') && $order->get_meta( 'pickup_time', true ) != "")) {
			    	if($this->hpos) {
						$pickup_minutes = $order->get_meta( 'pickup_time', true );
					} else {
						$pickup_minutes = get_post_meta($order_id,"pickup_time",true);
					}

					$pickup_minutes = explode(' - ', $pickup_minutes);

			    	if(!isset($pickup_minutes[1])) {
		    			$time_start = "T".date("H:i", strtotime($pickup_minutes[0])).':00';
		    		} else {

		    			$time_start = "T".date("H:i", strtotime($pickup_minutes[0])).':00';
		    			$time_end = "T".date("H:i", strtotime($pickup_minutes[1])).':00'; 			
		    		}
			    	
			    }

			    if(isset($time_start)) {
			    	$temp_orders['start'] = $date.$time_start;
			    } else {
			    	$temp_orders['start'] = $date;
			    }

			    if(isset($time_end)) {
			    	$temp_orders['end'] = $date.$time_end;
			    } else {
			    	$temp_orders['end'] = $date;
			    }

			    $response_orders [] = $temp_orders;

		    }


			$final_response = $response_orders;
		} 


	    $response=[
			"orders" => $final_response,
			"timezone" => $timezone,
			"time_format" => $time_format,
		];

		wp_send_json_success($response);
	}


	public function coderockz_woo_delivery_plugin_settings_export() {

       global $wpdb;

       $table = 'options';// table name
       $file = 'woo_delivery_plugin_settings'; // csv file name
       $csv_output = "";
       $results = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->prefix$table WHERE (option_name = 'coderockz_woo_delivery_option_delivery_settings' OR option_name = 'coderockz_woo_delivery_date_settings' OR option_name = 'coderockz_woo_delivery_pickup_date_settings' OR option_name = 'coderockz_woo_delivery_off_days_settings' OR option_name = 'coderockz_woo_delivery_time_settings' OR option_name = 'coderockz_woo_delivery_time_slot_settings' OR option_name = 'coderockz_woo_delivery_pickup_time_settings' OR option_name = 'coderockz_woo_delivery_pickup_slot_settings' OR option_name = 'coderockz_woo_delivery_pickup_location_settings' OR option_name = 'coderockz_woo_delivery_processing_days_settings' OR option_name = 'coderockz_woo_delivery_processing_time_settings' OR option_name = 'coderockz_woo_delivery_additional_field_settings' OR option_name = 'coderockz_woo_delivery_fee_settings' OR option_name = 'coderockz_woo_delivery_notify_email_settings' OR option_name = 'coderockz_woo_delivery_localization_settings' OR option_name = 'coderockz_woo_delivery_exclude_settings' OR option_name = 'coderockz_woo_delivery_other_settings' OR option_name = 'coderockz_woo_delivery_large_product_list' OR option_name = 'coderockz_woo_delivery_open_days_settings' OR option_name = 'coderockz_woo_delivery_google_calendar_settings' OR option_name = 'coderockz_woo_delivery_delivery_tips_settings' OR option_name = 'coderockz_woo_delivery_laundry_store_settings')",ARRAY_A );

      if(count($results) > 0){
          foreach($results as $result){
          	if(isset($result['option_value']) && $result['option_value'] != '') {
          		$result['option_value'] = str_replace(',','c-w-d', $result['option_value']);
          		$result['option_value'] = str_replace(PHP_EOL,'d-e-l', $result['option_value']);
          		$result = array_values($result);
          		$result = implode(", ", $result);
          		$csv_output .= $result."\n";
          	}
          
        }
      }


      $filename = $file."_".date("Y-m-d_H-i",time());
      header("Content-type: application/vnd.ms-excel");
      header("Content-disposition: csv" . date("Y-m-d") . ".csv");
      header( "Content-disposition: filename=".$filename.".csv");
      print $csv_output;
      exit;

    }

    public function coderockz_woo_delivery_process_reset_plugin_settings() {
    	global $wpdb;
		$plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE (option_name = 'coderockz_woo_delivery_option_delivery_settings' OR option_name = 'coderockz_woo_delivery_date_settings' OR option_name = 'coderockz_woo_delivery_pickup_date_settings' OR option_name = 'coderockz_woo_delivery_off_days_settings' OR option_name = 'coderockz_woo_delivery_time_settings' OR option_name = 'coderockz_woo_delivery_time_slot_settings' OR option_name = 'coderockz_woo_delivery_pickup_time_settings' OR option_name = 'coderockz_woo_delivery_pickup_slot_settings' OR option_name = 'coderockz_woo_delivery_pickup_location_settings' OR option_name = 'coderockz_woo_delivery_processing_days_settings' OR option_name = 'coderockz_woo_delivery_processing_time_settings' OR option_name = 'coderockz_woo_delivery_additional_field_settings' OR option_name = 'coderockz_woo_delivery_fee_settings' OR option_name = 'coderockz_woo_delivery_notify_email_settings' OR option_name = 'coderockz_woo_delivery_localization_settings' OR option_name = 'coderockz_woo_delivery_exclude_settings' OR option_name = 'coderockz_woo_delivery_other_settings' OR option_name = 'coderockz_woo_delivery_open_days_settings' OR option_name = 'coderockz_woo_delivery_google_calendar_settings' OR option_name = 'coderockz_woo_delivery_delivery_tips_settings' OR option_name = 'coderockz_woo_delivery_laundry_store_settings')" );
		foreach( $plugin_options as $option ) {
		    delete_option( $option->option_name );
		}

		wp_send_json_success();
    }

    public function coderockz_woo_delivery_make_delivery_completed_with_order_completed( $order_id, $old_status, $new_status ) {

    	$other_settings = get_option('coderockz_woo_delivery_other_settings');
    	$remove_delivery_status_column = (isset($other_settings['remove_delivery_status_column']) && !empty($other_settings['remove_delivery_status_column'])) ? $other_settings['remove_delivery_status_column'] : false;

    	$mark_delivery_completed_with_order_completed = (isset($other_settings['mark_delivery_completed_with_order_completed']) && !empty($other_settings['mark_delivery_completed_with_order_completed'])) ? $other_settings['mark_delivery_completed_with_order_completed'] : false;

    	if( $new_status == "completed" && $mark_delivery_completed_with_order_completed && !$remove_delivery_status_column) {
	        
	        if($this->hpos) {
		        $order = wc_get_order( $order_id );
				$order->update_meta_data( 'delivery_status', 'delivered' );
				$order->save();
			} else {
				update_post_meta($order_id, 'delivery_status', 'delivered');
			}
	    }

	    $order = wc_get_order( $order_id );

	    $timezone = $this->helper->get_the_timezone();
	    
	    $delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
	    $delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');           
	    $pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');          
	    $delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
	    $pickup_time_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
	    $delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
	    $additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');

	    $enable_delivery_option = (isset($delivery_option_settings['enable_option_time_pickup']) && !empty($delivery_option_settings['enable_option_time_pickup'])) ? $delivery_option_settings['enable_option_time_pickup'] : false;
	    $enable_delivery_date = (isset($delivery_date_settings['enable_delivery_date']) && !empty($delivery_date_settings['enable_delivery_date'])) ? $delivery_date_settings['enable_delivery_date'] : false;
	    $enable_pickup_date = (isset($pickup_date_settings['enable_pickup_date']) && !empty($pickup_date_settings['enable_pickup_date'])) ? $pickup_date_settings['enable_pickup_date'] : false;
	    $enable_delivery_time = (isset($delivery_time_settings['enable_delivery_time']) && !empty($delivery_time_settings['enable_delivery_time'])) ? $delivery_time_settings['enable_delivery_time'] : false;
	    $enable_pickup_time = (isset($pickup_time_settings['enable_pickup_time']) && !empty($pickup_time_settings['enable_pickup_time'])) ? $pickup_time_settings['enable_pickup_time'] : false;

	    $delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : "Delivery Date";
	    $pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : "Pickup Date";
	    $delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : "Delivery Time";
	    $pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : "Pickup Time";
	    $pickup_location_field_label = (isset($pickup_location_settings['field_label']) && !empty($pickup_location_settings['field_label'])) ? stripslashes($pickup_location_settings['field_label']) : "Pickup Location";
	    $additional_field_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? stripslashes($additional_field_settings['field_label']) : "Special Note About Delivery";
	    
	    $delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";
	    
	    $add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

	    if($add_weekday_name) {
	        $delivery_date_format = "l ".$delivery_date_format;
	    }

	    $pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";

	    $pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

	    if($pickup_add_weekday_name) {
	        $pickup_date_format = "l ".$pickup_date_format;
	    }
	    
	    $time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
	    if($time_format == 12) {
	        $time_format = "h:i A";
	    } elseif ($time_format == 24) {
	        $time_format = "H:i";
	    }

	    $pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
	    if($pickup_time_format == 12) {
	        $pickup_time_format = "h:i A";
	    } elseif ($pickup_time_format == 24) {
	        $pickup_time_format = "H:i";
	    }


	    $order_type_field_label = (isset($delivery_option_settings['delivery_option_label']) && !empty($delivery_option_settings['delivery_option_label'])) ? stripslashes($delivery_option_settings['delivery_option_label']) : "Order Type";
	    $delivery_field_label = (isset($delivery_option_settings['delivery_label']) && !empty($delivery_option_settings['delivery_label'])) ? stripslashes($delivery_option_settings['delivery_label']) : "Delivery";
	    $pickup_field_label = (isset($delivery_option_settings['pickup_label']) && !empty($delivery_option_settings['pickup_label'])) ? stripslashes($delivery_option_settings['pickup_label']) : "Pickup";

	    if((metadata_exists('post', $order_id, 'delivery_type') && get_post_meta($order_id,"delivery_type",true) != "") || ($order->meta_exists('delivery_type') && $order->get_meta( 'delivery_type', true ) != "")) {
	            
	        if(get_post_meta($order_id, 'delivery_type', true) == "delivery" || $order->get_meta( 'delivery_type', true ) == "delivery") {

	             $delivery_type = 'delivery';

	        } elseif(get_post_meta($order_id, 'delivery_type', true) == "pickup" || $order->get_meta( 'delivery_type', true ) == "pickup") {
	            
	            $delivery_type = 'pickup';

	        } else {
    			
    			$delivery_type = "";
    		}

	    }

	    if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {
	        if($this->hpos) {
				$en_delivery_date = $order->get_meta( 'delivery_date', true );
			} else {
				$en_delivery_date = get_post_meta($order_id, 'delivery_date', true);
			}
	        
	    }

	    if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {
	        if($this->hpos) {
				$en_pickup_date = $order->get_meta( 'pickup_date', true );
			} else {
				$en_pickup_date = get_post_meta($order_id, 'pickup_date', true);
			}
	    }

	    if((metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id,"delivery_time",true) != "") || ($order->meta_exists('delivery_time') && $order->get_meta( 'delivery_time', true ) != "")) {
	            
	        if($this->hpos) {
				$delivery_time = $order->get_meta( 'delivery_time', true );
			} else {
				$delivery_time = get_post_meta($order_id, 'delivery_time', true);
			}
	    }

	    if((metadata_exists('post', $order_id, 'pickup_time') && get_post_meta($order_id,"pickup_time",true) != "") || ($order->meta_exists('pickup_time') && $order->get_meta( 'pickup_time', true ) != "")) {
	            
	        if($this->hpos) {
				$pickup_time = $order->get_meta( 'pickup_time', true );
			} else {
				$pickup_time = get_post_meta($order_id, 'pickup_time', true);
			}
	    }

	    if((metadata_exists('post', $order_id, 'pickup_location') && get_post_meta($order_id, 'pickup_location', true) !="") || ($order->meta_exists('pickup_location') && $order->get_meta( 'pickup_location', true ) != "")) {
	        if($this->hpos) {
				$pickup_location = stripslashes(html_entity_decode($order->get_meta( 'pickup_location', true ), ENT_QUOTES));
			} else {
				$pickup_location = stripslashes(html_entity_decode(get_post_meta($order_id, 'pickup_location', true), ENT_QUOTES));
			}  
	    }

	    if((metadata_exists('post', $order_id, 'additional_note') && get_post_meta($order_id, 'additional_note', true) !="") || ($order->meta_exists('additional_note') && $order->get_meta( 'additional_note', true )!= "")) {
	        if($this->hpos) {
				$additional_field = $order->get_meta( 'additional_note', true );
			} else {
				$additional_field = get_post_meta($order_id, 'additional_note', true);
			}

			$additional_field = stripslashes(html_entity_decode($additional_field, ENT_QUOTES));
	    }
	    
	    $google_calendar_settings = get_option('coderockz_woo_delivery_google_calendar_settings');
	    
	    $enable_calendar_sync_client = isset($google_calendar_settings['google_calendar_sync']) && !empty($google_calendar_settings['google_calendar_sync']) ? $google_calendar_settings['google_calendar_sync'] : false;
	    
	    $calendar_sync_customer_client_id = isset($google_calendar_settings['google_calendar_client_id']) && !empty($google_calendar_settings['google_calendar_client_id']) ? $google_calendar_settings['google_calendar_client_id'] : "";
	    
	    $calendar_sync_customer_client_secret = isset($google_calendar_settings['google_calendar_client_secret']) && !empty($google_calendar_settings['google_calendar_client_secret']) ? $google_calendar_settings['google_calendar_client_secret'] : "";

	    
	    $order_status_keys = array_keys(wc_get_order_statuses());
	    $order_status = ['partially-paid'];
	    foreach($order_status_keys as $order_status_key) {
	        $order_status[] = substr($order_status_key,3);
	    }
	    $order_status = array_diff($order_status,['cancelled','failed','refunded','pending']);
	    $order_status_sync = isset($google_calendar_settings['order_status_sync']) && !empty($google_calendar_settings['order_status_sync']) ? $google_calendar_settings['order_status_sync'] : $order_status;
	    
	    $calendar_sync_customer_redirect_url = get_site_url().'/wp-admin/';
	    
	    
	    if(get_option('coderockz_woo_delivery_google_calendar_access_token') && $enable_calendar_sync_client && $google_calendar_settings['google_calendar_client_id'] != "" && $google_calendar_settings['google_calendar_client_secret'] != "") {
	        
	        $client = new Google_Client();
	        $client->setClientId($calendar_sync_customer_client_id);
	        $client->setClientSecret($calendar_sync_customer_client_secret);
	        $client->setRedirectUri($calendar_sync_customer_redirect_url);
	        $client->addScope("https://www.googleapis.com/auth/calendar.events");
	        $client->setAccessType('offline');
	        
	        $client->setAccessToken(get_option('coderockz_woo_delivery_google_calendar_access_token'));
	                    
	        if($client->isAccessTokenExpired()) {
	            $client->fetchAccessTokenWithRefreshToken(get_option('coderockz_woo_delivery_google_calendar_access_token')['refresh_token']);
	            $access_token = $client->getAccessToken();
	            update_option('coderockz_woo_delivery_google_calendar_access_token',$access_token);
	            
	        }
	        	        
	        if (isset($delivery_type) && !empty($delivery_type)) {
	            if($delivery_type == "delivery") {

	                 $delivery_type = $delivery_field_label;

	            } elseif($delivery_type == "pickup") {
	                
	                $delivery_type = $pickup_field_label;
	            }
	        } elseif(!$enable_delivery_option && (($enable_delivery_time && !$enable_pickup_time) || ($enable_delivery_date && !$enable_pickup_date))) {
	            $delivery_type = $delivery_field_label;
	        } elseif(!$enable_delivery_option && ((!$enable_delivery_time && $enable_pickup_time) || (!$enable_delivery_date && $enable_pickup_date))) {
	            $delivery_type = $pickup_field_label;
	        } else {
	            $delivery_type = "";
	        }
	        
	        include_once(ABSPATH.'wp-admin/includes/plugin.php');
	        
	        if((metadata_exists('post', $order_id, '_wcj_order_number') && get_post_meta($order_id, '_wcj_order_number', true) !="") || ($order->meta_exists('_wcj_order_number') && $order->get_meta( '_wcj_order_number', true ) != "")) {
				if($this->hpos) {
					$order_id_with_custom = '#'.$order->get_meta( '_wcj_order_number', true );
				} else {
					$order_id_with_custom = '#'.get_post_meta($order_id, '_wcj_order_number', true);
				}
			} elseif(is_plugin_active('wt-woocommerce-sequential-order-numbers-pro/wt-advanced-order-number-pro.php') || is_plugin_active('wt-woocommerce-sequential-order-numbers/wt-advanced-order-number.php') || is_plugin_active('custom-order-numbers-for-woocommerce/custom-order-numbers-for-woocommerce.php') || is_plugin_active('yith-woocommerce-sequential-order-number-premium/init.php')) {
	            $order_id_with_custom = '#'.$order->get_order_number();
	        } else {
	            $order_id_with_custom = '#'.$order_id;
	        }
	        
	        if(isset($delivery_time) && !empty($delivery_time) && $delivery_time == "as-soon-as-possible") {
	            
	            $as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : __("As Soon As Possible", 'coderockz-woo-delivery');
	            $summary = $delivery_type.$order_id_with_custom."(".$as_soon_as_possible_text.")". " - " . $order->get_billing_first_name() ." ".$order->get_billing_last_name();
	            
	        } else {
	            $summary = $delivery_type.$order_id_with_custom. " - " . $order->get_billing_first_name() ." ".$order->get_billing_last_name();
	        }
	        
	        if(isset($en_delivery_date) && !empty($en_delivery_date)) {
	            $date = date('Y-m-d', strtotime($en_delivery_date));
	        }

	        if(isset($en_pickup_date) && !empty($en_pickup_date)) {
	            $date = date('Y-m-d', strtotime($en_pickup_date)); 
	        }

	        if(isset($delivery_time) && !empty($delivery_time)) {

	            if($delivery_time != "as-soon-as-possible") {
	                $minutes = sanitize_text_field($delivery_time);
	                $minutes = explode(' - ', $minutes);

	                if(!isset($minutes[1])) {
	                    $time_start = "T".$minutes[0].':00';
	                    $time_end = "T".$minutes[0].':00';
	                } else {

	                    $time_start = "T".$minutes[0].':00';
	                    $time_end = "T".$minutes[1].':00';          
	                }
	            }
	            
	        }

	        if(isset($pickup_time) && !empty($pickup_time)) {
	            $pickup_minutes = sanitize_text_field($pickup_time);
	            $pickup_minutes = explode(' - ', $pickup_minutes);

	            if(!isset($pickup_minutes[1])) {
	                $time_start = "T".$pickup_minutes[0].':00';
	                $time_end = "T".$pickup_minutes[0].':00';
	            } else {

	                $time_start = "T".$pickup_minutes[0].':00';
	                $time_end = "T".$pickup_minutes[1].':00';           
	            }
	            
	        }
	        
	        if(isset($pickup_location) && !empty($pickup_location)) {
	            $location = sanitize_text_field($pickup_location);
	        } else {
	            $location = "";
	        }
	        
	        
	        $start = "";
	        $end = "";
	        if(isset($date)) {
	            $start .= $date;
	            $end .= $date;
	        } else {
	            $order_created_obj= current_datetime($order->get_date_created());
	            $start .= $order_created_obj->format("Y-m-d");
	            $end .= $order_created_obj->format("Y-m-d");
	        }
	        
	        if(isset($time_start)) {
	            $start .= $time_start;
	            $dateCriteria = 'dateTime';
	        } else {
	           $dateCriteria = 'date'; 
	        }
	        
	        if(isset($time_end)) {
	            $end .= $time_end;
	            $dateCriteria = 'dateTime';
	        } else {
	           $dateCriteria = 'date'; 
	        }
	                        
	        $delivery_details = "<b>"."Delivery Details:"."</b><br/>";

	        if(isset($en_pickup_date) && !empty($en_pickup_date)) {

	            if($this->hpos) {
					$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime($order->get_meta( 'pickup_date', true ))),"pickup"),"pickup");
				} else {
					$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime(get_post_meta( $order_id, 'pickup_date', true ))),"pickup"),"pickup");
				}

	            $delivery_details .= $pickup_date_field_label.': ' . sanitize_text_field($pickup_date) . "<br/>"; 

	        }

	        if(isset($pickup_time) && !empty($pickup_time)) {
	            $pickup_minutes = sanitize_text_field($pickup_time);
	            $pickup_minutes = explode(' - ', $pickup_minutes);

	            if(!isset($pickup_minutes[1])) {
	                $delivery_details .= $pickup_time_field_label.': ' . date($pickup_time_format, strtotime($pickup_minutes[0])) . "<br/>";
	            } else {

	                $delivery_details .= $pickup_time_field_label.': ' . date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1])) . "<br/>";             
	            }
	            
	        }

	        if(isset($pickup_location) && !empty($pickup_location)) {
	            $delivery_details .= $pickup_location_field_label.': ' . sanitize_text_field($pickup_location) . "<br/>";
	        }
	        
	        if(isset($en_delivery_date) && !empty($en_delivery_date)) {

	            if($this->hpos) {
					$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime($order->get_meta( 'delivery_date', true ))),"delivery"),"delivery");
				} else {
					$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime(get_post_meta( $order_id, 'delivery_date', true ))),"delivery"),"delivery");
				}

	            $delivery_details .= $delivery_date_field_label.': ' . sanitize_text_field($delivery_date) . "<br/>";

	        }

	        if(isset($delivery_time) && !empty($delivery_time)) {

	            if($delivery_time !="as-soon-as-possible") {
	                $minutes = sanitize_text_field($delivery_time);
	                $minutes = explode(' - ', $minutes);

	                if(!isset($minutes[1])) {
	                    $delivery_details .= $delivery_time_field_label.': ' . date($time_format, strtotime($minutes[0])) . "<br/>";
	                } else {

	                    $delivery_details .= $delivery_time_field_label.': ' . date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1])) . "<br/>";           
	                }
	            } else {
	                $as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : "As Soon As Possible";
	                $delivery_details .= $delivery_time_field_label.': ' . $as_soon_as_possible_text . "<br/>";
	            }
	            
	        }

	        if(isset($additional_field) && !empty($additional_field)) {
	            $delivery_details .= $additional_field_label.': ' . $additional_field;
	        }
	        
	        $i=1;
	        $other_settings = get_option('coderockz_woo_delivery_other_settings');
			$hide_metadata_reports_calendar = (isset($other_settings['hide_metadata_reports_calendar']) && !empty($other_settings['hide_metadata_reports_calendar'])) ? $other_settings['hide_metadata_reports_calendar'] : false;
	        $product_details = "<br/><b>"."Products:"."</b><br/>";
	        foreach ($order->get_items() as $item_id => $item) {

	            if($item->get_variation_id() == 0) {
	                $product_quantity = $item->get_quantity();
	                $product_name = $item->get_name();
					$item_meta_data = $item->get_formatted_meta_data();
					if(!empty($item_meta_data) && !$hide_metadata_reports_calendar) {
						foreach ( $item_meta_data as $meta_id => $meta ) {
							$product_name .= ', '.wp_kses_post( strip_tags($meta->value) );
						}
					}
	           } else {
	                $variation = new WC_Product_Variation($item->get_variation_id());
					$item_meta_data = $item->get_formatted_meta_data();
					$product_quantity = $item->get_quantity();
					$product_name = $variation->get_title();
					if(array_filter($variation->get_variation_attributes())) {
						$product_name .= " - ".strip_tags(implode(", ", array_filter($variation->get_variation_attributes(), 'strlen')));	
					}
					if(!empty($item_meta_data) && !$hide_metadata_reports_calendar) {
						foreach ( $item_meta_data as $meta_id => $meta ) {
							if (!array_key_exists("attribute_".$meta->key,$variation->get_variation_attributes()) || (array_key_exists("attribute_".$meta->key,$variation->get_variation_attributes()) && $variation->get_variation_attributes()["attribute_".$meta->key] == "") )
								$product_name .= ', '.wp_kses_post( strip_tags($meta->display_value) );

						}
					}
	            }

	            $product_details .= $i.'. ';
	            $product_details .= $product_name;
	            $product_details .= '   '.$this->helper->format_price($order->get_item_total( $item,true ),$order_id).'x';
	            $product_details .= $product_quantity.'=';
	            $product_details .= $this->helper->format_price($item->get_total() + $item->get_subtotal_tax(),$order_id);
	            $product_details .= "<br/>";
	            $i = $i+1;
	        }
	        
	        $total = "<br/><b>"."Total: "."</b>".$order->get_currency() . $order->get_total()."<br/>";
	        
	        $order_billing_address = "<br/><b>"."Billing Address:"."</b><br/>".$order->get_formatted_billing_address();
	        $order_billing_address .= "<br/>".'Mobile: '.$order->get_billing_phone();
	        $order_billing_address .= "<br/>".'Email: '.$order->get_billing_email();
	        $order_billing_address .="<br/>";
	        $order_shipping_address = "<br/><b>"."Shipping Address:"."</b><br/>".$order->get_formatted_shipping_address()."<br/>";
	             
	        $sync_custom_field_name = isset($google_calendar_settings['sync_custom_field_name']) && !empty($google_calendar_settings['sync_custom_field_name']) ? $google_calendar_settings['sync_custom_field_name'] : [];

	        $custom_field = "";
	        if(!empty($sync_custom_field_name)) {
		        
		        foreach($sync_custom_field_name as $custom_field_name) {

		        	if((metadata_exists('post', $order_id, $custom_field_name) && get_post_meta($order_id, $custom_field_name, true) !="") || ($order->meta_exists($custom_field_name) && $order->get_meta( $custom_field_name, true )!= "")) {

			        	if($this->hpos) {
							$custom_field .= "<br/><b>".ucwords(str_replace('_', ' ', $custom_field_name)).':</b> '.$order->get_meta( $custom_field_name, true );
						} else {
							$custom_field .= "<br/><b>".ucwords(str_replace('_', ' ', $custom_field_name)).':</b> '.get_post_meta($order_id, $custom_field_name, true);
						}

				    }

				}
			}

	        $payment_method = "<br/><b>"."Payment Method: "."</b>".$order->get_payment_method_title()."<br/>";
	        $order_status = "<br/><b>"."Order Status: "."</b>".$order->get_status()."<br/>";
	        
	        $customer_note = "<br/><b>"."Customer Note: "."</b>".$order->get_customer_note();

	        if($this->hpos) {
		    	$edit_order = "<b><a href='".get_site_url()."/wp-admin/admin.php?page=wc-orders&action=edit&id=".$order_id."' target='_blank'>".get_site_url()."/wp-admin/admin.php?page=wc-orders&action=edit&id=".$order_id."</a></b><br/><br/>";
		    } else {
		    	$edit_order = "<b><a href='".get_site_url()."/wp-admin/post.php?post=".$order_id."&action=edit' target='_blank'>".get_site_url()."/wp-admin/post.php?post=".$order_id."&action=edit</a></b><br/><br/>";
		    }
	        
	        $description = $edit_order.$delivery_details.$product_details.$total.$order_billing_address.$order_shipping_address.$custom_field.$payment_method.$order_status.$customer_note;
	        
	        $service = new Google_Service_Calendar($client);
	        
	        $event = new Google_Service_Calendar_Event(array(
	          'id' => 'order'.$order_id,
	          'summary' => $summary,
	          'location' => $location,
	          'description' => $description,
	          'start' => array(
	            $dateCriteria => $start,
	            'timeZone' => $timezone,
	          ),
	          'end' => array(
	            $dateCriteria => $end,
	            'timeZone' => $timezone,
	          ),
	          'reminders' => array(
	            'useDefault' => true,
	          ),
	        ));
	        
	        $calendarId = isset($google_calendar_settings['google_calendar_id']) && !empty($google_calendar_settings['google_calendar_id']) ? $google_calendar_settings['google_calendar_id'] : 'primary';

	        if(in_array($new_status, $order_status_sync)) {
	        	$exists = false;
	        	try {
		            if($service->events->get($calendarId, 'order'.$order_id)){
		                $exists = true;
		                $service->events->update($calendarId, 'order'.$order_id, $event);
		            }
		        } catch (Exception $e) {}

		        if (!$exists) {
		            try {
		                $service->events->insert($calendarId, $event);
		            } catch (Exception $e) {}
		                
		        }
	    	
	    	} elseif(!in_array($new_status, $order_status_sync)) {

	    		try {
		            if($service->events->get($calendarId, 'order'.$order_id)){
		                $service->events->delete($calendarId, 'order'.$order_id);
		            }
		        } catch (Exception $e) {}

	    	}

	    }
    }

    public function coderockz_woo_delivery_make_google_unauthenticate() {
        check_ajax_referer('coderockz_woo_delivery_nonce');
        delete_option( 'coderockz_woo_delivery_google_calendar_access_token' );
        wp_send_json_success();
    }


    public function coderockz_woo_delivery_main_layout() {
        include_once CODEROCKZ_WOO_DELIVERY_DIR . '/admin/partials/coderockz-woo-delivery-admin-display.php';
    }

    public function coderockz_woo_delivery_delivery_calendar() {
        include_once CODEROCKZ_WOO_DELIVERY_DIR . '/admin/partials/coderockz-woo-delivery-delivery-calendar-display.php';
    }

    public function coderockz_woo_delivery_wpo_wcpdf_delivery_info ($template_type, $order) {

	    if ($template_type == 'packing-slip') {

	    	if ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) {
				$order_id = $order->get_id();
			} else {
				$order_id = $order->id;
			}
	        
	    	$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');			
			$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');			
			$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
			$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
			$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
			$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');

			if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {

				$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : __( "Pickup Date", 'coderockz-woo-delivery' );

				$pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";

				$pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

				if($pickup_add_weekday_name) {
					$pickup_date_format = "l ".$pickup_date_format;
				}
				if($this->hpos) {
					$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime($order->get_meta( 'pickup_date', true ))),"pickup"),"pickup");
				} else {
					$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime(get_post_meta( $order_id, 'pickup_date', true ))),"pickup"),"pickup");
				}
				?>
		        <tr class="pickup-date">
		            <th><?php echo $pickup_date_field_label; ?>: </th>
		            <td><?php echo $pickup_date; ?></td>
		        </tr>
		    	
		        <?php
			}

			if((metadata_exists('post', $order_id, 'pickup_time') && get_post_meta($order_id,"pickup_time",true) != "") || ($order->meta_exists('pickup_time') && $order->get_meta( 'pickup_time', true ) != "")) {
				$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : __( "Pickup Time", 'coderockz-woo-delivery' );
				$pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
				if($pickup_time_format == 12) {
					$pickup_time_format = "h:i A";
				} elseif ($pickup_time_format == 24) {
					$pickup_time_format = "H:i";
				}
				if($this->hpos) {
					$pickup_minutes = $order->get_meta( 'pickup_time', true );
				} else {
					$pickup_minutes = get_post_meta($order_id,"pickup_time",true);
				}
				$pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
				if($pickup_time_format == 12) {
					$pickup_time_format = "h:i A";
				} elseif ($pickup_time_format == 24) {
					$pickup_time_format = "H:i";
				}
				$pickup_minutes = explode(' - ', $pickup_minutes);
	    		if(!isset($pickup_minutes[1])) {
	    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0]));
	    		} else {
	    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1]));
	    		}

				?>
		        <tr class="pickup-time">
		            <th><?php echo $pickup_time_field_label; ?>: </th>
		            <td><?php echo $pickup_time_value; ?></td>
		        </tr>
		    	
		        <?php

			}

			if((metadata_exists('post', $order_id, 'pickup_location') && get_post_meta($order_id, 'pickup_location', true) !="") || ($order->meta_exists('pickup_location') && $order->get_meta( 'pickup_location', true ) != "")) {
				$pickup_location_field_label = (isset($delivery_pickup_settings['field_label']) && !empty($delivery_pickup_settings['field_label'])) ? stripslashes($delivery_pickup_settings['field_label']) : __( "Pickup Location", 'coderockz-woo-delivery' );
				if($this->hpos) {
					$pickup_location = stripslashes(html_entity_decode($order->get_meta( 'pickup_location', true ), ENT_QUOTES));
				} else {
					$pickup_location = stripslashes(html_entity_decode(get_post_meta($order_id, 'pickup_location', true), ENT_QUOTES));
				} 
				?>
		        <tr class="pickup-location">
		            <th><?php echo $pickup_location_field_label; ?>: </th>
		            <td><?php echo $pickup_location; ?></td>
		        </tr>
		    	
		        <?php
			}

			if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {

				$delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : __( "Delivery Date", 'coderockz-woo-delivery' );
				$delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";
				$add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

				if($add_weekday_name) {
					$delivery_date_format = "l ".$delivery_date_format;
				}

				if($this->hpos) {
					$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime($order->get_meta( 'delivery_date', true ))),"delivery"),"delivery");
				} else {
					$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime(get_post_meta( $order_id, 'delivery_date', true ))),"delivery"),"delivery");
				}

				?>
		        <tr class="delivery-date">
		            <th><?php echo $delivery_date_field_label; ?>: </th>
		            <td><?php echo $delivery_date; ?></td>
		        </tr>
		    	
		        <?php
			}

			if((metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id,"delivery_time",true) != "") || ($order->meta_exists('delivery_time') && $order->get_meta( 'delivery_time', true ) != "")) {
				$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : __( "Delivery Time", 'coderockz-woo-delivery' );
				$time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
				if($time_format == 12) {
					$time_format = "h:i A";
				} elseif ($time_format == 24) {
					$time_format = "H:i";
				}

				if(get_post_meta($order_id,"delivery_time",true) == "as-soon-as-possible" || $order->get_meta( 'delivery_time', true ) == "as-soon-as-possible") {
					$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : "As Soon As Possible";
					?>
			        <tr class="delivery-time">
			            <th><?php echo $delivery_time_field_label; ?>: </th>
			            <td><?php echo $as_soon_as_possible_text; ?></td>
			        </tr>
			    	
			        <?php
				} else {

					if($this->hpos) {
						$minutes = $order->get_meta( 'delivery_time', true );
					} else {
						$minutes = get_post_meta($order_id,"delivery_time",true);
					}

					$minutes = explode(' - ', $minutes);
		    		if(!isset($minutes[1])) {
		    			$time_value = date($time_format, strtotime($minutes[0]));
		    		} else {
		    			$time_value = date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1]));
		    		}

					?>
			        <tr class="delivery-time">
			            <th><?php echo $delivery_time_field_label; ?>: </th>
			            <td><?php echo $time_value; ?></td>
			        </tr>
			    	
			        <?php
				}
			}

			if((metadata_exists('post', $order_id, 'additional_note') && get_post_meta($order_id, 'additional_note', true) !="") || ($order->meta_exists('additional_note') && $order->get_meta( 'additional_note', true )!= "")) {
				$additional_field_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? stripslashes($additional_field_settings['field_label']) : __( "Special Note About Delivery", 'coderockz-woo-delivery' );
				if($this->hpos) {
					$additional_note = $order->get_meta( 'additional_note', true );
				} else {
					$additional_note = get_post_meta($order_id, 'additional_note', true);
				}
				$additional_note = stripslashes(html_entity_decode($additional_note, ENT_QUOTES));
				?>
		        <tr class="additional-note">
		            <th><?php echo $additional_field_field_label; ?>: </th>
		            <td><?php echo $additional_note; ?></td>
		        </tr>
		    	
		        <?php
			}

	        
	    }
	}

	public function coderockz_woo_delivery_wcfm_orders_additional_info_column_label ($delivery_pickup_info_label) {
		if(class_exists('Coderockz_Woo_Delivery')) {
			$delivery_pickup_info_label = (isset(get_option('coderockz_woo_delivery_localization_settings')['delivery_details_text']) && !empty(get_option('coderockz_woo_delivery_localization_settings')['delivery_details_text'])) ? stripslashes(get_option('coderockz_woo_delivery_localization_settings')['delivery_details_text']) : __("Delivery Details","coderockz-woo-delivery");
		  	return $delivery_pickup_info_label;
	  	}
    }

    public function coderockz_woo_delivery_wcfm_orders_additonal_data_hidden () {
    	return false;
    }

    public function coderockz_woo_delivery_wcfm_orders_additonal_data ($delivery_pickup_info, $order_id) {
    	
    	if(class_exists('Coderockz_Woo_Delivery')) {
			$order = wc_get_order($order_id);
			$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');			
			$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');			
			$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
			$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
			$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
			$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');

			$delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : __( "Delivery Date", 'coderockz-woo-delivery' );
			$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : __( "Pickup Date", 'coderockz-woo-delivery' );
			$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : __( "Delivery Time", 'coderockz-woo-delivery' );
			$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : __( "Pickup Time", 'coderockz-woo-delivery' );
			$pickup_location_field_label = (isset($delivery_pickup_settings['field_label']) && !empty($delivery_pickup_settings['field_label'])) ? stripslashes($delivery_pickup_settings['field_label']) : __( "Pickup Location", 'coderockz-woo-delivery' );
			$additional_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? stripslashes($additional_field_settings['field_label']) : __( "Special Note About Delivery", 'coderockz-woo-delivery' );

			$delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";
			$add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

			if($add_weekday_name) {
				$delivery_date_format = "l ".$delivery_date_format;
			}

			$pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";

			$pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

			if($pickup_add_weekday_name) {
				$pickup_date_format = "l ".$pickup_date_format;
			}

			$time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
			if($time_format == 12) {
				$time_format = "h:i A";
			} elseif ($time_format == 24) {
				$time_format = "H:i";
			}
			
			$delivery_pickup_info = "";

			if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {
				if($this->hpos) {
					$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime($order->get_meta( 'pickup_date', true ))),"pickup"),"pickup");
				} else {
					$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime(get_post_meta( $order_id, 'pickup_date', true ))),"pickup"),"pickup");
				}
				$delivery_pickup_info .= $pickup_date_field_label.": " . $pickup_date;
				$delivery_pickup_info .= "<br>";
			}

			if((metadata_exists('post', $order_id, 'pickup_time') && get_post_meta($order_id,"pickup_time",true) != "") || ($order->meta_exists('pickup_time') && $order->get_meta( 'pickup_time', true ) != "")) {
				if($this->hpos) {
					$pickup_minutes = $order->get_meta( 'pickup_time', true );
				} else {
					$pickup_minutes = get_post_meta($order_id,"pickup_time",true);
				}
				$pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
				if($pickup_time_format == 12) {
					$pickup_time_format = "h:i A";
				} elseif ($pickup_time_format == 24) {
					$pickup_time_format = "H:i";
				}
				$pickup_minutes = explode(' - ', $pickup_minutes);
	    		if(!isset($pickup_minutes[1])) {
	    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0]));
	    		} else {
	    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1]));
	    		}

				$delivery_pickup_info .= $pickup_time_field_label.": " . $pickup_time_value;
				$delivery_pickup_info .= "<br>";

			}

			if((metadata_exists('post', $order_id, 'pickup_location') && get_post_meta($order_id, 'pickup_location', true) !="") || ($order->meta_exists('pickup_location') && $order->get_meta( 'pickup_location', true ) != "")) {
				if($this->hpos) {
					$pickup_location = $order->get_meta( 'pickup_location', true );
				} else {
					$pickup_location = get_post_meta($order_id,"pickup_location",true);
				}
				$delivery_pickup_info .= $pickup_location_field_label.": " . stripslashes(html_entity_decode($pickup_location, ENT_QUOTES));
				$delivery_pickup_info .= "<br>";
			}

			if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {

				if($this->hpos) {
					$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime($order->get_meta( 'delivery_date', true ))),"delivery"),"delivery");
				} else {
					$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime(get_post_meta( $order_id, 'delivery_date', true ))),"delivery"),"delivery");
				}

				$delivery_pickup_info .= $delivery_date_field_label.": " . $delivery_date;
				$delivery_pickup_info .= "<br>";
			}

			if((metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id,"delivery_time",true) != "") || ($order->meta_exists('delivery_time') && $order->get_meta( 'delivery_time', true ) != "")) {

				if(get_post_meta($order_id,"delivery_time",true) == "as-soon-as-possible" || $order->get_meta( 'delivery_time', true ) == "as-soon-as-possible") {
					$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : "As Soon As Possible";
					$delivery_pickup_info .= $delivery_time_field_label.": " . $as_soon_as_possible_text;
					$delivery_pickup_info .= "<br>";
				} else {
					if($this->hpos) {
						$minutes = $order->get_meta( 'delivery_time', true );
					} else {
						$minutes = get_post_meta($order_id,"delivery_time",true);
					}

					$minutes = explode(' - ', $minutes);
		    		if(!isset($minutes[1])) {
		    			$time_value = date($time_format, strtotime($minutes[0]));
		    		} else {
		    			$time_value = date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1]));
		    		}

					$delivery_pickup_info .= $delivery_time_field_label.": " . $time_value;
					$delivery_pickup_info .= "<br>";
				}
			}

			if((metadata_exists('post', $order_id, 'additional_note') && get_post_meta($order_id, 'additional_note', true) !="") || ($order->meta_exists('additional_note') && $order->get_meta( 'additional_note', true )!= "")) {
				if($this->hpos) {
					$additional_note = $order->get_meta( 'additional_note', true );
				} else {
					$additional_note = get_post_meta($order_id, 'additional_note', true);
				}
				$delivery_pickup_info .= $additional_field_label.": " . stripslashes(html_entity_decode($additional_note, ENT_QUOTES));
			}

			return $delivery_pickup_info;
		}

    }

    public function coderockz_woo_delivery_wcfm_custom_field_display($order){

		if(class_exists('Coderockz_Woo_Delivery') && !is_bool($order)) {

			if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
		        $order_id = $order->get_id();
		    } else {
		        $order_id = $order->id;
		    }

			$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');			
			$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');			
			$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
			$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_time_settings');
			$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
			$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');

			$delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : __( "Delivery Date", 'coderockz-woo-delivery' );
			$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : __( "Pickup Date", 'coderockz-woo-delivery' );
			$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : __( "Delivery Time", 'coderockz-woo-delivery' );
			$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : __( "Pickup Time", 'coderockz-woo-delivery' );
			$pickup_location_field_label = (isset($delivery_pickup_settings['field_label']) && !empty($delivery_pickup_settings['field_label'])) ? stripslashes($delivery_pickup_settings['field_label']) : __( "Pickup Location", 'coderockz-woo-delivery' );
			$additional_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? stripslashes($additional_field_settings['field_label']) : __( "Special Note About Delivery", 'coderockz-woo-delivery' );

			$delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";
			$add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

			if($add_weekday_name) {
				$delivery_date_format = "l ".$delivery_date_format;
			}

			$pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";

			$pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

			if($pickup_add_weekday_name) {
				$pickup_date_format = "l ".$pickup_date_format;
			}

			$time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
			if($time_format == 12) {
				$time_format = "h:i A";
			} elseif ($time_format == 24) {
				$time_format = "H:i";
			}
			
			$delivery_pickup_info = "";

			if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {
				if($this->hpos) {
					$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime($order->get_meta( 'pickup_date', true ))),"pickup"),"pickup");
				} else {
					$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime(get_post_meta( $order_id, 'pickup_date', true ))),"pickup"),"pickup");
				}
				$delivery_pickup_info .= "<p><strong>".$pickup_date_field_label.": </strong>" . $pickup_date."</p>";
			}

			if((metadata_exists('post', $order_id, 'pickup_time') && get_post_meta($order_id,"pickup_time",true) != "") || ($order->meta_exists('pickup_time') && $order->get_meta( 'pickup_time', true ) != "")) {
				if($this->hpos) {
					$pickup_minutes = $order->get_meta( 'pickup_time', true );
				} else {
					$pickup_minutes = get_post_meta($order_id,"pickup_time",true);
				}
				$pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
				if($pickup_time_format == 12) {
					$pickup_time_format = "h:i A";
				} elseif ($pickup_time_format == 24) {
					$pickup_time_format = "H:i";
				}
				$pickup_minutes = explode(' - ', $pickup_minutes);
	    		if(!isset($pickup_minutes[1])) {
	    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0]));
	    		} else {
	    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1]));
	    		}

				$delivery_pickup_info .= "<p><strong>".$pickup_time_field_label.": </strong>" . $pickup_time_value."</p>";

			}

			if((metadata_exists('post', $order_id, 'pickup_location') && get_post_meta($order_id, 'pickup_location', true) !="") || ($order->meta_exists('pickup_location') && $order->get_meta( 'pickup_location', true ) != "")) {
				if($this->hpos) {
					$pickup_location = $order->get_meta( 'pickup_location', true );
				} else {
					$pickup_location = get_post_meta($order_id,"pickup_location",true);
				}
				$delivery_pickup_info .= "<p><strong>".$pickup_location_field_label.": </strong>" . stripslashes(html_entity_decode($pickup_location, ENT_QUOTES))."</p>";
			}

			if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {

				if($this->hpos) {
					$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime($order->get_meta( 'delivery_date', true ))),"delivery"),"delivery");
				} else {
					$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime(get_post_meta( $order_id, 'delivery_date', true ))),"delivery"),"delivery");
				}

				$delivery_pickup_info .= "<p><strong>".$delivery_date_field_label.": </strong>" . $delivery_date."</p>";
			}

			if((metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id,"delivery_time",true) != "") || ($order->meta_exists('delivery_time') && $order->get_meta( 'delivery_time', true ) != "")) {

				if(get_post_meta($order_id,"delivery_time",true) == "as-soon-as-possible" || $order->get_meta( 'delivery_time', true ) == "as-soon-as-possible") {
					$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : "As Soon As Possible";
					$delivery_pickup_info .= "<p><strong>".$delivery_time_field_label.": </strong>" . $as_soon_as_possible_text."</p>";
				} else {
					if($this->hpos) {
						$minutes = $order->get_meta( 'delivery_time', true );
					} else {
						$minutes = get_post_meta($order_id,"delivery_time",true);
					}

					$minutes = explode(' - ', $minutes);
		    		if(!isset($minutes[1])) {
		    			$time_value = date($time_format, strtotime($minutes[0]));
		    		} else {
		    			$time_value = date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1]));
		    		}

					$delivery_pickup_info .= "<p><strong>".$delivery_time_field_label.": </strong>" . $time_value."</p>";
				}
			}

			if((metadata_exists('post', $order_id, 'additional_note') && get_post_meta($order_id, 'additional_note', true) !="") || ($order->meta_exists('additional_note') && $order->get_meta( 'additional_note', true )!= "")) {
				if($this->hpos) {
					$additional_note = $order->get_meta( 'additional_note', true );
				} else {
					$additional_note = get_post_meta($order_id, 'additional_note', true);
				}
				$delivery_pickup_info .= "<p><strong>".$additional_field_label.": </strong>" . stripslashes(html_entity_decode($additional_note, ENT_QUOTES))."</p>";
			}

			echo $delivery_pickup_info;
		}	
	
	}


	public function coderockz_woo_delivery_woocommerce_trash_order_action( $order_id ){

		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
		$order = wc_get_order( $order_id );

		if(((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && !in_array($order->get_status(), ['cancelled','failed','refunded'])) {
					
			if(get_option('coderockz_woo_delivery_max_per_day_count') !== false) {
				$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');

				if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {

					if($this->hpos) {
						$delivery_date = $order->get_meta( 'delivery_date', true );
					} else {
						$delivery_date = get_post_meta( $order_id, 'delivery_date', true );
					}

					if(isset($max_per_day_count['delivery']['order']) && array_key_exists($delivery_date, $max_per_day_count['delivery']['order'])) {
				    	if(isset($max_per_day_count['delivery']['order'][$delivery_date]) && ($max_per_day_count['delivery']['order'][$delivery_date]!= '' || $max_per_day_count['delivery']['order'][$delivery_date]>0)) {
				    		$max_per_day_count['delivery']['order'][$delivery_date] = $max_per_day_count['delivery']['order'][$delivery_date] - 1;
				    	}
				    }

				    $max_per_day_count = array_merge(get_option('coderockz_woo_delivery_max_per_day_count'),$max_per_day_count);
					update_option('coderockz_woo_delivery_max_per_day_count', $max_per_day_count);

				}
			}
		}


		if(((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && !in_array($order->get_status(), ['cancelled','failed','refunded'])) {
					
			if(get_option('coderockz_woo_delivery_max_per_day_count') !== false) {
				$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');

				if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {

					if($this->hpos) {
						$pickup_date = $order->get_meta( 'pickup_date', true );
					} else {
						$pickup_date = get_post_meta( $order_id, 'pickup_date', true );
					}

					if(isset($max_per_day_count['pickup']['order']) && array_key_exists($pickup_date, $max_per_day_count['pickup']['order'])) {
				    	if(isset($max_per_day_count['pickup']['order'][$pickup_date]) && ($max_per_day_count['pickup']['order'][$pickup_date]!= '' || $max_per_day_count['pickup']['order'][$pickup_date] > 0)) {
				    		$max_per_day_count['pickup']['order'][$pickup_date] = $max_per_day_count['pickup']['order'][$pickup_date] - 1;
				    	}
				    }

				    $max_per_day_count = array_merge(get_option('coderockz_woo_delivery_max_per_day_count'),$max_per_day_count);
					update_option('coderockz_woo_delivery_max_per_day_count', $max_per_day_count);

				}
			}
		}

	}

	public function coderockz_woo_delivery_woocommerce_untrash_order_action( $order_id ){

		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
		$order = wc_get_order( $order_id );

		if(((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && !in_array($order->get_status(), ['cancelled','failed','refunded'])) {
					
			if(get_option('coderockz_woo_delivery_max_per_day_count') !== false) {
				$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');

				if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {

					if($this->hpos) {
						$delivery_date = $order->get_meta( 'delivery_date', true );
					} else {
						$delivery_date = get_post_meta( $order_id, 'delivery_date', true );
					}

				
					if(isset($max_per_day_count['delivery']['order']) && array_key_exists($delivery_date, $max_per_day_count['delivery']['order'])) {
				    	if(isset($max_per_day_count['delivery']['order'][$delivery_date]) && ($max_per_day_count['delivery']['order'][$delivery_date]!= '' || $max_per_day_count['delivery']['order'][$delivery_date]>= 0)) {
				    		$max_per_day_count['delivery']['order'][$delivery_date] = $max_per_day_count['delivery']['order'][$delivery_date] + 1;
				    	} else {
				    		$max_per_day_count['delivery']['order'][$delivery_date] = 1;
				    	}
				    } else {
				    	$max_per_day_count['delivery']['order'][$delivery_date] = 1;
				    }

				    $max_per_day_count = array_merge(get_option('coderockz_woo_delivery_max_per_day_count'),$max_per_day_count);
					update_option('coderockz_woo_delivery_max_per_day_count', $max_per_day_count);

				}

			}
		}

		if(((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && !in_array($order->get_status(), ['cancelled','failed','refunded'])) {
					
			if(get_option('coderockz_woo_delivery_max_per_day_count') !== false) {
				$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');

				if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {

					if($this->hpos) {
						$pickup_date = $order->get_meta( 'pickup_date', true );
					} else {
						$pickup_date = get_post_meta( $order_id, 'pickup_date', true );
					}

					if(isset($max_per_day_count['pickup']['order']) && array_key_exists($pickup_date, $max_per_day_count['pickup']['order'])) {
				    	if(isset($max_per_day_count['pickup']['order'][$pickup_date]) && ($max_per_day_count['pickup']['order'][$pickup_date]!= '' || $max_per_day_count['pickup']['order'][$pickup_date]>= 0)) {
				    		$max_per_day_count['pickup']['order'][$pickup_date] = $max_per_day_count['pickup']['order'][$pickup_date] + 1;
				    	} else {
				    		$max_per_day_count['pickup']['order'][$pickup_date] = 1;
				    	}
				    } else {
				    	$max_per_day_count['pickup']['order'][$pickup_date] = 1;
				    }

				    $max_per_day_count = array_merge(get_option('coderockz_woo_delivery_max_per_day_count'),$max_per_day_count);
					update_option('coderockz_woo_delivery_max_per_day_count', $max_per_day_count);

				}
			}
		}

	}

	public function coderockz_woo_delivery_woocommerce_status_change($order_id, $old_status, $new_status) {

	    $delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
	    $order = wc_get_order($order_id);

	    if(((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && in_array($old_status, ['cancelled','failed','refunded']) && !in_array($new_status, ['cancelled','failed','refunded'])) {
					
			if(get_option('coderockz_woo_delivery_max_per_day_count') !== false) {
				$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');

				if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {

					if($this->hpos) {
						$delivery_date = $order->get_meta( 'delivery_date', true );
					} else {
						$delivery_date = get_post_meta( $order_id, 'delivery_date', true );
					}

				
					if(isset($max_per_day_count['delivery']['order']) && array_key_exists($delivery_date, $max_per_day_count['delivery']['order'])) {
				    	if(isset($max_per_day_count['delivery']['order'][$delivery_date]) && ($max_per_day_count['delivery']['order'][$delivery_date]!= '' || $max_per_day_count['delivery']['order'][$delivery_date]>= 0)) {
				    		$max_per_day_count['delivery']['order'][$delivery_date] = $max_per_day_count['delivery']['order'][$delivery_date] + 1;
				    	} else {
				    		$max_per_day_count['delivery']['order'][$delivery_date] = 1;
				    	}
				    } else {
				    	$max_per_day_count['delivery']['order'][$delivery_date] = 1;
				    }

				    $max_per_day_count = array_merge(get_option('coderockz_woo_delivery_max_per_day_count'),$max_per_day_count);
					update_option('coderockz_woo_delivery_max_per_day_count', $max_per_day_count);

				}

			}
		} elseif(((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && !in_array($old_status, ['cancelled','failed','refunded']) && in_array($new_status, ['cancelled','failed','refunded'])) {
					
			if(get_option('coderockz_woo_delivery_max_per_day_count') !== false) {
				$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');

				if((metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") || ($order->meta_exists('delivery_date') && $order->get_meta( 'delivery_date', true ) != "")) {

					if($this->hpos) {
						$delivery_date = $order->get_meta( 'delivery_date', true );
					} else {
						$delivery_date = get_post_meta( $order_id, 'delivery_date', true );
					}

					if(isset($max_per_day_count['delivery']['order']) && array_key_exists($delivery_date, $max_per_day_count['delivery']['order'])) {
				    	if(isset($max_per_day_count['delivery']['order'][$delivery_date]) && ($max_per_day_count['delivery']['order'][$delivery_date]!= '' || $max_per_day_count['delivery']['order'][$delivery_date] > 0)) {
				    		$max_per_day_count['delivery']['order'][$delivery_date] = $max_per_day_count['delivery']['order'][$delivery_date] - 1;
				    	}
				    }

				    $max_per_day_count = array_merge(get_option('coderockz_woo_delivery_max_per_day_count'),$max_per_day_count);
					update_option('coderockz_woo_delivery_max_per_day_count', $max_per_day_count);

				}
			}
		}


		if(((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && in_array($old_status, ['cancelled','failed','refunded']) && !in_array($new_status, ['cancelled','failed','refunded'])) {
					
			if(get_option('coderockz_woo_delivery_max_per_day_count') !== false) {
				$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');

				if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {

					if($this->hpos) {
						$pickup_date = $order->get_meta( 'pickup_date', true );
					} else {
						$pickup_date = get_post_meta( $order_id, 'pickup_date', true );
					}

					if(isset($max_per_day_count['pickup']['order']) && array_key_exists($pickup_date, $max_per_day_count['pickup']['order'])) {
				    	if(isset($max_per_day_count['pickup']['order'][$pickup_date]) && ($max_per_day_count['pickup']['order'][$pickup_date]!= '' || $max_per_day_count['pickup']['order'][$pickup_date]>= 0)) {
				    		$max_per_day_count['pickup']['order'][$pickup_date] = $max_per_day_count['pickup']['order'][$pickup_date] + 1;
				    	} else {
				    		$max_per_day_count['pickup']['order'][$pickup_date] = 1;
				    	}
				    } else {
				    	$max_per_day_count['pickup']['order'][$pickup_date] = 1;
				    }

				    $max_per_day_count = array_merge(get_option('coderockz_woo_delivery_max_per_day_count'),$max_per_day_count);
					update_option('coderockz_woo_delivery_max_per_day_count', $max_per_day_count);

				}
			}
		} elseif(((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) && !in_array($old_status, ['cancelled','failed','refunded']) && in_array($new_status, ['cancelled','failed','refunded'])) {
					
			if(get_option('coderockz_woo_delivery_max_per_day_count') !== false) {
				$max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');

				if((metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") || ($order->meta_exists('pickup_date') && $order->get_meta( 'pickup_date', true ) != "")) {

					if($this->hpos) {
						$pickup_date = $order->get_meta( 'pickup_date', true );
					} else {
						$pickup_date = get_post_meta( $order_id, 'pickup_date', true );
					}

					if(isset($max_per_day_count['pickup']['order']) && array_key_exists($pickup_date, $max_per_day_count['pickup']['order'])) {
				    	if(isset($max_per_day_count['pickup']['order'][$pickup_date]) && ($max_per_day_count['pickup']['order'][$pickup_date]!= '' || $max_per_day_count['pickup']['order'][$pickup_date] > 0)) {
				    		$max_per_day_count['pickup']['order'][$pickup_date] = $max_per_day_count['pickup']['order'][$pickup_date] - 1;
				    	}
				    }

				    $max_per_day_count = array_merge(get_option('coderockz_woo_delivery_max_per_day_count'),$max_per_day_count);
					update_option('coderockz_woo_delivery_max_per_day_count', $max_per_day_count);

				}
			}
		}
	}

    public function coderockz_woo_delivery_admin_init_functionality () {

		// DON'T DELETE || PERMANENT CODE
		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');

		$today = wp_date('Y-m-d',current_time( 'timestamp', 1 ));

		if($this->helper->detect_plugin_settings_page()) {
			$order_status_keys = array_keys(wc_get_order_statuses());
			$order_status = ['partially-paid'];
			foreach($order_status_keys as $order_status_key) {
				$order_status[] = substr($order_status_key,3);
			}
			$order_status = array_diff($order_status,['cancelled','failed','refunded']);

			$formated_obj = current_datetime($today);

			if((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) {
				
				if(get_option('coderockz_woo_delivery_max_per_day_counting_completed') == false) {
					$max_order_product_per_day['delivery'] = [];
					$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
					$period = $this->helper->get_date_from_range($today, $range_last_date);
					foreach ($period as $date) { 

					    if($this->hpos) {
					    	$args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $order_status,
								'meta_query' => array(
						            array(
						                'key'     => 'delivery_date',
						                'value'   => $date,
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'delivery_type',
						                'value'   => 'delivery',
						                'compare' => '==',
						            ),
						        ),
						        'return' => 'ids'
						    );
					    } else {
					    	$args = array(
						        'limit' => -1,
						        'delivery_date' => $date,
						        'delivery_type' => "delivery",
						        'status' => $order_status,
						        'return' => 'ids'
						    );
					    }

					    $delivery_orders_array = wc_get_orders( $args );

					    if( count($delivery_orders_array) > 0 ) {
						    if(isset($max_order_product_per_day['delivery']['order']) && array_key_exists($date, $max_order_product_per_day['delivery']['order'])) {
						    	if(isset($max_order_product_per_day['delivery']['order'][$date]) && ($max_order_product_per_day['delivery']['order'][$date]!= '' || $max_order_product_per_day['delivery']['order'][$date]>=0)) {
						    		$max_order_product_per_day['delivery']['order'][$date] = $max_order_product_per_day['delivery']['order'][$date] + count($delivery_orders_array);
						    	} else {
						    		$max_order_product_per_day['delivery']['order'][$date] = count($delivery_orders_array);
						    	}
						    } else {
						    	$max_order_product_per_day['delivery']['order'][$date] = count($delivery_orders_array);
						    }
						}

					}

					if(get_option('coderockz_woo_delivery_max_per_day_count') == false) {
						update_option('coderockz_woo_delivery_max_per_day_count', $max_order_product_per_day);
					} else {
						$max_order_product_per_day = array_merge(get_option('coderockz_woo_delivery_max_per_day_count'),$max_order_product_per_day);
						update_option('coderockz_woo_delivery_max_per_day_count', $max_order_product_per_day);
					}

					update_option('coderockz_woo_delivery_max_per_day_counting_completed','completed');

				}

			} else {
				delete_option('coderockz_woo_delivery_max_per_day_counting_completed');
				$temp_max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');
				if($temp_max_per_day_count !== false && isset($temp_max_per_day_count['delivery']['order']))
					unset($temp_max_per_day_count['delivery']['order']);
				update_option('coderockz_woo_delivery_max_per_day_count', $temp_max_per_day_count);
			}

			if((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) {
				
				if(get_option('coderockz_woo_delivery_max_pickup_per_day_counting_completed') == false) {
					$max_order_product_per_day['pickup'] = [];
					$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
					$period = $this->helper->get_date_from_range($today, $range_last_date);
					$max_pickup_per_day = (isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") ? (int)$pickup_date_settings['maximum_pickup_per_day'] : 10000000000000;
					foreach ($period as $date) {
						
					    if($this->hpos) {
					    	$pickup_args = array(
						        'limit' => -1,
								'type' => array( 'shop_order' ),
								'status' => $order_status,
								'meta_query' => array(
						            array(
						                'key'     => 'pickup_date',
						                'value'   => $date,
						                'compare' => '==',
						            ),
						            array(
						                'key'     => 'delivery_type',
						                'value'   => 'pickup',
						                'compare' => '==',
						            ),
						        ),
						        'return' => 'ids'
						    );
					    } else {
					    	$pickup_args = array(
						        'limit' => -1,
						        'pickup_date' => $date,
						        'delivery_type' => "pickup",
						        'status' => $order_status,
						        'return' => 'ids'
						    );
					    }

					    $pickup_orders_array = wc_get_orders( $pickup_args );

					    if( count($pickup_orders_array) > 0 ) {
						    if(isset($max_order_product_per_day['pickup']['order']) && array_key_exists($date, $max_order_product_per_day['pickup']['order'])) {
						    	if(isset($max_order_product_per_day['pickup']['order'][$date]) && ($max_order_product_per_day['pickup']['order'][$date]!= '' || $max_order_product_per_day['pickup']['order'][$date]>=0)) {
						    		$max_order_product_per_day['pickup']['order'][$date] = $max_order_product_per_day['pickup']['order'][$date] + count($pickup_orders_array);
						    	} else {
						    		$max_order_product_per_day['pickup']['order'][$date] = count($pickup_orders_array);
						    	}
						    } else {
						    	$max_order_product_per_day['pickup']['order'][$date] = count($pickup_orders_array);
						    }
						}	

					}

					if(get_option('coderockz_woo_delivery_max_per_day_count') == false) {
						update_option('coderockz_woo_delivery_max_per_day_count', $max_order_product_per_day);
					} else {
						$max_order_product_per_day = array_merge(get_option('coderockz_woo_delivery_max_per_day_count'),$max_order_product_per_day);
						update_option('coderockz_woo_delivery_max_per_day_count', $max_order_product_per_day);
					}

					update_option('coderockz_woo_delivery_max_pickup_per_day_counting_completed','completed');

				}

			} else {
				delete_option('coderockz_woo_delivery_max_pickup_per_day_counting_completed');
				$temp_max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');
				if($temp_max_per_day_count !== false && isset($temp_max_per_day_count['pickup']['order']))
					unset($temp_max_per_day_count['pickup']['order']);
				update_option('coderockz_woo_delivery_max_per_day_count', $temp_max_per_day_count);
			}
		}

		if((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) {
			if(get_option('coderockz_woo_delivery_trim_max_per_day_count') == false || get_option('coderockz_woo_delivery_trim_max_per_day_count') != $today) {

				$temp_max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');

				if(isset($temp_max_per_day_count['delivery']['order']) && !empty($temp_max_per_day_count['delivery']['order'])) { 
				
					foreach ($temp_max_per_day_count['delivery']['order'] AS $key => $date) {

					   if (strtotime($key) < strtotime($today)) {

							unset($temp_max_per_day_count['delivery']['order'][$key]);

					   }

					}

					update_option('coderockz_woo_delivery_max_per_day_count', $temp_max_per_day_count);
				}
				
			}
		}

		if((isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") || (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "")) {
			if(get_option('coderockz_woo_delivery_trim_max_per_day_count') == false || get_option('coderockz_woo_delivery_trim_max_per_day_count') != $today) {

				$temp_max_per_day_count = get_option('coderockz_woo_delivery_max_per_day_count');

				if(isset($temp_max_per_day_count['pickup']['order']) && !empty($temp_max_per_day_count['pickup']['order'])) { 
				
					foreach ($temp_max_per_day_count['pickup']['order'] AS $key => $date) {

					   if (strtotime($key) < strtotime($today)) {

							unset($temp_max_per_day_count['pickup']['order'][$key]);

					   }

					}

					update_option('coderockz_woo_delivery_max_per_day_count', $temp_max_per_day_count);

				}
			}
		}
		update_option('coderockz_woo_delivery_trim_max_per_day_count',$today);
		// END DON'T DELETE || PERMANENT CODE

    	if(get_option('coderockz_woo_delivery_change_pickup_time_settings_name')) {
			delete_option('coderockz_woo_delivery_change_pickup_time_settings_name');
		}

	    if(get_option('coderockz_woo_delivery_change_pickup_location_field_name')) {
			delete_option('coderockz_woo_delivery_change_pickup_location_field_name');
		}

		if(get_option('coderockz_woo_delivery_completed_category_opendays_extension')) {
			delete_option('coderockz_woo_delivery_completed_category_opendays_extension');
		}

		if(get_option('coderockz_woo_delivery_completed_category_opendays_pickup_extension')) {
			delete_option('coderockz_woo_delivery_completed_category_opendays_pickup_extension');
		}

		if(get_option('coderockz_woo_delivery_completed_offdays_extension')) {
			delete_option('coderockz_woo_delivery_completed_offdays_extension');
		}

		if(get_option('coderockz_woo_delivery_completed_order_settings_extension')) {
			delete_option('coderockz_woo_delivery_completed_order_settings_extension');
		}

    }

}
