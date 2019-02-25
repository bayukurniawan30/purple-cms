$(document).ready(function() {
	// Theme Blocks
	if ($('.remove-padding-in-real').length > 0) {
		$('.remove-padding-in-real').css('padding', '0');
	}

	// UIkit Attribute Checking
	if ($('*[data-purple-add-attr]').length > 0) {
		$("*[data-purple-add-attr]").each(function() {
       		var elAttr = $(this).data('purple-add-attr');
       		var value  = elAttr.indexOf("=");
       		if (value == -1) {
				if (elAttr != 'uk-slider') {
		       		$(this).attr(elAttr, '');
       			}
       		}
       		else {
				var splitAttr    = elAttr.split('=');
				var newAttr      = splitAttr[0];    			
				var newAttrValue = splitAttr[1];

				if (newAttr != 'uk-slider') {
		       		$(this).attr(newAttr, newAttrValue);
       			}
       		}
     	});
	}

	// Hide reCaptcha Badge
	$('.grecaptcha-badge').css('display', 'none');
})