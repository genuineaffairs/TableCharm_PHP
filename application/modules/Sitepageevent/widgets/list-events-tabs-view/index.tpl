<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
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
      <li class = '<?php echo $class ?>'  id = '<?php echo 'sitepageevent_' . $tab->name.'_tab' ?>'>
        <a href='javascript:void(0);'  onclick="tabSwitchSitepageevent('<?php echo$tab->name; ?>');"><?php echo $this->translate($tab->getTitle()) ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<div id="hideResponse_div" style="display: none;"></div>
<div id="sitepagelbum_events_tabs">   
   <?php endif; ?>
   <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
      <?php if($this->is_ajax !=2): ?>
     <ul class="seaocore_browse_list" id="sitepageevent_list_tab_event_content">
       <?php endif; ?>
      <?php foreach( $this->paginator as $event ): ?>
        <?php $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
						$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageevent.profile-sitepageevents', $event->page_id, $layout);?>
        <li>
					<div class="seaocore_browse_list_photo">
						<?php if($event->photo_id == 0)	:?>
							<a href="<?php echo $event->getHref(array( 'page_id' => $event->page_id, 'event_id' => $event->event_id,'slug' => $event->getSlug())); ?>">
								<?php echo $this->itemPhoto($event, 'thumb.profile', $event->getTitle()) ?>
							</a>
						<?php else :?>
							<a href="<?php echo $event->getHref(array( 'page_id' => $event->page_id, 'event_id' => $event->event_id,'slug' => $event->getSlug())); ?>">
								<img src="<?php echo $event->getPhotoUrl('thumb.normal'); ?>" alt="" />
							</a>
						<?php endif; ?>
					</div>
					<div class="seaocore_browse_list_info">
						<div class="seaocore_browse_list_info_title">
							<div class="seaocore_title">
								<?php echo $this->htmlLink($event->getHref(), $this->string()->chunk($this->string()->truncate($event->getTitle(), 45), 10),array('title' => $event->getTitle())) ?>
							</div>
            </div>
						<div class="seaocore_browse_list_info_date">
							<?php $sitepage_object = Engine_Api::_()->getItem('sitepage_page', $event->page_id);?>
							<?php
							$truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
							$tmpBody = strip_tags($sitepage_object->title);
							$page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
							?>
							<?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($event->page_id, $event->user_id, $event->getSlug()),  $page_title,array('title' => $sitepage_object->title)) ?>      
							<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?>  
								<?php echo $this->translate('by ').$this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle(), array('title' => $event->getOwner()->getTitle())) ?>
							<?php endif;?>
						</div>
						<div class="seaocore_browse_list_info_date">
							<?php if( $this->activTab->name == 'viewed_pageevents' ): ?>
								<?php echo $this->translate(array('%s view', '%s views', $event->view_count), $this->locale()->toNumber($event->view_count)) ?>
							<?php elseif( $this->activTab->name == 'member_pageevents' ): ?>
								<?php echo $this->translate(array('%s guest', '%s guests', $event->member_count), $this->locale()->toNumber($event->member_count)) ?>
							<?php endif; ?>
						</div>
						<div class='seaocore_browse_list_info_date'>
							<?php 
							$startDateObject = new Zend_Date(strtotime($event->starttime));
							if ($this->viewer() && $this->viewer()->getIdentity()) {    
								$tz = $this->viewer()->timezone; 				    
								$startDateObject->setTimezone($tz);	
							}    
							?>
							<?php
							echo $this->translate('%1$s at %2$s', $this->locale()->toDate($startDateObject), $this->locale()->toTime($startDateObject)
							)
							?>
						</div>
						<div class="seaocore_browse_list_info_blurb">
              <?php 
							$sitepagevent_body = Engine_String::strlen($event->description) > 200 ? Engine_String::substr($event->description, 0, 500) . '..' : $event->description;
							?>
							<?php  echo $sitepagevent_body; ?>
						</div>
        </li>
      <?php endforeach;?>
       <?php if($this->is_ajax !=2): ?>  
    </ul>  
      <?php endif; ?>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('No events have been created yet.');?>
      </span>
    </div>
  <?php endif; ?>   
<?php if(empty($this->is_ajax)): ?>    
</div>
<?php if (!empty($this->showViewMore)): ?>
<div class="seaocore_view_more" id="sitepageevent_events_tabs_view_more" onclick="viewMoreTabEvent()">
  <?php
  echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
      'id' => 'feed_viewmore_link',
      'class' => 'buttonlink icon_viewmore'
  ))
  ?>
</div>
<div class="seaocore_loading" id="sitepageevent_events_tabs_loding_image" style="display: none;">
  <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="" />
  <?php echo $this->translate("Loading ...") ?>
</div>
<?php endif; ?>
</div>
<?php endif; ?>

<?php if(empty($this->is_ajax)): ?>
<script type="text/javascript">
  
  var tabSwitchSitepageevent = function (tabName) {
 <?php foreach ($this->tabs as $tab): ?>
  if($('<?php echo 'sitepageevent_'.$tab->name.'_tab' ?>'))
        $('<?php echo 'sitepageevent_' .$tab->name.'_tab' ?>').erase('class');
  <?php  endforeach; ?>

 if($('sitepageevent_'+tabName+'_tab'))
        $('sitepageevent_'+tabName+'_tab').set('class', 'active');
   if($('sitepagelbum_events_tabs')) {
      $('sitepagelbum_events_tabs').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loader.gif" class="sitepage_tabs_loader_img" /></center>';
    }   
    if($('sitepageevent_events_tabs_view_more'))
    $('sitepageevent_events_tabs_view_more').style.display =  'none';
    var request = new Request.HTML({
     method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitepageevent/name/list-events-tabs-view',
      'data' : {
        format : 'html',
        isajax : 1,
        category_id : '<?php echo $this->category_id?>',
        tabName: tabName,
        margin_photo : '<?php echo $this->marginPhoto ?>'
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $('sitepagelbum_events_tabs').innerHTML = responseHTML;
            <?php if(!empty ($this->showViewMore)): ?>
              hideViewMoreLinkSitepageEventEvent();
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
    hideViewMoreLinkSitepageEventEvent();  
    });
    function getNextPageSitepageEventEvent(){
      return <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
    }
    function hideViewMoreLinkSitepageEventEvent(){
      if($('sitepageevent_events_tabs_view_more'))
        $('sitepageevent_events_tabs_view_more').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>';
    }
        
    function viewMoreTabEvent()
  {
    $('sitepageevent_events_tabs_view_more').style.display ='none';
    $('sitepageevent_events_tabs_loding_image').style.display ='';
    en4.core.request.send(new Request.HTML({
      method : 'post',
      'url' : en4.core.baseUrl + 'widget/index/mod/sitepageevent/name/list-events-tabs-view',
      'data' : {
        format : 'html', 
        isajax : 2,
        category_id : '<?php echo $this->category_id?>',
        tabName : '<?php echo $this->activTab->name ?>',
        margin_photo : '<?php echo $this->marginPhoto ?>',
        page: getNextPageSitepageEventEvent()
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {    
        $('hideResponse_div').innerHTML=responseHTML;      
        var photocontainer = $('hideResponse_div').getElement('.layout_sitepageevent_list_events_tabs_view').innerHTML;
        $('sitepageevent_list_tab_event_content').innerHTML = $('sitepageevent_list_tab_event_content').innerHTML + photocontainer;
        $('sitepageevent_events_tabs_loding_image').style.display ='none';
        $('hideResponse_div').innerHTML="";        
      }
    }));

    return false;

  }  
</script>
<?php endif; ?>
