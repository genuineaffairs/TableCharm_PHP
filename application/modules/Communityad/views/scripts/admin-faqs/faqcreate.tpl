<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faqcreate.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if( count($this->navigation) ): ?>
<div class='communityad_admin_tabs'>
  <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
<p>
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'helps', 'action' => 'help-and-learnmore'), $this->translate("Back to Manage Advertising Help Pages"), array('class'=>'cmad_icon_back buttonlink')) ?>&nbsp;&nbsp;&nbsp;
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'helps', 'action' => 'help-page-create', 'page_id' => $this->page_id), $this->translate("Back to FAQ Manage Page"), array('class'=>'cmad_icon_back buttonlink')) ?>
</p>
<br />
<div id='my_content'></div>
<div class='clear'>
  <div class='settings'>
		<?php echo $this->form->render($this)  ?>
  </div>
</div>
<style type="text/css">
.settings #submit-label {
	display: block;
}
</style>