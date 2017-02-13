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

<div class="headline">
  <h2>
    <?php echo $this->translate('Folders');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<div class='layout_right'>
  <div class="folder_edit_gutter"> 
    <?php echo $this->partial('/folder/_info.tpl', 'folder', array('folder' => $this->folder, 'dashboardNavigation' => $this->dashboardNavigation));?>
  </div>   
</div>
<div class='layout_middle'>
  <?php echo $this->form->render($this) ?>
</div>
