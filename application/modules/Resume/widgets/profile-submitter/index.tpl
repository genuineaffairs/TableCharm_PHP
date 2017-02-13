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

<div class='resume_profile_submitter'>
  <?php echo $this->htmlLink($this->owner->getHref(),
    $this->itemPhoto($this->owner, 'thumb.icon'),
    array('class' => 'resume_profile_submitter_photo')
  )?>
  <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle(), array('class' => 'resume_profile_submitter_user', 'href' => 'javascript:void(0)')) ?>
  <span><?php echo $this->translate(array('%s resume entry','%s resume entries', $this->totalResumes), $this->totalResumes); ?></span>
</div>