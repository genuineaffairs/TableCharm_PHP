<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
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
	  var sitepageEventsSearchText = '<?php echo $this->search ?>';
	  var sitepageEventsPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
	  var event_selected = '<?php echo $this->clicked ?>';
	  en4.core.runonce.add(function() {
	    var url = en4.core.baseUrl + 'widget/index/mod/sitepageevent/name/profile-sitepageevents';
	    $('sitepage_events_search_input_text').addEvent('keypress', function(e) { 
	      if( e.key != 'enter' ) return;
				 if($('sitepageevent_search') != null) {
					$('sitepageevent_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepageevent/externals/images/spinner_temp.gif" /></center>'; 
				 }
	        temp_eventrequest = new Request.HTML({ 
	        'url' : url,
	        'data' : {
	          'format' : 'html',
	          'subject' : en4.core.subject.guid,
	          'search' : $('sitepage_events_search_input_text').value,
						'selectbox' : $('sitepage_events_search_input_selectbox').value,
						'isajax' : 1,
						'clicked_event' : event_selected,
						'tab' : '<?php echo $this->content_id ?>'
	        },
				 onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
       	 $('id_' + <?php echo $this->content_id ?>).innerHTML = responseHTML;       	
	       	 if(event_selected == 'upcomingevent') {
	 					  if(document.getElementById('select_upcomingevent')) {
							  document.getElementById('select_upcomingevent').className="selected";	 
					    } 
					    if(document.getElementById('select_pastevent')) {
					    	document.getElementById('select_pastevent').className="";	
					    }
					    if(document.getElementById('select_myevent')) { 
						    document.getElementById('select_myevent').className="";	      
					    }  	 	
	       	 } else if(event_selected == 'pastevent') {
	  					 if(document.getElementById('select_pastevent')) {
							   document.getElementById('select_pastevent').className="selected";	 
					     } 
					     if(document.getElementById('select_upcomingevent')) {
						     document.getElementById('select_upcomingevent').className="";	 
					     }
					     if(document.getElementById('select_myevent')) { 
					       document.getElementById('select_myevent').className="";	      
					     }    	 	
	       	 } else if(event_selected == 'myevent') {
	  					 if(document.getElementById('select_myevent')) {
							   document.getElementById('select_myevent').className="selected";	 
					     } 
					     if(document.getElementById('select_upcomingevent')) {
						     document.getElementById('select_upcomingevent').className="";	 
					     }
					     if(document.getElementById('select_pastevent')) {
					       document.getElementById('select_pastevent').className="";	
					     }          	 	
	       	 }
         }	        
        });
       temp_eventrequest.send();
	    });
	  });
	
	  function showsearcheventcontent() { 
	      var url = en4.core.baseUrl + 'widget/index/mod/sitepageevent/name/profile-sitepageevents';
	    $('sitepage_events_search_input_text').addEvent('keypress', function(e) {
	      if( e.key != 'enter' ) return;
				 if($('sitepageevent_search') != null) {
					$('sitepageevent_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepageevent/externals/images/spinner_temp.gif" /></center>'; 
				 }
	        temp_eventrequest = new Request.HTML({ 
	        'url' : url,
	        'data' : {
	          'format' : 'html',
	          'subject' : en4.core.subject.guid,
	          'search' : $('sitepage_events_search_input_text').value,
						'selectbox' : $('sitepage_events_search_input_selectbox').value,
						'isajax' : 1,
						'clicked_event' : event_selected,
						'tab' : '<?php echo $this->content_id ?>'
	        },
				 onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
       	 $('id_' + <?php echo $this->content_id ?>).innerHTML = responseHTML; 
       	 showsearcheventcontent();      	
	       	 if(event_selected == 'upcomingevent') {
	 					  if(document.getElementById('select_upcomingevent')) {
							  document.getElementById('select_upcomingevent').className="selected";	 
					    } 
					    if(document.getElementById('select_pastevent')) {
					    	document.getElementById('select_pastevent').className="";	
					    }
					    if(document.getElementById('select_myevent')) { 
						    document.getElementById('select_myevent').className="";	      
					    }  	 	
	       	 } else if(event_selected == 'pastevent') {
	  					 if(document.getElementById('select_pastevent')) {
							   document.getElementById('select_pastevent').className="selected";	 
					     } 
					     if(document.getElementById('select_upcomingevent')) {
						     document.getElementById('select_upcomingevent').className="";	 
					     }
					     if(document.getElementById('select_myevent')) { 
					       document.getElementById('select_myevent').className="";	      
					     }    	 	
	       	 } else if(event_selected == 'myevent') {
	  					 if(document.getElementById('select_myevent')) {
							   document.getElementById('select_myevent').className="selected";	 
					     } 
					     if(document.getElementById('select_upcomingevent')) {
						     document.getElementById('select_upcomingevent').className="";	 
					     }
					     if(document.getElementById('select_pastevent')) {
					       document.getElementById('select_pastevent').className="";	
					     }          	 	
	       	 }
         }
	        
	        });
         temp_eventrequest.send();
	    });
	 }  
	  
	 function Ordereventselect(selectedevent){
			var sitepageEventsSearchSelectbox = '<?php echo $this->selectbox ?>';
			var sitepageEventsPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
			var url = en4.core.baseUrl + 'widget/index/mod/sitepageevent/name/profile-sitepageevents';
			 if($('sitepageevent_search') != null) {
				$('sitepageevent_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepageevent/externals/images/spinner_temp.gif" /></center>'; 
			 } 
			en4.core.request.send(new Request.HTML({
				'url' : url,
	      'data' : {
					'format' : 'html',
					'subject' : en4.core.subject.guid,
					 'search' : $('sitepage_events_search_input_text').value,
					 'selectbox' : $('sitepage_events_search_input_selectbox').value,
					 'isajax' : 1,
					 'clicked_event' : selectedevent,
					 'tab' : '<?php echo $this->content_id ?>'
	       },
				 onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
       	 $('id_' + <?php echo $this->content_id ?>).innerHTML = responseHTML;       	
	       	 if(selectedevent == 'upcomingevent') {
	 					  if(document.getElementById('select_upcomingevent')) {
							  document.getElementById('select_upcomingevent').className="selected";	 
					    } 
					    if(document.getElementById('select_pastevent')) {
					    	document.getElementById('select_pastevent').className="";	
					    }
					    if(document.getElementById('select_myevent')) { 
						    document.getElementById('select_myevent').className="";	      
					    }  	 	
	       	 } else if(selectedevent == 'pastevent') {
	  					 if(document.getElementById('select_pastevent')) {
							   document.getElementById('select_pastevent').className="selected";	 
					     } 
					     if(document.getElementById('select_upcomingevent')) {
						     document.getElementById('select_upcomingevent').className="";	 
					     }
					     if(document.getElementById('select_myevent')) { 
					       document.getElementById('select_myevent').className="";	      
					     }    	 	
	       	 } else if(selectedevent == 'myevent') {
	  					 if(document.getElementById('select_myevent')) {
							   document.getElementById('select_myevent').className="selected";	 
					     } 
					     if(document.getElementById('select_upcomingevent')) {
						     document.getElementById('select_upcomingevent').className="";	 
					     }
					     if(document.getElementById('select_pastevent')) {
					       document.getElementById('select_pastevent').className="";	
					     }          	 	
	       	 }
         }}));
		}
		
		function Mypageevents(click_event) 
		{		
			var clicked_event = '<?php echo $this->clicked ?>';			
			var sitepageEventsPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
			var url = en4.core.baseUrl + 'widget/index/mod/sitepageevent/name/profile-sitepageevents';
			
                        var data = {
                          'format' : 'html',
                          'subject' : en4.core.subject.guid,
                          'search' : $('sitepage_events_search_input_text').value,
                          'selectbox' : $('sitepage_events_search_input_selectbox').value,
                          'isajax' : 1,
                          'clicked_event' : click_event,
                          'tab' : '<?php echo $this->content_id ?>'
                        }
                        
                        if(click_event == 'calendar') {
                          data.calendar = 1;
                          data.clicked_event = 'upcomingevent';
                        }
                        
			 if($('sitepageevent_search') != null) {
					$('sitepageevent_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepageevent/externals/images/spinner_temp.gif" /></center>'; 
			 }
					en4.core.request.send(new Request.HTML({
					'url' : url,
					'data' : data, 
				 onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
	       	 $('id_' + <?php echo $this->content_id ?>).innerHTML = responseHTML;
                 if(click_event == 'calendar') {
                   //$exec('initCalendar();');
                 }
                 $$('.event_tabs').removeClass('selected');
                 $('select_' + click_event).addClass('selected');
					    } 
				}));
		}
	
	  var paginateSitepageevents = function(page, click_event) 
	  {
	    var url = en4.core.baseUrl + 'widget/index/mod/sitepageevent/name/profile-sitepageevents';
	    en4.core.request.send(new Request.HTML({
	      'url' : url,
	      'data' : {
	        'format' : 'html',
	        'subject' : en4.core.subject.guid,
	        'search' : sitepageEventsSearchText,
					'selectbox' : $('sitepage_events_search_input_selectbox').value,
	        'page' : page,
	        'isajax' : 1,
	        'clicked_event' : click_event,
	        'tab' : '<?php echo $this->content_id ?>'
	      }, 
				 onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
       	 $('id_' + <?php echo $this->content_id ?>).innerHTML = responseHTML;       	
	       	 if(click_event == 'upcomingevent') {
	 					  if(document.getElementById('select_upcomingevent')) {
							  document.getElementById('select_upcomingevent').className="selected";	 
					    } 
					    if(document.getElementById('select_pastevent')) {
					    	document.getElementById('select_pastevent').className="";	
					    }
					    if(document.getElementById('select_myevent')) { 
						    document.getElementById('select_myevent').className="";	      
					    }  	 	
	       	 } else if(click_event == 'pastevent') {
	  					 if(document.getElementById('select_pastevent')) {
							   document.getElementById('select_pastevent').className="selected";	 
					     } 
					     if(document.getElementById('select_upcomingevent')) {
						     document.getElementById('select_upcomingevent').className="";	 
					     }
					     if(document.getElementById('select_myevent')) { 
					       document.getElementById('select_myevent').className="";	      
					     }    	 	
	       	 } else if(click_event == 'myevent') {
	  					 if(document.getElementById('select_myevent')) {
							   document.getElementById('select_myevent').className="selected";	 
					     } 
					     if(document.getElementById('select_upcomingevent')) {
						     document.getElementById('select_upcomingevent').className="";	 
					     }
					     if(document.getElementById('select_pastevent')) {
					       document.getElementById('select_pastevent').className="";	
					     }          	 	
	       	 }
         }}));
	  }
	</script>
<?php endif;?>

<?php // TRISTAN ADD CALENDAR ?>
<script language="javascript" type="text/javascript" src="<?php echo($this->baseUrl() . '/application/modules/Ecalendar/externals/scripts/mecPHPPlugin.js'); ?>"></script>
<script language="javascript" type="text/javascript" src="<?php echo($this->baseUrl() . '/application/modules/Ecalendar/externals/scripts/mooECal.js'); ?>"></script>
<script  language="javascript" type="text/javascript">
  function initCalendar() {
    var feedPlugin = new mecPHPPlugin();
    feedPlugin.initialize({url:"<?php echo($this->baseUrl() . '/sitepageevent/index/page-events/subject/'); ?>" + en4.core.subject.guid})
    var today = new Date();
    new Calendar({
      calContainer: 'calBody',
      newDate: today,
      feedPlugin: feedPlugin,
      feedSpan: 2
    });
  }
</script>

<?php if (empty($this->isajax)) : ?>
	<div id="id_<?php echo $this->content_id; ?>">
<?php endif;?>

<?php if (!empty($this->show_content)) : ?>
  <?php if($this->showtoptitle == 1):?>
		<div class="layout_simple_head" id="layout_event">
      <?php echo $this->translate($this->sitepage_subject->getTitle());?><?php echo $this->translate("'s Events");?>
		</div>
  <?php endif;?>
	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventwidget', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage_subject)):?>
		<div class="layout_right" id="communityad_event">
		 <?php
			echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventwidget', 3),"loaded_by_ajax"=>1,'widgetId'=>'page_event')); 			 
			?>
		</div>
		<div class="layout_middle">
	<?php endif;?>
	<?php if($this->can_create): ?>
		<div class="seaocore_add fleft">
			<a href='<?php echo $this->url(array('page_id' => $this->sitepage_subject->page_id, 'tab_id' => $this->identity_temp), 'sitepageevent_create', true) ?>' class='buttonlink icon_sitepageevent_new'><?php echo $this->translate('Create_an_Event');?></a>
		</div>
	<?php endif; ?>	
	
	<?php if( !empty($this->getIsEvent) ) { ?>
		<?php if( $this->eventCount > 0) :?>
			<div class="sitepage_event_profile_links">
                                <a href="javascript:void(0);" onclick="Mypageevents('calendar');" id='select_calendar' class='event_tabs selected'><?php echo $this->translate('Circle\'s Calendar')?></a>
				|
				<a href="javascript:void(0);" onclick="Mypageevents('upcomingevent');" id='select_upcomingevent' class="event_tabs"><?php echo $this->translate('Upcoming Events')?></a>
				|
				<a href="javascript:void(0);" onclick="Mypageevents('pastevent');" id='select_pastevent' class='event_tabs'><?php echo $this->translate('Past_Events');?></a>		
				<?php if($this->can_create): ?>
				|
                                <a href="javascript:void(0);" onclick="Mypageevents('myevent');" id='select_myevent' class="event_tabs"><?php echo $this->translate('My_Events');?></a>
				<?php endif; ?>	
			</div>
		<?php endif; ?>
	<?php } ?>
        
        <?php // TRISTAN ADD CALENDAR ?>
        <?php if($this->isCalendar) : ?>
          <input id='sitepage_events_search_input_text' value='' type='hidden' />
          <input id='sitepage_events_search_input_selectbox' value='starttime' type='hidden' />
          <div style='clear:both'></div>
          <div id='sitepageevent_search'>
            <link rel="stylesheet" type="text/css" href="<?php echo($this->baseUrl() . '/application/modules/Ecalendar/externals/styles/main.css'); ?>" />
            <div class='layout_middle'>
              <div id="calBody"></div>
            </div>
          </div>
          
          <?php // Prevent calendar from being loaded twice when tab is loaded by default ?>
          <?php if (!empty($this->isajax)) : ?>
          <script type="text/javascript">
            setTimeout(function() {
              initCalendar()
            }, 500);
          </script>
          <?php endif; ?>
        <?php goto endScript; endif; ?>

	<?php if( $this->paginator->count() <= 0 && (empty($this->search) && empty($this->selectbox))): ?>
		<div class="sitepage_list_filters" style="display:none;">
	<?php else:?>
		<div class="sitepage_list_filters">
	<?php endif; ?>
	 <?php if($this->clicked == 'upcomingevent' || $this->clicked == 'pastevent'):?>	
		<div class="sitepage_list_filter_field">
			<?php echo $this->translate("Search: ");?>
			<input id="sitepage_events_search_input_text" type="text" value="<?php echo $this->search; ?>" />
	  </div> 

		<div class="sitepage_list_filter_field">		 
				<?php echo $this->translate('List by:');?>
				<select name="default_visibility" id="sitepage_events_search_input_selectbox" onchange = "Ordereventselect('<?php echo $this->clicked; ?>')">
				  <?php if($this->selectbox == 'starttime'): ?>
						<option value="starttime" selected='selected'><?php echo $this->translate("Start Time"); ?></option>
					<?php else:?>
						<option value="starttime"><?php echo $this->translate("Start Time"); ?></option>
					<?php endif;?>
					<?php if($this->selectbox == 'member_count'): ?>
						<option value="member_count" selected='selected'><?php echo $this->translate("Most Popular"); ?></option>
					<?php else:?>
						<option value="member_count"><?php echo $this->translate("Most Popular"); ?></option>
					<?php endif;?>
					<?php if($this->selectbox == 'creation_date'): ?>
						<option value="creation_date" selected='selected'><?php echo $this->translate("Most Recent"); ?></option>
					<?php else:?>
						<option value="creation_date"><?php echo $this->translate("Most Recent"); ?></option>
					<?php endif;?>		
          <?php if($this->selectbox == 'featured'): ?>
						<option value="featured" selected='selected'><?php echo $this->translate("Featured"); ?></option>
					<?php else:?>
						<option value="featured"><?php echo $this->translate("Featured"); ?></option>
					<?php endif;?>		
				</select>		
			</div>			
		</div>
			
	<?php elseif( $this->clicked == 'myevent'):?>
			<div class="sitepage_list_filter_field">
			<?php echo $this->translate("Search: ");?>
			<input id="sitepage_events_search_input_text" type="text" value="<?php echo $this->search; ?>" />
	  	</div>
  	<div class="sitepage_list_filter_field">
		 
				<?php echo $this->translate('View:');?>
				<select name="default_visibility" id="sitepage_events_search_input_selectbox" onchange = "Ordereventselect('<?php echo $this->clicked; ?>')">
				  <?php if($this->selectbox == 'allmyevent'): ?>
						<option value="allmyevent" selected='selected'><?php echo $this->translate("All_My_Events"); ?></option>
					<?php else:?>
						<option value="allmyevent"><?php echo $this->translate("All_My_Events"); ?></option>
					<?php endif;?>
					<?php if($this->selectbox == 'eventilead'): ?>
						<option value="eventilead" selected='selected'><?php echo $this->translate("Only_Events_I_Lead"); ?></option>
					<?php else:?>
						<option value="eventilead"><?php echo $this->translate("Only_Events_I_Lead"); ?></option>
					<?php endif;?>				
				</select>			
				</div>			
		</div>
	<?php endif;?>	
<div id='sitepageevent_search'>	
  <?php if( count($this->paginator) > 0 ): ?>
    <ul class="sitepage_profile_list" >
      <?php foreach ($this->paginator as $sitepageevent): ?>
      	<?php if($sitepageevent->user_id != $this->viewer_id): ?>
      		<li id="sitepageevent-item-<?php echo $sitepageevent->event_id ?>">
       	<?php else: ?>
      		<li id="sitepageevent-item-<?php echo $sitepageevent->event_id ?>" class="sitepage_profile_list_owner">
      	<?php endif; ?>
	        <?php echo  $this->htmlLink(
	          $sitepageevent->getHref(),
	          $this->itemPhoto($sitepageevent, 'thumb.icon', $sitepageevent->getTitle())
	        ) ?>
					<div class="sitepage_profile_list_options">
						<?php echo $this->htmlLink($sitepageevent->getHref(), $this->translate('View_Event'), array('class' => 'buttonlink icon_sitepageevent')) ?>
						<?php if($this->viewer_id == $sitepageevent->user_id || $this->can_edit == 1): ?>
						<?php echo $this->htmlLink(array( 'route' => 'sitepageevent_specific', 'action' => 'edit', 'event_id' => $sitepageevent->event_id, 'page_id' => $this->sitepage_subject->page_id, 'tab_id' => $this->identity_temp), $this->translate('Edit Event'), array('class' => 'buttonlink icon_sitepageevent_edit')) ?>

						<?php echo $this->htmlLink(array( 'route' => 'sitepageevent_specific', 'action' => 'edit-location', 'seao_locationid' => $sitepageevent->seao_locationid, 'event_id' => $sitepageevent->event_id, 'page_id' => $this->sitepage_subject->page_id, 'tab_id' => $this->identity_temp), $this->translate('Edit Location'), array('class' => 'buttonlink icon_sitepages_map_edit')); ?>

						<?php echo $this->htmlLink(array('route' => 'sitepageevent_specific', 'action' => 'delete', 'event_id' => $sitepageevent->getIdentity(), 'page_id' => $this->sitepage_subject->page_id, 'tab_id' => $this->identity_temp), $this->translate('Delete Event'), array(
							'class' => 'buttonlink icon_sitepageevent_delete'
						)) ?>
						<?php endif; ?>
            <?php if($this->allowView):?>
							<?php if($sitepageevent->featured == 1) echo $this->htmlLink(array('route' => 'sitepageevent_featured', 'event_id' => $sitepageevent->event_id,'tab'=>$this->identity_temp), $this->translate('Make Un-featured'), array(
								'onclick' => 'owner(this);return false', ' class' => 'buttonlink seaocore_icon_unfeatured')) ?>
							<?php if($sitepageevent->featured == 0) echo $this->htmlLink(array('route' => 'sitepageevent_featured', 'event_id' => $sitepageevent->event_id,'tab'=>$this->identity_temp), $this->translate('Make Featured'), array(
								'onclick' => 'owner(this);return false',' class' => 'buttonlink seaocore_icon_featured')) ?>
						<?php endif;?>
					</div>
	        <div class="sitepage_profile_list_info">
	          <div class="sitepage_profile_list_title">
              <span>
								<?php if($sitepageevent->featured == 1): ?>
									<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/featured.png', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
								<?php endif;?>
					    </span>
	            <?php echo $this->htmlLink($sitepageevent->getHref(), $sitepageevent->title) ?>
	          </div>
	          <div class="sitepage_profile_list_info_date">
	          <?php echo $this->translate('Led by %s', $this->htmlLink($sitepageevent->getOwner(), $sitepageevent->getOwner()->getTitle())) ?>
	            <?php echo $this->timestamp($sitepageevent->creation_date) ?>	            
	            -	            
	            <?php echo $this->translate(array('%s view', '%s views', $sitepageevent->view_count ), $this->locale()->toNumber($sitepageevent->view_count )) ?>							
	            -
	            <?php echo $this->translate(array('%s guest', '%s guests', $sitepageevent->member_count ), $this->locale()->toNumber($sitepageevent->member_count )) ?>	            
	          </div>
      <div class="sitepage_profile_list_info_date">
      <?php
      // Convert the dates for the viewer
      $startDateObject = new Zend_Date(strtotime($sitepageevent->starttime));
      $endDateObject = new Zend_Date(strtotime($sitepageevent->endtime));
      if ($this->viewer() && $this->viewer()->getIdentity()) {
        $tz = $this->viewer()->timezone;
        $startDateObject->setTimezone($tz);
        $endDateObject->setTimezone($tz);
      }
      ?>
      <?php if ($sitepageevent->starttime == $sitepageevent->endtime): ?>


          <?php echo $this->locale()->toDate($startDateObject) ?>

 - 

          <?php echo $this->locale()->toTime($startDateObject) ?>


      <?php elseif ($startDateObject->toString('y-MM-dd') == $endDateObject->toString('y-MM-dd')): ?>


          <?php echo $this->locale()->toDate($startDateObject) ?>

 - 

          <?php echo $this->locale()->toTime($startDateObject) ?>
          -
          <?php echo $this->locale()->toTime($endDateObject) ?>

      <?php else: ?>  

          <?php
          echo $this->translate('%1$s at %2$s', $this->locale()->toDate($startDateObject), $this->locale()->toTime($startDateObject)
          )
          ?>
	- 
          <?php
          echo $this->translate('%1$s at %2$s', $this->locale()->toDate($endDateObject), $this->locale()->toTime($endDateObject)
          )
          ?>

      <?php endif ?>
	       </div>
            <?php if (!empty($sitepageevent->description)): ?>
	            <div class="sitepage_profile_list_info_des">
	              <?php $sitepageevent_description = strip_tags($sitepageevent->description);
											$sitepageevent_description = Engine_String::strlen($sitepageevent_description) > 200 ? Engine_String::substr($sitepageevent_description, 0, 200) . '..' : $sitepageevent_description;
								?>
	              <?php  echo $sitepageevent_description ?>
	            </div>
	          <?php endif; ?>
	       </div>
       </li>
      <?php endforeach; ?>
	  </ul>
	<?php if( $this->paginator->count() > 1 ): ?>
    <div>
      <?php if( $this->paginator->getCurrentPageNumber() > 1 ): ?>
        <div id="user_sitepage_members_previous" class="paginator_previous">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
            'onclick' => "paginateSitepageevents(sitepageEventsPage - 1,'$this->clicked')",
            'class' => 'buttonlink icon_previous'
          )); ?>
        </div>
      <?php endif; ?>
      <?php if( $this->paginator->getCurrentPageNumber() < $this->paginator->count() ): ?>
        <div id="user_sitepage_members_next" class="paginator_next">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
            'onclick' => "paginateSitepageevents(sitepageEventsPage + 1,'$this->clicked')",
            'class' => 'buttonlink_right icon_next'
          )); ?>
        </div>
      <?php endif; ?>
    </div>
	<?php endif; ?>
	<?php elseif($this->paginator->count() <= 0 && ($this->search != '' || $this->selectbox == 'view_count' || $this->selectbox == 'member_count' || $this->selectbox == 'creation_date' || $this->selectbox == 'starttime' || $this->selectbox == 'endtime')):?>	
		<div class="tip" id='sitepageevent_search'>
			<span>
				<?php echo $this->translate('No events were found matching your search criteria.');?>
			</span>
		</div>
	<?php else: ?>
	  <?php if(!empty($this->myeventmessage)):?>
		<div class="tip" id='sitepageevent_search'>
	  	<span>
				<?php echo $this->translate('You have not created any events in this Page yet.'); ?>
				<?php if ($this->can_create):  ?>
					<?php echo $this->translate(' %1$sCreate%2$s one now!', '<a href="'.$this->url(array('page_id' => $this->sitepage_subject->page_id, 'tab_id' => $this->identity_temp), 'sitepageevent_create').'">', '</a>'); ?>
				<?php endif; ?>
			</span>		
		</div>	
	<?php elseif(!empty($this->pastmessage) && $this->eventCount):?>
	<div class="tip" id='sitepageevent_search'>
  	<span>
			<?php echo $this->translate('No past events could be found.'); ?>
		</span>		
	</div>	
	<?php elseif(!empty($this->upcomingmessage) && $this->eventCount):?>
	<div class="tip" id='sitepageevent_search'>
  	<span>
			<?php echo $this->translate('No upcoming events could be found.'); ?>
			<?php if ($this->can_create):  ?>
					<?php echo $this->translate('%1$sCreate%2$s one now!', '<a href="'.$this->url(array('page_id' => $this->sitepage_subject->page_id, 'tab_id' => $this->identity_temp), 'sitepageevent_create').'">', '</a>'); ?>
			<?php endif; ?>
		</span>		
	</div>
	<?php else:?>
		<div class="tip" id='sitepageevent_search'>
	
			<span>
				<?php echo $this->translate('No events have been created in this Page yet.'); ?>
				<?php if ($this->can_create):  ?>
					<?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('page_id' => $this->sitepage_subject->page_id, 'tab_id' => $this->identity_temp), 'sitepageevent_create').'">', '</a>'); ?>
				<?php endif; ?>
			</span>	
		</div>	
		<?php endif; ?>	
	<?php endif; ?>
	</div>
        
        <?php // TRISTAN ADD CALENDAR ?>
        <?php if($this->isCalendar) : ?>
          <?php endScript: ?>
        <?php endif; ?>
        
	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1 ) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventwidget', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage_subject)):?>
		</div>	
	<?php endif;?>
<?php endif;?>
<?php if (empty($this->isajax)) : ?>
	</div>
<?php endif;?>

<script type="text/javascript">
	var adwithoutpackage = '<?php echo Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage_subject) ?>';
  var event_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventwidget', 3);?>';
	var execute_Request_Event = '<?php echo $this->show_content;?>';
	var show_widgets = '<?php echo $this->widgets ?>';
	var is_ajax_divhide = '<?php echo $this->isajax;?>';
	var EventtabId = '<?php echo $this->module_tabid;?>';
  var EventTabIdCurrent = '<?php echo $this->identity_temp; ?>';
  var page_communityad_integration = '<?php echo $page_communityad_integration; ?>';
	 if (EventTabIdCurrent == EventtabId) {
	 	if(page_showtitle != 0) {
	 		if($('profile_status') && show_widgets == 1) {
		    $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitepage_subject->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Events');?></h2>";	
	 		}
	 		if($('layout_event')) {
			  $('layout_event').style.display = 'block';
			}
	 	 }
     hideWidgetsForModule('sitepageevent');
	   prev_tab_id = '<?php echo $this->content_id; ?>';
	   prev_tab_class = 'layout_sitepageevent_profile_sitepageevents';  
	   execute_Request_Event = true;
	   hideLeftContainer (event_ads_display, page_communityad_integration, adwithoutpackage);	   
	 } 
	 else if (is_ajax_divhide != 1) 
	 {	  	
	   if($('global_content').getElement('.layout_sitepageevent_profile_sitepageevents')) {
				$('global_content').getElement('.layout_sitepageevent_profile_sitepageevents').style.display = 'none';
		 }		  	
	 } 
	 
	 $$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function() {
	 	 $('global_content').getElement('.layout_sitepageevent_profile_sitepageevents').style.display = 'block';
	 	 	
	 	 if(page_showtitle != 0) {
	 	 	 if($('profile_status') && show_widgets == 1) {
		     $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitepage_subject->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Events');?></h2>";	
	 	 	 }
	 	 } 	
     hideWidgetsForModule('sitepageevent');
		 $('id_' + <?php echo $this->content_id ?>).style.display = "block";
		 if ($('id_' + prev_tab_id) != null &&  prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
		 	 $$('.'+ prev_tab_class).setStyle('display', 'none');	   
		 }	
		 if (prev_tab_id != '<?php echo $this->content_id; ?>') {
			  execute_Request_Event = false;
				prev_tab_id = '<?php echo $this->content_id; ?>';
				prev_tab_class = 'layout_sitepageevent_profile_sitepageevents';  
		 }
		
		if(execute_Request_Event == false) {
			ShowContent('<?php echo $this->content_id; ?>', execute_Request_Event, '<?php echo $this->identity_temp?>', 'event', 'sitepageevent', 'profile-sitepageevents', page_showtitle,'null', event_ads_display, page_communityad_integration, adwithoutpackage);
			execute_Request_Event = true;    		
		} 
		
		if('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1);?>' && event_ads_display == 0)
		 {setLeftLayoutForPage();}
		
	});   
</script>
