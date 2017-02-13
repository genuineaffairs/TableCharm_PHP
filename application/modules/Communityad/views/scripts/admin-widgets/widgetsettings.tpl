<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: widgetsetting.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate("Community Ads Plugin") ?></h2>
<script type="text/javascript">
 var showAdminPages =function(page_id, widget_id, page_type){
		if( page_type == 'page' ) {
			widget_id = 0;
		}
    window.location.href= en4.core.baseUrl+'admin/communityad/widgets/widgetsettings/page_id/' + page_id + '/widget_id/' + widget_id;
  }
</script>
<?php if( count($this->navigation) ): ?>
	<div class='communityad_admin_tabs'>
	  <?php
	  // Render the menu
	  //->setUlClass()
	  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
	  ?>
	</div>
<?php endif; ?>
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'widgets', 'action' => 'manage'), $this->translate("Back to Manage Ad Blocks"), array('class'=>'cmad_icon_back buttonlink')) ?>
<br style="clear:both;" /><br />
<div class="seaocore_settings_form">	
	<div class='settings'>
	  <?php echo $this->form->render($this); ?>
	</div>
</div>
<style type="text/css">
#dummy_msg-wrapper .form-label{
	width:800px;
	color:red;
}
#dummy_msg-wrapper .form-label label{
	max-width:800px;
}
#TB_iframeContent{
	width:360px !important;
	height:500px !important;
	overflow:auto;
}
</style>