<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';

	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepagenote/externals/styles/style_sitepagenote.css')
?>
<?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagenote.profile-sitepagenotes', $this->noteOfDay->page_id, $layout);?>
<ul class="generic_list_widget generic_list_widget_large_photo">
	<li>
    <?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $this->noteOfDay->page_id);?>
		<div class="photo">
			<?php if($this->noteOfDay->photo_id == 0):?>
				<?php 
				if($sitepage_object->photo_id == 0):?>
					<?php echo $this->htmlLink($this->noteOfDay->getHref(),$this->itemPhoto($this->noteOfDay, 'thumb.profile', $this->noteOfDay->getTitle())) ?>   
				<?php else:?>
					<?php echo $this->htmlLink($this->noteOfDay->getHref(),$this->itemPhoto($sitepage_object, 'thumb.profile', $this->noteOfDay->getTitle())) ?>
				<?php endif;?>
			<?php else:?>
				<?php echo $this->htmlLink($this->noteOfDay->getHref(),$this->itemPhoto($this->noteOfDay, 'thumb.profile', $this->noteOfDay->getTitle())) ?>
			<?php endif;?>
		</div>
		<div class="info">
			<div class="title">
			  <?php echo $this->htmlLink($this->noteOfDay->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->string()->truncate($this->noteOfDay->getTitle(), 45), 10),array('title' => $this->noteOfDay->getTitle())) ?>  
			</div>
	    <div class="owner">
				<?php
				$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
				$tmpBody = strip_tags($sitepage_object->title);
				$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
				?>	
			<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->noteOfDay->page_id, $this->noteOfDay->owner_id, $this->noteOfDay->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>      
			</div>	
		</div>
	</li>
</ul>		