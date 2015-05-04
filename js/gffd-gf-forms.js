jQuery(document).ready(function(){
	if(
		jQuery(".gform_card_icon_container")
	){
		setInterval(function(){
			jQuery(".gform_card_icon_container div").each(function(){

				// Find the div that is selected as the card type.
				if(
					jQuery(this).hasClass('gform_card_icon_selected')
				){

					// Get the value from the div, which
					// should have the card type.
					cc_type = jQuery(this).text();
				
					// Put the cc_type as a hidden input.
					_html  = '<input id="gffd_cc_type" type="hidden" ';
					_html += 'name="gffd_cc_type" ';
					_html += 'value="' + cc_type + '" />';

					// Update the input if it's already there.
					if(
						jQuery('#gffd_cc_type').val()
					){
						jQuery('#gffd_cc_type').val(cc_type);
					}else{
						jQuery('.gform_card_icon_container').append(
							_html
						);
					}
					
				}else{

				}
			});
		},
		500);
	}
});

// Get a list of classes for an element.
// Thanks to http://stackoverflow.com/a/11232541/1436129
;!(function (jQuery) {
    jQuery.fn.gffd_get_classes_for_cc = function (callback) {
        var gffd_get_classes_for_cc = [];
        jQuery.each(this, function (i, v) {
            var splitClassName = v.className.split(/\s+/);
            for (var j in splitClassName) {
                var className = splitClassName[j];
                if (-1 === gffd_get_classes_for_cc.indexOf(className)) {
                    gffd_get_classes_for_cc.push(className);
                }
            }
        });
        if ('function' === typeof callback) {
            for (var i in gffd_get_classes_for_cc) {
                callback(gffd_get_classes_for_cc[i]);
            }
        }
        return gffd_get_classes_for_cc;
    };
})(jQuery);