<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manage.tpl 9987 2013-03-20 00:58:10Z john $
 * @author	   John
 */
?>

<?php if( count($this->paginator) > 0 ): ?>
  <div class="circles-list">
    <?php foreach( $this->paginator as $group ): ?>
      <div class="circle">
        <div class="circle-summary">
          <div class="circle-photo">
            <?php echo $this->htmlLink($group->getHref(), $this->itemPhoto($group, 'thumb.normal')) ?>
          </div>
          <div class="circle-info">
            <div class="circle-title">
              <h3><?php echo $this->htmlLink($group->getHref(), $group->getTitle()) ?></h3>
            </div>
            <div class="circle-members">
              <?php echo $this->translate(array('%s member', '%s members', $group->membership()->getMemberCount()),$this->locale()->toNumber($group->membership()->getMemberCount())) ?>
              <?php echo $this->translate('led by');?> <?php echo $this->htmlLink($group->getOwner()->getHref(), $group->getOwner()->getTitle()) ?>
            </div>
            <div class="circle-description">
              <?php echo $this->viewMore($group->getDescription()) ?>
            </div>
          </div>
          <div class="circle-options">
            <?php if( $group->isOwner($this->viewer()) ): ?>
              <?php echo $this->htmlLink(array('route' => 'group_specific', 'action' => 'edit', 'group_id' => $group->getIdentity()), $this->translate('Edit Group'), array(
                'class' => 'buttonlink icon_group_edit'
              )) ?>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'group', 'controller' => 'group', 'action' => 'delete', 'group_id' => $group->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Group'), array(
                        'class' => 'buttonlink smoothbox icon_group_delete'
                      ));
              ?>
            <?php elseif( !$group->membership()->isMember($this->viewer(), null) ): ?>
              <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'join', 'group_id' => $group->getIdentity()), $this->translate('Join Group'), array(
                'class' => 'buttonlink smoothbox icon_group_join'
              )) ?>
            <?php elseif( $group->membership()->isMember($this->viewer(), true) && !$group->isOwner($this->viewer()) ): ?>
              <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'leave', 'group_id' => $group->getIdentity()), $this->translate('Leave Group'), array(
                'class' => 'buttonlink smoothbox icon_group_leave'
              )) ?>
            <?php endif; ?>
          </div>
        </div>
        <div class="circle-at-a-glance">
          <div class="circle-activity">
              <p style="background: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Mgsl/externals/images/flags/24/Australia.png) no-repeat; padding-left: 29px; padding-top: 3px;">User is now residing in Australia <span style="color: #AAA; float: right; text-align: right;">3 months ago</span></p>
          </div>
          <div class="circle-activity">
              <p style="background: url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Mgsl/externals/images/flags/24/Australia.png) no-repeat; padding-left: 29px; padding-top: 3px;">User is now residing in Australia <span style="color: #AAA; float: right; text-align: right;">3 months ago</span></p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php if( count($this->paginator) > 1 ): ?>
    <div>
      <?php echo $this->paginationControl($this->paginator); ?>
    </div>
  <?php endif; ?>

<?php else: ?>
  <div class="tip">
    <span>
    <?php echo $this->translate('You have not joined any groups yet.') ?>
    <?php if( $this->canCreate): ?>
      <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
        '<a href="'.$this->url(array('action' => 'create'), 'group_general').'">', '</a>') ?>
    <?php endif; ?>
    </span>
  </div>
<?php endif; ?>


<script type="text/javascript">
  $$('.core_main_group').getParent().addClass('active');
</script>