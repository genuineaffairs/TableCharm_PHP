<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
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
      <li class = '<?php echo $class ?>'  id = '<?php echo 'sitepagedocument_' . $tab->name.'_tab' ?>'>
        <a href='javascript:void(0);'  onclick="tabSwitchSitepagedocument('<?php echo$tab->name; ?>');"><?php echo $this->translate($tab->getTitle()) ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<div id="hideResponse_div" style="display: none;"></div>
<div id="sitepagelbum_documents_tabs">   
   <?php endif; ?>
   <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
      <?php if($this->is_ajax !=2): ?>
     <ul class="seaocore_browse_list" id="sitepagedocument_list_tab_document_content">
       <?php endif; ?>
      <?php foreach( $this->paginator as $document ): ?>
        <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagedocument.profile-sitepagedocuments', $document->page_id, $layout);?>
        <li>
					<div class="seaocore_browse_list_photo">
					<?php
						//SSL WORK
						$this->https = 0;
						if (!empty($_SERVER["HTTPS"]) && 'on' == strtolower($_SERVER["HTTPS"])) {
						$this->https = 1;
						}

						if($this->https) {
						$this->manifest_path = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.manifestUrl', "page-documents");
						$document->thumbnail = $this->baseUrl().'/'.$this->manifest_path."/ssl?url=".urlencode($document->thumbnail);
						}
			    ?>
          <?php echo $this->htmlLink($document->getHref(), '<img src="'. $document->thumbnail .'" alt="" />') ?>
					</div>
					<div class="seaocore_browse_list_info">
						<div class="seaocore_browse_list_info_title">
							<div class="seaocore_title">
              <?php echo $this->htmlLink($document->getHref(array('tab' => $tab_id)), $this->string()->chunk($this->string()->truncate($document->getTitle(), 45), 10),array('title'=> $document->getTitle())) ?>
							</div>
            </div>
						<div class="seaocore_browse_list_info_date">
							<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $document->page_id);?>
							<?php
							$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
							$tmpBody = strip_tags($sitepage_object->title);
							$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
							?>
							<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($document->page_id, $document->owner_id, $document->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>      
							<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?>  
								<?php echo $this->translate('by ').$this->htmlLink($document->getOwner()->getHref(), $document->getOwner()->getTitle(), array('title'=> $document->getOwner()->getTitle())) ?>
							<?php endif;?>
						</div>
						<div class="seaocore_browse_list_info_date">
							<?php if( $this->activTab->name == 'viewed_pagedocuments' ): ?>
								<?php echo $this->translate(array('%s view', '%s views', $document->views), $this->locale()->toNumber($document->views)) ?>
							<?php elseif( $this->activTab->name == 'commented_pagedocuments' ): ?>
								<?php echo $this->translate(array('%s comment', '%s comments', $document->comment_count), $this->locale()->toNumber($document->comment_count)) ?>
							<?php elseif( $this->activTab->name == 'liked_pagedocuments' ): ?>
								<?php echo $this->translate(array('%s like', '%s likes', $document->like_count), $this->locale()->toNumber($document->like_count)) ?>
							<?php endif; ?>
						</div>
						<div class="seaocore_browse_list_info_blurb"><?php echo $document->truncateText($document->sitepagedocument_description, 200); ?>
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
        <?php echo $this->translate('No documents have been created yet.');?>
      </span>
    </div>
  <?php endif; ?>   
<?php if(empty($this->is_ajax)): ?>    
</div>
<?php if (!empty($this->showViewMore)): ?>
<div class="seaocore_view_more" id="sitepagedocument_documents_tabs_view_more" onclick="viewMoreTabDocument()">
  <?php
  echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
      'id' => 'feed_viewmore_link',
      'class' => 'buttonlink icon_viewmore'
  ))
  ?>
</div>
<div class="seaocore_loading" id="sitepagedocument_documents_tabs_loding_image" style="display: none;">
  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/spinner_temp.gif' alt="" />
  <?php echo $this->translate("Loading ...") ?>
</div>
<?php endif; ?>
</div>
<?php endif; ?>

<?php if(empty($this->is_ajax)): ?>
<script type="text/javascript">
  
  var tabSwitchSitepagedocument = function (tabName) {
 <?php foreach ($this->tabs as $tab): ?>
  if($('<?php echo 'sitepagedocument_'.$tab->name.'_tab' ?>'))
        $('<?php echo 'sitepagedocument_' .$tab->name.'_tab' ?>').erase('class');
  <?php  endforeach; ?>

 if($('sitepagedocument_'+tabName+'_tab'))
        $('sitepagedocument_'+tabName+'_tab').set('class', 'active');
   if($('sitepagelbum_documents_tabs')) {
      $('sitepagelbum_documents_tabs').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loader.gif" class="sitepage_tabs_loader_img" /></center>';
    }   
    if($('sitepagedocument_documents_tabs_view_more'))
    $('sitepagedocument_documents_tabs_view_more').style.display =  'none';
    var request = new Request.HTML({
     method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitepagedocument/name/list-documents-tabs-view',
      'data' : {
        format : 'html',
        isajax : 1,
        category_id : '<?php echo $this->category_id?>',
        tabName: tabName,
        margin_photo : '<?php echo $this->marginPhoto ?>'
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('sitepagelbum_documents_tabs').innerHTML = responseHTML;
            <?php if(!empty ($this->showViewMore)): ?>
              hideViewMoreLinkSitepageDocumentDocument();
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
    hideViewMoreLinkSitepageDocumentDocument();  
    });
    function getNextPageSitepageDocumentDocument(){
      return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }
    function hideViewMoreLinkSitepageDocumentDocument(){
      if($('sitepagedocument_documents_tabs_view_more'))
        $('sitepagedocument_documents_tabs_view_more').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>';
    }
        
    function viewMoreTabDocument()
  {
    $('sitepagedocument_documents_tabs_view_more').style.display ='none';
    $('sitepagedocument_documents_tabs_loding_image').style.display ='';
    en4.core.request.send(new Request.HTML({
      method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitepagedocument/name/list-documents-tabs-view',
      'data' : {
        format : 'html', 
        isajax : 2,
        tabName : '<?php echo $this->activTab->name ?>',
        category_id : '<?php echo $this->category_id?>',
        margin_photo : '<?php echo $this->marginPhoto ?>',
        page: getNextPageSitepageDocumentDocument()
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {    
        $('hideResponse_div').innerHTML=responseHTML;      
        var photocontainer = $('hideResponse_div').getElement('.layout_sitepagedocument_list_documents_tabs_view').innerHTML;
        $('sitepagedocument_list_tab_document_content').innerHTML = $('sitepagedocument_list_tab_document_content').innerHTML + photocontainer;
        $('sitepagedocument_documents_tabs_loding_image').style.display ='none';
        $('hideResponse_div').innerHTML="";        
      }
    }));

    return false;

  }  
</script>
<?php endif; ?>
