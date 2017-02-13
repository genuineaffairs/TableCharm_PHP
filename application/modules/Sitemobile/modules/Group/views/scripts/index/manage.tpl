<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manage.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if (count($this->paginator) > 0): ?>
  <div class="sm-content-list">
    <ul data-role="listview" data-inset="false">
      <?php foreach ($this->paginator as $group): ?>
        <li data-icon="cog" data-inset="true">
          <a href="<?php echo $group->getHref(); ?>">
            <?php echo $this->itemPhoto($group, 'thumb.icon'); ?>
            <h3><?php echo $group->getTitle() ?></h3>
            <p>
              <?php echo $this->translate(array('%s guest', '%s guests', $group->membership()->getMemberCount()), $this->locale()->toNumber($group->membership()->getMemberCount())) ?>
              <?php echo $this->translate('led by') ?>
              <strong><?php echo $group->getOwner()->getTitle(); ?></strong>
            </p>
          </a>
          <a href="#manage_<?php echo $group->getGuid() ?>"  data-rel="popup"></a>
          <div data-role="popup" id="manage_<?php echo $group->getGuid() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window" >
            <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
              <h3><?php echo $group->getTitle() ?></h3>
              <?php if ($this->viewer() && $group->isOwner($this->viewer())): ?>
                <?php
                echo $this->htmlLink(array('route' => 'group_specific', 'action' => 'edit', 'group_id' => $group->getIdentity()), $this->translate('Edit Group'), array(
                    'class' => 'ui-btn-default ui-btn-action'
                ))
                ?>
                <?php
                echo $this->htmlLink(array('route' => 'default', 'module' => 'group', 'controller' => 'group', 'action' => 'delete', 'group_id' => $group->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Group'), array(
                    'class' => 'smoothbox ui-btn-default ui-btn-danger'
                ));
                ?>
              <?php endif; ?>
              <?php if ($this->viewer() && !$group->membership()->isMember($this->viewer(), null)): ?>
                <?php
                echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'join', 'group_id' => $group->getIdentity()), $this->translate('Join Group'), array(
                    'class' => 'smoothbox ui-btn-default ui-btn-action'
                ))
                ?>   
                <?php elseif ($this->viewer() && $group->membership()->isMember($this->viewer()) && !$group->isOwner($this->viewer())): ?>
                  <?php
                  echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'leave', 'group_id' => $group->getIdentity()), $this->translate('Leave Group'), array(
                      'class' => 'smoothbox ui-btn-default ui-btn-danger'
                  ))
                  ?>
        <?php endif; ?>
              <a href="#" data-rel="back" class="ui-btn-default">
      <?php echo $this->translate('Cancel'); ?>
              </a>
            </div> 
          </div>
        </li>
  <?php endforeach; ?>
    </ul>
  <?php if ($this->paginator->count() > 1): ?>
        <?php
        echo $this->paginationControl($this->paginator, null, null, array(
            'query' => array('view' => $this->view, 'text' => $this->text)
        ));
        ?>
      <?php endif; ?>
  </div>		
<?php else: ?>
  <div class="tip">
    <span>
  <?php echo $this->translate('You have not joined any groups yet.') ?>
  <?php if ($this->canCreate): ?>
    <?php echo $this->translate('Why don\'t you %1$screate one%2$s?', '<a href="' . $this->url(array('action' => 'create'), 'group_general') . '">', '</a>') ?>
  <?php endif; ?>
    </span>
  </div>
<?php endif; ?>