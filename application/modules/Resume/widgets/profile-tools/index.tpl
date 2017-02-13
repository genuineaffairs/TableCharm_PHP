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
<?php $resume = $this->resume; ?>
<div class='resume_profile_tools'>
  <?php echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'resume', 'id' => $this->resume->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'buttonlink icon_resume_share smoothbox')); ?>
  <?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' =>  $this->resume->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'buttonlink icon_resume_report smoothbox')); ?>
</div>