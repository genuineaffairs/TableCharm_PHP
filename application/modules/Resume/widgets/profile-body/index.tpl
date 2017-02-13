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
<div class="resume_profile_screen_container<?php echo $this->is_app ? ' zulu_app' : ''; ?>">
  <?php 
    if($this->is_app) {
      echo $this->partial('profile/_appBody.tpl', 'resume', array('resume' => $this->resume, 'is_app' => $this->is_app));
    } else {
      echo $this->partial('profile/_body.tpl', 'resume', array('resume' => $this->resume));
    }
  ?>
</div>