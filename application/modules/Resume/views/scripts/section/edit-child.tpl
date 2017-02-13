<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>
<script type="text/javascript">


function employment_update_is_current(val)
{
  if (val) {
	$('end_date-wrapper').setStyle('display', 'none');
  }
  else {
	$('end_date-wrapper').setStyle('display', 'block');
  }
}
window.addEvent('domready', function(){

 	$('is_current').addEvent('change', function(){
	  employment_update_is_current(this.checked);
 	});
 	employment_update_is_current($('is_current').checked);
        
        // Add missing required asterisks. Cheers SocialEngine
  	Array.each($$('label.required'), function(label) { label.set('html', '<span class="required-indicator">*</span>' + label.get('text')); });
	Array.each($$('a.menu_core_main'), function(menuLink) { menuLink.set('title', menuLink.get('text')); });
});

</script>

<?php echo $this->form->setAttrib('class', 'resume_form_popup global_form_popup')->render($this) ?>