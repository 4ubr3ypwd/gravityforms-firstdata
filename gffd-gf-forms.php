<?php

// All the components to allow us
// to perform a purchase on a GFFD
// active form.

// Show the customer reference number on the entry
function gffd_gform_entry_detail_content_before( $form, $lead ) {

	$customer_reference_number = get_option(
		'gffd_fd_' . $lead['id']
	);

	if( $customer_reference_number ) {
		?>
		<table cellspacing="0" class="widefat fixed entry-detail-view">
			<tbody>
			<tr>
				<td colspan="2" class="entry-view-field-name">
					<?php _e( 'Reference Number'); ?>
				</td>
			</tr>
			<tr>
				<td class="entry-view-field-value">
					<?php echo $customer_reference_number; ?>
				</td>
			</tr>
			</tbody>
		</table>
		<?php
	}
}

add_action(
	'gform_entry_detail_content_before',
	'gffd_gform_entry_detail_content_before',
	10, 2
);

// Make sure, when the entry is entered,
function gffd_gform_post_submission( $entry, $form ) {

	// Get the temporary stored reference number saved on FD
	$customer_reference_number = get_option(
		//gffd_fd_12_9.9.9.9
		"gffd_fd_" . $form['id'] . "_" . $_SERVER['REMOTE_ADDR']
	);

	if( $customer_reference_number ) {

		// Re-store this information for this lead (entry)
		update_option(
			'gffd_fd_' . $entry['id'],
			$customer_reference_number
		);

		// Delete the temp store
		delete_option( "gffd_fd_" . $form['id'] . "_" . $_SERVER['REMOTE_ADDR'] );

	}
}

add_action(
	'gform_post_submission',
	'gffd_gform_post_submission',
	10, 2
);

// Validate the data, reformat the data,
// try and perform the purchase and throw errors.
//
// Otherwise, do the purchase, save the reference number
// and continue to adding entry.
function gffd_validation_and_payment($validation_result){

	$form_id = $validation_result['form']['id'];
	$the_submitted_form = $_REQUEST; //for easier reading.

	// Is this form supposed to be used by gffd?
	if( gffd_feed_is_active($form_id) ){

		// Validate all the fields.
		foreach(
			gffd_is_array(
				gffd_get_purchase_field_requirements()
			) as $required_field
		){

			$gffd_feeds_get_form_feed_settings
				=gffd_feeds_get_form_feed_settings(
					$form_id
				);

			// Get the feed index
			$gf_form_feed_index
				= $gffd_feeds_get_form_feed_settings
					['feed_indexes'][
						$required_field['gffd_index']
					];

			// Get the $_POST index by using value_to_post_input
			// so we can pull input_3_4 in GF style
			$gffd_convert_float_value_to_post_input=
				gffd_convert_float_value_to_post_input($gf_form_feed_index);

			// In the case when a required field is removed,
			// we need to test for it's value in $_POST.
			//
			// Test $required_field for it's $_POST
			// counterpart.
			if(
				isset(
					$the_submitted_form[
						$gffd_convert_float_value_to_post_input
					]
				)
			){

				$gffd_is_valid_pre_format_feed_data=
					gffd_is_valid_pre_format_feed_data(

						// Tell is_valid, where to find the function
						$required_field['gffd_index'],

						// Pull the value from $_POST
						$the_submitted_form[
							$gffd_convert_float_value_to_post_input
						]
					);

				// If we have a good result, set the value to pass
				// to the purchasing functions.
				if($gffd_is_valid_pre_format_feed_data){

					$gffd_fd_form_info[
						$required_field[
							'gffd_index'
						]
					] = $gffd_is_valid_pre_format_feed_data;

				// If even one of the items fail, jump out,
				// thrown an error and try again.
				}else{

					// Fail the form.
					$validation_result["is_valid"]=false;
					$validation_result["form"]=gffd_get_form(
						$form_id
					);

					// Let's try and find out what failed by going through the
					// form's fields.
					foreach($validation_result['form']['fields'] as &$field){
						if($field['id']==floor($gf_form_feed_index)){
							$field["failed_validation"]=true;
							$field["validation_message"]
								=gffd_get_validation_message_feed_data(
									$required_field['gffd_index']
								);
						}
					}

					// Let's not return the validation result here,
					// instead let's test for it after it's added
					// messages to all fields.
				}

			// Okay, one of the fields were missing.
			}else{
				// If a field is missing, let's just do nothing and submit
				// the form. But, we will at least email the admin about it,
				// because we did not do the purchase.
			}

		} //end foreach

		// If something did not validate, throw the error
		// back to GF.
		if($validation_result["is_valid"]==false){

			return $validation_result;

		// If everything validated, let's try
		// and perform the purchase.
		}else{

			// Set the customer reference number on FD
			$gffd_fd_form_info['gffd_fd_customer_ref']
				= gffd_fd_customer_ref( $form_id );

			// Save the reference number to the DB temporarily so we can
			// get it later.
			update_option(

				// Save gffd_fd_form_#_#.#.#

				// form_id shoudn't change: gffd_fd_12
				"gffd_fd_$form_id"

					// Remote IP shouldn't change:
					// When the entry is added, we want to look for
					//
					// gffd_fd_12_9.9.9.9
					//
					. "_" . $_SERVER['REMOTE_ADDR'],

				// Save the reference number we saved in FD trasnsaction log
				$gffd_fd_form_info['gffd_fd_customer_ref']
			);

			// Run a purchase by form
			$result = gffd_fd_purchase_by_form(
				$gffd_fd_form_info,
				'as_original'
			);

			if(
				// If we have results
				gffd_is_array($result)

				// If there isn't an error message
				&& ! $result['error_message']

				// And, we haven't set debug mode in wp-config.php
				&& ! defined('GFFD_DEBUG_FORM_SUBMIT')

				// And there was some sort of error
				&& $result['error']
			){

				// Just show a simple error.
				wp_die( __(
					"There was an error, but FirstData didn't
						send back an error message."
				) );

				// Want to see what the error is? Just set
				// define('GFFD_DEBUG_FORM_SUBMIT', true);
				// in wp-config.php and try again
			} else {

				// To debug just use define('GFFD_DEBUG_FORM_SUBMIT', true);
				// in wp-config.php
				if(defined('GFFD_DEBUG_FORM_SUBMIT')){

					echo "<pre>";
						var_dump(
							//$result['gffd_fd_instance']->getBankResponseMessage()
							$result
						);
					echo "</pre>";

					exit;

				// If the dev doesn't want to GFFD_DEBUG_FORM_SUBMIT
				}else{

					// If there was an error, and we've submitted
					// the data for purchase, let's see what the
					// bank or FirstData class said.
					if($result['error']===true){

						// Don't submit the entry.
						$validation_result["is_valid"]=false;

						// If there is a bank response, let's
						// pass that to the CC field
						if($result['gffd_fd_instance']->getBankResponseMessage()){
							$validation_result
								=gffd_wp_die_by_fd_response(
									$result,
									'getBankResponseMessage'
								);

						}elseif($result['gffd_fd_instance']->getErrorMessage()){
							// Try and see if there is just a generic
							// error message.
							$validation_result
								=gffd_wp_die_by_fd_response(
									$result,
									'getErrorMessage'
								);
						}

						return $validation_result;

					// If there was no bank response, but an error,
					// we don't know what is wrong!
					}else{

						// Throw a general error message.
						foreach($validation_result['form']['fields'] as &$field){
							if($field['type']=='creditcard'){
								// Tell GF what field failed. Here,
								// always the CC.
								$field["failed_validation"]=true;

								// Pass the bank response back to
								// GF validation_message.
								$field["validation_message"]
									= __(
										"Sorry, but there was an error with the"
										."information provided. Please correct and try again. "
										."If this persists, contact the site owner."
									);
							}
						}

						return $validation_result;
					}
				}
			}

		}

	}else{
		// If the form is not a form with a feed, just ignore
		// and continue.
		return $validation_result;
	}

}

add_filter('gform_validation','gffd_validation_and_payment');

function gffd_wp_die_by_fd_response($result, $kind){

	if($kind=='getBankResponseMessage'){
		$message=$result['gffd_fd_instance']
			->getBankResponseMessage();
	}elseif($kind="getErrorMessage"){
		$message=$result['gffd_fd_instance']
			->getErrorMessage();
	}

	wp_die(
		"<h1>".$message."</h1>"
		."<br><br>"

		.__(
			"Please go back and try again.<br><br>"
			."If this persists, you can contact the site owner by "
			."emailing "
		)

		."<a href='mailto:".get_option('admin_email')."'>"
			.get_option('admin_email')
		."</a>"
	);

}

// Enqueue a script so we can watch for changes in
// #cctype and make it an input value.
function gffd_gf_forms_js(){
	wp_enqueue_script(
		'gffd-gf-forms-js',
		plugins_url(
			'gffd-gf-forms.js',
			___GFFDFILE___
		),
		array(),
		'',
		false
	);
}

add_action('gform_enqueue_scripts','gffd_gf_forms_js');

// Generate a reference number for FD
function gffd_fd_customer_ref( $form_id ) {
	return substr(

		// Nice hash based on form id and the user's IP.
		md5(
			$form_id
			. $_SERVER['REMOTE_ADDR']
			. time()
		),

		// Only 8 chars
		0, 8
	);
}

?>
