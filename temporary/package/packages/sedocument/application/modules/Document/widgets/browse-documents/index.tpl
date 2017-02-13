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

<script type="text/javascript">

  var categoryBrowseAction = function(category){
    if($('filter_form')) {
       var form = document.getElementById('filter_form');
      }else if($('filter_form_browse_category')){
				var form = document.getElementById('filter_form_browse_category');
    }
   
    form.elements['category'].value = category;
		form.elements['category_id'].value = category;
		form.submit();
  }

</script>

<?php $document_paginator = Zend_Registry::get('document_paginator'); ?>

<?php if( $this->paginator->count() > 0): ?>

	<form id='filter_form_browse_category' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'browse'), 'document_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="category" name="category"  value=""/>
		<input type="hidden" id="category_id" name="category_id"  value=""/>
  </form>

	<ul class="seaocore_browse_list">
		<?php foreach( $this->paginator as $document ): ?>
			<li>
				<div class='seaocore_browse_list_photo'>
					<?php if(!empty($document->photo_id)): ?>
						<?php echo $this->htmlLink($document->getHref(), $this->itemPhoto($document, 'thumb.normal'), array('title' => $document->document_title)) ?>
					<?php elseif(!empty($document->thumbnail)): ?>
						<?php echo $this->htmlLink($document->getHref(), '<img src="'. Engine_Api::_()->document()->sslThumbnail($document->thumbnail) .'" class="thumb_normal" />', array('title' => $document->document_title) ) ?>
					<?php else: ?>
						<?php echo $this->htmlLink($document->getHref(), '<img src="application/modules/Document/externals/images/document_thumb.png" class="thumb_normal" />', array('title' => $document->document_title) ) ?>
					<?php endif;?>
				</div>
				<div class='seaocore_browse_list_info'>
					<div class='seaocore_browse_list_info_title'>
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
									<span class="rating_star_generic rating_star_half" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>"> </span>
								<?php endif; ?>
							</span>
						<?php endif; ?>
						
						<p>
							<?php
								$truncation = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.title.truncation', 0);
								$item_title = $document->document_title;
								if(empty($truncation)) {
									$item_title = Engine_Api::_()->document()->truncateText($item_title, 80);
								}
							?>
							<?php echo $this->htmlLink($document->getHref(), $item_title, array('title' => $document->document_title)) ?>
						</p>
					</div>
					<div class='seaocore_browse_list_info_date'>
						<?php if(empty($document_paginator)){exit();} ?>
						<?php echo $this->translate('Created %s by %s', $this->timestamp($document->creation_date), $document->getOwner()->toString()) ?>,
						<?php echo $this->translate(array('%s comment', '%s comments', $document->comment_count), $this->locale()->toNumber($document->comment_count)) ?>, 
						<?php echo $this->translate(array('%s view', '%s views', $document->views), $this->locale()->toNumber($document->views)) ?>,
						<?php echo $this->translate(array('%s like', '%s likes', $document->like_count), $this->locale()->toNumber($document->like_count)) ?>,
						<?php if($document->category_id): ?>
							<?php $category = Engine_Api::_()->getDbtable('categories', 'document')->getCategory($document->category_id); ?>
							<?php echo $this->translate('Category:');?> <a href='javascript:void(0);' onclick='javascript:categoryBrowseAction(<?php echo $document->category_id?>);'><?php echo $category->category_name ?></a>  
						<?php endif; ?> 
					</div>
					<div class='seaocore_browse_list_info_blurb'>
						<?php echo Engine_Api::_()->document()->truncateText($document->document_description, 560); ?>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
		<?php if(empty($this->current_api)){ echo $this->translate($this->document_current_api); } ?>
	</ul>
	<?php 
		if(!empty($document_paginator)) {
			echo $this->paginationControl($this->paginator, null, null, array('query' => $this->formValues,'pageAsQuery' => true,));
		} else {
			echo $this->translate($this->document_current_api);
		} ?>
<?php elseif($this->search || $this->show || $this->category):?>	
	<div class="tip">
		<span>
			<?php echo $this->translate('No documents were found matching your search criteria.');?>
			<?php if ($this->can_create): ?>
			<?php if(empty($document_paginator)){exit();} else { 
					echo $this->translate('Be the first to %1$swrite%2$s one!', '<a href="'.$this->url(array(), 'document_create').'">', '</a>');} ?>
			<?php endif; ?>
		</span>
	</div>
<?php else: ?>
	<div class="tip">
		<span>
			<?php if(empty($document_paginator)){exit();} else { echo $this->translate('Nobody has created a document yet.'); } ?>
			<?php if ($this->can_create):  ?>
				<?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array(), 'document_create').'">', '</a>'); ?>
			<?php endif; ?>
		</span>
	</div>	
<?php endif; ?>