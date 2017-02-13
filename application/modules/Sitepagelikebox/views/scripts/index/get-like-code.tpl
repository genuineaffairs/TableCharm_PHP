<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagelikebox
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: get-like-code.tpl 2011-10-10 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="global_form_popup">
	<h3><?php echo $this->translate( "Your Embeddable Page Badge code" ) ; ?></h3>
	<div>
	  <ul>
	    <li class="mtop10">
	      <?php echo $this->translate( "Copy this code and paste it into your web page." ) ; ?>
	    </li>
	    <li>
	      <textarea spellcheck="false" class="text-box" onclick="this.focus(); this.select()"><?php echo $this->code ; ?></textarea>
	    </li>
	    <li class="mtop10">
	      <button onclick="parent.Smoothbox.close();" ><?php echo $this->translate( 'Okay' ) ?></button>
	    </li>
	  </ul>
	</div>
</div>
<style type="text/css">
.text-box{
	border: 2px solid #ccc; 
	height: 100px;
	margin-top: 10px;
	padding: 5px;
	width: 600px;
}
</style>