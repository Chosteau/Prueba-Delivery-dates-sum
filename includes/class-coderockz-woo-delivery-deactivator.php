<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://coderockz.com
 * @since      1.0.0
 *
 * @package    Coderockz_Woo_Delivery
 * @subpackage Coderockz_Woo_Delivery/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Coderockz_Woo_Delivery
 * @subpackage Coderockz_Woo_Delivery/includes
 * @author     CodeRockz <admin@coderockz.com>
 */
class Coderockz_Woo_Delivery_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$htaccess = get_home_path().'.htaccess';
		insert_with_markers($htaccess,'increase max execution time','');
	}

}
