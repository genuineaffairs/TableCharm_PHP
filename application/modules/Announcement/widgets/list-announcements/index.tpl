<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Announcements
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */
?>

<ul class="announcements">
  <?php foreach( $this->announcements as $item ): ?>
    <li>
      <div class="announcements_title">
        <?php echo $item->title ?>
      </div>
      <div class="announcements_info">
        <span class="announcements_author">
          <?php /* echo $this->translate('Posted by %1$s %2$s',
                        $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()),
                        $this->timestamp($item->modified_date)) */ ?>
          <?php echo $this->translate('%1$s', $this->timestamp($item->modified_date)) ?>
        </span>
      </div>
      <div class="announcements_body">
        <?php echo $item->body ?>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
