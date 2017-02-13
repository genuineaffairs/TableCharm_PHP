<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';?>
<?php  
	$sitepageOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer');
    if ($sitepageOfferEnabled) { 
    $this->headLink()
	  ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepageoffer/externals/styles/style_sitepageoffer.css');
    }
 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<?php $postedBy = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1);
?>

<script type="text/javascript" >

function owner(thisobj) {
	var Obj_Url = thisobj.href  ;

	Smoothbox.open(Obj_Url);
}
</script>

<script>
  var sitepages_likes = function(resource_id, resource_type) {
		var content_type = 'sitepage';
    //var error_msg = '<?php //echo $this->result['0']['like_id']; ?>';

		// SENDING REQUEST TO AJAX
		var request = createLikepage(resource_id, resource_type,content_type);

		// RESPONCE FROM AJAX
		request.addEvent('complete', function(responseJSON) {
			if (responseJSON.error_mess == 0) {
				$(resource_id).style.display = 'block';
				if(responseJSON.like_id )
				{
					$('backgroundcolor_'+ resource_id).className ="sitepage_browse_thumb sitepage_browse_liked";
					$('sitepage_like_'+ resource_id).value = responseJSON.like_id;
					$('sitepage_most_likes_'+ resource_id).style.display = 'none';
					$('sitepage_unlikes_'+ resource_id).style.display = 'block';
					$('show_like_button_child_'+ resource_id).style.display='none';
				}
				else
				{  $('backgroundcolor_'+ resource_id).className ="sitepage_browse_thumb";
					$('sitepage_like_'+ resource_id).value = 0;
					$('sitepage_most_likes_'+ resource_id).style.display = 'block';
					$('sitepage_unlikes_'+ resource_id).style.display = 'none';
					$('show_like_button_child_'+ resource_id).style.display='none';
				}
		
			}
			else {
				en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
				return;
			}
		});
	}
	// FUNCTION FOR CREATING A FEEDBACK
	var createLikepage = function( resource_id, resource_type, content_type )
	{
		if($('sitepage_most_likes_'+ resource_id).style.display == 'block')
			$('sitepage_most_likes_'+ resource_id).style.display='none';
    

		if($('sitepage_unlikes_'+ resource_id).style.display == 'block')
			$('sitepage_unlikes_'+ resource_id).style.display='none';
			$(resource_id).style.display='none';
			$('show_like_button_child_'+ resource_id).style.display='block';

		if (content_type == 'sitepage') {
			var like_id = $(content_type + '_like_'+ resource_id).value
		}
		var url = '<?php echo $this->url(array('action' => 'global-likes' ), 'sitepage_like', true);?>';
		var request = new Request.JSON({
			
			url : url,
			data : {
				format : 'json',
				'resource_id' : resource_id,
				'resource_type' : resource_type,
				'like_id' : like_id
			}
		});
		request.send();
		return request;
	}
</script>
<?php
  $this->headLink()
  	->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/styles/sitepage-tooltip.css');
	$viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
	$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	$MODULE_NAME = 'sitepage';
	$RESOURCE_TYPE = 'sitepage_page';
?>
<?php $enableBouce=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.sponsored', 1); 
$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');?>
<script type="text/javascript">
  var pageAction = function(page){
       
     var form;
     if($('filter_form')) {
       form=document.getElementById('filter_form');
      }else if($('filter_form_page')){
				form=$('filter_form_page');
			}
    form.elements['page'].value = page;
    <?php if($this->tag):?>
		form.elements['tag'].value = '<?php echo $this->tag?>'; 
    <?php endif;?>
    form.elements['category'].value = '<?php echo $this->category?>'; 
    form.elements['categoryname'].value = '<?php echo $this->categoryname?>'; 
    form.elements['subsubcategory'].value = '<?php echo $this->subsubcategory?>'; 
    form.elements['subcategory'].value = '<?php echo $this->subcategory?>'; 
    form.elements['subcategoryname'].value = '<?php echo $this->subcategoryname?>'; 
    form.elements['subsubcategoryname'].value = '<?php echo $this->subsubcategoryname?>';
    <?php if($this->sitepage_location): ?>
    form.elements['sitepage_location'].value = '<?php echo $this->sitepage_location?>';
    <?php  endif; ?>
		form.submit();
  } 
</script>

<?php if ($this->paginator->count() > 0): ?>
 <form id='filter_form_page' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'index'), 'sitepage_general', true) ?>' style='display: none;'>
    <input type="hidden" id="page" name="page"  value=""/>
		<input type="hidden" id="tag" name="tag"  value=""/>
    <input type="hidden" id="sitepage_location" name="sitepage_location"  value=""/>
		<input type="hidden" id="category" name="category"  value=""/>
		<input type="hidden" id="categoryname" name="categoryname"  value=""/>
		<input type="hidden" id="subsubcategory" name="subsubcategory" value=""/>
		<input type="hidden" id="subcategory" name="subcategory"  value=""/>
		<input type="hidden" id="subcategoryname" name="subcategoryname"  value=""/>
		<input type="hidden" id="subsubcategoryname" name="subsubcategoryname" value=""/>
  </form>
<?php  $latitude=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.latitude', 0); ?>
<?php  $longitude=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.longitude', 0); ?>
<?php  $defaultZoom=Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.map.zoom', 1); ?>
 <?php if (($this->list_view && $this->grid_view) || ($this->map_view && $this->grid_view) || ($this->list_view && $this->map_view)): ?>

	<div class="sitepage_view_select">
		<div class="fleft"> 
			<?php echo $this->translate(array('%s page found.', '%s pages found.', $this->paginator->getTotalItemCount()),$this->locale()->toNumber($this->paginator->getTotalItemCount())); ?>
		</div>
		<?php  if( $this->enableLocation  && $this->map_view): ?>  
			<span class="seaocore_tab_select_wrapper fright">
				<div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Map View"); ?></div>
        <span class="seaocore_tab_icon tab_icon_map_view" onclick="switchview(2)"></span>
			</span>
		<?php endif;?>
    <?php  if( $this->grid_view): ?>
		<span class="seaocore_tab_select_wrapper fright">
			<div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View"); ?></div>
			<span class="seaocore_tab_icon tab_icon_grid_view" onclick="switchview(1)"></span>
		</span>
    <?php endif;?>
     <?php  if( $this->list_view): ?>
		<span class="seaocore_tab_select_wrapper fright">
			<div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View"); ?></div>
			<span class="seaocore_tab_icon tab_icon_list_view" onclick="switchview(0)"></span>
		</span>
    <?php endif; ?>
	</div>
 <?php endif; ?>
  <?php if( $this->list_view): ?>
<div id="grid_view" style="display: none;">
		<ul class="seaocore_browse_list">
			<?php foreach ($this->paginator as $sitepage): ?>
				<li <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)):?><?php if($sitepage->featured):?> class="lists_highlight"<?php endif;?><?php endif;?>>
           <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)):?>
             <?php if($sitepage->featured):?>
							 <i title="<?php echo $this->translate('Featured')?>" class="seaocore_list_featured_label"></i>
            <?php endif;?>
          <?php endif;?>
					<div class='seaocore_browse_list_photo'>
						<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id ,$sitepage->getSlug()), $this->itemPhoto($sitepage, 'thumb.normal', '', array('align'=>'left'))) ?>
					  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)):?>
            <?php if (!empty($sitepage->sponsored)): ?>
              <?php $sponsored = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.image', 1);
              if (!empty($sponsored)) { ?>
                <div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.color', '#fc0505'); ?>;'>
                  <?php echo $this->translate('SPONSORED'); ?>                 
                </div>
              <?php } ?>
            <?php endif; ?>
          <?php endif; ?>
          </div>
					<div class='seaocore_browse_list_info'>
						<div class='seaocore_browse_list_info_title'>
							<span>
								<?php if( $sitepage->closed ): ?>
									<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>
									
								<?php endif;?> 
                
                <?php if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.fs.markers', 1)) :?>
                  <?php if ($sitepage->sponsored == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                  <?php endif; ?>
                  <?php if ($sitepage->featured == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                  <?php endif; ?>
                <?php endif; ?>
							</span>
							<?php if (is_array($this->statistics) && in_array('reviewCount', $this->statistics) && $this->ratngShow): ?>
								<?php if (($sitepage->rating > 0)): ?>
									<?php 
										$currentRatingValue = $sitepage->rating;
										$difference = $currentRatingValue- (int)$currentRatingValue;
										if($difference < .5) {
											$finalRatingValue = (int)$currentRatingValue;
										}
										else {
											$finalRatingValue = (int)$currentRatingValue + .5;
										}	
									?>
										<span class="list_rating_star" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
										<?php for ($x = 1; $x <= $sitepage->rating; $x++): ?>
										<span class="rating_star_generic rating_star" ></span>
										<?php endfor; ?>
										<?php if ((round($sitepage->rating) - $sitepage->rating) > 0): ?>
											<span class="rating_star_generic rating_star_half" ></span>
										<?php endif; ?>
									</span>		
								<?php endif; ?>
							<?php endif; ?>
							<h3><?php if(!empty($this->sitepage_generic)){ echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id,$sitepage->getSlug()), $sitepage->getTitle()); }else { return; } ?></h3>
						</div>
            <div class='seaocore_browse_list_info_date' style='color:chocolate'>
              <?php echo ($sitepage->isPublic() ? 'Public' : 'Private'); ?>
            </div>
						<div class='seaocore_browse_list_info_date'>
                <?php echo $this->timestamp(strtotime($sitepage->creation_date)) ?>
                <?php if($postedBy && !Engine_Api::_()->sitepage()->isSiteAdmin($sitepage->getOwner())):?>
                 - <?php echo $this->translate('posted by'); ?>
                 <?php echo $this->htmlLink($sitepage->getOwner()->getHref(), $sitepage->getOwner()->getTitle()) ?><?php if (!empty($this->statistics)) : ?>,<?php endif; ?>
                <?php endif;?>
                
              <?php if(is_array($this->statistics) && !empty($this->statistics)) : ?>
               <?php 
                $statistics = '';
                
                if(in_array('likeCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s like', '%s likes', $sitepage->like_count), $this->locale()->toNumber($sitepage->like_count)).', ';
                }
                if(in_array('followCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s follower', '%s followers', $sitepage->follow_count), $this->locale()->toNumber($sitepage->follow_count)).', ';
                }

                if(in_array('memberCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1);
									if ($sitepage->member_title && $memberTitle) {
										if ($sitepage->member_count == 1) :
											if (!empty($this->membercalled) && !empty($memberTitle)) :
												echo $sitepage->member_count . ' ' .  $sitepage->member_title.', ';
											else : 
												echo $sitepage->member_count . ' member'.', ';
											endif;
										else:  
											echo $sitepage->member_count . ' ' .  $sitepage->member_title.', ';
										endif;
									} else {
										$statistics .= $this->translate(array('%s member', '%s members', $sitepage->member_count), $this->locale()->toNumber($sitepage->member_count)).', ';
									}
                }
                
                if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') && in_array('reviewCount', $this->statistics) && !empty($this->ratngShow)) {
                  $statistics .= $this->translate(array('%s review', '%s reviews', $sitepage->review_count), $this->locale()->toNumber($sitepage->review_count)).', ';
                }
                
                if(in_array('commentCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s comment', '%s comments', $sitepage->comment_count), $this->locale()->toNumber($sitepage->comment_count)).', ';
                }
                
                if(in_array('viewCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s view', '%s views', $sitepage->view_count), $this->locale()->toNumber($sitepage->view_count)).', ';
                }
                $statistics = trim($statistics);
                $statistics = rtrim($statistics, ',');
              ?>
              <?php echo $statistics; ?>
							<?php endif; ?>
							
							
						</div>
            <?php if($this->showContactDetails): ?>
              <?php
                $user = Engine_Api::_()->user()->getUser($sitepage->owner_id);
                $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $user, 'contact_detail');
                $availableLabels = array('phone' => 'Phone','website' => 'Website','email' => 'Email');		
                $options_create = array_intersect_key($availableLabels, array_flip($view_options));
              ?>
               <?php $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'contact');
              if(!empty($isManageAdmin)): ?>
              <div class="seaocore_browse_list_info_date">
                <?php if( isset($options_create['phone']) && $options_create['phone'] == 'Phone'):?><?php if($sitepage->phone):?>
                  <?php echo $this->translate('Phone: '); ?><?php echo $sitepage->phone ?><?php endif; ?><?php endif; ?><?php if( isset($options_create['email']) && $options_create['email'] == 'Email'):?><?php if($sitepage->email):?><?php if( !empty($sitepage->phone) && in_array("Phone",$options_create )):?>, <?php endif; ?><?php echo $this->translate('Email: '); ?><a href='mailto:<?php echo $sitepage->email ?>'><?php echo $sitepage->email ?></a><?php endif; ?><?php endif; ?><?php if( isset($options_create['website']) && $options_create['website'] == 'Website'):?><?php if($sitepage->website):?><?php if( ($sitepage->email && in_array("Email",$options_create )) || !empty($sitepage->phone) && in_array("Phone",$options_create ) ):?>,&nbsp;<?php endif; ?><?php echo $this->translate('Website: '); ?><?php if(strstr($sitepage->website, 'http://') || strstr($sitepage->website, 'https://')):?><a href='<?php echo $sitepage->website ?>' target="_blank"><?php echo $sitepage->website ?></a><?php else:?><a href='http://<?php echo $sitepage->website ?>' target="_blank"><?php echo $sitepage->website ?></a><?php endif;?><?php endif; ?><?php endif; ?>
              </div>
              <?php endif; ?>
            <?php endif; ?>
						<?php if((!empty($sitepage->location) && $this->enableLocation) || (!empty($this->showprice) && !empty($sitepage->price) && $this->enablePrice) ): ?>
							<div class="seaocore_browse_list_info_date"><?php if(!empty($this->showprice) && !empty($sitepage->price) && $this->enablePrice): ?><?php echo $this->translate("Price: "); echo $this->locale()->toCurrency($sitepage->price, $currency); ?><?php endif; ?><?php if((!empty($sitepage->location) && $this->enableLocation) && (!empty($this->showprice) && !empty($sitepage->price ) && $this->enablePrice)): ?><?php //echo $this->translate(", "); ?>
              <?php endif; ?>
                <?php if(!empty($sitepage->location) && $this->enableLocation): ?>
									<?php  $locationId = Engine_Api::_()->getDbTable('locations', 'sitepage')->getLocationId($sitepage->page_id, $sitepage->location);
									echo $this->translate("Location: "); echo $this->translate($sitepage->location); ?>
									&nbsp;-
									<b><?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', 'id' => $sitepage->page_id, 'resouce_type' => 'sitepage_page', 'location_id' => $locationId, 'flag' => 'map'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')); ?></b>
                <?php endif; ?>
							</div>
							<?php if (!empty($sitepage->distance) && isset($sitepage->distance)): ?>
								<div class="seaocore_browse_list_info_stat seaocore_txt_light">
									<?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer')): ?>
										<?php echo $this->translate("approximately <b>%s</b> miles", round($sitepage->distance, 2)); ?>
									<?php else: ?>
										<?php $distance = (1 / 0.621371192) * $sitepage->distance; echo $this->translate("approximately <b>%s</b> kilometers", round($distance, 2)); ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						<?php endif; ?>
						
						<?php //if(!empty ($profileTypePrivacy)): ?>
							<?php if($this->showProfileField) : ?>
								<?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitepage/View/Helper', 'Sitepage_View_Helper');
								$fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitepage);
								$params = array('custom_field_heading' => $this->custom_field_heading, 'custom_field_title' => $this->custom_field_title, 'customFieldCount' => $this->customFieldCount, 'widgetName' => 'browse');
								$str =  $this->profileFieldValueLoop($sitepage, $fieldStructure, $params);
								?>
								<?php if($str): ?>
									<?php echo $this->profileFieldValueLoop($sitepage, $fieldStructure, $params) ?>
								<?php endif; ?>
							<?php endif; ?>
						<?php //endif; ?>
						
						<div class='seaocore_browse_list_info_blurb'>
              <?php echo $this->viewMore($sitepage->body,200,5000) ?>
						</div>

						<?php if(!empty($this->sitepageOfferEnabled) && !empty($sitepage->offer)): ?>
							<?php echo $sitepage->getOffer(); ?>
            <?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
  <?php endif; ?>
  <?php  if( $this->grid_view):?>
	<div id="image_view" style="display: none;">
  <div class="sitepage_img_view">
			<?php $counter=1; foreach ($this->paginator as $sitepage): ?>
			
		<?php
		  $likePage=false;
		  if(!empty($viewer_id)):
		  $likePage=Engine_Api::_()->sitepage()->hasPageLike($sitepage->page_id,$viewer_id);
		  endif;
		?>

     
      <div class="sitepage_browse_thumb <?php if($likePage): ?> sitepage_browse_liked <?php endif; ?>" id = "backgroundcolor_<?php echo $sitepage->page_id; ?>" style="width:<?php echo $this->columnWidth; ?>px;height:<?php echo $this->columnHeight; ?>px;">
      	<div class="sitepage_browse_thumb_list" <?php if(!empty($viewer_id) && !empty($this->showlikebutton)) : ?> onmouseOver="$('like_<?php echo $sitepage->getIdentity(); ?>').style.display='block'; if($('<?php echo $sitepage->getIdentity(); ?>').style.display=='none')$('<?php echo $sitepage->getIdentity(); ?>').style.display='block';"  onmouseout="$('like_<?php echo $sitepage->getIdentity(); ?>').style.display='none'; $('<?php echo $sitepage->getIdentity(); ?>').style.display='none';" <?php endif; ?> >
      	
			<?php if (!empty($this->showlikebutton)) :?>
				<a href="javascript:void(0);">
			<?php else :?>
				<a href="<?php echo Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id,$sitepage->getSlug()) ?>">
			<?php endif; ?>
					<?php $url= $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/nophoto_page_thumb_profile.png'; $temp_url=$sitepage->getPhotoUrl('thumb.profile'); if(!empty($temp_url)): $url=$sitepage->getPhotoUrl('thumb.profile'); endif;?>
					<span style="background-image: url(<?php echo $url; ?>);"> </span>
					<?php if (empty($this->showlikebutton)) :?>
						<div class="sitepage_browse_title">
							<p><?php echo $sitepage->getTitle() ?></p>
						</div>
					<?php endif; ?>
				</a>
	
					<?php if(!empty($viewer_id)) : ?>
            <div id="like_<?php echo $sitepage->getIdentity() ?>" style="display:none;">
							<?php
							$RESOURCE_ID = $sitepage->getIdentity(); ?>
							<div class="" style="display:none;" id="<?php echo $RESOURCE_ID; ?>">
								<?php
								// Check that for this 'resurce type' & 'resource id' user liked or not.
								$check_availability = Engine_Api::_()->$MODULE_NAME()->checkAvailability( $RESOURCE_TYPE, $RESOURCE_ID );
								if( !empty($check_availability) )
								{
									$label = 'Unlike this';
									$unlike_show = "display:block;";
									$like_show = "display:none;";
									$like_id = $check_availability[0]['like_id'];
								}
								else
								{
									$label = 'Like this';
									$unlike_show = "display:none;";
									$like_show = "display:block;";
									$like_id = 0;
								}
								//}
								?>
								<div class="sitepage_browse_thumb_hover_color"></div>

								<div class="seaocore_like_button sitepage_browse_thumb_hover_unlike_button" id="sitepage_unlikes_<?php echo $RESOURCE_ID;?>" style ='<?php echo $unlike_show;?>' >
									<a href = "javascript:void(0);" onclick = "sitepages_likes('<?php echo $RESOURCE_ID; ?>', 'sitepage_page');">
									<i class="seaocore_like_thumbdown_icon"></i>
									<span><?php echo $this->translate('Unlike') ?></span>
									</a>
								</div>

								<div class="seaocore_like_button sitepage_browse_thumb_hover_like_button" id="sitepage_most_likes_<?php echo $RESOURCE_ID;?>" style ='<?php echo $like_show;?>'>
									<a href = "javascript:void(0);" onclick = "sitepages_likes('<?php echo $RESOURCE_ID; ?>', 'sitepage_page');">
										<i class="seaocore_like_thumbup_icon"></i>
										<span><?php echo $this->translate('Like') ?></span>
									</a>
								</div>

								<input type ="hidden" id = "sitepage_like_<?php echo $RESOURCE_ID;?>" value = '<?php echo $like_id; ?>' />
								
							</div>
             </div>
							<div id = "show_like_button_child_<?php echo $RESOURCE_ID;?>" style="display:none;" >
								<div class="sitepage_browse_thumb_hover_color"></div>
								<div class="sitepage_browse_thumb_hover_loader">
									<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loader.gif" class="mtop5" />
								</div>	
							</div>
					<?php endif; ?>
          <?php if (!empty($this->showfeaturedLable) && $sitepage->featured == 1): ?>
          	<span class="seaocore_list_featured_label" title="<?php echo $this->translate('Featured')?>"></span>
          <?php endif; ?>
					
					<?php if (!empty($this->showlikebutton)):?>
						<div class="sitepage_browse_title">
							<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id,$sitepage->getSlug()), Engine_Api::_()->sitepage()->truncation($sitepage->getTitle(),$this->turncation), array('title' => $sitepage->getTitle())); ?>
						</div>
					<?php endif; ?>
				</div>
				<?php if (!empty($this->showsponsoredLable) && $sitepage->sponsored == 1): ?>
					<div class="seaocore_list_sponsored_label" style='background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponsored.color', '#fc0505'); ?>;'>
						<?php echo $this->translate('SPONSORED'); ?>     				
					</div>
				<?php endif; ?>
				<?php if(empty($this->sitepage_generic)){exit();} ?>

       <div class='sitepage_browse_thumb_info'>
       <?php if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled('sitepagemember') && is_array($this->statistics) && !empty($this->statistics) && in_array('memberCount', $this->statistics)) :?>
					<?php $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1);
					if ($sitepage->member_title && $memberTitle) : ?>
						<div class="member_count">
							<?php if ($sitepage->member_count == 1) : ?>
								<?php if (!empty($this->membercalled) && !empty($memberTitle)) : ?>
									<?php echo $sitepage->member_count . ' ' .  ucfirst($sitepage->member_title); ?>
								<?php else : ?>
									<?php echo $sitepage->member_count . ' ' . ucfirst('member'); ?> 
								<?php endif; ?>
							<?php  else: ?>
								<?php echo $sitepage->member_count . ' ' .  ucfirst($sitepage->member_title); ?>
							<?php endif; ?>
						</div>
					<?php else : ?>
						<div class="member_count">
							<?php echo $this->translate(array('%s '. ucfirst('member'), '%s '. ucfirst('members'), $sitepage->member_count), $this->locale()->toNumber($sitepage->member_count)) ?>
						</div>	
					<?php endif; ?>
					<?php endif; ?>
					<div class='sitepage_browse_thumb_stats seaocore_txt_light'>
						  <?php if(is_array($this->statistics) && !empty($this->statistics)) : ?>
               <?php 
                $statistics = '';
                
                if(in_array('likeCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s like', '%s likes', $sitepage->like_count), $this->locale()->toNumber($sitepage->like_count)).', ';
                }
                if(in_array('followCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s follower', '%s followers', $sitepage->follow_count), $this->locale()->toNumber($sitepage->follow_count)).', ';
                }

                if(in_array('commentCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s comment', '%s comments', $sitepage->comment_count), $this->locale()->toNumber($sitepage->comment_count)).', ';
                }
                
                if(in_array('viewCount', $this->statistics)) {
                  $statistics .= $this->translate(array('%s view', '%s views', $sitepage->view_count), $this->locale()->toNumber($sitepage->view_count)).', ';
                }
                $statistics = trim($statistics);
                $statistics = rtrim($statistics, ',');
              ?>
              <?php echo $statistics; ?>
							<?php endif; ?>
					</div>
				  <?php if (is_array($this->statistics) && in_array('reviewCount', $this->statistics) && $this->ratngShow): ?>
				  <div class='sitepage_browse_thumb_stats seaocore_txt_light'>
				  <?php if ($sitepage->review_count > 0) :?>
            <?php echo $this->translate(array('%s review', '%s reviews', $sitepage->review_count), $this->locale()->toNumber($sitepage->review_count)); ?>&nbsp;&nbsp;&nbsp;&nbsp;
          <?php endif; ?>
						<?php if (($sitepage->rating > 0)): ?>
							<?php 
								$currentRatingValue = $sitepage->rating;
								$difference = $currentRatingValue- (int)$currentRatingValue;
								if($difference < .5) {
									$finalRatingValue = (int)$currentRatingValue;
								}
								else {
									$finalRatingValue = (int)$currentRatingValue + .5;
								}	
							?>
								<span class="list_rating_star" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
								<?php for ($x = 1; $x <= $sitepage->rating; $x++): ?>
								<span class="rating_star_generic rating_star" ></span>
								<?php endfor; ?>
								<?php if ((round($sitepage->rating) - $sitepage->rating) > 0): ?>
									<span class="rating_star_generic rating_star_half" ></span>
								<?php endif; ?>
							</span>		
						<?php endif; ?>
					</div>
					<?php endif; ?>

					<div class='sitepage_browse_thumb_stats seaocore_txt_light'>
						<?php if(!empty($this->showpostedBy) && $postedBy && !Engine_Api::_()->user()->isSiteAdmin($sitepage->getOwner())):?>
						<?php echo $this->translate('posted by'); ?>
						<?php echo $this->htmlLink($sitepage->getOwner()->getHref(), $sitepage->getOwner()->getTitle()) ?>
						<?php endif; ?>
					</div>
					<?php if (!empty($this->showdate)) :?>
					<div class='sitepage_browse_thumb_stats seaocore_txt_light'>
						<?php echo $this->timestamp(strtotime($sitepage->creation_date)) ?> 
					</div>
					<?php endif; ?>
					<?php if(!empty($this->showprice) && !empty($sitepage->price) && $this->enablePrice): ?>
						<div class='sitepage_browse_thumb_stats seaocore_txt_light'>
							<?php  echo $this->translate("Price: "); echo $this->locale()->toCurrency($sitepage->price, $currency);?>
						</div>
					<?php  endif;?>
					<?php
						if(!empty($sitepage->location) && $this->enableLocation && !empty($this->showlocation)):
							echo "<div class='seaocore_browse_list_info_date'>";
							echo $this->translate("Location: "); echo $this->translate($sitepage->location);
							$location_id = Engine_Api::_()->getDbTable('locations', 'sitepage')->getLocationId($sitepage->page_id, $sitepage->location); ?><?php if (!empty($this->showgetdirection)) :?>&nbsp; - <b> <?php echo  $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $sitepage->page_id, 'resouce_type' => 'sitepage_page', 'location_id' => $location_id, 'flag' => 'map'), $this->translate("Get Directions"), array('onclick' => 'owner(this);return false')) ; ?> </b><?php endif; ?>
							<?php echo "</div>"; ?>
						  <?php if (!empty($sitepage->distance) && isset($sitepage->distance)): ?>
								<div class="seaocore_browse_list_info_stat seaocore_txt_light">
									<?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer')): ?>
										<?php echo $this->translate("approximately <b>%s</b> miles", round($sitepage->distance, 2)); ?>
									<?php else: ?>
										<?php $distance = (1 / 0.621371192) * $sitepage->distance; echo $this->translate("approximately <b>%s</b> kilometers", round($distance, 2)); ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						<?php endif; ?>
						<?php //if(!empty ($profileTypePrivacy)): ?>
							<?php if($this->showProfileField) : ?>
								<?php $this->addHelperPath(APPLICATION_PATH . '/application/modules/Sitepage/View/Helper', 'Sitepage_View_Helper');
								$fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitepage);
								$params = array('custom_field_heading' => $this->custom_field_heading, 'custom_field_title' => $this->custom_field_title, 'customFieldCount' => $this->customFieldCount, 'widgetName' => 'browse');
								$str =  $this->profileFieldValueLoop($sitepage, $fieldStructure, $params);
								?>
								<?php if($str): ?>
									<?php echo $this->profileFieldValueLoop($sitepage, $fieldStructure, $params) ?>
								<?php endif; ?>
							<?php endif; ?>
						<?php //endif; ?>
        </div>
      </div>
		<?php endforeach; ?>
  </div>
</div>
<?php endif; ?>
<div id="map_canvas_view" style="display: none;">
	<div class="seaocore_map clr" style="overflow:hidden;">
	  <div id="sitepage_browse_map_canvas"> </div>
	  <?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
		<?php if (!empty($siteTitle)) : ?>
		<div class="seaocore_map_info"><?php echo "Locations on "; ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
		<?php endif; ?>
	</div>	
  <?php if( $this->enableLocation && $this->flageSponsored && $this->map_view && $enableBouce): ?>
  	<a href="javascript:void(0);" onclick="toggleBounce()" class="stop_bounce_link"> <?php echo $this->translate('Stop Bounce'); ?></a>
  <?php endif;?>
</div>

<div class="clr"></div>
<?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitepage"), array("orderby" => $this->orderby, "query" => $this->formValues)); ?>

<?php elseif ($this->search):  ?>
  <div class="tip">
     <?php  if (Engine_Api::_()->sitepage()->hasPackageEnable()):
      $createUrl=$this->url(array('action'=>'index'), 'sitepage_packages');
       else:
       $createUrl=$this->url(array('action'=>'create'), 'sitepage_general');
     endif; ?>
		<span> <?php echo $this->translate('Nobody has created a page with that criteria. Be the first to %1$screate%2$s one!', '<a href="' . $createUrl . '">', '</a>'); ?>
    </span> 
	</div>
  <?php else: ?>
	<?php if(empty($this->sitepage_generic)){exit();} ?>
  <div class="tip"> <span> <?php echo $this->translate('No Pages have been created yet.'); ?>
    <?php if ($this->can_create): ?>
      <?php  if (Engine_Api::_()->sitepage()->hasPackageEnable()):
      $createUrl=$this->url(array('action'=>'index'), 'sitepage_packages');
       else:
       $createUrl=$this->url(array('action'=>'create'), 'sitepage_general');
     endif; ?>
    <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $createUrl. '">', '</a>'); ?>
    <?php endif; ?>
    </span>
	</div>
  <?php endif; ?>

<style type="text/css">
  #sitepage_browse_map_canvas {
    width: 100% !important;
    height: 400px;
    float: left;
  }
  #sitepage_browse_map_canvas > div{
    height: 300px;
  }
  #infoPanel {
    float: left;
    margin-left: 10px;
  }
  #infoPanel div {
    margin-bottom: 5px;
  }
</style>

<script type="text/javascript" >
  function switchview(flage){
    if(flage==2){
      if($('map_canvas_view')){
      $('map_canvas_view').style.display='block';
      <?php if( $this->enableLocation && $this->map_view && $this->paginator->count() > 0): ?>
      google.maps.event.trigger(map, 'resize');
      map.setZoom(<?php echo $defaultZoom; ?>);
      map.setCenter(new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude; ?>));
      <?php endif; ?>
      if($('grid_view'))
      $('grid_view').style.display='none';
      if($('image_view'))
      $('image_view').style.display='none';
    }
    }else if(flage==1){
      if($('image_view')){
      if($('map_canvas_view'))
      $('map_canvas_view').style.display='none';
      if($('grid_view'))
      $('grid_view').style.display='none';
      $('image_view').style.display='block';
      }
    }else{
      if($('grid_view')){
      if($('map_canvas_view'))
      $('map_canvas_view').style.display='none';
      $('grid_view').style.display='block';
      if($('image_view'))
      $('image_view').style.display='none';
      }
    }
  }

  /* moo style */
  en4.core.runonce.add(function() {
    //opacity / display fix
    $$('.sitepage_tooltip').setStyles({
      opacity: 0,
      display: 'block'
    });
    //put the effect in place
    $$('.jq-sitepage_tooltip li').each(function(el,i) {
      el.addEvents({
        'mouseenter': function() {
          el.getElement('div').fade('in');
        },
        'mouseleave': function() {
          el.getElement('div').fade('out');
        }
      });
    });
      <?php if($this->paginator->count()>0):?>
    <?php if( $this->enableLocation && $this->map_view): ?>
    initialize();
    <?php endif; ?>  
    //  $('grid_view').style.display='none';  
      switchview(<?php echo $this->defaultView ?>);
    <?php endif;?>
  });
</script>

<?php if( $this->enableLocation && $this->map_view && $this->paginator->count() > 0): ?>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>

<script type="text/javascript">
  //<![CDATA[
  // this variable will collect the html which will eventually be placed in the side_bar
  var side_bar_html = "";

  // arrays to hold copies of the markers and html used by the side_bar
  // because the function closure trick doesnt work there
  var gmarkers = [];

  // global "map" variable
  var map = null;
  // A function to create the marker and set up the event window function
  function createMarker(latlng, name, html,title_page) { 
    var contentString = html;
    if(name ==0){
      var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: title_page,
        animation: google.maps.Animation.DROP,
        zIndex: Math.round(latlng.lat()*-100000)<<5
      });
    }
    else{
      var marker =new google.maps.Marker({
        position: latlng,
        map: map,
        title: title_page,
        draggable: false,
        animation: google.maps.Animation.BOUNCE
      });
    }
    gmarkers.push(marker);
    google.maps.event.addListener(marker, 'click', function() {
      infowindow.setContent(contentString);
			google.maps.event.trigger(map, 'resize');

      infowindow.open(map,marker);

    });
  }

  function initialize() {

    // create the map
    var myOptions = {
      zoom: <?php echo $defaultZoom;?>,
      center: new google.maps.LatLng(<?php echo $latitude ?>,<?php echo $longitude ?>),
      //  mapTypeControl: true,
      // mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU},
      navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("sitepage_browse_map_canvas"),
    myOptions);

    google.maps.event.addListener(map, 'click', function() {

      infowindow.close();
			google.maps.event.trigger(map, 'resize');
    });
<?php $textPostedBy='';?>
	<?php   foreach ($this->locations as $location) : ?>
		 <?php if($postedBy):?>
			 <?php $textPostedBy = $this->string()->escapeJavascript($this->translate('posted by')); ?>
			 <?php $textPostedBy.= " " . $this->htmlLink($this->sitepage[$location->page_id]->getOwner()->getHref(), $this->string()->escapeJavascript($this->sitepage[$location->page_id]->getOwner()->getTitle())) ?>
		 <?php endif; ?>
     // obtain the attribues of each marker
     var lat = <?php echo $location->latitude ?>;
     var lng =<?php echo $location->longitude  ?>;
     var point = new google.maps.LatLng(lat,lng);
     <?php if(!empty ($enableBouce)):?>
     var sponsored = <?php echo $this->sitepage[$location->page_id]->sponsored ?>
      <?php else:?>
     var sponsored =0;
     <?php endif; ?>
     // create the marker

		 <?php $page_id = $this->sitepage[$location->page_id]->page_id; ?>
     var contentString = '<div id="content">'+
       '<div id="siteNotice">'+
       '</div>'+'  <ul class="sitepages_locationdetails"><li>'+

       '<div class="sitepages_locationdetails_info_title">'+
				'<a href="<?php echo $this->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id)), 'sitepage_entry_view', true) ?>">'+"<?php echo  $this->string()->escapeJavascript($this->sitepage[$location->page_id]->getTitle()); ?>"+'</a>'+

       '<div class="fright">'+
       '<span >'+
              <?php if ($this->sitepage[$location->page_id]->featured == 1): ?>
                  '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'icon', 'title' =>  $this->string()->escapeJavascript($this->translate('Featured')))) ?>'+	            <?php endif; ?>
                  '</span>'+
                    '<span>'+
              <?php if ($this->sitepage[$location->page_id]->sponsored == 1): ?>
                  '<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' =>  $this->string()->escapeJavascript($this->translate('Sponsored')))) ?>'+
              <?php endif; ?>
		        '</span>'+
		      '</div>'+
	      '<div class="clr"></div>'+
	      '</div>'+

       '<div class="sitepages_locationdetails_photo" >'+
       '<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage[$location->page_id]->page_id, $this->sitepage[$location->page_id]->owner_id,$this->sitepage[$location->page_id]->getSlug()), $this->itemPhoto($this->sitepage[$location->page_id], 'thumb.normal')) ?>'+
       '</div>'+
       '<div class="sitepages_locationdetails_info">'+

				<?php if (is_array($this->statistics) && in_array('reviewCount', $this->statistics) && $this->ratngShow): ?>
					<?php if (($this->sitepage[$location->page_id]->rating > 0)): ?>
							'<span class="clr">'+
							<?php for ($x = 1; $x <= $this->sitepage[$location->page_id]->rating; $x++): ?>
									'<span class="rating_star_generic rating_star"></span>'+
							<?php endfor; ?>
							<?php if ((round($this->sitepage[$location->page_id]->rating) - $this->sitepage[$location->page_id]->rating) > 0): ?>
									'<span class="rating_star_generic rating_star_half"></span>'+
							<?php endif; ?>
									'</span>'+
					<?php endif; ?>
				<?php endif; ?>
            

              

              '<div class="sitepages_locationdetails_info_date">'+
                '<?php echo $this->timestamp(strtotime($this->sitepage[$location->page_id]->creation_date)) ?>'+' - <?php echo $textPostedBy?>'+
                '</div>'+

               <?php if (is_array($this->statistics) && !empty($this->statistics)) : ?>
							'<div class="sitepages_locationdetails_info_date">'+
							<?php 
                $statistics = '';
                
                if(in_array('likeCount', $this->statistics)) {
                  $statistics .= $this->string()->escapeJavascript($this->translate(array('%s like', '%s likes', $this->sitepage[$location->page_id]->like_count), $this->locale()->toNumber($this->sitepage[$location->page_id]->like_count))).', ';
                }

                if(in_array('followCount', $this->statistics)) {
                  $statistics .= $this->string()->escapeJavascript($this->translate(array('%s follower', '%s followers', $this->sitepage[$location->page_id]->follow_count), $this->locale()->toNumber($this->sitepage[$location->page_id]->follow_count))).', ';
                }
                
                if(in_array('memberCount', $this->statistics) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                $memberTitle = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.member.title' , 1);
									if ($this->sitepage[$location->page_id]->member_title && $memberTitle) {
										if ($this->sitepage[$location->page_id]->member_count == 1) : 
										
											if (!empty($this->membercalled) && !empty($memberTitle)) :
											$statistics .=  $this->sitepage[$location->page_id]->member_count . ' ' .  $this->sitepage[$location->page_id]->member_title.', ';
											else : 
											$statistics .=  $this->sitepage[$location->page_id]->member_count . ' member'.', ';
											endif;
										else:  
											$statistics .=  $this->sitepage[$location->page_id]->member_count . ' ' .  $this->sitepage[$location->page_id]->member_title.', ';
										endif; 
									} else {
										$statistics .= $this->string()->escapeJavascript($this->translate(array('%s member', '%s members', $this->sitepage[$location->page_id]->member_count), $this->locale()->toNumber($this->sitepage[$location->page_id]->member_count))).', ';
									}
                }
                
                if(in_array('reviewCount', $this->statistics) && !empty($this->ratngShow)) {
                  $statistics .= $this->string()->escapeJavascript($this->translate(array('%s review', '%s reviews', $this->sitepage[$location->page_id]->review_count), $this->locale()->toNumber($this->sitepage[$location->page_id]->review_count))).', ';
                }
                
                if(in_array('commentCount', $this->statistics)) {
                  $statistics .= $this->string()->escapeJavascript($this->translate(array('%s comment', '%s comments', $this->sitepage[$location->page_id]->comment_count), $this->locale()->toNumber($this->sitepage[$location->page_id]->comment_count))).', ';
                }


                if(in_array('viewCount', $this->statistics)) {
                  $statistics .= $this->string()->escapeJavascript($this->translate(array('%s view', '%s views', $this->sitepage[$location->page_id]->view_count), $this->locale()->toNumber($this->sitepage[$location->page_id]->view_count))).', ';
                }


                $statistics = trim($statistics);
                $statistics = rtrim($statistics, ',');
              ?>
              '<?php echo $statistics; ?>'+
							'</div>'+
						<?php endif; ?>
             <?php if($this->showContactDetails):?> 
             '<div class="sitepages_locationdetails_info_date">'+
								<?php if (!empty($this->sitepage[$location->page_id]->phone)): ?>
								"<?php  echo  $this->string()->escapeJavascript($this->translate("Phone: ")) . $this->sitepage[$location->page_id]->phone ?><br />"+
								<?php endif; ?>
								<?php if (!empty($this->sitepage[$location->page_id]->email)): ?>
								"<?php  echo  $this->string()->escapeJavascript($this->translate("Email: ")) . $this->sitepage[$location->page_id]->email ?><br />"+
								<?php endif; ?>
								<?php if (!empty($this->sitepage[$location->page_id]->website)): ?>
								"<?php  echo  $this->string()->escapeJavascript($this->translate("Website: ")) .$this->sitepage[$location->page_id]->website ?>"+
								<?php endif; ?>
             '</div>'+
              <?php endif; ?>        
              <?php if(!empty($this->showprice) && $this->sitepage[$location->page_id]->price && $this->enablePrice): ?>
                '<div class="sitepages_locationdetails_info_date">'+
								"<?php  echo  $this->string()->escapeJavascript($this->translate("Price: ")); echo  $this->locale()->toCurrency($this->sitepage[$location->page_id]->price, $currency) ?>"+
							'</div>'+
              <?php endif; ?>
							'<div class="sitepages_locationdetails_info_date">'+
								"<?php  $this->translate("Location: "); echo $this->string()->escapeJavascript($location->location); ?>"+
							'</div>'+
              '</div>'+
              '<div class="clr"></div>'+
              ' </li></ul>'+


              '</div>';

            var marker = createMarker(point,sponsored,contentString,"<?php echo str_replace('"',' ',$this->sitepage[$location->page_id]->getTitle()); ?>");
      <?php   endforeach; ?>

        }

        var infowindow = new google.maps.InfoWindow(
        {
          size: new google.maps.Size(250,50)
        });

        function toggleBounce() {
          for(var i=0; i<gmarkers.length;i++){
            if (gmarkers[i].getAnimation() != null) {
              gmarkers[i].setAnimation(null);
            }
          }
        }
</script>
<?php endif;?>
