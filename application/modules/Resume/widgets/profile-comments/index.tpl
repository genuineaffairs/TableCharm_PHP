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

<div class='resume_profile_comments'>
  <a name="comments"></a>
  <?php echo $this->action("list", "comment", "core", array("type"=>"resume", "id"=>$this->resume->getIdentity())) ?>  
</div>