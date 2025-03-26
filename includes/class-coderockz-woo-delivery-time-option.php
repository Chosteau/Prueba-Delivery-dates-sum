<?php

require_once CODEROCKZ_WOO_DELIVERY_DIR . 'includes/class-coderockz-woo-delivery-helper.php';

if( !class_exists( 'Coderockz_Woo_Delivery_Time_Option' ) ) {
	
	class Coderockz_Woo_Delivery_Time_Option {
		
		public static function delivery_time_option($delivery_time_settings, $meta_box=null, $order_id=null) {
			
			$helper = new Coderockz_Woo_Delivery_Helper();

			$currency_symbol = get_woocommerce_currency_symbol();
			
			$start = (isset($delivery_time_settings['delivery_time_starts']) && !empty($delivery_time_settings['delivery_time_starts'])) ? $delivery_time_settings['delivery_time_starts'] : "0";
			$end = (isset($delivery_time_settings['delivery_time_ends']) && !empty($delivery_time_settings['delivery_time_ends'])) ? $delivery_time_settings['delivery_time_ends'] : "1440";
			$time_slot = (isset($delivery_time_settings['each_time_slot']) && !empty($delivery_time_settings['each_time_slot'])) ? $delivery_time_settings['each_time_slot'] : (int)$end-(int)$start;

			$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');

			$enable_time_slot_fee = (isset($delivery_fee_settings['enable_time_slot_fee']) && !empty($delivery_fee_settings['enable_time_slot_fee'])) ? $delivery_fee_settings['enable_time_slot_fee'] : false;

			$enable_conditional_delivery_fee = isset($delivery_fee_settings['enable_conditional_delivery_fee']) && !empty($delivery_fee_settings['enable_conditional_delivery_fee']) ? $delivery_fee_settings['enable_conditional_delivery_fee'] : false;

			$time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format'])) ? $delivery_time_settings['time_format'] : "12";
			if($time_format == 12) {
				$time_format = "h:i A";
			}
			elseif ($time_format == 24) {
				$time_format = "H:i";
			}

			$enable_as_soon_as_possible_option = (isset($delivery_time_settings['enable_as_soon_as_possible_option']) && !empty($delivery_time_settings['enable_as_soon_as_possible_option'])) ? $delivery_time_settings['enable_as_soon_as_possible_option'] : false;

			$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : __("As Soon As Possible", 'coderockz-woo-delivery');


			$other_settings = get_option('coderockz_woo_delivery_other_settings');
			$add_tax_delivery_pickup_fee = (isset($other_settings['add_tax_delivery_pickup_fee']) && !empty($other_settings['add_tax_delivery_pickup_fee'])) ? $other_settings['add_tax_delivery_pickup_fee'] : false;
			$shipping_tax_class = (isset($other_settings['shipping_tax_class']) && !empty($other_settings['shipping_tax_class'])) ? $other_settings['shipping_tax_class'] : "";
			if($add_tax_delivery_pickup_fee && get_option('woocommerce_calc_taxes') == 'yes') {
				foreach(WC_Tax::get_rates($shipping_tax_class) as $tax_rate) {
					$tax_rate_percentage_detect = $tax_rate['rate'];
				}
			} else {
				$tax_rate_percentage_detect = "";
			}

			$result = [];


			$it = $end;
			if(($end-$start)%$time_slot !=0){
				$remaining_time = ($end-$start)%$time_slot;
				$it = $end-$remaining_time;
				$fractional_from_hour = date($time_format, mktime(0, (int)$it));
				if($time_format == "H:i" && $end == 1440){
					$fractional_to_hour = "24:00";
				} else {
					$fractional_to_hour = date($time_format, mktime(0, (int)$end));
				}
				$result[date("H:i", mktime(0, (int)$it)) . ' - ' . date("H:i", mktime(0, (int)$end))] = $fractional_from_hour . ' - ' . $fractional_to_hour;
							
			}
			while($it > $start) {
				$to = $it;
				$from = $it - $time_slot;
				$from_hour = date($time_format, mktime(0, (int)$from));
				if($time_format == "H:i" && $to == 1440){
					$to_hour = "24:00";
				} else {
					$to_hour = date($time_format, mktime(0, (int)$to));
				}
				$result[date("H:i", mktime(0, (int)$from)) . ' - ' . date("H:i", mktime(0, (int)$to))] = $from_hour . ' - ' . $to_hour;
				
				$it = $from;
			}
			$new_result = [];
			$custom_result = [];
			if(is_null($meta_box)){
				$result[''] = '';
				$new_result[''] = '';
				$custom_result[''] = '';
			}

			if($enable_as_soon_as_possible_option) {
				$result["as-soon-as-possible"] = __($as_soon_as_possible_text, 'coderockz-woo-delivery');
			}

			$current_time = (wp_date("G")*60)+wp_date("i");


			if($enable_conditional_delivery_fee && (isset($delivery_fee_settings['conditional_delivery_time_starts']) && ($delivery_fee_settings['conditional_delivery_time_starts'] !='' || $delivery_fee_settings['conditional_delivery_time_starts'] == 0)) && (isset($delivery_fee_settings['conditional_delivery_time_ends']) && $delivery_fee_settings['conditional_delivery_time_ends'] !='') && ($current_time >= (int)$delivery_fee_settings['conditional_delivery_time_starts'] && (int)$delivery_fee_settings['conditional_delivery_time_ends'] >= $current_time) && (isset($delivery_fee_settings['conditional_delivery_fee_duration']) && !empty($delivery_fee_settings['conditional_delivery_fee_duration']))) {

				$duration = ""; 
        		$identity = __("Minutes", 'coderockz-woo-delivery');
    			if(isset($delivery_fee_settings['conditional_delivery_fee_duration']) && !empty($delivery_fee_settings['conditional_delivery_fee_duration'])) {
    				$conditional_delivery_fee_duration = (int)$delivery_fee_settings['conditional_delivery_fee_duration'];
    				if($conditional_delivery_fee_duration <= 59) {
    					$duration = $conditional_delivery_fee_duration;
    				} else {
    					$conditional_delivery_fee_duration = $conditional_delivery_fee_duration/60;
    					if($helper->containsDecimal($conditional_delivery_fee_duration)){
    						$duration = $conditional_delivery_fee_duration*60;
    						$identity = __("Minutes", 'coderockz-woo-delivery');
    					} else {
    						$duration = $conditional_delivery_fee_duration;
    						$identity = __("Hour", 'coderockz-woo-delivery');
    					}
    				}
    			}

    			$conditional_delivery_fee = isset($delivery_fee_settings['conditional_delivery_fee']) && !empty($delivery_fee_settings['conditional_delivery_fee']) ? esc_attr($delivery_fee_settings['conditional_delivery_fee']) : "";
		    	$conditional_delivery_dropdown_text = isset($delivery_fee_settings['conditional_delivery_dropdown_text']) && !empty($delivery_fee_settings['conditional_delivery_dropdown_text']) ? __(stripslashes(esc_attr($delivery_fee_settings['conditional_delivery_dropdown_text'])), 'coderockz-woo-delivery') : __("Delivery within ", 'coderockz-woo-delivery').$duration." ".$identity;

				$result["conditional-delivery"] = $conditional_delivery_dropdown_text;
			}
			
			$result = array_reverse($result);
						
			$custom_time_slot_settings = get_option('coderockz_woo_delivery_time_slot_settings');
			$enable_custom_time_slot = (isset($custom_time_slot_settings['enable_custom_time_slot']) && !empty($custom_time_slot_settings['enable_custom_time_slot'])) ? $custom_time_slot_settings['enable_custom_time_slot'] : false;

			if($enable_custom_time_slot) {
				if(isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0){

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

					$sorted_custom_slot = $helper->array_sort_by_column($custom_time_slot_settings['time_slot'],'start');

					if($enable_as_soon_as_possible_option) {
						$custom_result["as-soon-as-possible"] = __($as_soon_as_possible_text, 'coderockz-woo-delivery');
					}

					if($enable_conditional_delivery_fee && (isset($delivery_fee_settings['conditional_delivery_time_starts']) && ($delivery_fee_settings['conditional_delivery_time_starts'] !='' || $delivery_fee_settings['conditional_delivery_time_starts'] == 0)) && (isset($delivery_fee_settings['conditional_delivery_time_ends']) && $delivery_fee_settings['conditional_delivery_time_ends'] !='') && ($current_time >= (int)$delivery_fee_settings['conditional_delivery_time_starts'] && (int)$delivery_fee_settings['conditional_delivery_time_ends'] >= $current_time) && (isset($delivery_fee_settings['conditional_delivery_fee_duration']) && !empty($delivery_fee_settings['conditional_delivery_fee_duration']))) {

						$duration = ""; 
		        		$identity = __("Minutes", 'coderockz-woo-delivery');
		    			if(isset($delivery_fee_settings['conditional_delivery_fee_duration']) && !empty($delivery_fee_settings['conditional_delivery_fee_duration'])) {
		    				$conditional_delivery_fee_duration = (int)$delivery_fee_settings['conditional_delivery_fee_duration'];
		    				if($conditional_delivery_fee_duration <= 59) {
		    					$duration = $conditional_delivery_fee_duration;
		    				} else {
		    					$conditional_delivery_fee_duration = $conditional_delivery_fee_duration/60;
		    					if($helper->containsDecimal($conditional_delivery_fee_duration)){
		    						$duration = $conditional_delivery_fee_duration*60;
		    						$identity = __("Minutes", 'coderockz-woo-delivery');
		    					} else {
		    						$duration = $conditional_delivery_fee_duration;
		    						$identity = __("Hour", 'coderockz-woo-delivery');
		    					}
		    				}
		    			}
		    			$conditional_delivery_fee = isset($delivery_fee_settings['conditional_delivery_fee']) && !empty($delivery_fee_settings['conditional_delivery_fee']) ? esc_attr($delivery_fee_settings['conditional_delivery_fee']) : "";
		    			$conditional_delivery_dropdown_text = isset($delivery_fee_settings['conditional_delivery_dropdown_text']) && !empty($delivery_fee_settings['conditional_delivery_dropdown_text']) ? __(stripslashes(esc_attr($delivery_fee_settings['conditional_delivery_dropdown_text'])), 'coderockz-woo-delivery') : __("Delivery within ", 'coderockz-woo-delivery').$duration." ".$identity;

						$custom_result["conditional-delivery"] = $conditional_delivery_dropdown_text;
					}

			  		foreach($sorted_custom_slot as $individual_time_slot) {
			  			 
			  			if($individual_time_slot['enable'] ) {

			  				$timeslot_name = (isset($individual_time_slot['name']) && !empty($individual_time_slot['name'])) ? stripslashes($individual_time_slot['name']) ." || " : "";
			  				
			  				$timeslot_hide_categories = [];

			  				$timeslot_hide_categories_array = (isset($individual_time_slot['hide_categories']) && !empty($individual_time_slot['hide_categories'])) ? $individual_time_slot['hide_categories'] : array();

			  				foreach($timeslot_hide_categories_array as $timeslot_hide_category) {
			  					$timeslot_hide_categories [] = stripslashes($timeslot_hide_category);
			  				}

			  				$checkout_product_categories = [];
			  				if(!empty($timeslot_hide_categories)) {
								if(is_null($meta_box)){
									$checkout_product_categories = $helper->checkout_product_categories_opendays_exclusion_condition($timeslot_hide_categories, true);
								} else {
									$checkout_product_categories = $helper->order_product_categories_opendays_exclusion_condition($order_items,$timeslot_hide_categories, true);

								}
							}

			  				$timeslot_hide_products = (isset($individual_time_slot['hide_products']) && !empty($individual_time_slot['hide_products'])) ? $individual_time_slot['hide_products'] : array();

			  				$hide_categories_condition = (count(array_intersect($checkout_product_categories, $timeslot_hide_categories)) <= count($checkout_product_categories)) && count(array_intersect($checkout_product_categories, $timeslot_hide_categories))>0;
  			
				  			$hide_products_condition = (count(array_intersect($checkout_products, $timeslot_hide_products)) <= count($checkout_products)) && count(array_intersect($checkout_products, $timeslot_hide_products))>0;

							if($hide_categories_condition) {
								$hide_for_product_categories = true;
								if($individual_time_slot['time_slot_shown_other_categories_products'] && count($checkout_product_categories) > 1 && count($checkout_products) > 1 && count(array_diff($checkout_product_categories, $timeslot_hide_categories))>0) {
					  				$hide_for_product_categories = !$hide_for_product_categories;
					  			}
							} elseif($hide_products_condition) {
								$hide_for_product_categories = true;
								if($individual_time_slot['time_slot_shown_other_categories_products'] && count($checkout_products) > 1 && count(array_diff($checkout_products, $timeslot_hide_products))>0) {
					  				$hide_for_product_categories = !$hide_for_product_categories;
					  			}
							} else {
								$hide_for_product_categories = false;
							}

				  			if(!$hide_for_product_categories) {

			  				$temp_custom_result = [];

			  				if($tax_rate_percentage_detect != "") {		  					
			  					$fee_with_tax = round((float)$individual_time_slot['fee'] + (((float)$individual_time_slot['fee'] * $tax_rate_percentage_detect)/100), wc_get_price_decimals());
			  				} else {
			  					$fee_with_tax = $individual_time_slot['fee'];
			  				}
			  				
			  				if(class_exists('WOOCS_STARTER')) {
								global $WOOCS;
                            	$currencies=$WOOCS->get_currencies();
                            	$currency_symbol = $currencies[$WOOCS->current_currency]['symbol'];
								$fee = (isset($individual_time_slot['fee']) && $individual_time_slot['fee'] != "") ? " (+".$helper->postion_currency_symbol($currency_symbol,apply_filters('woocs_exchange_value', $fee_with_tax)).")" :"";
							} else {
								$fee = (isset($individual_time_slot['fee']) && $individual_time_slot['fee'] != "") ? " (+".$helper->postion_currency_symbol($currency_symbol,$fee_with_tax).")" :"";
							}
			  				$from = $individual_time_slot["start"];
			  				$to = $individual_time_slot["end"];

			  				if($individual_time_slot['enable_split']) {
			  					$split_time_slot = (isset($individual_time_slot['split_slot_duration']) && !empty($individual_time_slot['split_slot_duration'])) ? $individual_time_slot['split_slot_duration'] : "";
			  					
			  					
				  					/*if($individual_time_slot['enable_single_splited_slot']) {
										$temp_custom_result["$to,"] = date($time_format, mktime(0, $to)) . $fee;
									}*/

									$it = $to;
									if(($to-$from)%$split_time_slot !=0){

										$remaining_time = ($to-$from)%$split_time_slot;
										$it = $to-$remaining_time;
										$fractional_from_hour = date($time_format, mktime(0, (int)$it));
										
										if($time_format == "H:i" && $to == 1440){
											$fractional_to_hour = "24:00";
										} else {
											$fractional_to_hour = date($time_format, mktime(0, (int)$to));
										}

										if($individual_time_slot['enable_single_splited_slot']) {
											$temp_custom_result[date("H:i", mktime(0, (int)$it))] = $timeslot_name.$fractional_from_hour . $fee;
										} else {

											$temp_custom_result[date("H:i", mktime(0, (int)$it)) . ' - ' . date("H:i", mktime(0, (int)$to))] = $timeslot_name.$fractional_from_hour . ' - ' . $fractional_to_hour . $fee;
										}

										
													
									}
									while($it > $from) {
										$end = $it;
										$start = $it - $split_time_slot;
										$from_hour = date($time_format, mktime(0, (int)$start));
										if($time_format == "H:i" && $end == 1440){
											$to_hour = "24:00";
										} else {
											$to_hour = date($time_format, mktime(0, (int)$end));
										}
										if($individual_time_slot['enable_single_splited_slot']) {
											$temp_custom_result[date("H:i", mktime(0, (int)$start))] = $timeslot_name.$from_hour . $fee;
										} else {
											$temp_custom_result[date("H:i", mktime(0, (int)$start)) . ' - ' . date("H:i", mktime(0, (int)$end))] = $timeslot_name.$from_hour . ' - ' . $to_hour . $fee;
										}
										$it = $start;

									}	 

									$temp_custom_result = array_reverse($temp_custom_result);
									$custom_result = array_merge($custom_result,$temp_custom_result);

							
			  				} else {

			  					if($individual_time_slot['enable_single_slot']) {
									$custom_result[date("H:i", mktime(0, (int)$individual_time_slot['start']))] = $timeslot_name.date($time_format, mktime(0, (int)$individual_time_slot['start'])) . $fee;
								} else {

									if($time_format == "H:i" && $to == 1440){
										$custom_result[date("H:i", mktime(0, (int)$individual_time_slot['start'])) . ' - ' . date("H:i", mktime(0, (int)$individual_time_slot['end']))] = $timeslot_name.date($time_format, mktime(0, (int)$individual_time_slot['start'])) . ' - ' . "24:00" . $fee;
									} else {
										$custom_result[date("H:i", mktime(0, (int)$individual_time_slot['start'])) . ' - ' . date("H:i", mktime(0, (int)$individual_time_slot['end']))] = $timeslot_name.date($time_format, mktime(0, (int)$individual_time_slot['start'])) . ' - ' . date($time_format, mktime(0, (int)$individual_time_slot['end'])) . $fee;
									}
									
								}

			  				}

			  			}

			  			}

			  		}
			  	}

			  	ksort($custom_result);
			  	if(is_null($meta_box)) {
					array_shift($custom_result);
				}
				if(key(array_slice($custom_result, -1, 1, true)) == "conditional-delivery") {
					$custom_result = array_merge(array_splice($custom_result, -1), $custom_result);	
				}
				if(key(array_slice($custom_result, -1, 1, true)) == "as-soon-as-possible") {
					$custom_result = array_merge(array_splice($custom_result, -1), $custom_result);
				}
				if(is_null($meta_box)) {
					$custom_result = array_merge(array('' => ''), $custom_result);
				}
			  	return $custom_result;
			}

			if($enable_time_slot_fee) {

				$slot_fees = $delivery_fee_settings['time_slot_fee'];

				foreach($result as $key => $value) {
					
					if($key != "") {
						$slot_key = "";
						if($key != "as-soon-as-possible" && $key != "" && $key != "conditional-delivery"){
							$slot_key = explode(' - ', $key);
							$slot_key_one = explode(':', $slot_key[0]);
							$slot_key_two = explode(':', $slot_key[1]);
							$slot_key = ((int)$slot_key_one[0]*60+(int)$slot_key_one[1]).'-'.((int)( $slot_key_two[0] == "00" || $slot_key_two[0] == "0" ? 24 : $slot_key_two[0] )*60+(int)$slot_key_two[1]);
						}

						if(isset($slot_fees[$slot_key])) {
							if($slot_fees[$slot_key] > 0) {
								if($tax_rate_percentage_detect != "") {		  					
				  					$fee_with_tax = round((float)$slot_fees[$slot_key] + (((float)$slot_fees[$slot_key] * $tax_rate_percentage_detect)/100), wc_get_price_decimals());
				  				} else {
				  					$fee_with_tax = $slot_fees[$slot_key];
				  				}

								if(class_exists('WOOCS_STARTER')){
									global $WOOCS;
	                            	$currencies=$WOOCS->get_currencies();
	                            	$currency_symbol = $currencies[$WOOCS->current_currency]['symbol'];
									$fee = " (+".$helper->postion_currency_symbol($currency_symbol,apply_filters('woocs_exchange_value', $fee_with_tax)).")";
								} else {
									$fee = " (+". $helper->postion_currency_symbol($currency_symbol,$fee_with_tax) . ")";
								}

								$value = $value . $fee;
							}
						}
						$new_result[$key] = $value;
					}

				}
				return $new_result;
			}
			return $result;
		}
	}
}