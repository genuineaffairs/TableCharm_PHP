<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
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
	<form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitepagepoll_browse', true) ?>' style='display: none;'>
	<input type="hidden" id="page" name="page"  value=""/>
	</form>

	<ul class="seaocore_browse_list">
		<?php foreach ($this->paginator as $sitepagepoll): ?>
			<li>
				<div class="seaocore_browse_list_photo"> 
					<?php $sitepagepoll_object = Engine_Api::_()->getItem('sitepage_page', $sitepagepoll->page_id);?>
					<?php echo $this->htmlLink(
					$sitepagepoll->getHref(),
					$this->itemPhoto($sitepagepoll->getOwner(), 'thumb.profile', $sitepagepoll->getOwner()->getTitle()),
					array('title' => $sitepagepoll->title)
					) ?>
				</div>
				<div class='seaocore_browse_list_info'>
					<div class='seaocore_browse_list_info_title'>
            <span>
							<?php if (($sitepagepoll->price>0)): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
							<?php endif; ?>
            </span>
						<h3><?php echo $this->htmlLink($sitepagepoll->getHref(), $sitepagepoll->getTitle(), array('title' => $sitepagepoll->getTitle())); ?> </h3>
					</div>
          <div class="seaocore_browse_list_info_date">
						<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepagepoll->page_id, $sitepagepoll->owner_id, $sitepagepoll->getSlug()),  $sitepagepoll->page_title) ?>
					</div>
					<div class="seaocore_browse_list_info_date">
						<?php echo $this->translate('Created by %s', $this->htmlLink($sitepagepoll->getOwner(), $sitepagepoll->getOwner()->getTitle())) ?>
						<?php echo $this->translate(array('%s vote', '%s votes', $sitepagepoll->vote_count), $this->locale()->toNumber($sitepagepoll->vote_count)) ?> 
						-
						<?php echo $this->translate(array('%s view', '%s views', $sitepagepoll->views), $this->locale()->toNumber($sitepagepoll->views)) ?>

						-
						<?php echo $this->translate(array('%s like', '%s likes', $sitepagepoll->like_count), $this->locale()->toNumber($sitepagepoll->like_count)) ?>

						-
						<?php echo $this->translate(array('%s comment', '%s comments', $sitepagepoll->comment_count), $this->locale()->toNumber($sitepagepoll->comment_count)) ?>
					</div>	

					<?php if (!empty($sitepagepoll->description)): ?>
						<div class="seaocore_browse_list_info_blurb"> 
							<?php $sitepagepoll_description = strip_tags($sitepagepoll->description);
							$sitepagepoll_description = Engine_String::strlen($sitepagepoll_description) > 270 ? Engine_String::substr($sitepagepoll_description, 0, 270) . '..' : $sitepagepoll_description;
							?>
							<?php  echo $sitepagepoll_description ?>
						</div>
					<?php endif; ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitepagepoll"), array("orderby" => $this->orderby)); ?>
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