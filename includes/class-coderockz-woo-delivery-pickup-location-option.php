<?php

require_once CODEROCKZ_WOO_DELIVERY_DIR . 'includes/class-coderockz-woo-delivery-helper.php';

if( !class_exists( 'Coderockz_Woo_Delivery_Pickup_Location_Option' ) ) {
	
	class Coderockz_Woo_Delivery_Pickup_Location_Option {

		public static function pickup_location_option($pickup_location_settings, $meta_box=null, $order_id=null) {

			$helper = new Coderockz_Woo_Delivery_Helper();
			
			$pickup_locations = (isset($pickup_location_settings['pickup_location']) && !empty($pickup_location_settings['pickup_location'])) ? $pickup_location_settings['pickup_location'] : [];
			
			ksort($pickup_locations);
			$pickup_location = [];
		
			if(is_null($meta_box)){
				$pickup_location[''] = '';
			}
			
			if(!empty($pickup_locations)) {


				if(is_null($meta_box)){

				} else {
					//global $post;
					$order = wc_get_order( $order_id );
					$order_items = $order->get_items();

				}

				if(is_null($meta_box)){
					$checkout_products = $helper->checkout_product_id();
				} else {
					$checkout_products = $helper->order_product_id($order_items);
				}


				foreach($pickup_locations as $name => $location_settings) {
					
					if($location_settings['enable']) {


						$location_hide_categories = [];

		  				$location_hide_categories_array = (isset($location_settings['hide_categories']) && !empty($location_settings['hide_categories'])) ? $location_settings['hide_categories'] : array();

		  				foreach($location_hide_categories_array as $location_hide_category) {
		  					$location_hide_categories [] = stripslashes($location_hide_category);
		  				}

		  				$checkout_product_categories = [];
		  				if(!empty($location_hide_categories)) {
							if(is_null($meta_box)){
								$checkout_product_categories = $helper->checkout_product_categories_opendays_exclusion_condition($location_hide_categories, true);
							} else {
								$checkout_product_categories = $helper->order_product_categories_opendays_exclusion_condition($order_items,$location_hide_categories, true);

							}
						}


		  				$location_hide_products = (isset($location_settings['hide_products']) && !empty($location_settings['hide_products'])) ? $location_settings['hide_products'] : array();

		  				$hide_categories_condition = (count(array_intersect($checkout_product_categories, $location_hide_categories)) <= count($checkout_product_categories)) && count(array_intersect($checkout_product_categories, $location_hide_categories))>0;
			
			  			$hide_products_condition = (count(array_intersect($checkout_products, $location_hide_products)) <= count($checkout_products)) && count(array_intersect($checkout_products, $location_hide_products))>0;

						if($hide_categories_condition) {
							$hide_for_product_categories = true;
							if($location_settings['location_shown_other_categories_products'] && count($checkout_product_categories) > 1 && count($checkout_products) > 1 && count(array_diff($checkout_product_categories, $location_hide_categories))>0) {
				  				$hide_for_product_categories = !$hide_for_product_categories;
				  			}
						} elseif($hide_products_condition) {
							$hide_for_product_categories = true;
							if($location_settings['location_shown_other_categories_products'] && count($checkout_products) > 1 && count(array_diff($checkout_products, $location_hide_products))>0) {
				  				$hide_for_product_categories = !$hide_for_product_categories;
				  			}
						} else {
							$hide_for_product_categories = false;
						}


						if(!$hide_for_product_categories) {
							$pickup_location[stripslashes($name)] = stripslashes($name);
						}

						
					}
					
				}
			}

			return $pickup_location;
		}
	}
}