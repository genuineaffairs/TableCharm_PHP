<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<?php if(empty($this->is_ajax)): ?>
<div class="layout_core_container_tabs">
<div class="tabs_alt tabs_parent">
  <ul id="main_tabs">
    <?php foreach ($this->tabs as $tab): ?>
    <?php $class = $tab->name == $this->activTab->name ? 'active' : '' ?>
      <li class = '<?php echo $class ?>'  id = '<?php echo 'sitepagenote_' . $tab->name.'_tab' ?>'>
        <a href='javascript:void(0);'  onclick="tabSwitchSitepagenote('<?php echo$tab->name; ?>');"><?php echo $this->translate($tab->getTitle()) ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<div id="hideResponse_div" style="display: none;"></div>
<div id="sitepagelbum_notes_tabs">   
   <?php endif; ?>
   <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
      <?php if($this->is_ajax !=2): ?>
     <ul class="seaocore_browse_list" id="sitepagenote_list_tab_note_content">
       <?php endif; ?>
      <?php foreach( $this->paginator as $note ): ?>
        <?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $note->page_id);?>
        <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagenote.profile-sitepagenotes', $note->page_id, $layout);?>
        <li>
					<div class="seaocore_browse_list_photo">
						<?php if($note->photo_id == 0):?>
							<?php 
							if($sitepage_object->photo_id == 0):?>
								<a href="<?php echo $note->getHref(array( 'page_id' => $note->page_id, 'note_id' => $note->note_id,'slug' => $note->getSlug(), 'tab' => $tab_id)); ?>">
									<span><?php echo $this->itemPhoto($note, 'thumb.profile', $note->getTitle()) ?>
								</a>
							<?php else:?>
								<a href="<?php echo $note->getHref(array( 'page_id' => $note->page_id, 'note_id' => $note->note_id,'slug' => $note->getSlug(), 'tab' => $tab_id)); ?>">
									<img src="<?php echo $sitepage_object->getPhotoUrl('thumb.normal'); ?>" alt="" />
							</a>
							<?php endif;?>
						<?php else:?>
							<a href="<?php echo $note->getHref(array( 'page_id' => $note->page_id, 'note_id' => $note->note_id,'slug' => $note->getSlug(), 'tab' => $tab_id)); ?>">
									<img src="<?php echo $note->getPhotoUrl('thumb.normal'); ?>" alt="" />
							</a>
						<?php endif;?>
					</div>
					<div class="seaocore_browse_list_info">
						<div class="seaocore_browse_list_info_title">
							<div class="seaocore_title">
								<?php echo $this->htmlLink($note->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->string()->truncate($note->getTitle(), 45), 10),array('title' => $note->getTitle()));?>
							</div>
            </div>
						<div class="seaocore_browse_list_info_date">
							<?php
							$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
							$tmpBody = strip_tags($sitepage_object->title);
							$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
							?>
							<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($note->page_id, $note->owner_id, $note->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>      
							<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?>   
								<?php echo $this->translate('by %1$s',$this->htmlLink($note->getOwner()->getHref(), $note->getOwner()->getTitle(), array('title' => $note->getOwner()->getTitle()))) ?>
							<?php endif;?>
						</div>
						<div class="seaocore_browse_list_info_date">
							<?php if( $this->activTab->name == 'viewed_pagenotes' ): ?>
								<?php echo $this->translate(array('%s view', '%s views', $note->view_count), $this->locale()->toNumber($note->view_count)) ?>
							<?php elseif( $this->activTab->name == 'commented_pagenotes' ): ?>
								<?php echo $this->translate(array('%s comment', '%s comments', $note->comment_count), $this->locale()->toNumber($note->comment_count)) ?>
							<?php elseif( $this->activTab->name == 'liked_pagenotes' ): ?>
								<?php echo $this->translate(array('%s like', '%s likes', $note->like_count), $this->locale()->toNumber($note->like_count)) ?>
							<?php endif; ?>
						</div>
						<div class="seaocore_browse_list_info_blurb">
							<?php $sitepagenote_body = strip_tags($note->body);
							$sitepagenote_body = Engine_String::strlen($sitepagenote_body) > 200 ? Engine_String::substr($sitepagenote_body, 0, 200) . '..' : $sitepagenote_body;
							?>
							<?php  echo $sitepagenote_body ?>
						</div>
					</div>
        </li>
      <?php endforeach;?>
       <?php if($this->is_ajax !=2): ?>  
    </ul>  
      <?php endif; ?>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('No notes have been created yet.');?>
      </span>
    </div>
  <?php endif; ?>   
<?php if(empty($this->is_ajax)): ?>    
</div>
<?php if (!empty($this->showViewMore)): ?>
<div class="seaocore_view_more" id="sitepagenote_notes_tabs_view_more" onclick="viewMoreTabNote()">
  <?php
  echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
      'id' => 'feed_viewmore_link',
      'class' => 'buttonlink icon_viewmore'
  ))
  ?>
</div>
<div class="seaocore_loading" id="sitepagenote_notes_tabs_loding_image" style="display: none;">
  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="" />
  <?php echo $this->translate("Loading ...") ?>
</div>
<?php endif; ?>
</div>
<?php endif; ?>

<?php if(empty($this->is_ajax)): ?>
<script type="text/javascript">
  
  var tabSwitchSitepagenote = function (tabName) {
 <?php foreach ($this->tabs as $tab): ?>
  if($('<?php echo 'sitepagenote_'.$tab->name.'_tab' ?>'))
        $('<?php echo 'sitepagenote_' .$tab->name.'_tab' ?>').erase('class');
  <?php  endforeach; ?>

 if($('sitepagenote_'+tabName+'_tab'))
        $('sitepagenote_'+tabName+'_tab').set('class', 'active');
   if($('sitepagelbum_notes_tabs')) {
      $('sitepagelbum_notes_tabs').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loader.gif" class="sitepage_tabs_loader_img" /></center>';
    }   
    if($('sitepagenote_notes_tabs_view_more'))
    $('sitepagenote_notes_tabs_view_more').style.display =  'none';
    var request = new Request.HTML({
     method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitepagenote/name/list-notes-tabs-view',
      'data' : {
        format : 'html',
        isajax : 1,
        category_id : '<?php echo $this->category_id?>',
        tabName: tabName,
        margin_photo : '<?php echo $this->marginPhoto ?>'
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('sitepagelbum_notes_tabs').innerHTML = responseHTML;
            <?php if(!empty ($this->showViewMore)): ?>
              hideViewMoreLinkSitepageNoteNote();
             <?php endif; ?> 
      }
    });

    request.send();
  }
</script>
<?php endif; ?>
<?php if(!empty ($this->showViewMore)): ?>
<script type="text/javascript">
    en4.core.runonce.add(function() {
    hideViewMoreLinkSitepageNoteNote();  
    });
    function getNextPageSitepageNoteNote(){
      return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }
    function hideViewMoreLinkSitepageNoteNote(){
      if($('sitepagenote_notes_tabs_view_more'))
        $('sitepagenote_notes_tabs_view_more').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>';
    }
        
    function viewMoreTabNote()
  {
    $('sitepagenote_notes_tabs_view_more').style.display ='none';
    $('sitepagenote_notes_tabs_loding_image').style.display ='';
    en4.core.request.send(new Request.HTML({
      method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitepagenote/name/list-notes-tabs-view',
      'data' : {
        format : 'html', 
        isajax : 2,
        category_id : '<?php echo $this->category_id?>',
        tabName : '<?php echo $this->activTab->name ?>',
        margin_photo : '<?php echo $this->marginPhoto ?>',
        page: getNextPageSitepageNoteNote()
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {   
        $('hideResponse_div').innerHTML=responseHTML;      
        var photocontainer = $('hideResponse_div').getElement('.layout_sitepagenote_list_notes_tabs_view').innerHTML;
        $('sitepagenote_list_tab_note_content').innerHTML = $('sitepagenote_list_tab_note_content').innerHTML + photocontainer;
        $('sitepagenote_notes_tabs_loding_image').style.display ='none';
        $('hideResponse_div').innerHTML="";        
      }
    }));

    return false;

  }  
</script>
<?php endif; ?>
