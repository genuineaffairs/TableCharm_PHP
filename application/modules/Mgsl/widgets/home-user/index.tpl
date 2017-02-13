<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<div class="home-user-widget">
  <?php echo $this->htmlLink($this->viewer()->getHref(), $this->itemPhoto($this->viewer(), 'thumb.profile')) ?>
  <?php echo $this->htmlLink(
    $this->viewer()->getHref(),
    $this->viewer()->getTitle(),
    array('class' => 'user-link visit-user-profile'))
  ?>
  <?php echo $this->htmlLink(
    $this->baseUrl('members/edit/profile'),
    $this->translate('Edit Profile'),
    array('class' => 'user-link edit-user-profile'))
  ?>
</div>