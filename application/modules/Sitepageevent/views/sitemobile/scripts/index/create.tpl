<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<!--BREADCRUMB WORK -->
<?php 
$breadcrumb = array(
	array("href"=>$this->sitepage->getHref(),"title"=>$this->sitepage->getTitle(),"icon"=>"arrow-r"),
	array("href"=>$this->sitepage->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Events","icon"=> "arrow-d"),
);

echo $this->breadcrumb($breadcrumb);
?>

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