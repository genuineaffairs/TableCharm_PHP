<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>
<?php $resume = $this->resume; ?>
<?php /*
<span class="resume_location">
  <?php echo $this->partial('index/_address.tpl', 'resume', array('resume'=>$resume))?>
</span>
*/ ?>
<ul class="resume_specs">
  <li class="resume_category">
    <b><?php echo $resume->getFieldValueString('Sport'); ?></b>
  </li>
  <li class="resume_category">
    <?php echo $resume->getCategory()->toString(); ?>
  </li>
</ul>