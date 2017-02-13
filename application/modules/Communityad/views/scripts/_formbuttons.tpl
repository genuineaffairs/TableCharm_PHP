<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formbuttons.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  echo '
    <div class="form-wrapper">
			<div class="form-label"><label></label></div>
    	<div class="form-element">
    		<button name="submit_temp" id="submit_temp" type="submit">'.$this->translate("Send Message"). '</button>
    		<button name="preview" id="preview" type="button" onclick="parent.Smoothbox.close();">'.$this->translate("Cancel").'</button>
  		</div>
  	</div>'
?>