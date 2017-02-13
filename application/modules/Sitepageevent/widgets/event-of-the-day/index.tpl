<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
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
    . 'application/modules/Sitepageevent/externals/styles/style_sitepageevent.css')
?>
<?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageevent.profile-sitepageevents', $this->eventOfDay->page_id, $layout);?>
<ul class="generic_list_widget generic_list_widget_large_photo">
	<li>
		<div class="photo">
		  <?php echo $this->htmlLink($this->eventOfDay->getHref(), $this->itemPhoto($this->eventOfDay), array('title' => $this->eventOfDay->getTitle())); ?>
		</div>
		<div class="info">
			<div class="title">
			  <?php echo $this->htmlLink($this->eventOfDay->getHref(), $this->eventOfDay->getTitle(), array('title' => $this->eventOfDay->getTitle())); ?>
			</div>
	    <div class="owner">
				<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $this->eventOfDay->page_id);?>
				<?php
				$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
				$tmpBody = strip_tags($sitepage_object->title);
				$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
				?>	
			<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->eventOfDay->page_id, $this->eventOfDay->user_id, $this->eventOfDay->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>      
			</div>	
		</div>
	</li>
</ul>		