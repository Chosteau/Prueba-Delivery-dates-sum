<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Coderockz_Woo_Delivery_Licensing_Manager' ) ) {
    class Coderockz_Woo_Delivery_Licensing_Manager {
        
        private $vars;

        public function __construct() {
            $this->vars = array (
                // The plugin file, if this array is defined in the plugin
                'plugin_file' => __FILE__,

                // The current version of the plugin.
                // Also need to change in readme.txt and plugin header.
                'version' => '1.0.0',

                // The main URL of your store for license verification
                'store_url' => 'https://coderockz.com',

                // Your name
                'author' => 'CodeRockz',

                // The URL to renew or purchase a license
                'purchase_url' => 'https://coderockz.com/downloads/woocommerce-delivery-date-time-wordpress-plugin/',

                // The URL of your contact page
                'contact_url' => 'https://coderockz.com/support',

                // This should match the download name exactly
                'item_name' => 'WooCommerce Delivery Date Time Plugin (Woo Delivery)',

                // The option names to store the license key and activation status
                'license_key' => 'coderockz-woo-delivery-license-key',
                'license_status' => 'coderockz-woo-delivery-license-status',
                'transient_set' => 'cwd-transient-status',

                // Option group param for the settings api
                'option_group' => 'coderockz-woo-delivery-license',

                // The plugin settings admin page slug
                'admin_page_slug' => 'coderockz-woo-delivery-settings',

                // If using add_menu_page, this is the parent slug to add a submenu item underneath.
                // Otherwise we'll add our own parent menu item.
                'parent_menu_slug' => '',

                // The translatable title of the plugin
                'plugin_title' => __( 'WooCommerce Delivery Date Time', 'coderockz-woo-delivery' ),

                // Title of the settings page with activation key
                'settings_page_title' => __( 'Settings', 'coderockz-woo-delivery' ),

                // If this plugin depends on another plugin to be installed,
                // we can either check that a class exists or plugin is active.
                // Only one is needed.
                'dependent_class_to_check' => 'WooCommerce', // name of class to verify...
                'dependent_plugin' => 'WooCommerce', // ...or plugin name for is_plugin_active() call
                'dependent_plugin_title' => __( 'WooCommerce', 'coderockz-woo-delivery' ),
            );

            /*add_action( 'admin_menu', array( $this, 'license_menu' ), 99 );*/
            add_action( 'plugins_loaded', array( $this, 'coderockz_woo_delivery_license_notice' ) );
            add_action( 'admin_init', array( $this, 'register_option' ) );
            add_action( 'admin_init', array( $this, 'activate_license' ) );
            add_action( 'admin_init', array( $this, 'deactivate_license' ) );
            add_action( 'admin_init', array( $this, 'check_license' ) );
            add_action( 'admin_notices', array( $this, 'coderockz_woo_delivery_license_admin_notices' ) );
        }

        public function get_var( $var ) {
            if ( isset( $this->vars[ $var ] ) )
                return $this->vars[ $var ];
            return false;
        }

        /**
         * Show an error message that license needs to be activated
         */
        public function coderockz_woo_delivery_license_notice() {
            if ( 'valid' != get_option( $this->get_var( 'license_status' ) ) ) {
                if ( ( ! isset( $_GET['page'] ) or $this->get_var( 'admin_page_slug' ) != $_GET['page'] ) ) {
                    add_action( 'admin_notices', function() {
                        echo '<div class="error"><img style="width:50px;margin-top:5px;" src="'.CODEROCKZ_WOO_DELIVERY_URL.'admin/images/woo-delivery-logo.png" alt="woocommerce-delivery-date-time"><p style="display:inline-block;vertical-align: top;margin-top: 15px;margin-left:5px;font-weight: 600;">' .
                             sprintf( __( '%s license needs to be activated. %sActivate Now%s', 'coderockz-woo-delivery' ), $this->get_var( 'item_name' ), '<a href="' . admin_url( 'admin.php?page=' . $this->get_var( 'admin_page_slug' ) ) . '">', '</a>' ) .
                             '</p></div>';
                    } );
                }
            }

            /**
             * If your plugin depends on another plugin, adds a condition to verify
             * if that plugin is installed.
             */
            if ( ( $this->get_var( 'dependent_class_to_check' ) and ! class_exists( $this->get_var( 'dependent_class_to_check' ) ) ) ) {
                add_action( 'admin_notices', function() {
                    echo '<div class="error"><img style="width:50px;margin-top:5px;" src="'.CODEROCKZ_WOO_DELIVERY_URL.'admin/images/woo-delivery-logo.png" alt="woocommerce-delivery-date-time"><p style="display:inline-block;vertical-align: top;margin-top: 15px;margin-left:5px;font-weight: 600;">' .
                         sprintf( __( 'The %s plugin requires %s to be installed and activated', 'coderockz-woo-delivery' ), $this->get_var( 'plugin_title' ), $this->get_var( 'dependent_plugin_title' ) ) .
                         '</p></div>';
                } );
            }

        }

        public function register_option() {
            // creates our settings in the options table
            register_setting( $this->get_var( 'option_group' ), $this->get_var( 'license_key' ), array( $this, 'sanitize_license' ) );
        }

        public function sanitize_license( $new ) {
            $old = get_option( $this->get_var( 'license_key' ) );
            if ( $old && $old != $new ) {
                delete_option( $this->get_var( 'license_status' ) ); // new license has been entered, so must reactivate
            }
            return $new;
        }

        public function activate_license() {
            // listen for our activate button to be clicked
            if ( isset( $_POST[ $this->get_var( 'option_group' ) . '_activate' ] ) ) {
                // run a quick security check
                if ( ! check_admin_referer( $this->get_var( 'option_group' ) . '_nonce', $this->get_var( 'option_group' ) . '_nonce' ) )
                    return; // get out if we didn't click the Activate button

                // save the license key to the database
                update_option( $this->get_var( 'license_key' ), trim($_POST[$this->get_var( 'license_key' )]) );

                // retrieve the license from the database
                $license = trim( get_option( $this->get_var( 'license_key' ) ) );

                // data to send in our API request
                $api_params = array(
                    'edd_action'=> 'activate_license',
                    'license'   => $license,
                    //'item_name' => urlencode( $this->get_var( 'item_name' ) ), // the name of our product in EDD
                    'item_id' => 9630,
                    'url'       => home_url()
                );

                // Call the custom API.
                $response = wp_remote_post( $this->get_var( 'store_url' ), array( 'timeout' => 45, 'sslverify' => false, 'body' => $api_params ) );

                if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

                    if ( is_wp_error( $response ) ) {
                        $message = $response->get_error_message();
                    } else {
                        $message = __( 'An error occurred, please try again.' );
                    }

                } else {

                    $license_data = json_decode( wp_remote_retrieve_body( $response ) );


                    if ( $license_data->license != 'valid' ) {
                        delete_option( $this->get_var( 'license_status' ) );
                    }

                    // Set to check again in 12 hours
                    set_transient(
                        $this->get_var( 'transient_set' ) . '_checking',
                        $license_data,
                        ( 60 * 60 * 12 )
                    );


                    if ( false === $license_data->success ) {

                        switch( $license_data->error ) {

                            case 'expired' :

                                $message = sprintf(
                                    __( 'Your license key expired on %s.' ),
                                    date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                                );
                                break;

                            case 'disabled' :
                            case 'revoked' :

                                $message = __( 'Your license key has been disabled.' );
                                break;

                            case 'missing' :

                                $message = sprintf( __( 'This appears to be an invalid license key for %s.' ), urlencode( $this->get_var( 'item_name' ) ) );
                                break;

                            case 'invalid' :
                            case 'site_inactive' :

                                $message = __( 'Your license is not active for this URL.' );
                                break;

                            case 'item_name_mismatch' :

                                $message = sprintf( __( 'This appears to be an invalid license key for %s.' ), urlencode( $this->get_var( 'item_name' ) ) );
                                break;

                            case 'no_activations_left':

                                $message = __( 'Your license key has reached its activation limit.' );
                                break;

                            default :

                                $message = __( 'An error occurred, please try again.' );
                                break;
                        }

                    }

                }

                
                // Check if anything passed on a message constituting a failure
                if ( ! empty( $message ) ) {
                    $base_url = admin_url( 'admin.php?page=coderockz-woo-delivery-settings' );
                    $redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

                    wp_redirect( $redirect );
                    exit();
                }

                // $license_data->license will be either "valid" or "invalid"
                update_option( $this->get_var( 'license_status' ), $license_data->license );
                wp_redirect( admin_url( 'admin.php?page=coderockz-woo-delivery-settings' ) );
                exit();


                /*// make sure the response came back okay
                if ( is_wp_error( $response ) ) {
                    add_settings_error(
                        $this->get_var( 'option_group' ),
                        'activate',
                        __( 'There was an error activating the license, please verify your license is correct and try again or contact support.', 'coderockz-woo-delivery' )
                    );
                    return false;
                }

                // decode the license data
                $license_data = json_decode( wp_remote_retrieve_body( $response ) );

                // $license_data->license will be either "valid" or "invalid"
                update_option( $this->get_var( 'license_status' ), $license_data->license );
                if ( 'valid' != $license_data->license ) {
                    add_settings_error(
                        $this->get_var( 'option_group' ),
                        'activate',
                        __( 'There was an error activating the license, please verify your license is correct and try again or contact support.', 'coderockz-woo-delivery' )
                    );
                }*/
            }
        }

        public function deactivate_license() {
            // listen for our activate button to be clicked
            if ( isset( $_POST[ $this->get_var( 'option_group' ) . '_deactivate'] ) ) {
                // run a quick security check
                if( ! check_admin_referer( $this->get_var( 'option_group' ) . '_nonce', $this->get_var( 'option_group' ) . '_nonce' ) )
                    return; // get out if we didn't click the Activate button

                // retrieve the license from the database
                $license = trim( get_option( $this->get_var( 'license_key' ) ) );

                // data to send in our API request
                $api_params = array(
                    'edd_action'=> 'deactivate_license',
                    'license'   => $license,
                    //'item_name' => urlencode( $this->get_var( 'item_name' ) ), // the name of our product in EDD
                    'item_id' => 9630,
                    'url'       => home_url()
                );

                // Call the custom API.
                $response = wp_remote_post( $this->get_var( 'store_url' ), array( 'timeout' => 45, 'sslverify' => false, 'body' => $api_params ) );

                /*// make sure the response came back okay
                if ( is_wp_error( $response ) ) {
                    add_settings_error(
                        $this->get_var( 'option_group' ),
                        'deactivate',
                        __( 'There was an error deactivating the license, please try again or contact support.', 'coderockz-woo-delivery' )
                    );
                    return false;
                }*/

                // make sure the response came back okay
                if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

                    if ( is_wp_error( $response ) ) {
                        $message = $response->get_error_message();
                    } else {
                        $message = __( 'An error occurred, please try again.' );
                    }

                    $base_url = admin_url( 'admin.php?page=coderockz-woo-delivery-settings' );
                    $redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

                    wp_redirect( $redirect );
                    exit();
                }

                // decode the license data
                $license_data = json_decode( wp_remote_retrieve_body( $response ) );

                // $license_data->license will be either "deactivated" or "failed"
                if ( 'deactivated' == $license_data->license ) {
                    delete_option( $this->get_var( 'license_status' ) );
                }

                wp_redirect( admin_url( 'admin.php?page=coderockz-woo-delivery-settings' ) );
                exit();
            }
        }

        /**
         * This is a means of catching errors from the activation method above and displaying it to the customer
         */
        public function coderockz_woo_delivery_license_admin_notices() {
            if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

                switch( $_GET['sl_activation'] ) {

                    case 'false':
                        $message = urldecode( $_GET['message'] );
                        echo '<div class="error notice" style="margin: 15px 20px 0px 0px;"><img style="width:50px;margin-top:5px;" src="'.CODEROCKZ_WOO_DELIVERY_URL.'admin/images/woo-delivery-logo.png" alt="woocommerce-delivery-date-time"><p style="display:inline-block;vertical-align: top;margin-top: 15px;margin-left:5px;font-weight: 600;">' .$message.'</p></div>';
                        break;

                    case 'true':
                    default:
                        // Developers can put a custom success message here for when activation is successful if they way.
                        break;

                }
            }
        }


        public function check_license() {
            if ( get_transient( $this->get_var( 'transient_set' ) . '_checking' ) ) {
                return;
            }

            $license = trim( get_option( $this->get_var( 'license_key' ) ) );

            $api_params = array(
                'edd_action' => 'check_license',
                'license' => $license,
                //'item_name' => urlencode( $this->get_var( 'item_name' ) ),
                'item_id' => 9630,
                'url'       => home_url()
            );

            // Call the custom API.
            $response = wp_remote_post(
                $this->get_var( 'store_url' ),
                array(
                    'timeout' => 45,
                    'sslverify' => false,
                    'body' => $api_params
                )
            );

            if ( is_wp_error( $response ) )
                return false;

            $license_data = json_decode(
                wp_remote_retrieve_body( $response )
            );

            if ( $license_data->license != 'valid' ) {
                delete_option( $this->get_var( 'license_status' ) );
            }

            // Set to check again in 12 hours
            set_transient(
                $this->get_var( 'transient_set' ) . '_checking',
                $license_data,
                ( 60 * 60 * 12 )
            );
        }
    }

}