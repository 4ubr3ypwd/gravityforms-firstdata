<?php

// Setup all the settings pages, etc.
require_once "gffd-gfadmin.php";

//Add the Feed stuff
require_once "gffd-gfadmin-feeds.php";

// Include out terms library and
// keep it as a separate file
// so it's easy to find.
require_once 'gffd-gf-terms.php';

// Include the setup for interrupting
// form data to perform purchases
require_once 'gffd-gf-forms.php';

function gffd_get_validation_message_feed_data($gffd_index){
	$gffd_get_purchase_field_requirements
		=gffd_get_purchase_field_requirements(
			$gffd_index
		);

	if(
		is_callable(
			$gffd_get_purchase_field_requirements
				['validate_bad_message']
		)
	){
		return
			call_user_func(
				$gffd_get_purchase_field_requirements
					['validate_bad_message']
			);
	}else{
		return false;
	}
}

// Use the validation functions against the values stored
// in $_POST.
function gffd_is_valid_pre_format_feed_data($gffd_index, $value){
	$gffd_get_purchase_field_requirements
		=gffd_get_purchase_field_requirements($gffd_index);

	if(is_callable(
		$gffd_get_purchase_field_requirements['validate_pre_format']
	)){
		return
			call_user_func(
				$gffd_get_purchase_field_requirements[
					'validate_pre_format'
				],

				$value
			)
		;
	}else{
		return false;
	}
}

// Converts the float format for fields like 1.1 to input_1_1
// so we can catch them on $_POST
function gffd_convert_float_value_to_post_input($gf_float_value){
	return 'input_'.str_replace(".","_",$gf_float_value);
}

function gffd_feeds_get_form_feed_settings($form_id,$as_object=false){
	$current_form_settings['feed_indexes'] = get_option(
		'gffd_form_'.$form_id.'_feed_indexes'
	);
	$current_form_settings['feed_active'] = get_option(
		'gffd_form_'.$form_id.'_feed_active'
	);

	if(!$as_object){
		return $current_form_settings;
	}elseif($as_object=='as_object'){
		return json_decode(json_encode( $current_form_settings ));
	}
}

// Need to configure this function to figure out if a feed
// is active nor not.
function gffd_feed_is_active($form_id){
	if(
		gffd_feeds_get_form_feed_settings(
			$form_id,
			'as_object'
		)->feed_active == 'active'
	){
		return true;
	}else{
		return false;
	}
}

// Get the Gravity Forms plugin URL
function gffd_plugin_url(){
	$url = plugins_url()
	. '/'
	. basename(GFCommon::get_base_path())
	. '/css/admin.css';

	return $url;
}

// Shorthand way to get a form from
// GF
function gffd_get_form($form_id){
	return RGFormsModel::get_form_meta($form_id);
}

// Enable the CC field in gravity forms.
function gffd_enable_cc(){
	add_filter( 'gform_enable_credit_card_field', '__return_true' );
}

add_action('admin_init','gffd_enable_cc');

// Glossary of things, etc so it's easier!
//
// To use, you can:
//
// gffd_glossary() which will return the whole glossary array.
// gffd_glossary('plugin_name') will return the specific term in the array.
// gffd_glossary('', 'as_object') will give you the glossary as an object
function gffd_glossary($key=null,$as_object=false){

	$glossary=array(
		'plugin_name'=>"Gravity Forms + First Data Global Gateway e4",
		'settings_name'=>"First Data Global Gateway",
		'service_name'=>"First Data Global Gateway",
	);

	if(!$key || $key==''){
		if($as_object){
			return json_decode(json_encode($glossary));
		}else{
			return $glossary;
		}
	}else{
		if($as_object){
			return json_decode(json_encode($glossary[$key]));
		}else{
			return $glossary[$key];
		}
	}
}

$gffd_glossary = gffd_glossary(false, true);

// Convert an array to an object.
function gffd_array_as_object($a){
	return json_decode(json_encode($a));
}

// These are the minimum fields we need to make a purchase.
function gffd_get_purchase_field_requirements(
		$as_object_or_gffd_index=false,
		$as_object=false
){
	$fields=array(

		// Here we package the full customer name
		// from the GF credit card field as one
		// field, which here we call firstname
		array(
			'gffd_index'=>'gffd_fd_cc_firstname',
			'label'=>__('Name'),
			'meta'=>__(
				"Usually called <strong>Credit Card (Cardholder's Name)</strong>"
			),
			'validate_bad_message'=>function(){
				return gffd_language_terms(
					'term_validate_bad_message_most_card_fields'
				);
			},
			'validate_pre_format'=>function($value){
				if($value!=''){
					return $value;
				}else{
					return false;
				}
			}
		),

		// CCtype is different as GF does not submit the cctype,
		// though it can be selected from the feed.
		array(
			'gffd_index'=>'gffd_fd_cc_type',
			'label'=>__('Credit Card Type'),
			'meta'=>__(
				'Usually called <strong>Credit Card (Card Type)</strong>'
			),
			'validate_bad_message'=>function(){
				return gffd_language_terms(
					'term_validate_bad_message_most_card_fields'
				);
			},
			'validate_pre_format'=>function($value){

				// Right now the value that is selected from the form
				// feed setup does not work because GF does not send
				// the cc type when it's submitted like you would think.
				//
				// So, gffd-gf-forms.js should give us a
				// $_REQUEST['gffd_cc_type']

				if(
					gffd_request('gffd_cc_type')
				){
						return
							gffd_request('gffd_cc_type');
				}else{
					return
						false;
				}
			}
		),

		array(
			'gffd_index'=>'gffd_fd_cc_number',
			'label'=>__('Credit Card Number'),
			'meta'=>__(
				'Usually called <strong>Credit Card (Card Number)</strong>'
			),
			'validate_bad_message'=>function(){
				return gffd_language_terms(
					'term_validate_bad_message_most_card_fields'
				);
			},
			'validate_pre_format'=>function($value){
				if($value!=''){
					return $value;
				}else{
					return false;
				}
			}
		),

		// Do some re-formatting for the expiration date.
		array(
			'gffd_index'=>'gffd_fd_cc_exp',
			'label'=>__('Card Expiration Date'),
			'meta'=>__(
				'Usually called <strong>Credit Card (Expiration Date)</strong>'
				),
			'validate_bad_message'=>function(){
				return gffd_language_terms(
					'term_validate_bad_message_most_card_fields'
				);
			},
			'validate_pre_format'=>function($value){
				if(is_array($value)){

					// The value (when submitted by $_POST)
					// is an array:
					//
					// array(2) { [0]=> string(1) "1" [1]=> string(4) "2016" }
					//
					// So, let's chop those two array
					// structs into a value that is good for FD.
					if(isset($value[0]) && isset($value[1])){
						return
							// Tak on a 0 if we have 4, or 5, but not if
							// we have 12
							str_pad($value[0], 2, '0', STR_PAD_LEFT)
							. substr($value[1], -2);
					}else{
						return false;
					}
				}else{
					return false;
				}
			}
		),

		array(
			'gffd_index'=>'gffd_fd_cc_cvv',
			'label'=>__('CVV/Security Code'),
			'meta'=>__(
				'Usually called <strong>Credit Card (Security Code)</strong>'
			),
			'validate_bad_message'=>function(){
				return gffd_language_terms(
					'term_validate_bad_message_most_card_fields'
				);
			},
			'validate_pre_format'=>function($value){
				if($value!=''){
					return $value;
				}else{
					return false;
				}
			}
		),

		// Just make sure amount is a decimal float.
		array(
			'gffd_index'=>'gffd_fd_cc_amount',
			'label'=>__('Charge Amount'),
			'meta'=>__(
				'Here you will need to select a <strong>'
				.'total field</strong> to your form'
			),
			'validate_bad_message'=>function(){
				return gffd_language_terms(
					'term_validate_bad_message_most_card_fields'
				);
			},
			'validate_pre_format'=>function($value){
				if($value!=''){
					return
						//Remove $
						str_replace(
							"$",
							"",
							$value
						);
				}else{
					return false;
				}
			}
		),

		// Address is actually formatted post validation. Here, we just
		// want to make sure we have values. They will be re-formatted
		// for the API like address|city|state|zip, etc later.
		array(
			'gffd_index'=>'gffd_fd_cc_address',
			'label'=>__('Address'),
			'meta'=>gffd_language_terms(
				'you_will_need_address_field_select_here'
			),
			'validate_bad_message'=>function(){
				return gffd_language_terms(
					'term_validate_bad_message_most_address'
				);
			},
			'validate_pre_format'=>function($value){
				if($value!=''){
					return $value;
				}else{
					return false;
				}
			}
		),

		array(
			'gffd_index'=>'gffd_fd_cc_address2',
			'label'=>__('Address2'),
			'meta'=>gffd_language_terms(
				'you_will_need_address_field_select_here'
			),
			'validate_bad_message'=>function(){
				return true;
			},
			'validate_pre_format'=>function($value){
				return true;
			}
		),

		array(
			'gffd_index'=>'gffd_fd_cc_zip',
			'label'=>__('Zip'),
			'meta'=>gffd_language_terms(
				'you_will_need_address_field_select_here'
			),
			'validate_bad_message'=>function(){
				return gffd_language_terms(
					'term_validate_bad_message_most_address'
				);
			},
			'validate_pre_format'=>function($value){
				if($value!=''){
					return $value;
				}else{
					return false;
				}
			}
		),

		array(
			'gffd_index'=>'gffd_fd_cc_city',
			'label'=>__('City'),
			'meta'=>gffd_language_terms(
				'you_will_need_address_field_select_here'
			),
			'validate_bad_message'=>function(){
				return gffd_language_terms(
					'term_validate_bad_message_most_address'
				);
			},
			'validate_pre_format'=>function($value){
				if($value!=''){
					return $value;
				}else{
					return false;
				}
			}
		),

		array(
			'gffd_index'=>'gffd_fd_cc_state',
			'label'=>__('State'),
			'meta'=>gffd_language_terms(
				'you_will_need_address_field_select_here'
			),
			'validate_bad_message'=>function(){
				return gffd_language_terms(
					'term_validate_bad_message_most_address'
				);
			},
			'validate_pre_format'=>function($value){
				if($value!=''){
					return $value;
				}else{
					return false;
				}
			}
		),

		// Just make US the default, if they submit country, use that.
		array(
			'gffd_index'=>'gffd_fd_cc_country',
			'label'=>__('Country'),
			'meta'=>gffd_language_terms(
				'you_will_need_address_field_select_here'
			),
			'validate_bad_message'=>function(){
				return gffd_language_terms(
					'term_validate_bad_message_most_address'
				);
			},
			'validate_pre_format'=>function($value){
				if($value!=''){
					return $value;
				}else{
					return "US";
				}
			}
		)
	);

	// If they ask for 'gffd_fd_cc_zip' or 'as_object'
	if(is_string($as_object_or_gffd_index)){

		// If it is 'as_object'
		if($as_object_or_gffd_index=='as_object'){
			return gffd_array_as_object(
				$fields
			);

		// If it's not 'as_object', let's assume
		// it's a gffd_index.
		}else{

			//Pull out just the field they are asking for.
			foreach($fields as $field){
				if($field['gffd_index']==$as_object_or_gffd_index){
					$_the_field = $field;
				}
			}

			// If they also said, 'gffd_fd_cc_zip', 'as_object',
			// return as an object.
			if($as_object){
				return gffd_array_as_object(
					$_the_field
				);

			// If they just said 'gffd_fd_cc_zip' then return
			// it as an array
			}else{
				return $_the_field;
			}
		}

	// If they pass (true), then just pass as an
	// object.
	}elseif($as_object_or_gffd_index==true){
		return gffd_array_as_object(
			$fields
		);

	// If they just pass () then return
	// as an array.
	}else{
		return $fields;
	}
}

// Check and make sure that everything
// is setup for a purchase.
function gffd_is_setup(){
	if(
		// We are going to need the  gateway id (login).
		get_option('gffd_gateway_id')

		// We are going to also need the password.
		&& get_option('gffd_gateway_password')

		// The test option may be off,
		// in that case it may not exist, which
		// means that we are live!
		//
		// So, we're not testing for it's setting
		// here so we don't get false.
		//
		// && get_option('gffd_test_mode')
	){
		return true;
	}else{
		return false;
	}
}

//Get all the available forms built using
//Gravity forms
function gffd_get_forms(){
	$forms=RGFormsModel::get_forms();

	//Return an empty array so we don't
	//break foreach;
	if(sizeof($forms)==0){
		return false;
	}else{
		return $forms;
	}
}

// From: http://goo.gl/dxYMXL
// Automagically pulls out the forms fields.
function gffd_get_form_fields ( $form ) {
	$fields = array();

	if ( is_array( $form["fields"] ) ) {
		foreach ( $form["fields"] as $field ) {
			if ( is_array( gffd_rgar( $field, 'inputs' ) ) ) {
				foreach ( $field["inputs"] as $input ) {
					 $fields[] = array(
						$input["id"],
						GFCommon::get_label(
							$field,
							$input["id"]
						)
					);
				}
			}
			else if ( ! gffd_rgar( $field, 'displayOnly' ) ) {
				$fields[] = array(
					$field["id"], GFCommon::get_label( $field )
				);
			}
		}
	}

	return $fields;
}

// Took from Gravity Forms Stripe plugin,
// because it's used in the function
// gffd_get_form_fields().
function gffd_rgar ( $array, $name ) {
	if ( isset( $array[$name] ) )
		return $array[$name];
	return '';
}

// Just shorthand for $_REQUEST
// so I can, maybe, expand it later.
function gffd_request($key){
	return $_REQUEST[$key];
}

// A bogus function to help us fix foreach
// when array is not there so we
// don't fail foreach
function gffd_is_array($possible_array){
	if(is_array($possible_array)){
		return $possible_array;
	}else{
		return array();
	}
}

?>
