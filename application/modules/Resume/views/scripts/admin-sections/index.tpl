<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @section   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>

<?php 
	echo $this->render('admin-sections/_jsSort.tpl');
?>

<h2><?php echo $this->translate("Resumes Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div>
<?php echo $this->translate("This page show sections which can be added to resume. Default sections would be automatically added to resume upon creation."); ?>
</div>
<?php echo $this->render('admin-sections/_options.tpl'); ?>
<?php echo $this->render('admin-sections/_list.tpl'); ?>
