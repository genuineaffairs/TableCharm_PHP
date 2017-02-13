<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: partialWidget.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<?php
	//SSL WORK
	$this->https = 0;
	if (!empty($_SERVER["HTTPS"]) && 'on' == strtolower($_SERVER["HTTPS"])) {
		$this->https = 1;
	}

	if($this->https) {
		$this->manifest_path = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.manifestUrl', "page-documents");
		$this->sitepagedocument->thumbnail = $this->baseUrl().'/'.$this->manifest_path."/ssl?url=".urlencode($this->sitepagedocument->thumbnail);
	}
?>

<li>
	<?php echo $this->htmlLink($this->sitepagedocument->getHref(), '<img src="' . $this->sitepagedocument->thumbnail . '" class="list_thumb" />', array('title' => $this->sitepagedocument->sitepagedocument_title)) ?>
	<div class='sitepage_sidebar_list_info'>
		<div class='sitepage_sidebar_list_title'>
			<?php
				$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.truncation.limit', 13);
				$item_title = $this->sitepagedocument->truncateText($this->sitepagedocument->sitepagedocument_title, $truncation_limit);
			?>
			<?php echo $this->htmlLink($this->sitepagedocument->getHref(), $item_title, array('title' => $this->sitepagedocument->sitepagedocument_title)) ?>
		</div>
		<div class='sitepage_sidebar_list_details'>