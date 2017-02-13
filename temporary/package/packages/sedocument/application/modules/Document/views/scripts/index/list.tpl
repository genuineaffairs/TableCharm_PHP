<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: list.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  var pageAction = function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
  var categoryListAction = function(category){
    $('page').value = 1;
    $('category').value = category;
    $('filter_form').submit();
  }
  var tagListAction = function(tag){
    $('page').value = 1;
    $('tag').value = tag;
    $('filter_form').submit();
  }
</script>

<div class='layout_left'>
  <div class='seaocore_gutter_photo'>
    <?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner)) ?>
    <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle(), array('class' => 'seaocore_gutter_title')) ?>
  </div> 
  <div class="quicklinks seaocore_gutter_blocks">
  	<ul>
	  	<li>
	    	<?php echo $this->htmlLink(array('route' => 'document_browse'), $this->translate('View All Documents'), array('class' => 'buttonlink icon_type_document')) ?>
	    </li>
    </ul>
  </div> 
	<div class="seaocore_gutter_blocks generic_layout_container">
  	<h3><?php echo $this->translate('Search Documents')?></h3>
	  <ul>
	    <form id='filter_form' class='global_form_box' method='get'>
	      <input type='text' id="search" name="<?php echo $this->translate('search')?>" value="<?php if( $this->search ) echo $this->search; ?>"/>
	      <input type="hidden" id="tag" name="tag" value="<?php if( $this->tag ) echo $this->tag; ?>"/>
	      <input type="hidden" id="category" name="category" value="<?php if( $this->category ) echo $this->category; ?>"/>
	      <input type="hidden" id="page" name="page" value="<?php if( $this->page ) echo $this->page; ?>"/>
	    </form>
	  </ul>
	</div>  
  <?php if( count($this->userCategories) ): ?>
    <div class="seaocore_gutter_blocks generic_layout_container">
    	<h3><?php echo $this->translate('Categories');?></h3>
      <ul class="quicklinks">
      	<li> 
      		<a href='javascript:void(0);' onclick='javascript:categoryListAction(0);' <?php if ($this->category==0) echo " style='font-weight: bold;'";?>><?php echo $this->translate('All Categories');?></a>
      	</li>
        <?php foreach ($this->userCategories as $category): ?>
        	<li>
        		<a href='javascript:void(0);' onclick='javascript:categoryListAction(<?php echo $category->category_id?>);' <?php if( $this->category == $category->category_id ) echo " style='font-weight: bold;'";?>><?php echo $category->category_name?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>  
  <?php endif; ?>
	<?php if( count($this->userTags) ):?>
 		<div class="seaocore_gutter_blocks generic_layout_container">
			<h3><?php echo $this->translate("%s's Tags", $this->owner->getTitle()); ?></h3>
      <ul class="seaocore_sidebar_list">
      	<li>
      		<div>
        		<?php foreach ($this->userTags as $tag): ?>
          		<a href='javascript:void(0);' onclick='javascript:tagListAction(<?php echo $tag->tag_id; ?>);' <?php if ($this->tags==$tag->tag_id) echo " style='font-weight: bold;'";?>>
          			#<?php echo $tag->text?>
          		</a>&nbsp;
        		<?php endforeach; ?>
        	</div>
        </li>		
      </ul>
    </div>  
  <?php endif; ?>
</div>

<div class='layout_middle'>
	<div class="seaocore_gutter_view">
		<div class='seaocore_gutter_view_title'>
			<h3><?php echo $this->translate("%s's Documents", $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()))?></h3>
		</div>
		<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
	    <ul class="seaocore_browse_list document_browse_list">
	    	<?php foreach ($this->paginator as $item): ?>
		     	<li>
	        	<div class="seaocore_browse_list_photo">
							<?php if(!empty($item->photo_id)): ?>
								<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'), array('title' => $item->document_title) ) ?>
							<?php elseif(!empty($item->thumbnail)): ?>
								<?php echo $this->htmlLink($item->getHref(), '<img src="'. Engine_Api::_()->document()->sslThumbnail($item->thumbnail) .'" class="thumb_normal" />', array('title' => $item->document_title) ) ?>
							<?php else: ?>
								<?php echo $this->htmlLink($item->getHref(), '<img src="application/modules/Document/externals/images/document_thumb.png" class="thumb_normal" />', array('title' => $document->document_title) ) ?>
							<?php endif;?>
	          </div>
	         	<div class='seaocore_browse_list_info'>
							<div class='seaocore_browse_list_info_title'>
	            	<span>
		          		<?php if($item->featured == 1): ?>
	            			<?php echo $this->htmlImage('application/modules/Seaocore/externals/images/featured.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
	            		<?php endif;?>
	          		</span>

	            	<span>
		          		<?php if($item->sponsored == 1): ?>
	            			<?php echo $this->htmlImage('application/modules/Seaocore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
	            		<?php endif;?>
	          		</span>

							  <?php if($this->show_rate && $item->rating > 0):?>
									<?php 
										$currentRatingValue = $item->rating;
										$difference = $currentRatingValue- (int)$currentRatingValue;
										if($difference < .5) {
											$finalRatingValue = (int)$currentRatingValue;
										}
										else {
											$finalRatingValue = (int)$currentRatingValue + .5;
										}	
									?>
									<span class="list_rating_star">
	        					<?php for($x = 1; $x <= $item->rating; $x++): ?>
											<span class="rating_star_generic rating_star" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
											</span>
										<?php endfor; ?>
										<?php if((round($item->rating) - $item->rating) > 0):?>
											<span class="rating_star_generic rating_star_half" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
											</span>
										<?php endif; ?>
									</span>
	      				<?php endif; ?>
	          	
	          		<p>
									<?php
										$truncation = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.title.truncation', 0);
										$item_title = $item->document_title;
										if(empty($truncation)) {
											$item_title = Engine_Api::_()->document()->truncateText($item_title, 70);
										}
									?>
									<?php echo $this->htmlLink($item->getHref(), $item_title, array('title' => $item->document_title)) ?>
								</p>
	          	</div>
							<div class='seaocore_browse_list_info_date'>
								<?php echo $this->translate('Created %s by %s', $this->timestamp($item->creation_date), $item->getOwner()->toString()) ?>,
								<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>, 
	   	 					<?php echo $this->translate(array('%s view', '%s views', $item->views), $this->locale()->toNumber($item->views)) ?>,
								<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>,
								<?php if($item->category_id): ?>
									<?php $category = Engine_Api::_()->getDbtable('categories', 'document')->getCategory($item->category_id); ?>
									<?php echo $this->translate('Category:');?> <a href='javascript:void(0);' onclick='javascript:categoryListAction(<?php echo $item->category_id?>);'><?php echo $category->category_name ?></a>  
								<?php endif; ?> 
	            </div>	            
		          <div class="seaocore_browse_list_info_blurb">
								<?php echo Engine_Api::_()->document()->truncateText($item->document_description, 560); ?>
		          </div>
		        </div> 
			    </li>
	    	<?php endforeach; ?>
	    </ul>
		<?php elseif( $this->category || $this->tags || $this->search): ?>
			<div class="tip">
				<span>
					<?php echo $this->translate('No documents were found matching your search criteria.'); ?>
				</span>
			</div>
		<?php else: ?>
			<div class="tip">
				<span>
					<?php echo $this->translate('%1$s has not created a document yet.', $this->owner->getTitle()); ?>
				</span>
			</div>
		<?php endif; ?>
		<div class='browse_nextlast'>
			<?php echo $this->paginationControl($this->paginator, null, null, array('query' => $this->formValues,'pageAsQuery' => true,)); ?>
		</div>
	</div>	
</div>