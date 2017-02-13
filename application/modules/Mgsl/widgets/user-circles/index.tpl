<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author		 John
 */
?>
<div class="quick-links">
  <h3><?php echo $this->translate('Circles') ?></h3>
  <ul class="user-circles">
    <?php foreach( $this->paginator as $group ): ?>
      <li>
        <div class="quick-link-icon">
          <?php echo $this->htmlLink($group, $this->itemPhoto($group, 'thumb.normal')) ?>
        </div>
        <div class="quick-link-title">
          <?php echo $this->htmlLink($group->getHref(), $group->getTitle()) ?>
        </div>
      </li>
    <?php endforeach; ?>
      <li>
        <div class="quick-link-icon">
          <?php echo $this->htmlLink(array('route' => 'group_general', 'module' => 'group', 'controller' => 'index', 'action' => 'create'), '<img src="'. 'application/themes/mgsl/images/circles/circles_new.png'.'" width="20" height="20" />', array('class'=>'thumb_normal item_photo_group item_nophoto')) ?>
        </div>
        <div class="quick-link-title">
          <?php echo $this->htmlLink(array('route' => 'group_general', 'module' => 'group', 'controller' => 'index', 'action' => 'create'), $this->translate('Create Group').'...') ?>
        </div>
      </li>
  </ul>
</div>