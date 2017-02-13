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
<h3 class="resume_title_featured_<?php echo $this->resume->featured ? 'yes' : 'no'?> resume_title_sponsored_<?php echo $this->resume->sponsored ? 'yes' : 'no'?>">
  <?php 
    $resume_title = $this->resume->getOwner()->getTitle();
    if ($this->max_title_length) {
      $resume_title = $this->radcodes()->text()->truncate($resume_title, $this->max_title_length);
    }
  ?>
  <?php echo $this->htmlLink($this->resume->getHref(), $resume_title); ?>
</h3>