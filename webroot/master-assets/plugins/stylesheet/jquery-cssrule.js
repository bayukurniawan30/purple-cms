/*	Copyright (c) 2017 Jean-Marc VIGLINO, 
	released under MIT license
	(http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt).
*/
/** @fileoverview A simple jQuery plugin to manipulate CSS styleSheet
 *	@see {@link https://www.w3.org/wiki/Dynamic_style_-_manipulating_CSS_with_JavaScript }
 *  @author  Jean-Marc VIGLINO
 *  @version 1.0
 *  @requires jQuery
 *	@external "jQuery.fn"
 *	@see {@link http://learn.jquery.com/plugins/|jQuery Plugins}
 */
(function ($) {
	// Create a new stylesheet in the bottom of the <body> 
	// or the <head> depending on the place of the file
	var stylesheet = $("<style id='custom-embedded-stylesheet'>")
		.prop("type", "text/css")
		.appendTo(document.body||'head');

	// List of rules
	var rules = [];

	function getRuleId (selector, property)
	{	for (var i=0,r; r=rules[i]; i++)
		{	if (r.selector==selector && r.property==property) return i;
		}
		return -1;
	}
	
	function setRule(selector, property, value) 
	{	var id = getRuleId (selector, property)
		if (id>=0) rules.splice(id, 1);
		if (value) rules.push({ 'selector':selector, 'property':property, 'value':value });
		return;
	}

	function setSheet()
	{	var html = "";	
		for (var i=0,r; r=rules[i]; i++)
		{	html += r.selector+' {'+r.property+':'+r.value+'; }\n';
		}
		stylesheet.html(html);
	}
	
	/** Manipulate CSS styleSheet. 
	 *	The function will add a new property for the selector in a style sheet.
	 *	The style sheet will be inserted where the js is placed and will override other css style sheets placed before.
	 *
	 *	@example 
	 *	$("body").cssRule("background","red");	// Change background color of the body
	 *	$("body").cssRule("background");			// return "red"
	 *	$("body").cssRule({background":"red", "color":"blue"});
	 *	$("body").cssRule("background",null);	// Remove previous value
	 *	$("*").cssRule(null);					// Remove all values
	 *
	 *  @function external:"jQuery.fn".styleSheet
	 *	@param {string|object} property a property or a key, value array of properties you want to set
	 *	@param {string|null|undefined} value the value you want to set, if undefined will return the current value, if null remove the property
	 *	@return {jQuery object|string} the object or the property value id value is undefined
	 */
	$.fn.cssRule = function (property, value)
	{	var p = property;
		// Reset properties
		if (this.selector == "*" && property === null) 
		{	stylesheet.html("");
			rules = [];
			return this;
		}
		else if (typeof(property) == 'string') 
		{	// Get the property
			if (value===undefined)
			{	var id = getRuleId(this.selector,property);
				if (id<0) return null;
				else return rules[i].value;
			}
			// Set the property
			else
			{	p = {};
				p[property] = value;
			}
		}
		// Process
		for (var i in p)
		{	setRule(this.selector, i, p[i]);
		}
		setSheet();
		return this;
	};

})(jQuery);