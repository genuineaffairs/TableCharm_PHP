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
<?php $section = $this->section; ?>
  <div class="radcodes_section_item">
    <img src="application/modules/Core/externals/images/admin/sortable.png" width="16" height="16" class='move-me' /> 
    <span class="section_name">
      <?php echo $section->getTitle(); ?>
    </span>
    <span class="section_type">
      <?php echo $section->child_type; ?>
    </span>
      <?php // echo $section->getIdentity(); ?>
    <span class="section_enabled <?php echo $section->enabled ? 'section_enabled_on' : 'section_enabled_off'?>">
      <?php echo $this->translate($section->enabled ? 'Default' : 'Optional');?>
    </span>
    <span class="section_options">
      <?php if ($section->photo_id): ?>
        <?php echo $this->htmlLink($this->url(array('action'=>'icon', 'section_id'=>$section->section_id)), $this->translate('icon'), array('class'=>'smoothbox'))?>
        |
      <?php endif; ?>    
      <?php echo $this->htmlLink($this->url(array('action'=>'edit', 'section_id'=>$section->section_id)), $this->translate('edit'), array('class'=>'smoothbox'))?>
      |
      <?php echo $this->htmlLink($this->url(array('action'=>'delete', 'section_id'=>$section->section_id)), $this->translate('delete'), array('class'=>'smoothbox'))?>

    </span>
  </div>