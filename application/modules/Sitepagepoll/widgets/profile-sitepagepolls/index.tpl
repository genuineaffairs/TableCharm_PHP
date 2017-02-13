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
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>

<?php if (!empty($this->show_content)) : ?>
	<script type="text/javascript">

		var sitepagePollSearchText = '<?php echo $this->search ?>';
		var sitepagePollPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;      
		en4.core.runonce.add(function() {
			var url = en4.core.baseUrl + 'widget/index/mod/sitepagepoll/name/profile-sitepagepolls';
			$('sitepage_polls_search_input_text').addEvent('keypress', function(e) {
				if( e.key != 'enter' ) return;
				if($('sitepage_polls_search_input_checkbox') && $('sitepage_polls_search_input_checkbox').checked == true) {
					var checkbox_value = 1;
				}
				else {
					var checkbox_value = 0;
				}
				if($('sitepagepoll_search') != null) {
					$('sitepagepoll_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagepoll/externals/images/spinner_temp.gif" /></center>'; 
				}
					en4.core.request.send(new Request.HTML({
					'url' : url,
					'data' : {
						'format' : 'html',
						'subject' : en4.core.subject.guid,
						'search' : $('sitepage_polls_search_input_text').value,
						'selectbox' : $('sitepage_polls_search_input_selectbox').value,
						'checkbox' : checkbox_value,
						'isajax' : '1',
						'tab' : '<?php echo $this->content_id ?>'
					}
				}), {
				'element' : $('id_' + <?php echo $this->content_id ?>)
				});
			});
		});
		
		
		function showsearchpollcontent () {
		 
		  var url = en4.core.baseUrl + 'widget/index/mod/sitepagepoll/name/profile-sitepagepolls';
			$('sitepage_polls_search_input_text').addEvent('keypress', function(e) {
				if( e.key != 'enter' ) return;
				if($('sitepage_polls_search_input_checkbox') && $('sitepage_polls_search_input_checkbox').checked == true) {
					var checkbox_value = 1;
				}
				else {
					var checkbox_value = 0;
				}
				if($('sitepagepoll_search') != null) {
					$('sitepagepoll_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagepoll/externals/images/spinner_temp.gif" /></center>'; 
				}
					en4.core.request.send(new Request.HTML({
					'url' : url,
					'data' : {
						'format' : 'html',
						'subject' : en4.core.subject.guid,
						'search' : $('sitepage_polls_search_input_text').value,
						'selectbox' : $('sitepage_polls_search_input_selectbox').value,
						'checkbox' : checkbox_value,
						'isajax' : '1',
						'tab' : '<?php echo $this->content_id ?>'
					}
				}), {
				'element' : $('id_' + <?php echo $this->content_id ?>)
				});
			});
		  
		}

		function Orderpollselect()
		{
			var sitepagePollSearchSelectbox = '<?php echo $this->selectbox ?>';
			var sitepagePollPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
			var url = en4.core.baseUrl + 'widget/index/mod/sitepagepoll/name/profile-sitepagepolls';
			if($('sitepage_polls_search_input_checkbox') && $('sitepage_polls_search_input_checkbox').checked == true) {
				var checkbox_value = 1;
			}
			else {
				var checkbox_value = 0;
			}
			if($('sitepagepoll_search') != null) {
				$('sitepagepoll_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagepoll/externals/images/spinner_temp.gif" /></center>'; 
			} 
			en4.core.request.send(new Request.HTML({
				'url' : url,
				'data' : {
					'format' : 'html',
					'subject' : en4.core.subject.guid,
					'search' : $('sitepage_polls_search_input_text').value,
					'selectbox' : $('sitepage_polls_search_input_selectbox').value,
					'checkbox' : checkbox_value,
					'isajax' : '1',
					'tab' : '<?php echo $this->content_id ?>'
				}
			}), {
						'element' : $('id_' + <?php echo $this->content_id ?>)
					});
		}

		function Mypoll() {
			var sitepagePollSearchCheckbox = '<?php echo $this->checkbox ?>';
			var sitepagePollPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
			var url = en4.core.baseUrl + 'widget/index/mod/sitepagepoll/name/profile-sitepagepolls';
			if($('sitepage_polls_search_input_checkbox') && $('sitepage_polls_search_input_checkbox').checked == true) {
				var checkbox_value = 1;
			}
			else {
				var checkbox_value = 0;
			}
			if($('sitepagepoll_search') != null) {
					$('sitepagepoll_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagepoll/externals/images/spinner_temp.gif" /></center>'; 
			}
					en4.core.request.send(new Request.HTML({
					'url' : url,
					'data' : {
						'format' : 'html',
						'subject' : en4.core.subject.guid,
						'search' : $('sitepage_polls_search_input_text').value,
						'selectbox' : $('sitepage_polls_search_input_selectbox').value,
						'checkbox' : checkbox_value,
						'isajax' : '1',
						'tab' : '<?php echo $this->content_id ?>'
					} 
				}), {
					'element' : $('id_' + <?php echo $this->content_id ?>)
				});
		}

		var paginateSitepagePolls = function(page) {
			var url = en4.core.baseUrl + 'widget/index/mod/sitepagepoll/name/profile-sitepagepolls';
			if($('sitepage_polls_search_input_checkbox') && $('sitepage_polls_search_input_checkbox').checked == true) {
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
					'search' : sitepagePollSearchText,
					'selectbox' : $('sitepage_polls_search_input_selectbox').value,
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
<?php endif; ?>

<?php if (empty($this->isajax)) : ?>
  <div id="id_<?php echo $this->content_id; ?>">
<?php endif;?>

<?php if (!empty($this->show_content)) : ?>
	<?php if($this->showtoptitle == 1):?>
		<div class="layout_simple_head" id="layout_poll">
			<?php echo $this->translate($this->sitepage->getTitle());?><?php echo $this->translate("'s Polls");?>
		</div>
	<?php endif;?>	
	<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adpollwidget', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)):?>
		<div class="layout_right" id="communityad_poll">
		<?php
			echo $this->content()->renderWidget("sitepage.page-ads", array('limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adpollwidget', 3), 'tab' =>'polls', 'communityadid' => 'communityad_poll', 'isajax' => $this->isajax));  
		?>
		</div>
		<div class="layout_middle">
	<?php endif;?>

	<?php if($this->can_create): ?>
		<div class="seaocore_add">
			<a href='<?php echo $this->url(array('page_id' => $this->page_id,'tab'=>$this->identity_temp), 'sitepagepoll_create', true) ?>' class='buttonlink icon_sitepagepoll_new'><?php echo $this->translate('Create a Poll');?></a>
		</div>
	<?php endif; ?>

	<?php if( $this->paginator->count() <= 0 && (empty($this->search) && empty($this->checkbox) && empty($this->selectbox))): ?>
		<div class="sitepage_list_filters" style="display:none;">
	<?php else: ?>
		<div class="sitepage_list_filters">
	<?php endif; ?>

	<?php if(!empty($this->viewer_id)):?>
		<div class="sitepage_list_filter_first">
			<?php if($this->checkbox != 1): ?>
				<input id="sitepage_polls_search_input_checkbox" type="checkbox" value="1" onclick="Mypoll();" /><?php echo $this->translate("Show my polls");?>
			<?php else: ?>
				<input id="sitepage_polls_search_input_checkbox" type="checkbox" value="2"  checked = "checked" onclick="Mypoll();" /><?php echo $this->translate("Show my polls"); ?>
			<?php endif; ?>
		</div>
	<?php endif;?>
	
	<div class="sitepage_list_filter_field">
		<?php echo $this->translate("Search:");?>
		<input id="sitepage_polls_search_input_text" type="text" value="<?php echo $this->search; ?>" />
  </div>

	<div class="sitepage_list_filter_field">
	  <?php echo $this->translate('Browse by:');?>
	
		<select name="default_visibility" id="sitepage_polls_search_input_selectbox" onchange = "Orderpollselect()">
			<?php if($this->selectbox == 'creation_date'): ?>
				<option value="creation_date" selected='selected'><?php echo $this->translate("Most Recent"); ?></option>
			<?php else:?>
				<option value="creation_date"><?php echo $this->translate("Most Recent"); ?></option>
			<?php endif;?>
			<?php if($this->selectbox == 'like_count'): ?>
				<option value="like_count" selected='selected'><?php echo $this->translate("Most Liked"); ?></option>
			<?php else:?>
				<option value="like_count"><?php echo $this->translate("Most Liked"); ?></option>
			<?php endif;?>
			<?php if($this->selectbox == 'comment_count'): ?>
				<option value="comment_count" selected='selected'><?php echo $this->translate("Most Commented"); ?></option>
			<?php else:?>
				<option value="comment_count"><?php echo $this->translate("Most Commented"); ?></option>
			<?php endif;?>
			<?php if($this->selectbox == 'vote_count'): ?>
				<option value="vote_count" selected='selected'><?php echo $this->translate("Most Voted"); ?></option>
			<?php else:?>
				<option value="vote_count"><?php echo $this->translate("Most Voted"); ?></option>
			<?php endif;?>
			<?php if($this->selectbox == 'views'): ?>
				<option value="views" selected='selected'><?php echo $this->translate("Most Viewed"); ?></option>
			<?php else:?>
				<option value="views"><?php echo $this->translate("Most Viewed"); ?></option>
			<?php endif;?>
		</select>
	</div>
</div>

<div id='sitepagepoll_search'>
  <?php if( count($this->paginator) > 0 ): ?>
    <ul class="sitepage_profile_list" >
      <?php foreach ($this->paginator as $sitepagepoll): ?>
				<?php if($sitepagepoll->owner_id != $this->viewer_id): ?>
					<li id="sitepagepoll-item-<?php echo $sitepagepoll->poll_id ?>">
				<?php else: ?>
					<li id="sitepagepoll-item-<?php echo $sitepagepoll->poll_id ?>" class="sitepage_profile_list_owner">
				<?php endif; ?>
        <?php echo $this->htmlLink(
          $sitepagepoll->getHref(),
          $this->itemPhoto($sitepagepoll->getOwner(), 'thumb.icon', $sitepagepoll->getOwner()->getTitle()),
          array('title' => $sitepagepoll->title)
        ) ?>
				<div class="sitepage_profile_list_options">
           <?php echo $this->htmlLink(array('route' => 'sitepagepoll_detail_view', 'user_id' => $sitepagepoll->owner_id, 'poll_id' => $sitepagepoll->poll_id,'slug'=> $sitepagepoll->getSlug(),'tab'=>$this->identity_temp), $this->translate('View Poll'), array(
				'class'=>'buttonlink icon_sitepagepoll_viewall')) ?>
          <?php if($sitepagepoll->owner_id == $this->viewer_id || $this->can_edit == 1): ?>
						<?php echo $this->htmlLink(array('route' => 'sitepagepoll_delete', 'poll_id' => $sitepagepoll->poll_id, 'page_id' => $sitepagepoll->page_id ,'tab'=>$this->identity_temp), $this->translate('Delete Poll'), array(
				    'class'=>'buttonlink icon_sitepagepoll_delete')) ?>
            <?php if( !$sitepagepoll->closed ): ?>
							<?php echo $this->htmlLink(array(
								'route' => 'sitepagepoll_specific',
								'poll_id' => $sitepagepoll->poll_id,
								'page_id' => $sitepagepoll->page_id,
								'tab'=>$this->identity_temp,
								'closed' => 1,
							), $this->translate('Close Poll'), array(
								'class' => 'buttonlink icon_sitepagepoll_close'
							)) ?>
						<?php else: ?>
							<?php echo $this->htmlLink(array(
								'route' => 'sitepagepoll_specific',
								'poll_id' => $sitepagepoll->poll_id,
								'page_id' => $sitepagepoll->page_id,
                'tab'=>$this->identity_temp,
								'closed' => 0,
							), $this->translate('Open Poll'), array(
								'class' => 'buttonlink icon_sitepagepoll_open'
							)) ?>
						<?php endif; ?>
					<?php endif; ?>
				</div>
        <div class="sitepage_profile_list_info">
          <div class="sitepage_profile_list_title">
						<span >
							<?php if($sitepagepoll->approved != 1): ?>
								<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepagepoll/externals/images/sitepagepoll_approved0.gif', '', array('class' => 'icon', 'title' => $this->translate('Not Approved'))) ?>
							<?php endif;?>
						</span>
            <?php echo $this->htmlLink(array('route' => 'sitepagepoll_detail_view', 'user_id' => $sitepagepoll->owner_id, 'poll_id' => $sitepagepoll->poll_id,'slug'=> $sitepagepoll->getSlug(),'tab'=>$this->identity_temp), $sitepagepoll->title) ?>
           <?php if( $sitepagepoll->closed ): ?>
						<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/close.png' alt="<?php echo $this->translate('Closed') ?>" />
					<?php endif ?><br/>
          </div>

          <div class="sitepage_profile_list_info_date">
            <?php echo $this->translate('Created by %s', $this->htmlLink($sitepagepoll->getOwner(), $sitepagepoll->getOwner()->getTitle())) ?>
            <?php echo $this->timestamp($sitepagepoll->creation_date) ?>
            -
            <?php echo $this->translate(array('%s vote', '%s votes', $sitepagepoll->vote_count), $this->locale()->toNumber($sitepagepoll->vote_count)) ?>
            -
            <?php echo $this->translate(array('%s view', '%s views', $sitepagepoll->views), $this->locale()->toNumber($sitepagepoll->views)) ?>
        
						-
              <?php echo $this->translate(array('%s like', '%s likes', $sitepagepoll->like_count), $this->locale()->toNumber($sitepagepoll->like_count)) ?>
        
						-
            <?php echo $this->translate(array('%s comment', '%s comments', $sitepagepoll->comment_count), $this->locale()->toNumber($sitepagepoll->comment_count)) ?>
          </div>
          <?php if (!empty($sitepagepoll->description)): ?>
            <div class="sitepage_profile_list_info_des">
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
		<?php if( $this->paginator->count() > 1 ): ?>
	    <div>
	      <?php if( $this->paginator->getCurrentPageNumber() > 1 ): ?>
	        <div id="user_sitepage_members_previous" class="paginator_previous">
	          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
	            'onclick' => 'paginateSitepagePolls(sitepagePollPage - 1)',
	            'class' => 'buttonlink icon_previous'
	          )); ?>
	        </div>
	      <?php endif; ?>
	      <?php if( $this->paginator->getCurrentPageNumber() < $this->paginator->count() ): ?>
	        <div id="user_sitepage_members_next" class="paginator_next">
	          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
	            'onclick' => 'paginateSitepagePolls(sitepagePollPage + 1)',
	            'class' => 'buttonlink_right icon_next'
	          )); ?>
	        </div>
	      <?php endif; ?>
	    </div>
  	<?php endif; ?>
  	
    <?php elseif($this->paginator->count() <= 0 && ($this->search != '' || $this->checkbox == 1 || $this->selectbox == 'views' ||  $this->selectbox == 'comment_count' || $this->selectbox == 'like_count' || $this->selectbox == 'vote_count' || $this->selectbox == 'creation_date')):?>	
	  <div class="tip" id='sitepagepoll_search'>
		  <span>
			  <?php echo $this->translate('No polls were found matching your search criteria.');?>
		  </span>
	</div>
<?php else: ?>
  <div class="tip" id='sitepagepoll_search'>
		<span>
			<?php echo $this->translate('No polls have been created in this Page yet.'); ?>
			<?php if ($this->can_create):  ?>
				<?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('page_id' => $this->page_id,'tab'=>$this->identity_temp), 'sitepagepoll_create').'">', '</a>'); ?>
			<?php endif; ?>
		</span>
	</div>	
  <?php endif;?>
</div>

<?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adpollwidget', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)):?>
	</div>
<?php endif; ?>
<?php endif;?>

<?php if (empty($this->isajax)) : ?>
	</div>
<?php endif;?>

<script type="text/javascript">
  var adwithoutpackage = '<?php echo Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage) ?>';
  var poll_ads_display = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adpollwidget', 3);?>';
	var is_ajax_divhide = '<?php echo $this->isajax;?>';
  var execute_Request_Poll = '<?php echo $this->show_content;?>';
  //window.addEvent('domready', function () {
 	var show_widgets = '<?php echo $this->widgets ?>';
  var PolltabId = '<?php echo $this->module_tabid;?>';
  var PollTabIdCurrent = '<?php echo $this->identity_temp; ?>';
  var page_communityad_integration = '<?php echo $page_communityad_integration; ?>';
  if (PollTabIdCurrent == PolltabId) {
  	if(page_showtitle != 0) {
  		if($('profile_status') && show_widgets == 1) {
			  $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitepage->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Polls');?></h2>";	
  		}
  		if($('layout_poll')) {
			  $('layout_poll').style.display = 'block';
			}  		
  	}    	
    hideWidgetsForModule('sitepagepoll');
		prev_tab_id = '<?php echo $this->content_id; ?>'; 
		prev_tab_class = 'layout_sitepagepoll_profile_sitepagepolls';		
		execute_Request_Poll = true;
		hideLeftContainer (poll_ads_display, page_communityad_integration, adwithoutpackage);
  } 
	else if (is_ajax_divhide != 1) {  	
  	if($('global_content').getElement('.layout_sitepagepoll_profile_sitepagepolls')) {
			$('global_content').getElement('.layout_sitepagepoll_profile_sitepagepolls').style.display = 'none';
	  } 	
	 }
  // });

	$$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function() {
		$('global_content').getElement('.layout_sitepagepoll_profile_sitepagepolls').style.display = 'block';
		if(page_showtitle != 0) {
			if($('profile_status') && show_widgets == 1) {
			  $('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitepage->getTitle())?><?php echo $this->translate(' &raquo; ');?><?php echo $this->translate('Polls');?></h2>";	
			}
		}
    hideWidgetsForModule('sitepagepoll');
		$('id_' + <?php echo $this->content_id ?>).style.display = "block";
    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
      $$('.'+ prev_tab_class).setStyle('display', 'none');
    }
		if (prev_tab_id != '<?php echo $this->content_id; ?>') {
			execute_Request_Poll = false;
			prev_tab_id = '<?php echo $this->content_id; ?>';
			prev_tab_class = 'layout_sitepagepoll_profile_sitepagepolls';
		}
		if(execute_Request_Poll == false) {
			ShowContent('<?php echo $this->content_id; ?>', execute_Request_Poll, '<?php echo $this->identity_temp?>', 'poll', 'sitepagepoll', 'profile-sitepagepolls', page_showtitle, 'null', poll_ads_display, page_communityad_integration, adwithoutpackage);
			execute_Request_Poll = true;    		
		}   		
		if('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1);?>' && poll_ads_display == 0)
{setLeftLayoutForPage();} 
	});
 
</script>