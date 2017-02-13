<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="sm_profile_item_photo">
  <?php if($this->subject()->getType() == 'blog' || $this->subject()->getType() == 'sitepagedocument_document' || $this->subject()->getType() == 'sitebusinessdocument_document' || $this->subject()->getType() == 'sitegroupdocument_document' ) :?>
    <?php echo $this->itemPhoto($this->subject()->getOwner(), 'thumb.profile') ?>
  <?php else :?>
	  <?php echo $this->itemPhoto($this->subject(), 'thumb.profile') ?>
  <?php endif;?>
</div>
<div class="sm_profile_item_info">
	<div class="sm_profile_item_title">
		<?php echo $this->subject()->getTitle() ?>
	</div>
</div>
<br /><br />
<?php if (($this->subject()->getType() == 'user') && (!$this->subject()->authorization()->isAllowed(null, 'view'))): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('This profile is private - only friends of this member may view it.'); ?>
    </span>
  </div>
<?php endif; ?>