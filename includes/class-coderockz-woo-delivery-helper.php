<?php

if( !class_exists( 'Coderockz_Woo_Delivery_Helper' ) ) {

	class Coderockz_Woo_Delivery_Helper {

		public function coderockz_woo_delivery_array_sanitize($array) {
		    $newArray = array();
		    if (count($array)>0) {
		        foreach ($array as $key => $value) {
		            if (is_array($value)) {
		                foreach ($value as $key2 => $value2) {
		                    if (is_array($value2)) {
		                        foreach ($value2 as $key3 => $value3) {
		                            $newArray[$key][$key2][$key3] = sanitize_text_field($value3);
		                        }
		                    } else {
		                        $newArray[$key][$key2] = sanitize_text_field($value2);
		                    }
		                }
		            } else {
		                $newArray[$key] = sanitize_text_field($value);
		            }
		        }
		    }
		    return $newArray;
		}

		public function keyexistinstring($array, $string) {
			foreach($array as $key=>$value){

			  if (strpos($string, $key) !== false) {
			    return $value;
			  }
			}

			return [];
		}

		public function hex2rgb( $colour ) {
	        if ( $colour[0] == '#' ) {
	                $colour = substr( $colour, 1 );
	        }
	        if ( strlen( $colour ) == 6 ) {
	                list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
	        } elseif ( strlen( $colour ) == 3 ) {
	                list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
	        } else {
	                return false;
	        }
	        $r = hexdec( $r );
	        $g = hexdec( $g );
	        $b = hexdec( $b );
	        return array( 'red' => $r, 'green' => $g, 'blue' => $b );
		}

		public function containsDecimal( $value ) {
			$value = (string)$value;
		    if ( strpos( $value, "." ) !== false ) {
		        return true;
		    }
		    return false;
		}

		public function objectToArray($d) {
		    foreach($d as $key => $value) {
			    $d[$key] = (array) $value;
			}
			return $d;
		}

		public function get_date_from_range($start, $end, $format = 'Y-m-d') {
		    $array = array();
		    $interval = new DateInterval('P1D');

		    $realEnd = new DateTime($end);
		    $realEnd->add($interval);

		    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

		    foreach($period as $date) { 
		        $array[] = $date->format($format); 
		    }

		    return $array;
		}


		// Function to check string starting with given substring 
		public function starts_with ($string, $startString) { 
		    $len = strlen($startString); 
		    return (substr($string, 0, $len) === $startString); 
		}
		// Function to check whether a string starts with number and take the numeric value until non numeric character
		public function starts_with_starting_numeric($string) {
		  $length = strlen($string);   
		  for ($i = 0, $int = ''; $i < $length; $i++) {
		    if (is_numeric($string[$i]))
		        $int .= $string[$i];
		     else break;
		  }

		  return (int) $int;
		}

		public function detect_plugin_settings_page() {
			global $wp;  
			$current_url = home_url(add_query_arg(array($_GET), $wp->request));
			if (strpos($current_url, "coderockz-woo-delivery-settings")!==false){
			    return true;
			}
		}

		public function detect_delivery_calendar_page() {
			global $wp;  
			$current_url = home_url(add_query_arg(array($_GET), $wp->request));
			if (strpos($current_url, "coderockz-woo-delivery-delivery-calendar")!==false){
			    return true;
			}
		}

		public function currency_exchange_value( $currency_value ) {
	        if ( class_exists('WOOCS') ) {
	            $currency_value = apply_filters('woocs_exchange_value', $currency_value);
	        }
	       
	        return $currency_value;
	    }


	    public function wp_strtotime($str) {
		    return strtotime( wp_date( 'Y-m-d H:i:s', strtotime( $str ) ) );
		}

	    public function week_last_date($weekday) {
	    	switch ($weekday) {
			  case "1":
			    $week_day_name = "sunday";
			    break;
			  case "2":
			    $week_day_name = "monday";
			    break;
			  case "3":
			    $week_day_name = "tuesday";
			    break;
			  case "4":
			    $week_day_name = "wednesday";
			    break;
			  case "5":
			    $week_day_name = "thursday";
			    break;
			  case "6":
			    $week_day_name = "friday";
			    break;
			  case "0":
			    $week_day_name = "saturday";
			    break;
			  default:
			  	$week_day_name = "sunday";
			
			}

			return $week_day_name;
	    }


		public function cart_total() {
			$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings'); 
			$cart_total_price = [];
			$enable_including_tax = (isset($delivery_option_settings['calculating_include_tax']) && !empty($delivery_option_settings['calculating_include_tax'])) ? $delivery_option_settings['calculating_include_tax'] : false;
			$enable_including_discount = (isset($delivery_option_settings['calculating_include_discount']) && !empty($delivery_option_settings['calculating_include_discount'])) ? $delivery_option_settings['calculating_include_discount'] : false;

			$cart_total_price['delivery'] = WC()->cart->get_cart_contents_total();

			if($enable_including_tax/* && wc_prices_include_tax()*/) {
				
				/*if($enable_including_discount) {
					$cart_total_price['delivery'] = WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax() + WC()->cart->get_cart_discount_tax_total();
				} else {*/
					$cart_total_price['delivery'] = WC()->cart->get_cart_contents_total() + (float)WC()->cart->get_cart_contents_tax();
				/*}*/
			}

			if($enable_including_discount) {
				$cart_total_price['delivery'] = $cart_total_price['delivery'] + (float)WC()->cart->get_cart_discount_total();
			}


			$calculating_include_tax_free_shipping = (isset($delivery_option_settings['calculating_include_tax_free_shipping']) && !empty($delivery_option_settings['calculating_include_tax_free_shipping'])) ? $delivery_option_settings['calculating_include_tax_free_shipping'] : false;
			$calculating_include_discount_free_shipping = (isset($delivery_option_settings['calculating_include_discount_free_shipping']) && !empty($delivery_option_settings['calculating_include_discount_free_shipping'])) ? $delivery_option_settings['calculating_include_discount_free_shipping'] : false;

			$cart_total_price['delivery_free_shipping'] = WC()->cart->get_cart_contents_total();

			if($calculating_include_tax_free_shipping/* && wc_prices_include_tax()*/) {
				$cart_total_price['delivery_free_shipping'] = WC()->cart->get_cart_contents_total() + (float)WC()->cart->get_cart_contents_tax();
			}

			if($calculating_include_discount_free_shipping) {
				$cart_total_price['delivery_free_shipping'] = $cart_total_price['delivery_free_shipping'] + (float)WC()->cart->get_cart_discount_total();
			}


			/*$cart_total_price = wc_prices_include_tax() ? WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax() : WC()->cart->get_cart_contents_total();*/


			$enable_including_tax_pickup = (isset($delivery_option_settings['calculating_include_tax_pickup']) && !empty($delivery_option_settings['calculating_include_tax_pickup'])) ? $delivery_option_settings['calculating_include_tax_pickup'] : false;
			$enable_including_discount_pickup = (isset($delivery_option_settings['calculating_include_discount_pickup']) && !empty($delivery_option_settings['calculating_include_discount_pickup'])) ? $delivery_option_settings['calculating_include_discount_pickup'] : false;

			$cart_total_price['pickup'] = WC()->cart->get_cart_contents_total();
			
			if($enable_including_tax_pickup/* && wc_prices_include_tax()*/) {
				$cart_total_price['pickup'] = WC()->cart->get_cart_contents_total() + (float)WC()->cart->get_cart_contents_tax();
			}

			if($enable_including_discount_pickup) {
				$cart_total_price['pickup'] = $cart_total_price['pickup'] + (float)WC()->cart->get_cart_discount_total();
			}

			$exclude_settings = get_option('coderockz_woo_delivery_exclude_settings');
			$enable_including_tax_hide_module = (isset($exclude_settings['calculating_include_tax_hide_plugin']) && !empty($exclude_settings['calculating_include_tax_hide_plugin'])) ? $exclude_settings['calculating_include_tax_hide_plugin'] : false;
			$enable_including_discount_hide_module = (isset($exclude_settings['calculating_include_discount_hide_plugin']) && !empty($exclude_settings['calculating_include_discount_hide_plugin'])) ? $exclude_settings['calculating_include_discount_hide_plugin'] : false;

			$cart_total_price['hide_module'] = WC()->cart->get_cart_contents_total();
			
			if($enable_including_tax_hide_module/* && wc_prices_include_tax()*/) {
				$cart_total_price['hide_module'] = WC()->cart->get_cart_contents_total() + (float)WC()->cart->get_cart_contents_tax();
			}

			if($enable_including_discount_hide_module) {
				$cart_total_price['hide_module'] = $cart_total_price['hide_module'] + (float)WC()->cart->get_cart_discount_total();
			}
			

			return $cart_total_price;
		}

		public function cart_total_tips($enable_including_discount, $enable_including_tax, $enable_including_shipping_cost, $enable_including_fees) { 
			
			$cart_total_price = WC()->cart->get_cart_contents_total();
			if($enable_including_tax) {
				
				$cart_total_price = WC()->cart->get_cart_contents_total() + (float)WC()->cart->get_cart_contents_tax();
			}

			if($enable_including_discount) {
				
				$cart_total_price = $cart_total_price + (float)WC()->cart->get_cart_discount_total();
			}

			if($enable_including_shipping_cost) {
				
				$cart_total_price = $cart_total_price + (float)WC()->cart->shipping_total;
			}

			if($enable_including_fees) {
				
				$cart_total_price = $cart_total_price + (float)WC()->cart->fee_total;
			}
			
			return $cart_total_price;
		}

		public function order_total_tips($order, $enable_including_discount, $enable_including_tax, $enable_including_shipping_cost, $enable_including_fees) { 

			$order_total_price = (float) $order->get_total();

			if(!$enable_including_tax) {
				
				$order_total_price = $order_total_price - (float)$order->get_total_tax();
			}

			if(!$enable_including_discount) {
				
				$order_total_price = $order_total_price - (float)$order->get_discount_total();
			}

			if(!$enable_including_shipping_cost) {
				
				$order_total_price = $order_total_price + (float)$order->get_total_shipping();
			}

			if(!$enable_including_fees) {
				
				$order_total_price = $order_total_price + (float)$order->get_fees();
			}
			
			return $order_total_price;
		}


		public function order_cart_total($order_id) {
			
			//global $post;

			$order = wc_get_order( $order_id );

			$order_total_price = [];

			$total_without_tax_shipping = number_format( (float) $order->get_total() - (float) $order->get_total_tax() - (float) $order->get_total_shipping(), wc_get_price_decimals(), '.', '' );

			$total_with_tax = number_format( (float) $order->get_total() - (float) $order->get_total_shipping(), wc_get_price_decimals(), '.', '' );

			$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings'); 

			$enable_including_tax = (isset($delivery_option_settings['calculating_include_tax']) && !empty($delivery_option_settings['calculating_include_tax'])) ? $delivery_option_settings['calculating_include_tax'] : false;
			$enable_including_discount = (isset($delivery_option_settings['calculating_include_discount']) && !empty($delivery_option_settings['calculating_include_discount'])) ? $delivery_option_settings['calculating_include_discount'] : false;

			if($enable_including_tax/* && wc_prices_include_tax()*/) {
				$order_total_price['delivery'] = $total_with_tax;
			} else {
				$order_total_price['delivery'] = $total_without_tax_shipping;
			}

			if($enable_including_discount) {
				$order_total_price['delivery'] = (float)$order_total_price['delivery']+(float)$order->get_discount_total();
			}

			$enable_including_tax_pickup = (isset($delivery_option_settings['calculating_include_tax_pickup']) && !empty($delivery_option_settings['calculating_include_tax_pickup'])) ? $delivery_option_settings['calculating_include_tax_pickup'] : false;
			$enable_including_discount_pickup = (isset($delivery_option_settings['calculating_include_discount_pickup']) && !empty($delivery_option_settings['calculating_include_discount_pickup'])) ? $delivery_option_settings['calculating_include_discount_pickup'] : false;

			if($enable_including_tax_pickup/* && wc_prices_include_tax()*/) {
				$order_total_price['pickup'] = $total_with_tax;
			} else {
				$order_total_price['pickup'] = $total_without_tax_shipping;
			}

			if($enable_including_discount_pickup) {
				$order_total_price['pickup'] = (float)$order_total_price['pickup']+(float)$order->get_discount_total();
			}


			$calculating_include_tax_free_shipping = (isset($delivery_option_settings['calculating_include_tax_free_shipping']) && !empty($delivery_option_settings['calculating_include_tax_free_shipping'])) ? $delivery_option_settings['calculating_include_tax_free_shipping'] : false;
			$calculating_include_discount_free_shipping = (isset($delivery_option_settings['calculating_include_discount_free_shipping']) && !empty($delivery_option_settings['calculating_include_discount_free_shipping'])) ? $delivery_option_settings['calculating_include_discount_free_shipping'] : false;

			if($calculating_include_tax_free_shipping/* && wc_prices_include_tax()*/) {
				$order_total_price['delivery_free_shipping'] = $total_with_tax;;
			} else {
				$order_total_price['delivery_free_shipping'] = $total_without_tax_shipping;
			}

			if($calculating_include_discount_free_shipping) {
				$order_total_price['delivery_free_shipping'] = (float)$order_total_price['delivery_free_shipping']+(float)$order->get_discount_total();
			}

			return $order_total_price;
		}


		public function get_the_timezone() {

			// If site timezone string exists, return it.
			$timezone = get_option( 'timezone_string' );
			if ( $timezone ) {
				return $timezone;
			}

			// Get UTC offset, if it isn't set then return UTC.
			$utc_offset = floatval( get_option( 'gmt_offset', 0 ) );
			if ( ! is_numeric( $utc_offset ) || 0.0 === $utc_offset ) {
				return 'UTC';
			}

			// Adjust UTC offset from hours to seconds.
			$utc_offset = (int) ( $utc_offset * 3600 );

			// Attempt to guess the timezone string from the UTC offset.
			$timezone = timezone_name_from_abbr( '', $utc_offset );
			if ( $timezone ) {
				return $timezone;
			}

			// Last try, guess timezone string manually.
			foreach ( timezone_abbreviations_list() as $abbr ) {
				foreach ( $abbr as $city ) {
					// WordPress restrict the use of date(), since it's affected by timezone settings, but in this case is just what we need to guess the correct timezone.
					if ( (bool) date( 'I' ) === (bool) $city['dst'] && $city['timezone_id'] && intval( $city['offset'] ) === $utc_offset ) { // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
						return $city['timezone_id'];
					}
				}
			}

			// Last try
			$time_zones =  DateTimeZone::listIdentifiers();
			foreach ($time_zones as $time_zone) {
		    	$date = new DateTime('now', new DateTimeZone($time_zone));
		    	$offset_in_hours = $date->getOffset() / 3600;
		    	if ($offset_in_hours == get_option( 'gmt_offset', 0 )) {
		        	return $time_zone;
		      	}
			}

			// Fallback to UTC.
			return 'UTC';
			
		}

		
		public function check_virtual_downloadable_products() {
			// By default, no virtual or downloadable product
			$has_virtual_downloadable_products = false;
			  
			// Default virtual products number
			$virtual_products = 0;

			// Default downloadable products number
			$downloadable_products = 0;
			  
			// Get all products in cart
			$products = WC()->cart->get_cart();

			// Loop through cart products
			foreach( $products as $product ) {

				if($product['variation_id'] == 0) {
					$product_id = $product['product_id'];
				} else {
					$product_id = $product['variation_id'];
				}

				// Get product ID and '_virtual' post meta

				$is_virtual = get_post_meta( $product_id, '_virtual', true );
				$is_downloadable = get_post_meta( $product_id, '_downloadable', true );

				// Update $has_virtual_product if product is virtual
				if( $is_virtual == 'yes' ) {
					$virtual_products += 1;
				}

				// Update $has_virtual_product if product is virtual
				if( $is_downloadable == 'yes' ) {
					$downloadable_products += 1;
				}

				if( $is_virtual == 'yes' && $is_downloadable == 'yes') {
					$virtual_products -= 1;
				}


			}

			$total_virtual_downloadable_products = $virtual_products + $downloadable_products;

			/*if( count($products) == $virtual_products || count($products) == $downloadable_products || count($products) == $total_virtual_downloadable_products) {
				$has_virtual_downloadable_products = true;
			}*/


			$other_settings = get_option('coderockz_woo_delivery_other_settings');
			$disable_fields_for_downloadable_products = (isset($other_settings['disable_fields_for_downloadable_products']) && !empty($other_settings['disable_fields_for_downloadable_products'])) ? $other_settings['disable_fields_for_downloadable_products'] : false;
			$disable_fields_for_downloadable_regular_products = (isset($other_settings['disable_fields_for_downloadable_regular_products']) && !empty($other_settings['disable_fields_for_downloadable_regular_products'])) ? $other_settings['disable_fields_for_downloadable_regular_products'] : false;


			if( !$disable_fields_for_downloadable_products && count($products) == $total_virtual_downloadable_products && $total_virtual_downloadable_products > 0 ) {

				$has_virtual_downloadable_products = true;
			}

			if( !$disable_fields_for_downloadable_regular_products && count($products) > $total_virtual_downloadable_products && $total_virtual_downloadable_products > 0 ) {

				$has_virtual_downloadable_products = true;

			}

			//return $has_virtual_downloadable_products;

			return apply_filters('coderockz_has_virtual_downloadable_products', $has_virtual_downloadable_products);
		}


		public function order_check_virtual_downloadable_products($order_items) {
			$has_virtual_downloadable_products = false;
			$virtual_products = 0;
			$downloadable_products = 0;
			foreach( $order_items as $item ) {

				if( $item->get_variation_id() ) {
					$product_id = $item->get_variation_id();
				} else {
					$product_id = $item->get_product_id();
				}

				$is_virtual = get_post_meta( $product_id, '_virtual', true );
				$is_downloadable = get_post_meta( $product_id, '_downloadable', true );

				if( $is_virtual == 'yes' ) {
					$virtual_products += 1;
				}

				if( $is_downloadable == 'yes' ) {
					$downloadable_products += 1;
				}

				if( $is_virtual == 'yes' && $is_downloadable == 'yes') {
					$virtual_products -= 1;
				}

			}

			$total_virtual_downloadable_products = $virtual_products + $downloadable_products;

			$other_settings = get_option('coderockz_woo_delivery_other_settings');
			$disable_fields_for_downloadable_products = (isset($other_settings['disable_fields_for_downloadable_products']) && !empty($other_settings['disable_fields_for_downloadable_products'])) ? $other_settings['disable_fields_for_downloadable_products'] : false;
			$disable_fields_for_downloadable_regular_products = (isset($other_settings['disable_fields_for_downloadable_regular_products']) && !empty($other_settings['disable_fields_for_downloadable_regular_products'])) ? $other_settings['disable_fields_for_downloadable_regular_products'] : false;


			if( !$disable_fields_for_downloadable_products && count($order_items) == $total_virtual_downloadable_products && $total_virtual_downloadable_products > 0 ) {

				$has_virtual_downloadable_products = true;
			}


			if( !$disable_fields_for_downloadable_regular_products && count($order_items) > $total_virtual_downloadable_products && $total_virtual_downloadable_products > 0 ) {

				$has_virtual_downloadable_products = true;

			}

			return $has_virtual_downloadable_products;
		}

		public function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		    $sort_col = array();
		    foreach ($arr as $key=> $row) {
		        $sort_col[$key] = $row;
		    }

		    array_multisort($sort_col, $dir, $arr);
		    return $sort_col;
		}

		public function number_between($varToCheck, $high, $low) {
			if($varToCheck < $low) return false;
			if($varToCheck > $high) return false;
			return true;
		}

		public function checkout_product_categories($exclude_checking=false) {
			$product_cat = [];
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if(!is_null($cart_item['data']->get_parent_id()) && $cart_item['data']->get_parent_id()) {
					$variable_product = $cart_item['data']->get_parent_id();
					$terms = get_the_terms( $variable_product, 'product_cat' );
				} else {
					$terms = get_the_terms( $cart_item['data']->get_id(), 'product_cat' );
				}

				if(!empty($terms)) {
					foreach ($terms as $term) {
						if(!in_array(htmlspecialchars_decode($term->name),$product_cat))
							$product_cat[] = htmlspecialchars_decode($term->name);
					}
				}				
			}
			$checkout_product_categories = $product_cat;
			if($exclude_checking == false) {
				$checkout_product_categories = array_map('strtolower', $checkout_product_categories);
			}
			
			return $checkout_product_categories;
		}


		public function coderockz_woo_deivery_report_special_text($input,$change=false) {
		    $o = $s1 = $s2 = array();
		    $basea = array('?','(','@',';','$','#',"]","&",'*'); // base symbol set
		    $basea = array_merge($basea, range('a','z'), range('A','Z'), range(0,9) );
		    $basea = array_merge($basea, array('!',')','_','+','|','%','/','[','.',' ') );
		    $dimension=9;
		    for($i=0;$i<$dimension;$i++) { // create Squares
		        for($j=0;$j<$dimension;$j++) {
		            $s1[$i][$j] = $basea[$i*$dimension+$j];
		            $s2[$i][$j] = str_rot13($basea[($dimension*$dimension-1) - ($i*$dimension+$j)]);
		        }
		    }
		    unset($basea);
		    $m = floor(strlen($input)/2)*2;
		    $symbl = $m==strlen($input) ? '':$input[strlen($input)-1];
		    $al = array();
		    for ($ii=0; $ii<$m; $ii+=2) {
		        $symb1 = $symbn1 = strval($input[$ii]);
		        $symb2 = $symbn2 = strval($input[$ii+1]);
		        $a1 = $a2 = array();
		        for($i=0;$i<$dimension;$i++) {
		            for($j=0;$j<$dimension;$j++) {
		                if ($change) {
		                    if ($symb1===strval($s2[$i][$j]) ) $a1=array($i,$j);
		                    if ($symb2===strval($s1[$i][$j]) ) $a2=array($i,$j);
		                    if (!empty($symbl) && $symbl===strval($s2[$i][$j])) $al=array($i,$j);
		                }
		                else {
		                    if ($symb1===strval($s1[$i][$j]) ) $a1=array($i,$j);
		                    if ($symb2===strval($s2[$i][$j]) ) $a2=array($i,$j);
		                    if (!empty($symbl) && $symbl===strval($s1[$i][$j])) $al=array($i,$j);
		                }
		            }
		        }
		        if (sizeof($a1) && sizeof($a2)) {
		            $symbn1 = $change ? $s1[$a1[0]][$a2[1]] : $s2[$a1[0]][$a2[1]];
		            $symbn2 = $change ? $s2[$a2[0]][$a1[1]] : $s1[$a2[0]][$a1[1]];
		        }
		        $o[] = $symbn1.$symbn2;
		    }
		    if (!empty($symbl) && sizeof($al))
		        $o[] = $change ? $s1[$al[1]][$al[0]] : $s2[$al[1]][$al[0]];
		    return implode('',$o);
		}


		public function checkout_product_categories_opendays_exclusion_condition($categories,$exclude_checking=false) {
			$product_cat = [];
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if(!is_null($cart_item['data']->get_parent_id()) && $cart_item['data']->get_parent_id()) {
					$variable_product = $cart_item['data']->get_parent_id();
					$terms = get_the_terms( $variable_product, 'product_cat' );
				} else {
					$terms = get_the_terms( $cart_item['data']->get_id(), 'product_cat' );
				}

				$old_term_name = [];

				if(!empty($terms)) {

					if(count($terms) > 1 ) {
					
						foreach ($terms as $term) {
							if($exclude_checking == false) {
								if (in_array(htmlspecialchars_decode(strtolower($term->name)),$categories)){
									$old_term_name = [];
									$old_term_name[] = htmlspecialchars_decode($term->name);
									break;
								}
							} elseif($exclude_checking == true) {
								if (in_array(htmlspecialchars_decode($term->name),$categories)){
									$old_term_name = [];
									$old_term_name [] = htmlspecialchars_decode($term->name);
									break;
								}
							}
							$old_term_name[] = htmlspecialchars_decode(strtolower($term->name));
						}
					
					} else {
						foreach ($terms as $term) {
							$old_term_name[] = htmlspecialchars_decode($term->name);
						}
					}

				}

				$product_cat = array_merge($product_cat, $old_term_name);

			}

			$checkout_product_categories = array_filter(array_values(array_unique($product_cat)), 'strlen');

			if($exclude_checking == false) {
				$checkout_product_categories = array_map('strtolower', $checkout_product_categories);
			}
				
			return $checkout_product_categories;
		}

		public function checkout_product_id() {
			$product_id = [];
			foreach ( WC()->cart->get_cart() as $cart_item ) {

				if($cart_item['data']->get_parent_id()) {
					$product_id[] = $cart_item['data']->get_parent_id();
					$product_id[] = $cart_item['data']->get_id();
				} else {
					$product_id[] = $cart_item['data']->get_id();
				}
				
			}
			return $product_id;
		}

		public function order_product_categories($order_items, $exclude_checking=false) {
			$product_cat = [];
			foreach ( $order_items as $item ) { 
				$terms = get_the_terms( $item->get_product_id(), 'product_cat' );
				if(!empty($terms)) {
					foreach ($terms as $term) {
						$product_cat[] = htmlspecialchars_decode($term->name);
					}
				}
			}

			// preparing product categories data from cart
			$order_product_categories = array_values(array_unique($product_cat));
			if($exclude_checking == false) {
				$order_product_categories = array_map('strtolower', $order_product_categories);
			}

			return $order_product_categories;
		}

		public function order_product_categories_opendays_exclusion_condition($order_items, $categories, $exclude_checking=false) {
			$product_cat = [];
			foreach ( $order_items as $item ) {
				$terms = get_the_terms( $item->get_product_id(), 'product_cat' );
				
				$old_term_name = [];

				if(!empty($terms)) {

					if(count($terms) > 1 ) {
					
						foreach ($terms as $term) {
							if($exclude_checking == false) {
								if (in_array(htmlspecialchars_decode(strtolower($term->name)),$categories)){
									$old_term_name = [];
									$old_term_name[] = htmlspecialchars_decode($term->name);
									break;
								}
							} elseif($exclude_checking == true) {
								if (in_array(htmlspecialchars_decode($term->name),$categories)){
									$old_term_name = [];
									$old_term_name [] = htmlspecialchars_decode($term->name);
									break;
								}
							}
							$old_term_name[] = htmlspecialchars_decode(strtolower($term->name));
						}
					
					} else {
						foreach ($terms as $term) {
							$old_term_name[] = htmlspecialchars_decode($term->name);
						}
					}

				}
				
				$product_cat = array_merge($product_cat, $old_term_name);
	
			}

			$order_product_categories = array_filter(array_values(array_unique($product_cat)), 'strlen');

			if($exclude_checking == false) {
				$order_product_categories = array_map('strtolower', $order_product_categories);
			}
			
			return $order_product_categories;
		}

		public function order_product_id($order_items) {
			$product_id = [];
			foreach ( $order_items as $item ) {
			    if( $item->get_variation_id() ) {
			        $product_id[] = $item->get_variation_id();
			        $product_id[] = $item->get_product_id();			        
			    } else {
					$product_id[] = $item->get_product_id();
				}

			}
			return $product_id;
		}

		public function detect_exclude_condition() {
			$exclude_settings = get_option('coderockz_woo_delivery_exclude_settings');

			$exclude_categories_array = (isset($exclude_settings['exclude_categories']) && !empty($exclude_settings['exclude_categories'])) ? $exclude_settings['exclude_categories'] : array();
			$exclude_categories = [];
			foreach ($exclude_categories_array as $exclude_category) {
				$exclude_categories [] = stripslashes($exclude_category);
			}

			$exclude_products = (isset($exclude_settings['exclude_products']) && !empty($exclude_settings['exclude_products'])) ? $exclude_settings['exclude_products'] : array();

			$reverse_current_condition = (isset($exclude_settings['reverse_current_condition']) && !empty($exclude_settings['reverse_current_condition'])) ? $exclude_settings['reverse_current_condition'] : false;

			//$checkout_product_categories = $this->checkout_product_categories(true);
			$checkout_product_categories = $this->checkout_product_categories_opendays_exclusion_condition($exclude_categories,true);
			$checkout_products = $this->checkout_product_id();


			$exclude_categories_condition = (count(array_intersect($checkout_product_categories, $exclude_categories)) <= count($checkout_product_categories)) && count(array_intersect($checkout_product_categories, $exclude_categories))>0;

  			
  			$exclude_products_condition = (count(array_intersect($checkout_products, $exclude_products)) <= count($checkout_products)) && count(array_intersect($checkout_products, $exclude_products))>0;

			if($exclude_categories_condition) {
				$exclude_condition = true;
				if($reverse_current_condition && count($checkout_product_categories) > 1 && count($checkout_products) > 1 && count(array_diff($checkout_product_categories, $exclude_categories))>0) {
	  				$exclude_condition = !$exclude_condition;
	  			}
			} elseif($exclude_products_condition) {
				$exclude_condition = true;
				if($reverse_current_condition && count($checkout_products) > 1 && count(array_diff($checkout_products, $exclude_products))>0) {
	  				$exclude_condition = !$exclude_condition;
	  			}
			} else {
				$exclude_condition = false;
			}

			return $exclude_condition;
		}


		public function detect_exclude_user_roles_condition() {
			$exclude_settings = get_option('coderockz_woo_delivery_exclude_settings');

			$exclude_user_roles = (isset($exclude_settings['exclude_user_roles']) && !empty($exclude_settings['exclude_user_roles'])) ? $exclude_settings['exclude_user_roles'] : array();
			/*$exclude_user_roles = [];
			foreach ($exclude_user_roles_array as $exclude_category) {
				$exclude_user_roles [] = stripslashes($exclude_category);
			}*/

			$exclusion_non_logged_in_user = (isset($exclude_settings['exclusion_non_logged_in_user']) && !empty($exclude_settings['exclusion_non_logged_in_user'])) ? $exclude_settings['exclusion_non_logged_in_user'] : false;

			if( is_user_logged_in() ) { // check if there is a logged in user 
	 
				$user = wp_get_current_user(); // getting & setting the current user 
				$roles = ( array ) $user->roles; // obtaining the role 
				 
				$user_roles = $roles; // return the role for the current user 
				 
			
			} else {
					 
				$user_roles = array(); // if there is no logged in user return empty array  
			 
			}

			$exclude_user_roles_condition = (count(array_intersect($user_roles, $exclude_user_roles)) <= count($user_roles)) && count(array_intersect($user_roles, $exclude_user_roles))>0;


			if($exclude_user_roles_condition) {
				$exclude_user_roles_condition = true;
			} else {
				$exclude_user_roles_condition = false;
			}

			if( !is_user_logged_in() && $exclusion_non_logged_in_user) {
				$exclude_user_roles_condition = true;
			}

			return $exclude_user_roles_condition;
		}


		public function order_detect_exclude_condition($order_items) {
			$exclude_settings = get_option('coderockz_woo_delivery_exclude_settings');

			$exclude_categories_array = (isset($exclude_settings['exclude_categories']) && !empty($exclude_settings['exclude_categories'])) ? $exclude_settings['exclude_categories'] : array();
			$exclude_categories = [];
			foreach ($exclude_categories_array as $exclude_category) {
				$exclude_categories [] = stripslashes($exclude_category);
			}

			$exclude_products = (isset($exclude_settings['exclude_products']) && !empty($exclude_settings['exclude_products'])) ? $exclude_settings['exclude_products'] : array();

			$reverse_current_condition = (isset($exclude_settings['reverse_current_condition']) && !empty($exclude_settings['reverse_current_condition'])) ? $exclude_settings['reverse_current_condition'] : false;

			//$order_product_categories = $this->order_product_categories($order_items,true);
			$order_product_categories = $this->order_product_categories_opendays_exclusion_condition($order_items, $exclude_categories,true);
			$order_products = $this->order_product_id($order_items);

			$exclude_categories_condition = (count(array_intersect($order_product_categories, $exclude_categories)) <= count($order_product_categories)) && count(array_intersect($order_product_categories, $exclude_categories))>0;
  			
  			$exclude_products_condition = (count(array_intersect($order_products, $exclude_products)) <= count($order_products)) && count(array_intersect($order_products, $exclude_products))>0;

			if($exclude_categories_condition) {
				$exclude_condition = true;
				if($reverse_current_condition && count($order_product_categories) > 1 && count($order_products) > 1 && count(array_diff($order_product_categories, $exclude_categories))>0) {
	  				$exclude_condition = !$exclude_condition;
	  			}
			} elseif($exclude_products_condition) {
				$exclude_condition = true;
				if($reverse_current_condition && count($order_products) > 1 && count(array_diff($order_products, $exclude_products))>0) {
	  				$exclude_condition = !$exclude_condition;
	  			}
			} else {
				$exclude_condition = false;
			}

			return $exclude_condition;
		}

		/*public function site_url() {
		    if(isset($_SERVER['HTTPS'])){
		        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
		    }
		    else{
		        $protocol = "http";
		    }
		    if(!is_multisite()) {
			    if($_SERVER['HTTP_HOST'] =="localhost") {
			    	return filter_var($protocol . "://" . $_SERVER['HTTP_HOST']. dirname($_SERVER['PHP_SELF']),
	                FILTER_SANITIZE_URL);
			    } else {
			    	return filter_var($protocol . "://" . $_SERVER['HTTP_HOST'],
	                FILTER_SANITIZE_URL);
			    }
			} else {
				return get_bloginfo( 'url' );
			}
		    
		}*/

		public function format_price($price, $orderId = null) {
	        return sprintf(get_woocommerce_price_format(), get_woocommerce_currency(wc_get_order($orderId)->get_currency()), $price);
	    }

	    public function postion_currency_symbol($currency_symbol,$price) {
	    	if(get_option( 'woocommerce_currency_pos' ) == 'right') {
				$price = $price.$currency_symbol;
			} elseif(get_option( 'woocommerce_currency_pos' ) == 'left_space') {
				$price = $currency_symbol.$price;
			} elseif(get_option( 'woocommerce_currency_pos' ) == 'right_space') {
				$price = $price.$currency_symbol;
			} if(get_option( 'woocommerce_currency_pos' ) == 'left') {
				$price = $currency_symbol.$price;
			}
	        return $price;
	    }

	    public function get_product_image($id) {
	        $image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'single-post-thumbnail' );
			$image = '<img src="'.$image_url[0].'" style="width:35px;margin-right: 10px;vertical-align: middle;float:left;">';
	            return $image;
			return $image;
	    }

	    public function product_name_length($name) { 
	    	$product_title_length = 30;
	        if(strlen($name)>$product_title_length){
	            $name =substr($name,0,$product_title_length). "...";
	            return $name;
	        } else {
	            return $name;
	        }
	    }

	    public function weekday_conversion( $date, $delivery_type="delivery" ) {
			$arWeek = ["الأحد","الاثنين","الثلاثاء","الأربعاء","الخميس","الجمعة","السبت"];
			$atWeek = ["Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag"];
		    $azWeek = ["Bazar","Bazar ertəsi","Çərşənbə axşamı","Çərşənbə","Cümə axşamı","Cümə","Şənbə"];
			$beWeek = ["Нядзеля","Панядзелак","Аўторак","Серада","Чацвер","Пятніца","Субота"];
			$bgWeek = ["Неделя","Понеделник","Вторник","Сряда","Четвъртък","Петък","Събота"];
		    $bnWeek = ["রবিবার","সোমবার","মঙ্গলবার","বুধবার","বৃহস্পতিবার","শুক্রবার","শনিবার"];
		    $bsWeek = ["Nedjelja","Ponedjeljak","Utorak","Srijeda","Četvrtak","Petak","Subota"];
		    $catWeek = ["Diumenge","Dilluns","Dimarts","Dimecres","Dijous","Divendres","Dissabte"];
		    $csWeek = ["Neděle","Pondělí","Úterý","Středa","Čtvrtek","Pátek","Sobota"];
		    $cyWeek = ["Dydd Sul","Dydd Llun","Dydd Mawrth","Dydd Mercher","Dydd Iau","Dydd Gwener","Dydd Sadwrn"];
		    $daWeek = ["søndag","mandag","tirsdag","onsdag","torsdag","fredag","lørdag"];
		    $deWeek = ["Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag"];
		    $defaultWeek = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
		    $eoWeek = ["dimanĉo","lundo","mardo","merkredo","ĵaŭdo","vendredo","sabato"];
		    $esWeek = ["Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"];
		    $etWeek = ["Pühapäev","Esmaspäev","Teisipäev","Kolmapäev","Neljapäev","Reede","Laupäev"];
		    $enWeek = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
		    $fiWeek = ["Sunnuntai","Maanantai","Tiistai","Keskiviikko","Torstai","Perjantai","Lauantai"];
		    $foWeek = ["Sunnudagur","Mánadagur","Týsdagur","Mikudagur","Hósdagur","Fríggjadagur","Leygardagur"];
		    $frWeek = ["dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi"];
		    $faWeek = ["یک‌شنبه","دوشنبه","سه‌شنبه","چهارشنبه","پنچ‌شنبه","جمعه","شنبه"];
		    $gaWeek = ["Dé Domhnaigh","Dé Luain","Dé Máirt","Dé Céadaoin","Déardaoin","Dé hAoine","Dé Sathairn"];
		    $grWeek = ["Κυριακή","Δευτέρα","Τρίτη","Τετάρτη","Πέμπτη","Παρασκευή","Σάββατο"];
		    $heWeek = ["ראשון","שני","שלישי","רביעי","חמישי","שישי","שבת"];
		    $hiWeek = ["रविवार","सोमवार","मंगलवार","बुधवार","गुरुवार","शुक्रवार","शनिवार"];
		    $hrWeek = ["Nedjelja","Ponedjeljak","Utorak","Srijeda","Četvrtak","Petak","Subota"];
		    $huWeek = ["Vasárnap","Hétfő","Kedd","Szerda","Csütörtök","Péntek","Szombat"];
		    $idWeek = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
		    $isWeek = ["Sunnudagur","Mánudagur","Þriðjudagur","Miðvikudagur","Fimmtudagur","Föstudagur","Laugardagur"];
		    $itWeek = ["Domenica","Lunedì","Martedì","Mercoledì","Giovedì","Venerdì","Sabato"];
		    $jaWeek = ["日曜日","月曜日","火曜日","水曜日","木曜日","金曜日","土曜日"];
		    $kaWeek = ["კვირა","ორშაბათი","სამშაბათი","ოთხშაბათი","ხუთშაბათი","პარასკევი","შაბათი"];
		    $kmWeek = ["អាទិត្យ","ចន្ទ","អង្គារ","ពុធ","ព្រហស្បតិ៍","សុក្រ","សៅរ៍"];
		    $koWeek = ["일요일","월요일","화요일","수요일","목요일","금요일","토요일"];
		    $kzWeek = ["Жексенбi","Дүйсенбi","Сейсенбi","Сәрсенбi","Бейсенбi","Жұма","Сенбi"];
		    $ltWeek = ["Sekmadienis","Pirmadienis","Antradienis","Trečiadienis","Ketvirtadienis","Penktadienis","Šeštadienis"];
		    $lvWeek = ["Svētdiena","Pirmdiena","Otrdiena","Trešdiena","Ceturtdiena","Piektdiena","Sestdiena"];
		    $mkWeek = ["Недела","Понеделник","Вторник","Среда","Четврток","Петок","Сабота"];
		    $mnWeek = ["Даваа","Мягмар","Лхагва","Пүрэв","Баасан","Бямба","Ням"];
		    $msWeek = ["Minggu","Isnin","Selasa","Rabu","Khamis","Jumaat","Sabtu"];
		    $myWeek = ["တနင်္ဂနွေ","တနင်္လာ","အင်္ဂါ","ဗုဒ္ဓဟူး","ကြာသပတေး","သောကြာ","စနေ"];
		    $nlWeek = ["zondag","maandag","dinsdag","woensdag","donderdag","vrijdag","zaterdag"];
		    $noWeek = ["Søndag","Mandag","Tirsdag","Onsdag","Torsdag","Fredag","Lørdag"];
		    $paWeek = ["ਐਤਵਾਰ","ਸੋਮਵਾਰ","ਮੰਗਲਵਾਰ","ਬੁੱਧਵਾਰ","ਵੀਰਵਾਰ","ਸ਼ੁੱਕਰਵਾਰ","ਸ਼ਨਿੱਚਰਵਾਰ"];
		    $plWeek = ["Niedziela","Poniedziałek","Wtorek","Środa","Czwartek","Piątek","Sobota"];
		    $ptWeek = ["Domingo","Segunda-feira","Terça-feira","Quarta-feira","Quinta-feira","Sexta-feira","Sábado"];
		    $roWeek = ["Duminică","Luni","Marți","Miercuri","Joi","Vineri","Sâmbătă"];
		    $ruWeek = ["Воскресенье","Понедельник","Вторник","Среда","Четверг","Пятница","Суббота"];
		    $skWeek = ["Nedeľa","Pondelok","Utorok","Streda","Štvrtok","Piatok","Sobota"];
		    $slWeek = ["Nedelja","Ponedeljek","Torek","Sreda","Četrtek","Petek","Sobota"];
		    $siWeek = ["ඉරිදා","සඳුදා","අඟහරුවාදා","බදාදා","බ්‍රහස්පතින්දා","සිකුරාදා","සෙනසුරාදා"];
		    $sqWeek = ["E Diel","E Hënë","E Martë","E Mërkurë","E Enjte","E Premte","E Shtunë"];
		    $srcyrWeek = ["Недеља","Понедељак","Уторак","Среда","Четвртак","Петак","Субота"];
		    $srWeek = ["Nedelja","Ponedeljak","Utorak","Sreda","Četvrtak","Petak","Subota"];
		    $svWeek = ["Söndag","Måndag","Tisdag","Onsdag","Torsdag","Fredag","Lördag"];
		    $thWeek = ["อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์"];
		    $trWeek = ["Pazar","Pazartesi","Salı","Çarşamba","Perşembe","Cuma","Cumartesi"];
		    $ukWeek = ["Неділя","Понеділок","Вівторок","Середа","Четвер","П'ятниця","Субота"];
		    $uzWeek = ["Якшанба","Душанба","Сешанба","Чоршанба","Пайшанба","Жума","Шанба"];
		    $uzlatnWeek = ["Yakshanba","Dushanba","Seshanba","Chorshanba","Payshanba","Juma","Shanba"];
		    $vnWeek = ["Chủ nhật","Thứ hai","Thứ ba","Thứ tư","Thứ năm","Thứ sáu","Thứ bảy"];
		    $zhWeek = ["星期日","星期一","星期二","星期三","星期四","星期五","星期六"];
		    
		    
		    if($delivery_type == "delivery") {
		    	$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
				$calendar_locale = (isset($delivery_date_settings['calendar_locale']) && !empty($delivery_date_settings['calendar_locale'])) ? $delivery_date_settings['calendar_locale'] : "default";
		    } elseif ($delivery_type == "pickup") {
		    	$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
				$calendar_locale = (isset($pickup_date_settings['calendar_locale']) && !empty($pickup_date_settings['calendar_locale'])) ? $pickup_date_settings['calendar_locale'] : "default";
		    }
		    

			switch ($calendar_locale) {
			    case "ar":
			        return str_replace($arWeek, $enWeek, $date);
			        break;
			    case "at":
			        return str_replace($atWeek, $enWeek, $date);
			        break;
			    case "az":
			        return str_replace($azWeek, $enWeek, $date);
			        break;
			    case "be":
			        return str_replace($beWeek, $enWeek, $date);
			        break;
			    case "bg":
			        return str_replace($bgWeek, $enWeek, $date);
			        break;
			    case "bn":
			        return str_replace($bnWeek, $enWeek, $date);
			        break;
			    case "bs":
			        return str_replace($bsWeek, $enWeek, $date);
			        break;
			    case "cat":
			        return str_replace($catWeek, $enWeek, $date);
			        break;
			    case "cs":
			        return str_replace($csWeek, $enWeek, $date);
			        break;
			    case "cy":
			        return str_replace($cyWeek, $enWeek, $date);
			        break;
			    case "da":
			        return str_replace($daWeek, $enWeek, $date);
			        break;
			    case "de":
			        return str_replace($deWeek, $enWeek, $date);
			        break;
			    case "default":
			        return str_replace($defaultWeek, $enWeek, $date);
			        break;
			    case "eo":
			        return str_replace($eoWeek, $enWeek, $date);
			        break;
			    case "es":
			        return str_replace($esWeek, $enWeek, $date);
			        break;
			    case "et":
			        return str_replace($etWeek, $enWeek, $date);
			        break;
			    case "fi":
			        return str_replace($fiWeek, $enWeek, $date);
			        break;
			    case "fo":
			        return str_replace($foWeek, $enWeek, $date);
			        break;
			    case "fr":
			        return str_replace($frWeek, $enWeek, $date);
			        break;
			    case "fa":
			        return str_replace($faWeek, $enWeek, $date);
			        break;
			    case "ga":
			        return str_replace($gaWeek, $enWeek, $date);
			        break;
			    case "gr":
			        return str_replace($grWeek, $enWeek, $date);
			        break;
			    case "he":
			        return str_replace($heWeek, $enWeek, $date);
			        break;
			    case "hi":
			        return str_replace($hiWeek, $enWeek, $date);
			        break;
			    case "hr":
			        return str_replace($hrWeek, $enWeek, $date);
			        break;
			    case "hu":
			        return str_replace($huWeek, $enWeek, $date);
			        break;
			    case "id":
			        return str_replace($idWeek, $enWeek, $date);
			        break;
			    case "is":
			        return str_replace($isWeek, $enWeek, $date);
			        break;
			    case "it":
			        return str_replace($itWeek, $enWeek, $date);
			        break;
			    case "ja":
			        return str_replace($jaWeek, $enWeek, $date);
			        break;
			    case "ka":
			        return str_replace($kaWeek, $enWeek, $date);
			        break;
			    case "km":
			        return str_replace($kmWeek, $enWeek, $date);
			        break;
			    case "ko":
			        return str_replace($koWeek, $enWeek, $date);
			        break;
			    case "kz":
			        return str_replace($kzWeek, $enWeek, $date);
			        break;
			    case "lt":
			        return str_replace($ltWeek, $enWeek, $date);
			        break;
			    case "lv":
			        return str_replace($lvWeek, $enWeek, $date);
			        break;
			    case "mk":
			        return str_replace($mkWeek, $enWeek, $date);
			        break;
			    case "mn":
			        return str_replace($mnWeek, $enWeek, $date);
			        break;
			    case "ms":
			        return str_replace($msWeek, $enWeek, $date);
			        break;
			    case "my":
			        return str_replace($myWeek, $enWeek, $date);
			        break;
			    case "nl":
			        return str_replace($nlWeek, $enWeek, $date);
			        break;
			    case "no":
			        return str_replace($noWeek, $enWeek, $date);
			        break;
			    case "pa":
			        return str_replace($paWeek, $enWeek, $date);
			        break;
			    case "pl":
			        return str_replace($plWeek, $enWeek, $date);
			        break;
			    case "pt":
			        return str_replace($ptWeek, $enWeek, $date);
			        break;
			    case "ro":
			        return str_replace($roWeek, $enWeek, $date);
			        break;
			    case "ru":
			        return str_replace($ruWeek, $enWeek, $date);
			        break;
			    case "sk":
			        return str_replace($skWeek, $enWeek, $date);
			        break;
			    case "sl":
			        return str_replace($slWeek, $enWeek, $date);
			        break;
			    case "si":
			        return str_replace($siWeek, $enWeek, $date);
			        break;
			    case "sq":
			        return str_replace($sqWeek, $enWeek, $date);
			        break;
			    case "sr-cyr":
			        return str_replace($srcyrWeek, $enWeek, $date);
			        break;
			    case "sr":
			        return str_replace($srWeek, $enWeek, $date);
			        break;
			    case "sv":
			        return str_replace($svWeek, $enWeek, $date);
			        break;
			    case "th":
			        return str_replace($thWeek, $enWeek, $date);
			        break;
			    case "tr":
			        return str_replace($trWeek, $enWeek, $date);
			        break;
			    case "uk":
			        return str_replace($ukWeek, $enWeek, $date);
			        break;
			    case "uz":
			        return str_replace($uzWeek, $enWeek, $date);
			        break;
			    case "uz-latn":
			        return str_replace($uzlatnWeek, $enWeek, $date);
			        break;
			    case "vn":
			        return str_replace($vnWeek, $enWeek, $date);
			        break;
			    case "zh":
			        return str_replace($zhWeek, $enWeek, $date);
			        break;

			}
		    
		}

		public function weekday_conversion_to_locale( $date, $delivery_type="delivery" ) {
			$arWeek = ["الأحد","الاثنين","الثلاثاء","الأربعاء","الخميس","الجمعة","السبت"];
			$atWeek = ["Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag"];
		    $azWeek = ["Bazar","Bazar ertəsi","Çərşənbə axşamı","Çərşənbə","Cümə axşamı","Cümə","Şənbə"];
			$beWeek = ["Нядзеля","Панядзелак","Аўторак","Серада","Чацвер","Пятніца","Субота"];
			$bgWeek = ["Неделя","Понеделник","Вторник","Сряда","Четвъртък","Петък","Събота"];
		    $bnWeek = ["রবিবার","সোমবার","মঙ্গলবার","বুধবার","বৃহস্পতিবার","শুক্রবার","শনিবার"];
		    $bsWeek = ["Nedjelja","Ponedjeljak","Utorak","Srijeda","Četvrtak","Petak","Subota"];
		    $catWeek = ["Diumenge","Dilluns","Dimarts","Dimecres","Dijous","Divendres","Dissabte"];
		    $csWeek = ["Neděle","Pondělí","Úterý","Středa","Čtvrtek","Pátek","Sobota"];
		    $cyWeek = ["Dydd Sul","Dydd Llun","Dydd Mawrth","Dydd Mercher","Dydd Iau","Dydd Gwener","Dydd Sadwrn"];
		    $daWeek = ["søndag","mandag","tirsdag","onsdag","torsdag","fredag","lørdag"];
		    $deWeek = ["Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag"];
		    $defaultWeek = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
		    $eoWeek = ["dimanĉo","lundo","mardo","merkredo","ĵaŭdo","vendredo","sabato"];
		    $esWeek = ["Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"];
		    $etWeek = ["Pühapäev","Esmaspäev","Teisipäev","Kolmapäev","Neljapäev","Reede","Laupäev"];
		    $enWeek = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
		    $fiWeek = ["Sunnuntai","Maanantai","Tiistai","Keskiviikko","Torstai","Perjantai","Lauantai"];
		    $foWeek = ["Sunnudagur","Mánadagur","Týsdagur","Mikudagur","Hósdagur","Fríggjadagur","Leygardagur"];
		    $frWeek = ["dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi"];
		    $faWeek = ["یک‌شنبه","دوشنبه","سه‌شنبه","چهارشنبه","پنچ‌شنبه","جمعه","شنبه"];
		    $gaWeek = ["Dé Domhnaigh","Dé Luain","Dé Máirt","Dé Céadaoin","Déardaoin","Dé hAoine","Dé Sathairn"];
		    $grWeek = ["Κυριακή","Δευτέρα","Τρίτη","Τετάρτη","Πέμπτη","Παρασκευή","Σάββατο"];
		    $heWeek = ["ראשון","שני","שלישי","רביעי","חמישי","שישי","שבת"];
		    $hiWeek = ["रविवार","सोमवार","मंगलवार","बुधवार","गुरुवार","शुक्रवार","शनिवार"];
		    $hrWeek = ["Nedjelja","Ponedjeljak","Utorak","Srijeda","Četvrtak","Petak","Subota"];
		    $huWeek = ["Vasárnap","Hétfő","Kedd","Szerda","Csütörtök","Péntek","Szombat"];
		    $idWeek = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
		    $isWeek = ["Sunnudagur","Mánudagur","Þriðjudagur","Miðvikudagur","Fimmtudagur","Föstudagur","Laugardagur"];
		    $itWeek = ["Domenica","Lunedì","Martedì","Mercoledì","Giovedì","Venerdì","Sabato"];
		    $jaWeek = ["日曜日","月曜日","火曜日","水曜日","木曜日","金曜日","土曜日"];
		    $kaWeek = ["კვირა","ორშაბათი","სამშაბათი","ოთხშაბათი","ხუთშაბათი","პარასკევი","შაბათი"];
		    $kmWeek = ["អាទិត្យ","ចន្ទ","អង្គារ","ពុធ","ព្រហស្បតិ៍","សុក្រ","សៅរ៍"];
		    $koWeek = ["일요일","월요일","화요일","수요일","목요일","금요일","토요일"];
		    $kzWeek = ["Жексенбi","Дүйсенбi","Сейсенбi","Сәрсенбi","Бейсенбi","Жұма","Сенбi"];
		    $ltWeek = ["Sekmadienis","Pirmadienis","Antradienis","Trečiadienis","Ketvirtadienis","Penktadienis","Šeštadienis"];
		    $lvWeek = ["Svētdiena","Pirmdiena","Otrdiena","Trešdiena","Ceturtdiena","Piektdiena","Sestdiena"];
		    $mkWeek = ["Недела","Понеделник","Вторник","Среда","Четврток","Петок","Сабота"];
		    $mnWeek = ["Даваа","Мягмар","Лхагва","Пүрэв","Баасан","Бямба","Ням"];
		    $msWeek = ["Minggu","Isnin","Selasa","Rabu","Khamis","Jumaat","Sabtu"];
		    $myWeek = ["တနင်္ဂနွေ","တနင်္လာ","အင်္ဂါ","ဗုဒ္ဓဟူး","ကြာသပတေး","သောကြာ","စနေ"];
		    $nlWeek = ["zondag","maandag","dinsdag","woensdag","donderdag","vrijdag","zaterdag"];
		    $noWeek = ["Søndag","Mandag","Tirsdag","Onsdag","Torsdag","Fredag","Lørdag"];
		    $paWeek = ["ਐਤਵਾਰ","ਸੋਮਵਾਰ","ਮੰਗਲਵਾਰ","ਬੁੱਧਵਾਰ","ਵੀਰਵਾਰ","ਸ਼ੁੱਕਰਵਾਰ","ਸ਼ਨਿੱਚਰਵਾਰ"];
		    $plWeek = ["Niedziela","Poniedziałek","Wtorek","Środa","Czwartek","Piątek","Sobota"];
		    $ptWeek = ["Domingo","Segunda-feira","Terça-feira","Quarta-feira","Quinta-feira","Sexta-feira","Sábado"];
		    $roWeek = ["Duminică","Luni","Marți","Miercuri","Joi","Vineri","Sâmbătă"];
		    $ruWeek = ["Воскресенье","Понедельник","Вторник","Среда","Четверг","Пятница","Суббота"];
		    $skWeek = ["Nedeľa","Pondelok","Utorok","Streda","Štvrtok","Piatok","Sobota"];
		    $slWeek = ["Nedelja","Ponedeljek","Torek","Sreda","Četrtek","Petek","Sobota"];
		    $siWeek = ["ඉරිදා","සඳුදා","අඟහරුවාදා","බදාදා","බ්‍රහස්පතින්දා","සිකුරාදා","සෙනසුරාදා"];
		    $sqWeek = ["E Diel","E Hënë","E Martë","E Mërkurë","E Enjte","E Premte","E Shtunë"];
		    $srcyrWeek = ["Недеља","Понедељак","Уторак","Среда","Четвртак","Петак","Субота"];
		    $srWeek = ["Nedelja","Ponedeljak","Utorak","Sreda","Četvrtak","Petak","Subota"];
		    $svWeek = ["Söndag","Måndag","Tisdag","Onsdag","Torsdag","Fredag","Lördag"];
		    $thWeek = ["อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์"];
		    $trWeek = ["Pazar","Pazartesi","Salı","Çarşamba","Perşembe","Cuma","Cumartesi"];
		    $ukWeek = ["Неділя","Понеділок","Вівторок","Середа","Четвер","П'ятниця","Субота"];
		    $uzWeek = ["Якшанба","Душанба","Сешанба","Чоршанба","Пайшанба","Жума","Шанба"];
		    $uzlatnWeek = ["Yakshanba","Dushanba","Seshanba","Chorshanba","Payshanba","Juma","Shanba"];
		    $vnWeek = ["Chủ nhật","Thứ hai","Thứ ba","Thứ tư","Thứ năm","Thứ sáu","Thứ bảy"];
		    $zhWeek = ["星期日","星期一","星期二","星期三","星期四","星期五","星期六"];
		    
		    if($delivery_type == "delivery") {
		    	$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
				$calendar_locale = (isset($delivery_date_settings['calendar_locale']) && !empty($delivery_date_settings['calendar_locale'])) ? $delivery_date_settings['calendar_locale'] : "default";
		    } elseif ($delivery_type == "pickup") {
		    	$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
				$calendar_locale = (isset($pickup_date_settings['calendar_locale']) && !empty($pickup_date_settings['calendar_locale'])) ? $pickup_date_settings['calendar_locale'] : "default";
		    }


			switch ($calendar_locale) {
			    case "ar":
			        return str_replace($enWeek, $arWeek, $date);
			        break;
			    case "at":
			        return str_replace($enWeek, $atWeek, $date);
			        break;
			    case "az":
			        return str_replace($enWeek, $azWeek, $date);
			        break;
			    case "be":
			        return str_replace($enWeek, $beWeek, $date);
			        break;
			    case "bg":
			        return str_replace($enWeek, $bgWeek, $date);
			        break;
			    case "bn":
			        return str_replace($enWeek, $bnWeek, $date);
			        break;
			    case "bs":
			        return str_replace($enWeek, $bsWeek, $date);
			        break;
			    case "cat":
			        return str_replace($enWeek, $catWeek, $date);
			        break;
			    case "cs":
			        return str_replace($enWeek, $csWeek, $date);
			        break;
			    case "cy":
			        return str_replace($enWeek, $cyWeek, $date);
			        break;
			    case "da":
			        return str_replace($enWeek, $daWeek, $date);
			        break;
			    case "de":
			        return str_replace($enWeek, $deWeek, $date);
			        break;
			    case "default":
			        return str_replace($enWeek, $defaultWeek, $date);
			        break;
			    case "eo":
			        return str_replace($enWeek, $eoWeek, $date);
			        break;
			    case "es":
			        return str_replace($enWeek, $esWeek, $date);
			        break;
			    case "et":
			        return str_replace($enWeek, $etWeek, $date);
			        break;
			    case "fi":
			        return str_replace($enWeek, $fiWeek, $date);
			        break;
			    case "fo":
			        return str_replace($enWeek, $foWeek, $date);
			        break;
			    case "fr":
			        return str_replace($enWeek, $frWeek, $date);
			        break;
			    case "fa":
			        return str_replace($enWeek, $faWeek, $date);
			        break;
			    case "ga":
			        return str_replace($enWeek, $gaWeek, $date);
			        break;
			    case "gr":
			        return str_replace($enWeek, $grWeek, $date);
			        break;
			    case "he":
			        return str_replace($enWeek, $heWeek, $date);
			        break;
			    case "hi":
			        return str_replace($enWeek, $hiWeek, $date);
			        break;
			    case "hr":
			        return str_replace($enWeek, $hrWeek, $date);
			        break;
			    case "hu":
			        return str_replace($enWeek, $huWeek, $date);
			        break;
			    case "id":
			        return str_replace($enWeek, $idWeek, $date);
			        break;
			    case "is":
			        return str_replace($enWeek, $isWeek, $date);
			        break;
			    case "it":
			        return str_replace($enWeek, $itWeek, $date);
			        break;
			    case "ja":
			        return str_replace($enWeek, $jaWeek, $date);
			        break;
			    case "ka":
			        return str_replace($enWeek, $kaWeek, $date);
			        break;
			    case "km":
			        return str_replace($enWeek, $kmWeek, $date);
			        break;
			    case "ko":
			        return str_replace($enWeek, $koWeek, $date);
			        break;
			    case "kz":
			        return str_replace($enWeek, $kzWeek, $date);
			        break;
			    case "lt":
			        return str_replace($enWeek, $ltWeek, $date);
			        break;
			    case "lv":
			        return str_replace($enWeek, $lvWeek, $date);
			        break;
			    case "mk":
			        return str_replace($enWeek, $mkWeek, $date);
			        break;
			    case "mn":
			        return str_replace($enWeek, $mnWeek, $date);
			        break;
			    case "ms":
			        return str_replace($enWeek, $msWeek, $date);
			        break;
			    case "my":
			        return str_replace($enWeek, $myWeek, $date);
			        break;
			    case "nl":
			        return str_replace($enWeek, $nlWeek, $date);
			        break;
			    case "no":
			        return str_replace($enWeek, $noWeek, $date);
			        break;
			    case "pa":
			        return str_replace($enWeek, $paWeek, $date);
			        break;
			    case "pl":
			        return str_replace($enWeek, $plWeek, $date);
			        break;
			    case "pt":
			        return str_replace($enWeek, $ptWeek, $date);
			        break;
			    case "ro":
			        return str_replace($enWeek, $roWeek, $date);
			        break;
			    case "ru":
			        return str_replace($enWeek, $ruWeek, $date);
			        break;
			    case "sk":
			        return str_replace($enWeek, $skWeek, $date);
			        break;
			    case "sl":
			        return str_replace($enWeek, $slWeek, $date);
			        break;
			    case "si":
			        return str_replace($enWeek, $siWeek, $date);
			        break;
			    case "sq":
			        return str_replace($enWeek, $sqWeek, $date);
			        break;
			    case "sr-cyr":
			        return str_replace($enWeek, $srcyrWeek, $date);
			        break;
			    case "sr":
			        return str_replace($enWeek, $srWeek, $date);
			        break;
			    case "sv":
			        return str_replace($enWeek, $svWeek, $date);
			        break;
			    case "th":
			        return str_replace($enWeek, $thWeek, $date);
			        break;
			    case "tr":
			        return str_replace($enWeek, $trWeek, $date);
			        break;
			    case "uk":
			        return str_replace($enWeek, $ukWeek, $date);
			        break;
			    case "uz":
			        return str_replace($enWeek, $uzWeek, $date);
			        break;
			    case "uz-latn":
			        return str_replace($enWeek, $uzlatnWeek, $date);
			        break;
			    case "vn":
			        return str_replace($enWeek, $vnWeek, $date);
			        break;
			    case "zh":
			        return str_replace($enWeek, $zhWeek, $date);
			        break;

			}
		    
		}

		public function date_conversion( $date, $delivery_type="delivery" ) {
			$arMonths = ["يناير","فبراير","مارس","أبريل","مايو","يونيو","يوليو","أغسطس","سبتمبر","أكتوبر","نوفمبر","ديسمبر"];
			$atMonths = ["Jänner","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"];
		    $azMonths = ["Yanvar","Fevral","Mart","Aprel","May","İyun","İyul","Avqust","Sentyabr","Oktyabr","Noyabr","Dekabr"];
			$beMonths = ["Студзень","Люты","Сакавік","Красавік","Травень","Чэрвень","Ліпень","Жнівень","Верасень","Кастрычнік","Лістапад","Снежань"];
			$bgMonths = ["Януари","Февруари","Март","Април","Май","Юни","Юли","Август","Септември","Октомври","Ноември","Декември"];
		    $bnMonths = ["জানুয়ারী","ফেব্রুয়ারী","মার্চ","এপ্রিল","মে","জুন","জুলাই","আগস্ট","সেপ্টেম্বর","অক্টোবর","নভেম্বর","ডিসেম্বর"];
		    $bsMonths = ["Januar","Februar","Mart","April","Maj","Juni","Juli","Avgust","Septembar","Oktobar","Novembar","Decembar"];
		    $catMonths = ["Gener","Febrer","Març","Abril","Maig","Juny","Juliol","Agost","Setembre","Octubre","Novembre","Desembre"];
		    $csMonths = ["Leden","Únor","Březen","Duben","Květen","Červen","Červenec","Srpen","Září","Říjen","Listopad","Prosinec"];
		    $cyMonths = ["Ionawr","Chwefror","Mawrth","Ebrill","Mai","Mehefin","Gorffennaf","Awst","Medi","Hydref","Tachwedd","Rhagfyr"];
		    $daMonths = ["januar","februar","marts","april","maj","juni","juli","august","september","oktober","november","december"];
		    $deMonths = ["Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"];
		    $defaultMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		    $eoMonths = ["januaro","februaro","marto","aprilo","majo","junio","julio","aŭgusto","septembro","oktobro","novembro","decembro"];
		    $esMonths = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
		    $etMonths = ["Jaanuar","Veebruar","Märts","Aprill","Mai","Juuni","Juuli","August","September","Oktoober","November","Detsember"];
		    $enMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		    $fiMonths = ["Tammikuu", "Helmikuu", "Maaliskuu", "Huhtikuu", "Toukokuu", "Kesäkuu", "Heinäkuu", "Elokuu", "Syyskuu", "Lokakuu", "Marraskuu", "Joulukuu"];
		    $foMonths = ["Januar", "Februar", "Mars", "Apríl", "Mai", "Juni", "Juli", "August", "Septembur", "Oktobur", "Novembur", "Desembur"];
		    $frMonths = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];
		    $faMonths = ["ژانویه", "فوریه", "مارس", "آوریل", "مه", "ژوئن", "ژوئیه", "اوت", "سپتامبر", "اکتبر", "نوامبر", "دسامبر"];
		    $gaMonths = ["Eanáir", "Feabhra", "Márta", "Aibreán", "Bealtaine", "Meitheamh", "Iúil", "Lúnasa", "Meán Fómhair", "Deireadh Fómhair", "Samhain", "Nollaig"];
		    $grMonths = ["Ιανουάριος", "Φεβρουάριος", "Μάρτιος", "Απρίλιος", "Μάιος", "Ιούνιος", "Ιούλιος", "Αύγουστος", "Σεπτέμβριος", "Οκτώβριος", "Νοέμβριος", "Δεκέμβριος"];
		    $heMonths = ["ינואר", "פברואר", "מרץ", "אפריל", "מאי", "יוני", "יולי", "אוגוסט", "ספטמבר", "אוקטובר", "נובמבר", "דצמבר"];
		    $hiMonths = ["जनवरी ", "फरवरी", "मार्च", "अप्रेल", "मई", "जून", "जूलाई", "अगस्त ", "सितम्बर", "अक्टूबर", "नवम्बर", "दिसम्बर"];
		    $hrMonths = ["Siječanj", "Veljača", "Ožujak", "Travanj", "Svibanj", "Lipanj", "Srpanj", "Kolovoz", "Rujan", "Listopad", "Studeni", "Prosinac"];
		    $huMonths = ["Január", "Február", "Március", "Április", "Május", "Június", "Július", "Augusztus", "Szeptember", "Október", "November", "December"];
		    $idMonths = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
		    $isMonths = ["Janúar", "Febrúar", "Mars", "Apríl", "Maí", "Júní", "Júlí", "Ágúst", "September", "Október", "Nóvember", "Desember"];
		    $itMonths = ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"];
		    $jaMonths = ["01月", "02月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"];
		    $kaMonths = ["იანვარი", "თებერვალი", "მარტი", "აპრილი", "მაისი", "ივნისი", "ივლისი", "აგვისტო", "სექტემბერი", "ოქტომბერი", "ნოემბერი", "დეკემბერი"];
		    $kmMonths = ["មករា", "កុម្ភះ", "មីនា", "មេសា", "ឧសភា", "មិថុនា", "កក្កដា", "សីហា", "កញ្ញា", "តុលា", "វិច្ឆិកា", "ធ្នូ"];
		    $koMonths = ["01월", "02월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"];
		    $kzMonths = ["Қаңтар", "Ақпан", "Наурыз", "Сәуiр", "Мамыр", "Маусым", "Шiлде", "Тамыз", "Қыркүйек", "Қазан", "Қараша", "Желтоқсан"];
		    $ltMonths = ["Sausis", "Vasaris", "Kovas", "Balandis", "Gegužė", "Birželis", "Liepa", "Rugpjūtis", "Rugsėjis", "Spalis", "Lapkritis", "Gruodis"];
		    $lvMonths = ["Janvāris", "Februāris", "Marts", "Aprīlis", "Maijs", "Jūnijs", "Jūlijs", "Augusts", "Septembris", "Oktobris", "Novembris", "Decembris"];
		    $mkMonths = ["Јануари", "Февруари", "Март", "Април", "Мај", "Јуни", "Јули", "Август", "Септември", "Октомври", "Ноември", "Декември"];
		    $mnMonths = ["Нэгдүгээр сар", "Хоёрдугаар сар", "Гуравдугаар сар", "Дөрөвдүгээр сар", "Тавдугаар сар", "Зургаадугаар сар", "Долдугаар сар", "Наймдугаар сар", "Есдүгээр сар", "Аравдугаар сар", "Арваннэгдүгээр сар", "Арванхоёрдугаар сар"];
		    $msMonths = ["Januari", "Februari", "Mac", "April", "Mei", "Jun", "Julai", "Ogos", "September", "Oktober", "November", "Disember"];
		    $myMonths = ["ဇန်နဝါရီ", "ဖေဖော်ဝါရီ", "မတ်", "ဧပြီ", "မေ", "ဇွန်", "ဇူလိုင်", "သြဂုတ်", "စက်တင်ဘာ", "အောက်တိုဘာ", "နိုဝင်ဘာ", "ဒီဇင်ဘာ"];
		    $nlMonths = ["januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december"];
		    $noMonths = ["Januar", "Februar", "Mars", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Desember"];
		    $paMonths = ["ਜਨਵਰੀ", "ਫ਼ਰਵਰੀ", "ਮਾਰਚ", "ਅਪ੍ਰੈਲ", "ਮਈ", "ਜੂਨ", "ਜੁਲਾਈ", "ਅਗਸਤ", "ਸਤੰਬਰ", "ਅਕਤੂਬਰ", "ਨਵੰਬਰ", "ਦਸੰਬਰ"];
		    $plMonths = ["Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec", "Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień"];
		    $ptMonths = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
		    $roMonths = ["Ianuarie", "Februarie", "Martie", "Aprilie", "Mai", "Iunie", "Iulie", "August", "Septembrie", "Octombrie", "Noiembrie", "Decembrie"];
		    $ruMonths = ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"];
		    $skMonths = [ "Január", "Február", "Marec", "Apríl", "Máj", "Jún", "Júl", "August", "September", "Október", "November", "December"];
		    $slMonths = ["Januar", "Februar", "Marec", "April", "Maj", "Junij", "Julij", "Avgust", "September", "Oktober", "November", "December"];
		    $siMonths = ["ජනවාරි", "පෙබරවාරි", "මාර්තු", "අප්‍රේල්", "මැයි", "ජුනි", "ජූලි", "අගෝස්තු", "සැප්තැම්බර්", "ඔක්තෝබර්", "නොවැම්බර්", "දෙසැම්බර්"];
		    $sqMonths = ["Janar", "Shkurt", "Mars", "Prill", "Maj", "Qershor", "Korrik", "Gusht", "Shtator", "Tetor", "Nëntor", "Dhjetor"];
		    $srcyrMonths = ["Јануар", "Фебруар", "Март", "Април", "Мај", "Јун", "Јул", "Август", "Септембар", "Октобар", "Новембар", "Децембар"];
		    $srMonths = ["Januar", "Februar", "Mart", "April", "Maj", "Jun", "Jul", "Avgust", "Septembar", "Oktobar", "Novembar", "Decembar"];
		    $svMonths = ["Januari", "Februari", "Mars", "April", "Maj", "Juni", "Juli", "Augusti", "September", "Oktober", "November", "December"];
		    $thMonths = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
		    $trMonths = ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"];
		    $ukMonths = ["Січень", "Лютий", "Березень", "Квітень", "Травень", "Червень", "Липень", "Серпень", "Вересень", "Жовтень", "Листопад", "Грудень"];
		    $uzMonths = ["Январ", "Феврал", "Март", "Апрел", "Май", "Июн", "Июл", "Август", "Сентябр", "Октябр", "Ноябр", "Декабр"];
		    $uzlatnMonths = ["Yanvar", "Fevral", "Mart", "Aprel", "May", "Iyun", "Iyul", "Avgust", "Sentabr", "Oktabr", "Noyabr", "Dekabr"];
		    $vnMonths = ["Tháng một", "Tháng hai", "Tháng ba", "Tháng tư", "Tháng năm", "Tháng sáu", "Tháng bảy", "Tháng tám", "Tháng chín", "Tháng mười", "Tháng 11", "Tháng 12"];
		    $zhMonths = ["十一月", "一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十二月"];

		    $enzhMonths = ['November', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'December'];
		    
		    
		    if($delivery_type == "delivery") {
		    	$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
				$calendar_locale = (isset($delivery_date_settings['calendar_locale']) && !empty($delivery_date_settings['calendar_locale'])) ? $delivery_date_settings['calendar_locale'] : "default";
		    } elseif ($delivery_type == "pickup") {
		    	$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
				$calendar_locale = (isset($pickup_date_settings['calendar_locale']) && !empty($pickup_date_settings['calendar_locale'])) ? $pickup_date_settings['calendar_locale'] : "default";
		    }
		    

			switch ($calendar_locale) {
			    case "ar":
			        return str_replace($arMonths, $enMonths, $date);
			        break;
			    case "at":
			        return str_replace($atMonths, $enMonths, $date);
			        break;
			    case "az":
			        return str_replace($azMonths, $enMonths, $date);
			        break;
			    case "be":
			        return str_replace($beMonths, $enMonths, $date);
			        break;
			    case "bg":
			        return str_replace($bgMonths, $enMonths, $date);
			        break;
			    case "bn":
			        return str_replace($bnMonths, $enMonths, $date);
			        break;
			    case "bs":
			        return str_replace($bsMonths, $enMonths, $date);
			        break;
			    case "cat":
			        return str_replace($catMonths, $enMonths, $date);
			        break;
			    case "cs":
			        return str_replace($csMonths, $enMonths, $date);
			        break;
			    case "cy":
			        return str_replace($cyMonths, $enMonths, $date);
			        break;
			    case "da":
			        return str_replace($daMonths, $enMonths, $date);
			        break;
			    case "de":
			        return str_replace($deMonths, $enMonths, $date);
			        break;
			    case "default":
			        return str_replace($defaultMonths, $enMonths, $date);
			        break;
			    case "eo":
			        return str_replace($eoMonths, $enMonths, $date);
			        break;
			    case "es":
			        return str_replace($esMonths, $enMonths, $date);
			        break;
			    case "et":
			        return str_replace($etMonths, $enMonths, $date);
			        break;
			    case "fi":
			        return str_replace($fiMonths, $enMonths, $date);
			        break;
			    case "fo":
			        return str_replace($foMonths, $enMonths, $date);
			        break;
			    case "fr":
			        return str_replace($frMonths, $enMonths, $date);
			        break;
			    case "fa":
			        return str_replace($faMonths, $enMonths, $date);
			        break;
			    case "ga":
			        return str_replace($gaMonths, $enMonths, $date);
			        break;
			    case "gr":
			        return str_replace($grMonths, $enMonths, $date);
			        break;
			    case "he":
			        return str_replace($heMonths, $enMonths, $date);
			        break;
			    case "hi":
			        return str_replace($hiMonths, $enMonths, $date);
			        break;
			    case "hr":
			        return str_replace($hrMonths, $enMonths, $date);
			        break;
			    case "hu":
			        return str_replace($huMonths, $enMonths, $date);
			        break;
			    case "id":
			        return str_replace($idMonths, $enMonths, $date);
			        break;
			    case "is":
			        return str_replace($isMonths, $enMonths, $date);
			        break;
			    case "it":
			        return str_replace($itMonths, $enMonths, $date);
			        break;
			    case "ja":
			        return str_replace($jaMonths, $enMonths, $date);
			        break;
			    case "ka":
			        return str_replace($kaMonths, $enMonths, $date);
			        break;
			    case "km":
			        return str_replace($kmMonths, $enMonths, $date);
			        break;
			    case "ko":
			        return str_replace($koMonths, $enMonths, $date);
			        break;
			    case "kz":
			        return str_replace($kzMonths, $enMonths, $date);
			        break;
			    case "lt":
			        return str_replace($ltMonths, $enMonths, $date);
			        break;
			    case "lv":
			        return str_replace($lvMonths, $enMonths, $date);
			        break;
			    case "mk":
			        return str_replace($mkMonths, $enMonths, $date);
			        break;
			    case "mn":
			        return str_replace($mnMonths, $enMonths, $date);
			        break;
			    case "ms":
			        return str_replace($msMonths, $enMonths, $date);
			        break;
			    case "my":
			        return str_replace($myMonths, $enMonths, $date);
			        break;
			    case "nl":
			        return str_replace($nlMonths, $enMonths, $date);
			        break;
			    case "no":
			        return str_replace($noMonths, $enMonths, $date);
			        break;
			    case "pa":
			        return str_replace($paMonths, $enMonths, $date);
			        break;
			    case "pl":
			        return str_replace($plMonths, $enMonths, $date);
			        break;
			    case "pt":
			        return str_replace($ptMonths, $enMonths, $date);
			        break;
			    case "ro":
			        return str_replace($roMonths, $enMonths, $date);
			        break;
			    case "ru":
			        return str_replace($ruMonths, $enMonths, $date);
			        break;
			    case "sk":
			        return str_replace($skMonths, $enMonths, $date);
			        break;
			    case "sl":
			        return str_replace($slMonths, $enMonths, $date);
			        break;
			    case "si":
			        return str_replace($siMonths, $enMonths, $date);
			        break;
			    case "sq":
			        return str_replace($sqMonths, $enMonths, $date);
			        break;
			    case "sr-cyr":
			        return str_replace($srcyrMonths, $enMonths, $date);
			        break;
			    case "sr":
			        return str_replace($srMonths, $enMonths, $date);
			        break;
			    case "sv":
			        return str_replace($svMonths, $enMonths, $date);
			        break;
			    case "th":
			        return str_replace($thMonths, $enMonths, $date);
			        break;
			    case "tr":
			        return str_replace($trMonths, $enMonths, $date);
			        break;
			    case "uk":
			        return str_replace($ukMonths, $enMonths, $date);
			        break;
			    case "uz":
			        return str_replace($uzMonths, $enMonths, $date);
			        break;
			    case "uz-latn":
			        return str_replace($uzlatnMonths, $enMonths, $date);
			        break;
			    case "vn":
			        return str_replace($vnMonths, $enMonths, $date);
			        break;
			    case "zh":
			        return str_replace($zhMonths, $enzhMonths, $date);
			        break;

			}
		    
		}

		public function date_conversion_to_locale( $date, $delivery_type="delivery" ) {
			$arMonths = ["يناير","فبراير","مارس","أبريل","مايو","يونيو","يوليو","أغسطس","سبتمبر","أكتوبر","نوفمبر","ديسمبر"];
			$atMonths = ["Jänner","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"];
		    $azMonths = ["Yanvar","Fevral","Mart","Aprel","May","İyun","İyul","Avqust","Sentyabr","Oktyabr","Noyabr","Dekabr"];
			$beMonths = ["Студзень","Люты","Сакавік","Красавік","Травень","Чэрвень","Ліпень","Жнівень","Верасень","Кастрычнік","Лістапад","Снежань"];
			$bgMonths = ["Януари","Февруари","Март","Април","Май","Юни","Юли","Август","Септември","Октомври","Ноември","Декември"];
		    $bnMonths = ["জানুয়ারী","ফেব্রুয়ারী","মার্চ","এপ্রিল","মে","জুন","জুলাই","আগস্ট","সেপ্টেম্বর","অক্টোবর","নভেম্বর","ডিসেম্বর"];
		    $bsMonths = ["Januar","Februar","Mart","April","Maj","Juni","Juli","Avgust","Septembar","Oktobar","Novembar","Decembar"];
		    $catMonths = ["Gener","Febrer","Març","Abril","Maig","Juny","Juliol","Agost","Setembre","Octubre","Novembre","Desembre"];
		    $csMonths = ["Leden","Únor","Březen","Duben","Květen","Červen","Červenec","Srpen","Září","Říjen","Listopad","Prosinec"];
		    $cyMonths = ["Ionawr","Chwefror","Mawrth","Ebrill","Mai","Mehefin","Gorffennaf","Awst","Medi","Hydref","Tachwedd","Rhagfyr"];
		    $daMonths = ["januar","februar","marts","april","maj","juni","juli","august","september","oktober","november","december"];
		    $deMonths = ["Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"];
		    $defaultMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		    $eoMonths = ["januaro","februaro","marto","aprilo","majo","junio","julio","aŭgusto","septembro","oktobro","novembro","decembro"];
		    $esMonths = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
		    $etMonths = ["Jaanuar","Veebruar","Märts","Aprill","Mai","Juuni","Juuli","August","September","Oktoober","November","Detsember"];
		    $enMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		    $fiMonths = ["Tammikuu", "Helmikuu", "Maaliskuu", "Huhtikuu", "Toukokuu", "Kesäkuu", "Heinäkuu", "Elokuu", "Syyskuu", "Lokakuu", "Marraskuu", "Joulukuu"];
		    $foMonths = ["Januar", "Februar", "Mars", "Apríl", "Mai", "Juni", "Juli", "August", "Septembur", "Oktobur", "Novembur", "Desembur"];
		    $frMonths = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];
		    $faMonths = ["ژانویه", "فوریه", "مارس", "آوریل", "مه", "ژوئن", "ژوئیه", "اوت", "سپتامبر", "اکتبر", "نوامبر", "دسامبر"];
		    $gaMonths = ["Eanáir", "Feabhra", "Márta", "Aibreán", "Bealtaine", "Meitheamh", "Iúil", "Lúnasa", "Meán Fómhair", "Deireadh Fómhair", "Samhain", "Nollaig"];
		    $grMonths = ["Ιανουάριος", "Φεβρουάριος", "Μάρτιος", "Απρίλιος", "Μάιος", "Ιούνιος", "Ιούλιος", "Αύγουστος", "Σεπτέμβριος", "Οκτώβριος", "Νοέμβριος", "Δεκέμβριος"];
		    $heMonths = ["ינואר", "פברואר", "מרץ", "אפריל", "מאי", "יוני", "יולי", "אוגוסט", "ספטמבר", "אוקטובר", "נובמבר", "דצמבר"];
		    $hiMonths = ["जनवरी ", "फरवरी", "मार्च", "अप्रेल", "मई", "जून", "जूलाई", "अगस्त ", "सितम्बर", "अक्टूबर", "नवम्बर", "दिसम्बर"];
		    $hrMonths = ["Siječanj", "Veljača", "Ožujak", "Travanj", "Svibanj", "Lipanj", "Srpanj", "Kolovoz", "Rujan", "Listopad", "Studeni", "Prosinac"];
		    $huMonths = ["Január", "Február", "Március", "Április", "Május", "Június", "Július", "Augusztus", "Szeptember", "Október", "November", "December"];
		    $idMonths = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
		    $isMonths = ["Janúar", "Febrúar", "Mars", "Apríl", "Maí", "Júní", "Júlí", "Ágúst", "September", "Október", "Nóvember", "Desember"];
		    $itMonths = ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"];
		    $jaMonths = ["01月", "02月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"];
		    $kaMonths = ["იანვარი", "თებერვალი", "მარტი", "აპრილი", "მაისი", "ივნისი", "ივლისი", "აგვისტო", "სექტემბერი", "ოქტომბერი", "ნოემბერი", "დეკემბერი"];
		    $kmMonths = ["មករា", "កុម្ភះ", "មីនា", "មេសា", "ឧសភា", "មិថុនា", "កក្កដា", "សីហា", "កញ្ញា", "តុលា", "វិច្ឆិកា", "ធ្នូ"];
		    $koMonths = ["01월", "02월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"];
		    $kzMonths = ["Қаңтар", "Ақпан", "Наурыз", "Сәуiр", "Мамыр", "Маусым", "Шiлде", "Тамыз", "Қыркүйек", "Қазан", "Қараша", "Желтоқсан"];
		    $ltMonths = ["Sausis", "Vasaris", "Kovas", "Balandis", "Gegužė", "Birželis", "Liepa", "Rugpjūtis", "Rugsėjis", "Spalis", "Lapkritis", "Gruodis"];
		    $lvMonths = ["Janvāris", "Februāris", "Marts", "Aprīlis", "Maijs", "Jūnijs", "Jūlijs", "Augusts", "Septembris", "Oktobris", "Novembris", "Decembris"];
		    $mkMonths = ["Јануари", "Февруари", "Март", "Април", "Мај", "Јуни", "Јули", "Август", "Септември", "Октомври", "Ноември", "Декември"];
		    $mnMonths = ["Нэгдүгээр сар", "Хоёрдугаар сар", "Гуравдугаар сар", "Дөрөвдүгээр сар", "Тавдугаар сар", "Зургаадугаар сар", "Долдугаар сар", "Наймдугаар сар", "Есдүгээр сар", "Аравдугаар сар", "Арваннэгдүгээр сар", "Арванхоёрдугаар сар"];
		    $msMonths = ["Januari", "Februari", "Mac", "April", "Mei", "Jun", "Julai", "Ogos", "September", "Oktober", "November", "Disember"];
		    $myMonths = ["ဇန်နဝါရီ", "ဖေဖော်ဝါရီ", "မတ်", "ဧပြီ", "မေ", "ဇွန်", "ဇူလိုင်", "သြဂုတ်", "စက်တင်ဘာ", "အောက်တိုဘာ", "နိုဝင်ဘာ", "ဒီဇင်ဘာ"];
		    $nlMonths = ["januari", "februari", "maart", "april", "mei", "juni", "juli", "augustus", "september", "oktober", "november", "december"];
		    $noMonths = ["Januar", "Februar", "Mars", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Desember"];
		    $paMonths = ["ਜਨਵਰੀ", "ਫ਼ਰਵਰੀ", "ਮਾਰਚ", "ਅਪ੍ਰੈਲ", "ਮਈ", "ਜੂਨ", "ਜੁਲਾਈ", "ਅਗਸਤ", "ਸਤੰਬਰ", "ਅਕਤੂਬਰ", "ਨਵੰਬਰ", "ਦਸੰਬਰ"];
		    $plMonths = ["Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec", "Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień"];
		    $ptMonths = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
		    $roMonths = ["Ianuarie", "Februarie", "Martie", "Aprilie", "Mai", "Iunie", "Iulie", "August", "Septembrie", "Octombrie", "Noiembrie", "Decembrie"];
		    $ruMonths = ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"];
		    $skMonths = [ "Január", "Február", "Marec", "Apríl", "Máj", "Jún", "Júl", "August", "September", "Október", "November", "December"];
		    $slMonths = ["Januar", "Februar", "Marec", "April", "Maj", "Junij", "Julij", "Avgust", "September", "Oktober", "November", "December"];
		    $siMonths = ["ජනවාරි", "පෙබරවාරි", "මාර්තු", "අප්‍රේල්", "මැයි", "ජුනි", "ජූලි", "අගෝස්තු", "සැප්තැම්බර්", "ඔක්තෝබර්", "නොවැම්බර්", "දෙසැම්බර්"];
		    $sqMonths = ["Janar", "Shkurt", "Mars", "Prill", "Maj", "Qershor", "Korrik", "Gusht", "Shtator", "Tetor", "Nëntor", "Dhjetor"];
		    $srcyrMonths = ["Јануар", "Фебруар", "Март", "Април", "Мај", "Јун", "Јул", "Август", "Септембар", "Октобар", "Новембар", "Децембар"];
		    $srMonths = ["Januar", "Februar", "Mart", "April", "Maj", "Jun", "Jul", "Avgust", "Septembar", "Oktobar", "Novembar", "Decembar"];
		    $svMonths = ["Januari", "Februari", "Mars", "April", "Maj", "Juni", "Juli", "Augusti", "September", "Oktober", "November", "December"];
		    $thMonths = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
		    $trMonths = ["Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık"];
		    $ukMonths = ["Січень", "Лютий", "Березень", "Квітень", "Травень", "Червень", "Липень", "Серпень", "Вересень", "Жовтень", "Листопад", "Грудень"];
		    $uzMonths = ["Январ", "Феврал", "Март", "Апрел", "Май", "Июн", "Июл", "Август", "Сентябр", "Октябр", "Ноябр", "Декабр"];
		    $uzlatnMonths = ["Yanvar", "Fevral", "Mart", "Aprel", "May", "Iyun", "Iyul", "Avgust", "Sentabr", "Oktabr", "Noyabr", "Dekabr"];
		    $vnMonths = ["Tháng một", "Tháng hai", "Tháng ba", "Tháng tư", "Tháng năm", "Tháng sáu", "Tháng bảy", "Tháng tám", "Tháng chín", "Tháng mười", "Tháng 11", "Tháng 12"];
		    $zhMonths = ["十一月", "一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十二月"];

		    $enzhMonths = ['November', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'December'];
		    
		    if($delivery_type == "delivery") {
		    	$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
				$calendar_locale = (isset($delivery_date_settings['calendar_locale']) && !empty($delivery_date_settings['calendar_locale'])) ? $delivery_date_settings['calendar_locale'] : "default";
		    } elseif ($delivery_type == "pickup") {
		    	$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
				$calendar_locale = (isset($pickup_date_settings['calendar_locale']) && !empty($pickup_date_settings['calendar_locale'])) ? $pickup_date_settings['calendar_locale'] : "default";
		    }

			switch ($calendar_locale) {
			    case "ar":
			        return str_replace($enMonths, $arMonths, $date);
			        break;
			    case "at":
			        return str_replace($enMonths, $atMonths, $date);
			        break;
			    case "az":
			        return str_replace($enMonths, $azMonths, $date);
			        break;
			    case "be":
			        return str_replace($enMonths, $beMonths, $date);
			        break;
			    case "bg":
			        return str_replace($enMonths, $bgMonths, $date);
			        break;
			    case "bn":
			        return str_replace($enMonths, $bnMonths, $date);
			        break;
			    case "bs":
			        return str_replace($enMonths, $bsMonths, $date);
			        break;
			    case "cat":
			        return str_replace($enMonths, $catMonths, $date);
			        break;
			    case "cs":
			        return str_replace($enMonths, $csMonths, $date);
			        break;
			    case "cy":
			        return str_replace($enMonths, $cyMonths, $date);
			        break;
			    case "da":
			        return str_replace($enMonths, $daMonths, $date);
			        break;
			    case "de":
			        return str_replace($enMonths, $deMonths, $date);
			        break;
			    case "default":
			        return str_replace($enMonths, $defaultMonths, $date);
			        break;
			    case "eo":
			        return str_replace($enMonths, $eoMonths, $date);
			        break;
			    case "es":
			        return str_replace($enMonths, $esMonths, $date);
			        break;
			    case "et":
			        return str_replace($enMonths, $etMonths, $date);
			        break;
			    case "fi":
			        return str_replace($enMonths, $fiMonths, $date);
			        break;
			    case "fo":
			        return str_replace($enMonths, $foMonths, $date);
			        break;
			    case "fr":
			        return str_replace($enMonths, $frMonths, $date);
			        break;
			    case "fa":
			        return str_replace($enMonths, $faMonths, $date);
			        break;
			    case "ga":
			        return str_replace($enMonths, $gaMonths, $date);
			        break;
			    case "gr":
			        return str_replace($enMonths, $grMonths, $date);
			        break;
			    case "he":
			        return str_replace($enMonths, $heMonths, $date);
			        break;
			    case "hi":
			        return str_replace($enMonths, $hiMonths, $date);
			        break;
			    case "hr":
			        return str_replace($enMonths, $hrMonths, $date);
			        break;
			    case "hu":
			        return str_replace($enMonths, $huMonths, $date);
			        break;
			    case "id":
			        return str_replace($enMonths, $idMonths, $date);
			        break;
			    case "is":
			        return str_replace($enMonths, $isMonths, $date);
			        break;
			    case "it":
			        return str_replace($enMonths, $itMonths, $date);
			        break;
			    case "ja":
			        return str_replace($enMonths, $jaMonths, $date);
			        break;
			    case "ka":
			        return str_replace($enMonths, $kaMonths, $date);
			        break;
			    case "km":
			        return str_replace($enMonths, $kmMonths, $date);
			        break;
			    case "ko":
			        return str_replace($enMonths, $koMonths, $date);
			        break;
			    case "kz":
			        return str_replace($enMonths, $kzMonths, $date);
			        break;
			    case "lt":
			        return str_replace($enMonths, $ltMonths, $date);
			        break;
			    case "lv":
			        return str_replace($enMonths, $lvMonths, $date);
			        break;
			    case "mk":
			        return str_replace($enMonths, $mkMonths, $date);
			        break;
			    case "mn":
			        return str_replace($enMonths, $mnMonths, $date);
			        break;
			    case "ms":
			        return str_replace($enMonths, $msMonths, $date);
			        break;
			    case "my":
			        return str_replace($enMonths, $myMonths, $date);
			        break;
			    case "nl":
			        return str_replace($enMonths, $nlMonths, $date);
			        break;
			    case "no":
			        return str_replace($enMonths, $noMonths, $date);
			        break;
			    case "pa":
			        return str_replace($enMonths, $paMonths, $date);
			        break;
			    case "pl":
			        return str_replace($enMonths, $plMonths, $date);
			        break;
			    case "pt":
			        return str_replace($enMonths, $ptMonths, $date);
			        break;
			    case "ro":
			        return str_replace($enMonths, $roMonths, $date);
			        break;
			    case "ru":
			        return str_replace($enMonths, $ruMonths, $date);
			        break;
			    case "sk":
			        return str_replace($enMonths, $skMonths, $date);
			        break;
			    case "sl":
			        return str_replace($enMonths, $slMonths, $date);
			        break;
			    case "si":
			        return str_replace($enMonths, $siMonths, $date);
			        break;
			    case "sq":
			        return str_replace($enMonths, $sqMonths, $date);
			        break;
			    case "sr-cyr":
			        return str_replace($enMonths, $srcyrMonths, $date);
			        break;
			    case "sr":
			        return str_replace($enMonths, $srMonths, $date);
			        break;
			    case "sv":
			        return str_replace($enMonths, $svMonths, $date);
			        break;
			    case "th":
			        return str_replace($enMonths, $thMonths, $date);
			        break;
			    case "tr":
			        return str_replace($enMonths, $trMonths, $date);
			        break;
			    case "uk":
			        return str_replace($enMonths, $ukMonths, $date);
			        break;
			    case "uz":
			        return str_replace($enMonuzs, $thMonths, $date);
			        break;
			    case "uz-latn":
			        return str_replace($enMonths, $uzlatnMonths, $date);
			        break;
			    case "vn":
			        return str_replace($enMonths, $vnMonths, $date);
			        break;
			    case "zh":
			        return str_replace($enzhMonths, $zhMonths, $date);
			        break;

			}
		    
		}		

	}

}