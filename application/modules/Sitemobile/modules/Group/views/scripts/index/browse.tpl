<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 9800 2012-10-17 01:16:09Z richard $
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
    <ul data-role="listview" data-icon="false">
      <?php foreach ($this->paginator as $group): ?>
        <li class="sm-ui-browse-items" data-icon="arrow-r">
          <a href="<?php echo $group->getHref(); ?>">
            <?php echo $this->itemPhoto($group, 'thumb.icon'); ?>
            <h3><?php echo $group->getTitle() ?></h3>
            <p><?php echo $this->translate(array('%s member', '%s members', $group->membership()->getMemberCount()), $this->locale()->toNumber($group->membership()->getMemberCount())) ?>
              <?php echo $this->translate('led by') ?>
              <strong><?php echo $group->getOwner()->getTitle(); ?></strong>
            </p>
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
      <?php echo $this->translate('Nobody has created a group with that criteria.'); ?>
      <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Why don\'t you %1$screate one%2$s?', '<a href="' . $this->url(array('action' => 'create'), 'group_general') . '">', '</a>') ?>
      <?php endif; ?>
    </span>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no groups yet.') ?>
      <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Why don\'t you %1$screate one%2$s?', '<a href="' . $this->url(array('action' => 'create'), 'group_general') . '">', '</a>') ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>