<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/css.php?request=/application/modules/Sitepagenote/externals/styles/style_sitepagenote.css');
?>
<?php //if($this->showLightBox):
 // include_once APPLICATION_PATH . '/application/modules/Sitepagenote/views/scripts/_lightboxImage.tpl';
//endif; ?>
<script type="text/javascript" >
	function publishnote(thisobj) {
		var Obj_Url = thisobj.href;
		Smoothbox.open(Obj_Url);
	}

  var tagAction = function(tag){
    if($('filter_form')) {
       form=document.getElementById('filter_form');
      }else if($('filter_form_page')){
				form=$('filter_form_page');
    }   
    form.elements['tag'].value = tag;
    if( $('filter_form'))
    $('filter_form').submit();
		else
		$('filter_form_page').submit();
  }
</script>
<form id='filter_form' class='global_form_box' method='get' action='<?php echo $this->url(array(), 'sitepagenote_browse', true) ?>' style='display: none;'>
  <input type="hidden" id="tag" name="tag"  value=""/>
</form>
<div class="sitepage_viewpages_head">
	<?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
	<h2>
	  <?php echo $this->sitepage->__toString() ?>
	  <?php echo $this->translate('&raquo; ');?>
	  <?php //echo $this->htmlLink(array( 'route' => 'sitepage_entry_view', 'page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->sitepage->page_id), 'tab' => $this->tab_selected_id), $this->translate('Notes')) ?>
    <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Notes')) ?>
	  <?php echo $this->translate('&raquo; ');?>
	  <?php echo $this->sitepagenote->getTitle() ?>
	</h2>
</div>

<div class='layout_left'>
	<div class="sitepagenotes_view_photo">
		<?php if($this->sitepagenote->photo_id == 0):?>
			<?php if($this->sitepage->photo_id == 0):?>
				<?php echo $this->itemPhoto($this->sitepagenote, 'thumb.profile', '' , array('align'=>'left')) ?>   
			<?php else:?>
				<?php echo $this->itemPhoto($this->sitepage, 'thumb.profile', '' , array('align'=>'left')) ?>
			<?php endif;?>
		<?php else:?>
			<?php echo $this->itemPhoto($this->sitepagenote, 'thumb.profile', '' , array('align'=>'left')) ?>
		<?php endif;?>
	</div>      
	<div class="quicklinks sitepagenote_options">
		<ul>
			<li>
				<?php //echo $this->htmlLink(array('route' => 'sitepage_entry_view', 'page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->sitepagenote->page_id), 'tab' => $this->tab_selected_id), $this->translate('Back to Page'), array('class'=>'buttonlink  icon_sitepage_back')) ?>	
        <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Back to Page'),array('class'=>'buttonlink  icon_sitepage_back')) ?>
			</li>
			<?php if($this->can_create):?>
				<li>
					<a href='<?php echo $this->url(array('page_id' => $this->sitepage->page_id, 'tab' => $this->tab_selected_id), 'sitepagenote_create', true) ?>' class='buttonlink icon_sitepagenote_new'><?php echo $this->translate('Write a Note');?></a>
				</li>
			<?php endif; ?>
			
			<?php if($this->sitepagenote->owner_id == $this->viewer_id || $this->can_edit == 1): ?>
			<?php if($this->sitepagenote->draft == 1) :?>
				<li>
					<?php echo $this->htmlLink(array('route' => 'sitepagenote_publish', 'note_id' => $this->sitepagenote->note_id,'tab' => $this->tab_selected_id), $this->translate('Publish Note'), array(
						'class'=>'buttonlink icon_sitepagenote_publish', 'onclick' => 'publishnote(this);return false')) ?>   						
				</li>
				<?php endif; ?>
				<li>
					<?php echo $this->htmlLink(array('route' => 'sitepagenote_edit', 'note_id' => $this->sitepagenote->note_id, 'page_id' => $this->sitepagenote->page_id,'tab' => $this->tab_selected_id), $this->translate('Edit Note'), array('class' => 'buttonlink icon_sitepagenote_edit')) ?>
				</li>

				<li>
					<?php echo $this->htmlLink(array('route' => 'sitepagenote_delete', 'note_id' => $this->sitepagenote->note_id, 'page_id' => $this->sitepagenote->page_id,'tab' => $this->tab_selected_id), $this->translate('Delete Note'), array(
					'class'=>'buttonlink icon_sitepagenote_delete')) ?>
				</li>
			<?php endif; ?>
			
			<?php if($this->sitepagenote->owner_id == $this->viewer_id && Engine_Api::_()->getApi('settings', 'core')->sitepagenote_allow_image): ?>	
	     	<li>	
					<?php echo $this->htmlLink(array('route' => 'sitepagenote_photoupload', 'note_id' => $this->sitepagenote->note_id, 'tab' => $this->tab_selected_id), $this->translate('Add Photos'), array('class' => 'buttonlink icon_sitepagenote_photo_new')) ?> 
				
				</li>
			<?php endif; ?>
			
			<?php if(($this->sitepagenote->owner_id == $this->viewer_id || $this->can_edit == 1) && $this->sitepagenote->total_photos && Engine_Api::_()->getApi('settings', 'core')->sitepagenote_allow_image): ?>
				<li>
					<?php echo $this->htmlLink(array('route' => 'sitepagenote_editphoto', 'note_id' => $this->sitepagenote->note_id, 'page_id' => $this->sitepagenote->page_id,'tab' => $this->tab_selected_id), $this->translate('Edit Photos'), array('class' => 'buttonlink icon_sitepagenote_photo_edit')) ?><br />
				</li>
			<?php endif; ?>
			<!--  Start: Suggest to Friend link show work -->
			<?php if( !empty($this->noteSuggLink) && !empty($this->sitepagenote->search) && empty($this->sitepagenote->draft) ): ?>				
				<li>
					<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'suggestion', 'controller' => 'index', 'action' => 'popups', 'sugg_id' => $this->sitepagenote->note_id, 'sugg_type' => 'page_note'), $this->translate('Suggest to Friends'), array(
						'class'=>'buttonlink  icon_page_friend_suggestion smoothbox')) ?>
				</li>
			<?php endif; ?>					
			<!--  End: Suggest to Friend link show work -->
		</ul>
	</div>
	<br />
  <?php if( count($this->tagNotes) > 0 ): ?>
		<div class="generic_layout_container">
	    <?php if(count($this->tagNotes) > 1):?>
		    <h3> <?php echo $this->translate("Related Notes"); ?> </h3>
	    <?php elseif(count($this->tagNotes)== 1):?>
		    <h3> <?php echo $this->translate("Related Note"); ?> </h3>
	    <?php endif;?>
		  <ul class="sitepage_sidebar_list">
				<?php foreach ($this->tagNotes as $sitepagenote): ?>
					<li> 
						<?php if($sitepagenote->photo_id == 0):?>
						   <?php if($this->sitepage->photo_id == 0):?>
					  			<?php echo $this->htmlLink($sitepagenote->getHref(),$this->itemPhoto($sitepagenote, 'thumb.icon', $sitepagenote->getTitle())) ?>   
					  	<?php else:?>
					  	<?php echo $this->htmlLink($sitepagenote->getHref(),$this->itemPhoto($this->sitepage, 'thumb.icon', $sitepagenote->getTitle())) ?>
					  <?php endif;?>
					  <?php else:?>			   
					   		<?php echo $this->htmlLink($sitepagenote->getHref(),$this->itemPhoto($sitepagenote, 'thumb.icon', $sitepagenote->getTitle())) ?>
					  <?php endif;?>		
						<div class='sitepage_sidebar_list_info'>
							<div class='sitepage_sidebar_list_title'>
							<?php 
									$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.truncation.limit', 13);
									$tmpBody = strip_tags($sitepagenote->getTitle());
									
									$item_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
									
						  ?>
								<?php echo $this->htmlLink($sitepagenote->getHref(), $item_title, array('title'=> $sitepagenote->getTitle()));?> 	
							</div>
							<div class='sitepage_sidebar_list_details'>									
								<?php echo $this->translate(array('%s view', '%s views', $this->sitepagenote->view_count ), $this->locale()->toNumber($this->sitepagenote->view_count )) ?>
								|
								<?php echo $this->translate(array('%s comment', '%s comments', $this->sitepagenote->comment_count ), $this->locale()->toNumber($this->sitepagenote->comment_count )) ?>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
	      <?php if($this->count_note > $this->limit_sitepagenote):?>
					<li><a href='<?php echo $this->url(array('tab' => $this->tab_selected_id,'page_id' => $this->sitepage->page_id, 'see_all' => 1 ), 'sitepagenote_tagcreate', true) ?>' style="float:right;font-size:11px;"><?php echo $this->translate('See All');?> &raquo;</a></li>
				<?php endif;?>
		  </ul>	
		 </div> 
  <?php endif; ?>
</div>

<!--RIGHT AD START HERE-->
<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnoteview', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)):?>
	<div class="layout_right" id="communityad_noteview">
		<?php echo $this->content()->renderWidget("sitepage.page-ads", array('limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnoteview', 3), 'tab' =>'noteview', 'communityadid' => 'communityad_noteview', 'isajax' => 0));  ?>
	</div>
<?php endif;?>

<!--RIGHT AD END HERE-->
<div class="layout_middle">
  <ul class="sitepagenote_view">
    <li>
	    <h3>
	      <?php echo $this->sitepagenote->title ?>
	    </h3>
    	<div class="sitepagenote_view_stats">    	
				<?php echo $this->translate('Posted by %s', $this->htmlLink($this->sitepagenote->getOwner(), $this->sitepagenote->getOwner()->getTitle())) ?>
				<?php echo $this->timestamp($this->sitepagenote->creation_date) ?>			  
				  -			  
				<?php echo $this->translate(array('%s view', '%s views', $this->sitepagenote->view_count ), $this->locale()->toNumber($this->sitepagenote->view_count )) ?>
					-
				<?php echo $this->translate(array('%s comment', '%s comments', $this->sitepagenote->comment_count), $this->locale()->toNumber($this->sitepagenote->comment_count)) ?>			  
				  -
				<?php echo $this->translate(array('%s like', '%s likes', $this->sitepagenote->like_count ), $this->locale()->toNumber($this->sitepagenote->like_count )) ?>			
          
  		 <?php if (count($this->noteTags )):?>      
	        <?php  foreach ($this->noteTags as $tag): ?>
	        -
	         <a href='javascript:void(0);' onclick="javascript:tagAction('<?php echo $tag->getTag()->tag_id; ?>');">
	          <?php if(!empty($tag->getTag()->text)):?>#<?php endif;?><?php echo $tag->getTag()->text?></a>
	       <?php endforeach; ?>
      <?php  endif; ?>
      
      <!--FACEBOOK LIKE BUTTON START HERE-->
       <?php  $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
        if (!empty ($fbmodule)) :
          $enable_facebookse = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse'); 
          if (!empty ($enable_facebookse) && !empty($fbmodule->version)) :
            $fbversion = $fbmodule->version; 
            if (!empty($fbversion) && ($fbversion >= '4.1.5')) { ?>
               <div class="mtop10">
                  <?php echo Engine_Api::_()->facebookse()->isValidFbLike(); ?>
                </div>
            
            <?php } ?>
          <?php endif; ?>
     <?php endif; ?> 
     
  		</div>
	    <div class="sitepagenote_desc">
	      <?php echo $this->sitepagenote->body ?>
	    </div>
	  </li>      
		<?php if($this->sitepagenote->total_photos != 0): ?>
    	<li>
	      <div class="sitepagenote_images">
			    <?php foreach( $this->photoNotes as $photo ): ?>
			      	<div class="sitepagenote_img">
              <?php $phototitle = $photo->title;?>
             	<?php //if(!$this->showLightBox):?>
<!--              <a class="thumbs_photo" href="<?php //echo $this->url(array('owner_id' => $photo->user_id, 'album_id' => $photo->album_id, 'photo_id' => $photo->photo_id,'tab' => $this->tab_selected_id), 'sitepagenote_image_specific') ?>" title="<?php //echo $phototitle;?>">
                 <?php //echo $this->itemPhoto($photo, 'thumb.normal') ?>
              </a>-->
              <?php //else:?>
                <a href="<?php echo $this->url(array('owner_id' => $photo->user_id, 'album_id' => $photo->album_id, 'photo_id' => $photo->photo_id,'tab' => $this->tab_selected_id), 'sitepagenote_image_specific') ?>"  title="<?php echo $phototitle;?>"  <?php if(SEA_SITEPAGENOTE_LIGHTBOX) :?> onclick ='openSeaocoreLightBox("<?php echo $this->url(array('owner_id' => $photo->user_id, 'album_id' => $photo->album_id, 'photo_id' => $photo->photo_id,'tab' => $this->tab_selected_id), 'sitepagenote_image_specific') ?>");return false;' <?php endif;?> class="thumbs_photo">
                 <?php echo $this->itemPhoto($photo, 'thumb.normal') ?>
                </a>
            	<?php //endif; ?>
   				    	<?php if($this->viewer_id == $this->sitepagenote->owner_id || $this->can_edit == 1): ?>
				    		<?php echo $this->htmlLink(array('route'=>'sitepagenote_removeimage', 'note_id'=>$this->sitepagenote->note_id, 'photo_id' => $photo->photo_id, 'owner_id' => $photo->user_id,'tab' => $this->tab_selected_id), $this->translate('Delete')) ?> 
				    	<?php endif; ?>
					  </div> 
			    <?php endforeach;?>
	    	</div>
	    </li>	
  	<?php endif; ?>			

		<li>		
			<?php echo $this->action("list", "comment", "core", array("type"=>"sitepagenote_note", "id"=>$this->sitepagenote->note_id)) ?>
		</li>
  </ul>
</div>