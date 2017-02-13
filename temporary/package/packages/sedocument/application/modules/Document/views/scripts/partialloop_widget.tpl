<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: partialloop_widget.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<li>
	<?php if(!empty($this->document->photo_id)): ?>
		<?php echo $this->htmlLink($this->document->getHref(), $this->itemPhoto($this->document, 'thumb.icon'), array('title' => $this->document->document_title)) ?>
	<?php else: ?>
		<?php echo $this->htmlLink($this->document->getHref(), '<img src="'. Engine_Api::_()->document()->sslThumbnail($this->document->thumbnail) .'" class="thumb_icon" />', array('title' => $this->document->document_title) ) ?>
	<?php endif; ?>
	<div class='seaocore_sidebar_list_info'>
		<div class='seaocore_sidebar_list_title'>
			<?php
				$truncation = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.title.truncation', 0);
				$item_title = $this->document->document_title;
				if(empty($truncation)) {
					$item_title = Engine_Api::_()->document()->truncateText($item_title, 13);
				}
			?>
			<?php echo $this->htmlLink($this->document->getHref(), $item_title, array('title' => $this->document->document_title)) ?>
		</div>
		<div class='seaocore_sidebar_list_details'>
			<?php echo $this->translate(array('%s comment', '%s comments', $this->document->comment_count), $this->locale()->toNumber($this->document->comment_count)) ?> |
			<?php echo $this->translate(array('%s like', '%s likes', $this->document->like_count), $this->locale()->toNumber($this->document->like_count)) ?>
			<?php $show_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.rating', 1); ?>
			<?php if(($this->document->rating > 0) && ($show_rate == 1)):?>
				<?php 
					$currentRatingValue = $this->document->rating;
					$difference = $currentRatingValue- (int)$currentRatingValue;
					if($difference < .5) {
						$finalRatingValue = (int)$currentRatingValue;
					}
					else {
						$finalRatingValue = (int)$currentRatingValue + .5;
					}	
				?>
				<div class="seaocore_sidebar_list_details">
					<?php for($x = 1; $x <= $this->document->rating; $x++): ?>
						<span class="rating_star_generic rating_star" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
						</span>
					<?php endfor; ?>
					<?php if((round($this->document->rating) - $this->document->rating) > 0):?>
						<span class="rating_star_generic rating_star_half" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
						</span>
					<?php endif; ?>
				</div>	
			<?php endif; ?>
		</div>
	</div>
</li>