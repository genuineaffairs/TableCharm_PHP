<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<a id="sitepageevent_profile_members_anchor" style="position:absolute;"></a>

<script type="text/javascript">
  var sitepageeventMemberSearch = '<?php echo $this->search ?>';
  var sitepageeventMemberPage = '<?php echo $this->members->getCurrentPageNumber() ?>';
  en4.core.runonce.add(function() {
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    $('sitepageevent_members_search_input').addEvent('keypress', function(e) {
      if( e.key != 'enter' ) return;

      en4.core.request.send(new Request.HTML({
        'url' : url,
        'data' : {
          'format' : 'html',
          'subject' : en4.core.subject.guid,
          'search' : this.value,
          'is_ajax' : 1
        }
      }), {
        'element' : $('sitepageevent_profile_members_anchor').getParent()
      });
    });
  });

  var paginateEventMembers = function(page) {
    //var url = '<?php echo $this->url(array('module' => 'sitepageevent', 'controller' => 'widget', 'action' => 'profile-members', 'subject' => $this->subject()->getGuid(), 'format' => 'html'), 'default', true) ?>';
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'subject' : en4.core.subject.guid,
        'search' : sitepageeventMemberSearch,
        'page' : page,
				'is_ajax' : 1
      }
    }), {
      'element' : $('sitepageevent_profile_members_anchor').getParent()
    });
  }
</script>

<?php if (!empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0): ?>
  <script type="text/javascript">
    var showWaitingMembers = function() {
      //var url = '<?php echo $this->url(array('module' => 'sitepageevent', 'controller' => 'widget', 'action' => 'profile-members', 'subject' => $this->subject()->getGuid(), 'format' => 'html'), 'default', true) ?>';
      var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
      en4.core.request.send(new Request.HTML({
        'url' : url,
        'data' : {
          'format'  : 'html',
          'subject' : en4.core.subject.guid,
          'waiting' : true,
					'is_ajax' : 1
        }
      }), {
        'element' : $('sitepageevent_profile_members_anchor').getParent()
      });
    }
  </script>
<?php endif; ?>

<?php if (!$this->waiting): ?>
  <div class="sitepageevent_members_info">
    <div class="sitepageevent_members_search">
      <input id="sitepageevent_members_search_input" type="text" value="<?php echo $this->translate('Search Guests'); ?>" onfocus="$(this).store('over', this.value);this.value = '';" onblur="this.value = $(this).retrieve('over');">
    </div>
    <div class="sitepageevent_members_total">
      <?php if ('' == $this->search): ?>
        <?php echo $this->translate(array('This Page event has %1$s guest.', 'This page event has %1$s guests.', $this->members->getTotalItemCount()), $this->locale()->toNumber($this->members->getTotalItemCount())) ?>
      <?php else: ?>
        <?php echo sprintf($this->translate(array('This page event has %1$s guest that matched the query "%2$s".', 'This event has %1$s guests that matched the query "%2$s".', $this->members->getTotalItemCount())), $this->locale()->toNumber($this->members->getTotalItemCount()), $this->escape($this->search)) ?>
      <?php endif; ?>
    </div>
    <?php if (!empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0): ?>
      <div class="sitepageevent_members_total">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('See Waiting'), array('onclick' => 'showWaitingMembers(); return false;')) ?>
      </div>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="sitepageevent_members_info">
    <div class="sitepageevent_members_total">
      <?php echo $this->translate(array('This Page event has %s member waiting approval or waiting for a invite response.', 'This page event has %s members waiting approval or waiting for a invite response.', $this->members->getTotalItemCount()), $this->locale()->toNumber($this->members->getTotalItemCount())) ?>
    </div>
  </div>
<?php endif; ?>

<?php if ($this->members->getTotalItemCount() > 0): ?>
  <ul class='sitepageevent_members'>
    <?php
    foreach ($this->members as $member):
      if (!empty($member->resource_id)) {
        $memberInfo = $member;
        $member = $this->item('user', $memberInfo->user_id);
      } else {
        $memberInfo = $this->sitepageevent_subject->membership()->getMemberInfo($member);
      }
      ?>
      <li id="sitepageevent_member_<?php echo $member->getIdentity() ?>">
        <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'), array('class' => 'sitepageevent_members_icon')) ?>
        <div class='sitepageevent_members_options'>
          <?php // Add/Remove Friend ?>
          <?php if ($this->viewer()->getIdentity() && !$this->viewer()->isSelf($member)): ?>
            <?php if (!$this->viewer()->membership()->isMember($member)): ?>
              <?php
              echo $this->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'add', 'user_id' => $member->getIdentity()), $this->translate('Add Friend'), array(
                  'class' => 'buttonlink smoothbox icon_friend_add'
              ))
              ?>
            <?php else: ?>
              <?php
              echo $this->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'remove', 'user_id' => $member->getIdentity()), $this->translate('Remove Friend'), array(
                  'class' => 'buttonlink smoothbox icon_friend_remove'
              ))
              ?>
            <?php endif; ?>
          <?php endif; ?>
          <?php // Remove/Promote/Demote member ?>
          <?php if ($this->sitepageevent_subject->isOwner($this->viewer())): ?>

            <?php if (!$this->sitepageevent_subject->isOwner($member) && $memberInfo->active == true): ?>
              <?php
              echo $this->htmlLink(array('route' => 'sitepageevent_extended', 'controller' => 'index', 'action' => 'remove', 'event_id' => $this->sitepageevent_subject->getIdentity(), 'page_id' => $this->sitepageevent_subject->page_id, 'user_id' => $member->getIdentity()), $this->translate('Remove_Member'), array(
                  'class' => 'buttonlink smoothbox icon_friend_remove'
              ))
              ?>
            <?php endif; ?>
            <?php if ($memberInfo->active == false && $memberInfo->resource_approved == false): ?>
              <?php
              echo $this->htmlLink(array('route' => 'sitepageevent_extended', 'controller' => 'index', 'action' => 'approve', 'event_id' => $this->sitepageevent_subject->getIdentity(), 'page_id' => $this->sitepageevent_subject->page_id, 'user_id' => $member->getIdentity()), $this->translate('Approve_Request'), array(
                  'class' => 'buttonlink smoothbox icon_sitepageevent_accept'
              ))
              ?>              
              <?php
              echo $this->htmlLink(array('route' => 'sitepageevent_extended', 'action' => 'reject', 'event_id' => $this->sitepageevent_subject->getIdentity(), 'page_id' => $this->sitepageevent_subject->page_id, 'user_id' => $member->getIdentity()), $this->translate('Reject_Request'), array(
                  'class' => 'buttonlink smoothbox icon_sitepageevent_reject'
              ))
              ?>
                <?php endif; ?>
                <?php if ($memberInfo->active == false && $memberInfo->resource_approved == true): ?>
                  <?php
                  echo $this->htmlLink(array('route' => 'sitepageevent_extended', 'controller' => 'index', 'action' => 'cancel', 'event_id' => $this->sitepageevent_subject->getIdentity(), 'page_id' => $this->sitepageevent_subject->page_id, 'user_id' => $member->getIdentity()), $this->translate('Cancel_Invite'), array(
                      'class' => 'buttonlink smoothbox icon_sitepageevent_cancel'
                  ))
                  ?>
      <?php endif; ?>
              <?php endif; ?>
        </div>
        <div class='sitepageevent_members_body'>
          <div>
            <span class='sitepageevent_members_status'>
            <?php echo $this->htmlLink(array('route' => 'user_profile', 'id' => $member->user_id), $member->getTitle()) ?>

            <?php // Titles ?>
            <?php if ($this->sitepageevent_subject->getParent()->getGuid() == ($member->getGuid())): ?>
                <?php echo $this->translate('(%s)', ( $memberInfo->title ? $memberInfo->title : $this->translate('owner') )) ?>
            <?php endif; ?>
            </span>
            <span>
            <?php echo $member->status; ?>
            </span>
          </div>
          <div class="sitepageevent_members_rsvp">
      <?php if ($memberInfo->rsvp == 0): ?>
      <?php echo $this->translate('Not Attending') ?>
    <?php elseif ($memberInfo->rsvp == 1): ?>
        <?php echo $this->translate('Maybe Attending') ?>
      <?php elseif ($memberInfo->rsvp == 2): ?>
          <?php echo $this->translate('Attending') ?>
        <?php else: ?>
          <?php echo $this->translate('Awaiting Reply') ?>
        <?php endif; ?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
      <?php if ($this->members->count() > 1): ?>
    <div>
        <?php if ($this->members->getCurrentPageNumber() > 1): ?>
        <div id="user_sitepageevent_members_previous" class="paginator_previous">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
              'onclick' => 'paginateEventMembers(sitepageeventMemberPage - 1)',
              'class' => 'buttonlink icon_previous',
          ));
          ?>
        </div>
    <?php endif; ?>
    <?php if ($this->members->getCurrentPageNumber() < $this->members->count()): ?>
        <div id="user_sitepageevent_members_next" class="paginator_next">
      <?php
      echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
          'onclick' => 'paginateEventMembers(sitepageeventMemberPage + 1)',
          'class' => 'buttonlink icon_next'
      ));
      ?>
        </div>
    <?php endif; ?>
    </div>
  <?php endif; ?>
<?php endif; ?>

<style type="text/css">

.layout_sitepageevent_profile_members h3{
	display: none;
}

</style>