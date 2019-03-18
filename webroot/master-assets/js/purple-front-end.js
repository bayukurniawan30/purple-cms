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

	// Disable Editable Content
	$('[contenteditable]').attr('contenteditable', 'false');

	// Remove Duplicate UIKit Slider Navigation
	$('.uk-slidenav').each(function() {
	    var child = $(this).children('svg').first().get(0).outerHTML;
	    console.log(child);

	    $(this).html(child);
	});

	// Remove Tree Panel Identifier
	$('body').find('*').removeAttr('data-tree-id');

	$('a[href="https://www.froala.com/wysiwyg-editor?k=u"]').remove();
})