<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>
<?php $folder = $this->folder; ?>
<div class='folder_profile_tools'>
  <?php echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'folder', 'id' => $this->folder->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'buttonlink icon_folder_share smoothbox')); ?>
  <?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' =>  $this->folder->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'buttonlink icon_folder_report smoothbox')); ?>
</div>