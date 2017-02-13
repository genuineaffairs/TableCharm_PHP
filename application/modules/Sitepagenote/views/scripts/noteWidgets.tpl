<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: noteWidgets.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<?php if ($sitepagenote->photo_id == 0): ?>
  <?php if ($sitepagenote->page_photo_id == 0): ?>
    <?php echo $this->htmlLink($sitepagenote->getHref(), $this->itemPhoto($sitepagenote, 'thumb.icon', $sitepagenote->getTitle()), array('title' => $sitepagenote->getTitle())) ?>   
  <?php else: ?>
    <?php echo $this->htmlLink($this->sitepage_subject, $this->itemPhoto($this->sitepage_subject, 'thumb.icon', $this->sitepage_subject->getTitle()), array('title' => $this->sitepage_subject->getTitle())) ?>
  <?php endif; ?>
<?php else: ?>			   
  <?php echo $this->htmlLink($sitepagenote->getHref(), $this->itemPhoto($sitepagenote, 'thumb.icon', $sitepagenote->getTitle()), array('title' => $sitepagenote->getTitle())) ?>
<?php endif; ?>
<?php
  $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.truncation.limit', 13); 
  $tmpBody = strip_tags($sitepagenote->title);
  $item_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
?>		