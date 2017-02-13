<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<ul class="sitepage_sidebar_list">
	<?php foreach ($this->paginator as $sitepagenote): ?>
		<li>
      <?php $this->sitepage_subject = Engine_Api::_()->getItem('sitepage_page', $sitepagenote->page_id);?>
			<?php if ($sitepagenote->photo_id == 0): ?>
				<?php if (isset($sitepagenote->page_photo_id) && $sitepagenote->page_photo_id == 0): ?>
					<?php echo $this->htmlLink($sitepagenote->getHref(), $this->itemPhoto($sitepagenote, 'thumb.icon', $sitepagenote->getTitle()), array('title' => $sitepagenote->getTitle())) ?>   
			<?php else: ?>
				<?php echo $this->htmlLink($this->sitepage_subject, $this->itemPhoto($this->sitepage_subject, 'thumb.icon', $this->sitepage_subject->getTitle()), array('title' => $this->sitepage_subject->getTitle())) ?>
			<?php endif; ?>
			<?php else: ?>			   
				<?php echo $this->htmlLink($sitepagenote->getHref(), $this->itemPhoto($sitepagenote, 'thumb.icon', $sitepagenote->getTitle()), array('title' => $sitepagenote->getTitle())) ?>
			<?php endif; ?>
			<div class="sitepage_sidebar_list_info">
				<div class="sitepage_sidebar_list_title">
					<?php
						$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.truncation.limit', 13); 
						$tmpBody = strip_tags($sitepagenote->title);
						$item_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
					?>	
					<?php echo $this->htmlLink($sitepagenote->getHref(), $item_title, array('title'=> $sitepagenote->getTitle()));?>
				</div>
				<div class="sitepage_sidebar_list_details">
					<?php
					$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
					$tmpBody = strip_tags(Engine_Api::_()->getItem('sitepage_page', $sitepagenote->page_id)->title);
					$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
					?>
					<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepagenote->page_id, $sitepagenote->owner_id, $sitepagenote->getSlug()), $page_title, array('title' => $tmpBody)) ?>    
				</div>
				<div class="sitepage_sidebar_list_details"> 
					<?php echo $this->translate(array('%s like', '%s likes', $sitepagenote->like_count), $this->locale()->toNumber($sitepagenote->like_count)) ?>
					|
					<?php echo $this->translate(array('%s view', '%s views', $sitepagenote->view_count), $this->locale()->toNumber($sitepagenote->view_count)) ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>