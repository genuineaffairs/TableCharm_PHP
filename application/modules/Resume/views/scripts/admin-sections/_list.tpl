<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @section   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>
<div class="radcodes_sections_lists">
<?php if (count($this->sections) > 0): ?>
	<ul class="radcodes_sections_list" id="admin_section_parent_0">
	<?php foreach ($this->sections as $section): ?>
	  <li id="admin_section_item_<?php echo $section->section_id; ?>">
	    <?php echo $this->partial('admin-sections/_list_item_section.tpl', array('section'=>$section)); ?>
	  </li>
	<?php endforeach; ?>
	</ul>

<?php else: ?>
  <br/>
  <div class="tip">
    <span><?php echo $this->translate("There are currently no sections.") ?></span>
  </div>
<?php endif; ?>
</div>