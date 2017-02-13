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
<?php if ($this->display_style == 'wide'): ?>
  <div class="folders_categories_links">
    <ul>
    <?php foreach ($this->categories as $category): ?>
      <li>
        <?php if ($this->showphoto): ?>
          <div class="folder_category_photo">
            <?php echo $this->htmlLink($category->getHref(), $this->itemPhoto($category, 'thumb.icon')); ?>
          </div>
        <?php endif; ?>
        <div class="folder_category_info">
          <?php echo $this->htmlLink($category->getHref(), $this->translate($category->getTitle()), array('class' => 'folder_category_title'))?>
          <?php if ($this->showdescription && $category->getDescription()): ?>
            <div class="folder_category_desc">
              <?php echo $this->viewMore($category->getDescription()); ?>
            </div>
          <?php endif; ?>
        </div>
      </li>
    <?php endforeach; ?>
    </ul>
  </div>
<?php else: ?>
  <div class="quicklinks folders_categories_quicklinks">
    <ul>
    <?php foreach ($this->categories as $category): ?>
      <li>
        <?php 
          $attrs = array();
          if ($this->showphoto) {
            $attrs['class'] = 'buttonlink icon_folder_category';
            if ($category->photo_id) {
              $attrs['style'] = "background-image: url(".$category->getPhotoUrl('thumb.mini').");";
            }
          }
          echo $this->htmlLink($category->getHref(), $this->translate($category->getTitle()), $attrs
        );?>
        <?php if ($this->showdescription && $category->getDescription()): ?>
          <div class="folder_category_desc">
            <?php echo $this->viewMore($category->getDescription()); ?>
          </div>
        <?php endif; ?>        
      </li>
    <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>