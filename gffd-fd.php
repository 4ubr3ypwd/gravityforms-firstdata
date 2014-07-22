<?php

use VinceG\FirstDataApi\FirstData;

// Bypass having to send by reference
function gffd_end( $var ) {
	return end( $var );
}

// Assign data no matter what is in the array.
function gffd_fd_form_info( $key, $gffd_fd_form_info ) {
	if( isset( $gffd_fd_form_info[ $key ] ) ) {
		return $gffd_fd_form_info[ $key ];
	} else {
		return false;
	}
}

// Perform the purchase by sending date submitted
// by a form.
function gffd_fd_purchase_by_form(
	$gffd_fd_form_info,
	$echo_or_as_object=false
){

	// Don't continue if the needed
	// options are not setup.
	if(!gffd_is_setup()){
		return gffd_fd_format_error_without_instance(
			__("Gateway ID or Gateway Password does not appear to be setup.")
		);
	}

	// Get the options for the gateway id
	// and password.
	$gffd_fd_gateway_id=gffd_admin_get_setting('gffd_gateway_id');
	$gffd_fd_gateway_pass=gffd_admin_get_setting('gffd_gateway_password');
	$gffd_fd_test_mode=gffd_admin_get_setting('gffd_test_mode');

	// Make sure we (bool) the DB store
	// for test mode.
	if($gffd_fd_test_mode=='on'){
		$gffd_fd_test_mode = true;
	}else{
		$gffd_fd_test_mode = false;
	}

	// Setup the FirstData instance so we can use it
	// to process the payment.
	$fd_request=new FirstData(

		// Gateway ID from Terminal > Settings.
		$gffd_fd_gateway_id,

		// Generated Password for Terminal.
		$gffd_fd_gateway_pass,

		// Test mode?.
		$gffd_fd_test_mode
	);

	// Make sure there is <something> in the cc_type field.
	if( ! isset( $gffd_fd_form_info['gffd_fd_cc_type'] ) ) {
		$gffd_fd_form_info['gffd_fd_cc_type'] = '';
	}

	// Information on formatting: http://goo.gl/46V13c
	$gffd_fd_info = array(
		'gffd_fd_cc_type' => gffd_fd_form_info( 'gffd_fd_cc_type', $gffd_fd_form_info ),
		'gffd_fd_cc_number' => gffd_fd_form_info( 'gffd_fd_cc_number', $gffd_fd_form_info ),

		// Because GF credit card field only allows the name to be
		// entered in "First and Last" in one input, we stored it
		// in cc_firstname.
		//
		// Get the first name from the "John" in "John Doe".
		'gffd_fd_cc_firstname' => (
			current(
				explode(
					" ",
					gffd_fd_form_info(
						'gffd_fd_cc_firstname',
						$gffd_fd_form_info
					)
				)
			)
		),

		// Get the last name from the "Doe" in "John Doe".
		'gffd_fd_cc_lastname' => (
			gffd_end(
				explode(
					" ",
					gffd_fd_form_info(
						'gffd_fd_cc_firstname',
						$gffd_fd_form_info
					)
				)
			)
		),

		'gffd_fd_cc_exp' => gffd_fd_form_info( 'gffd_fd_cc_exp', $gffd_fd_form_info ), //mmyy
		'gffd_fd_cc_amount' => gffd_fd_form_info( 'gffd_fd_cc_amount', $gffd_fd_form_info ),
		'gffd_fd_cc_zip' => gffd_fd_form_info( 'gffd_fd_cc_zip', $gffd_fd_form_info ),
		'gffd_fd_cc_cvv' => gffd_fd_form_info( 'gffd_fd_cc_cvv', $gffd_fd_form_info ),

		// The $gffd_fd_form_info passed to this function has the
		// address data segregated. Here, let's combine it the way
		// First Data likes it.
		'gffd_fd_cc_address'=>(
				//Address
				 gffd_fd_form_info( 'gffd_fd_cc_address', $gffd_fd_form_info )
					." ". gffd_fd_form_info( 'gffd_fd_cc_address2', $gffd_fd_form_info )

				//Zip
				."|".gffd_fd_form_info( 'gffd_fd_cc_zip', $gffd_fd_form_info )

				//City
				."|".gffd_fd_form_info( 'gffd_fd_cc_city', $gffd_fd_form_info )

				//State
				."|".gffd_fd_form_info( 'gffd_fd_cc_state', $gffd_fd_form_info )

				//Country
				."|".gffd_fd_form_info( 'gffd_fd_cc_country', $gffd_fd_form_info )
		),

		// Set to something at least
		'gffd_fd_cc_address2' => '',

		// Set the entry ID of the form
		'gffd_fd_customer_ref' => gffd_fd_form_info(
			'gffd_fd_customer_ref',
			$gffd_fd_form_info
		)
	);

	$purchase_action_result=
		gffd_fd_perform_auth_purchase( $fd_request, $gffd_fd_info );

	if($echo_or_as_object===true
		|| $echo_or_as_object=='echo'
		|| $echo_or_as_object=='json'
	){
		echo json_encode($purchase_action_result);
	}elseif($echo_or_as_object=='as_original'){
		return $purchase_action_result;
	}else{
		return json_encode($purchase_action_result);
	}

}

// Perform a purchase by sending data
// via $_REQUEST.
function gffd_fd_purchase_by_request(){
	$gffd_fd_purchase_by_request = new FirstData(

		// Gateway ID from Terminal > Settings.
		$_REQUEST['gffd_fd_gateway_id'],

		// Generated Password for Terminal.
		$_REQUEST['gffd_fd_gateway_pass'],

		// Test mode?.
		$_REQUEST['gffd_fd_gateway_test']
	);

	$gffd_fd_info = array(
		'gffd_fd_cc_type' => $_REQUEST['gffd_fd_cc_type'],
		'gffd_fd_cc_number' => $_REQUEST['gffd_fd_cc_number'],
		'gffd_fd_cc_firstname' => $_REQUEST['gffd_fd_cc_firstname'],
		'gffd_fd_cc_lastname' => $_REQUEST['gffd_fd_cc_lastname'],
		'gffd_fd_cc_exp' => $_REQUEST['gffd_fd_cc_exp'], //mmyy
		'gffd_fd_cc_amount' => $_REQUEST['gffd_fd_cc_amount'],
		'gffd_fd_cc_zip' => $_REQUEST['gffd_fd_cc_zip'],
		'gffd_fd_cc_cvv' => $_REQUEST['gffd_fd_cc_cvv'],
		'gffd_fd_cc_address' => $_REQUEST['gffd_fd_cc_address'],
	);

	$result = gffd_fd_perform_auth_purchase(
		$gffd_fd_purchase_by_request,
		$gffd_fd_info
	);

	echo json_encode($result);
}

function gffd_fd_process($gffd_fd__,$trans_type,$gffd_fd_info){
	$gffd_fd__->setTransactiontype(
		$trans_type
		)->setCreditCardType(
			$gffd_fd_info['gffd_fd_cc_type']
		)->setCreditCardNumber(
			$gffd_fd_info['gffd_fd_cc_number']
		)->setCreditCardName(
			$gffd_fd_info['gffd_fd_cc_firstname']
			. " "
			. $gffd_fd_info['gffd_fd_cc_lastname']
		)->setCreditCardExpiration(
			$gffd_fd_info['gffd_fd_cc_exp']
		)->setAmount(
			$gffd_fd_info['gffd_fd_cc_amount']
		)->setCreditCardZipCode(
			$gffd_fd_info['gffd_fd_cc_zip']
		)->setCreditCardVerification(
			$gffd_fd_info['gffd_fd_cc_cvv']
		)->setCreditCardAddress(
			$gffd_fd_info['gffd_fd_cc_address']
			. " "
			.$gffd_fd_info['gffd_fd_cc_address2']
		)->setReferenceNumber(
			$gffd_fd_info['gffd_fd_customer_ref']
		)->setCustomerReferenceNumber(
			$gffd_fd_info['gffd_fd_customer_ref']
		)->process();

	return $gffd_fd__;
}

// Try both the pre-auth and the purchase.
// If the pre-auth doesn't work, just throw back the error
function gffd_fd_perform_auth_purchase($gffd_fd__, $gffd_fd_info){

	// Store the transaction amount.
	$transaction_amount
		=$gffd_fd_info['gffd_fd_cc_amount'];

	// Set the transaction amount for the pre_auth
	// to 0 for the pre-auth.
	$gffd_fd_info['gffd_fd_cc_amount']='0';

	// Perform the pre-auth.
	$gffd_fd__ = gffd_fd_process(
		$gffd_fd__,
		FirstData::TRAN_PREAUTHONLY,
		$gffd_fd_info
	);

	// Set the amount back to the actual transaction amount
	// for actual purchase.
	$gffd_fd_info['gffd_fd_cc_amount']
		=$transaction_amount;

	if($gffd_fd__->isError()){
		return gffd_fd_format_error($gffd_fd__, $gffd_fd_info);
	}else{
		$gffd_fd__ = gffd_fd_process(
			$gffd_fd__,
			FirstData::TRAN_PURCHASE,
			$gffd_fd_info
		);

		if($gffd_fd__->isError()){
			return gffd_fd_format_error($gffd_fd__, $gffd_fd_info);
		}else{
			return array(
				'error'=>false,
				'transaction_type'=>$gffd_fd__->getTransactionType(),
				'gffd_fd_instance'=>$gffd_fd__
			);
		}
	}
}

// DRY way of returning some useful information
// when there is an error.
function gffd_fd_format_error($gffd_fd__, $gffd_fd_info=false){
	$result = array(
		'error'=>true,
		'transaction_type'=>$gffd_fd__->getTransactionType(),
		'error_message'=>$gffd_fd__->getErrorMessage(),
		'error_code'=>$gffd_fd__->getErrorCode(),
		'gffd_fd_instance'=>$gffd_fd__,
		'print_r'=>print_r($gffd_fd__, true),
	);

	if(
		is_array($gffd_fd_info)
	){
		$result['gffd_fd_info']=$gffd_fd_info;
	}

	return $result;
}

// Return a formatted error without any _fd
// instances
function gffd_fd_format_error_without_instance($error_message){
	return array(
		'error'=>true,
		'error_message'=>$error_message,
	);
}

?>
