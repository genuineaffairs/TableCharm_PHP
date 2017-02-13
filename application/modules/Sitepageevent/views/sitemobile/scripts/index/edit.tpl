<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php echo $this->content()->renderWidget("sitepageevent.sitemobile-breadcrumb",array('noShowTitle'=> 1,'tab'=>$this->tab_selected_id, 'icon'=>"arrow-d")); ?>

<div class="layout_middle">
  <?php echo $this->form->render($this) ?>
</div>	

<script type="text/javascript">
  sm4.core.runonce.add(function() { 
    if (DetectAllWindowsMobile()) {
      $.mobile.activePage.find('#dummy-wrapper').css('display', 'block');
      $.mobile.activePage.find('#photo-wrapper').css('display', 'none');
    } else {
      $.mobile.activePage.find('#photo-wrapper').css('display', 'block');
      $.mobile.activePage.find('#dummy-wrapper').css('display', 'none');
    } 
  });
</script>