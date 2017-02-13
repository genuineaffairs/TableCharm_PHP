
/* $Id: core.js 9968 2013-03-19 00:20:56Z john $ */

window.addEvent('domready', function() {
	addRequiredLabels();
	replaceContactFormDescription();
});

function addRequiredLabels() {
	// Add missing required asterisks. Cheers SocialEngine
  	Array.each($$('#global_content label.required'), function(label) { label.set('html', '<span class="required-indicator">*</span>' + label.get('text')); });
	Array.each($$('a.menu_core_main'), function(menuLink) { menuLink.set('title', menuLink.get('text')); });
}

function replaceContactFormDescription() {
	if (jQuery('.contact-form-description').length > 0) {
		var contactFormDescription = jQuery('.contact-form-description');
		if (jQuery('.form-errors').length > 0) {
			jQuery('.form-errors').before(contactFormDescription);
		} else {
			jQuery('.form-elements').before(contactFormDescription);
		}
	}	
}