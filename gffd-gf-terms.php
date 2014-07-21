<?php

// At times we may be repeating ourselves when saying
// the same language over and over.
// 
// This will return the same language to them all.
function gffd_language_terms($as_object_or_index='whole_as_object'){
	$terms = array(

		// The button is set back to Save and is initially Save
		// so this needs to be there for all functions
		'term_feed_admin_save'=>__(
			'Save'
		),
		'term_validate_bad_message_most_card_fields'=>__(
			"You must provide the Credit Card Number, Expiration Date, 
			Security Code, and Cardholder's Name."
		),
		'term_validate_bad_message_most_address'=>__(
			"You must provide Street Address, City, State, and Zip."
		),
		'you_will_need_address_field_select_here'=>__(
			'You will need to have an <strong>Address field</strong> to select from here'
		)
	);

	if(
		$as_object_or_index==='as_object' 
		|| $as_object_or_index===true
	){
		return json_decode(json_encode($terms));
	}elseif(is_string($as_object_or_index)){
		if( isset( $terms[$as_object_or_index] ) ) {
			return $terms[$as_object_or_index];
		}
	}elseif($as_object_or_index=='whole_as_object'){
		return json_decode(json_encode($terms));
	}
}

// Set a global variable that can be used in ""
$gffd_language_terms = gffd_language_terms('whole_as_object');

?>
