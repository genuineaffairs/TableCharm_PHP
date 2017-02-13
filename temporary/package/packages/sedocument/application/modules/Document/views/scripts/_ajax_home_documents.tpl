<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _ajax_home_documents.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->ratngShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.rating', 1);?>

<?php if( $this->list_view): ?>
	<div id="rgrid_view_document" style="display: none;">
	<?php if (count($this->documents)): ?>
		<?php $counter='1';
				$limit = $this->active_tab_list;
		?>
		<ul class="seaocore_browse_list">
			<?php foreach ($this->documents as $document): ?>
				<?php if($counter > $limit):
					break;
					endif;
					$counter++;
				?>
				<li>
					<div class='seaocore_browse_list_photo'>
						<?php if(!empty($document->photo_id)): ?>
							<?php echo $this->htmlLink($document->getHref(), $this->itemPhoto($document, 'thumb.normal'), array('title' => $document->document_title)) ?>
						<?php else: ?>
							<?php echo $this->htmlLink($document->getHref(), '<img src="'. Engine_Api::_()->document()->sslThumbnail($document->thumbnail) .'" class="thumb_normal" />', array('title' => $document->document_title) ); ?>
						<?php endif; ?>
					</div>
					<div class='seaocore_browse_list_info'>
						<div class='seaocore_browse_list_info_title'>
							<span>
								<?php if ($document->sponsored == 1): ?>
									<?php echo $this->htmlImage('application/modules/Seaocore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
								<?php endif; ?>
								<?php if ($document->featured == 1): ?>
									<?php echo $this->htmlImage('application/modules/Seaocore/externals/images/featured.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
								<?php endif; ?>
							</span>
							<div class="seaocore_title">
								<?php echo $this->htmlLink($document->getHref(), $document->document_title) ?>
							</div>
						</div>
						<?php if($this->ratngShow && $document->rating > 0): ?>
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
							<span class="clr" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
								<?php for ($x = 1; $x <= $document->rating; $x++): ?>
									<span class="rating_star_generic rating_star" ></span>
								<?php endfor; ?>
								<?php if ((round($document->rating) - $document->rating) > 0): ?>
									<span class="rating_star_generic rating_star_half" ></span>
								<?php endif; ?>
							</span>
						<?php endif; ?>

						<div class='seaocore_browse_list_info_date'>
							<?php echo $this->timestamp(strtotime($document->creation_date)) ?> - <?php echo $this->translate('posted by'); ?>
							<?php echo $this->htmlLink($document->getOwner()->getHref(), $document->getOwner()->getTitle()) ?>
							</div>

						<div class='seaocore_browse_list_info_date'>
							<?php echo $this->translate(array('%s comment', '%s comments', $document->comment_count), $this->locale()->toNumber($document->comment_count)) ?>,
							<?php echo $this->translate(array('%s view', '%s views', $document->views), $this->locale()->toNumber($document->views)) ?>,
							<?php echo $this->translate(array('%s like', '%s likes', $document->like_count), $this->locale()->toNumber($document->like_count)) ?>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
<?php endif; ?>
<?php  if( $this->grid_view):?>
<div id="rimage_view_document" style="display: none;">
	<?php if (count($this->documents)): ?>
	
	  <?php $counter=1;
	  			$total_list = count($this->documents);
					$limit = $this->active_tab_image;
		?>
		<div class="seaocore_img_view">
			<?php foreach ($this->documents as $document): ?>
        <?php if($counter > $limit):
					break;
					endif;
					$counter++;
				?>
				<div class="seaocore_img_view_thumb" >
					<ul class="jq-document_tooltip">
						<li>
							<a href="<?php echo $document->getHref(); ?>">
								<span>
									<?php if(!empty($document->photo_id)): ?>
										<?php echo $this->itemPhoto($document, 'thumb.normal'); ?>
									<?php else: ?>
										<img src="<?php echo Engine_Api::_()->document()->sslThumbnail($document->thumbnail); ?>" alt="" />
									<?php endif; ?>
								</span>
							</a>
              <?php echo $this->htmlLink($document->getHref(), Engine_Api::_()->document()->truncateText($document->document_title, 23)); ?>
				    	<div class="seaocore_thumbs_tooltip document_tooltip_show">
								<div class="seaocore_thumbs_tooltip_wrapper">
									<div class="seaocore_thumbs_tooltip_inner">
										<div class="seaocore_thumbs_tooltip_arrow">
											<img src='application/modules/Seaocore/externals/images/tip-arrow-top.png' alt="" />
										</div>
				    		  	<div class='seaocore_thumbs_tooltip_content'>
				    					<div class="title">
				          			<?php echo $this->htmlLink($document->getHref(), $document->getTitle()); ?>
						            <span>
							            <?php if ($document->featured == 1): ?>
								            <?php echo $this->htmlImage('application/modules/Seaocore/externals/images/featured.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
							            <?php endif; ?>
						            </span>
						             <span>
							            <?php if ($document->sponsored == 1): ?>
								            <?php echo $this->htmlImage('application/modules/Seaocore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
						            <?php endif; ?>
						            </span>
				        			</div>
				          		<?php if (($document->rating > 0) && $this->ratngShow): ?>
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
				          			<span class="clr" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
							            <?php for ($x = 1; $x <= $document->rating; $x++): ?>
							            <span class="rating_star_generic rating_star" ></span>
							            <?php endfor; ?>
							            <?php if ((round($document->rating) - $document->rating) > 0): ?>
							            <span class="rating_star_generic rating_star_half" ></span>
							            <?php endif; ?>
							          </span>
					            <?php endif; ?>
											<div class='recipes_tooltip_info_stat clr'>
								      	<?php echo $this->timestamp(strtotime($document->creation_date)) ?> - <?php echo $this->translate('posted by'); ?>
                        <?php echo $this->htmlLink($document->getOwner()->getHref(), $document->getOwner()->getTitle()) ?>
            	        </div>
								      <div class='recipes_tooltip_info_stat'>
								      	<?php echo $this->translate(array('%s comment', '%s comments', $document->comment_count), $this->locale()->toNumber($document->comment_count)) ?>,
								        <?php echo $this->translate(array('%s view', '%s views', $document->views), $this->locale()->toNumber($document->views)) ?>,
                           <?php echo $this->translate(array('%s like', '%s likes', $document->like_count), $this->locale()->toNumber($document->like_count)) ?>
									    </div>
										</div>
				      		</div>
								</div>
				      </div>
		      	</li>
		      </ul>
			  </div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
<?php endif; ?>