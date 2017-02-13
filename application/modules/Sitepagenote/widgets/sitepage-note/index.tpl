<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
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
  <form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitepagenote_browse', true) ?>' style='display: none;'>
    <input type="hidden" id="page" name="page"  value=""/>
  </form>

		<ul class="seaocore_browse_list">
			<?php foreach ($this->paginator as $sitepagenote): ?>
        <?php $this->sitepageSubject = Engine_Api::_()->getItem('sitepage_page', $sitepagenote->page_id);?>
				<li id="sitepagenote-item-<?php echo $sitepagenote->note_id ?>">
				<div class="seaocore_browse_list_photo">
				<?php if($sitepagenote->photo_id == 0):?>
				   <?php 
            if($this->sitepageSubject->photo_id == 0):?>
			  			<?php echo $this->htmlLink($sitepagenote->getHref(),$this->itemPhoto($sitepagenote, 'thumb.profile', $sitepagenote->getTitle())) ?>   
			  	<?php else:?>
			  	<?php echo $this->htmlLink($sitepagenote->getHref(),$this->itemPhoto($this->sitepageSubject, 'thumb.normal', $sitepagenote->getTitle())) ?>
			  <?php endif;?>
			  <?php else:?>
					<?php echo $this->htmlLink($sitepagenote->getHref(),$this->itemPhoto($sitepagenote, 'thumb.normal', $sitepagenote->getTitle())) ?>
			   <?php endif;?>	
        </div>			
			  <div class="seaocore_browse_list_info">
					<div class="seaocore_browse_list_info_title">
						<span>
							<?php if (($sitepagenote->price>0)): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
							<?php endif; ?>
              <?php if ($sitepagenote->featured == 1): ?>
							<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
						  <?php endif; ?>
						</span>
					  <h3><?php echo $this->htmlLink($sitepagenote->getHref(), $sitepagenote->title) ?></h3>
					</div>
          <div class="seaocore_browse_list_info_date">
							<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepagenote->page_id, $sitepagenote->owner_id, $sitepagenote->getSlug()),  $sitepagenote->page_title) ?>
					</div>
					<div class="seaocore_browse_list_info_date">
						<?php echo $this->translate('Posted by %s', $this->htmlLink($sitepagenote->getOwner(), $sitepagenote->getOwner()->getTitle())) ?>
					  <?php echo $this->timestamp($sitepagenote->creation_date) ?>
					  -
					  <?php echo $this->translate(array('%s view', '%s views', $sitepagenote->view_count ), $this->locale()->toNumber($sitepagenote->view_count )) ?>
						-
					  <?php echo $this->translate(array('%s comment', '%s comments', $sitepagenote->comment_count), $this->locale()->toNumber($sitepagenote->comment_count)) ?>
					  -
					  <?php echo $this->translate(array('%s like', '%s likes', $sitepagenote->like_count), $this->locale()->toNumber($sitepagenote->like_count )) ?>
					</div>
				  <?php if (!empty($sitepagenote->body)): ?>
					  <div class="seaocore_browse_list_info_blurb">
		          <?php $sitepagenote_body = strip_tags($sitepagenote->body);
										$sitepagenote_body = Engine_String::strlen($sitepagenote_body) > 300 ? Engine_String::substr($sitepagenote_body, 0, 300) . '..' : $sitepagenote_body;
							?>
					    <?php  echo $sitepagenote_body ?>
					  </div>
					<?php endif; ?>
		   	 </div>
		  	</li>
			<?php endforeach; ?>
		</ul>
		<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitepagenote"), array("orderby" => $this->orderby)); ?>

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