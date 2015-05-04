<?php

class GFFD_Core {

	public $gffd_glossary;

	// Instances:
	public $gffd_admin;

	function __construct() {

		// Setup the admin stuff.
		$this->gffd_admin = new GFFD_Admin( $this );
		$this->gffd_admin_feeds = new GFFD_Admin_Feeds( $this );

		// Hooks
		add_action( 'plugins_loaded', array( $this, 'gffd_check_requirements' ) );
		add_action( 'admin_init', array( $this, 'gffd_enable_cc' ) );
		add_action( 'init', array( $this, 'debug' ) );

		// Glossary of terms
		$this->gffd_glossary = $this->gffd_glossary( false, true );
	}

	public function debug( $result ) {

		// If you would like to see debug information,
		// you may call this script with &debug=1
		if( isset( $_REQUEST['gffd_debug'] ) && isset( $result['print_r'] ) ) {
			include 'gffd-debug.html.php';
		}
	}

	// First, check that we have the requirements,
	// if not stop (this way we don't throw an error).
	function gffd_check_requirements(){
		if( class_exists( 'RGForms' ) && class_exists( 'RGFormsModel' ) && class_exists( 'GFCommon' ) ){

			// If we have the requirements,
			// let's prepare everything:

			// Integrate with Gravity Forms
			add_action( 'init', array( $this, 'gffd_check_n_load' ) );

		}
	}

	function gffd_load(){
		require_once 'gffd-gf.php';
	}

	function gffd_check_n_load(){
		$this->gffd_load();
	}

	function gffd_get_validation_message_feed_data( $gffd_index ) {
		$gffd_get_purchase_field_requirements = gffd_get_purchase_field_requirements( $gffd_index );

		if ( isset( $gffd_get_purchase_field_requirements['validate_bad_message'] ) ) {
			return $gffd_get_purchase_field_requirements['validate_bad_message'];
		} else {
			return false;
		}
	}

	// Use the validation functions against the values stored
	// in $_POST.
	function gffd_is_valid_pre_format_feed_data( $gffd_index, $value ) {
		$gffd_get_purchase_field_requirements = gffd_get_purchase_field_requirements( $gffd_index );
		$value = apply_filters( array( $this, $gffd_get_purchase_field_requirements['validate_pre_format'] ), $value );
		return $value;
	}

	// Converts the float format for fields like 1.1 to input_1_1
	// so we can catch them on $_POST
	function gffd_convert_float_value_to_post_input( $gf_float_value ) {
		return 'input_' . str_replace( '.', '_', $gf_float_value );
	}

	function gffd_feeds_get_form_feed_settings( $form_id, $as_object=false ) {
		$current_form_settings['feed_indexes'] = get_option( 'gffd_form_' . $form_id . '_feed_indexes' );
		$current_form_settings['feed_active'] = get_option( 'gffd_form_' . $form_id . '_feed_active' );

		if( ! $as_object ) {
			return $current_form_settings;
		} elseif ( 'as_object'  == $as_object ) {
			return json_decode( json_encode( $current_form_settings ) );
		}
	}

	// Need to configure this function to figure out if a feed
	// is active nor not.
	function gffd_feed_is_active( $form_id ) {
		$this->$this_form_feed_settings = $this->gffd_feeds_get_form_feed_settings( $form_id, 'as_object' );
		$feed_active = $this_form_feed_settings->feed_active;

		if ( $feed_active == 'active' ) {
			return true;
		} else {
			return false;
		}
	}

	// Get the Gravity Forms plugin URL
	function gffd_plugin_url() {
		$url = plugins_url() . '/' . basename( GFCommon::get_base_path() ) . '/css/admin.css';
		return $url;
	}

	// Shorthand way to get a form from GF
	function gffd_get_form( $form_id ) {
		return RGFormsModel::get_form_meta( $form_id );
	}

	// Enable the CC field in gravity forms.
	function gffd_enable_cc() {
		add_filter( 'gform_enable_credit_card_field', '__return_true' );
	}

	// Glossary of things, etc so it's easier!
	//
	// To use, you can:
	//
	// gffd_glossary() which will return the whole glossary array.
	// gffd_glossary('plugin_name') will return the specific term in the array.
	// gffd_glossary('', 'as_object') will give you the glossary as an object
	function gffd_glossary( $key=null, $as_object = false ) {

		$glossary = array(
			'plugin_name'   => "Gravity Forms + First Data Global Gateway e4",
			'settings_name' => "First Data Global Gateway",
			'service_name'  => "First Data Global Gateway",
		);

		if ( ! $key || '' == $key ) {
			if ( $as_object ) {
				return json_decode( json_encode( $glossary ) );
			} else {
				return $glossary;
			}
		} else {
			if ( $as_object ) {
				return json_decode( json_encode( $glossary[ $key ] ) );
			}else{
				return $glossary[ $key ];
			}
		}
	}

	// Convert an array to an object.
	function gffd_array_as_object( $a ) {
		return json_decode( json_encode( $a ) );
	}

	function gffd_validate_is_set( $value ) {
		if ( ! empty( $value ) ) {
			return $value;
		} else {
			return false;
		}

		return false;
	}

	function gffd_validate_money_value( $value ) {
		if( ! empty( $value ) ) {

			//Remove $
			return str_replace( "$", "", $value );
		}else{
			return false;
		}
	}

	function gffd_validate_us( $value ) {
		if ( ! empty( $value) ) {
			return $value;
		} else {
			return "US";
		}
	}

	function gffd_validate_exp( $value ) {
		if ( is_array( $value ) ) {

			// The value (when submitted by $_POST)
			// is an array:
			//
			// array(2) { [0]=> string(1) "1" [1]=> string(4) "2016" }
			//
			// So, let's chop those two array
			// structs into a value that is good for FD.
			if ( isset( $value[0] ) && isset( $value[1] ) ) {

				// Tak on a 0 if we have 4, or 5, but not if
				// we have 12
				return str_pad( $value[0], 2, '0', STR_PAD_LEFT ) . substr( $value[1], -2 );
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	// These are the minimum fields we need to make a purchase.
	function gffd_get_purchase_field_requirements( $as_object_or_gffd_index = false, $as_object = false ) {

		$fields = array(

			// Here we package the full customer name
			// from the GF credit card field as one
			// field, which here we call firstname
			array(
				'gffd_index'           => 'gffd_fd_cc_firstname',
				'label'                => __( 'Name', 'gffd' ),
				'meta'                 => __( "Usually called <strong>Credit Card (Cardholder's Name)</strong>", 'gffd' ),
				'validate_bad_message' => $this->gffd_language_terms( 'term_validate_bad_message_most_card_fields' ),

				// Filter
				'validate_pre_format'  => 'gffd_validate_is_set',
			),

			// CCtype is different as GF does not submit the cctype,
			// though it can be selected from the feed.
			array(
				'gffd_index'           => 'gffd_fd_cc_type',
				'label'                => __( 'Credit Card Type', 'gffd' ),
				'meta'                 => __( 'Usually called <strong>Credit Card (Card Type)</strong>', 'gffd' ),
				'validate_bad_message' => $this->gffd_language_terms( 'term_validate_bad_message_most_card_fields' ),
				'validate_pre_format'  => gffd_request( 'gffd_cc_type' ),
			),

			array(
				'gffd_index'           => 'gffd_fd_cc_number',
				'label'                => __( 'Credit Card Number', 'gffd' ),
				'meta'                 => __( 'Usually called <strong>Credit Card (Card Number)</strong>', 'gffd' ),
				'validate_bad_message' => $this->gffd_language_terms( 'term_validate_bad_message_most_card_fields' ),
				'validate_pre_format'  => 'gffd_validate_is_set',
			),

			// Do some re-formatting for the expiration date.
			array(
				'gffd_index'           => 'gffd_fd_cc_exp',
				'label'                => __( 'Card Expiration Date', 'gffd' ),
				'meta'                 => __( 'Usually called <strong>Credit Card (Expiration Date)</strong>', 'gffd' ),
				'validate_bad_message' => $this->gffd_language_terms( 'term_validate_bad_message_most_card_fields' ),
				'validate_pre_format'  => 'gffd_validate_exp',
			),

			array(
				'gffd_index'           => 'gffd_fd_cc_cvv',
				'label'                => __( 'CVV/Security Code', 'gffd' ),
				'meta'                 => __( 'Usually called <strong>Credit Card (Security Code)</strong>', 'gffd' ),
				'validate_bad_message' => $this->gffd_language_terms(),
				'validate_pre_format'  => 'gffd_validate_is_set',
			),

			// Just make sure amount is a decimal float.
			array(
				'gffd_index'           => 'gffd_fd_cc_amount',
				'label'                => __('Charge Amount'),
				'meta'                 => __( 'Here you will need to select a <strong>' .'total field</strong> to your form', 'gffd' ),
				'validate_bad_message' => $this->gffd_language_terms( 'term_validate_bad_message_most_card_fields' ),
				'validate_pre_format'  => 'gffd_validate_money_value',
			),

			// Address is actually formatted post validation. Here, we just
			// want to make sure we have values. They will be re-formatted
			// for the API like address|city|state|zip, etc later.
			array(
				'gffd_index'           => 'gffd_fd_cc_address',
				'label'                => __( 'Address', 'gffd' ),
				'meta'                 => $this->gffd_language_terms( 'you_will_need_address_field_select_here' ),
				'validate_bad_message' => $this->gffd_language_terms( 'term_validate_bad_message_most_address' ),
				'validate_pre_format'  => 'gffd_validate_is_set',
			),

			array(
				'gffd_index'           => 'gffd_fd_cc_address2',
				'label'                => __('Addr ess2', 'gffd' ),
				'meta'                 => $this->gffd_language_terms( 'you_will_need_address_field_select_here' ),
				'validate_bad_message' => true,
				'validate_pre_format'  => true
			),

			array(
				'gffd_index'           => 'gffd_fd_cc_zip',
				'label'                => __( 'Zip', 'gffd' ),
				'meta'                 => $this->gffd_language_terms( 'you_will_need_address_field_select_here' ),
				'validate_bad_message' => $this->gffd_language_terms( 'term_validate_bad_message_most_address' ),
				'validate_pre_format'  => 'gffd_validate_is_set',
			),

			array(
				'gffd_index'           => 'gffd_fd_cc_city',
				'label'                => __( 'City', 'gffd' ),
				'meta'                 => $this->gffd_language_terms( 'you_will_need_address_field_select_here' ),
				'validate_bad_message' => $this->gffd_language_terms( 'term_validate_bad_message_most_address' ),
				'validate_pre_format'  => 'gffd_validate_is_set',
			),

			array(
				'gffd_index'           => 'gffd_fd_cc_state',
				'label'                => __( 'State', 'gffd' ),
				'meta'                 => $this->gffd_language_terms( 'you_will_need_address_field_select_here' ),
				'validate_bad_message' => $this->gffd_language_terms( 'term_validate_bad_message_most_address' ),
				'validate_pre_format'  => 'gffd_validate_is_set',
			),

			// Just make US the default, if they submit country, use that.
			array(
				'gffd_index'           => 'gffd_fd_cc_country',
				'label'                => __( 'Country', 'gffd' ),
				'meta'                 => $this->gffd_language_terms( 'you_will_need_address_field_select_here' ),
				'validate_bad_message' => $this->gffd_language_terms( 'term_validate_bad_message_most_address' ),
				'validate_pre_format'  => 'gffd_validate_us',
			)
		);

		// If they ask for 'gffd_fd_cc_zip' or 'as_object'
		if ( is_string( $as_object_or_gffd_index ) ) {

			// If it is 'as_object'
			if( 'as_object' == $as_object_or_gffd_index ) {
				return $this->gffd_array_as_object( $fields );

			// If it's not 'as_object', let's assume
			// it's a gffd_index.
			} else {

				//Pull out just the field they are asking for.
				foreach ( $fields as $field ) {
					if( $as_object_or_gffd_index == $field['gffd_index'] ){
						$_the_field = $field;
					}
				}

				// If they also said, 'gffd_fd_cc_zip', 'as_object',
				// return as an object.
				if ( $as_object ) {
					return gffd_array_as_object( $_the_field );

				// If they just said 'gffd_fd_cc_zip' then return
				// it as an array
				} else {
					return $_the_field;
				}
			}

		// If they pass (true), then just pass as an
		// object.
		} elseif ( true == $as_object_or_gffd_index ) {
			return gffd_array_as_object( $fields );

		// If they just pass () then return
		// as an array.
		} else {
			return $fields;
		}
	}

	// Check and make sure that everything
	// is setup for a purchase.
	function gffd_is_setup() {

		// The test option may be off,
		// in that case it may not exist, which
		// means that we are live!
		//
		// So, we're not testing for it's setting
		// here so we don't get false.
		//
		// && get_option('gffd_test_mode')
		if( get_option('gffd_gateway_id') && get_option('gffd_gateway_password') ){
			return true;
		}else{
			return false;
		}
	}

	//Get all the available forms built using
	//Gravity forms
	function gffd_get_forms(){
		$forms = RGFormsModel::get_forms();

		//Return an empty array so we don't
		//break foreach;
		if( sizeof( $forms ) == 0 ) {
			return array();
		} else {
			return $forms;
		}
	}

	// From: http://goo.gl/dxYMXL
	// Automagically pulls out the forms fields.
	function gffd_get_form_fields ( $form ) {
		$fields = array();

		if ( is_array( $form['fields'] ) ) {
			foreach ( $form['fields'] as $field ) {
				if ( is_array( gffd_rgar( $field, 'inputs' ) ) ) {
					foreach ( $field["inputs"] as $input ) {
						$fields[] = array( $input["id"], GFCommon::get_label( $field, $input["id"] ) );
					}
				}
				else if ( ! gffd_rgar( $field, 'displayOnly' ) ) {
					$fields[] = array( $field['id'], GFCommon::get_label( $field ) );
				}
			}
		}

		return $fields;
	}

	// Took from Gravity Forms Stripe plugin,
	// because it's used in the function
	// gffd_get_form_fields().
	function gffd_rgar( $array, $name ) {
		if ( isset( $array[$name] ) ) {
			return $array[$name];
		}
		return '';
	}

	// Just shorthand for $_REQUEST
	// so I can, maybe, expand it later.
	function gffd_request( $key ) {
		return $_REQUEST[ $key ];
	}

	// A bogus function to help us fix foreach
	// when array is not there so we
	// don't fail foreach
	function gffd_is_array( $possible_array ) {
		if( is_array( $possible_array ) ) {
			return $possible_array;
		}else{
			return array();
		}
	}

}

new GFFD_Core();