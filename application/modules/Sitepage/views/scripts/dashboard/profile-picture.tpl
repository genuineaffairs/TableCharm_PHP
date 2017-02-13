<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: profilepicture.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (empty($this->is_ajax)) : ?>
	<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

  <div class="layout_middle">
    <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
    <div class="sitepage_edit_content">
      <div class="sitepage_edit_header">
        <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage->page_id, $this->sitepage->owner_id, $this->sitepage->getSlug()),$this->translate('VIEW_PAGE')) ?>
        <h3><?php echo $this->translate('Dashboard: ') . $this->sitepage->title; ?></h3>
      </div>
      <div id="show_tab_content">
      <?php endif; ?>  

      <?php
      echo $this->form->render($this);
      ?>
      <?php if (empty($this->is_ajax)) : ?>
      </div>
    </div>
  </div>
<?php endif; ?>