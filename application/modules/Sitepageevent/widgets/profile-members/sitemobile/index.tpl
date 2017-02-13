<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @access	   John
 */
?>

<a id="sitepageevent_profile_members_anchor"></a>

<script type="text/javascript">
  var eventMemberSearch = <?php echo Zend_Json::encode($this->search) ?>;
  var eventMemberPage = Number('<?php echo $this->members->getCurrentPageNumber() ?>');
  var waiting = '<?php echo $this->waiting ?>';

  function getAjaxContent(search, page, waiting) {
		var ajax = sm4.core.request.send({
			type: "POST", 
			dataType: "html", 
			url : sm4.core.baseUrl + 'core/widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
			data: {
				'format' : 'html',
				'subject' : sm4.core.subject.guid,
				'search' : search,
        'page' : page,
        'waiting' : waiting,
        'isajax': 1
			}
		},{
			  'element' : $('#sitepageevent_profile_members_anchor').parent()
		  }
		);   
  }

  sm4.core.runonce.add(function() {
		$('#sitepageevent_members_search_input').bind('keypress', function(e) {
		if( e.which != 13) return;
			getAjaxContent(this.value, null, null);
		});
  });

  var paginateEventMembers = function(page) {
     getAjaxContent(eventMemberSearch, page, waiting);
  }
</script>

<?php if( !empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0 ): ?>
	<script type="text/javascript">
		var showWaitingMembers = function() {
			getAjaxContent(null, null, true);
		}
	</script>
<?php endif; ?>

<?php if( !$this->waiting ): ?>
  <div>
		<input id="sitepageevent_members_search_input" type="text" placeholder="<?php echo $this->translate('Search Guests');?>" role="search" data-type="search" class="ui-input-text" data-mini="true" value="<?php echo $this->search;?>">

    <div class="sm-item-members-count">
      <?php if( '' == $this->search ): ?>
        <?php echo $this->translate(array('This page event has <strong>%1$s</strong> guest.', 'This event has <strong>%1$s</strong> guests.', $this->members->getTotalItemCount()),$this->locale()->toNumber($this->members->getTotalItemCount())) ?>
      <?php else: ?>
        <?php echo sprintf($this->translate(array('This page event has %1$s guest that matched the query "%2$s".', 'This event has %1$s guests that matched the query "%2$s".', $this->members->getTotalItemCount())), $this->locale()->toNumber($this->members->getTotalItemCount()), $this->escape($this->search)) ?>
      <?php endif; ?>
    </div>
    <?php if( !empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0 ): ?>
      <div class="sm-item-members-count">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('See Waiting'), array('onclick' => 'showWaitingMembers(); return false;')) ?>
      </div>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="sm-item-members-count">
      <?php echo $this->translate(array('This page event has %s member waiting approval or waiting for a invite response.', 'This page event has %s members waiting approval or waiting for a invite response.', $this->members->getTotalItemCount()),$this->locale()->toNumber($this->members->getTotalItemCount())) ?>
  </div>
<?php endif; ?>

<?php if( $this->members->getTotalItemCount() > 0 ): ?>
  <div class="sm-content-list">
	<ul data-role="listview" data-inset="false">
    <?php foreach( $this->members as $member ):
      if( !empty($member->resource_id) ) {
        $memberInfo = $member;
        $member = $this->item('user', $memberInfo->user_id);
      } else {
        $memberInfo = $this->sitepageevent_subject->membership()->getMemberInfo($member);
      }
      ?>
			<li <?php if($this->sitepageevent_subject->isOwner($this->viewer()) && ((!$this->sitepageevent_subject->isOwner($member) && $memberInfo->active == true) || ($memberInfo->active == false && $memberInfo->resource_approved == false) || ($memberInfo->active == false && $memberInfo->resource_approved == true) )):?> data-icon="cog" <?php else:?> data-icon="arrow-r" <?php endif;?> data-inset="true" >
				<a href="<?php echo $member->getHref();?>">
					<?php echo $this->itemPhoto($member, 'thumb.icon'); ?>
					<h3><?php echo $member->getTitle() ?></h3>
					<p>
						<?php // Titles ?>
						<?php if( $this->sitepageevent_subject->getParent()->getGuid() == ($member->getGuid())): ?>
							<strong><?php echo $this->translate('(%s)', ( $memberInfo->title ? $memberInfo->title : $this->translate('owner') )) ?></strong>
						<?php endif; ?>
					</p>
	
				</a>
        <?php if($this->sitepageevent_subject->isOwner($this->viewer()) && ((!$this->sitepageevent_subject->isOwner($member) && $memberInfo->active == true) || ($memberInfo->active == false && $memberInfo->resource_approved == false) || ($memberInfo->active == false && $memberInfo->resource_approved == true) )):?>
					<a href="#manage_<?php echo $member->getGuid()?>" data-rel="popup"></a>
					<div data-role="popup" id="manage_<?php echo $member->getGuid()?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme'=>"c")); ?> >
						<ul data-role="listview" data-inset="true" data-coners="false">
							<li data-role="divider">
									<?php echo $this->translate('Options');?>
							</li>
							<?php // Remove/Promote/Demote member ?>
								<?php if ($this->sitepageevent_subject->isOwner($this->viewer())): ?>
								<?php if (!$this->sitepageevent_subject->isOwner($member) && $memberInfo->active == true): ?>
									<li data-shadow="false" data-coners="false">
										<?php
											echo $this->htmlLink(array('route' => 'sitepageevent_extended', 'controller' => 'index', 'action' => 'remove', 'event_id' => $this->sitepageevent_subject->getIdentity(), 'page_id' => $this->sitepageevent_subject->page_id, 'user_id' => $member->getIdentity()), $this->translate('Remove_Member'), array(
													'class' => 'buttonlink smoothbox icon_friend_remove'
											))
										?>
									</li>
								<?php endif; ?>
								<?php if( $memberInfo->active == false && $memberInfo->resource_approved == false ): ?>
									<li data-shadow="false" data-coners="false">
										<?php
											echo $this->htmlLink(array('route' => 'sitepageevent_extended', 'controller' => 'index', 'action' => 'approve', 'event_id' => $this->sitepageevent_subject->getIdentity(), 'page_id' => $this->sitepageevent_subject->page_id, 'user_id' => $member->getIdentity()), $this->translate('Approve_Request'), array(
												'class' => 'buttonlink smoothbox icon_sitepageevent_accept'
										))
										?>  
									</li>
									<li data-shadow="false" data-coners="false">
									<?php
										echo $this->htmlLink(array('route' => 'sitepageevent_extended', 'action' => 'reject', 'event_id' => $this->sitepageevent_subject->getIdentity(), 'page_id' => $this->sitepageevent_subject->page_id, 'user_id' => $member->getIdentity()), $this->translate('Reject_Request'), array(
											'class' => 'buttonlink smoothbox icon_sitepageevent_reject'
									))
									?>
									</li>
								<?php endif; ?>
								<?php if( $memberInfo->active == false && $memberInfo->resource_approved == true ): ?>
									<li data-shadow="false" data-coners="false">
									<?php
										echo $this->htmlLink(array('route' => 'sitepageevent_extended', 'controller' => 'index', 'action' => 'cancel', 'event_id' => $this->sitepageevent_subject->getIdentity(), 'page_id' => $this->sitepageevent_subject->page_id, 'user_id' => $member->getIdentity()), $this->translate('Cancel_Invite'), array(
                      'class' => 'buttonlink smoothbox icon_sitepageevent_cancel'
                  ))
                  ?>
									</li>
								<?php endif; ?>
							<?php endif; ?>
						</ul>
					</div>
        <?php endif; ?>
       </li>
    <?php endforeach;?>
  </ul>
  </div>
	<?php if ($this->members->count() > 1): ?>
		<?php
			echo $this->paginationAjaxControl(
						$this->members, $this->identity, 'sitepageevent_profile_members_anchor');
		?>
	<?php endif; ?>

<?php endif; ?>

<style type="text/css">


.layout_sitepageevent_profile_members > h3 {
display:none;
}

</style>