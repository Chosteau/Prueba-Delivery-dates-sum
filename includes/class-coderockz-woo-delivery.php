<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://coderockz.com
 * @since      1.0.0
 *
 * @package    Coderockz_Woo_Delivery
 * @subpackage Coderockz_Woo_Delivery/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Coderockz_Woo_Delivery
 * @subpackage Coderockz_Woo_Delivery/includes
 * @author     CodeRockz <admin@coderockz.com>
 */
class Coderockz_Woo_Delivery {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Coderockz_Woo_Delivery_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'CODEROCKZ_WOO_DELIVERY_VERSION' ) ) {
			$this->version = CODEROCKZ_WOO_DELIVERY_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'coderockz-woo-delivery';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Coderockz_Woo_Delivery_Loader. Orchestrates the hooks of the plugin.
	 * - Coderockz_Woo_Delivery_i18n. Defines internationalization functionality.
	 * - Coderockz_Woo_Delivery_Admin. Defines all hooks for the admin area.
	 * - Coderockz_Woo_Delivery_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coderockz-woo-delivery-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coderockz-woo-delivery-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-coderockz-woo-delivery-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/libs/phpspreadsheet/autoload.php';

		$google_calendar_settings = get_option('coderockz_woo_delivery_google_calendar_settings');

		$calendar_sync_customer = isset($google_calendar_settings['google_calendar_customer_sync']) && !empty($google_calendar_settings['google_calendar_customer_sync']) ? $google_calendar_settings['google_calendar_customer_sync'] : false;

		$enable_calendar_sync_client = isset($google_calendar_settings['google_calendar_sync']) && !empty($google_calendar_settings['google_calendar_sync']) ? $google_calendar_settings['google_calendar_sync'] : false;

		$calendar_sync_customer_client_id = isset($google_calendar_settings['google_calendar_client_id']) && !empty($google_calendar_settings['google_calendar_client_id']) ? $google_calendar_settings['google_calendar_client_id'] : "";
		
		$calendar_sync_customer_client_secret = isset($google_calendar_settings['google_calendar_client_secret']) && !empty($google_calendar_settings['google_calendar_client_secret']) ? $google_calendar_settings['google_calendar_client_secret'] : "";

		if(($calendar_sync_customer && $calendar_sync_customer_client_id != "" && $calendar_sync_customer_client_secret != "") || ($enable_calendar_sync_client && $google_calendar_settings['google_calendar_client_id'] != "" && $google_calendar_settings['google_calendar_client_secret'] != "" )) {

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/libs/google-api/autoload.php';

		}

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-coderockz-woo-delivery-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coderockz-woo-delivery-helper.php';
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coderockz-woo-delivery-field-calculate.php';
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coderockz-woo-delivery-email.php';
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coderockz-woo-delivery-time-option.php';
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coderockz-woo-delivery-tips-option.php';
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coderockz-woo-delivery-pickup-time-option.php';
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coderockz-woo-delivery-pickup-location-option.php';
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coderockz-woo-delivery-delivery-option.php';

		$this->loader = new Coderockz_Woo_Delivery_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Coderockz_Woo_Delivery_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Coderockz_Woo_Delivery_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {


		$plugin_admin = new Coderockz_Woo_Delivery_Admin( $this->get_plugin_name(), $this->get_version() );
		
		$this->loader->add_action( 'admin_init', $plugin_admin, 'coderockz_woo_delivery_admin_init_functionality' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts',0 );
		
		$this->loader->add_action( 'restrict_manage_posts', $plugin_admin, 'coderockz_woo_delivery_filter_orders_by_delivery' );
		$this->loader->add_action( 'woocommerce_order_list_table_restrict_manage_orders', $plugin_admin, 'coderockz_woo_delivery_filter_orders_by_delivery' );
		
		$this->loader->add_filter( 'posts_join', $plugin_admin, 'coderockz_woo_delivery_add_order_items_join');
		$this->loader->add_filter( 'posts_where', $plugin_admin, 'coderockz_woo_delivery_add_filterable_where' );
		$this->loader->add_filter( 'woocommerce_orders_table_query_clauses', $plugin_admin, 'coderockz_woo_delivery_type_date_filter_order_page_hpos');


		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_get_order_details_for_delivery_calender', $plugin_admin, 'coderockz_woo_delivery_get_order_details_for_delivery_calender' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'coderockz_woo_delivery_menus_sections' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'coderockz_woo_delivery_woocommerce_submenu',99);
		$this->loader->add_filter( 'plugin_action_links_' . CODEROCKZ_WOO_DELIVERY , $plugin_admin, 'coderockz_woo_delivery_settings_link' );
		$this->loader->add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', $plugin_admin, 'coderockz_woo_delivery_handle_custom_query_var', 10, 2 );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_submit_report_filter_form', $plugin_admin, 'coderockz_woo_delivery_submit_report_filter_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_submit_report_product_quantity', $plugin_admin, 'coderockz_woo_delivery_submit_report_product_quantity' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_make_order_delivered', $plugin_admin, 'coderockz_woo_delivery_make_order_delivered' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_make_order_complete', $plugin_admin, 'coderockz_woo_delivery_make_order_complete' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_get_order_details', $plugin_admin, 'coderockz_woo_delivery_get_order_details' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_delivery_date_form', $plugin_admin, 'coderockz_woo_delivery_process_delivery_date_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_pickup_date_form', $plugin_admin, 'coderockz_woo_delivery_process_pickup_date_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_offdays_form', $plugin_admin, 'coderockz_woo_delivery_process_delivery_date_offdays_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_pickup_offdays_form', $plugin_admin, 'coderockz_woo_delivery_process_pickup_date_offdays_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_delivery_opendays_form', $plugin_admin, 'coderockz_woo_delivery_process_delivery_date_delivery_opendays_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_pickup_opendays_form', $plugin_admin, 'coderockz_woo_delivery_process_delivery_date_pickup_opendays_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_category_wise_offdays_form', $plugin_admin, 'coderockz_woo_delivery_category_wise_offdays_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_product_wise_offdays_form', $plugin_admin, 'coderockz_woo_delivery_product_wise_offdays_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_zone_wise_offdays_form', $plugin_admin, 'coderockz_woo_delivery_zone_wise_offdays_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_zone_wise_processingdays_form', $plugin_admin, 'coderockz_woo_delivery_zone_wise_processingdays_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_zone_wise_processingtime_form', $plugin_admin, 'coderockz_woo_delivery_zone_wise_processingtime_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_specific_date_coupon_form', $plugin_admin, 'coderockz_woo_delivery_specific_date_coupon_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_state_wise_offdays_form', $plugin_admin, 'coderockz_woo_delivery_state_wise_offdays_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_postcode_wise_offdays_form', $plugin_admin, 'coderockz_woo_delivery_postcode_wise_offdays_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_shippingmethod_wise_offdays_form', $plugin_admin, 'coderockz_woo_delivery_shippingmethod_wise_offdays_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_store_closing_form', $plugin_admin, 'coderockz_woo_delivery_process_store_closing_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_store_closing_pickup', $plugin_admin, 'coderockz_woo_delivery_process_store_closing_pickup' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_different_store_closing_form', $plugin_admin, 'coderockz_woo_delivery_process_different_store_closing_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_delivery_time_form', $plugin_admin, 'coderockz_woo_delivery_process_delivery_time_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_pickup_time_form', $plugin_admin, 'coderockz_woo_delivery_process_pickup_time_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_custom_time_slot_settings', $plugin_admin, 'coderockz_woo_delivery_process_custom_time_slot_settings' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_add_enable_custom_time_slot', $plugin_admin, 'coderockz_woo_delivery_add_enable_custom_time_slot' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_delete_custom_time_slot', $plugin_admin, 'coderockz_woo_delivery_delete_custom_time_slot' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_custom_pickup_slot_settings', $plugin_admin, 'coderockz_woo_delivery_process_custom_pickup_slot_settings' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_add_enable_custom_pickup_slot', $plugin_admin, 'coderockz_woo_delivery_add_enable_custom_pickup_slot' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_delete_custom_pickup_slot', $plugin_admin, 'coderockz_woo_delivery_delete_custom_pickup_slot' );		
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_pickup_location_form', $plugin_admin, 'coderockz_woo_delivery_process_pickup_location_form' );

		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_delete_pickup_location', $plugin_admin, 'coderockz_woo_delivery_delete_pickup_location' );

		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_enable_and_save_pickup_location', $plugin_admin, 'coderockz_woo_delivery_enable_and_save_pickup_location' );

		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_overall_processing_days_settings_form', $plugin_admin, 'coderockz_woo_delivery_overall_processing_days_settings_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_tips_settings_form', $plugin_admin, 'coderockz_woo_delivery_tips_settings_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_processing_days_settings_form', $plugin_admin, 'coderockz_woo_delivery_processing_days_settings_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_category_processing_days_form', $plugin_admin, 'coderockz_woo_delivery_category_processing_days_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_shippingmethod_wise_processingdays_form', $plugin_admin, 'coderockz_woo_delivery_shippingmethod_wise_processingdays_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_category_cutoff_form', $plugin_admin, 'coderockz_woo_delivery_category_cutoff_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_category_open_days_form', $plugin_admin, 'coderockz_woo_delivery_category_open_days_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_category_open_days_pickup_form', $plugin_admin, 'coderockz_woo_delivery_category_open_days_pickup_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_product_processing_days_form', $plugin_admin, 'coderockz_woo_delivery_product_processing_days_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_processing_time_settings_form', $plugin_admin, 'coderockz_woo_delivery_processing_time_settings_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_conditional_delivery_fee_settings_form', $plugin_admin, 'coderockz_woo_delivery_conditional_delivery_fee_settings_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_category_processing_time_form', $plugin_admin, 'coderockz_woo_delivery_category_processing_time_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_product_processing_time_form', $plugin_admin, 'coderockz_woo_delivery_product_processing_time_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_weekday_processing_time_settings', $plugin_admin, 'coderockz_woo_delivery_weekday_processing_time_settings' );
		
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_common_email_settings', $plugin_admin, 'coderockz_woo_delivery_process_common_email_settings' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_reminder_email', $plugin_admin, 'coderockz_woo_delivery_process_reminder_email' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_notify_email', $plugin_admin, 'coderockz_woo_delivery_process_notify_email' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_time_slot_fee', $plugin_admin, 'coderockz_woo_delivery_process_time_slot_fee' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_specific_date_fee', $plugin_admin, 'coderockz_woo_delivery_process_specific_date_fee' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_pickup_slot_fee', $plugin_admin, 'coderockz_woo_delivery_process_pickup_slot_fee' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_delivery_date_fee', $plugin_admin, 'coderockz_woo_delivery_process_delivery_date_fee' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_weekday_wise_fee', $plugin_admin, 'coderockz_woo_delivery_process_weekday_wise_fee' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_weekday_wise_processing_days', $plugin_admin, 'coderockz_woo_delivery_process_weekday_wise_processing_days' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_additional_field', $plugin_admin, 'coderockz_woo_delivery_process_additional_field' );

		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_exclusion_settings_form', $plugin_admin, 'coderockz_woo_delivery_exclusion_settings_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_next_month_off_settings_form', $plugin_admin, 'coderockz_woo_delivery_next_month_off_settings_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_next_week_off_settings_form', $plugin_admin, 'coderockz_woo_delivery_next_week_off_settings_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_current_week_off_settings_form', $plugin_admin, 'coderockz_woo_delivery_current_week_off_settings_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_overall_laundry_store_settings_form', $plugin_admin, 'coderockz_woo_delivery_overall_laundry_store_settings_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_category_after_pickup_dates_form', $plugin_admin, 'coderockz_woo_delivery_category_after_pickup_dates_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_other_settings', $plugin_admin, 'coderockz_woo_delivery_process_other_settings' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_google_calendar_settings', $plugin_admin, 'coderockz_woo_delivery_process_google_calendar_settings' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_localization_settings', $plugin_admin, 'coderockz_woo_delivery_process_localization_settings' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_process_delivery_option_settings', $plugin_admin, 'coderockz_woo_delivery_process_delivery_option_settings' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_delivery_restriction_settings', $plugin_admin, 'coderockz_woo_delivery_delivery_restriction_settings' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_pickup_restriction_settings', $plugin_admin, 'coderockz_woo_delivery_pickup_restriction_settings' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_category_product_delivery_restriction_settings_form', $plugin_admin, 'coderockz_woo_delivery_category_product_delivery_restriction_settings_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_category_product_pickup_restriction_settings_form', $plugin_admin, 'coderockz_woo_delivery_category_product_pickup_restriction_settings_form' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_free_shipping_restriction_settings', $plugin_admin, 'coderockz_woo_delivery_free_shipping_restriction_settings' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_disable_delivery_facility_days', $plugin_admin, 'coderockz_woo_delivery_disable_delivery_facility_days' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_disable_pickup_facility_days', $plugin_admin, 'coderockz_woo_delivery_disable_pickup_facility_days' );
		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_spinner_clear_btn', $plugin_admin, 'coderockz_woo_delivery_spinner_clear_btn' );

		$this->loader->add_filter('manage_woocommerce_page_wc-orders_columns', $plugin_admin, "coderockz_woo_delivery_add_custom_fields_orders_list");
		$this->loader->add_action('woocommerce_shop_order_list_table_custom_column', $plugin_admin, "coderockz_woo_delivery_show_custom_fields_data_orders_list_hpos", 10, 2 );

		$this->loader->add_filter('manage_edit-shop_order_columns', $plugin_admin, "coderockz_woo_delivery_add_custom_fields_orders_list");
		$this->loader->add_action('manage_shop_order_posts_custom_column', $plugin_admin, "coderockz_woo_delivery_show_custom_fields_data_orders_list");

		/*$this->loader->add_filter('manage_edit-shop_subscription_columns', $plugin_admin, "coderockz_woo_delivery_add_custom_fields_subscription_list");
		$this->loader->add_action('manage_shop_subscription_posts_custom_column', $plugin_admin, "coderockz_woo_delivery_show_custom_fields_data_subscription_list");*/

		$this->loader->add_action( 'woocommerce_admin_order_data_after_shipping_address', $plugin_admin, 'coderockz_woo_delivery_information_after_shipping_address', 10, 1 );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'coderockz_woo_delivery_review_notice' );
		/*$this->loader->add_action( 'admin_notices', $plugin_admin, 'coderockz_woo_delivery_notice_of_separate_pickup_date' );*/
        $this->loader->add_action('wp_ajax_coderockz_woo_delivery_save_review_notice', $plugin_admin, 'coderockz_woo_delivery_save_review_notice');
        $this->loader->add_action('admin_footer', $plugin_admin, 'coderockz_woo_delivery_deactivate_scripts');
        $this->loader->add_action('wp_ajax_coderockz-woo-delivery-submit-deactivate-reason', $plugin_admin, 'coderockz_woo_delivery_deactivate_reason_submission');

        $this->loader->add_action('admin_footer', $plugin_admin, 'coderockz_woo_delivery_review_scripts');
        $this->loader->add_action('wp_ajax_coderockz-woo-delivery-submit-review', $plugin_admin, 'coderockz_woo_delivery_review_submission');

        $this->loader->add_action("add_meta_boxes", $plugin_admin, 'coderockz_woo_delivery_custom_meta_box');
        $this->loader->add_action('wp_ajax_coderockz_woo_delivery_meta_box_get_orders', $plugin_admin, 'coderockz_woo_delivery_meta_box_get_orders');
        $this->loader->add_action('wp_ajax_coderockz_woo_delivery_meta_box_get_orders_pickup', $plugin_admin, 'coderockz_woo_delivery_meta_box_get_orders_pickup');

        
        $this->loader->add_action('wp_ajax_coderockz_woo_delivery_save_meta_box_information', $plugin_admin, 'coderockz_woo_delivery_save_meta_box_information');
        $this->loader->add_action('wp_ajax_coderockz_woo_delivery_get_state_zip_disable_weekday', $plugin_admin, 'coderockz_woo_delivery_get_state_zip_disable_weekday');

        
        $this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_make_delivery_completed_bulk', $plugin_admin, 'coderockz_woo_delivery_make_order_delivered_bulk' );
        
        $this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_make_google_calendar_sync_bulk', $plugin_admin, 'coderockz_woo_delivery_make_google_calendar_sync_bulk' );

        $this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_admin_option_delivery_time_pickup', $plugin_admin, 'coderockz_woo_delivery_admin_option_delivery_time_pickup' );

        $this->loader->add_filter('bulk_actions-edit-shop_order', $plugin_admin, 'register_bulk_delivery_completed_actions',11);
        $this->loader->add_filter('bulk_actions-woocommerce_page_wc-orders', $plugin_admin, 'register_bulk_delivery_completed_actions',11);

        $this->loader->add_filter( 'get_user_option_meta-box-order_shop_order', $plugin_admin, 'override_post_meta_box_order' );
        $this->loader->add_filter( 'get_user_option_meta-box-order_wc-orders', $plugin_admin, 'override_post_meta_box_order' );

 
		$notify_email_settings = get_option('coderockz_woo_delivery_notify_email_settings');
		$notify_email_different_name_email = (isset($notify_email_settings['notify_email_different_name_email']) && !empty($notify_email_settings['notify_email_different_name_email'])) ? $notify_email_settings['notify_email_different_name_email'] : false;

		if($notify_email_different_name_email) {
			$this->loader->add_filter( 'wp_mail_from', $plugin_admin, 'coderockz_woo_delivery_sender_email' );
			$this->loader->add_filter( 'wp_mail_from_name', $plugin_admin, 'coderockz_woo_delivery_sender_name' );
		}

		$this->loader->add_action('wp_ajax_coderockz_woo_delivery_plugin_settings_export', $plugin_admin, 'coderockz_woo_delivery_plugin_settings_export');

		$this->loader->add_action('wp_ajax_coderockz_woo_delivery_process_reset_plugin_settings', $plugin_admin, 'coderockz_woo_delivery_process_reset_plugin_settings');

		$this->loader->add_action( 'woocommerce_order_status_changed', $plugin_admin, 'coderockz_woo_delivery_make_delivery_completed_with_order_completed', 99, 3 );

		$this->loader->add_action('wp_ajax_coderockz_woo_delivery_make_google_unauthenticate', $plugin_admin, 'coderockz_woo_delivery_make_google_unauthenticate');

		$this->loader->add_filter( "manage_edit-shop_order_sortable_columns", $plugin_admin, 'coderockz_woo_delivery_deliverywise_order_sort' );
		$this->loader->add_action('pre_get_posts', $plugin_admin, 'coderockz_woo_delivery_meta_field_sortable_orderby' );

		$this->loader->add_filter( "manage_woocommerce_page_wc-orders_sortable_columns", $plugin_admin, 'coderockz_woo_delivery_deliverywise_order_sort' );
		$this->loader->add_filter( 'woocommerce_orders_table_query_clauses', $plugin_admin, 'coderockz_woo_delivery_meta_field_sortable_orderby_hpos' );

		
		$this->loader->add_filter( 'woocommerce_shop_order_search_fields', $plugin_admin, 'coderockz_woo_delivery_deliverywise_searchable_field' );
		$this->loader->add_filter( 'woocommerce_order_table_search_query_meta_keys', $plugin_admin, 'coderockz_woo_delivery_deliverywise_searchable_field_hpos' );
		
		$this->loader->add_action( 'wpo_wcpdf_after_order_data', $plugin_admin, 'coderockz_woo_delivery_wpo_wcpdf_delivery_info', 10, 2); 

		$this->loader->add_filter( 'wcfm_orders_additional_info_column_label', $plugin_admin, 'coderockz_woo_delivery_wcfm_orders_additional_info_column_label' );

		$this->loader->add_filter( 'wcfm_orders_additonal_data_hidden', $plugin_admin, 'coderockz_woo_delivery_wcfm_orders_additonal_data_hidden' );

		$this->loader->add_filter( 'wcfm_orders_additonal_data', $plugin_admin, 'coderockz_woo_delivery_wcfm_orders_additonal_data', 50, 2);

		$this->loader->add_action( 'wcfm_order_details_after_order_table', $plugin_admin, 'coderockz_woo_delivery_wcfm_custom_field_display', 10, 1 );

		$this->loader->add_action( 'woocommerce_trash_order', $plugin_admin, 'coderockz_woo_delivery_woocommerce_trash_order_action' );
		$this->loader->add_action( 'woocommerce_untrash_order', $plugin_admin, 'coderockz_woo_delivery_woocommerce_untrash_order_action' );
		$this->loader->add_action('woocommerce_order_status_changed', $plugin_admin, 'coderockz_woo_delivery_woocommerce_status_change', 10, 3);

		//$this->loader->add_action( 'woocommerce_process_shop_order_meta', $plugin_admin, 'coderockz_woo_delivery_save_delivery_pickup_details' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Coderockz_Woo_Delivery_Public( $this->get_plugin_name(), $this->get_version() );

		//$this->loader->add_action( 'init', $plugin_public, 'coderockz_woo_delivery_init_functionality' );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 0 );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'dequeue_salient_theme_hoverintent_script', 99999 );
		
		$other_settings = get_option('coderockz_woo_delivery_other_settings');
		$position = isset($other_settings['field_position']) && $other_settings['field_position'] != "" ? $other_settings['field_position'] : "after_billing";
		
		if($position == "before_billing") {
			$this->loader->add_action( 'woocommerce_checkout_billing', $plugin_public, 'coderockz_woo_delivery_add_custom_field' );

		} elseif( $position == "after_billing" ) {
			$this->loader->add_action( 'woocommerce_after_checkout_billing_form', $plugin_public, 'coderockz_woo_delivery_add_custom_field' );
		} elseif($position == "before_shipping") {
			$this->loader->add_action( 'woocommerce_checkout_shipping', $plugin_public, 'coderockz_woo_delivery_add_custom_field' );

		} elseif( $position == "after_shipping" ) {
			$this->loader->add_action( 'woocommerce_after_checkout_shipping_form', $plugin_public, 'coderockz_woo_delivery_add_custom_field' );
		} elseif( $position == "before_notes" ) {
			$this->loader->add_action( 'woocommerce_before_order_notes', $plugin_public, 'coderockz_woo_delivery_add_custom_field' );
		} elseif( $position == "after_notes" ) {
			$this->loader->add_action( 'woocommerce_after_order_notes', $plugin_public, 'coderockz_woo_delivery_add_custom_field' );
		} elseif( $position == "before_payment" ) {
			$this->loader->add_action( 'woocommerce_review_order_before_payment', $plugin_public, 'coderockz_woo_delivery_add_custom_field');
		} elseif( $position == "before_your_order" ) {
			$this->loader->add_action( 'woocommerce_checkout_before_order_review_heading', $plugin_public, 'coderockz_woo_delivery_add_custom_field');
		} elseif($position == "before_customer_details") {
			$this->loader->add_action( 'woocommerce_checkout_before_customer_details', $plugin_public, 'coderockz_woo_delivery_add_custom_field' );
		} 

		$this->loader->add_action('woocommerce_checkout_process', $plugin_public, 'coderockz_woo_delivery_customise_checkout_field_process');
		//$this->loader->add_action('woocommerce_after_checkout_validation', $plugin_public, 'coderockz_woo_delivery_customise_checkout_field_process');
		$this->loader->add_action('woocommerce_checkout_update_order_meta', $plugin_public, 'coderockz_woo_delivery_customise_checkout_field_update_order_meta');

		$this->loader->add_action('wp_ajax_coderockz_woo_delivery_get_orders', $plugin_public, 'coderockz_woo_delivery_get_orders');
		$this->loader->add_action('wp_ajax_nopriv_coderockz_woo_delivery_get_orders', $plugin_public, 'coderockz_woo_delivery_get_orders');
		
		$this->loader->add_action('wp_ajax_coderockz_woo_delivery_get_orders_pickup', $plugin_public, 'coderockz_woo_delivery_get_orders_pickup');
		$this->loader->add_action('wp_ajax_nopriv_coderockz_woo_delivery_get_orders_pickup', $plugin_public, 'coderockz_woo_delivery_get_orders_pickup');

		$this->loader->add_filter( 'woocommerce_account_orders_columns', $plugin_public, 'coderockz_woo_delivery_add_account_orders_column', 10, 1 );
		$this->loader->add_action( "woocommerce_my_account_my_orders_column_order_delivery_details", $plugin_public, "coderockz_woo_delivery_show_delivery_details_my_account_tab");
		$this->loader->add_action( 'woocommerce_cart_calculate_fees', $plugin_public, 'coderockz_woo_delivery_add_custom_fee', 10, 1 );

		$this->loader->add_action( 'woocommerce_before_calculate_totals', $plugin_public, 'auto_add_remove_coupon_based_delivery_date' );

		$this->loader->add_action( 'woocommerce_checkout_update_order_review', $plugin_public, 'coderockz_checkout_delivery_date_time_set_session' );

		$this->loader->add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', $plugin_public, 'coderockz_woo_delivery_handle_custom_query_var', 10, 2 );

		$this->loader->add_filter( 'woocommerce_get_order_item_totals', $plugin_public, 'coderockz_woo_delivery_add_delivery_information_row', 10, 2 );

		$this->loader->add_action('wp_ajax_coderockz_woo_delivery_option_delivery_time_pickup', $plugin_public, 'coderockz_woo_delivery_option_delivery_time_pickup');
		$this->loader->add_action('wp_ajax_nopriv_coderockz_woo_delivery_option_delivery_time_pickup', $plugin_public, 'coderockz_woo_delivery_option_delivery_time_pickup');

		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_check_state_disable_timeslot', $plugin_public, 'coderockz_woo_delivery_check_state_disable_timeslot' );
		$this->loader->add_action( 'wp_ajax_nopriv_coderockz_woo_delivery_check_state_disable_timeslot', $plugin_public, 'coderockz_woo_delivery_check_state_disable_timeslot' );

		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_get_state_zip_disable_weekday_checkout', $plugin_public, 'coderockz_woo_delivery_get_state_zip_disable_weekday_checkout' );
		$this->loader->add_action( 'wp_ajax_nopriv_coderockz_woo_delivery_get_state_zip_disable_weekday_checkout', $plugin_public, 'coderockz_woo_delivery_get_state_zip_disable_weekday_checkout' );

		$this->loader->add_filter( 'woocommerce_package_rates', $plugin_public, 'hide_show_shipping_methods_based_on_selection', 100, 2 );
		$this->loader->add_action( 'woocommerce_checkout_update_order_review', $plugin_public, 'coderockz_woo_delivery_refresh_shipping_methods', 10, 1 );
		
		$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');
		$disable_order_notes = (isset($additional_field_settings['disable_order_notes']) && !empty($additional_field_settings['disable_order_notes'])) ? $additional_field_settings['disable_order_notes'] : false;

		if($disable_order_notes) {
			$this->loader->add_filter( 'woocommerce_enable_order_notes_field', $plugin_public, 'coderockz_woo_delivery_remove_order_note', 9999 );
		}

		$this->loader->add_action( 'woocommerce_checkout_order_processed', $plugin_public, 'coderockz_woo_delivery_add_delivery_info_order_note',  50, 3  );

		$this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'coderockz_woo_delivery_add_delivery_info_google_calendar', 10, 1 );

		$this->loader->add_action( 'woocommerce_before_cart', $plugin_public, 'add_custom_notice_minimum_amount');
		$this->loader->add_action( 'woocommerce_before_checkout_form', $plugin_public, 'add_custom_notice_minimum_amount');

		$this->loader->add_action('wp_ajax_coderockz_woo_delivery_get_available_shipping_methods', $plugin_public, 'coderockz_woo_delivery_get_available_shipping_methods');
		$this->loader->add_action('wp_ajax_nopriv_coderockz_woo_delivery_get_available_shipping_methods', $plugin_public, 'coderockz_woo_delivery_get_available_shipping_methods');

		$this->loader->add_action( 'wp_ajax_coderockz_woo_delivery_get_correct_formated_date', $plugin_public, 'coderockz_woo_delivery_get_correct_formated_date' );
		$this->loader->add_action( 'wp_ajax_nopriv_coderockz_woo_delivery_get_correct_formated_date', $plugin_public, 'coderockz_woo_delivery_get_correct_formated_date' );

		$this->loader->add_action('wp_ajax_coderockz_woo_delivery_disable_conditional_delivery_for_no_conditional_shipping_method', $plugin_public, 'coderockz_woo_delivery_disable_conditional_delivery_for_no_conditional_shipping_method');
		$this->loader->add_action('wp_ajax_nopriv_coderockz_woo_delivery_disable_conditional_delivery_for_no_conditional_shipping_method', $plugin_public, 'coderockz_woo_delivery_disable_conditional_delivery_for_no_conditional_shipping_method');

		$this->loader->add_action( 'wp_footer', $plugin_public,'coderockz_woo_delivery_load_custom_css', 50000);

		$this->loader->add_filter( 'woocommerce_thankyou_order_received_text', $plugin_public, 'coderockz_woo_delivery_add_google_calendar_btn', 999, 2);

		$this->loader->add_action( 'dokan_checkout_update_order_meta', $plugin_public, 'dokan_checkout_update_order_meta',10,2);

		$this->loader->add_filter('woocommerce_email_recipient_new_order', $plugin_public, 'coderockz_woo_delivery_new_order_email_recipient', 10, 2);

		$this->loader->add_action('coderockz_woo_delivery_reminder_email_schedule', $plugin_public, 'coderockz_woo_delivery_reminder_email_schedule');

		$this->loader->add_filter( 'woocommerce_form_field_select', $plugin_public, 'coderockz_woo_delivery_prevent_field_value_change', 20, 4 );

		$this->loader->add_action( 'wpi_after_formatted_shipping_address', $plugin_public, 'coderockz_woo_delivery_info_at_wpi_invoice', 10, 1 );

		$this->loader->add_action( 'wc_pip_after_body', $plugin_public, 'coderockz_woo_delivery_info_at_pip_invoice', 10, 4 );
		
		$this->loader->add_filter( 'wcdn_order_info_fields', $plugin_public, 'coderockz_woo_delivery_info_at_wcdn_invoice', 10, 2 );

		$this->loader->add_action( 'woocommerce_cloudprint_internaloutput_footer', $plugin_public, 'coderockz_woo_delivery_cloud_print_fields' );
		$this->loader->add_action( 'Zprinttemplatescustomer-htmlafterShippingDetails', $plugin_public, 'coderockz_woo_delivery_biz_print_fields', 10, 1 );
				
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Coderockz_Woo_Delivery_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
