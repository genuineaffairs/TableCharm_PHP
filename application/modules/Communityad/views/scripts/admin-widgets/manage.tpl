<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate('Community Ads Plugin') ?>
</h2>



<?php if( count($this->navigation) ): ?>
<div class='communityad_admin_tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
	<?php
	// Show link for "Guidelines to display ads over Non-widgetized pages".
		/*echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'settings', 'action' => 'guidelines'), $this->translate("Guidelines for placing Ad Blocks on Non-widgetized Pages"), array('class'=>'buttonlink cmad_icon_help'));*/
 
		    echo $this->htmlLink(array('route'=>'admin_default','module' => 'communityad','controller' => 'settings','action' => 'faq', 'faq_type' => 'blocks'), $this->translate("Guidelines for placing Ad Blocks on Non-widgetized Pages"), array('class'=>'buttonlink cmad_icon_help'))
		  
	?>
	<br><br>
  <div class="tip">
    <span>We have placed "Display Advertisements" widget on all the pages for which you had created ‘Ad Blocks’ from here. So, please manage settings for displaying ads from Layout Editor Section.</span>
    
  </div>
