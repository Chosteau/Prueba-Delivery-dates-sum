<?php

require_once CODEROCKZ_WOO_DELIVERY_DIR . 'includes/class-coderockz-woo-delivery-helper.php';

if( !class_exists( 'Coderockz_Woo_Delivery_Tips_Option' ) ) {
	
	class Coderockz_Woo_Delivery_Tips_Option {

		public static function delivery_tips_option($delivery_tips_settings, $meta_box=null) {

			$helper = new Coderockz_Woo_Delivery_Helper();
			
			$delivery_tips = (isset($delivery_tips_settings['delivery_tips_dropdown_value']) && !empty($delivery_tips_settings['delivery_tips_dropdown_value'])) ? $delivery_tips_settings['delivery_tips_dropdown_value'] : [];
			$delivery_tip = [];

			$enable_input_field_dropdown = (isset($delivery_tips_settings['enable_input_field_dropdown']) && !empty($delivery_tips_settings['enable_input_field_dropdown'])) ? $delivery_tips_settings['enable_input_field_dropdown'] : false;
		
			if(is_null($meta_box)){
				$delivery_tip[''] = '';
				if($enable_input_field_dropdown) {
					$delivery_tip["custom-delivery-tips"] = __('Custom Delivery Tips', 'coderockz-woo-delivery');
				}
			}
			
			if(!empty($delivery_tips)) {

				foreach($delivery_tips as $tips) {
				
					if(strpos($tips, '%') !== false) {

						$delivery_tip[$tips] = $tips;
						
					} else {
						if(class_exists('WOOCS_STARTER')){
							global $WOOCS;
			            	$currencies = $WOOCS->get_currencies();
			            	$currency_symbol = $currencies[$WOOCS->current_currency]['symbol'];
						} else {
							$currency_symbol = get_woocommerce_currency_symbol();
						}

						$delivery_tip[$tips] = $currency_symbol.$tips;
						
					}
					
					
				}
			}

			return $delivery_tip;
		}
	}
}