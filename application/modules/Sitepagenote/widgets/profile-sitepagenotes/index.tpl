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
  if(file_exists(APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl'))
    include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<script type="text/javascript" >
function owner(thisobj) {
	var Obj_Url = thisobj.href ;
	Smoothbox.open(Obj_Url);
}
</script>

<?php if (!empty($this->show_content)) : ?>
	<script type="text/javascript">	
		function publishnote(thisobj) 
		{
			var Obj_Url = thisobj.href;
			Smoothbox.open(Obj_Url);
		}
		
	  var sitepageNotesSearchText = '<?php echo $this->search ?>';
	  var pageNotePage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
	  en4.core.runonce.add(function() 
	  { 	
	    var url = en4.core.baseUrl + 'widget/index/mod/sitepagenote/name/profile-sitepagenotes';
	    $('sitepage_notes_search_input_text').addEvent('keypress', function(e) {
	      if( e.key != 'enter' ) return;
	      if($('sitepage_notes_search_input_checkbox') && $('sitepage_notes_search_input_checkbox').checked == true) {
					var checkbox_value = 1;
				}
				else {
					var checkbox_value = 0;
				}
				if($('sitepagenote_search') != null) {
					$('sitepagenote_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagenote/externals/images/spinner_temp.gif" /></center>'; 
				}
	      en4.core.request.send(new Request.HTML({
	      'url' : url,
	      'data' : {
	        'format' : 'html',
	        'subject' : en4.core.subject.guid,
	        'search' : $('sitepage_notes_search_input_text').value,
					'selectbox' : $('sitepage_notes_search_input_selectbox').value,
					'checkbox' : checkbox_value,
					'isajax' : '1',
					'tab' : '<?php echo $this->content_id ?>'
	      }
	      }), {
	       'element' : $('id_' + <?php echo $this->content_id ?>)
	      });
	    });
	  });
	  
	  function showsearchnotecontent () 
	  {			 
			var url = en4.core.baseUrl + 'widget/index/mod/sitepagenote/name/profile-sitepagenotes';
	    $('sitepage_notes_search_input_text').addEvent('keypress', function(e) {
	      if( e.key != 'enter' ) return;
	      if($('sitepage_notes_search_input_checkbox') && $('sitepage_notes_search_input_checkbox').checked == true) {
					var checkbox_value = 1;
				}
				else {
					var checkbox_value = 0;
				}
				if($('sitepagenote_search') != null) {
					$('sitepagenote_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagenote/externals/images/spinner_temp.gif" /></center>'; 
				}
				
	      en4.core.request.send(new Request.HTML({
	      'url' : url,
	      'data' : {
	        'format' : 'html',
	        'subject' : en4.core.subject.guid,
	        'search' : $('sitepage_notes_search_input_text').value,
					'selectbox' : $('sitepage_notes_search_input_selectbox').value,
					'checkbox' : checkbox_value,
					'isajax' : '1',
					'tab' : '<?php echo $this->content_id ?>'
	      }
	      }), {
	       'element' : $('id_' + <?php echo $this->content_id ?>)
	      });
	    });
			  
			}
	
		function Ordernoteselect()
	  {
			var sitepageNotesSearchSelectbox = '<?php echo $this->selectbox ?>';
			var pageNotePage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
			var url = en4.core.baseUrl + 'widget/index/mod/sitepagenote/name/profile-sitepagenotes';
		 	if($('sitepage_notes_search_input_checkbox') && $('sitepage_notes_search_input_checkbox').checked == true) {
		    var checkbox_value = 1;
		  }
			else {
				var checkbox_value = 0;
			}
			if($('sitepagenote_search') != null) {
				$('sitepagenote_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagenote/externals/images/spinner_temp.gif" /></center>'; 
			} 
			en4.core.request.send(new Request.HTML({
				'url' : url,
	      'data' : {
					'format' : 'html',
					'subject' : en4.core.subject.guid,
					 'search' : $('sitepage_notes_search_input_text').value,
					 'selectbox' : $('sitepage_notes_search_input_selectbox').value,
					 'checkbox' : checkbox_value,
					 'isajax' : '1', 
					 'tab' : '<?php echo $this->content_id ?>'
	       }
	    }), {
				'element' : $('id_' + <?php echo $this->content_id ?>)
			});
		}
	
		function Mypagenotes() 
		{		
			var sitepageNotesSearchCheckbox = '<?php echo $this->checkbox ?>';
			var pageNotePage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
			var url = en4.core.baseUrl + 'widget/index/mod/sitepagenote/name/profile-sitepagenotes';
			
		 	if($('sitepage_notes_search_input_checkbox') && $('sitepage_notes_search_input_checkbox').checked == true) {
		    var checkbox_value = 1;
		  }
			else {
				var checkbox_value = 0;
			}
			if($('sitepagenote_search') != null) {
				$('sitepagenote_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagenote/externals/images/spinner_temp.gif" /></center>'; 
			}
			en4.core.request.send(new Request.HTML({
			'url' : url,
			'data' : {
				'format' : 'html',
				'subject' : en4.core.subject.guid,
				'search' : $('sitepage_notes_search_input_text').value,
				'selectbox' : $('sitepage_notes_search_input_selectbox').value,
				'checkbox' : checkbox_value,
				'isajax' : '1',
				'tab' : '<?php echo $this->content_id ?>'
			} 
		 }), {
			'element' : $('id_' + <?php echo $this->content_id ?>)
		 });
		}
	
	  var paginatePageNotes = function(page) 
	  {
	    var url = en4.core.baseUrl + 'widget/index/mod/sitepagenote/name/profile-sitepagenotes';
		 	if($('sitepage_notes_search_input_checkbox') && $('sitepage_notes_search_input_checkbox').checked == true) {
		    var checkbox_value = 1;
		  }
			else {
				var checkbox_value = 0;
			}
	
	    en4.core.request.send(new Request.HTML({
	      'url' : url,
	      'data' : {
	        'format' : 'html',
	        'subject' : en4.core.subject.guid,
	        'search' : sitepageNotesSearchText,
					'selectbox' : $('sitepage_notes_search_input_selectbox').value,
					'checkbox' : checkbox_value,
	        'page' : page,
	        'isajax' : '1',
	        'tab' : '<?php echo $this->content_id ?>'
	      }
	    }), {
	      'element' : $('id_' + <?php echo $this->content_id ?>)
	    });
	  }
	</script>
<?php endif;?>

<?php if (empty($this->isajax)) : ?>
	<div id="id_<?php echo $this->content_id; ?>">
<?php endif;?>


<?php if (!empty($this->show_content)) : ?>
  <?php if($this->showtoptitle == 1):?>
		<div class="layout_simple_head" id="layout_note">
			<?php echo $this->translate($this->sitepageSubject->getTitle());?><?php echo $this->translate("'s Notes");?>
		</div>
	<?php endif; ?>
	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnotewidget', 3)  && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepageSubject)):?>
	<div class="layout_right" id="communityad_note">
		<?php
		  echo $this->content()->renderWidget("sitepage.page-ads", array('limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnotewidget', 3), 'tab' => 'notes', 'communityadid' => 'communityad_note', 'isajax' => $this->isajax)); 
		?>
	</div>
	<div class="layout_middle">
	<?php endif;?>
	<?php if($this->can_create):?>
		<div class="seaocore_add">
		<a href='<?php echo $this->url(array('page_id' => $this->sitepageSubject->page_id, 'tab' => $this->identity_temp), 'sitepagenote_create', true) ?>' class='buttonlink icon_sitepagenote_new'><?php echo $this->translate('Write a Note');?></a>
		</div>
	<?php  endif; ?>
	<?php if( $this->paginator->count() <= 0 && (empty($this->search) && empty($this->checkbox) && empty($this->selectbox))): ?>
		<div class="sitepage_list_filters" style="display:none;">
	<?php else: ?>
		<div class="sitepage_list_filters">
	<?php endif; ?>
	<?php if(!empty($this->viewer_id)):?>
		<div class="sitepage_list_filter_first">
			<?php if($this->checkbox != 1): ?>
				<input id="sitepage_notes_search_input_checkbox" type="checkbox" value="1" onclick="Mypagenotes();" /><?php echo $this->translate("Show my notes");?>
			<?php else: ?>
				<input id="sitepage_notes_search_input_checkbox" type="checkbox" value="2"  checked = "checked" onclick="Mypagenotes();" /><?php echo $this->translate("Show my notes");?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
		<div class="sitepage_list_filter_field">
			<?php echo $this->translate("Search: ");?>
			<input id="sitepage_notes_search_input_text" type="text" value="<?php echo $this->search; ?>" />
	  </div>
		<div class="sitepage_list_filter_field">
			<?php echo $this->translate('Browse by:');?>	
			<select name="default_visibility" id="sitepage_notes_search_input_selectbox" onchange = "Ordernoteselect()">
				<?php if($this->selectbox == 'creation_date'): ?>
					<option value="creation_date" selected='selected'><?php echo $this->translate("Most Recent"); ?></option>
				<?php else:?>
					<option value="creation_date"><?php echo $this->translate("Most Recent"); ?></option>
				<?php endif;?>
				<?php if($this->selectbox == 'comment_count'): ?>
					<option value="comment_count" selected='selected'><?php echo $this->translate("Most Commented"); ?></option>
				<?php else:?>
					<option value="comment_count"><?php echo $this->translate("Most Commented"); ?></option>
				<?php endif;?>		
				<?php if($this->selectbox == 'view_count'): ?>
					<option value="view_count" selected='selected'><?php echo $this->translate("Most Viewed"); ?></option>
				<?php else:?>
					<option value="view_count"><?php echo $this->translate("Most Viewed"); ?></option>
				<?php endif;?>		
			  <?php if($this->selectbox == 'like_count'): ?>
					<option value="like_count" selected='selected'><?php echo $this->translate("Most Liked"); ?></option>
				<?php else:?>
					<option value="like_count"><?php echo $this->translate("Most Liked"); ?></option>
				<?php endif;?>
        <?php if($this->selectbox == 'featured'): ?>
					<option value="featured" selected='selected'><?php echo $this->translate("Featured"); ?></option>
			  <?php else:?>
					<option value="featured"><?php echo $this->translate("Featured"); ?></option>
			  <?php endif;?>
			</select>
		</div>
	</div>
<div id= 'sitepagenote_search'>
	<?php if( count($this->paginator) > 0 ):  ?>
		<ul class="sitepage_profile_list" >
		  <?php foreach ($this->paginator as $sitepagenote): ?>
			  <?php if($sitepagenote->owner_id != $this->viewer_id): ?>
				<li id="sitepagenote-item-<?php echo $sitepagenote->note_id ?>">
			  <?php else: ?>
				<li id="sitepagenote-item-<?php echo $sitepagenote->note_id ?>" class="sitepage_profile_list_owner">
				<?php endif; ?>
				<?php if($sitepagenote->photo_id == 0):?>
				   <?php if($this->sitepageSubject->photo_id == 0):?>
			  			<?php echo $this->htmlLink($sitepagenote->getHref(),$this->itemPhoto($sitepagenote, 'thumb.icon', $sitepagenote->getTitle())) ?>   
			  	<?php else:?>
			  	<?php echo $this->htmlLink($sitepagenote->getHref(),$this->itemPhoto($this->sitepageSubject, 'thumb.icon', $sitepagenote->getTitle())) ?>
			  <?php endif;?>
			  <?php else:?>
			   
			   		<?php echo $this->htmlLink($sitepagenote->getHref(),$this->itemPhoto($sitepagenote, 'thumb.icon', $sitepagenote->getTitle())) ?>

			   <?php endif;?>
				<div class="sitepage_profile_list_options">
					<?php $link = $sitepagenote->getHref()?>
					<?php echo $this->htmlLink($link, $this->translate('View Note'), array('class' => 'buttonlink icon_sitepagenote_viewall')) ?>
					<?php if($sitepagenote->owner_id == $this->viewer_id || $this->can_edit == 1): ?>
						<?php if($sitepagenote->draft == 1) echo $this->htmlLink(array('route' => 'sitepagenote_publish', 'note_id' => $sitepagenote->note_id, 'tab' => $this->identity_temp), $this->translate('Publish Note'), array(
					'class'=>'buttonlink  icon_sitepagenote_publish', 'onclick' => 'publishnote(this);return false')) ?> 
					<?php echo $this->htmlLink(array( 'route' => 'sitepagenote_edit', 'note_id' => $sitepagenote->note_id, 'page_id' => $this->sitepageSubject->page_id, 'tab' => $this->identity_temp), $this->translate('Edit Note'), array('class' => 'buttonlink icon_sitepagenote_edit')) ?>
						<?php echo $this->htmlLink(array('route' => 'sitepagenote_delete', 'note_id' => $sitepagenote->note_id, 'page_id' => $sitepagenote->page_id, 'tab' => $this->identity_temp), $this->translate('Delete Note'), array(
					'class'=>'buttonlink icon_sitepagenote_delete')) ?>
						<?php endif; ?>	
						<?php if($sitepagenote->owner_id == $this->viewer_id && Engine_Api::_()->getApi('settings', 'core')->sitepagenote_allow_image) :?>
				  	  <?php echo $this->htmlLink(array('route' => 'sitepagenote_photoupload', 'note_id' => $sitepagenote->note_id, 'tab' => $this->identity_temp), $this->translate('Add Photos'), array('class' => 'buttonlink icon_sitepagenote_photo_new')) ?> 		
						<?php endif; ?>
						<?php if(($sitepagenote->owner_id == $this->viewer_id || $this->can_edit == 1 ) && $sitepagenote->total_photos && Engine_Api::_()->getApi('settings', 'core')->sitepagenote_allow_image): ?>
							<?php echo $this->htmlLink(array('route' => 'sitepagenote_editphoto', 'note_id' => $sitepagenote->note_id, 'page_id' => $this->sitepageSubject->page_id, 'tab' => $this->identity_temp), $this->translate('Edit Photos'), array('class' => 'buttonlink icon_sitepagenote_photo_edit')) ?>
						<?php endif; ?>		
            <?php if(($this->allowView) && $this->canMakeFeatured):?>
							<?php if($sitepagenote->featured == 1) echo $this->htmlLink(array('route' => 'sitepagenote_featured', 'note_id' => $sitepagenote->note_id,'tab'=>$this->identity_temp), $this->translate('Make Un-featured'), array(
								'onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_unfeatured')) ?>
							<?php if($sitepagenote->featured == 0) echo $this->htmlLink(array('route' => 'sitepagenote_featured', 'note_id' => $sitepagenote->note_id,'tab'=>$this->identity_temp), $this->translate('Make Featured'), array(
								'onclick' => 'owner(this);return false',' class' => 'buttonlink seaocore_icon_featured')) ?>
						<?php endif;?>
				</div>					
			  <div class="sitepage_profile_list_info">
					<div class="sitepage_profile_list_title">
            <span>
							<?php if($sitepagenote->featured == 1): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.png', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
							<?php endif;?>
					  </span>
					  <?php echo $this->htmlLink($sitepagenote->getHref(), $sitepagenote->title) ?>
					</div>
					<div class="sitepage_profile_list_info_date">
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
					  <div class="sitepage_profile_list_info_des">
		          <?php $sitepagenote_body = strip_tags($sitepagenote->body);
										$sitepagenote_body = Engine_String::strlen($sitepagenote_body) > 200 ? Engine_String::substr($sitepagenote_body, 0, 200) . '..' : $sitepagenote_body;
							?>
					    <?php  echo $sitepagenote_body ?>
					  </div>
					<?php endif; ?>
		   	 </div>
		  	</li>
			<?php endforeach; ?>
		</ul>
	<?php if( $this->paginator->count() > 1 ): ?>
    <div>
      <?php if( $this->paginator->getCurrentPageNumber() > 1 ): ?>
        <div id="user_group_members_previous" class="paginator_previous">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            'onclick' => 'paginatePageNotes(pageNotePage - 1)',
            'class' => 'buttonlink icon_previous'
          )); ?>
        </div>
      <?php endif; ?>
      <?php if( $this->paginator->getCurrentPageNumber() < $this->paginator->count() ): ?>
        <div id="user_group_members_next" class="paginator_next">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
            'onclick' => 'paginatePageNotes(pageNotePage + 1)',
            'class' => 'buttonlink_right icon_next'
          )); ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>
	<?php elseif($this->paginator->count() <= 0 && ($this->search != '' || $this->checkbox == 1 || $this->selectbox == 'view_count' ||  $this->selectbox == 'comment_count' ||  $this->selectbox == 'like_count' ||  $this->selectbox == 'creation_date')):?>	
		<div class="tip" id='sitepagennote_search'>
			<span>
				<?php echo $this->translate('No notes were found matching your search criteria.');?>
			</span>
		</div>
	<?php else: ?>	
		<div class="tip" id='sitepagennote_search'>
		<span>
			<?php echo $this->translate('No notes have been written in this Page yet.'); ?>
			<?php if ($this->can_create):  ?>
				<?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a href="'.$this->url(array('page_id' => $this->sitepageSubject->page_id, 'tab' => $this->identity_temp), 'sitepagenote_create').'">', '</a>'); ?>
			<?php endif; ?>
		</span>
		</div>	
	<?php endif; ?>
</div>
	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnotewidget', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepageSubject)):?>
		</div>
	<?php endif; ?>
<?php endif;?>
<?php if (empty($this->isajax)) : ?>
	</div>
<?php endif;?>

<script type="text/javascript">
  var adwithoutpackage = '<?php echo Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepageSubject) ?>';
  var note_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnotewidget', 3);?>';
	var is_ajax_divhide = '<?php echo $this->isajax;?>';
	var execute_Request_Note = '<?php echo $this->show_content;?>';		
	var NotetabId = '<?php echo $this->module_tabid;?>';
  var show_widgets = '<?php echo $this->widgets ?>';
	var NoteTabIdCurrent = '<?php echo $this->identity_temp; ?>';
	var page_communityad_integration = '<?php echo $page_communityad_integration; ?>';
	if (NoteTabIdCurrent == NotetabId) {
	  if(page_showtitle != 0) {		 	 	
 	 	  if($('profile_status') && show_widgets == 1) {
	 	    $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitepageSubject->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Notes');?></h2>";	
 	 	  }
 	 	  if($('layout_note')) {
			 $('layout_note').style.display = 'block';
		  }
    }      
    hideWidgetsForModule('sitepagenote');
    prev_tab_id = '<?php echo $this->content_id; ?>';
    prev_tab_class = 'layout_sitepagenote_profile_sitepagenotes';   
    execute_Request_Note = true;
    hideLeftContainer (note_ads_display, page_communityad_integration, adwithoutpackage);
	}	 
  else if (is_ajax_divhide != 1) {  	
  	if($('global_content').getElement('.layout_sitepagenote_profile_sitepagenotes')) {
			$('global_content').getElement('.layout_sitepagenote_profile_sitepagenotes').style.display = 'none';
	  }	
	}
	
	$$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function() {
		$('global_content').getElement('.layout_sitepagenote_profile_sitepagenotes').style.display = 'block';
	 	if(page_showtitle != 0) {
	 		if($('profile_status') && show_widgets == 1) {
	 	    $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitepageSubject->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Notes');?></h2>";	
	 		}
	  } 	  
    hideWidgetsForModule('sitepagenote');
    $('id_' + <?php echo $this->content_id ?>).style.display = "block";
    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
      $$('.'+ prev_tab_class).setStyle('display', 'none');
    }
		if (prev_tab_id != '<?php echo $this->content_id; ?>') {
			execute_Request_Note = false;
			prev_tab_id = '<?php echo $this->content_id; ?>';
			prev_tab_class = 'layout_sitepagenote_profile_sitepagenotes';       		
		}
		if(execute_Request_Note == false) {		
			ShowContent('<?php echo $this->content_id; ?>', execute_Request_Note, '<?php echo $this->identity_temp?>', 'note', 'sitepagenote', 'profile-sitepagenotes', page_showtitle, 'null', note_ads_display, page_communityad_integration, adwithoutpackage);
			execute_Request_Note = true;    		
		}   	

		if('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1);?>' && note_ads_display == 0)
{setLeftLayoutForPage();}
		
		 
	}); 
</script>