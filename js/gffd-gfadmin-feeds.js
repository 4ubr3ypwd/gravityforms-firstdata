jQuery(document).ready(function(){

	if(gffd_is_feed_button_checked()==true){
		gffd_feed_do_validation();
	}

	jQuery('#gffd_feed_active').click(function(){
		if(gffd_is_feed_button_checked()==true){
			gffd_feed_do_validation();
		}else{
			gffd_feed_stop_validation();
		}
	});

	// Make sure if we move, or change something, that we clear
	// the button error.
	jQuery(window).bind('click scroll',function(){
		gffd_message_on_button(
			'#gffd-feed-admin-edit-submit',

			// See gffd-gfadmin-feeds.php @ wp_localize_script for _e
			_e.admin_feeds_button_save
		);
	});

	// Make it so when selects and input checks are changed
	// We set the button to save again
	jQuery('select, input').bind('click change',function(){
		gffd_message_on_button(
			'#gffd-feed-admin-edit-submit',
			_e.admin_feeds_button_save
		);
	});

	// When the form for a particular feed is submitted,
	// do some validation
	jQuery('#gffd-gf-feed-form').on('submit',function(event){

		// If the check on all the fields pass,
		// submit
		if(gffd_is_feed_button_checked()==true){
			if(gffd_validate_feed_fields('check_all')==true){
				return;
			}

			// If they don't pass, 
			// we will prevent the submit.
			event.preventDefault();

			gffd_message_on_button(
				'#gffd-feed-admin-edit-submit',

				// See gffd-gfadmin-feeds.php @ wp_localize_script for _e
				_e.admin_feeds_button_error
			);
		}

	});

});

// Remove validation 
// (when the feed checkmark is inactive)
function gffd_feed_stop_validation(){

	// Remove the current errors (really only read stuff).
	jQuery('#gffd-gf-feed-form select').removeClass('error');

	// Unbind any error checking.
	jQuery('#gffd-gf-feed-form select').unbind('change');	
}

// Enable validation, by first checking all
// and then adding individual changes.
function gffd_feed_do_validation(){
	gffd_validate_feed_fields('check_all');
	gffd_feed_dropdown_check_changes();
}

function gffd_feed_dropdown_check_changes(){

	// Also, when we change individual 
	// drop-down's, let's check them individually
	// for instant feedback.
	jQuery('#gffd-gf-feed-form select').bind('click change',function(){
		gffd_validate_feed_fields(this);
	});
}

function gffd_is_feed_button_checked(){
	if(jQuery('#gffd_feed_active').is(':checked')){
		return true;
	}else{
		return false;
	}
}

// Validates all fields, or a single field.
function gffd_validate_feed_fields(field){

	_continue = true;

	// If we aren't validating a specifc_field, 
	// do them all.
	if(field=='check_all'){
		jQuery('select.feed-dropdown').each(function(){
			if(_continue != false){
				_continue = gffd_validate_feed_field(this);
			}else{
				gffd_validate_feed_field(this)
			}
		});

	//If we are testing a specific field, just do it.
	}else{
		_continue = gffd_validate_feed_field(field);
	}

	return _continue;
}

// Validate a single field.
function gffd_validate_feed_field(field){
	
	if(jQuery(field).find("option:selected").val()==''){
		jQuery(field).addClass('error');
		return false;
	}else{
		jQuery(field).removeClass('error');
		return true;
	}
}