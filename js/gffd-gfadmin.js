// This file is used to setup all the admin stuff
// for our plugin.
jQuery(document).ready(function(){
	
	// This allows us to make buttons and such with href's
	// attached to them using:
	// 
	// <input type="button" class="button gffd-href" data-href="http://" value="My Button">
	jQuery('.gffd-href').click(function(){
		window.location.href=jQuery(this).data('href');
	});

});

// This will just set the button val.
function gffd_message_on_button(selector, _error){
	//Change the button to an error...
	jQuery(selector).val(_error);
}