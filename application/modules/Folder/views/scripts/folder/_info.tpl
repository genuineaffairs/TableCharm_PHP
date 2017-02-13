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
<?php
$folder = $this->folder;
$total_files = count($folder);
?>

<div class="folder_edit_info">
  <ul>
    <li>
        <?php echo $this->translate('Folder: %s', $this->htmlLink($folder->getHref(), $this->radcodes()->text()->truncate($folder->getTitle(), 26)))?>
    </li>
    <li>
      <?php echo $this->translate('Type: %s', $folder->getParentTypeText())?>
    </li>
    <li>
      <?php echo $this->translate('Parent: %s', $folder->getParent()->toString()); ?>
    </li>
    <li>
      <?php echo $this->translate('Featured: %s', $this->translate($folder->featured ? 'Yes' : 'No'))?>
    </li>
    <li>
      <?php echo $this->translate('Sponsored: %s', $this->translate($folder->sponsored ? 'Yes' : 'No'))?>
    </li>
    <li><?php echo $this->translate('Created: %s', $this->timestamp($this->folder->creation_date)); ?></li>
    <li><?php echo $this->translate(array('%d file', '%d files', $total_files), $total_files); ?></li>
    <!--<li><?php echo $this->translate(array("%s comment", "%s comments", $this->folder->comment_count), $this->locale()->toNumber($this->folder->comment_count)); ?></li>-->
    <!--<li><?php echo $this->translate(array('%1$s like', '%1$s likes', $this->folder->like_count), $this->locale()->toNumber($this->folder->like_count)); ?></li>-->
    <li><?php echo $this->translate(array('%s view', '%s views', $this->folder->view_count), $this->locale()->toNumber($this->folder->view_count)); ?></li>
  </ul>
</div>
<div id="profile_options">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->dashboardNavigation)
        ->render();
    ?>
</div>    
