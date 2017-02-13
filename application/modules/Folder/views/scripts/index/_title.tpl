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
<h3>
  <?php 
    $folder_title = $this->folder->getTitle();
    if ($this->max_title_length) {
      $folder_title = $this->radcodes()->text()->truncate($folder_title, $this->max_title_length);
    }
  ?>
  <?php echo $this->htmlLink($this->folder->getHref(), $folder_title); ?>
</h3>