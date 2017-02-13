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


<?php
  // Render the admin js
  echo $this->render('_jsAdmin.tpl')
?>

<h2><?php echo $this->translate($this->main_title) ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p><?php echo $this->translate('Your members will be asked to provide some information about their E-Medical Record when signing up. To reorder the E-Medical Record questions, click on their names and drag them up or down.'); ?>
</p>

<br />

<div class="admin_fields_options">
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion"><?php echo $this->translate('Add Question'); ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addheading"><?php echo $this->translate('Add Heading') ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;"><?php echo $this->translate('Save Order') ?></a>
</div>

<br />


<ul class="admin_fields">
  <?php foreach( $this->topLevelMaps as $field ): ?>
    <?php if($field->getChild()->type !== 'grid') : ?>
        <?php echo $this->adminFieldMeta($field); ?>
    <?php else : ?>
        <?php echo $this->zuluAdminGridFieldMeta($field); ?>
    <?php endif; ?>
  <?php endforeach; ?>
</ul>

<br />
<br />


