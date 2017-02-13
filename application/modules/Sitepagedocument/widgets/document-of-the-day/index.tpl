<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
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
    . 'application/modules/Sitepagedocument/externals/styles/style_sitepagedocument.css')
?>
<?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagedocument.profile-sitepagedocuments', $this->documentOfDay->page_id, $layout);?>
<ul class="generic_list_widget generic_list_widget_large_photo">
	<li>
		<div class="photo generic_list_widget_day"">
			<?php
			//SSL WORK
			$this->https = 0;
			if (!empty($_SERVER["HTTPS"]) && 'on' == strtolower($_SERVER["HTTPS"])) {
			$this->https = 1;
			}

			if($this->https) {
			$this->manifest_path = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.manifestUrl', "page-documents");
			$this->documentOfDay->thumbnail = $this->baseUrl().'/'.$this->manifest_path."/ssl?url=".urlencode($this->documentOfDay->thumbnail);
			}
			?>
		  <?php echo $this->htmlLink($this->documentOfDay->getHref(), '<img src="'. $this->documentOfDay->thumbnail .'" alt="" class="thumb_profile" />', array() ) ?>
		</div>
		<div class="info">
			<div class="title">
			  <?php echo $this->htmlLink($this->documentOfDay->getHref(array('tab' => $tab_id)), $this->documentOfDay->getTitle(), array('title' => $this->documentOfDay->getTitle())); ?>
			</div>
	    <div class="owner">
				<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $this->documentOfDay->page_id);?>
				<?php
				$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
				$tmpBody = strip_tags($sitepage_object->title);
				$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
				?>	
			<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->documentOfDay->page_id, $this->documentOfDay->owner_id, $this->documentOfDay->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>   
			</div>	
		</div>
	</li>
</ul>		