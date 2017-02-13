<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate("CommunityAds Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='communityad_admin_tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
<div>
<p style="display:block;">
	<?php
	// Show link for "Create Featured Content".
		echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'widgets', 'action' => 'adreports'), $this->translate("Advertisement User Reports"), array('class'=>'cmad_icon_package_add'));
	?>
</p>
</div>

<br>
<div>
<p style="display:block;">
	<?php
	// Show link for "Create Featured Content".
		echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'statistics', 'action' => 'export-report'), $this->translate("Export Report"), array('class'=>'cmad_icon_package_add'));
	?>
</p>
</div>