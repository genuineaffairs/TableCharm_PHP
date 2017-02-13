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
<ul class="seaocore_profile_list">
  <?php foreach ($this->paginator as $document): ?>
    <li>
  		<div class='seaocore_profile_list_photo'>
				<?php if(!empty($document->photo_id)):?>
					<?php echo $this->htmlLink($document->getHref(), $this->itemPhoto($document, 'thumb.normal'), array('title' => $document->document_title) ) ?>
				<?php else: ?>
					<?php echo $this->htmlLink($document->getHref(), '<img src="'. Engine_Api::_()->document()->sslThumbnail($document->thumbnail) .'" class="thumb_normal" />', array('title' => $document->document_title) ) ?>
				<?php endif; ?>

			</div>
    	<div class='seaocore_profile_list_info'>
    		<div class='seaocore_profile_list_title'>
        	<span>
        		<?php if($document->featured == 1): ?>
        			<?php echo $this->htmlImage('application/modules/Seaocore/externals/images/featured.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
        		<?php endif;?>
      		</span>

        	<span>
        		<?php if($document->sponsored == 1): ?>
        			<?php echo $this->htmlImage('application/modules/Seaocore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
        		<?php endif;?>
      		</span>

    			<?php if(($document->rating > 0) && ($this->show_rate == 1)):?>
						<span class="list_rating_star">
							<?php 
								$currentRatingValue = $document->rating;
								$difference = $currentRatingValue- (int)$currentRatingValue;
								if($difference < .5) {
									$finalRatingValue = (int)$currentRatingValue;
								}
								else {
									$finalRatingValue = (int)$currentRatingValue + .5;
								}	
							?>
      				<?php for($x = 1; $x <= $document->rating; $x++): ?>
								<span class="rating_star_generic rating_star" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
								</span>
							<?php endfor; ?>
							<?php if((round($document->rating) - $document->rating) > 0):?>
								<span class="rating_star_generic rating_star_half" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>>">
								</span>
							<?php endif; ?>
						</span>
    			<?php endif; ?>
    			
      		<p>
						<?php
							$truncation = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.title.truncation', 0);
							$item_title = $document->document_title;
							if(empty($truncation)) {
								$item_title = Engine_Api::_()->document()->truncateText($item_title, 60);
							}
						?>
						<?php echo $this->htmlLink($document->getHref(), $item_title, array('title' => $document->document_title)) ?>
					</p>
      	</div>
      	<div class='seaocore_profile_info_date'>
      		<?php echo $this->translate('Created by %s about %s', $document->getOwner()->toString(), $this->timestamp($document->creation_date)) ?>,
     			<?php echo $this->translate(array('%s comment', '%s comments', $document->comment_count), $this->locale()->toNumber($document->comment_count)) ?>, 
     	 		<?php echo $this->translate(array('%s like', '%s likes', $document->like_count), $this->locale()->toNumber($document->like_count)) ?>,
					<?php if($document->category_id): ?>
						<?php $category = Engine_Api::_()->getDbtable('categories', 'document')->getCategory($document->category_id); ?>
						<?php echo $this->translate('Category:');?> <?php echo $category->category_name ?>
					<?php endif; ?> 
      	</div>
      	<div class='seaocore_profile_info_blurb'>
      		<?php if(empty($this->document_rating)){ echo $this->translate($this->document_current_ratings); } else { echo Engine_Api::_()->document()->truncateText($document->document_description, 560); } ?>
      	</div>
    	</div>
  	</li>
  <?php endforeach; ?>
</ul>
<?php if($this->paginator->getTotalItemCount() > $this->items_per_page):?>
	<div class="seaocore_profile_list_more">
	  <?php echo $this->htmlLink($this->url(array('user_id' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'document_list'), $this->translate('View All Documents'), array('class' => 'buttonlink icon_type_document')) ?>
	</div>
<?php endif;?>