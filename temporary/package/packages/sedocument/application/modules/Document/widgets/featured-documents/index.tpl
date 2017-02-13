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
  	              . '/application/modules/Document/externals/styles/style_document.css');
?>

<?php 
	$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Document/externals/scripts/MooTooltips.js');
?>

<script language="javascript" type="text/javascript">
	window.addEvent('load', function(){
		var browserName=navigator.appName;
		var document_height = 120;
		if (browserName=="Microsoft Internet Explorer")
		{
			document_height =105;
		}
		new MooTooltips({
			hovered:'.tipper',		// the element that when hovered shows the tip
			ToolTipClass:'ToolTips',	// tooltip display class
			toolTipPosition:1, // -1 top; 1: bottom - set this as a default position value if none is set on the element
			sticky:false,		// remove tooltip if closed
			fromTop:document_height,		// distance from mouse or object
			fromLeft: -74,	// distance from left
			duration:100,		// fade effect transition duration
			fadeDistance: 0    // the distance the tooltip starts the morph
		});		
	});
</script>

<?php if( $this->paginator->count() > 0): ?>
	<?php $current_index = 0; ?>
	<?php $total_featured = $this->paginator->count(); ?>
	<?php if($total_featured > 0 ): ?>
		<?php $current_index = 3 ?>
		<?php $previous_index ?>
		<?php if($current_index == 0): ?>
			<?php $previous_index = ($total_featured-1) ?>
		<?php else: ?>
			<?php $previous_index = $current_index-1 ?>
		<?php endif; ?>
			<?php $next_index ?>
		<?php if($current_index == $total_featured-1): ?>
			<?php $next_index = 0?>
		<?php else: ?>
			<?php $next_index = $current_index+1 ?>
		<?php endif; ?>
	<?php endif;?>	
	<?php $current_num ?> <?php $current_num = $current_index+1 ?>

	<h3><?php echo $this->translate('Featured Documents'); ?></h3>
	<div class="browse_document_carousel">
		<div class="document_carousel">
			<table cellpadding='0' cellspacing='0' align='center'>
				<tr>
					<td>
						<a href='javascript:void(0);' onClick='moveLeft();this.blur()'>
						<?php echo $this->htmlImage('application/modules/Document/externals/images/doc_car_left.png', '', array('align'=>'left', 'onMouseOver'=>'this.src="application/modules/Document/externals/images/doc_car_left_over.png";','onMouseOut'=>'this.src="application/modules/Document/externals/images/doc_car_left.png";', 'border'=>'0')) ?>
						</a>
					</td>
					<td>
						<div id='document_carousel' class="document_carousel_documents">
							<table cellpadding='0' cellspacing='0'>
								<tr>
									<?php foreach($this->paginator as $key => $featureDocument):  ?>  
										<td id='thumb<?php echo $key+1 ?>' class='browse_document_carousel_item' align="center" width="120">
											<div class="tipper" rel="{content:'focus_tooltip_document_featured_<?php echo $featureDocument->document_id; ?>'}">

												<?php if(!empty($featureDocument->photo_id)):?>
													<?php echo $this->htmlLink($featureDocument->getHref(), $this->itemPhoto($featureDocument, 'thumb.normal'), array('title' => $featureDocument->document_title) ) ?>
												<?php else: ?>
													<?php echo $this->htmlLink($featureDocument->getHref(), '<img src="'. Engine_Api::_()->document()->sslThumbnail($featureDocument->thumbnail) .'" width="120" height="120" />', array('title' => $featureDocument->document_title) ) ?>
												<?php endif; ?>

											</div>
											
											<div id="focus_tooltip_document_featured_<?php echo $featureDocument->document_id; ?>" style="display:none;">	
												<div class="document_tooltip">
													<div class="document_tooltip_content">
														<div class="tooltip_arrow">
															<img src='application/modules/Document/externals/images/tooltip_arrow.png' alt="" />
														</div>
														
														<?php if($featureDocument->sponsored == 1): ?>
															<span class="fright">
																<?php echo $this->htmlImage('application/modules/Seaocore/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
															</span>
														<?php endif;?>
														
														<div class="title">
															<?php
																$truncation = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.title.truncation', 0);
																$item_title = $featureDocument->document_title;
																if(empty($truncation)) {
																	$item_title = Engine_Api::_()->document()->truncateText($item_title, 33);
																}
															?>
															<?php echo $this->htmlLink($featureDocument->getHref(), $item_title, array('title' => $featureDocument->document_title)) ?> 
														</div>
														<div class="document_tooltip_details">
															<?php echo $this->translate('Created by %s about %s', $featureDocument->getOwner()->toString(), $this->timestamp($featureDocument->creation_date)) ?>,<br />
																<?php echo $this->translate(array('%s comment', '%s comments', $featureDocument->comment_count), $this->locale()->toNumber($featureDocument->comment_count)) ?>,
																<?php echo $this->translate(array('%s view', '%s views', $featureDocument->views), $this->locale()->toNumber($featureDocument->views)) ?>,
																<?php echo $this->translate(array('%s like', '%s likes', $featureDocument->like_count), $this->locale()->toNumber($featureDocument->like_count)) ?>
																<?php if(($featureDocument->rating > 0) && ($this->show_rate == 1)):?>
																	<?php 
																		$currentRatingValue = $featureDocument->rating;
																		$difference = $currentRatingValue- (int)$currentRatingValue;
																		if($difference < .5) {
																			$finalRatingValue = (int)$currentRatingValue;
																		}
																		else {
																			$finalRatingValue = (int)$currentRatingValue + .5;
																		}	
																	?>
																	<br />
																	<?php for($x = 1; $x <= $featureDocument->rating; $x++): ?>
																		<span class="rating_star_generic rating_star" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
																		</span>
																	<?php endfor; ?>
																	<?php if((round($featureDocument->rating) - $featureDocument->rating) > 0):?>
																		<span class="rating_star_generic rating_star_half" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
																		</span>
																	<?php endif; ?>
																	<br />
																<?php endif; ?>
																<p> 
																	<?php if($featureDocument->category_id): ?>
																		<?php $category = Engine_Api::_()->getDbtable('categories', 'document')->getCategory($featureDocument->category_id); ?>
																		<?php echo $this->translate('Category:');?> <a href='javascript:void(0);' onclick='javascript:categoryAction(<?php echo $featureDocument->category_id?>);'><?php echo $category->category_name ?></a>  
																	<?php endif; ?> 
																</p>
														</div>
													</div>
												</div>
											</div>
										</td>
									<?php endforeach; ?> 
								</tr>
							</table>
						</div>
					</td>
					<td>
						<a href='javascript:void(0);' onClick='moveRight();this.blur()'>
						<?php echo $this->htmlImage('application/modules/Document/externals/images/doc_car_right.png', '', array('align'=>'left', 'onMouseOver'=>'this.src="application/modules/Document/externals/images/doc_car_right_over.png";','onMouseOut'=>'this.src="application/modules/Document/externals/images/doc_car_right.png";', 'border'=>'0')) ?>
						</a>
					</td>
				</tr>
			</table>
		</div>
	</div>		
<?php endif; ?>

<script type="text/javascript">
  var current_id = 1;
  var myFx_docuement_featured;
  window.addEvent('domready', function() {
    myFx_docuement_featured = new Fx.Scroll('document_carousel');
    current_id = parseInt(<?php echo $current_index - 2 ; ?>);
    var position = $('thumb'+current_id).getPosition($('document_carousel'));
    myFx_docuement_featured.set(position.x, position.y);
  });

	function moveLeft() {
    if($('thumb'+(current_id-1))) {
      myFx_docuement_featured.toElement('thumb'+(current_id-1));
      myFx_docuement_featured.toLeft();
      current_id = parseInt(current_id-1);
    }
  }

  function moveRight() { 
    if($('thumb'+(current_id+1))) { 
      myFx_docuement_featured.toElement('thumb'+(current_id+1));
      myFx_docuement_featured.toRight();
      current_id = parseInt(current_id+1);
    }
  }
</script>	
