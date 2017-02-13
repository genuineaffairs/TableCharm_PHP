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

<div class="folder_file_folder_link">
  <?php echo $this->htmlLink($this->attachment->getFolder()->getHref(), $this->translate('Back to Folder'), array('class'=>'buttonlink')); ?>
</div>