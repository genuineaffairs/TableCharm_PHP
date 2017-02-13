<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>
<ul>
  <?php if ($this->show_owner !== false): ?>
    <li class="resume_meta_owner"><?php echo $this->translate('by %s', $this->resume->getOwner()->toString()); ?></li>
  <?php endif; ?>
  <?php if ($this->show_date !== false): ?>
    <li class="resume_meta_date"><?php echo $this->timestamp($this->resume->creation_date); ?></li>
  <?php endif; ?>
  <?php if ($this->show_comments !== false): ?>
    <!--<li class="resume_meta_comments"><?php echo $this->translate(array("%s comment", "%s comments", $this->resume->comment_count), $this->locale()->toNumber($this->resume->comment_count)); ?></li>-->
  <?php endif; ?>
  <?php if ($this->show_likes !== false): ?>
    <!--<li class="resume_meta_likes"><?php echo $this->translate(array('%1$s like', '%1$s likes', $this->resume->like_count), $this->locale()->toNumber($this->resume->like_count)); ?></li>-->
  <?php endif; ?>
  <?php if ($this->show_views !== false): ?>
    <li class="resume_meta_views"><?php echo $this->translate(array('%s view', '%s views', $this->resume->view_count), $this->locale()->toNumber($this->resume->view_count)); ?></li>
  <?php endif; ?>
</ul>