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
<div class="radcodes_sections_options">
<?php echo $this->htmlLink(
  $this->url(array('action'=>'add')),
  $this->translate("Add New Section"),
  array('class' => 'smoothbox buttonlink icon_radcodes_section_new')
); ?>
</div>