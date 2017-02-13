<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<?php if($this->paginator->getTotalItemCount()):?>
  <form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitepagedocument_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="page" name="page"  value=""/>
  </form>
  
		<ul class="seaocore_browse_list">
			<?php foreach ($this->paginator as $sitepagedocument): ?>
			<li>
        <div class="seaocore_browse_list_photo"> 
					<?php if(!empty($sitepagedocument->thumbnail)): ?>

						<?php if($this->https):?>
							<?php $sitepagedocument->thumbnail = $this->baseUrl().'/'.$this->manifest_path."/ssl?url=".urlencode($sitepagedocument->thumbnail); ?>
						<?php endif; ?>

						<?php echo $this->htmlLink($sitepagedocument->getHref(), '<img src="'. $sitepagedocument->thumbnail .'" />', array('title' => $sitepagedocument->sitepagedocument_title) ) ?>
					<?php else: ?>
						<?php echo $this->htmlLink($sitepagedocument->getHref(), '<img src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/sitepagedocument_thumb.png" />', array('title' => $sitepagedocument->sitepagedocument_title) ) ?>
					<?php endif;?>
				</div>
				<div class='seaocore_browse_list_info'>
					<div class='seaocore_browse_list_info_title'>
						<span>
							<?php if($sitepagedocument->featured == 1): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/icons/sitepagedocument_goldmedal1.png', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
              <?php endif;?>
              <?php if (($sitepagedocument->price>0)): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
							<?php endif;?>
						</span>
						<span class="list_rating_star">
							<?php if(($sitepagedocument->rating > 0) && ($this->show_rate == 1)):?>

								<?php 
									$currentRatingValue = $sitepagedocument->rating;
									$difference = $currentRatingValue- (int)$currentRatingValue;
									if($difference < .5) {
										$finalRatingValue = (int)$currentRatingValue;
									}
									else {
										$finalRatingValue = (int)$currentRatingValue + .5;
									}	
								?>

								<?php for($x=1; $x<=$sitepagedocument->rating; $x++): ?><span class="rating_star_generic rating_star" title="<?php echo $finalRatingValue ?> <?php echo $this->translate('rating');?>"></span><?php endfor; ?><?php if((round($sitepagedocument->rating)-$sitepagedocument->rating)>0):?><span class="rating_star_generic rating_star_half" title="<?php echo $finalRatingValue.$this->translate("rating"); ?>"></span><?php endif; ?>
							<?php endif; ?>
						</span>
						<h3><?php echo $this->htmlLink($sitepagedocument->getHref(), $sitepagedocument->sitepagedocument_title, array('title' => $sitepagedocument->sitepagedocument_title)) ?></h3>
					</div>
          <div class="seaocore_browse_list_info_date">
							<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepagedocument->page_id, $sitepagedocument->owner_id, $sitepagedocument->getSlug()),  $sitepagedocument->page_title) ?>
					</div>
					<div class='seaocore_browse_list_info_date'>
						<?php echo $this->translate('Created %s by %s', $this->timestamp($sitepagedocument->creation_date), $sitepagedocument->getOwner()->toString()) ?>,
						<?php echo $this->translate(array('%s comment', '%s comments', $sitepagedocument->comment_count), $this->locale()->toNumber($sitepagedocument->comment_count)) ?>, 
						<?php echo $this->translate(array('%s view', '%s views', $sitepagedocument->views), $this->locale()->toNumber($sitepagedocument->views)) ?>,
						<?php echo $this->translate(array('%s like', '%s likes', $sitepagedocument->like_count), $this->locale()->toNumber($sitepagedocument->like_count)) ?>
					</div>
					<div class='seaocore_browse_list_info_blurb'>
							<?php echo $sitepagedocument->truncateText($sitepagedocument->sitepagedocument_description, 300); ?>
				 </div>
				</div>
			</li>
  <?php endforeach; ?>
</ul>
<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitepagedocument"), array("orderby" => $this->orderby)); ?>
	
<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('There are no search results to display.');?>
		</span>
	</div>
<?php endif;?> 

<script type="text/javascript">
  var pageAction = function(page){
     var form;
     if($('filter_form')) {
       form=document.getElementById('filter_form');
      }else if($('filter_form_page')){
				form=$('filter_form_page');
			}
    form.elements['page'].value = page;
    
		form.submit();
  } 
</script>