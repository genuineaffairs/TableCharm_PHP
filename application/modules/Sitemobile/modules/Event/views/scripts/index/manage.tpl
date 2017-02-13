<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: manage.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if (count($this->paginator) > 0): ?>
  <div class="sm-content-list">
    <ul data-role="listview" data-inset="false">
      <?php foreach ($this->paginator as $event): ?>
        <li data-icon="cog" data-inset="true">
          <a href="<?php echo $event->getHref(); ?>">
            <?php echo $this->itemPhoto($event, 'thumb.icon'); ?>
            <h3><?php echo $event->getTitle() ?></h3>
            <p>
              <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()), $this->locale()->toNumber($event->membership()->getMemberCount())) ?>   
              <?php echo $this->translate('led by') ?>
              <strong><?php echo $event->getOwner()->getTitle(); ?></strong>
            </p>
            <p><?php echo $this->locale()->toDateTime($event->starttime) ?></p>
          </a>
          <a href="#manage_<?php echo $event->getGuid() ?>" data-rel="popup"></a>
          <div data-role="popup" id="manage_<?php echo $event->getGuid() ?>" <?php echo $this->dataHtmlAttribs("popup_content", array('data-theme' => "c")); ?> data-tolerance="15"  data-overlay-theme="a" data-theme="none" aria-disabled="false" data-position-to="window">
            <div data-inset="true" style="min-width:150px;" class="sm-options-popup">
              <h3><?php echo $event->getTitle() ?></h3>
              <?php if ($this->viewer() && $event->isOwner($this->viewer())): ?>
                <?php
                echo $this->htmlLink(array('route' => 'event_specific', 'action' => 'edit', 'event_id' => $event->getIdentity()), $this->translate('Edit Event'), array(
                    'class' => 'ui-btn-default ui-btn-action'
                ))
                ?>
                <?php
                echo $this->htmlLink(array('route' => 'default', 'module' => 'event', 'controller' => 'event', 'action' => 'delete', 'event_id' => $event->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Event'), array(
                    'class' => 'smoothbox ui-btn-default ui-btn-danger'
                ));
                ?>
              <?php endif; ?>
              <?php if ($this->viewer() && !$event->membership()->isMember($this->viewer(), null)): ?>
                <?php
                echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'join', 'event_id' => $event->getIdentity()), $this->translate('Join Event'), array(
                    'class' => 'ui-btn-default ui-btn-action smoothbox'
                ))
                ?>
                <?php elseif ($this->viewer() && $event->membership()->isMember($this->viewer()) && !$event->isOwner($this->viewer())): ?>
                  <?php
                  echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'leave', 'event_id' => $event->getIdentity()), $this->translate('Leave Event'), array(
                      'class' => 'ui-btn-default ui-btn-danger smoothbox'
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
  <?php echo $this->translate('You have not joined any events yet.') ?>
  <?php if ($this->canCreate): ?>
    <?php echo $this->translate('Why don\'t you %1$screate one%2$s?', '<a href="' . $this->url(array('action' => 'create'), 'event_general') . '">', '</a>') ?>
  <?php endif; ?>
    </span>
  </div>
<?php endif; ?>