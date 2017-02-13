<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$this->headLink()->appendStylesheet($this->seaddonsBaseUrl()
  	              . '/application/modules/Seaocore/externals/styles/styles.css');
?>
<ul class="seaocore_item_day">
	<li>
		<?php if(!empty($this->dayitem->photo_id)): ?>
			<?php echo $this->htmlLink($this->dayitem->getHref(), $this->itemPhoto($this->dayitem, 'thumb.normal'), array('title' => $this->dayitem->document_title) ); ?>
		<?php else: ?>
			<?php echo $this->htmlLink($this->dayitem->getHref(), '<img src="'. Engine_Api::_()->document()->sslThumbnail($this->dayitem->thumbnail) .'" class="thumb_profile" />', array('title' => $this->dayitem->document_title) ); ?>
		<?php endif; ?>
		
		<?php echo $this->htmlLink($this->dayitem->getHref(), $this->dayitem->document_title, array('title' => $this->dayitem->document_title)) ?>
	</li>
</ul>