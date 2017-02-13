<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="sitepage_viewpages_head">
 <?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
  <h2>	
    <?php echo $this->sitepage->__toString() ?>	
    <?php echo $this->translate('&raquo; '); ?>
     <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>Zend_Controller_Front::getInstance()->getRequest()->getParam('tab_id', null))),$this->translate('Events')) ?>
  
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->sitepageevent_subject->getTitle(); ?>
  </h2>
</div>