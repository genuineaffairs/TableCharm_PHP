<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
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
  <form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitepageevent_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="page" name="page"  value=""/>
  </form>
	<h3 class="sitepage_mypage_head"><?php echo $this->translate('Events');?></h3>
	<ul class="seaocore_browse_list">
		<?php foreach ($this->paginator as $sitepageevent): ?>
				<li id="sitepageevent-item-<?php echo $sitepageevent->event_id ?>">
				<div class="seaocore_browse_list_photo"> 
					<?php echo  $this->htmlLink(
						$sitepageevent->getHref(),
						$this->itemPhoto($sitepageevent, 'thumb.normal', $sitepageevent->getTitle())
					) ?>
				</div>
				<div class="seaocore_browse_list_info">
					<div class="seaocore_browse_list_info_title">
            <span>
							<?php if (($sitepageevent->price>0)): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
							<?php endif; ?>
              <?php if ($sitepageevent->featured == 1): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
						  <?php endif; ?>
            </span>
						<h3><?php echo $this->htmlLink($sitepageevent->getHref(), $sitepageevent->title) ?></h3>
					</div>
          <div class="seaocore_browse_list_info_date">
							<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepageevent->page_id, $sitepageevent->owner_id, $sitepageevent->getSlug()),  $sitepageevent->page_title) ?>
					</div>
					<div class="seaocore_browse_list_info_date">
					<?php echo $this->translate('Led by %s', $this->htmlLink($sitepageevent->getOwner(), $sitepageevent->getOwner()->getTitle())) ?>
						<?php echo $this->timestamp($sitepageevent->creation_date) ?>	            
						-	            
						<?php echo $this->translate(array('%s view', '%s views', $sitepageevent->view_count ), $this->locale()->toNumber($sitepageevent->view_count )) ?>							
						-
						<?php echo $this->translate(array('%s guest', '%s guests', $sitepageevent->member_count ), $this->locale()->toNumber($sitepageevent->member_count )) ?>	            
					</div>
					<?php if (!empty($sitepageevent->description)): ?>
						<div class="seaocore_browse_list_info_blurb">
							<?php echo $this->viewMore($sitepageevent->description); ?><br />
						</div>
					<?php endif; ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitepageevent"), array("orderby" => $this->orderby)); ?>
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