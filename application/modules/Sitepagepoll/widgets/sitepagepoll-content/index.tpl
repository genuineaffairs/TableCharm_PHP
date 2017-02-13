<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
	if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebusiness.css', 0)  && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness')) {
		$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/common_style_page_business.css');
	} else {
			$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage.css');
	}

	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/css.php?request=/application/modules/Sitepagepoll/externals/styles/style_sitepagepoll.css')
?>
<div class="sitepage_viewpages_head">
	<?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
	<h2>
	  <?php echo $this->sitepage->__toString() ?>
	  <?php echo $this->translate('&raquo; ');?>
    <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Polls')) ?>
	  <?php echo $this->translate('&raquo; ');?>
	  <?php echo $this->sitepagepoll->title ?>
	</h2>
</div>	
<div class='sitepagepolls_view'>
	<h3>
		<?php echo $this->sitepagepoll->title ?>
    <?php if( $this->sitepagepoll->closed ): ?>
      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/close.png' alt="<?php echo $this->translate('Closed') ?>" />
		<?php endif ?><br/>
	</h3>
	<div class="sitepagepolls_view_info_date">
		<?php echo $this->translate('Created by %s', $this->htmlLink($this->sitepagepoll->getOwner(), $this->sitepagepoll->getOwner()->getTitle())) ?>
		<?php echo $this->timestamp($this->sitepagepoll->creation_date) ?>
		
		
		 <!--FACEBOOK LIKE BUTTON START HERE-->
     <?php  $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
      if (!empty ($fbmodule)) :
        $enable_facebookse = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse'); 
        if (!empty ($enable_facebookse) && !empty($fbmodule->version)) :
          $fbversion = $fbmodule->version; 
          if (!empty($fbversion) && ($fbversion >= '4.1.5')) { ?>
             <div class="mtop5">
                <script type="text/javascript">
                    var fblike_moduletype = 'sitepagepoll_poll';
		                var fblike_moduletype_id = '<?php echo $this->sitepagepoll->poll_id ?>';
                 </script>
                <?php echo Engine_Api::_()->facebookse()->isValidFbLike(); ?>
              </div>
          
          <?php } ?>
        <?php endif; ?>
   <?php endif; ?>
		  
	</div>
	<div class="sitepagepoll_desc">
		<?php echo nl2br($this->sitepagepoll->description); ?>
	</div>
	
	<?php
	  
		echo $this->render('_sitepagepoll.tpl')
	?>
	
	<?php echo $this->action("list", "comment", "seaocore", array("type"=>"sitepagepoll_poll",
"id"=>$this->sitepagepoll->poll_id)) ?>

</div>