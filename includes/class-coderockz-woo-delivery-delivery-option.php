<?php
require_once CODEROCKZ_WOO_DELIVERY_DIR . 'includes/class-coderockz-woo-delivery-helper.php';
if( !class_exists( 'Coderockz_Woo_Delivery_Delivery_Option' ) ) {

	class Coderockz_Woo_Delivery_Delivery_Option {

		public static function delivery_option($delivery_option_settings, $meta_box=null, $order_id=null) {
			
			$helper = new Coderockz_Woo_Delivery_Helper();
			
			$disable_delivery_facility = (isset($delivery_option_settings['disable_delivery_facility']) && !empty($delivery_option_settings['disable_delivery_facility'])) ? $delivery_option_settings['disable_delivery_facility'] : array();
			$disable_pickup_facility = (isset($delivery_option_settings['disable_pickup_facility']) && !empty($delivery_option_settings['disable_pickup_facility'])) ? $delivery_option_settings['disable_pickup_facility'] : array();

			$disable_delivery_facility_dates = (isset($delivery_option_settings['disable_delivery_facility_dates']) && !empty($delivery_option_settings['disable_delivery_facility_dates'])) ? $delivery_option_settings['disable_delivery_facility_dates'] : array();
			$disable_pickup_facility_dates = (isset($delivery_option_settings['disable_pickup_facility_dates']) && !empty($delivery_option_settings['disable_pickup_facility_dates'])) ? $delivery_option_settings['disable_pickup_facility_dates'] : array();

			$delivery_field_label = (isset($delivery_option_settings['delivery_label']) && !empty($delivery_option_settings['delivery_label'])) ? stripslashes($delivery_option_settings['delivery_label']) : __("Delivery", 'coderockz-woo-delivery');
			$pickup_field_label = (isset($delivery_option_settings['pickup_label']) && !empty($delivery_option_settings['pickup_label'])) ? stripslashes($delivery_option_settings['pickup_label']) : __("Pickup", 'coderockz-woo-delivery');

			$current_week_day = wp_date("w");

			$today = wp_date('Y-m-d',current_time( 'timestamp', 1 ));

			$delivery_option = [];

			


			$enable_delivery_restriction = (isset($delivery_option_settings['enable_delivery_restriction']) && !empty($delivery_option_settings['enable_delivery_restriction'])) ? $delivery_option_settings['enable_delivery_restriction'] : false;
			$minimum_amount = (isset($delivery_option_settings['minimum_amount_cart_restriction']) && $delivery_option_settings['minimum_amount_cart_restriction'] != "") ? (float)$delivery_option_settings['minimum_amount_cart_restriction'] : "";


			$enable_pickup_restriction = (isset($delivery_option_settings['enable_pickup_restriction']) && !empty($delivery_option_settings['enable_pickup_restriction'])) ? $delivery_option_settings['enable_pickup_restriction'] : false;
			$minimum_amount_pickup = (isset($delivery_option_settings['minimum_amount_cart_restriction_pickup']) && $delivery_option_settings['minimum_amount_cart_restriction_pickup'] != "") ? (float)$delivery_option_settings['minimum_amount_cart_restriction_pickup'] : "";


			if(is_null($meta_box)){
				$cart_total = $helper->cart_total();
				$delivery_option[''] = '';
			} else {
				$cart_total = $helper->order_cart_total($order_id);
			}

			if( $enable_delivery_restriction && $minimum_amount != "" && $cart_total['delivery'] < $minimum_amount){
		    	$hide_delivery = true;
		    } else {
		    	$hide_delivery = false;
		    }

		    if( $enable_pickup_restriction && $minimum_amount_pickup != "" && $cart_total['pickup'] < $minimum_amount_pickup){
		    	$hide_pickup = true;
		    } else {
		    	$hide_pickup = false;
		    }

		    if(is_null($meta_box)){
				$checkout_product_categories = $helper->checkout_product_categories(true);
				$checkout_products = $helper->checkout_product_id();
			} else {
				//global $post;
				$order = wc_get_order( $order_id );
				$order_items = $order->get_items();
				$checkout_product_categories = $helper->order_product_categories($order_items,true);
				$checkout_products = $helper->order_product_id($order_items);
			}


			$restrict_delivery_categories = [];

			$restrict_delivery_categories_array = (isset($delivery_option_settings['restrict_delivery_categories']) && !empty($delivery_option_settings['restrict_delivery_categories'])) ? $delivery_option_settings['restrict_delivery_categories'] : array();

			foreach($restrict_delivery_categories_array as $restrict_delivery_category) {
				$restrict_delivery_categories [] = stripslashes($restrict_delivery_category);
			}

			$restrict_delivery_products = (isset($delivery_option_settings['restrict_delivery_products']) && !empty($delivery_option_settings['restrict_delivery_products'])) ? $delivery_option_settings['restrict_delivery_products'] : array();

			$hide_categories_condition = (count(array_intersect($checkout_product_categories, $restrict_delivery_categories)) <= count($checkout_product_categories)) && count(array_intersect($checkout_product_categories, $restrict_delivery_categories))>0;

			$hide_products_condition = (count(array_intersect($checkout_products, $restrict_delivery_products)) <= count($checkout_products)) && count(array_intersect($checkout_products, $restrict_delivery_products))>0;

			if($hide_categories_condition) {
				$hide_for_product_categories = true;
				if($delivery_option_settings['restrict_delivery_reverse_current_condition'] && count($checkout_product_categories) > 1 && count($checkout_products) > 1 && count(array_diff($checkout_product_categories, $restrict_delivery_categories))>0) {
	  				$hide_for_product_categories = !$hide_for_product_categories;
	  			}
			} elseif($hide_products_condition) {
				$hide_for_product_categories = true;
				if($delivery_option_settings['restrict_delivery_reverse_current_condition'] && count($checkout_products) > 1 && count(array_diff($checkout_products, $restrict_delivery_products))>0) {
	  				$hide_for_product_categories = !$hide_for_product_categories;
	  			}
			} else {
				$hide_for_product_categories = false;
			}




			if(!in_array($today, $disable_delivery_facility_dates) && !in_array($current_week_day, $disable_delivery_facility) && !$hide_delivery && !$hide_for_product_categories) {
				$delivery_option['delivery'] = __( $delivery_field_label, 'coderockz-woo-delivery' );
			}

			$restrict_pickup_categories = [];

			$restrict_pickup_categories_array = (isset($delivery_option_settings['restrict_pickup_categories']) && !empty($delivery_option_settings['restrict_pickup_categories'])) ? $delivery_option_settings['restrict_pickup_categories'] : array();

			foreach($restrict_pickup_categories_array as $restrict_pickup_category) {
				$restrict_pickup_categories [] = stripslashes($restrict_pickup_category);
			}

			$restrict_pickup_products = (isset($delivery_option_settings['restrict_pickup_products']) && !empty($delivery_option_settings['restrict_pickup_products'])) ? $delivery_option_settings['restrict_pickup_products'] : array();

			$hide_pickup_categories_condition = (count(array_intersect($checkout_product_categories, $restrict_pickup_categories)) <= count($checkout_product_categories)) && count(array_intersect($checkout_product_categories, $restrict_pickup_categories))>0;

			$hide_pickup_products_condition = (count(array_intersect($checkout_products, $restrict_pickup_products)) <= count($checkout_products)) && count(array_intersect($checkout_products, $restrict_pickup_products))>0;

			if($hide_pickup_categories_condition) {
				$hide_for_pickup_product_categories = true;
				if($delivery_option_settings['restrict_pickup_reverse_current_condition'] && count($checkout_product_categories) > 1 && count($checkout_products) > 1) {
	  				$hide_for_pickup_product_categories = !$hide_for_pickup_product_categories;
	  			}
			} elseif($hide_pickup_products_condition) {
				$hide_for_pickup_product_categories = true;
				if($delivery_option_settings['restrict_pickup_reverse_current_condition'] && count($checkout_products) > 1) {
	  				$hide_for_pickup_product_categories = !$hide_for_pickup_product_categories;
	  			}
			} else {
				$hide_for_pickup_product_categories = false;
			}



			if(!in_array($today, $disable_pickup_facility_dates) && !in_array($current_week_day, $disable_pickup_facility) && !$hide_pickup && !$hide_for_pickup_product_categories) {
				$delivery_option['pickup'] = __( $pickup_field_label, 'coderockz-woo-delivery' );
			}
			
			return $delivery_option;
		}
	}
}