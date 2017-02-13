<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9800 2012-10-17 01:16:09Z richard $
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
    <ul data-role="listview" data-icon="arrow-r">
      <?php foreach ($this->paginator as $event): ?>
        <li class="sm-ui-browse-items">
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
        </li>
      <?php endforeach; ?>
    </ul>
    <?php if ($this->paginator->count() > 1): ?>
      <?php
      echo $this->paginationControl($this->paginator, null, null, array(
          'query' => $this->formValues,
      ));
      ?>
  <?php endif; ?>
  </div>	
<?php elseif (preg_match("/category_id=/", $_SERVER['REQUEST_URI'])): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created an event with that criteria.'); ?>
      <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'create'), 'event_general') . '">', '</a>'); ?>
  <?php endif; ?>
    </span>
  </div>   
<?php else: ?>
  <div class="tip">
    <span>
      <?php if ($this->filter != "past"): ?>
        <?php echo $this->translate('Nobody has created an event yet.') ?>
        <?php if ($this->canCreate): ?>
          <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'create'), 'event_general') . '">', '</a>'); ?>
        <?php endif; ?>
      <?php else: ?>
        <?php echo $this->translate('There are no past events yet.') ?>
  <?php endif; ?>
    </span>
  </div>
<?php endif; ?>